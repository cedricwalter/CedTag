<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die();

jimport('joomla.application.controller');
jimport( 'joomla.application.input' );

class CedTagControllerTerm extends JController
{

    public function execute($task)
    {
        switch ($task) {
            case 'save':
                $this->save();
                break;
            case 'edit':
            case 'add':
                $this->edit();
                break;
            case 'remove':
                $this->remove();
                break;
            case 'batchadd':
                $this->batchAdd();
                break;
            case 'batchsave':
                $this->batchSave();
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
        JFactory::getApplication()->input->set('view', 'term');
        parent::display();
    }


    /**
     * save categories
     */
    function save()
    {
        $jinput = JFactory::getApplication()->input;
        $id = $jinput->get('cid', 0, 'POST');
        $name = $jinput->get('name', '', 'POST');
        $description = $jinput->get('description', '', 'POST', 'string', JREQUEST_ALLOWHTML);
        $weight = $jinput->get('weight', '', 'POST');
        $model = $this->getModel('term');
        $isok = true;
        if (isset($id[0]) && $id[0]) {
            $isok = $model->update($id[0], $name, $description, $weight);
        } else {
            $isok = $model->store($name, $description, $weight);
        }
        if (!$isok) {
            $msg = JText::_('TERM COULD NOT BE CREATED PLEASE CHECK');
        } else {
            $msg = JText::_('TERM SUCCESSFULLY CREATED');
        }
        $this->setRedirect("index.php?option=com_cedtag&controller=term", $msg);

        //$this->getEditor()->save($this->description);
    }

    function edit()
    {
        JFactory::getApplication()->input->set('view', 'term');
        JFactory::getApplication()->input->set('layout', 'edit');
        parent::display();
    }

    function remove()
    {
        $jinput = JFactory::getApplication()->input;
        $ids = $jinput->get('cid', 0, 'POST');
        $model = $this->getModel('term');
        if (!$model->remove($ids)) {
            $msg = JText::_('TERM COULD NOT BE REMOVED PLEASE CHECK');
        } else {
            $msg = JText::_('TERM SUCCESSFULLY REMOVED');
        }
        $this->setRedirect("index.php?option=com_cedtag&controller=term", $msg);
    }

    function batchAdd()
    {
        JFactory::getApplication()->input->set('view', 'term');
        JFactory::getApplication()->input->set('layout', 'batchadd');
        parent::display();

    }

    function batchSave()
    {
        $jinput = JFactory::getApplication()->input;
        $terms = $jinput->get('names', '', 'POST');
        $msg = null;
        $terms = trim($terms);
        if (isset($terms) && $terms) {
            $model = $this->getModel('term');
            $isok = $model->insertTerms($terms);

            if (!$isok) {
                $msg = JText::_('TERMS COULD NOT BE CREATED PLEASE CHECK');
            } else {
                $msg = JText::_('TERMS SUCCESSFULLY CREATED');
            }
        } else {
            $msg = JText::_('TERMS CAN NOT BE BLANK');
        }
        $this->setRedirect("index.php?option=com_cedtag&controller=term", $msg);

    }
}

?>
