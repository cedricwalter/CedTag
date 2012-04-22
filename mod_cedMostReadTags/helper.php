<?php
/**
 * @package module cedMostReadTags for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_content/helpers/route.php';
require_once JPATH_SITE . '/components/com_cedtag/helper/helper.php';

class modCedMostReadTagsHelper
{
    function getList(&$params)
    {
        $dbo =& JFactory::getDBO();
        $count = intval($params->get('count', 25));
        $query = "select count(*) as ct,id,name,hits, t.created from #__cedtag_term_content as tc inner join #__cedtag_term as t on t.id=tc.tid where t.published='1' group by(tid) ORDER BY hits DESC";
        $dbo->setQuery($query, 0, $count);
        $rows = $dbo->loadObjectList();

        if (isset($rows) && !empty($rows)) {
            usort($rows, array('CedTagsHelper', 'hitsasort'));

            CedTagsHelper::addCss();

            $tag_sizes = 7;
            $total_tags = count($rows);
            $min_tags = $total_tags / $tag_sizes;
            $bucket_count = 1;
            $bucket_items = 0;
            $tags_set = 0;
            for ($index = 0; $index < $total_tags; $index++) {
                $row =& $rows[$index];
                $last_count = 0;
                //$row->link=JRoute::_('index.php?option=com_cedtag&task=tag&tag='.urlencode($row->name));
                $row->link = JRoute::_('index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($row->name));
                $tag_count = $row->hits;
                if (($bucket_items >= $min_tags) and $last_count != $tag_count and $bucket_count < $tag_sizes) {
                    $bucket_count++;
                    $bucket_items = 0;
                    // Calculate a new minimum number of tags for the remaining classes.
                    $remaining_tags = $total_tags - $tags_set;
                    $min_tags = $remaining_tags / $bucket_count;
                }
                $row->class = 'tag' . $bucket_count;

                //linear scaling
                $row->size = 65 + ($bucket_count* 10); //65 + ($tag_count * 10);
                //$row->size = 65 + ($row->ct * 10);

                //expo scaling
                //$row->size = 9 * $bucket_count;
                $bucket_items++;
                $tags_set++;
                $last_count = $tag_count;
                $row->name = CedTagsHelper::ucwords($row->name);

            }
            usort($rows, array('CedTagsHelper', $params->get('sorting', 'sizeasort')));

            if (intval($params->get('reverse', 1))) {
                $rows = array_reverse($rows);
            }
        }
        return $rows;
    }


}


function tag_sortbyHits($tag1, $tag2)
{
    if ($tag1->hits == $tag2->hits) {
        return 0;
    }
    return ($tag1->hits < $tag2->hits) ? -1 : 1;
}

