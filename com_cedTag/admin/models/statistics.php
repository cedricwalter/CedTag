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
require_once JPATH_COMPONENT_SITE . DS . 'helper/helper.php';

class CedTagModelStatistics extends JModel
{

    function getStatistics()
    {

        $statistics = new stdClass();
        $query = "select count(*) as ct from #__cedtag_term where published = '1';";
        $dbo = JFactory::getDbo();
        $dbo->setQuery($query);
        $statistics->termPublished = $dbo->loadResult();

        $query = "select count(*) as ct from #__cedtag_term where published = '0';";
        $dbo = JFactory::getDbo();
        $dbo->setQuery($query);
        $statistics->termUnpublished = $dbo->loadResult();


        $query = "select count(*) as ct  from #__content where id not in (select cid from #__cedtag_term_content)";
        $dbo = JFactory::getDbo();
        $dbo->setQuery($query);
        $statistics->articlesWithoutTags = $dbo->loadResult();

        $query = "select count(*) as ct  from #__content where id in (select cid from #__cedtag_term_content)";
        $dbo = JFactory::getDbo();
        $dbo->setQuery($query);
        $statistics->articlesWithTags = $dbo->loadResult();

        return $statistics;
    }

}
