<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/tag.php';

class CedTagModelExport extends CedTagModelTag
{

    function __construct()
    {
        parent::__construct();
    }

    private function getSqlFor($param, $field, &$sql)
    {
        $isField = CedTagsHelper::param($param, '1');
        $sql .= $isField ? "$field as $field," : "";
        return $sql;
    }


    public function exportTagsToCsv()
    {
        $items = $this->fetchData();

        $csvArray = array();
        foreach ($items as $item) {
            $dataCsv = array($item->id, $item->name);
            $csvArray[] = $dataCsv;
        }

        $this->outputHeaders();

        // create a file pointer connected to the output stream
        $fp = @fopen("php://output", "w");

        $headerDisplayed = false;
        try {
            foreach ($items as $item) {
                //typecast (object)$array; and (array)$stdClass; which will do the conversion, see PHP Manual for objects
                //http://php.net/manual/en/language.types.object.php
                $itemsArray = (array)$item;

                if (!$headerDisplayed) {
                    fputcsv($fp, array_keys($itemsArray));
                    $headerDisplayed = true;
                }

                $arrayValues = array_values($itemsArray);
                fputcsv($fp, $arrayValues);
            }
        } catch (Exception $e) {

        }
        fclose($fp);

        $app = JFactory::getApplication();
        $app->close();
    }

    /**
     * output headers so that the file is downloaded rather than displayed
     */
    private function outputHeaders()
    {
        header("Content-type: text/csv");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-type: application/csv");
        header('Content-Description: File Transfer');
        header("Content-Disposition: attachment; filename=cedTag_export.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
    }

    private function fetchData()
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('*');
        $query->from('#__cedtag_term');
        $dbo->setQuery($query);
        $dbo->query();
        $items = $dbo->loadObjectList();
        return $items;
    }

    public function exportTagsToMetaKeys()
    {
        $dbo = JFactory::getDbo();
        $executionResult = true;
        $executionMessages = "";
        $tmpTable = "#__cedtag_export";

        try {
            $executionMessages .= JText::_('First create a temp table ') . $tmpTable;
            $query = "CREATE TABLE " . $tmpTable . " (cid INTEGER(11) UNSIGNED NOT NULL, metakey TEXT NOT NULL default '');";
            $dbo->setQuery($query);
            $dbo->query();
            $executionMessages .= JText::_('-OK');
        } catch (Exception $e) {
            $executionResult = false;
            $executionMessages .= JText::_('-FAIL');
        }
        try {
            $executionMessages .= JText::_('Fill Table with the metadata');
            $query = 'INSERT INTO ' . $tmpTable . ' SELECT c.id, GROUP_CONCAT(t.name SEPARATOR ",") FROM #__content';
            $query .= ' AS c LEFT JOIN #__cedtag_term_content AS t2c ON t2c.cid=c.id LEFT JOIN #__cedtag_term AS t ON t.id=t2c.tid and t.published=\'1\' GROUP BY c.id;';
            $dbo->setQuery($query);
            $dbo->query();
            $executionMessages .= JText::_('-OK');
        } catch (Exception $e) {
            $executionResult = false;
            $executionMessages .= JText::_('-FAIL');
        }
        try {
            $executionMessages .= JText::_('Copy the metadata to the content');
            $query = 'UPDATE #__content AS c, ' . $tmpTable . ' AS t SET c.metakey=t.metakey WHERE c.id=t.cid;';
            $dbo->setQuery($query);
            $dbo->query();
            $executionMessages .= JText::_('-OK');
        } catch (Exception $e) {
            $executionResult = false;
            $executionMessages .= JText::_('-FAIL');
        }
        try {
            $executionMessages .= JText::_('Drop table ' . $tmpTable);
            $query = 'DROP TABLE ' . $tmpTable . ';';
            $dbo->setQuery($query);
            $dbo->query();
            $executionMessages .= JText::_('-OK');
        } catch (Exception $e) {
            $executionResult = false;
            $executionMessages .= JText::_('-FAIL');
        }

        $message = JText::_('Tags are successfully exported to all Joomla! articles Meta Keywords'); //.$executionMessages;

        if (!$executionResult) {
            $message = JText::_('We met some problems while exporting tags, please check!');
            $message .= $executionMessages;
        }

        return $message;
    }


}