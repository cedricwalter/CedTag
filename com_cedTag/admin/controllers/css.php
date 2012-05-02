<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/


defined('_JEXEC') or die();
jimport('joomla.application.input');
jimport('joomla.filesystem.file');

require_once (JPATH_COMPONENT . '/controllers/file.php');
require_once JPATH_COMPONENT_SITE . '/helper/helper.php';
require_once JPATH_COMPONENT_SITE . '/helper/themes.php';


class CedTagControllerCss extends CedTagControllerFile
{
    /**
     * @Override
     * @return string
     */
    public function getDefaultFile()
    {
        return JPATH_SITE . '/media/com_cedtag/css/simple.default.css';
    }

    /**
     * @Override
     * @return string
     */
    public function getFile()
    {
        $CedTagThemes  = new CedTagThemes();
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

    public function save()
    {
        $updatedFileContent = JFactory::getApplication()->input->get('content', '', 'STRING');
        $updatedFileContent = $this->transform($updatedFileContent);

        $file = $this->getFile();
        $content = trim($updatedFileContent);
        JFile::write($file, $content);
        JFactory::getApplication()->input->set('view', $this->getDefaultView());
        parent::display();
    }

    public function transform($fileContent)
    {
        $useGoogleFonts = CedTagsHelper::param('useGoogleFonts','1');
        if ($useGoogleFonts) {
            $googleFonts = explode("|",CedTagsHelper::param('googleFonts',"font-family: 'Open Sans', sans-serif;|Open+Sans"));
            $fileContent = preg_replace ("/(font-family:.*;)/i", $googleFonts[0], $fileContent);
        }

        return $fileContent;
    }


}

?>