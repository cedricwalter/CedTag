<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

//dont use JPATH_COMPONENT_SITE here
require_once JPATH_SITE . '/components/com_cedtag/helpers/helper.php';

//You can think of BuildRoute as a form of encoding and ParseRoute as the corresponding decoding.

/**
 * The first function, [componentname]BuildRoute(&$query), must transform an array of URL parameters into an array of segments that will form the SEF URL.
 */
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

/**
 * The second function, [componentname]ParseRoute($segments), must transform an array of segments back into an array of URL parameters. Schematically:
 * @param $segments
 * @return array
 */
function cedtagParseRoute($segments)
{
    $vars = array();
    $tag = array_shift($segments);
    $vars['tag'] = CedTagsHelper::urlTagname($tag);
    $vars['task'] = 'tag';
    $vars['view'] = 'tag';
    return $vars;
}