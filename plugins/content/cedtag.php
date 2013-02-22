<?php
/**
 * @package Plugin cedtags for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or  die('Restricted access');
jimport('joomla.event.plugin');

jimport('joomla.plugin.plugin');
jimport('joomla.language.helper');

require_once (JPATH_SITE . '/components/com_cedtag/helpers/themes.php');
require_once (JPATH_SITE . '/components/com_cedtag/helpers/helper.php');
require_once (JPATH_SITE . '/components/com_cedtag/helpers/suggest.php');
require_once (JPATH_SITE . '/components/com_content/helpers/route.php');
require_once (JPATH_SITE . '/components/com_cedtag/models/tags.php');

require_once(dirname(__FILE__) . '/related/relatedfactory.php');

class plgContentCedTag extends JPlugin
{
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();

        //Insert it to avoid multiple insert in onContentBeforeDisplay/onContentPrepare while in blog view
        $CedTagThemes = new CedTagThemes();
        $CedTagThemes->addCss();
    }

    /**
     * this method allow cedTag to insert tags in Front page
     *  - Display after the article fulltext
     *  - Display below the article title
     *  - Display at both position
     *
     * @param $context
     * @param $row
     * @param $params
     * @param $page
     * @return mixed
     */
    public function onContentBeforeDisplay($context, &$row, &$params, $page = 0)
    {
        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            return true;
        }
        $view = JFactory::getApplication()->input->get('view');
        $viewIsBlogOrList = $view == 'tag';

        $frontPageTagView = CedTagsHelper::param('FrontPageTagView', '1');
        $displayTagInBlogOrListView = CedTagsHelper::param('BlogTag', '1');

        if (!$viewIsBlogOrList && $frontPageTagView == '0') {
            return true;
        }
        if ($viewIsBlogOrList && !$displayTagInBlogOrListView) {
            return true;
        }

        if ($viewIsBlogOrList && $displayTagInBlogOrListView) {
            //0  Display after the article title
            //1  Display after the article intro text
            //2  Display after the article fulltext
            //3  Display after the article title and after the article fulltext
            $blogTagPosition = CedTagsHelper::param('blogTagPosition', '0');
            $blogTagShowTagTitle = CedTagsHelper::param('blogTagShowTagTitle', '1');
            return $this->execute($context, $row->id, $row->catid, $row->introtext, $blogTagPosition, $blogTagShowTagTitle);
        }

        //0 Display after the article fulltext
        //1 Display below the article title
        //2 Display at both position
        $frontPageTagViewTagPosition = CedTagsHelper::param('FrontPageTagViewTagPosition', '1');
        $showFrontPageTagTitle = CedTagsHelper::param('showFrontPageTagTitle', '0');
        return $this->execute($context, $row->id, $row->catid, $row->introtext, $frontPageTagViewTagPosition, $showFrontPageTagTitle);
    }

    /**
     * this method allow cedTag to insert tags when viewing an article
     *  - Display after the article fulltext
     *  - Display below the article title
     *  - Display at both position
     *
     * @param $context
     * @param $row
     * @param $params
     * @param int $page
     * @return bool
     */
    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        $canProceed = $context == 'com_content.article';
        if (!$canProceed) {
            return true;
        }
        //do not run in administrator area
        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            return true;
        }

        //don't display if user want so
        $frontPageTagArticleView = CedTagsHelper::param('FrontPageTagArticleView', '1');
        if (!$frontPageTagArticleView) {
            return true;
        }
        //no content id, no chance to run
        if (isset($row) && (!isset($row->id) || is_null($row->id))) {
            return true;
        }

        $blogTag = CedTagsHelper::param('BlogTag');
        $layout = JFactory::getApplication()->input->get('layout');
        if ($layout == 'blog' && !$blogTag) {
            return true;
        }
        $view = JFactory::getApplication()->input->get('view');
        if (($layout != 'blog') && ($view == 'category' || $view == 'section')) {
            return true;
        }

        //0 Display after the article fulltext
        //1 Display below the article title
        //2 Display at both position
        $position = intval(CedTagsHelper::param('ArticleViewTagPosition', '0'));
        $showTagTitle = intval(CedTagsHelper::param('showArticleTagTitle', '0'));

        return $this->execute($context, $row->id, $row->catid, $row->text, $position, $showTagTitle);
    }


    private function execute($context, $id, $catid, &$text, $position, $showTagTitle)
    {
        global $access;

        $htmlTag = $this->getHtmlTag($context, $id, $catid, $showTagTitle);

        //0 Display after the article fulltext
        //1 Display below the article title
        //2 Display at both position
        if ($position == 1) {
            $text = $htmlTag[0] . $text;
        } else {
            if ($position == 2) {
                // Both before and after Text
                $text = $htmlTag[0] . $text . $htmlTag[0];
            } else {
                // After Text
                $text .= $htmlTag[0];
            }
        }

        $userIsViewingAnArticle = (JFactory::getApplication()->input->get('view') == 'article');
        $showRelatedArticles = CedTagsHelper::param('RelatedArticlesByTags', 0);
        $thereIsSomeTagsInCurrentArticle = !empty($tags);

        if ($showRelatedArticles && $thereIsSomeTagsInCurrentArticle && $userIsViewingAnArticle) {
            $related = CedTagsHelper::param('related', 'Joomla');
            $plugin = CedTagRelatedFactory::getInstance($related);
            $text .= $plugin->getRelatedAsHtml($id, $catid, $access, $htmlTag[1]);
        }

    }

    /**
     * @param $context
     * @param $id
     * @param $catid
     * @param $showTagTitle
     * @return mixed
     */
    private function getHtmlTag($context, $id, $catid, $showTagTitle)
    {
        $cache = JFactory::getCache('plugins_cedtag', '');
        $cache->setCaching(1);

        $cacheId = md5(serialize(array($context, $id, $catid, $showTagTitle)));
        if (!($html = $cache->get($cacheId))) {

            $cedTagModelTags = new CedTagModelTags();
            $tags = $cedTagModelTags->getModelTags($id);

            $CedTagsHelper = new CedTagsHelper();
            $canEdit = $CedTagsHelper->canUserDoTagOperations($id);
            if ($canEdit) {
                $frontPageEditTagEditOnly = CedTagsHelper::param('FrontPageEditTagEditOnly', '0');
                $layout = JFactory::getApplication()->input->get('layout');

                /*   $frontPageEditTagEditOnly  $layout  ->display
                 *       yes                      edit      yes
                 *                                any       no
                 *
                 *       no                       edit      yes
                 *                                any       yes
                 */
                $display = true;
                if ($frontPageEditTagEditOnly && $layout != 'edit') {
                    $display = false;
                }

                if ($display) {
                    $CedTagSuggest = new CedTagSuggest();
                    $tagIt = array();
                    foreach ($tags as $tag) {
                        $tagIt[] = $tag->tag;
                    }
                    $CedTagSuggest->addSiteJs($tagIt, $id);
                    $tagResult = '<div class="cedtagplugin">';
                    $tagResult .= ' <div class="title">' . JText::_('PLG_CONTENT_CEDTAGS_ARTICLE_TAGGED') . '</div>';
                    $tagResult .= ' <div>' . JText::_('PLG_CONTENT_CEDTAGS_DOCUMENTATION') . '</div>';
                    $tagResult .= ' <ul id="tags' . $id . '" class="tags"></ul>';
                    $tagResult .= '</div>';
                }
            } else {
                $htmlList = "";
                foreach ($tags as $tag) {
                    $htmlList .= '<li><a href="' . $tag->link . '" rel="tag" title="' . $tag->title . '" >' . $tag->tag . '</a></li> ';
                }
                $tagResult = '<div class="cedtag" />';
                if ($showTagTitle) {
                    $tagResult .= ' <div class="title">' . JText::_('PLG_CONTENT_CEDTAGS_ARTICLE_TAGGED') . '</div >';
                }
                $tagResult .= ' <ul class="cedtag" > ' . $htmlList . '</ul >';
                $tagResult .= '</div > ';
            }

            $cache->store(array($tagResult, $tags), $cacheId);
        }

        return $cache->get($cacheId);
    }


    /**
     * Auto extract meta keywords as tags
     * Article is passed by reference, but after the save, so no changes will be saved.
     * Method is called right after the content is saved
     *
     * @param    string $context       The context of the content passed to the plugin (added in 1.6)
     * @param    object $article       A JTableContent object
     * @param    bool   $isNew     If the content has just been created
     * @return bool
     */
    public
    function onContentAfterSave($context, &$article, $isNew)
    {
        $isFrontendEditArticleForm = $context == 'com_content.form';
        $isBackendEditArticleForm = $context == 'com_content.article';

        if ($isFrontendEditArticleForm || $isBackendEditArticleForm) {
            $useAutoMetaKeywordsExtractor = CedTagsHelper::param('autoMetaKeywordsExtractor', '1');
            if ($useAutoMetaKeywordsExtractor) {
                $useMetaKeywordsAreSourceForExistingArticles = CedTagsHelper::param('metaKeywordsAreSourceForExistingArticles', '0');
                if ($isNew || $useMetaKeywordsAreSourceForExistingArticles) {
                    $tags = $article->metakey;
                    $id = $article->id;
                    $combined = array();
                    $combined[$id] = $tags;
                    require_once(JPATH_SITE . '/administrator/components/com_cedtag/models/tag.php');
                    $model = new CedTagModelTag();
                    $model->batchUpdate($combined);
                }
            }
        }
        return true;
    }
}
