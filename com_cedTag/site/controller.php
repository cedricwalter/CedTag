<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL v3.0 http://www.gnu.org/copyleft/gpl.html
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.application.input');
require_once JPATH_SITE . '/components/com_cedtag/helper/helper.php';


class CedTagController extends JController
{
    public function execute($task)
    {
        switch ($task) {
            case 'suggest':
                $this->suggest();
                break;
            case 'tag':
                $this->display();
                break;
            case 'save':
                $this->save();
                break;
            case 'add':
                $this->add();
                break;
            case 'delete':
                $this->delete();
                break;
            case 'tags':
                $this->allTags();
                break;
            default:
                $this->display();
        }
    }

    public function display($cachable = false, $urlparams = false)
    {
        $view = JFactory::getApplication()->input->get('view', 'tag', 'string');
        //Set default view
        if (!isset($view)) {
            JFactory::getApplication()->input->set('view', 'tag');
        }
        parent::display();
    }

    private function suggest()
    {
        $CedTagsHelper = new CedTagsHelper();
        $partialTag = JRequest::getVar('term', '', 'get', 'cmd');
        return $CedTagsHelper->getTagModel()->suggestJson($partialTag);
    }

    private function allTags()
    {
        JFactory::getApplication()->input->set('view', 'alltags');
        parent::display();
    }

    /*
     * Ajax entry point
     */
    private function add()
    {
        $id = JFactory::getApplication()->input->get('cid', 0, 'int');
        $CedTagsHelper = new CedTagsHelper();
        if ($CedTagsHelper->canUserDoTagOperations($id)) {
            $tags = JFactory::getApplication()->input->get('tags', '', 'string');
            $combined = array();
            $combined[$id] = $tags;
            $ok = $CedTagsHelper->getTagModel()->batchUpdate($combined, false);
            $message = $ok ? JText::_('Tags could not be Saved, please check!') : JText::_('Tags successfully saved!');
        }
    }

    /*
     * Ajax entry point
     */
    private function delete()
    {
        $id = JFactory::getApplication()->input->get('cid', 0, 'int');
        $CedTagsHelper = new CedTagsHelper();
        if ($CedTagsHelper->canUserDoTagOperations($id)) {
            $tag = JFactory::getApplication()->input->get('tags', '', 'string');
            $CedTagsHelper->getTagModel()->deleteTag($id, $tag);
        }
    }



}