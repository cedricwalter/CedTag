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

    function execute($task)
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
     * display the form
     * @return void
     */
    function display()
    {
        JFactory::getApplication()->input->set('view', 'export');
        parent::display();
    }

    function export()
    {
        $dbo = JFactory::getDbo();
        $executionResult = true;
        $executionMessages = "";
        $tmpTable = "tmpcontent" . uniqid();

        try {
            $executionMessages .= JText::_('First create a temp table ') . $tmpTable;
            $query = 'CREATE TABLE #__' . $tmpTable . ' (cid INTEGER(11) UNSIGNED NOT NULL, metakey TEXT NOT NULL);';
            $dbo->setQuery($query);
            $dbo->query();
            $executionMessages .= JText::_('-OK');
        }
        catch (Exception $e) {
            $executionResult = false;
            $executionMessages .= JText::_('-FAIL');
        }
        try {
            $executionMessages .= JText::_('Fill Table with the metadata');
            $query = 'INSERT INTO #__' . $tmpTable . ' SELECT c.id, GROUP_CONCAT(t.name SEPARATOR ",") FROM #__content';
            $query .= 'AS c LEFT JOIN #__cedtag_term_content AS t2c ON t2c.cid=c.id LEFT JOIN jos_tag_term AS t ON t.id=t2c.tid GROUP BY c.id;';
            $dbo->setQuery($query);
            $dbo->query();
            $executionMessages .= JText::_('-OK');
        }
        catch (Exception $e) {
            $executionResult = false;
            $executionMessages .= JText::_('-FAIL');
        }
        try {
            $executionMessages .= JText::_('Copy the metadata to the content');
            $query = 'UPDATE #__content AS c, #__' . $tmpTable . ' AS t SET c.metakey=t.metakey WHERE c.id=t.cid;';
            $dbo->setQuery($query);
            $dbo->query();
            $executionMessages .= JText::_('-OK');
        }
        catch (Exception $e) {
            $executionResult = false;
            $executionMessages .= JText::_('-FAIL');
        }
        try {
            $executionMessages .= JText::_('Copy the metadata to the content');
            $query = 'DROP TABLE #__' . $tmpTable . ';';
            $dbo->setQuery($query);
            $dbo->query();
            $executionMessages .= JText::_('-OK');
        }
        catch (Exception $e) {
            $executionResult = false;
            $executionMessages .= JText::_('-FAIL');
        }

        $msg = JText::_('Tags are successfully imported!');
        if (!$executionResult) {
            $msg = JText::_('We met some problems while exporting tags, please check!');
            $msg .= $executionMessages;
        }

        //parent::display();
        $this->setRedirect(JRoute::_('index.php?option=com_cedtag&controller=export'), $msg);
    }

}

?>
