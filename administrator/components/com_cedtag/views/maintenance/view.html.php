<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die();
jimport('joomla.application.component.view');

// userhelper for acl
require_once JPATH_SITE . '/administrator/components/com_users/helpers/users.php';

class CedTagViewMaintenance extends JView
{

    function display($tpl = null)
    {
        $this->defaultTpl($tpl);
    }

    function defaultTpl($tpl = null)
    {
        $this->addToolbar();

        $editor = JFactory::getEditor();
        $this->assignRef('editor', $editor);

        parent::display($tpl);
    }

    function addToolbar()
    {
        JToolBarHelper::title(JText::_('Maintenance'), 'tag.png');

        $canDo = UsersHelper::getActions();
        if ($canDo->get('core.create')) {
            //JToolBarHelper::custom('migrate', 'default', '', JText::_('Execute'), false);
            //JToolBarHelper::spacer();
        }
        JToolBarHelper::back(JText::_('CONTROL PANEL'), 'index.php?option=com_cedtag');

    }


}
