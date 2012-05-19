<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die();
jimport('joomla.application.component.view');
jimport('joomla.application.input');

// userhelper for acl
require_once JPATH_SITE . '/administrator/components/com_users/helpers/users.php';

class CedTagViewTerm extends JView
{

    function display($tpl = null)
    {
        $layout = JRequest::getVar("layout");
        //TODO JInput::get('layout', "", 'STRING'); <- dont work
        switch ($layout) {
            case 'edit':
                $this->edit($tpl);
                break;
            case 'batchadd':
                $this->batchAdd($tpl);
                break;
            default:
                $this->defaultTpl($tpl);
        }

    }

    function batchAdd($tpl = null)
    {
        JToolBarHelper::title(JText::_('BATCH TERM ADD'), 'tag.png');

        $canDo = UsersHelper::getActions();
        if ($canDo->get('core.create')) {
            JToolBarHelper::custom('batchsave', 'save', '', JText::_('SAVE'), false);
            JToolBarHelper::spacer();
        }
        JToolBarHelper::back();
        //get params
        $params = JComponentHelper::getParams('com_cedtag');
        $this->assignRef('params', $params);

        parent::display($tpl);
    }

    function edit($tpl = null)
    {
        $term = $this->get('term');
        if (isset($term->id)) {
            JToolBarHelper::title(JText::_('TERM EDIT'), 'tag.png');
        } else {
            JToolBarHelper::title(JText::_('TERM ADD'), 'tag.png');
        }

        $canDo = UsersHelper::getActions();

        if ($canDo->get('core.edit')) {
            JToolBarHelper::save();
            JToolBarHelper::spacer();
        }

        JToolBarHelper::back();
        //get params
        $params = JComponentHelper::getParams('com_cedtag');
        $this->assignRef('params', $params);
        //get data
        $this->assignRef('term', $term);

        $editor = JFactory::getEditor();
        $this->assignRef('editor', $editor);

        parent::display($tpl);
    }

    function defaultTpl($tpl = null)
    {
        JToolBarHelper::title(JText::_('TERM MANAGER'), 'tag.png');

        $canDo = UsersHelper::getActions();
        if ($canDo->get('core.create')) {
            JToolBarHelper::custom('batchadd', 'new', '', JText::_('Batch Add'), false);
            JToolBarHelper::spacer();
        }
        if ($canDo->get('core.edit')) {
            JToolBarHelper::editListX();
            JToolBarHelper::spacer();
        }
        if ($canDo->get('core.edit.state')) {
            //publish / unpublish / archiveList / checkin
        }
        if ($canDo->get('core.delete')) {
            JToolBarHelper::deleteList(JText::_('Confirm Suppression'), 'remove', JText::_('Delete'));
            JToolBarHelper::spacer();
        }
        if ($canDo->get('core.edit.state')) {
            //trash
        }
        if ($canDo->get('core.admin')) {
            //preference
        }

        JToolBarHelper::back(JText::_('CONTROL PANEL'), 'index.php?option=com_cedtag');

        //get params
        $params = JComponentHelper::getParams('com_cedtag');
        $this->assignRef('params', $params);
        //get data
        $termList = $this->get('termList');

        $this->assignRef('termList', $termList);

        parent::display($tpl);
    }

}
