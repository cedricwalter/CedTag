<?php
/**
 * @package module cedCustomTagsCloud for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_content/helpers/route.php';
require_once JPATH_BASE . '/components/com_cedtag/helper/helper.php';
class modCedCustomTagsCloudHelper
{
    public function getList(&$params)
    {
        $dbo =& JFactory::getDBO();
        $termIds = $params->get("tagIds");

        $idsArray = @explode(',', $termIds);
        if (empty($idsArray)) {
            return array();
        }
        $query = "select id,name, 1 as sequence from #__cedtag_term as t where t.id in(" . @implode(',', $idsArray) . ");";
        $dbo->setQuery($query);
        $rows = $dbo->loadObjectList();

        if (isset($rows) && !empty($rows)) {
            $rowsMap = array();
            $total_tags = count($rows);
            foreach ($rows as $row) {
                $rowsMap[$row->id] = $row;
            }
            $sortedRows = array();

            for ($index = 0; $index < $total_tags; $index++) {
                $id = $idsArray[$index];
                $rowsMap[$id]->sequence = $total_tags - $index;
                $sortedRows[] = $rowsMap[$id];
            }

            $rows = array_reverse($sortedRows);
            unset($sortedRows);
            unset($rowsMap);
            $document =& JFactory::getDocument();
            $document->addStyleSheet(JURI::base() . 'media/com_cedtag/css/tagcloud.css');
            $tag_sizes = 7;

            $min_tags = $total_tags / $tag_sizes;
            $bucket_count = 1;
            $bucket_items = 0;
            $tags_set = 0;
            for ($index = 0; $index < $total_tags; $index++) {
                $row =& $rows[$index];
                //$row->link=JRoute::_('index.php?option=com_cedtag&task=tag&tag='.urlencode($row->name));
                $row->link = JRoute::_('index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($row->name));
                $tag_count = $row->sequence;
                $last_count = 0;
                if (($bucket_items >= $min_tags) and $last_count != $tag_count and $bucket_count < $tag_sizes) {
                    $bucket_count++;
                    $bucket_items = 0;
                    // Calculate a new minimum number of tags for the remaining classes.
                    $remaining_tags = $total_tags - $tags_set;
                    $min_tags = $remaining_tags / $bucket_count;
                }
                $row->class = 'tag' . $bucket_count;
                $row->size = 9 * $bucket_count;

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



