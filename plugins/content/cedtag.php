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

require_once JPATH_SITE . '/components/com_cedtag/helpers/themes.php';
require_once JPATH_SITE . '/components/com_cedtag/helpers/helper.php';
require_once JPATH_SITE . '/components/com_cedtag/helpers/suggest.php';

require_once JPATH_SITE . '/components/com_content/helpers/route.php';

require_once JPATH_SITE . '/components/com_cedtag/models/tags.php';

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
    public function onContentBeforeDisplay($context, &$row, &$params, $page=0)
    {
        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            return true;
        }

        $frontPageTagView = CedTagsHelper::param('FrontPageTagView', '1');
        $view = JRequest :: getVar('view');

        if ($frontPageTagView == '0') {
            return;
        }

        //0 Display after the article fulltext
        //1 Display below the article title
        //2 Display at both position
        $position = CedTagsHelper::param('FrontPageTagViewTagPosition', '1');
        $showTagTitle = CedTagsHelper::param('showFrontPageTagTitle', '0');
        $this->execute($context, $row->id, $row->introtext, $params, $page, $position, $showTagTitle);
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
        $layout = JRequest :: getVar('layout');
        if ($layout == 'blog' && !$blogTag) {
            return true;
        }

        $view = JRequest :: getVar('view');
        if (($layout != 'blog') && ($view == 'category' || $view == 'section')) {
            return true;
        }

        //0 Display after the article fulltext
        //1 Display below the article title
        //2 Display at both position
        $position = intval(CedTagsHelper::param('ArticleViewTagPosition', '0'));
        $showTagTitle = intval(CedTagsHelper::param('showArticleTagTitle', '0'));
        return $this->execute($context, $row->id, $row->text, $params, $page, $position, $showTagTitle);
    }


    private function execute($context, $id, &$text, &$params, $page = 0, $position, $showTagTitle)
    {

        $cedTagModelTags = new CedTagModelTags();
        $tags = $cedTagModelTags->getModelTags($id);

        $CedTagsHelper = new CedTagsHelper();
        $canEdit = $CedTagsHelper->canUserDoTagOperations($id);
        if ($canEdit) {

            $frontPageEditTagEditOnly = CedTagsHelper::param('FrontPageEditTagEditOnly', '0');
            $layout = JRequest :: getVar('layout');

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

        //0 Display after the article fulltext
        //1 Display below the article title
        //2 Display at both position
        if ($position == 1) {
            $text = $tagResult . $text;
        } else {
            if ($position == 2) {
                // Both before and after Text
                $text = $tagResult . $text . $tagResult;
            } else {
                // After Text
                $text .= $tagResult;
            }
        }

        $view = JRequest :: getVar('view');
        $showRelatedArticles = CedTagsHelper::param('RelatedArticlesByTags', 0);
        if ($showRelatedArticles && !empty($tags) && ($view == 'article')) {
            $text .= $this->showRelatedArticlesByTags($id, $tags);
        }

        return true;
    }

    /**
     * @param $id
     * @param $tags
     * @return string
     */
    private function showRelatedArticlesByTags($id, $tags)
    {
        $count = CedTagsHelper::param('RelatedArticlesCountByTags', 10);
        $relatedArticlesTitle = CedTagsHelper::param('RelatedArticlesTitleByTags', "Related Articles");
        //$max=max(intval($relatedArticlesCount),array_count_values($termIds));

        //find the unique article ids
        $cids = $this->getUniqueArticleId($tags, $id);

        $html = "";
        if (is_array($cids) && sizeof($cids) > 0) {

            $dbo = JFactory::getDBO();
            $nullDate = $dbo->getNullDate();

            $date = JFactory::getDate();
            $now = JDate::getInstance()->toSql($date);

            $query = $dbo->getQuery(true);
            $query->select('a . id');
            $query->select('a . title');
            $query->select('a . alias');
            $query->select('a . access');
            $query->select('CASE WHEN CHAR_LENGTH(a . alias) THEN CONCAT_WS(":", a . id, a . alias) ELSE a . id END as slug');
            $query->select('CASE WHEN CHAR_LENGTH(cc . alias) THEN CONCAT_WS(":", cc . id, cc . alias) ELSE cc . id END as catslug');

            $query->from('#__content AS a');

            $query->innerJoin('#__categories AS cc ON cc.id = a.catid');
            $query->where('a.id in(' . @implode(',', $cids) . ')');
            $query->where('a.state = 1');
            $query->where('( a.publish_up = ' . $dbo->Quote($nullDate) . ' OR a.publish_up <= ' . $dbo->Quote($now) . ' )');
            $query->where('( a.publish_down = ' . $dbo->Quote($nullDate) . ' OR a.publish_down >= ' . $dbo->Quote($now) . ' )');
            $query->where('cc.published = 1');

            $dbo->setQuery($query, 0, $count);
            //$queryDump = $query->dump();
            $rows = $dbo->loadObjectList();

            if (empty($rows)) {
                return '';
            }
            $user = JFactory::getUser();
            $aid = (JVERSION < 1.6)? $user->get('aid', 0) : max ($user->getAuthorisedViewLevels());

            $html = '<div class="relateditemsbytags">' . $relatedArticlesTitle . '</div>
                    <div>
                    <ul class="relateditems">';

            foreach ($rows as $row) {

                if ($row->access <= $aid) {
                    $link = (JVERSION < 1.6)? JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid)) : JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug));
                } else {
                    $link = JRoute::_('index.php?option=com_user&view=login');
                }
                $html .= '<li> <a href="' . $link . '">' . htmlspecialchars($row->title) . '</a></li>';
            }
            $html .= '</ul></div>';

        }
        return $html;
    }

    /**
     * @param $tags
     * @param $id
     * @return mixed
     */
    private function getUniqueArticleId($tags, $id)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $relatedArticlesCount = 0;
        $max = max(intval($relatedArticlesCount), count($tags));
        $tags = array_slice($tags, 0, $max);

        $tagIds = array();
        foreach ($tags as $tag) {
            $tagIds[] = $tag->id;
        }
        $tagIdsCondition = @implode(',', $tagIds);

        $query->select('distinct cid');
        $query->from('#__cedtag_term_content');
        $query->where('tid in('.$tagIdsCondition.')');
        $query->where('cid<>' . $id);
        $dbo->setQuery($query);
        $text = $query->dump();

        $ids = $dbo->loadColumn(0);
        return $ids;
    }

    /**
     * Auto extract meta keywords as tags
     * Article is passed by reference, but after the save, so no changes will be saved.
     * Method is called right after the content is saved
     *
     * @param    string        The context of the content passed to the plugin (added in 1.6)
     * @param    object        A JTableContent object
     * @param    bool        If the content has just been created
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

?>