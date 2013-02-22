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

require_once (JPATH_SITE . '/components/com_cedtag/helpers/helper.php');

/**
 *
 */
class CedTagController extends JController
{
    /**
     * @param string $task
     * @return mixed|void
     */
    public function execute($task)
    {
        switch ($task) {
            case 'ajaxSuggest':
                $this->ajaxSuggest();
                break;
            case 'ajaxAdd':
                $this->ajaxAdd();
                break;
            case 'ajaxDelete':
                $this->ajaxDelete();
                break;
            case 'tag':
                $this->display();
                break;
            case 'tags':
                $this->allTags();
                break;
            default:
                $this->display();
        }
    }

    /**
     * @param bool $cachable
     * @param bool $urlparams
     * @return JController|void
     */
    public function display($cachable = false, $urlparams = false)
    {
        $view = JFactory::getApplication()->input->get('view', null, 'string');
        //Set default view
        if (!isset($view)) {
            JFactory::getApplication()->input->set('view', 'tag');
        }

        parent::display();
    }

    /**
     *
     */
    private function allTags()
    {
        JFactory::getApplication()->input->set('view', 'alltags');
        parent::display();
    }

    private function ajaxSuggest()
    {
        //this will check for the token in the url.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $CedTagsHelper = new CedTagsHelper();
        $partialTag = JFactory::getApplication()->input->get('term', '', 'string');
        return $CedTagsHelper->getTagModel()->suggestJson($partialTag);
    }

    /*
     * Ajax entry point
     */
    private function ajaxAdd()
    {
        //this will check for the token in the url.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $id = JFactory::getApplication()->input->get('cid', 0, 'int');
        $CedTagsHelper = new CedTagsHelper();

        //here in backend it is not authorized beacuse not logger in
        if ($CedTagsHelper->canUserDoTagOperations($id)) { }
            $tags = JFactory::getApplication()->input->get('tags', '', 'string');
            $combined = array();
            $combined[$id] = $tags;
            $ok = $CedTagsHelper->getTagModel()->batchUpdate($combined, false);
            //$message = $ok ? JText::_('Tags could not be Saved, please check!') : JText::_('Tags successfully saved!');
    }

    /*
     * Ajax entry point
     */
    private function ajaxDelete()
    {
        //this will check for the token in the url.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $id = JFactory::getApplication()->input->get('cid', 0, 'int');
        $CedTagsHelper = new CedTagsHelper();
        if ($CedTagsHelper->canUserDoTagOperations($id)) {
            $tag = JFactory::getApplication()->input->get('tags', '', 'string');
            $CedTagsHelper->getTagModel()->deleteTag($id, $tag);
        }
    }



}