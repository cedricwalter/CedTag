<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once JPATH_SITE . '/components/com_cedtag/helpers/helper.php';
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
        //Has to be a string
        $input = JFactory::getApplication()->input;
        $tag = $input->get('tag', null, 'string');

        //Tag is in front, this should remove ?start= or ?limitstart=
        $tags = explode('?', $tag);
        $tag = $tags[0];
        $tag = URLDecode($tag);

        //filter the tag
        $cedTagsHelper = new CedTagsHelper();
        $tag = $cedTagsHelper->unUrlTagname($tag);
        $tag = CedTagsHelper::preHandle($tag);

        JFactory::getApplication()->input->set('tag', $tag);
        $tag = trim($tag);
        $tagObj = null;
        $ids = $this->_ids;
        if (!isset($this->_tagDescription)) {

            $tagObj = $this->getTagByTagName($tag);

            if (isset($tagObj) && $tagObj->id) {
                $this->_termExist = true;
            } else {
                $this->_termExist = false;
                return '';
            }

            $this->_tagDescription = $tagObj->description;
            $this->incrementHitsForTagId($tagObj->id);

            $this->_total = $this->countNumberOfArticleForTagId($tagObj->id);

            $contentIds = $this->getArticlesCidForTag($tagObj->id);

            $ids = implode(',', $contentIds);
            $this->_ids = $ids;
        }

        $this->setTermArticles(!empty($ids));

        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select('a.id');
        $query->select('a.title');
        $query->select('a.created');
        $query->select('a.alias');
        $query->select('a.modified');
        $query->select('a.images');
        $query->select('a.created_by as contactid');
        $query->select('u.name as author');
        $query->select('a.created_by_alias as created_by_alias');
        $query->select('COUNT(a.id) as total');
        $query->select('a.introtext');
        $query->select('a.fulltext');
        $query->select('a.access');
        $query->select('a.state');
        $query->select('a.publish_up');
        $query->select('a.hits');
        $query->select('a.parentid as parent_id');
        $query->select('a.catid as catid');
        $query->select('cc.title as category_title');
        $query->select('cc.title as section');
        $query->select('cc.alias as parent_alias');

        $query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
        $query->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
        $query->select('CHAR_LENGTH( a.`fulltext` ) AS readmore');

        $query->from('#__content AS a');

        $query->innerJoin('#__categories AS cc ON cc.id = a.catid');
        $query->innerJoin('#__users AS u ON u.id=a.created_by');

        $query->where('a.id in ('.$ids.')');
        $query->where(sprintf('(%s)', implode(' AND ', $this->getAccessWhere(false))));

        $query->where('cc.published = '.$dbo->quote(1));
        $query->group('(a.id)');
        $query->order($this->_buildOrderBy());

        // TODO
        //$this->setState('filter.language', $app->getLanguageFilter());

        return $query;

    }

    private function getAccessWhere($showUnpublished)
    {
        $user = &JFactory::getUser();
        $dbo = JFactory::getDBO();
        $access_where[] = 'a.access IN(' . implode(',', $user->getAuthorisedViewLevels()) . ')';
        if (!$showUnpublished) {
            // Show both the published and unpublished articles
            if ((!$user->authorise('core.edit.state', 'com_content')) && (!$user->authorise('core.edit', 'com_content'))) {

                $ShowArchiveArticles = CedTagsHelper::param('ShowArchiveArticles');
                if ($ShowArchiveArticles) {
                    $access_where[] = '(a.state = 1 or a.state = 2 or a.state = -1)';
                } else {
                    $access_where[] = '(a.state = 1 or a.state = 2)';
                }

                // Hide any articles that are not in the published date range
                $now = &JFactory::getDate()->toSql();
                $nullDate = $dbo->getNullDate();
                $access_where[] = '(a.publish_up = ' . $dbo->Quote($nullDate) . ' OR a.publish_up <= ' . $dbo->Quote($now) . ')';
                $access_where[] = '(a.publish_down = ' . $dbo->Quote($nullDate) . ' OR a.publish_down >= ' . $dbo->Quote($now) . ')';
            }

        }
        $access_where[] = '(a.state != -2)'; // Never show trashed
        return $access_where;
    }

    private function _buildOrderBy()
    {
        $order = CedTagsHelper::param('Order');
        switch ($order) {
            case 'date' :
                $orderBy = 'a.created';
                break;

            case 'rdate' :
                $orderBy = 'a.created DESC';
                break;

            case 'alpha' :
                $orderBy = 'a.title';
                break;

            case 'ralpha' :
                $orderBy = 'a.title DESC';
                break;

            case 'hits' :
                $orderBy = 'a.hits DESC';
                break;

            case 'rhits' :
                $orderBy = 'a.hits';
                break;

            case 'order' :
                $orderBy = 'a.ordering';
                break;

            case 'author' :
                $orderBy = 'a.created_by_alias, u.name';
                break;

            case 'rauthor' :
                $orderBy = 'a.created_by_alias DESC, u.name DESC';
                break;

            case 'front' :
                $orderBy = 'f.ordering';
                break;

            default :
                $orderBy = 'a.ordering';
                break;

        }
        return $orderBy;
    }

}
