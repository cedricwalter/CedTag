<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/


defined('_JEXEC') or die();
jimport( 'joomla.application.input' );
jimport( 'joomla.filesystem.file' );

class CedTagControllerCss extends JController
{

    function execute($task)
    {
        switch ($task) {
            case 'save':
                $this->save();
                break;
            case 'restore':
                $this->restore();
                break;

            default:
                $this->display();
        }
    }

    function display()
    {
        JFactory::getApplication()->input->set('view', 'css');
        parent::display();
    }

    function save()
    {
        $updatedCss = JFactory::getApplication()->input->get('csscontent', '', 'STRING');
        $tagCssFile = JPATH_SITE.'/media/com_cedtag/css/tagcloud.css';

        JFile::write($tagCssFile, $updatedCss);

        JFactory::getApplication()->input->set('view', 'css');
        parent::display();
    }

    function restore()
    {
        $defaultCssFile = JPATH_SITE . '/media/com_cedtag/css/tagcloud.default.css';
        $defaultCssFileContent = JFile::read($defaultCssFile);

        $tagCssFile = JPATH_SITE.'/media/com_cedtag/css/tagcloud.css';
        JFile::write($tagCssFile, $defaultCssFileContent);

        JFactory::getApplication()->input->set('view', 'css');
        parent::display();
    }
}
?>