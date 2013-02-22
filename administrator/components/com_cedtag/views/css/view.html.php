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
require_once JPATH_SITE . '/components/com_cedtag/helpers/themes.php';

// userhelper for acl
require_once JPATH_SITE . '/administrator/components/com_users/helpers/users.php';

class CedTagViewCss extends JView
{

    function display($tpl = null)
    {
        $this->defaultTpl($tpl);
    }

    function defaultTpl($tpl = null)
    {
        $this->addToolbar();

        $CedTagThemes = new CedTagThemes();
        $tagCssFile = $CedTagThemes->getFile();
        $isCssWritable = is_writable($tagCssFile);

        $cssFileContent = file_get_contents($tagCssFile);
        $this->assign('isCssWritable', $isCssWritable);
        $this->assignRef('cssFileName', $tagCssFile);
        $this->assignRef('cssFileContent', $cssFileContent);

        parent::display($tpl);
    }

    function addToolbar()
    {
        JToolBarHelper::title(JText::_('TEMPLATE MANAGER'), 'tag.png');

        $canDo = UsersHelper::getActions();
        if ($canDo->get('core.create')) {
            JToolBarHelper::apply();
            JToolBarHelper::spacer();

            JToolBarHelper::save();
            JToolBarHelper::spacer();

            JToolBarHelper::custom('restore', 'default', '', JText::_('RESTORE DEFAULT'), false);
            JToolBarHelper::spacer();
        }
        JToolBarHelper::back(JText::_('CEDTAG_CONTROL_PANEL'), 'index.php?option=com_cedtag');
    }


}
