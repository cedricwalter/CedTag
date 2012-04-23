<?php
/**
 * @package module cedMostReadTags for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
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
        $query = "select count(*) as frequency,name as name,t.hits as hits, t.created as created from #__cedtag_term_content as tc
                   inner join #__cedtag_term as t on t.id=tc.tid where t.published='1' group by(tid) ORDER BY hits DESC";

        $count = intval($params->get('count', 25));
        $dbo->setQuery($query, 0, $count);
        $rows = $dbo->loadObjectList();

        if (isset($rows) && !empty($rows)) {
            $CedTagsHelper = new CedTagsHelper();
            $rows = $CedTagsHelper->mappingFrequencyToSize($rows);

            $sorting = $params->get('sorting', 'hitsasort');
            usort($rows, array('CedTagsHelper', $sorting));

            CedTagsHelper::addCss();

            if (intval($params->get('reverse', 1))) {
                $rows = array_reverse($rows);
            }
        }

        return $rows;
    }

}