<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__) . '/helper.php';

class CedTagThemes extends JObject
{


    public function getFile()
    {
        $themes = CedTagsHelper::param('themes', 'simple');
        return JPATH_SITE . '/media/com_cedtag/css/' . $themes . '.css';
    }

    static function addCss()
    {
        $document = JFactory::getDocument();
        $themes = CedTagsHelper::param('themes', 'simple');

        if (JFile::exists(JPATH_SITE . '/media/com_cedtag/css/' . $themes . '.css')) {
            $document->addStyleSheet(JURI::base() . 'media/com_cedtag/css/' . $themes . '.css');
        }
        $useGoogleFonts = CedTagsHelper::param('useGoogleFonts', '1');
        if ($useGoogleFonts) {
            $googleFonts = explode("|", CedTagsHelper::param('googleFonts', "font-family: 'Open Sans', sans-serif;|Open+Sans"));
            $document->addStyleSheet('http://fonts.googleapis.com/css?family=' . $googleFonts[1]);
        }


    }

}