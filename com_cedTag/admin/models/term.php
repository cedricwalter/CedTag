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

require_once JPATH_SITE . '/components/com_cedtag/helper/helper.php';
require_once JPATH_SITE . '/administrator/components/com_cedtag/helpers/wikipedia.php';

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
     * @return string
     */
    public function autofillDescriptions()
    {
        $dbo = JFactory::getDbo();

        //Get all Tags
        $query = $dbo->getQuery(true);
        $query->select('t.id as tid');
        $query->select('t.name as name');
        $query->select('t.description as description');
        $query->from('#__cedtag_term as t');
        $query->order('tid ASC');
        $dbo->setQuery($query);
        $tags = $dbo->loadObjectList();

        //
        $wikipedia = new wikipedia();
        $wikipediaAutocompleteStrategy = CedTagsHelper::param('wikipediaAutocompleteStrategy', '0');

        //some variables for statistics purposes
        $imported = 0;
        $scanned = 0;
        $termsNotFoundInWikipedia = array();

        foreach ($tags as $tag) {
            $updateTerm = (strlen($tag->description) == 0) && ($wikipediaAutocompleteStrategy == 0) ||
                ($wikipediaAutocompleteStrategy == 1);

            if ($updateTerm) {
                $descriptions = $wikipedia->wikidefinition($tag->name);
                $definitionfoundInWikipedia = is_array($descriptions);

                if ($definitionfoundInWikipedia) {
                    //TODO offer a template manager
                    $descriptionText = "<h1>$descriptions[0]</h1>
                    <p>$descriptions[1] <a href='" . $descriptions[2] . "'>[$descriptions[2]]</a></p>";

                    $query->clear();
                    $query->update('#__cedtag_term');
                    $query->set('description=' . $dbo->quote($descriptionText));
                    $query->where('id=' . $dbo->quote($tag->tid));
                    $dbo->setQuery($query);
                    $dbo->query();
                    $imported++;
                } else {
                    $termsNotFoundInWikipedia[] = $tag->name;
                }
            }
            $scanned++;
        }

        $message = "";
        if ($imported != 0) {
            $message = JText::_('Went through ') . $scanned . JText::_(' term(s) and did imported successfully ') . $imported . JText::_(' term(s) description from ') . $wikipedia->wikipediaServer;
        } else {
            $message = JText::_('Went through ') . $scanned . JText::_(' term(s) and did not import any new descriptions from ') . $wikipedia->wikipediaServer . JText::_(' as you have decided to not overwrite existing descriptions.');
        }

        if (sizeof($termsNotFoundInWikipedia) > 0) {
            $message .= JText::_(' Also CedTag did not found any definitions for ') . implode(",", $termsNotFoundInWikipedia);
        }
        return $message;
    }


    public
    function remove($termId)
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

    public
    function update($id, $name, $description = null, $weight = 0)
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

    public
    function store($name, $description = null, $weight = 0)
    {
        $dbo = JFactory::getDbo();

        $cedTagsHelper = new CedTagsHelper();
        $name = $cedTagsHelper->isValidName($name);
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

    public
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

    public
    function deleteContentTerms($id)
    {
        $dbo = JFactory::getDbo();

        $query = $dbo->getQuery(true);
        $query->delete('#__cedtag_term_content');
        $query->where('cid=' . $dbo->quote($id));
        $dbo->setQuery($query);

        $dbo->query();
    }

    public
    function insertContentTerms($cid, $tids)
    {
        foreach ($tids as $tid) {
            $this->insertContentterm($tid, $cid);
        }
    }

    public
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

    public
    function termsForContent($id)
    {
        $dbo = JFactory::getDbo();

        $query = $dbo->getQuery(true);

        $query->select('t.id as tid');
        $query->select('t.name as name');
        $query->from('#__cedtag_term as t');
        $query->leftJoin('#__cedtag_term_content as c  on c.tid=t.id');
        $query->where('c.cid=' . $dbo->quote($id));
        $query->where('t.published=\'1\'');
        $query->order('t.weight desc,t.name');

        $dbo->setQuery($query);

        return $dbo->loadColumn();
    }

//TODO Unused
    public
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


//TODO Unused
    public
    function getTerm()
    {
        $dbo = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $id = $input->get('cid', array(0), '', 'array');

        $query = 'select * from #__cedtag_term  where id=' . $id[0];


        $dbo->setQuery($query);
        return $dbo->loadObject();
    }

}
