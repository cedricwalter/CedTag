<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die();
jimport('joomla.application.input');

class CedTagControllerMaintenance extends JController
{

    function __construct()
    {
        parent::__construct();
    }

    public function execute($task)
    {
        $input = JFactory::getApplication()->input;

        $tagX = $input->get('tagxxxx', null, 'string');
        $articles = $input->get('articles', null, 'int');

        $tagY = $input->get('tagyyyy', null, 'string');
        $tagYWeigtht = $input->get('tagyyyyweigtht', null, 'int');
        $tagYDescription = $input->get('tagyyyydescription', null, 'string');

        switch ($task) {
            case 'replace':
                $this->replace($tagX, $tagY, $tagYWeigtht, $tagYDescription);
                break;
            case 'add':
                $this->add($tagX, $tagY, $tagYWeigtht, $tagYDescription);
                break;
            case 'remove':
                $this->remove($tagX, $tagY);
                break;
            case 'publish':
                $this->publish($articles);
                break;
            case 'publish':
                $this->unpublish($articles);
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
        JFactory::getApplication()->input->set('view', 'maintenance');
        parent::display();
    }

    private function unpublish($articles)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('maintenance');
        $message = $model->unpublish($articles);

        $this->setRedirect("index.php?option=com_cedtag&controller=maintenance", $message);
    }

    private function publish($articles)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('maintenance');
        $message = $model->publish($articles);

        $this->setRedirect("index.php?option=com_cedtag&controller=maintenance", $message);
    }

    /**
     * Remove Tag xxxx to all articles which have also Tag yyyy
     */
    private function remove($tagX, $tagY)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('maintenance');
        $message = $model->remove($tagX, $tagY);

        $this->setRedirect("index.php?option=com_cedtag&controller=maintenance", $message);
    }

    /**
     * Add Tag yyyy to all articles which have also Tag xxxx
     */
    private function add($tagX, $tagY, $tagYWeigtht, $tagYDescription)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('maintenance');

        if (!isset($tagX) || !isset($tagY)) {
            $message = "Please provide Tags xxxx and Tags yyyy";
        } else {
            $message = $model->add($tagX, $tagY);
        }

        $this->setRedirect("index.php?option=com_cedtag&controller=maintenance", $message);
    }

    /**
     *
    Replace Tag xxxx with Tag yyyy in all articles
     */
    private function replace($tagX, $tagY, $tagYWeigtht, $tagYDescription)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('maintenance');
        $message = $model->replace($tagX, $tagY, $tagYWeigtht, $tagYDescription);

        $this->setRedirect("index.php?option=com_cedtag&controller=maintenance", $message);
    }

}

