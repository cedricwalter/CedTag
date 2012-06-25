<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_cedtag/helper/helper.php';

jimport('joomla.application.component.modellist');

//reuse joomla model ContentModelArticles
require_once JPATH_SITE . '/administrator/components/com_content/models/articles.php';

/**
 * Methods supporting a list of article records.
 *
 * reuse joomla model ContentModelArticles
 *
 * @package        Joomla.Administrator
 * @subpackage    com_content
 */
class CedTagModelTag extends ContentModelArticles
{
    var $_defaultLimit = 10;

    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null)
    {
        // Adjust the context to support modal layouts.
        if ($layout = JRequest::getVar('layout')) {
            $this->context .= '.' . $layout;
        }

        $search = $this->getUserStateFromRequest($this->context . '.tag.filter.search', 'tag_filter_search');
        $this->setState('tag.filter.search', $search);

        // List state information.
        parent::populateState('a.title', 'asc');
    }


    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('tag.filter.search');
        return parent::getStoreId($id);
    }


    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $user = JFactory::getUser();

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.title, a.alias, a.checked_out, a.checked_out_time, a.catid' .
                    ', a.state, a.access, a.created, a.created_by, a.ordering, a.featured, a.language, a.hits' .
                    ', a.publish_up, a.publish_down'
            )
        );
        $query->from('#__content AS a');

        // Join over the language
        $query->select('l.title AS language_title');
        $query->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the asset groups.
        $query->select('ag.title AS access_level');
        $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

        // Join over the categories.
        $query->select('c.title AS category_title');
        $query->join('LEFT', '#__categories AS c ON c.id = a.catid');

        // Join over the users for the author.
        $query->select('ua.name AS author_name');
        $query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');

        // Filter by access level.
        if ($access = $this->getState('filter.access')) {
            $query->where('a.access = ' . (int)$access);
        }

        // Implement View Level Access
        if (!$user->authorise('core.admin')) {
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $query->where('a.access IN (' . $groups . ')');
        }

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.state = ' . (int)$published);
        } elseif ($published === '') {
            $query->where('(a.state = 0 OR a.state = 1)');
        }

        // Filter by a single or group of categories.
        $baselevel = 1;
        $categoryId = $this->getState('filter.category_id');
        if (is_numeric($categoryId)) {
            $cat_tbl = JTable::getInstance('Category', 'JTable');
            $cat_tbl->load($categoryId);
            $rgt = $cat_tbl->rgt;
            $lft = $cat_tbl->lft;
            $baselevel = (int)$cat_tbl->level;
            $query->where('c.lft >= ' . (int)$lft);
            $query->where('c.rgt <= ' . (int)$rgt);
        } elseif (is_array($categoryId)) {
            JArrayHelper::toInteger($categoryId);
            $categoryId = implode(',', $categoryId);
            $query->where('a.catid IN (' . $categoryId . ')');
        }

        // Filter on the level.
        if ($level = $this->getState('filter.level')) {
            $query->where('c.level <= ' . ((int)$level + (int)$baselevel - 1));
        }

        // Filter by author
        $authorId = $this->getState('filter.author_id');
        if (is_numeric($authorId)) {
            $type = $this->getState('filter.author_id.include', true) ? '= ' : '<>';
            $query->where('a.created_by ' . $type . (int)$authorId);
        }

        // Filter by search in title.
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } elseif (stripos($search, 'author:') === 0) {
                $search = $db->Quote('%' . $db->escape(substr($search, 7), true) . '%');
                $query->where('(ua.name LIKE ' . $search . ' OR ua.username LIKE ' . $search . ')');
            }
            else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('(a.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
            }
        }

        // Filter by tag search.
        $tagSearch = $this->getState('tag.filter.search');
        if (!empty($tagSearch)) {
            $tag = substr($tagSearch, 3);
            $tags = explode(",", $tag);


            $query->where("a.id in (SELECT cid FROM #__cedtag_term_content where tid in (SELECT id FROM #__cedtag_term where name like '%".$tag."%'))");
        }


        // Filter on the language.
        if ($language = $this->getState('filter.language')) {
            $query->where('a.language = ' . $db->quote($language));
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering', 'a.title');
        $orderDirn = $this->state->get('list.direction', 'asc');
        if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
            $orderCol = 'c.title ' . $orderDirn . ', a.ordering';
        }
        //sqlsrv change
        if ($orderCol == 'language')
            $orderCol = 'l.title';
        if ($orderCol == 'access_level')
            $orderCol = 'ag.title';
        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        // echo nl2br(str_replace('#__','jos_',$query));

        $sql = $query->dump();

        return $query;
    }


    function clearAll()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__cedtag_term_content');
        $db->setQuery($query);
        return $db->query();
    }

    function getTagList()
    {
        $dbo = JFactory::getDbo();
        $mainframe = JFactory::getApplication();
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

        $totalQuery = "select count(*) as frequency from #__content where 1=1" . $where;

        $dbo->setQuery($totalQuery);
        $dbo->query();
        $total = $dbo->loadResult();

        $limitstart = JFactory::getApplication()->input->get('limitstart', 0, '', 'int');
        $params = JComponentHelper::getParams('com_cedtag');
        $defaultLimit = $params->get('tag_page_limit', 30);
        $contentQuery = 'select id from #__content as c where 1=1' . $where;

        $dbo->setQuery($contentQuery, $limitstart, $defaultLimit);

        jimport('joomla.html.pagination');
        $result = null;

        $this->_total = $total;
        //pagination = new JPagination($total, $limitstart, $limit);


        $contentIdsArray = $dbo->loadColumn();

        $contentIds = implode(',', $contentIdsArray);

        $query = 'select c.id as cid,cc.title as category, c.title,t.name from ';
        $query .= ' #__content as c left join #__cedtag_term_content as tc on c.id=tc.cid';
        $query .= ' left join #__categories as cc on c.catid=cc.id';
        $query .= ' left join #__cedtag_term as t on tc.tid=t.id where c.id in(' . $contentIds . ') ';
        $dbo->setQuery($query);
        $result->list = $dbo->loadObjectList();

        return $result;
    }

    function getTagsForArticle()
    {
        $dbo = JFactory::getDbo();
        $cid = JFactory::getApplication()->input->get('article_id', '', 'int');
        $cid = strval(intval($cid));
        if ($cid < 0) {
            $cid = 0;
        }

        if (isset($cid)) {
            $query = 'select t.name from #__cedtag_term_content as tc';
            $query .= 'left join #__cedtag_term as t on t.id=tc.tid where tc.cid=' . $dbo->quote($cid) . ' and t.published=\'1\';';
            $dbo->setQuery($query);

            $tagsInArray = $dbo->loadColumn();
            if (isset($tagsInArray) && !empty($tagsInArray)) {
                return implode(',', $tagsInArray);
            }
        }

        /*
                $cid = JFactory::getApplication()->input->get('article_id', '', 'int');
                $cid = strval(intval($cid));
                if ($cid < 0) {
                    $cid = 0;
                }

                if (isset($cid)) {
                    $dbo = JFactory::getDBO();
                    $query = $dbo->getQuery(true);
                    $query->select('tagterm.name as name');
                    $query->leftJoin('#__cedtag_term_content as tagtermcontent on tagtermcontent.tid=tagterm.id');

                    $query->from('#__cedtag_term as tagterm');

                    $query->where('tagtermcontent.cid = ' . $dbo->quote($cid));
                    $query->where('tagterm.published=1');

                    $query->group('tid');
                    $query->order('tagterm.name');

                    $dbo->setQuery($query);
                    $terms = $dbo->loadObjectList();
                    return $terms;
                }
        */

        return '';
    }

    function batchUpdate($arrayTags, $clear = true)
    {
        $dbo = JFactory::getDbo();
        if (count($arrayTags)) {

            foreach ($arrayTags as $cid => $tags) {
                if ($clear) {
                    $query = $dbo->getQuery(true);
                    $query->delete('#__cedtag_term_content');
                    $query->where('cid=' . $dbo->quote($cid));
                    $dbo->setQuery($query);
                    $dbo->query();
                }

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

    public function deleteTag($id, $tag)
    {
        $dbo = JFactory::getDbo();
        $query = "delete from #__cedtag_term_content  where tid = (SELECT term.id FROM #__cedtag_term as term where term.name = " . $dbo->quote($tag) . ") and cid = " . $dbo->quote($id) . ";";
        $dbo->setQuery($query);
        $dbo->query();
    }

    function storeTerm($name, $description = NULL, $weight = 0)
    {
        //TODO not object oriented!

        $dbo = JFactory::getDbo();
        //		$name=JoomlaTagsHelper::preHandle($name);
        //		if(empty($name)){
        //			return 0;
        //		}
        $cedTagsHelper = new CedTagsHelper();
        $name = $cedTagsHelper->isValidName($name);
        if (!$name) {
            return false;
        }
        $query = "SELECT * FROM #__cedtag_term where binary name=" . $dbo->quote($name) . "";
        $dbo->setQuery($query, 0, 1);
        $tagInDB = $dbo->loadObject();
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
                $dbo->setQuery($updateQuery);
                $dbo->query();
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
            $date = JFactory::getDate();
            $now = JDate::getInstance()->toSql($date);
            $insertQuery .= ',created) ';
            $valuePart .= ',' . $dbo->Quote($now) . ')';
            $dbo->setQuery($insertQuery . $valuePart);
            $dbo->query();
            return $dbo->insertid();
        }
    }

    function insertContentTerm($tid, $cid)
    {
        $dbo = JFactory::getDbo();
        $insertQuery = 'insert into #__cedtag_term_content (tid,cid) values(' . $tid . ',' . $cid . ')';
        $dbo->setQuery($insertQuery);
        $dbo->query();
    }

    function storeContentTerm($tid, $cid)
    {
        $dbo = JFactory::getDbo();
        $selectQuery = 'select * from  #__cedtag_term_content where tid=' . $dbo->quote($tid) . ' and cid=' . $dbo->quote($cid) . ';';
        $dbo->setQuery($selectQuery);
        $dbo->query();
        $numRows = $dbo->getNumRows();
        if ($numRows <= 0) {
            //Not exist, insert
            $this->insertContentTerm($tid, $cid);
        }
    }

    function isContentHasTags($cid)
    {
        $dbo = JFactory::getDbo();
        $query = 'select count(*) as frequency from #__cedtag_term_content where cid=' . $dbo->quote($cid) . ';';
        $dbo->setQuery($query);
        return $dbo->loadResult();
    }

    public function suggestJson($partialTag)
    {
        $term = JRequest::getVar('term', '', 'get', 'cmd');
        if (isset($term)) {
            $db = JFactory::getDBO();
            $query = 'SELECT name FROM #__cedtag_term WHERE name like ' . $db->Quote($partialTag . "%");
            $db->setQuery($query);
            $result = $db->loadColumn();
            echo (json_encode($result));
        }
        return true;
    }


}