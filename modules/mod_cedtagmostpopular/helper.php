<?php
/**
 * @package module cedMostPopularTags for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_content/helpers/route.php';
require_once JPATH_SITE . '/components/com_cedtag/helpers/helper.php';

class modCedMostPopularTagsHelper
{

    function getList(&$params)
    {
        $CedTagsHelper = new CedTagsHelper();

        $count = intval($params->get('count', 25));
        $sorting = $params->get('sorting', 'sizeasort');
        $reverse = intval($params->get('reverse', 0));

        return $CedTagsHelper->getPopularTagModel($count, $sorting, $reverse);
    }


}






