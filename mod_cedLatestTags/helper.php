<?php
/**
 * @package module cedLatestTags for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_content/helpers/route.php';
require_once JPATH_SITE . '/components/com_cedtag/helper/helper.php';

class modCedLatestTagsHelper
{
    function getList(&$params)
    {
        //$mainframe =& JFactory::getApplication();
        $count = intval($params->get('count', 25));

        //Get the latest tag by creation date
        $query = "select count(*) as ct,name,hits, t.created from #__cedtag_term_content as tc inner join #__cedtag_term as t on t.id=tc.tid where t.published='1' group by(tid) order by created desc";
        $db =& JFactory::getDBO();
        $db->setQuery($query, 0, $count);
        $rows = $db->loadObjectList();

        if (isset($rows) && !empty($rows)) {
            usort($rows, array('CedTagsHelper', 'tag_latestasort'));

            CedTagsHelper::addCss();

            $tag_sizes = 7;
            $total_tags = count($rows);
            $min_tags = $total_tags / $tag_sizes;
            $bucket_count = 1;
            $bucket_items = 0;
            $tags_set = 0;

            // for all tags
            for ($index = 0; $index < $total_tags; $index++) {
                $row =& $rows[$index];

                //$row->link=JRoute::_('index.php?option=com_cedtag&task=tag&tag='.urlencode($row->name));
                $row->link = JRoute::_('index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($row->name));
                $last_count = 0;
                $tag_count = $row->created;
                if (($bucket_items >= $min_tags) and $last_count != $tag_count and $bucket_count < $tag_sizes) {
                    $bucket_count++;
                    $bucket_items = 0;
                    // Calculate a new minimum number of tags for the remaining classes.
                    $remaining_tags = $total_tags - $tags_set;
                    $min_tags = $remaining_tags / $bucket_count;
                }
                $row->class = 'tag' . $bucket_count;
                $row->size = 65 + ($row->ct * 10);
                $row->created = $row->created;

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



