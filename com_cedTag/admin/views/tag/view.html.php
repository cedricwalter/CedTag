<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/


defined('_JEXEC') or die();
jimport('joomla.application.component.view');
jimport( 'joomla.application.input' );

class CedTagViewTag extends JView
{

    function display($tpl = null)
    {
        $layout = JInput::get('layout', "default", 'STRING');
        switch ($layout) {
            case 'add':
                $this->add($tpl);
                break;
            case 'warning':
                $this->warning($tpl);
                break;
            default:
                $this->defaultTpl($tpl);
        }


    }

    function defaultTpl($tpl = null)
    {
        JToolBarHelper::title(JText::_('Tag Manager'), 'tag.png');
        JToolBarHelper::custom('batchsave', 'save', '', JText::_('SAVE'), false);
        JToolBarHelper::spacer();
        JToolBarHelper::custom('clearall', 'delete', '', JText::_('CLEAR ALL'), false);
        JToolBarHelper::spacer();
        JToolBarHelper::back(JText::_('CONTROL PANEL'), 'index.php?option=com_cedtag');
        //get params
        $params = JComponentHelper::getParams('com_cedtag');
        $this->assignRef('params', $params);
        //get data
        $tagList =& $this->get('tagList');

        $this->assignRef('tagList', $tagList);

        parent::display($tpl);
    }

    function add($tpl = null)
    {
        $tags =& $this->get('tagsForArticle');
        $this->assignRef('tags', $tags);
        parent::display($tpl);
    }

    function warning($tpl = null)
    {
        parent::display($tpl);
    }

}
