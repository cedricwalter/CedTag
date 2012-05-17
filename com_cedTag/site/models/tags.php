<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once JPATH_SITE . '/components/com_cedtag/helper/helper.php';

class CedTagModelTags extends JModel
{

    function __construct()
    {
        parent::__construct();
    }

    public function getModelTags($id)
    {
        $cedTagModelTags = new CedTagModelTags();
        $terms = $cedTagModelTags->getTagsForArticle($id);
        $showRelatedArticles = CedTagsHelper::param('RelatedArticlesByTags', 0);

        $tags = array();
        if (isset($terms) && !empty($terms)) {
            $singleTermsSuppress = CedTagsHelper::param('SuppresseSingleTerms');
            $displayHitsNumber = CedTagsHelper::param('HitsNumber');

            $termIds = array();

            $CedTagThemes = new CedTagThemes();
            $CedTagThemes->addCss();

            foreach ($terms as $term) {
                $ct = $cedTagModelTags->getTagFrequency($term);
                if ($showRelatedArticles || $singleTermsSuppress) {
                    if (@intval($ct) <= 1) {
                        if ($singleTermsSuppress) {
                            continue;
                        }
                    } else {
                        $termIds[] = $term->id;
                    }
                }
                $hrefTitle = $ct . ' items tagged with ' . $term->name;
                if ($displayHitsNumber) {
                    $hrefTitle .= ' | Hits:' . $term->hits;
                }

                $tag = new stdClass();
                $tag->link = JRoute::_('index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($term->name));
                $tag->title = $hrefTitle;
                $tag->tag = CedTagsHelper::ucwords($term->name);
                $tags[] = $tag;
            }
        }

        //Limit size of tags displayed
        array_splice($tags, intval(CedTagsHelper::param('MaxTagsNumber', 10)));

        return $tags;
    }

    public function getTagFrequency($term)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select('count(cid) as frequency');
        $query->from('#__cedtag_term_content');
        $query->where('tid=' . $dbo->quote($term->id));

        $dbo->setQuery($query);
        $ct = $dbo->loadResult();

        return $ct;
    }

    public function getTagsForArticle($id)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);
        $query->select('tagterm.id');
        $query->select('tagterm.name');
        $query->select('tagterm.hits');
        $query->leftJoin('#__cedtag_term_content as tagtermcontent on tagtermcontent.tid=tagterm.id');

        $query->from('#__cedtag_term as tagterm');

        $query->where('tagtermcontent . cid = ' . $dbo->quote($id));
        $query->where('tagterm.published=1');

        $query->group('tid');
        $query->order('tagterm.weight desc');
        $query->order('tagterm.name');

        $dbo->setQuery($query);
        $terms = $dbo->loadObjectList();
        return $terms;
    }

    public function countNumberOfArticleForTagId($tagId)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select('count(c.cid) as frequency');
        $query->from('#__cedtag_term_content as c');
        $query->where('tid=' . $dbo->quote($tagId));

        $dbo->setQuery($query);
        $dbo->query();
        $total = $dbo->loadResult();
        return $total;
    }

    public function incrementHitsForTagId($tagId)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->update('#__cedtag_term');
        $query->set('hits=hits+1');
        $query->where('id=' . $dbo->quote($tagId));

        $dbo->setQuery($query);
        $dbo->query();
    }

}