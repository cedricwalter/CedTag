<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once JPATH_COMPONENT_SITE . DS . 'helper/helper.php';

class CedTagModelTerm extends JModel
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
        $query = 'delete from #__cedtag_term where ' . $where;
        $dbo = JFactory::getDbo();
        $dbo->setQuery($query);
        return $dbo->query();
    }

    function update($id, $name, $description, $weight)
    {
        $dbo = JFactory::getDbo();
        $name = CedTagsHelper::isValidName($name);
        if (!$name) {
            return false;
        }
        $updateQuery = 'update #__cedtag_term set name=' . $dbo->quote($name) . ', weight=' . $dbo->quote($weight) . ', description=' . $dbo->quote($description) . ' where id=' . $dbo->quote($id);
        $dbo->setQuery($updateQuery);
        return $dbo->query();
    }

    function store($name, $description = NULL, $weight = 0)
    {
        $dbo = JFactory::getDbo();
        $name = CedTagsHelper::isValidName($name);
        if (!$name) {
            return false;
        }
        $query = "SELECT id as id FROM #__cedtag_term where binary name=" .$dbo->quote($name).";";
        $dbo->setQuery($query);
        $tagAlreadyExisting = $dbo->loadObject();

        if (isset($tagAlreadyExisting) & isset($tagAlreadyExisting->id)) {
            $needUpdate = false;
            $updateQuery = 'update #__cedtag_term set ';
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
                $updateQuery .= ' where id=' . $tagAlreadyExisting->id;
                $dbo->setQuery($updateQuery);
                $dbo->query();
            }
            return $tagAlreadyExisting->id;
        } else {
            $insertQuery = "insert into #__cedtag_term (name";
            $valuePart = " values('" . $name . "'";
            if (isset($description) && !empty($description)) {
                $insertQuery .= ",description";
                $valuePart .= ",'" . $description . "'";
            }
            if (isset($weight)) {
                $insertQuery .= ",weight";
                $valuePart .= "," . $weight;
            }
            $date = JFactory::getDate();
            $now = JDate::getInstance()->toSql($date);
            $insertQuery .= ',created) ';
            $valuePart .= ',' . $dbo->Quote($now) . ')';
            $dbo->setQuery($insertQuery . $valuePart);
            $dbo->query();
            return $dbo->insertid();
        }
    }

    function insertTerms($terms)
    {
        //$terms = CedTagsHelper::isValidName($terms);
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
            $isok = $this->store($term);
        }
        return $isok;
    }

    function deleteContentTerms($cid)
    {
        $dbo = JFactory::getDbo();
        $deleteQuery = 'delete from #__cedtag_term_content where cid=' . $dbo->quote($cid);
        $dbo->setQuery($deleteQuery);
        $dbo->query();
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
        $insertQuery = 'insert into #__cedtag_term_content (tid,cid) values(' . $tid . ',' . $cid . ')';
        $db->setQuery($insertQuery);
        $db->query();
    }

    function termsForContent($cid)
    {
        $dbo = JFactory::getDbo();
        $query = 'select t.id as tid,t.name from #__cedtag_term as t';
        $query .= ' left join #__cedtag_term_content as c  on c.tid=t.id';
        $query .= ' where c.cid=' . $dbo->quote($cid) . ' and t.published=\'1\' order by t.weight desc,t.name';
        $dbo->setQuery($query);
        return $dbo->loadColumn();
    }

    function getTermList()
    {
        $dbo = JFactory::getDbo();
        $mainframe = JFactory::getApplication();
        $search = $mainframe->getUserStateFromRequest('articleelement.search', 'search', '', 'string');
        $search = JString::strtolower($search);
        $where = null;
        if (!is_null($search)) {
            $where = " where name like'%" . $search . "%' ";
        }
        $query = "select count(*) as frequency from #__cedtag_term as t " . $where;
        $dbo->setQuery($query);
        $dbo->query();
        $total = $dbo->loadResult();

        $jinput = JFactory::getApplication()->input;
        $limitstart = $jinput->get('limitstart', 0, '', 'int');
        $params = JComponentHelper::getParams('com_cedtag');
        $limit = $params->get('tag_page_limit', 30);

        $query = 'select t.id,t.name,t.description,t.weight,t.created,t.hits,count(c.cid) as count, t.published from #__cedtag_term';
        $query .= ' as t  left join  #__cedtag_term_content as c  on c.tid=t.id ' . $where . ' group by(t.id) order by t.name';
        $dbo->setQuery($query, $limitstart, $limit);
        jimport('joomla.html.pagination');

        $this->page = new JPagination($total, $limitstart, $limit);
        $this->list = $dbo->loadObjectList();
        return $this;
    }

    function getTerm()
    {
        $dbo = JFactory::getDbo();
        $jinput = JFactory::getApplication()->input;
        $id = $jinput->get('cid', array(0), '', 'array');
        $query = 'select * from #__cedtag_term  where id=' . $id[0];
        $dbo->setQuery($query);
        return $dbo->loadObject();
    }

}
