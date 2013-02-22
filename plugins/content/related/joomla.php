<?php
/**
 * @package Plugin cedtags for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or  die('Restricted access');

require_once(dirname(__FILE__) . '/relatedinterface.php');

class CedTagJoomlaRelated implements CedTagRelatedInterface
{
    public function __construct($params)
    {
    }

    /**
     * @param $id
     * @param $tags
     * @return string
     */
    public function getRelatedAsHtml($id, $catid, $access, $tags)
    {
        $count = CedTagsHelper::param('RelatedArticlesCountByTags', 10);
        $relatedArticlesTitle = CedTagsHelper::param('RelatedArticlesTitleByTags', "Related Articles");
        //$max=max(intval($relatedArticlesCount),array_count_values($termIds));

        //find the unique article ids
        $contentIds = $this->getUniqueArticleId($tags, $id);

        $html = "";
        if (is_array($contentIds) && sizeof($contentIds) > 0) {
            $rows = $this->getModel($contentIds, $count);

            if (empty($rows)) {
                return '';
            }
            $user = JFactory::getUser();
            $aid = (JVERSION < 1.6) ? $user->get('aid', 0) : max($user->getAuthorisedViewLevels());

            $html = '<div class="relateditemsbytags">' . $relatedArticlesTitle . '</div>
                        <div>
                        <ul class="relateditems">';
            foreach ($rows as $row) {
                if ($row->access <= $aid) {
                    $link = (JVERSION < 1.6) ? JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid)) : JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug));
                } else {
                    $link = JRoute::_('index.php?option=com_user&view=login');
                }
                $html .= '<li> <a href="' . $link . '">' . htmlspecialchars($row->title) . '</a></li>';
            }
            $html .= '</ul></div>';

        }
        return $html;
    }

    private function getModel($contentIds, $count)
    {
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
        $query->where('a.id in(' . @implode(',', $contentIds) . ')');
        $query->where('a.state = 1');
        $query->where('( a.publish_up = ' . $dbo->Quote($nullDate) . ' OR a.publish_up <= ' . $dbo->Quote($now) . ' )');
        $query->where('( a.publish_down = ' . $dbo->Quote($nullDate) . ' OR a.publish_down >= ' . $dbo->Quote($now) . ' )');
        $query->where('cc.published = 1');

        $dbo->setQuery($query, 0, $count);
        //$queryDump = $query->dump();
        $rows = $dbo->loadObjectList();
        return $rows;
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
        $query->where('tid in(' . $tagIdsCondition . ')');
        $query->where('cid<>' . $id);
        $dbo->setQuery($query);
        //$text = $query->dump();

        $ids = $dbo->loadColumn(0);
        return $ids;
    }

}
