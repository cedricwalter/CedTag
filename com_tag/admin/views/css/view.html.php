<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @version $Id: com_tag.php 599 2010-06-06 23:26:33Z you $
 * @author waltercedric.com, Joomlatags.org
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die();
jimport('joomla.application.component.view');

class TagViewCss extends JView
{

    function display($tpl = null)
    {

        $this->defaultTpl($tpl);

    }

    function defaultTpl($tpl = null)
    {
        JToolBarHelper::title(JText::_('TEMPLATE MANAGER'), 'tag.png');
        JToolBarHelper::save('save', JText::_('SAVE'));
        JToolBarHelper::spacer();
        JToolBarHelper::custom('restore', 'default', '', JText::_('RESTORE DEFAULT'), false);
        JToolBarHelper::spacer();
        JToolBarHelper::back(JText::_('CONTROL PANEL'), 'index.php?option=com_tag');
        $tagCssFile = JPATH_SITE.'media/com_tag/css/tagcloud.css';
        $isCssWritable = is_writable($tagCssFile);
        $cssFileContent = file_get_contents($tagCssFile);
        $this->assign('isCssWritable', $isCssWritable);
        $this->assignRef('cssFileName', $tagCssFile);
        $this->assignRef('cssFileContent', $cssFileContent);
        parent::display($tpl);
    }


}
