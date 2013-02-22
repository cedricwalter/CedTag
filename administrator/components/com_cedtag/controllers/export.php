<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die();
jimport('joomla.application.input');

class CedTagControllerExport extends JController
{

    function __construct()
    {
        parent::__construct();
    }

    public function execute($task)
    {
        switch ($task) {
            case 'export':
                $this->export();
                break;
            default:
                $this->display();
        }
    }

    /**
     * @param bool $cacheable
     * @param bool $urlParams
     * @return JController|void
     */
    public function display($cacheable = false, $urlParams = false)
    {
        JFactory::getApplication()->input->set('view', 'export');
        parent::display();
    }

    private function export()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('export');
        $input = JFactory::getApplication()->input;

        $destination = $input->get('destination', 'meta-keys');
        $message = "";
        if ($destination == 'meta-keys') {
            $message = $model->exportTagsToMetaKeys();
        } else if ($destination == 'csv') {
            $message = $model->exportTagsToCsv();
        }

        $this->setRedirect("index.php?option=com_cedtag&controller=export", $message);
    }

}

