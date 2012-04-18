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
require_once JPATH_COMPONENT_SITE . '/helper/helper.php';

class CedTagModelImport extends CedTagModelTag
{
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
        $query = 'select id,metakey from #__content';
        $this->_db->setQuery($query);
        $metaKeys = $this->_db->loadObjectList();
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
                        $deleteTags = 'delete from #__cedtag_term_content where cid=' . $this->_db->quote($cid);
                        $this->_db->setQuery($deleteTags);
                        $this->_db->query();
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
        $db = JFactory::getDbo();
        $query1 = "INSERT INTO #__cedtag_term_content SELECT * FROM #__tag_term_content;";
        $query2 = "INSERT INTO #__cedtag_term SELECT * FROM #__tag_term;";

        try {
            $db->setQuery($query1);
            $db->query();
            $db->setQuery($query2);
            $db->query();
        } catch (Exception $e) {
            error_log("importTagsFromJoomlaTags".$e);
            return false;
        }
        return true;
    }

    function importTagsFromJTags()
    {
        $jtagsQuery = "select tag_id,item_id from #__jtags_items where component='com_content'";
        $this->_db->setQuery($jtagsQuery);
        $jtagTags = $this->_db->loadObjectList();
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
        $this->_db->setQuery($jtermsQuery);
        $jtagterms = $this->_db->loadObjectList();
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
