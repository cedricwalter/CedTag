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
require_once JPATH_SITE . '/components/com_cedtag/helpers/helper.php';
require_once JPATH_SITE . '/components/com_cedtag/helpers/themes.php';

class modCedMostReadTagsHelper
{
    function getList(&$params)
    {
        $dbo = JFactory::getDBO();
        $query	= $dbo->getQuery(true);

        $query->select('count(*) as frequency');
        $query->select('name as name');
        $query->select('t.hits as hits');
        $query->select('t.created as created');

        $query->from('#__cedtag_term_content as tc');

        $query->innerJoin('#__cedtag_term as t on t.id=tc.tid');

        $query->where("t.published='1'");

        $query->group('tid');
        $query->order('hits desc');

        $dbo->setQuery($query);

        $count = intval($params->get('count', 25));
        $dbo->setQuery($query, 0, $count);
        $rows = $dbo->loadObjectList();

        if (isset($rows) && !empty($rows)) {
            $CedTagsHelper = new CedTagsHelper();
            $rows = $CedTagsHelper->mappingFrequencyToSize($rows);

            $sorting = $params->get('sorting', 'hitsasort');
            usort($rows, array('CedTagsHelper', $sorting));

            $CedTagThemes = new CedTagThemes();
            $CedTagThemes->addCss();

            if (intval($params->get('reverse', 0))) {
                $rows = array_reverse($rows);
            }
        }

        return $rows;
    }

}