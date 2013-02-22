<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die();

jimport('joomla.application.input');

class CedTagControllerTag extends JController
{
    function __construct()
    {
        parent::__construct();
    }

    public function execute($task)
    {
        switch ($task) {
            case 'batchsave':
                $this->batchSave();
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
            case 'clearall':
                $this->clearAll();
                break;
            case 'ajaxSuggest':
                $this->ajaxSuggest();
                break;
            case 'ajaxAdd':
                $this->ajaxAdd();
                break;
            case 'ajaxDelete':
                $this->ajaxDelete();
                break;
            default:
                $this->display();
        }
    }

    /**
     * @param bool $cachable
     * @param bool $urlparams
     * @return \JController|void
     */
    public function display($cachable = false, $urlparams = false)
    {
        JFactory::getApplication()->input->set('view', 'tag');
        parent::display();
    }

    function batchSave()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $ids = JFactory::getApplication()->input->get('id', array(0), 'array');
        $tags = JFactory::getApplication()->input->get('tag', array(0), 'array');
        $combined = $this->array_combine($ids, $tags);

        $model = $this->getModel('tag');
        $message = JText::_('TAGS SUCCESSFULLY SAVED');

        $ok = $model->batchUpdate($combined);
        if ($ok) {
            $message = JText::_('TAGS COULD NOT BE SAVED PLEASE CHECK');
        }

        $this->setRedirect("index.php?option=com_cedtag&controller=tag", $message);
    }

    function clearAll()
    {
        $message = JText::_('ALL TAGS REMOVED');
        $model = $this->getModel('tag');
        $model->clearAll();

        $this->setRedirect("index.php?option=com_cedtag&controller=tag", $message);
    }

    function save()
    {
        $id = JFactory::getApplication()->input->get('cid');
        $tags = JFactory::getApplication()->input->get('tags');
        //$id = JRequest::getVar('cid');
        //$tags = JRequest::getVar('tags');

        $combined = array();
        $combined[$id] = $tags;

        $model = $this->getModel('tag');
        $msg = "";
        $ok = $model->batchUpdate($combined);
        if ($ok) {
            $msg = JText::_('TAGS COULD NOT BE SAVED PLEASE CHECK');
        } else {
            $msg = JText::_('TAGS SUCCESSFULLY SAVED');
        }
        // echo('<script> alert("'.$msg.'"); window.history.go(-1); </script>');

        echo "<script>window.parent.document.getElementById('sbox-window').close()</script>";
        exit();
        //parent::display();
        //$this->setRedirect( "index.php?option=com_content&sectionid=-1&task=edit&cid[]=".$id,$msg );
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
        JFactory::getApplication()->input->set('tmpl', 'component');
        parent::display();
    }

    function array_combine($keys, $values)
    {
        $result = array();
        foreach (array_map(null, $keys, $values) as $pair) {
            $result[$pair[0]] = $pair[1];
        }
        return $result;
    }

    /*
     * Ajax entry point
     */
    private function ajaxSuggest()
    {
        //this will check for the token in the url.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $CedTagsHelper = new CedTagsHelper();
        $partialTag = JFactory::getApplication()->input->get('term');

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

        if ($CedTagsHelper->canUserDoTagOperations($id)) {
            $tags = JFactory::getApplication()->input->get('tags', '', 'string');
            $combined = array();
            $combined[$id] = $tags;
            $ok = $CedTagsHelper->getTagModel()->batchUpdate($combined, false);
            //$message = $ok ? JText::_('Tags could not be Saved, please check!') : JText::_('Tags successfully saved!');
        }
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
