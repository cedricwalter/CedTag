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
require_once JPATH_SITE . '/components/com_cedtag/models/tags.php';

//TODO see \components\com_content\models\category.php


class CedTagModelTag extends CedTagModelTags
{
    /**
     * tag data array
     *
     * @var array
     */
    var $_data = null;

    /**
     * Search total
     *
     * @var integer
     */
    var $_total = null;

    var $_termExist = false;
    var $_termArticles = false;

    var $_pagination = null;

    var $_defaultLimit = 10;
    var $_tagDescription = null;
    var $_ids = null;


    function __construct()
    {
        parent::__construct();

        $this->_defaultLimit = CedTagsHelper::param('page_limit', 10);
        $this->_loadData();
    }

    function getTermExist()
    {
        return $this->_termExist;
    }

    /**
     * Return the number of articles having the tag
     */
    function getTermArticles()
    {
        return $this->_termArticles;
    }

    function setTermArticles($termArticles)
    {
        $this->_termArticles = $termArticles;
    }


    function getData()
    {
        return $this->_data;
    }

    function getTagDescription()
    {
        return $this->_tagDescription;
    }

    private function _loadData()
    {
        $query = $this->_buildQuery();
        if ($this->getTermExist()) {
            $limitStart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
            $this->_data = $this->_getList($query, $limitStart, $this->_defaultLimit);
        }
    }

    public function getTotal()
    {
        return $this->_total;
    }

    public function getPagination()
    {
        // Lets load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            $limitStart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $limitStart, $this->_defaultLimit);
        }

        return $this->_pagination;
    }

    private function _buildQuery()
    {
        $tag = JRequest::getString('tag', null);

        //$tag=URLDecode($tag);
        $cedTagsHelper = new CedTagsHelper();

        $tag = $cedTagsHelper->unUrlTagname($tag);
        $tag = explode("?start=", $tag);
        $tag = CedTagsHelper::preHandle($tag[0]);

        JFactory::getApplication()->input->set('tag', $tag);
        $tag = trim($tag);
        $dbo = JFactory::getDBO();
        $tagObj = null;
        $ids = $this->_ids;
        if (!isset($this->_tagDescription)) {

            $tagObj = $this->getTabObjectByTagName($tag);

            if (isset($tagObj) && $tagObj->id) {
                $this->_termExist = true;
            } else {
                $this->_termExist = false;
                return '';
            }

            $this->_tagDescription = $tagObj->description;
            $this->incrementHitsForTagId($tagObj->id);

            $this->_total = $this->countNumberOfArticleForTagId($tagObj->id);

            $dbo = JFactory::getDBO();
            $tagQuery = "select c.cid from #__cedtag_term_content as c where c.tid=" . $dbo->quote($tagObj->id);
            $dbo->setQuery($tagQuery);
            $contentIds = $dbo->loadColumn();

            $ids = implode(',', $contentIds);
            $this->_ids = $ids;
        }

        $this->setTermArticles(!empty($ids));

        $nullDate = $dbo->getNullDate();
        jimport('joomla.utilities.date');
        $date = new JDate();
        $now = JDate::getInstance()->toSql($date);
        $order = CedTagsHelper::param('Order');
        $ShowArchiveArticles = CedTagsHelper::param('ShowArchiveArticles');
        $state = ' a.state = 1 ';
        if ($ShowArchiveArticles) {
            $state .= ' or a.state = -1';
        }

        $query = 'SELECT ' .
            ' a.id, a.title, a.created,u.name as author,a.created_by_alias as created_by_alias , COUNT(a.id) as total,a.introtext, a.fulltext, a.access, cc.title as section,' .
            ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,' .
            ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug,' .
            ' CHAR_LENGTH( a.`fulltext` ) AS readmore' .
            ' FROM #__content AS a' .
            ' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
            ' INNER JOIN #__users AS u ON u.id=a.created_by' .
            ' WHERE (a.id in (' . $ids . ') AND ' .
            '(' . $state . '))' .
            ' AND ( a.publish_up = ' . $dbo->Quote($nullDate) . ' OR a.publish_up <= ' . $dbo->Quote($now) . ' )' .
            ' AND ( a.publish_down = ' . $dbo->Quote($nullDate) . ' OR a.publish_down >= ' . $dbo->Quote($now) . ' )' .
            ' AND cc.published = 1' .
            ' GROUP BY(a.id)  ORDER BY  ' . $this->_buildOrderBy($order);

        // TODO
        //$this->setState('filter.language', $app->getLanguageFilter());


        return $query;

    }

    private function getTabObjectByTagName($tag)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->from('#__cedtag_term as t');
        $query->select('id AS id');
        $query->select('description AS description');
        $query->where('binary t.name=' . $dbo->quote($tag));
        $query->where("t.published='1'");
        $dbo->setQuery($query);
        $this->_tagDescription = $dbo->loadResult();

        $tagObj = $dbo->loadObject();
        return $tagObj;
    }

    private function _buildOrderBy($order)
    {
        switch ($order) {
            case 'date' :
                $orderby = 'a.created';
                break;

            case 'rdate' :
                $orderby = 'a.created DESC';
                break;

            case 'alpha' :
                $orderby = 'a.title';
                break;

            case 'ralpha' :
                $orderby = 'a.title DESC';
                break;

            case 'hits' :
                $orderby = 'a.hits DESC';
                break;

            case 'rhits' :
                $orderby = 'a.hits';
                break;

            case 'order' :
                $orderby = 'a.ordering';
                break;

            case 'author' :
                $orderby = 'a.created_by_alias, u.name';
                break;

            case 'rauthor' :
                $orderby = 'a.created_by_alias DESC, u.name DESC';
                break;

            case 'front' :
                $orderby = 'f.ordering';
                break;

            default :
                $orderby = 'a.ordering';
                break;

        }
        return $orderby;
    }

}
