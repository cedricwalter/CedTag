<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die();
jimport('joomla.application.input');

class CedTagControllerImport extends JController
{

    function __construct()
    {
        parent::__construct();
    }

    public function execute($task)
    {
        switch ($task) {
            case 'import':
                $this->import();
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
    public function display($cacheable = false, $urlparams = false)
    {
        JFactory::getApplication()->input->set('view', 'import');
        parent::display();
    }


    function import()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('import');
        $source = JFactory::getApplication()->input->get('source', 'meta-keys');
        $importMessage = "";
        if ($source == 'meta-keys') {
            $importMessage = $model->importTagsFromMetaKeys();
        } else if ($source == 'jtags') {
            $importMessage = $model->importTagsFromJTags();
        } else if ($source == 'joomlatags') {
            $importMessage = $model->importTagsFromJoomlaTags();
        } else if ($source == 'joomlatags') {
            $importMessage = $model->joomlatagsPhil();
        }

        if (strlen($importMessage) != 0) {
            $message = JText::_('We met some problems while importing tags, please check!') . $importMessage;
        } else {
            $message = JText::_('Tags are successfully imported!');
        }
        $this->setRedirect("index.php?option=com_cedtag&controller=import", $message);
    }

}