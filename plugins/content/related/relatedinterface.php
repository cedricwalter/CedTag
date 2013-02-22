<?php
/**
 * @package Plugin cedtags for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or  die('Restricted access');

interface CedTagRelatedInterface
{
    /**
     * @param $id
     * @param $tags
     * @return mixed
     */
    public function getRelatedAsHtml($id, $catid, $access, $tags);
}
