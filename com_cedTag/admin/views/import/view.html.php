<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die();
jimport('joomla.application.component.view');

class CedTagViewImport extends JView
{

    function display($tpl = null)
    {
        $this->defaultTpl($tpl);
    }

    function defaultTpl($tpl = null)
    {
        JToolBarHelper::title(JText::_('IMPORT TAGS FROM OTHER COMPONENTS'), 'tag.png');

        JToolBarHelper::spacer();
        JToolBarHelper::custom('import', 'default', '', JText::_('IMPORT'), false);
        JToolBarHelper::spacer();
        JToolBarHelper::back(JText::_('CONTROL PANEL'), 'index.php?option=com_cedtag');

        parent::display($tpl);
    }


}
