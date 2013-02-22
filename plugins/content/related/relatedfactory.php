<?php
/**
 * @package Plugin cedtags for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or  die('Restricted access');

class CedTagRelatedFactory
{

    public static function getInstance($type)
    {
        $filename = dirname(__FILE__) . '/' . strtolower($type) . '.php';
        if (include_once($filename)) {
            $className = 'CedTag' . $type . 'Related';
            return new $className;
        } else {
            throw new Exception('Related class not found');
        }
    }
}
