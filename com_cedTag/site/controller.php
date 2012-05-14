<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.application.input');

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
        if ($tags = JRequest::getVar('tags', '', 'get', 'cmd')) {

            $array = explode(",", $tags);
            $lastElement = array_pop($array);
            if (isset($lastElement)) {
            }
            //http://forum.joomla.org/viewtopic.php?f=642&t=701004
            $db = JFactory::getDBO();
            $query = 'SELECT name FROM #__cedtag_term WHERE name like ' . $db->Quote($lastElement . "%");
            $db->setQuery($query);
            $result = $db->loadObject();
            if ($result) {

                /*  $data = new stdClass;
                                $data->cedTagSuggest = '{"0":"7","1":"8","2":"9","3":"5"}'; // Just to implement your example
                                $data->cedTagSuggest = json_decode(trim($data->cedTagSuggest), true); // Returns Associative Array
                            }
                */

                $response['html'] = JText::_('COM_AA4J_UNAME_NOT_AVAILABLE');
                $response['msg'] = 'false';
            } else {
                $response['html'] = JText::_('COM_AA4J_UNAME_AVAILABLE');
                $response['msg'] = 'true';
            }


        }
        echo (json_encode($response));

        return true;
    }


    private
    function allTags()
    {
        JFactory::getApplication()->input->set('view', 'alltags');
        parent::display();
    }

    private
    function warning()
    {
        JFactory::getApplication()->input->set('view', 'tag');
        JFactory::getApplication()->input->set('layout', 'warning');
        parent::display();
    }

    private
    function add()
    {
        JFactory::getApplication()->input->set('view', 'tag');
        JFactory::getApplication()->input->set('layout', 'add');
        parent::display();
    }

    private
    function save()
    {
        $id = JFactory::getApplication()->input->get('cid', 0, 'int');
        $tags = JFactory::getApplication()->input->get('tags', '', 'string');
        $combined = array();
        $combined[$id] = $tags;

        JModel::addIncludePath(JPATH_SITE . '/administrator/components/com_cedtag/models', 'TagModel');
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