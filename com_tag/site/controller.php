<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @version $Id: com_tag.php 599 2010-06-06 23:26:33Z you $
 * @author waltercedric.com, Joomlatags.org
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.application.input');

class TagController extends JController
{
    function execute($task)
    {
        switch ($task) {
            case 'tag':
                $this->display();
                break;
            case 'save':
                $this->save();
                break;
            case 'add':
                $this->add();
                break;

            case 'warning':
                $this->warning();
                break;
            case 'tags':
                $this->allTags();
                break;
            default:
                $this->display();
        }
    }

    function display()
    {
        $view = JFactory::getApplication()->input->get('view', 'tag', 'string');
        //Set default view
        if (!isset($view)) {
            JFactory::getApplication()->input->set('view', 'tag');
        }
        parent::display();
    }

    function allTags()
    {
        JFactory::getApplication()->input->set('view', 'alltags');
        parent::display();
    }

    function warning()
    {
        JFactory::getApplication()->input->set('view', 'tag');
        JFactory::getApplication()->input->set('layout', 'warning');
        parent::display();
    }

    function add()
    {
        JFactory::getApplication()->input->set('view', 'tag');
        JFactory::getApplication()->input->set('layout', 'add');
        parent::display();
    }

    function save()
    {
        $id = JFactory::getApplication()->input->get('cid', 0, 'int');
        $tags = JFactory::getApplication()->input->get('tags', '', 'string');
        $combined = array();
        $combined[$id] = $tags;

        JModel::addIncludePath(JPATH_SITE . '/administrator/components/com_tag/models', 'TagModel');
        $model = JModel::getInstance('tag', 'TagModel', array('ignore_request' => true));
        $ok = $model->batchUpdate($combined);

        $message = JText::_('Tags successfully saved!');
        if ($ok) {
            $message = JText::_('Tags could not be Saved, please check!');
        }

        $refresh = JFactory::getApplication()->input->get('refresh', '', 'string');
        $script = "<script>window.parent.document.getElementById('sbox-window').close();";
        if ($refresh) {
            $script .= "window.parent.location.reload();";
        }
        $script .= "</script>";
        echo $script;
        exit();
        //parent::display();
        //$this->setRedirect( "index2.php?option=com_content&sectionid=-1&task=edit&cid[]=".$id,$msg );
    }
}
