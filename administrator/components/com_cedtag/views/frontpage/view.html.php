<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die();
jimport('joomla.application.component.view');

class CedTagViewFrontpage extends JView
{
    function display($tpl = null)
    {
        $this->defaultTpl($tpl);
    }

    function defaultTpl($tpl = null)
    {
        JToolBarHelper::title(JText::_('CedTag')." ".CedTagsHelper::getComponentVersion(), 'tag.png');
        //JURI::root().'media/com_cedtag/images/tag_logo48.png'
        //JToolBarHelper::help('JHELP_SITE_SYSTEM_INFORMATION');
        parent::display($tpl);
    }
}
