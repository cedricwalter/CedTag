<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
require_once JPATH_SITE . '/components/com_cedtag/helper/helper.php';

function cedtagBuildRoute(&$query)
{
    $segments = array();

    if (isset($query['tag'])) {
        $segments[] = $query['tag'];
        unset($query['tag']);
    }

    if (isset($query['view'])) {
        unset($query['view']);
    }
    if (isset($query['task']) && $query['task'] == 'tag') {
        unset($query['task']);
    }

    if (isset($query['layout'])) {
        unset($query['layout']);
    }
    return $segments;
}

function cedtagParseRoute($segments)
{
    $vars = array();
    $tag = array_shift($segments);
    $vars['tag'] = CedTagsHelper::urlTagname($tag);
    $vars['view'] = 'tag';
    return $vars;
}