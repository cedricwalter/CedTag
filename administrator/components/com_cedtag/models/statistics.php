<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once JPATH_SITE . '/components/com_cedtag/helpers/helper.php';

class CedTagModelStatistics extends JModel
{

    function __construct()
    {
        parent::__construct();
    }

    public function getStatistics()
    {
        $dbo = JFactory::getDBO();

        $statistics = new stdClass();

        $query = $dbo->getQuery(true);
        $query->select('count(*) as ct');
        $query->from('#__cedtag_term');
        $query->where("published='1'");
        $dbo->setQuery($query);
        $statistics->termPublished = $dbo->loadResult();

        $query = $dbo->getQuery(true);
        $query->select('count(*) as ct');
        $query->from('#__cedtag_term');
        $query->where("published='0'");
        $dbo->setQuery($query);
        $statistics->termUnpublished = $dbo->loadResult();

        $query = $dbo->getQuery(true);
        $query->select('count(*) as ct');
        $query->from('#__cedtag_term');
        $query->where("published='1'");
        $query->where("description is null");
        $dbo->setQuery($query);
        $statistics->termPublishedWithoutDescription = $dbo->loadResult();

        $query = $dbo->getQuery(true);
        $query->select('count(*) as ct');
        $query->from('#__cedtag_term');
        $query->where("published='1'");
        $query->where("description is not null");
        $dbo->setQuery($query);
        $statistics->termPublishedWithDescription = $dbo->loadResult();

        $query = $dbo->getQuery(true);
        $query->select('count(*) as ct');
        $query->from('#__cedtag_term');
        $query->where("id not in (select cid from #__cedtag_term_content)");
        $dbo->setQuery($query);
        $statistics->articlesWithoutTags = $dbo->loadResult();

        $query = $dbo->getQuery(true);
        $query->select('count(*) as ct');
        $query->from('#__cedtag_term');
        $query->where("id in (select cid from #__cedtag_term_content)");
        $dbo->setQuery($query);
        $statistics->articlesWithTags = $dbo->loadResult();

        return $statistics;
    }

}
