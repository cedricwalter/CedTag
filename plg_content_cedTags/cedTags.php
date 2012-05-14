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
        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            return true;
        }

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
        $terms = $cedTagModelTags->getTagsForArticle($id);

        if (isset($terms) && !empty($terms)) {
            $suppresseSingleTerms = CedTagsHelper::param('SuppresseSingleTerms');
            $hitsNumber = CedTagsHelper::param('HitsNumber');
            $maxTagsNumber = CedTagsHelper::param('MaxTagsNumber', 10);
            $showRelatedArticles = CedTagsHelper::param('RelatedArticlesByTags', 0);

            $currentNumber = 0;

            $havingTags = false;
            $htmlList = '';
            $termIds = array();

            $CedTagThemes = new CedTagThemes();
            $CedTagThemes->addCss();
            $haveValidTags = false;

            foreach ($terms as $term) {
                $ct = $cedTagModelTags->getTagFrequency($term);
                if ($showRelatedArticles || $suppresseSingleTerms) {
                    if (@intval($ct) <= 1) {
                        if ($suppresseSingleTerms) {
                            continue;
                        }
                    } else {
                        $termIds[] = $term->id;
                    }
                }
                //do some specail filters.
                if ($currentNumber >= $maxTagsNumber) {
                    break;
                }
                $currentNumber++;

                //capitalize of not the terms
                $term->name = CedTagsHelper::ucwords($term->name);

                $hrefTitle = $ct . ' items tagged with ' . $term->name;
                if ($hitsNumber) {
                    $hrefTitle .= ' | Hits:' . $term->hits;
                }

                $link = JRoute::_('index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($term->name));
                $htmlList .= '<li><a href="' . $link . '" rel="tag" title="' . $hrefTitle . '" >' . $term->name . '</a></li> ';
                $havingTags = true;
            }

            if ($havingTags) {
                $tagResult = '<div class="clearfix">
                </div>
                    <div class="cedtag">' . JText::_('TAGS:') . '
                    <ul class="cedtag">' . $htmlList . '</ul>
                </div>';

                //0 Display after the article fulltext
                //1 Display below the article title
                //2 Display at both position
                if ($position == 1) {
                    $text = $tagResult . $text;
                }

                else
                    if ($position == 2) {
                        // Both before and after Text
                        $text = $tagResult . $text . $tagResult;
                    } else {
                        // After Text
                        $text .= $tagResult;
                    }
            }

        }
        /* auto suggestion using ajax
        $CedTagSuggest = new CedTagSuggest();
        $CedTagSuggest->addJs();
        JHtml::_('behavior.mootools');
        $text .= '<span id="cedTagSuggestStatus">Start typing</span>
        <input type="text" name="cedTagSuggest" id="cedTagSuggest" value="caliber30,kyosho" title="Search in plug-in title.">';
        */

        $showAddTagButton = CedTagsHelper::param('ShowAddTagButton');
        if ($showAddTagButton) {
            $user = JFactory::getUser();
            $canEdit = $this->canUserAddTags($user, $id);
            if ($canEdit) {
                $Itemid = JRequest::getVar('Itemid', false);
                if (is_numeric($Itemid)) {
                    $Itemid = intval($Itemid);
                }
                else {
                    $Itemid = 1;
                }
                $text .= $this->addTagsButtonsHTML($id, $Itemid, $havingTags);
            }
        }

        $view = JRequest :: getVar('view');
        if ($showRelatedArticles && !empty($termIds) && ($view == 'article')) {
            $text .= $this->showReleatedArticlesByTags($id, $termIds);
        }

        return true;
    }



    function showReleatedArticlesByTags($articleId, $termIds)
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
        $query->select('a.id');
        $query->select('a.title');
        $query->select('a.alias');
        $query->select('a.access');
        $query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
        $query->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');

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

    public
    function getUniqueArticleId($termIds, $articleId)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $relatedArticlesCount = 0;
        $max = max(intval($relatedArticlesCount), count($termIds));
        $termIds = array_slice($termIds, 0, $max);
        $termIdsCondition = @implode(',', $termIds);

        $query->select('distinct cid');
        $query->from('#__cedtag_term_content');
        $query->where("tid in (' . $termIdsCondition . ')");
        $query->where('cid<>' . $articleId);
        $dbo->setQuery($query);
        $cids = $dbo->loadColumn(0);
        return $cids;
    }


    function canUserAddTags($user, $article_id)
    {
        // A user must be logged in to add attachments
        if ($user->get('username') == '') {
            return false;
        }

        // If the user generally has permissions to add content, they qualify.
        // (editor, publisher, admin, etc)
        // NOTE: Exclude authors since they need to be handled separately.
        $user_type = $user->get('usertype', false);
        if (($user_type != 'Author') &&
            $user->authorize('com_content', 'add', 'content', 'all')
        ) {
            return true;
        }

        // Make sure the article is valid and load its info
        if ($article_id == null || $article_id == '' || !is_numeric($article_id)) {
            return false;
        }
        $dbo = JFactory::getDBO();
        $query = "SELECT created_by from #__content WHERE id='" . $article_id . "'";
        $dbo->setQuery($query);
        $rows = $dbo->loadObjectList();
        if (count($rows) == 0) {
            return false;
        }
        $created_by = $rows[0]->created_by;

        //the created author can add tags.
        if ($user->get('id') == $created_by) {
            return true;
        }

        // No one else is allowed to add articles
        return false;
    }

    function addTagsButtonsHTML($article_id, $Itemid, $havingTags)
    {
        $document = & JFactory::getDocument();
        $document->addScript(JURI::root(true) . '/media/system/js/modal.js');
        JHTML::_('behavior.modal', 'a.modal');

        // Generate the HTML for a  button for the user to click to get to a form to add an attachment
        $url = "index.php?option=com_cedtag&amp;task=add&amp;tmpl=component&amp;refresh=1&amp;article_id=" . $article_id;

        $url = JRoute::_($url);
        $icon_url = JURI::Base() . 'components/com_cedtag/images/logo.png';

        $title = JText::_('ADD TAGS');
        if ($havingTags) {
            $title = JText::_('EDIT TAGS');
        }

        $modalLink = '<a class="modal" type="button" href="' . $url . '" rel="{handler: \'iframe\', size: {x: 500, y: 260}}\">';
        $links = "$modalLink<img src=\"$icon_url\" /></a>";
        $links .= $modalLink . $title . "</a>";
        return "\n<div class=\"addtags\">$links</div>\n";
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
                $tmodel = new CedTagModelTag();
                $tmodel->batchUpdate($combined);
            }
        }
        return true;
    }
}

?>