<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/


defined('_JEXEC') or die();
jimport('joomla.application.input');
jimport('joomla.filesystem.file');

require_once (JPATH_COMPONENT . '/controllers/file.php');
require_once (JPATH_SITE . '/components/com_cedtag/helpers/helper.php');
require_once (JPATH_SITE . '/components/com_cedtag/helpers/themes.php');


class CedTagControllerCss extends CedTagControllerFile
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * @Override
     * @return string
     */
    public function getDefaultFile()
    {
        $CedTagThemes = new CedTagThemes();
        return $CedTagThemes->getDefaultFile();
    }

    /**
     * @Override
     * @return string
     */
    public function getFile()
    {
        $CedTagThemes = new CedTagThemes();
        return $CedTagThemes->getFile();
    }

    /**
     * @Override
     * @return string
     */
    public function getDefaultView()
    {
        return 'css';
    }

    /**
     * @Override
     * @param $fileContent
     * @return mixed
     *
     */
    public function transform($fileContent)
    {
        $useGoogleFonts = CedTagsHelper::param('useGoogleFonts', '1');
        if ($useGoogleFonts) {
            $googleFonts = explode("|", CedTagsHelper::param('googleFonts', "font-family: 'Open Sans', sans-serif;|Open+Sans"));
            $fileContent = preg_replace("/(font-family:.*;)/i", $googleFonts[0], $fileContent);
        }

        return $fileContent;
    }


}
