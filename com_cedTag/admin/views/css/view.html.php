<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die();
jimport('joomla.application.component.view');
jimport('joomla.filesystem.file');


class CedTagViewCss extends JView
{

    function display($tpl = null)
    {
        $this->defaultTpl($tpl);
    }

    function defaultTpl($tpl = null)
    {
        $this->addToolbar();

        $tagCssFile = JPATH_SITE . '/media/com_cedtag/css/tagcloud.css';
        $isCssWritable = is_writable($tagCssFile);

        $cssFileContent = JFile::read($tagCssFile);
        $this->assign('isCssWritable', $isCssWritable);
        $this->assignRef('cssFileName', $tagCssFile);
        $this->assignRef('cssFileContent', $cssFileContent);

        parent::display($tpl);
    }

    function addToolbar()
    {
        JToolBarHelper::title(JText::_('TEMPLATE MANAGER'), 'tag.png');
        JToolBarHelper::save();
        JToolBarHelper::spacer();
        JToolBarHelper::custom('restore', 'default', '', JText::_('RESTORE DEFAULT'), false);
        JToolBarHelper::spacer();
        JToolBarHelper::back(JText::_('CONTROL PANEL'), 'index.php?option=com_cedtag');
    }


}
