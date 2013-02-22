<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die();
jimport('joomla.application.component.view');

class CedTagViewDiagnostic extends JView
{
    function display($tpl = null)
    {
        $this->defaultTpl($tpl);
    }

    function defaultTpl($tpl = null)
    {
        $this->addToolbar();

        //get Model
        $diagnostics = $this->get('diagnostic');
        $this->assign('diagnostics', $diagnostics);

        parent::display($tpl);
    }

    function addToolbar()
    {
        JToolBarHelper::title(JText::_('DIAGNOSTIC'), 'tag.png');
        JToolBarHelper::spacer();
        JToolBarHelper::back(JText::_('CEDTAG_CONTROL_PANEL'), 'index.php?option=com_cedtag');
    }
}
