<?php
/**
 * @package Plugin cedSearchTags for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_content/router.php';
require_once JPATH_SITE . '/components/com_cedtag/helpers/helper.php';

class plgCedTagSearch extends JPlugin
{
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    /**
     * @return array An array of search areas
     */
    function onContentSearchAreas()
    {
        static $areas = array(
            'tags' => 'Tags'
        );
        return $areas;
    }

    /**
     * Tags Search method
     *
     * The sql must return the following fields that are
     * used in a common display routine: href, title, section, created, text,
     * browsernav
     * @param string Target search string
     * @param string mathcing option, exact|any|all
     * @param string ordering option, newest|oldest|popular|alpha|category
     * @param mixed An array if restricted to areas, null if search all
     */
    function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
    {
        $searchText = $text;
        if (is_array($areas)) {
            if (!array_intersect($areas, array_keys(plgSearchTagsAreas()))) {
                return array();
            }
        }

        // load plugin params info
        $plugin = JPluginHelper::getPlugin('search', 'cedtags');
        $pluginParams = new JParameter($plugin->params);

        $limit = $pluginParams->def('search_limit', 50);

        $text = trim($text);
        if ($text == '') {
            return array();
        }

        $rows = $this->searchForText($text, $limit);

        $count = count($rows);
        for ($i = 0; $i < $count; $i++) {
            $link = 'index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($rows[$i]->name);
            $rows[$i]->href = JRoute::_($link);
            $rows[$i]->section = JText::_('TAG');
        }

        $return = array();
        foreach ($rows AS $key => $tag) {
            if (searchHelper::checkNoHTML($tag, $searchText, array('name', 'title', 'text'))) {
                $return[] = $tag;
            }
        }

        return $return;
    }

    private function searchForText($text, $limit)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->from('#__cedtag_term as t');

        $query->select('name');
        $query->select('name as title');
        $query->select('description as text');

        $text = $db->Quote('%' . $db->escape($text, true) . '%', false);
        $query->where('t.name like' . $text);
        $query->where('t.published=\'1\'');
        $query->order('weight desc,name');

        $db->setQuery($query, 0, $limit);
        $rows = $db->loadObjectList();
        return $rows;
    }
}