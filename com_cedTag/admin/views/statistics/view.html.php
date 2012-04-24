<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die();
jimport('joomla.application.component.view');

class CedTagViewStatistics extends JView
{

    function display($tpl = null)
    {
        $this->defaultTpl($tpl);
    }

    function defaultTpl($tpl = null)
    {
        $this->addToolbar();

        $statistics = $this->get('statistics');
        $this->assign('statistics', $statistics);

        parent::display($tpl);
    }

    function addToolbar()
    {
        JToolBarHelper::title(JText::_('STATISTICS'), 'tag.png');
        JToolBarHelper::spacer();
        JToolBarHelper::back(JText::_('CONTROL PANEL'), 'index.php?option=com_cedtag');

    }


}
