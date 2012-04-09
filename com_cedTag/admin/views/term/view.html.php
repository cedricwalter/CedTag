<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die();
jimport('joomla.application.component.view');
jimport( 'joomla.application.input' );

class CedTagViewTerm extends JView
{

    function display($tpl = null)
    {
        $layout = JInput::get('layout', "default", 'STRING');
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
        JToolBarHelper::custom('batchsave', 'save', '', JText::_('SAVE'), false);
        JToolBarHelper::spacer();
        JToolBarHelper::back();
        //get params
        $params = JComponentHelper::getParams('com_cedtag');
        $this->assignRef('params', $params);

        parent::display($tpl);
    }

    function edit($tpl = null)
    {
        $term =& $this->get('term');
        if (isset($term->id)) {
            JToolBarHelper::title(JText::_('TERM EDIT'), 'tag.png');
        } else {
            JToolBarHelper::title(JText::_('TERM ADD'), 'tag.png');
        }
        JToolBarHelper::save('save', JText::_('SAVE'));
        JToolBarHelper::spacer();
        JToolBarHelper::back();
        //get params
        $params = JComponentHelper::getParams('com_cedtag');
        $this->assignRef('params', $params);
        //get data

        $this->assignRef('term', $term);
        parent::display($tpl);
    }

    function defaultTpl($tpl = null)
    {
        JToolBarHelper::title(JText::_('TERM MANAGER'), 'tag.png');
        JToolBarHelper::editListX();
        JToolBarHelper::spacer();
        JToolBarHelper::addNewX();
        JToolBarHelper::spacer();
        JToolBarHelper::custom('batchadd', 'new', '', JText::_('BATCH ADD'), false);
        JToolBarHelper::spacer();
        JToolBarHelper::deleteListX();
        JToolBarHelper::spacer();
        JToolBarHelper::back(JText::_('CONTROL PANEL'), 'index.php?option=com_cedtag');
        //JToolBarHelper::preferences( 'com_cedtag' );

        //get params
        $params = JComponentHelper::getParams('com_cedtag');
        $this->assignRef('params', $params);
        //get data
        $termList =& $this->get('termList');

        $this->assignRef('termList', $termList);

        parent::display($tpl);
    }

}
