<?php
/**
 * @package Plugin cedtags for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or  die('Restricted access');

require_once(dirname(__FILE__) . '/relatedinterface.php');

require_once(JPATH_SITE . '/plugins/content/relatedthumbarticles/relatedthumbarticles/controller.php');

class CedTagCedThumbnailsRelated implements CedTagRelatedInterface
{

    public function __construct($params)
    {
        //nothing
    }

    /**
     * @param $id
     * @param $catid
     * @param $access
     * @param $tags
     * @return string HTML representation
     */
    public function getRelatedAsHtml($id, $catid, $access, $tags)
    {
        $params = CedTagCedThumbnailsRelated::getCedThumbnailsParams();
        $controller = new relatedThumbArticlesController($params);
        $html = $controller->execute($id, $catid, $access);
        return $html;
    }

    /**
     * @return JRegistry
     */
    static function getCedThumbnailsParams()
    {
        static $params;
        if (!isset($params)) {
            $plugin = JPluginHelper::getPlugin("content", 'relatedthumbarticles');
            $params = new JRegistry($plugin->params);
        }
        return $params;
    }

}
