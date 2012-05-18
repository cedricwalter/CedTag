<?php
/**
 * @package Plugin cedtags for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or  die('Restricted access');
jimport('joomla.event.plugin');

jimport('joomla.plugin.plugin');
jimport('joomla.language.helper');

require_once JPATH_SITE . '/components/com_cedtag//helper/themes.php';
require_once JPATH_SITE . '/components/com_cedtag/helper/helper.php';
require_once JPATH_SITE . '/components/com_cedtag/helper/suggest.php';
require_once JPATH_SITE . '/components/com_content/helpers/route.php';
require_once JPATH_SITE . '/components/com_cedtag/models/tags.php';
require_once JPATH_SITE . '/components/com_cedtag/helper/suggest.php';

class plgContentCedTags extends JPlugin
{
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    /**
     * @param $context
     * @param $article
     * @param $params
     * @param $page
     * @return mixed
     */
    public function onContentBeforeDisplay($context, &$article, &$params, $page)
    {
        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            return true;
        }

        $frontPageTagView = CedTagsHelper::param('FrontPageTagView', '1');
        $view = JRequest :: getVar('view');

        if (($view == 'frontpage') && !$frontPageTagView) {
            return;
        }

        //0 Display after the article fulltext
        //1 Display below the article title
        //2 Display at both position
        $frontPageTagViewTagPosition = CedTagsHelper::param('FrontPageTagViewTagPosition', '1');
        $this->execute($context, $article->id, $article->introtext, $params, $page, $frontPageTagViewTagPosition);
    }

    /**
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

        //do not work in admin
        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            return true;
        }

        //dont display if user want so
        $frontPageTagArticleView = CedTagsHelper::param('FrontPageTagArticleView', '1');
        if (!$frontPageTagArticleView) {
            return true;
        }

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
        $frontPageTagArticleViewTagPosition = CedTagsHelper::param('FrontPageTagArticleViewTagPosition', '0');
        return $this->execute($context, $row->id, $row->text, $params, $page, $frontPageTagArticleViewTagPosition);
    }


    private function execute($context, $id, &$text, &$params, $page = 0, $position)
    {
        $cedTagModelTags = new CedTagModelTags();
        $tags = $cedTagModelTags->getModelTags($id);

        $CedTagsHelper = new CedTagsHelper();
        $canEdit = $CedTagsHelper->canUserDoTagOperations($id);
        if ($canEdit) {
            $CedTagSuggest = new CedTagSuggest();
            $tagit = array();
            foreach ($tags as $tag) {
                $tagit[] = $tag->tag;
            }
            $CedTagSuggest->addJs($tagit, $id);
            $tagResult = '<div class="cedtagplugin">';
            $tagResult .= ' <div class="title">' . JText::_('TAGS:') . '</div>';
            $tagResult .= ' <ul id="tags'.$id.'" class="tags"></ul>';
            $tagResult .= '</div>';
        }
        else {
            $htmlList = "";
            foreach ($tags as $tag) {
                $htmlList .= '<li><a href="' . $tag->link . '" rel="tag" title="' . $tag->title . '" >' . $tag->tag . '</a></li> ';
            }
            $tagResult = '<div class="cedtag" />';
            $tagResult .= ' <div class="title">' . JText::_('TAGS:') . '</div >';
            $tagResult .= ' <ul class="cedtag" > ' . $htmlList . '</ul >';
            $tagResult .= '</div > ';
        }

        //0 Display after the article fulltext
        //1 Display below the article title
        //2 Display at both position
        if ($position == 1) {
            $text = $tagResult . $text;
        }
        else {
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
        if ($showRelatedArticles && !empty($termIds) && ($view == 'article')) {
            $text .= $this->showRelatedArticlesByTags($id, $termIds);
        }

        return true;
    }


    private function showRelatedArticlesByTags($articleId, $termIds)
    {
        $count = CedTagsHelper::param('RelatedArticlesCountByTags', 10);
        $relatedArticlesTitle = CedTagsHelper::param('RelatedArticlesTitleByTags', "Related Articles");
        //$max=max(intval($relatedArticlesCount),array_count_values($termIds));

        //find the unique article ids
        $cids = $this->getUniqueArticleId($termIds, $articleId);

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
        $rows = $dbo->loadObjectList();

        if (empty($rows)) {
            return '';
        }
        $user = JFactory::getUser();
        $aid = $user->get('aid', 0);

        $html = '
        <div class="relateditemsbytags">' . $relatedArticlesTitle . '</div><ul class="relateditems">';
        $link = "";
        foreach ($rows as $row) {

            if ($row->access <= $aid) {
                $link = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid));
            } else {
                $link = JRoute::_('index.php?option=com_user&view=login');
            }
            $html .= '<li> <a href="' . $link . '">' . htmlspecialchars($row->title) . '</a></li>';
        }
        $html .= '</ul></div>';
        return $html;

    }

    /**
     * @param $termIds
     * @param $id the store id.
     * @return mixed
     */
    private
    function getUniqueArticleId($termIds, $id)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $relatedArticlesCount = 0;
        $max = max(intval($relatedArticlesCount), count($termIds));
        $termIds = array_slice($termIds, 0, $max);
        $termIdsCondition = @implode(',', $termIds);

        $query->select('distinct cid');
        $query->from('#__cedtag_term_content');
        $query->where("tid in(' . $termIdsCondition . ')");
        $query->where('cid<>' . $id);
        $dbo->setQuery($query);
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
        // Check we are handling the frontend edit form.
        if ($context != 'com_content.form') {
            return true;
        }

        $autoMetaKeywordsExtractor = CedTagsHelper::param('FrontPageTag') &&
            CedTagsHelper::param('autoMetaKeywordsExtractor');
        if ($autoMetaKeywordsExtractor) {
            if ($isNew) {
                $tags = $article->metakey;
                $id = $article->id;
                $combined = array();
                $combined[$id] = $tags;
                require_once(JPATH_ADMINISTRATOR . '/components/com_cedtag/models/tag.php');

                $model = new CedTagModelTag();
                $model->batchUpdate($combined);
            }
        }
        return true;
    }
}

?>