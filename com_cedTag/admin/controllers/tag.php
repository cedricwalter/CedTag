<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die();

jimport('joomla.application.input');

class CedTagControllerCedTag extends JController
{

    function execute($task)
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

            default:
                $this->display();
        }
    }

    /**
     * display the form
     * @return void
     */
    function display()
    {
        JFactory::getApplication()->input->set('view', 'tag');
        parent::display();
    }

    function batchSave()
    {
        $ids = JFactory::getApplication()->input->get('id', array(0), 'array');
        $tags = JFactory::getApplication()->input->get('tag', array(0), 'array');
        $combined = $this->array_combine($ids, $tags);

        $model = $this->getModel('tag');
        $message = JText::_('TAGS SUCCESSFULLY SAVED');

        $ok = $model->batchUpdate($combined);
        if ($ok) {
            $message = JText::_('TAGS COULD NOT BE SAVED PLEASE CHECK');
        }
        //parent::display();
        $this->setRedirect("index.php?option=com_cedtag&controller=tag", $message);
    }

    function clearAll()
    {
        $msg = JText::_('ALL TAGS REMOVED');
        $model = $this->getModel('tag');
        $model->clearAll();

        //parent::display();
        $this->setRedirect("index.php?option=com_cedtag&controller=tag", $msg);
    }

    function save()
    {
        $id = JRequest::getVar('cid');
        $tags = JRequest::getVar('tags');
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


}

?>
