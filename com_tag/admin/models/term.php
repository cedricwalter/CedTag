<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @version $Id: com_tag.php 599 2010-06-06 23:26:33Z you $
 * @author waltercedric.com, Joomlatags.org
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once JPATH_COMPONENT_SITE . DS . 'helper/helper.php';
/**
 * Content Component Category Model
 *
 * @package        Joomla
 * @subpackage    Content
 * @since 1.5
 */
class TagModelTerm extends JModel
{
    function remove($ids)
    {
        $where = "";
        if (count($ids) > 1) {
            $where = ' id in(' . implode(',', $ids) . ')';
        } else if (count($ids) == 1) {
            $where = ' id=' . $ids[0];
        } else {
            return false;
        }
        $query = 'delete from #__tag_term where ' . $where;
        JDatabase::getInstance();
        $db = JFactory::getDbo();
        $db->setQuery($query);
        return $db->query();
    }

    function update($id, $name, $description, $weight)
    {
        $name = JoomlaTagsHelper::isValidName($name);
        if (!$name) {
            return false;
        }
        $updateQuery = 'update #__tag_term set name="' . $name . '", weight="' . $weight . '", description="' . $description . '" where id=' . $id;
        $db = JFactory::getDbo();
        $db->setQuery($updateQuery);
        return $db->query();
    }

    function store($name, $description = NULL, $weight = 0)
    {
        $name = JoomlaTagsHelper::isValidName($name);
        if (!$name) {
            return false;
        }
        $query = "SELECT * FROM #__tag_term where binary name='" . $name . "'";
        $db = JFactory::getDbo();
        $$db->setQuery($query, 0, 1);
        $tagInDB = $db->loadObject();
        if (isset($tagInDB) & isset($tagInDB->id)) {
            $needUpdate = false;
            $updateQuery = 'update #__tag_term set ';
            if (isset($description) && !empty($description)) {
                $needUpdate = true;
                $updateQuery .= "description='" . $description . "'";
            }
            if (isset($weight)) {
                if ($needUpdate) {
                    $updateQuery .= ', weight=' . $weight;
                } else {
                    $updateQuery .= ' weight=' . $weight;
                    $needUpdate = true;
                }
            }
            if ($needUpdate) {
                $updateQuery .= ' where id=' . $tagInDB->id;
                $db->setQuery($updateQuery);
                $db->query();
            }
            return $tagInDB->id;
        } else {
            $insertQuery = "insert into #__tag_term (name";
            $valuePart = " values('" . $name . "'";
            if (isset($description) && !empty($description)) {
                $insertQuery .= ",description";
                $valuePart .= ",'" . $description . "'";
            }
            if (isset($weight)) {
                $insertQuery .= ",weight";
                $valuePart .= "," . $weight;
            }
            $date =& JFactory::getDate();
            $now = JDate::toSql($date);
            $insertQuery .= ',created) ';
            $valuePart .= ',' . $db->Quote($now) . ')';
            $db->setQuery($insertQuery . $valuePart);
            $db->query();
            return $db->insertid();
        }
    }

    function insertTerms($terms)
    {
        $terms = JoomlaTagsHelper::isValidName($terms);
        if (!$terms) {
            return false;
        }
        //$terms=JoomlaTagsHelper::preHandle($terms);
        $termsInArray = explode(',', $terms);
        if (empty($termsInArray)) {
            return false;
        }
        $isok = true;
        foreach ($termsInArray as $term) {
            $this->store($term);
        }
        return $isok;
    }

    function deleteContentTerms($cid)
    {
        $db = JFactory::getDbo();
        $deleteQuery = 'delete from #__tag_term_content where cid=' . $cid;
        $db->setQuery($deleteQuery);
        $db->query();
    }

    function insertContentTerms($cid, $tids)
    {
        foreach ($tids as $tid) {
            insertContentterm($tid, $cid);
        }
    }

    function insertContentterm($tid, $cid)
    {
        $db = JFactory::getDbo();
        $insertQuery = 'insert into #__tag_term_content (tid,cid) values(' . $tid . ',' . $cid . ')';
        $db->setQuery($insertQuery);
        $db->query();
    }

    function termsForContent($cid)
    {
        $db = JFactory::getDbo();
        $query = 'select t.id as tid,t.name from #__tag_term as t left join #__tag_term_content as c  on c.tid=t.id where c.cid=' . $cid . ' order by t.weight desc,t.name';
        $db->setQuery($query);
        return $db->loadColumn();
    }

    function getTermList()
    {
        $mainframe =& JFactory::getApplication();
        $search = $mainframe->getUserStateFromRequest('articleelement.search', 'search', '', 'string');
        $search = JString::strtolower($search);
        $where = null;
        if (!is_null($search)) {
            $where = " where name like'%" . $search . "%' ";
        }
        $db = JFactory::getDbo();
        $query = "select count(*) as ct from #__tag_term " . $where;
        $db->setQuery($query);
        $db->query();
        $total = $db->loadResult();

        $jinput = JFactory::getApplication()->input;
        $limitstart = $jinput->get('limitstart', 0, '', 'int');
        $params = JComponentHelper::getParams('com_tag');
        $limit = $params->get('tag_page_limit', 30);

        $query = 'select t.id,t.name,t.description,t.weight,t.created,t.hits,count(c.cid)as count from #__tag_term  as t  left join  #__tag_term_content as c  on c.tid=t.id ' . $where . ' group by(t.id) order by t.name';
        $db->setQuery($query, $limitstart, $limit);
        jimport('joomla.html.pagination');


        $this->page = new JPagination($total, $limitstart, $limit);
        $this->list = $db->loadObjectList();
        return $this;


    }

    function getTerm()
    {
        $db = JFactory::getDbo();
        $jinput = JFactory::getApplication()->input;
        $id = $jinput->get('cid', array(0), '', 'array');
        $query = 'select * from #__tag_term  where id=' . $id[0];
        $db->setQuery($query);
        return $db->loadObject();
    }

}
