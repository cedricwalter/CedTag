<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die();
jimport('joomla.application.component.view');

// userhelper for acl
require_once JPATH_SITE . '/administrator/components/com_users/helpers/users.php';

class CedTagViewImport extends JView
{

    function display($tpl = null)
    {
        $this->defaultTpl($tpl);
    }

    function defaultTpl($tpl = null)
    {
        JToolBarHelper::title(JText::_('IMPORT TAGS FROM OTHER COMPONENTS'), 'tag.png');

        $canDo = UsersHelper::getActions();
        if ($canDo->get('core.create')) {

            $bar = JToolBar::getInstance('toolbar');
            $bar->appendButton('Confirm', JText::_('Are you sure you want to import?'), 'default', JText::_('Import'), 'import', false);

            JToolBarHelper::spacer();
        }
        JToolBarHelper::back(JText::_('CEDTAG_CONTROL_PANEL'), 'index.php?option=com_cedtag');

        parent::display($tpl);
    }


}
