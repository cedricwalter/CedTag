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


    function getSqlFor($param, $field, &$sql)
    {
        $isfield = CedTagsHelper::param($param, '1');
        $sql .= $isfield ? "$field as $field," : "";
        return $sql;
    }


    function exportTagsToCsv()
    {
        $app = JFactory::getApplication();

        $sql = "select ";
        $this->getSqlFor('export_csv_id', 'id', $sql);
        $this->getSqlFor('export_csv_name', 'name', $sql);
        $this->getSqlFor('export_csv_alias', 'alias', $sql);
        $this->getSqlFor('export_csv_description', 'description', $sql);
        $this->getSqlFor('export_csv_weight', 'weight', $sql);
        $this->getSqlFor('export_csv_hits', 'hits', $sql);
        $this->getSqlFor('export_csv_created', 'created', $sql);
        $this->getSqlFor('export_published_hits', 'hits', $sql);

        $sql = substr_replace($sql ,"",-1);

        $sql .= "  from #__cedtag_term;";

        $dbo = JFactory::getDbo();
        $dbo->setQuery($sql);
        $dbo->query();
        $items = $dbo->loadObjectList();

        $csvarray = array();
        foreach ($items as $item) {
            $datacsv = array($item->id, $item->name);
            $csvarray[] = $datacsv;
        }

        $fp = fopen("php://output", "w");

        $export_csv_delimiter = CedTagsHelper::param('export_csv_delimiter', null);
        $export_csv_enclosure = CedTagsHelper::param('export_csv_enclosure', null);
        foreach ($csvarray as $fields) {
            fputcsv($fp, $fields, $export_csv_delimiter, $export_csv_enclosure);
        }

        fclose($fp);

        $datetime = Jdate::getInstance()->toUnix();

        header("Content-type: text/csv");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=cedTag_export.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        $app->close();
    }


    function importTagsFromMetaKeys()
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
        }
        catch (Exception $e) {
            $executionResult = false;
            $executionMessages .= JText::_('-FAIL');
        }
        try {
            $executionMessages .= JText::_('Fill Table with the metadata');
            $query = 'INSERT INTO ' . $tmpTable . ' SELECT c.id, GROUP_CONCAT(t.name SEPARATOR ",") FROM #__content';
            $query .= 'AS c LEFT JOIN #__cedtag_term_content AS t2c ON t2c.cid=c.id LEFT JOIN #__cedtag_term AS t ON t.id=t2c.tid and t.published=\'1\' GROUP BY c.id;';
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
            $query = 'UPDATE #__content AS c, ' . $tmpTable . ' AS t SET c.metakey=t.metakey WHERE c.id=t.cid;';
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
            $query = 'DROP TABLE ' . $tmpTable . ';';
            $dbo->setQuery($query);
            $dbo->query();
            $executionMessages .= JText::_('-OK');
        }
        catch (Exception $e) {
            $executionResult = false;
            $executionMessages .= JText::_('-FAIL');
        }

        $msg = JText::_('Tags are successfully exported to all Joomla articles Meta Keywords');

        if (!$executionResult) {
            $msg = JText::_('We met some problems while exporting tags, please check!');
            $msg .= $executionMessages;
        }

        return true;
    }


}