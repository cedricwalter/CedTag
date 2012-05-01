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
require_once JPATH_SITE . '/components/com_cedtag/helper/themes.php';

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
        $query = "select id,count(*) as frequency,name,hits as hits, t.created as created, 1 as sequence from #__cedtag_term as t where t.id in(" . @implode(',', $idsArray) . ")";
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

            $sorting = $params->get('sorting', 'tag_random');

            $CedTagsHelper = new CedTagsHelper();
            $rows = $CedTagsHelper->mappingFrequencyToSize($rows);

            $sorting = $params->get('sorting', 'tag_latestasort');
            usort($rows, array('CedTagsHelper', $sorting));

            $CedTagThemes = new CedTagThemes();
            $CedTagThemes->addCss();


            if (intval($params->get('reverse', 1))) {
                $rows = array_reverse($rows);
            }
        }
        return $rows;
    }


}



