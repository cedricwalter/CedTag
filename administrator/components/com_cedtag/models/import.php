<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/tag.php';
require_once JPATH_SITE . '/components/com_cedtag/helpers/helper.php';

class CedTagModelImport extends CedTagModelTag
{

    function __construct()
    {
        parent::__construct();
    }

    function termCheck($term)
    {
        $ignoreNumericTags = CedTagsHelper::param('IgnoeNumericTags', 0);
        if ($ignoreNumericTags) {
            if (is_numeric($term)) {
                echo('ignore:' . $term);
                return false;
            }
        }
        $minTagLength = CedTagsHelper::param('MinTagLength', 1);
        $len = JString::strlen($term);
        if ($len < $minTagLength) {
            return false;
        }
        return true;
    }

    function importTagsFromMetaKeys()
    {
        $dbo = JFactory::getDbo();
        $query = 'select id,metakey from #__content';
        $dbo->setQuery($query);
        $metaKeys = $dbo->loadObjectList();

        if (!empty($metaKeys)) {
            foreach ($metaKeys as $meta) {
                if (isset($meta->metakey) && empty($meta->metakey) == false) {
                    $cid = $meta->id;
                    if (!$this->isContentHasTags($cid)) {

                        $keys = explode(',', $meta->metakey);
                        $keysProcessed = array();
                        foreach ($keys as $key) {
                            $key = CedTagsHelper::preHandle($key);
                            if (empty($key) == false) {
                                if (!in_array($key, $keysProcessed)) {
                                    $keysProcessed[] = $key;
                                }
                            }
                        }

                        unset($keys);

                        $deleteTags = 'delete from #__cedtag_term_content where cid=' . $dbo->quote($cid);
                        $dbo->setQuery($deleteTags);
                        $dbo->query();
                        foreach ($keysProcessed as $key) {
                            $pass = $this->termCheck($key);
                            if ($pass) {
                                $tid = $this->storeTerm($key);
                                $this->insertContentterm($tid, $cid);
                            }
                        }
                    }
                }
            }
        }
        return true;
    }


    function importTagsFromJoomlaTags()
    {
        $dbo = JFactory::getDbo();
        $query1 = "INSERT INTO #__cedtag_term_content SELECT * FROM #__tag_term_content;";
        $query2 = "INSERT INTO #__cedtag_term SELECT * FROM #__tag_term;";

        try {
            $dbo->setQuery($query1);
            $dbo->query();

            $dbo->setQuery($query2);
            $dbo->query();
        } catch (Exception $e) {
            error_log("importTagsFromJoomlaTags" . $e);
            return false;
        }
        return true;
    }


    function joomlatagsPhil()
    {
        $dbo = JFactory::getDbo();

        $queryCopyTagsIntoTerm = "INSERT INTO #__cedtag_term (id,name,hits,weight,created) SELECT id, tagname, hits, weight, created FROM #__tag_tags;";
        $queryTagsAssociationToContentInfoTermContent = "INSERT INTO #__cedtag_term_content (tid,cid) SELECT tid,cid FROM #__tag_term_content;";

        try {
            $dbo->setQuery($queryCopyTagsIntoTerm);
            $dbo->query();

            $dbo->setQuery($queryTagsAssociationToContentInfoTermContent);
            $dbo->query();
        } catch (Exception $e) {
            error_log("joomlatagsPhil" . $e);
            return false;
        }
        return true;
    }


    function importTagsFromJTags()
    {
        $dbo = JFactory::getDbo();
        $query	= $dbo->getQuery(true);
        $query->select("tag_id");
        $query->select("item_id");
        $query->from("#__jtags_items");
        $query->where("component='com_content'");
        $dbo->setQuery($query);
        $jtagTags = $dbo->loadObjectList();

        $jtags = array();
        if (!empty($jtagTags)) {
            foreach ($jtagTags as $jtag) {
                if (array_key_exists($jtag->tag_id, $jtags)) {
                    $jtags[$jtag->tag_id][] = $jtag->item_id;
                } else {
                    $jtags[$jtag->tag_id] = array($jtag->item_id);
                }
            }
        }
        $jtermsQuery = 'select tag_id,name from #__jtags_tags';
        $dbo->setQuery($jtermsQuery);
        $jtagterms = $dbo->loadObjectList();
        if (!empty($jtagterms)) {
            foreach ($jtagterms as $jterm) {
                $pass = $this->termCheck($jterm->name);
                if ($pass) {
                    $tid = $this->storeTerm($jterm->name);
                    if (array_key_exists($jterm->tag_id, $jtags)) {
                        $cids = $jtags[$jterm->tag_id];
                        foreach ($cids as $cid) {
                            $this->storeContentTerm($tid, $cid);
                        }
                    }
                }
            }
        }

    }

}
