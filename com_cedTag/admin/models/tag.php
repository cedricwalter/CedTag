<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_SITE . '/helper/helper.php';

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