<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once JPATH_SITE . '/components/com_cedtag/helpers/helper.php';

class CedTagModelTags extends JModel
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * @param $cid
     * @return array
     */
    public function getModelTags($cid)
    {
        $terms = $this->getTagsForArticle($cid);
        $showRelatedArticles = CedTagsHelper::param('RelatedArticlesByTags', 0);

        $tags = array();
        if (isset($terms) && !empty($terms)) {
            $singleTermsSuppress = CedTagsHelper::param('SuppresseSingleTerms', 1);

            $termIds = array();

            foreach ($terms as $term) {
                $frequency = $this->getTagFrequency($term);
                if ($showRelatedArticles || $singleTermsSuppress) {
                    if (@intval($frequency) <= 1) {
                        if ($singleTermsSuppress) {
                            continue;
                        }
                    } else {
                        $termIds[] = $term->id;
                    }
                }

                $date = $term->created;

                $tag = new stdClass();
                $tag->title = JText::sprintf('COM_CEDTAG_ITEMS_TITLE',
                    (string)$frequency,
                    (string)$term->name,
                    (string)$date,
                    (string)$term->hits
                );
                $tag->id = $term->id;
                $tag->link = JRoute::_('index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($term->name));
                $tag->tag = CedTagsHelper::ucwords($term->name);
                $tags[] = $tag;
            }
        }

        //Limit size of tags displayed
        array_splice($tags, intval(CedTagsHelper::param('MaxTagsNumber', 10)));

        return $tags;
    }


    public function getTagFrequency($ModelTags)
    {
        return $this->getTagFrequencyBy($ModelTags->id);
    }

    public function getTagFrequencyBy($tid)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select('count(cid) as frequency');
        $query->from('#__cedtag_term_content');
        $query->where('tid=' . $dbo->quote($tid));

        $dbo->setQuery($query);
        $ct = $dbo->loadResult();

        return $ct;
    }

    public function getTagsForArticle($cid)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select('tagterm.id');
        $query->select('tagterm.name');
        $query->select('tagterm.hits');
        $query->select('tagterm.created');

        $query->leftJoin(' #__cedtag_term_content as tagtermcontent on tagtermcontent.tid=tagterm.id ');

        $query->from(' #__cedtag_term as tagterm');

        $query->where('tagtermcontent . cid = ' . $dbo->quote($cid));
        $query->where('tagterm.published=1');

        $query->group('tid');
        $query->order('tagterm.weight desc');
        $query->order('tagterm.name');

        $dbo->setQuery($query);
        $tags = $dbo->loadObjectList();
        return $tags;
    }

    public function countNumberOfArticleForTagId($tid)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select('count(c.cid) as frequency');
        $query->from('#__cedtag_term_content as c');
        $query->where('tid=' . $dbo->quote($tid));

        $dbo->setQuery($query);
        $dbo->query();
        $total = $dbo->loadResult();
        return $total;
    }

    public function incrementHitsForTagId($termId)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->update('#__cedtag_term');
        $query->set('hits=hits+1');
        $query->where('id=' . $dbo->quote($termId));

        $dbo->setQuery($query);
        $dbo->query();
    }

    public function getArticlesCidForTag($tid)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->from('#__cedtag_term_content as c');
        $query->select('c.cid');
        $query->where('c.tid=' . $dbo->quote($tid));

        $dbo->setQuery($query);
        $cIds = $dbo->loadColumn();
        return $cIds;
    }

    public function getTagByTagName($tag)
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

}