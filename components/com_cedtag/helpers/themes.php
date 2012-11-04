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

    public function __construct()
    {
        parent::__construct();
    }

    public function getFile()
    {
        $themes = CedTagsHelper::param('themes', 'stylish');

        $themeFile = 'media/com_cedtag/css/' . $themes . '.css';
        if (JFile::exists(JPATH_SITE . "/".$themeFile)) {
            return JPATH_SITE . "/".$themeFile;
        }
        throw new Exception("Could not find theme at " . $themeFile);
    }

    public function getDefaultFile()
    {
        $themes = CedTagsHelper::param('themes', 'stylish');

        $themeFile = 'media/com_cedtag/css/' . $themes . '.default.css';
        if (JFile::exists(JPATH_SITE . "/".$themeFile)) {
            return JPATH_SITE . "/".$themeFile;
        }
        throw new Exception("Could not find default theme at " . $themeFile);
    }



    public function getStyleSheet()
    {
        $themes = CedTagsHelper::param('themes', 'stylish');

        $themeFile = 'media/com_cedtag/css/' . $themes . '.css';
        if (JFile::exists(JPATH_SITE . "/".$themeFile)) {
            return JURI::root().$themeFile;
        }
        throw new Exception("Could not find theme at " . $themeFile);
    }


    public function addCss()
    {
        $document = JFactory::getDocument();
        $document->addStyleSheet($this->getStyleSheet());

        $useGoogleFonts = CedTagsHelper::param('useGoogleFonts', '1');
        if ($useGoogleFonts) {
            $googleFonts = explode("|", CedTagsHelper::param('googleFonts', "font-family: 'Open Sans', sans-serif;|Open+Sans"));
            $document->addStyleSheet('http://fonts.googleapis.com/css?family=' . $googleFonts[1]);
        }
    }

}