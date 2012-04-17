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

require_once JPATH_SITE . '/components/com_cedtag/helper/helper.php';
require_once JPATH_SITE . '/components/com_content/helpers/route.php';

class plgContentCedTags extends JPlugin
{

    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onContentPrepare($context, &$article, &$params, $limitstart)
    {

        //$regex = "#{tag\s*(.*?)}(.*?){/tag}#s";
        //$article->text=preg_replace($regex,' ',$article->text);
        $app =& JFactory::getApplication();
        if ($app->getName() != 'site') {
            return true;
        }
        If (isset($article) && (!isset($article->id) || is_null($article->id))) {
            return true;
        }


        $FrontPageTag = CedTagsHelper::param('FrontPageTag');
        $BlogTag = CedTagsHelper::param('BlogTag');
        $view = JRequest :: getVar('view');
        $layout = JRequest :: getVar('layout');
        if (($view == 'frontpage') && !$FrontPageTag) {
            return true;
        }
        if ($layout == 'blog' && !$BlogTag) {
            return true;
        }
        if (($layout != 'blog') && ($view == 'category' || $view == 'section')) {
            return true;
        }

        $lang = & JFactory::getLanguage();
        $lang->load('com_cedtag', JPATH_SITE);

        //select t.id as tid,t.name, count(tc.cid) as ct from #__cedtag_term_content as c left join jos_tag_term as t on c.tid=t.id left join jos_tag_term_content tc on c.tid=tc.cid where c.cid=1 group by t.id,tc.cid ;

        $query = 'select tagterm.id,tagterm.name,tagterm.hits from #__cedtag_term as tagterm
                        left join #__cedtag_term_content as tagtermcontent
                        on tagtermcontent.tid=tagterm.id
                        where tagtermcontent.cid=' . $article->id . '
                        group by(tid)
                        order by tagterm.weight
                        desc,tagterm.name';

        $db =& JFactory::getDBO();
        $db->setQuery($query);
        $terms = $db->loadObjectList();
        $SuppresseSingleTerms = CedTagsHelper::param('SuppresseSingleTerms');
        $HitsNumber = CedTagsHelper::param('HitsNumber');

        CedTagsHelper::addCss();

        $havingTags = false;
        $htmlList = '';
        $link = '';
        $maxTagsNumber = CedTagsHelper::param('MaxTagsNumber', 10);
        $showRelatedArticles = CedTagsHelper::param('RelatedArticlesByTags', 0);
        $currentNumber = 0;
        $termIds = array();
        if (isset($terms) && !empty($terms)) {
            $haveValidTags = false;
            foreach ($terms as $term) {

                $countQuery = 'select count(cid) as ct from #__cedtag_term_content where tid=' . $term->id;
                $db->setQuery($countQuery);
                $ct = $db->loadResult();


                if ($showRelatedArticles || $SuppresseSingleTerms) {


                    if (@intval($ct) <= 1) {
                        if ($SuppresseSingleTerms) {
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
                //$link='index.php?option=com_cedtag&task=tag&tag='.urlencode($term->name);
                $link = 'index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($term->name);
                $link = JRoute::_($link);

                $term->name = CedTagsHelper::ucwords($term->name);

                if ($HitsNumber) {
                    $htmlList .= '<li><a href="' . $link . '" rel="tag" title="'.$ct.' items tagged with ' . $term->name . ' | Hits:' . $term->hits . '" >' . $term->name . '</a></li> ';
                } else {
                    $htmlList .= '<li><a href="' . $link . '" rel="tag" title="'.$ct.' items tagged with ' . $term->name . '" >' . $term->name . '</a></li> ';
                }
                $havingTags = true;
            }
            //$article->text.='<div class="tag">Tags:<ul>'.$links.'</ul></div>';
            if ($havingTags) {
                $tagResult = '<div class="clearfix"></div><div class="tag">' . JText::_('TAGS:') . '<ul>' . $htmlList . '</ul></div>';
                $position = CedTagsHelper::param('TagPosition');
                if ($position == 1) {
                    $article->text = $tagResult . $article->text;
                } else if ($position == 2) {
                    $article->text = $tagResult . $article->text . $tagResult;
                } else {
                    $article->text .= $tagResult;
                }
            }

        }
        $showAddTagButton = CedTagsHelper::param('ShowAddTagButton');
        if ($showAddTagButton) {
            $user =& JFactory::getUser();

            $canEdit = $this->canUserAddTags($user, $article->id);
            if ($canEdit) {
                $Itemid = JRequest::getVar('Itemid', false);
                if (is_numeric($Itemid)) {
                    $Itemid = intval($Itemid);
                }
                else {
                    $Itemid = 1;
                }
                $article->text .= $this->addTagsButtonsHTML($article->id, $Itemid, $havingTags);
            }
        }

        if ($showRelatedArticles && !empty($termIds) && ($view == 'article')) {
            $article->text .= $this->showReleatedArticlesByTags($article->id, $termIds);
        }
        return true;
    }

    function showReleatedArticlesByTags($articleId, $termIds)
    {
        $count = CedTagsHelper::param('RelatedArticlesCountByTags', 10);
        $relatedArticlesTitle = CedTagsHelper::param('RelatedArticlesTitleByTags', "Related Articles");
        //$max=max(intval($relatedArticlesCount),array_count_values($termIds));
        $relatedArticlesCount = 0;
        $max = max(intval($relatedArticlesCount), count($termIds));

        $termIds = array_slice($termIds, 0, $max);
        $termIdsCondition = @implode(',', $termIds);

        //find the unique article ids
        $query = ' select distinct cid from #__cedtag_term_content where tid in (' . $termIdsCondition . ') and cid<>' . $articleId;

        $dbo =& JFactory::getDBO();
        $dbo->setQuery($query);
        $cids = $dbo->loadColumn(0);

        $nullDate = $dbo->getNullDate();
        $date =& JFactory::getDate();
        $now = JDate::getInstance()->toSql($date);

        $where = ' a.id in(' . @implode(',', $cids) . ') AND a.state = 1'
            . ' AND ( a.publish_up = ' . $dbo->Quote($nullDate) . ' OR a.publish_up <= ' . $dbo->Quote($now) . ' )'
            . ' AND ( a.publish_down = ' . $dbo->Quote($nullDate) . ' OR a.publish_down >= ' . $dbo->Quote($now) . ' )';

        // Content Items only
        $query = 'SELECT a.id,a.title, a.alias,a.access, ' .
            ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,' .
            ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug' .
            ' FROM #__content AS a' .
            ' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
            ' WHERE ' . $where .
            ' AND cc.published = 1';
        $dbo->setQuery($query, 0, $count);
        $rows = $dbo->loadObjectList();

        if (empty($rows)) {
            return '';
        }
        $user =& JFactory::getUser();
        $aid = $user->get('aid', 0);

        $html = '<div class="relateditemsbytags"><h3>' . $relatedArticlesTitle . '</h3><ul class="relateditems">';
        $link = "";
        foreach ($rows as $row)
        {

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
        $db =& JFactory::getDBO();
        $query = "SELECT created_by from #__content WHERE id='" . $article_id . "'";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
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
        $url = "index.php?option=com_cedtag&task=add&refresh=1&article_id=" . $article_id;

        $url = JRoute::_($url);
        $icon_url = JURI::Base() . 'components/com_cedtag/images/logo.png';

        $add_tag_txt = $add_tag_txt = JText::_('ADD TAGS');
        if ($havingTags) {
            $add_tag_txt = JText::_('EDIT TAGS');
        }

        $ahead = '<a class="modal" type="button" href="' . $url . '" ';
        $ahead .= "rel=\"{handler: 'iframe', size: {x: 500, y: 260}}\">";
        $links = "$ahead<img src=\"$icon_url\" /></a>";
        $links .= $ahead . $add_tag_txt . "</a>";
        return "\n<div class=\"addtags\">$links</div>\n";

    }

    /**
     * Auto extract meta keywords as tags
     *
     * @param $article
     * @param $isNew
     * @return unknown_type
     */
    function onAfterContentSave(&$article, $isNew)
    {
        $autoMetaKeywordsExtractor = $FrontPageTag = CedTagsHelper::param('autoMetaKeywordsExtractor');
        if ($autoMetaKeywordsExtractor) {

            if ($isNew) {
                $tags = $article->metakey;
                $id = $article->id;
                $combined = array();
                $combined[$id] = $tags;

                require_once(JPATH_ADMINISTRATOR . DS . 'components/com_cedtag/models/tag.php');
                $tmodel = new TagModelTag();
                $tmodel->batchUpdate($combined);
            }
        }

        return true;
    }


}
?>