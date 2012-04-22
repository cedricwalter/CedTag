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


class CedTagControllerCss extends CedTagControllerFile
{
    /**
     * @Override
     * @return string
     */
    public function getDefaultFile()
    {
        return JPATH_SITE . '/media/com_cedtag/css/tagcloud.default.css';
    }

    /**
     * @Override
     * @return string
     */
    public function getFile()
    {
        return JPATH_SITE . '/media/com_cedtag/css/tagcloud.css';
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
        JFile::write($this->getFile(), trim($updatedFileContent));
        JFactory::getApplication()->input->set('view', $this->getDefaultView());
        parent::display();
    }



}

?>