<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once JPATH_SITE . '/components/com_cedtag/helper/helper.php';

jimport('joomla.filesystem.file');

class CedTagModelTag extends JModel
{
    var $_pagination = null;
    var $_total = null;
    var $_defaultLimit = 10;

    function __construct()
    {
        parent::__construct();

        $this->_defaultLimit = CedTagsHelper::param('page_limit', 10);
        //$this->_loadData();
    }

    function clearAll()
    {
        $db = JFactory::getDbo();
        $query = 'delete from #__cedtag_term_content';
        $db->setQuery($query);
        return $db->query();
    }


    function getTagList()
    {
        $db = JFactory::getDbo();
        $mainframe =& JFactory::getApplication();
        $catid = $mainframe->getUserStateFromRequest('articleelement.catid', 'catid', 0, 'int');
        $search = $mainframe->getUserStateFromRequest('articleelement.search', 'search', '', 'string');
        $search = JString::strtolower($search);

        $where = '';
        if ($catid > 0) {
            $where .= ' and catid=' . $catid;
        }
        if (!empty($search)) {
            $where .= " and ( `title` like'%" . $search . "%'  or `fulltext` like'%" . $search . "%'  or  `introtext` like'%" . $search . "%')";
        }

        $totalQuery = "select count(*) as ct from #__content where 1=1" . $where;

        $db->setQuery($totalQuery);
        $db->query();
        $total = $db->loadResult();

        $limitstart = JFactory::getApplication()->input->get('limitstart', 0, '', 'int');
        $params = JComponentHelper::getParams('com_cedtag');
        $defaultLimit = $params->get('tag_page_limit', 30);
        $contentQuery = 'select id from #__content as c where 1=1' . $where;

        $db->setQuery($contentQuery, $limitstart, $defaultLimit);

        jimport('joomla.html.pagination');
        $result = null;

        $this->_total = $total;
        //pagination = new JPagination($total, $limitstart, $limit);


        $contentIdsArray = $db->loadColumn();

        $contentIds = implode(',', $contentIdsArray);

        $query = 'select c.id as cid,cc.title as category, c.title,t.name from ';
        $query .= ' #__content as c left join #__cedtag_term_content as tc on c.id=tc.cid';
        $query .= ' left join #__categories as cc on c.catid=cc.id';
        $query .= ' left join #__tag_term as t on tc.tid=t.id where c.id in(' . $contentIds . ') ';
        $db->setQuery($query);
        $result->list = $db->loadObjectList();

        return $result;
    }

    function getTagsForArticle()
    {
        $db = JFactory::getDbo();
        $cid = JFactory::getApplication()->input->get('article_id', '', 'int');
        $cid = strval(intval($cid));
        if ($cid < 0) {
            $cid = 0;
        }

        if (isset($cid)) {
            $query = 'select t.name from #__cedtag_term_content as tc';
            $query .= 'left join #__cedtag_term as t on t.id=tc.tid where tc.cid=' . $cid;
            $db->setQuery($query);

            $tagsInArray = $db->loadColumn();
            if (isset($tagsInArray) && !empty($tagsInArray)) {
                return implode(',', $tagsInArray);
            }
        }

        return '';
    }

    function batchUpdate($arrayTags)
    {
        $db = JFactory::getDbo();
        if (count($arrayTags)) {

            foreach ($arrayTags as $cid => $tags) {
                $deleteTags = 'delete from #__cedtag_term_content where cid=' . $cid;
                $db->setQuery($deleteTags);
                $db->query();

                if (isset($tags)) {
                    $tagsArray = explode(',', $tags);
                    //$tagsArray=array_unique($tagsArray);
                    if (count($tagsArray)) {
                        $insertedTids = array();
                        foreach ($tagsArray as $tag) {
                            $tid = $this->storeTerm($tag);
                            if ($tid && !in_array($tid, $insertedTids)) {
                                $this->insertContentTerm($tid, $cid);
                                $insertedTids[] = $tid;
                            }
                        }
                    }
                }
            }
        }
    }

    function storeTerm($name, $description = NULL, $weight = 0)
    {
        //TODO not object oriented!

        $db = JFactory::getDbo();
        //		$name=JoomlaTagsHelper::preHandle($name);
        //		if(empty($name)){
        //			return 0;
        //		}
        $name = CedTagsHelper::isValidName($name);
        if (!$name) {
            return false;
        }
        $query = "SELECT * FROM #__cedtag_term where binary name='" . $name . "'";
        $db->setQuery($query, 0, 1);
        $tagInDB = $db->loadObject();
        if (isset($tagInDB) & isset($tagInDB->id)) {
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
                $updateQuery .= ' where id=' . $tagInDB->id;
                $db->setQuery($updateQuery);
                $db->query();
            }
            return $tagInDB->id;
        } else {
            $insertQuery = 'insert into #__cedtag_term (name';
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

    function insertContentTerm($tid, $cid)
    {
        $db = JFactory::getDbo();
        $insertQuery = 'insert into #__cedtag_term_content (tid,cid) values(' . $tid . ',' . $cid . ')';
        $db->setQuery($insertQuery);
        $db->query();
    }

    function storeContentTerm($tid, $cid)
    {
        $db = JFactory::getDbo();
        $selectQuery = 'select * from  #__cedtag_term_content where tid=' . $tid . ' and cid=' . $cid;
        $db->setQuery($selectQuery);
        $db->query();
        $numRows = $db->getNumRows();
        if ($numRows <= 0) {
            //Not exist, insert
            $this->insertContentTerm($tid, $cid);
        }
    }

    function isContentHasTags($cid)
    {
        $db = JFactory::getDbo();
        $query = 'select count(*) as ct from #__cedtag_term_content where cid=' . $cid;
        $db->setQuery($query);
        return $db->loadResult();
    }

    function format()
    {
    }

    function getTotal()
    {
        return $this->_total;
    }

    public function getPagination()
    {
        // Lets load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $limitstart, $this->_defaultLimit);
        }

        return $this->_pagination;
    }
}