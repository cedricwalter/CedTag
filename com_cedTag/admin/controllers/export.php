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
        $db = JFactory::getDbo();
        $ok = true;
        try {
            echo "First create a temp table 'tmpcontent'";
            $query = 'CREATE TABLE #__tmpcontent (cid INTEGER(11) UNSIGNED NOT NULL, metakey TEXT NOT NULL);';
            $db->setQuery($query);
            $db->query();
        }
        catch (Exception $e) {
            $ok = false;
        }
        try {
            echo "Fill 'tmpcontent' with the metadata";
            $query = "INSERT INTO #__tmpcontent SELECT c.id, GROUP_CONCAT(t.name SEPARATOR ',') FROM #__content AS c LEFT JOIN #__cedtag_term_content AS t2c ON t2c.cid=c.id LEFT JOIN jos_tag_term AS t ON t.id=t2c.tid GROUP BY c.id";
            $db->setQuery($query);
            $db->query();
        }
        catch (Exception $e) {
            $ok = false;
        }
        try {
            echo "Copy the metadata to the content";
            $query = "UPDATE #__content AS c, #__tmpcontent AS t SET c.metakey=t.metakey WHERE c.id=t.cid;";
            $db->setQuery($query);
            $db->query();
        }
        catch (Exception $e) {
            $ok = false;
        }
        try {
                    echo "Copy the metadata to the content";
                    $query = "DROP TABLE #__tmpcontent;";
                    $db->setQuery($query);
                    $db->query();
                }
                catch (Exception $e) {
                    $ok = false;
                }


        $msg = JText::_('Tags are successfully imported!');
        if (!$ok) {
            $msg = JText::_('We met some problems while exporting tags, please check!');
        }

        //parent::display();
        $this->setRedirect("index.php?option=com_cedtag&controller=export", $msg);

    }

}

?>
