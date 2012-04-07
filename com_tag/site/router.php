<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @version $Id: com_tag.php 599 2010-06-06 23:26:33Z you $
 * @author waltercedric.com, Joomlatags.org
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
require_once JPATH_SITE . '/components/com_tag/helper/helper.php';

function TagBuildRoute(&$query)
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

function TagParseRoute($segments)
{
    $vars = array();
    $tag = array_shift($segments);
    $vars['tag'] = JoomlaTagsHelper::urlTagname($tag);
    $vars['view'] = 'tag';
    return $vars;
}