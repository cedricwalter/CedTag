<?php
/**
 * @package module cedLatestTags for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_content/helpers/route.php';
require_once JPATH_SITE . '/components/com_cedtag/helper/helper.php';
require_once JPATH_SITE . '/components/com_cedtag/helper/themes.php';

class modCedLatestTagsHelper
{
    function getList(&$params)
    {
        //Get the latest tag by creation date
        $query = "select count(*) as frequency,name,hits as hits, t.created as created from #__cedtag_term_content as tc
                    inner join #__cedtag_term as t on t.id=tc.tid where t.published='1' group by(tid) order by created desc";
        $dbo =& JFactory::getDBO();

        $count = intval($params->get('count', 25));
        $dbo->setQuery($query, 0, $count);
        $rows = $dbo->loadObjectList();

        if (isset($rows) && !empty($rows)) {
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



