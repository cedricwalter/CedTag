<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

class CedTagControllerFile extends JController
{

    public function execute($task)
    {
        switch ($task) {
            case 'save':
                $this->save();
                break;
            case 'restore':
                $this->restore();
                break;
            default:
                $this->display();
        }
    }

    public function display()
    {
        JFactory::getApplication()->input->set('view', $this->getDefaultView());
        parent::display();
    }

    public function save()
    {
        $updatedFileContent = JFactory::getApplication()->input->get('content', '', 'STRING');
        JFile::write($this->getFile(), trim($updatedFileContent));
        JFactory::getApplication()->input->set('view', $this->getDefaultView());
        parent::display();
    }

    public function getDefaultFile()
    {
    }

    public function getFile()
    {
    }

    // dont name this getView() as joomla use this already
    public function getDefaultView()
    {
    }

    public function restore()
    {
        JFile::copy($this->getDefaultFile(), $this->getFile());
        JFactory::getApplication()->input->set('view', $this->getDefaultView());
        parent::display();
    }
}

?>