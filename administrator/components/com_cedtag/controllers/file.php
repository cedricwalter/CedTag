<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

class CedTagControllerFile extends JController
{

    function __construct()
    {
        parent::__construct();
    }

    public function execute($task)
    {
        switch ($task) {
            case 'save':
                $this->save();
                break;
            case 'apply':
                $this->apply();
                break;
            case 'restore':
                $this->restore();
                break;
            default:
                $this->display();
        }
    }

    public function display($cacheable = false, $urlParams = false)
    {
        JFactory::getApplication()->input->set('view', $this->getDefaultView());
        parent::display();
    }

    public function apply()
    {
        $this->saveOnly();
        JFactory::getApplication()->input->set('view', $this->getDefaultView());
        parent::display();
    }

    public function save()
    {
        $this->saveOnly();
        $this->setRedirect(JRoute::_('index.php?option=com_cedtag'));
        $this->redirect();
    }

    protected function saveOnly()
    { //JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $updatedFileContent = JFactory::getApplication()->input->get('content', '', 'STRING');
        $updatedFileContent = $this->transform($updatedFileContent);

        $file = $this->getFile();
        $content = trim($updatedFileContent);
        JFile::write($file, $content);
    }


    public function transform($fileContent)
    {
        return $fileContent;
    }

    /**
     * to be overriden
     * @return string
     */
    public function getDefaultFile()
    {
        return "";
    }

    /**
     * to be overriden
     * @return string
     */
    public function getFile()
    {
        return "";
    }

    /**
     * dont name this getView() as joomla use this already
     * to be overriden
     * @return string
     */
    public function getDefaultView()
    {
        return "";
    }

    public function restore()
    {
        JFile::copy($this->getDefaultFile(), $this->getFile());
        JFactory::getApplication()->input->set('view', $this->getDefaultView());
        parent::display();
    }
}
