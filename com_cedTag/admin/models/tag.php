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

/**
 * Methods supporting a list of article records.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 */
class CedTagModelTag extends JModelList
{
    var $_defaultLimit = 10;

    /**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
        $this->_defaultLimit = CedTagsHelper::param('page_limit', 10);

        if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'featured', 'a.featured',
				'language', 'a.language',
				'hits', 'a.hits',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.'.$layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$authorId = $app->getUserStateFromRequest($this->context.'.filter.author_id', 'filter_author_id');
		$this->setState('filter.author_id', $authorId);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		$level = $this->getUserStateFromRequest($this->context.'.filter.level', 'filter_level', 0, 'int');
		$this->setState('filter.level', $level);


		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// List state information.
		parent::populateState('a.title', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.category_id');
		$id	.= ':'.$this->getState('filter.author_id');
		$id	.= ':'.$this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();

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
		$query->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = a.language');

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
			$query->where('a.access = ' . (int) $access);
		}

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
		    $groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$groups.')');
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '') {
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
			$baselevel = (int) $cat_tbl->level;
			$query->where('c.lft >= '.(int) $lft);
			$query->where('c.rgt <= '.(int) $rgt);
		}
		elseif (is_array($categoryId)) {
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			$query->where('a.catid IN ('.$categoryId.')');
		}

		// Filter on the level.
		if ($level = $this->getState('filter.level')) {
			$query->where('c.level <= '.((int) $level + (int) $baselevel - 1));
		}

		// Filter by author
		$authorId = $this->getState('filter.author_id');
		if (is_numeric($authorId)) {
			$type = $this->getState('filter.author_id.include', true) ? '= ' : '<>';
			$query->where('a.created_by '.$type.(int) $authorId);
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			elseif (stripos($search, 'author:') === 0) {
				$search = $db->Quote('%'.$db->escape(substr($search, 7), true).'%');
				$query->where('(ua.name LIKE '.$search.' OR ua.username LIKE '.$search.')');
			}
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = '.$db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.title');
		$orderDirn	= $this->state->get('list.direction', 'asc');
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
			$orderCol = 'c.title '.$orderDirn.', a.ordering';
		}
		//sqlsrv change
		if($orderCol == 'language')
			$orderCol = 'l.title';
		if($orderCol == 'access_level')
			$orderCol = 'ag.title';
		$query->order($db->escape($orderCol.' '.$orderDirn));

		// echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	/**
	 * Build a list of authors
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	public function getAuthors() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Construct the query
		$query->select('u.id AS value, u.name AS text');
		$query->from('#__users AS u');
		$query->join('INNER', '#__content AS c ON c.created_by = u.id');
		$query->group('u.id, u.name');
		$query->order('u.name');

		// Setup the query
		$db->setQuery($query->__toString());

		// Return the result
		return $db->loadObjectList();
	}

	/**
	 * Method to get a list of articles.
	 * Overridden to add a check for access levels.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 * @since	1.6.1
	 */
	public function getItems()
	{
		$items	= parent::getItems();
		$app	= JFactory::getApplication();
		if ($app->isSite()) {
			$user	= JFactory::getUser();
			$groups	= $user->getAuthorisedViewLevels();

			for ($x = 0, $count = count($items); $x < $count; $x++) {
				//Check the access level. Remove articles the user shouldn't see
				if (!in_array($items[$x]->access, $groups)) {
					unset($items[$x]);
				}
			}
		}
		return $items;
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
            $dbo = JFactory::getDbo();
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
                $query .= 'left join #__cedtag_term as t on t.id=tc.tid where tc.cid=' . $dbo->quote($cid). ' and t.published=\'1\';';
                $dbo->setQuery($query);

                $tagsInArray = $dbo->loadColumn();
                if (isset($tagsInArray) && !empty($tagsInArray)) {
                    return implode(',', $tagsInArray);
                }
            }

            return '';
        }

        function batchUpdate($arrayTags)
        {
            $dbo = JFactory::getDbo();
            if (count($arrayTags)) {

                foreach ($arrayTags as $cid => $tags) {
                    $deleteTags = 'delete from #__cedtag_term_content where cid=' . $dbo->quote($cid);
                    $dbo->setQuery($deleteTags);
                    $dbo->query();

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

            $dbo = JFactory::getDbo();
            //		$name=JoomlaTagsHelper::preHandle($name);
            //		if(empty($name)){
            //			return 0;
            //		}
            $name = CedTagsHelper::isValidName($name);
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
                $date =& JFactory::getDate();
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
            $selectQuery = 'select * from  #__cedtag_term_content where tid=' . $dbo->quote($tid) . ' and cid=' . $dbo->quote($cid).';';
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
            $query = 'select count(*) as ct from #__cedtag_term_content where cid=' . $dbo->quote($cid).';';
            $dbo->setQuery($query);
            return $dbo->loadResult();
        }
}