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
        $dbo = JFactory::getDbo();

        $query = $dbo->getQuery(true);
        $query->delete('#__cedtag_term');
        $query->where($where);
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
        $query = $dbo->getQuery(true);

        $query->update('#__cedtag_term');
        $query->set('name=' . $dbo->quote($name));
        $query->set('weight=' . $dbo->quote($weight));
        $query->set('description=' . $dbo->quote(description));
        $query->where('id=' . $dbo->quote($id));

        $dbo->setQuery($query);
        return $dbo->query();
    }

    function store($name, $description = NULL, $weight = 0)
    {
        $dbo = JFactory::getDbo();
        $name = CedTagsHelper::isValidName($name);
        if (!$name) {
            return false;
        }

        $query = $dbo->getQuery(true);

        $query->select('id as id');
        $query->from('#__cedtag_term');
        $query->where(' name=" . $dbo->quote($name)');
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

        $query = $dbo->getQuery(true);
        $query->delete('#__cedtag_term_content');
        $query->where('cid=' . $dbo->quote($cid));
        $dbo->setQuery($query);

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
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->insert('#__cedtag_term_content');
        $query->columns(array($dbo->quoteName('tid'), $dbo->quoteName('cid')));
        $query->values($tid . ',' . $cid);

        $dbo->setQuery($query);
        $dbo->query();
    }

    function termsForContent($cid)
    {
        $dbo = JFactory::getDbo();

        $query = $dbo->getQuery(true);

        $query->select('t.id as tid');
        $query->select('t.name as name');
        $query->from('#__cedtag_term as t');
        $query->leftJoin('#__cedtag_term_content as c  on c.tid=t.id');
        $query->where('c.cid=' . $dbo->quote($cid));
        $query->where('t.published=\'1\'');
        $query->order('t.weight desc,t.name');

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
