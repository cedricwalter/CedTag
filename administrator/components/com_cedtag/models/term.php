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

require_once JPATH_SITE . '/components/com_cedtag/helpers/helper.php';
require_once JPATH_SITE . '/administrator/components/com_cedtag/helpers/wikipedia.php';
require_once JPATH_SITE . '/administrator/components/com_cedtag/models/tag.php';


class CedTagModelTerm extends JModel
{

    var $page = null;
    var $list = null;
    var $authors = null;

    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * @param $id
     * @param $name
     * @param $description
     * @param $message
     * @param $redirectTo
     */
    public function autofillDescriptions($id, $name, $description, &$message, &$redirectTo)
    {
        $autocompleteStrategy = CedTagsHelper::param('wikipediaAutocompleteStrategy', '0');

        //Only supported source is Wikipedia
        $wikipedia = new CedTagWikipedia();

        //Update single tag
        if (isset($name) && strlen($name) > 0) {
            $tag = new stdClass();
            $tag->id = $id[0];
            $tag->name = $name;
            $tag->description = $description;

            $updateTerm = (strlen($tag->description) == 0) && ($autocompleteStrategy == 0) ||
                ($autocompleteStrategy == 1);

            $this->updateDescriptionForTag($wikipedia, $updateTerm, $tag);
            $redirectTo = "index.php?option=com_cedtag&controller=term&task=edit&cid[]=" . $id[0];
        } else {
            //Update all tags
            $tags = $this->getAllTags();
            foreach ($tags as $tag) {
                $updateTerm = (strlen($tag->description) == 0) && ($autocompleteStrategy == 0) ||
                    ($autocompleteStrategy == 1);

                $this->updateDescriptionForTag($wikipedia, $updateTerm, $tag);
            }
            $redirectTo = "index.php?controller=term&option=com_cedtag";
        }

        $message = JText::_('Using ') . $wikipedia->getWikipediaServer() . ".";
        $message .= JText::_(' Did Search for ') . $wikipedia->getRequested() . ' term(s).';
        $message .= JText::_(' Did found ') . $wikipedia->getFound() . JText::_(' term(s) description(s) ');

        if (sizeof($wikipedia->getNotFound()) > 0) {
            $message .= JText::_('Did not found ') . sizeof($wikipedia->getNotFound()) . JText::_(' term(s) description(s) ');
            foreach ($wikipedia->getNotFound() as $term) {
                //  $message .= "<a href='/index.php?option=com_cedtag&controller=term&task=edit&cid[]=".$term->tid.">$term->name</a> - ";
                $message .= $term->name;
            }
        }
    }

    /**
     * @return list of tags as array
     */
    public function getAllTags()
    {
        $dbo = JFactory::getDbo();

        $query = $dbo->getQuery(true);
        $query->select('t.id as id');
        $query->select('t.name as name');
        $query->select('t.description as description');
        $query->from('#__cedtag_term as t');
        $query->order('id ASC');

        $dbo->setQuery($query);

        $tags = $dbo->loadObjectList();
        return $tags;
    }

    /**
     * @param $source
     * @param $updateTerm
     * @param $tag
     */
    public function updateDescriptionForTag($source, $updateTerm, $tag)
    {
        if ($updateTerm) {
            $descriptions = $source->getDefinition($tag->name);
            $definitionFound = is_array($descriptions);

            if ($definitionFound) {
                //TODO offer a template manager
                $descriptionText = "<h1>$descriptions[0]</h1>
                    <p>$descriptions[1] <a href='" . $descriptions[2] . "'>[$descriptions[2]]</a></p>";

                $dbo = JFactory::getDbo();
                $updateQuery = $dbo->getQuery(true);
                $updateQuery->update('#__cedtag_term');
                $updateQuery->set('description=' . $dbo->quote($descriptionText));
                $updateQuery->where('id=' . $dbo->quote($tag->id));
                $dbo->setQuery($updateQuery);
                $dbo->query();
            }
        }
    }


    /**
     * remove a term by term id
     * @param $termId
     * @return bool|mixed
     */
    public function remove($termId)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->delete('#__cedtag_term');

        if (count($termId) > 1) {
            $query->where('id in(' . implode(',', $termId) . ')');
        } else if (count($termId) == 1) {
            $query->where('id=' . $termId[0]);
        } else {
            return false;
        }

        $dbo->setQuery($query);

        return $dbo->query();
    }

    /**
     * Delete all terms and all content association
     * @return bool|mixed
     */
    public function clearall()
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->delete('#__cedtag_term');
        $dbo->setQuery($query);
        $res = $dbo->query();

        $query->clear();
        $query->delete('#__cedtag_term_content');
        $dbo->setQuery($query);
        $res = $res & $dbo->query();

        return $res;
    }


    /**
     * @param $id id of term
     * @param $name
     * @param null $description
     * @param int $weight
     * @return bool|mixed
     */
    public function update($id, $name, $description = null, $weight = 0)
    {
        $dbo = JFactory::getDbo();

        $cedTagsHelper = new CedTagsHelper();
        $name = $cedTagsHelper->isValidName($name);
        if (!$name) {
            return false;
        }
        $query = $dbo->getQuery(true);

        $query->update('#__cedtag_term');
        $query->set('name=' . $dbo->quote($name));
        $query->set('weight=' . $dbo->quote($weight));
        $query->set('description=' . $dbo->quote($description));
        $query->where('id=' . $dbo->quote($id));

        $dbo->setQuery($query);
        return $dbo->query();
    }

    /**
     * @param $name
     * @param null $description
     * @param int $weight
     * @return bool|int
     */
    public function store($name, $description = null, $weight = 0)
    {
        $dbo = JFactory::getDbo();

        $cedTagsHelper = new CedTagsHelper();
        $name = $cedTagsHelper->isValidName($name);
        if (!$name) {
            return false;
        }

        $cedTagModelTag = new CedTagModelTag();
        $tagAlreadyExisting = $cedTagModelTag->getTagId($name);

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

    /**
     * @param $terms
     * @return bool|int
     */
    public function insertTerms($terms)
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

    /**
     * @param $id
     */
    public function deleteContentTerms($id)
    {
        $dbo = JFactory::getDbo();

        $query = $dbo->getQuery(true);
        $query->delete('#__cedtag_term_content');
        $query->where('cid=' . $dbo->quote($id));
        $dbo->setQuery($query);

        $dbo->query();
    }

    /**
     * @param $cid content id
     * @param $tIds tag id
     */
    public function insertContentTerms($cid, $tIds)
    {
        foreach ($tIds as $tid) {
            $this->insertContentterm($tid, $cid);
        }
    }

    /**
     * @param $tid
     * @param $cid
     */
    public function insertContentterm($tid, $cid)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->insert('#__cedtag_term_content');
        $query->columns(array($dbo->quoteName('tid'), $dbo->quoteName('cid')));
        $query->values($tid . ',' . $cid);

        $dbo->setQuery($query);
        $dbo->query();
    }

    /**
     * @param $cid
     * @return mixed
     */
    public function termsForContent($cid)
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

    /**
     * @param null $ordering
     * @param null $direction
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // Adjust the context to support modal layouts.
        if ($layout = JRequest::getVar('layout')) {
            $this->context .= '.' . $layout;
        }

        //$search = $this->getUserStateFromRequest($this->context . 'filter.published', 'filter_published');
//        /$this->setState('tag.filter.search', $search);

        // List state information.
        parent::populateState('a.title', 'asc');
    }


    protected function getStoreId($id = '')
    {
        // Compile the store id.
        //$id .= ':' . $this->getState('filter.published');
        return parent::getStoreId($id);
    }

    public function getTermList()
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

        $limitStart = JFactory::getApplication()->input->get('limitstart', 0, '', 'int');
        $params = JComponentHelper::getParams('com_cedtag');
        $limit = $params->get('tag_page_limit', 30);

        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select('t.id');
        $query->select('t.name');
        $query->select('t.description');
        $query->select('t.weight');
        $query->select('t.created');
        $query->select('t.hits');
        $query->select('count(c.cid) as count');
        $query->select('t.published');

        $query->from('#__cedtag_term as t');
        $query->leftJoin('#__cedtag_term_content as c  on c.tid=t.id');

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('t.published = ' . (int)$published);
        } elseif ($published === '') {
            $query->where('(t.published = 0 OR t.published = 1)');
        }

        if (!is_null($search)) {
            $query->where("name like '%" . $search . "%' ");
        }
        $query->group('(t.id)');
        $query->order('t.name');

        $sql = $query->dump();
        $dbo->setQuery($query, $limitStart, $limit);
        $this->list = $dbo->loadObjectList();

        jimport('joomla.html.pagination');

        $this->page = new JPagination($total, $limitStart, $limit);
        return $this;
    }

    public function getTerm()
    {
        $id = JFactory::getApplication()->input->get('cid', array(0), '', 'array');

        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select('*');
        $query->from('#__cedtag_term');
        $query->where('id=' . $dbo->Quote($id[0]));

        $dbo->setQuery($query);

        return $dbo->loadObject();
    }

}
