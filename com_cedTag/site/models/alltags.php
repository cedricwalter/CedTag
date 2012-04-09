<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once JPATH_COMPONENT_SITE . '/helper/helper.php';

class CedTagModelAllTags extends JModel
{
    function getAllTags()
    {
        $order = CedTagsHelper::param('tagOrder');
        $orderby = $this->_buildOrderBy($order);
        $query = 'select count(*) as ct,name from #__cedtag_term_content as tc inner join #__cedtag_term as t on t.id=tc.tid  group by(tid) order by ' . $orderby;
        return $this->_getList($query);
    }

    function _buildOrderBy($order)
    {
        switch ($order)
        {
            case 'random':
                $orderBy = 'RAND()';
                break;
            case 'date' :
                $orderBy = 't.created';
                break;

            case 'rdate' :
                $orderBy = 't.created DESC';
                break;

            case 'alpha' :
                $orderBy = 't.name';
                break;

            case 'ralpha' :
                $orderBy = 't.name DESC';
                break;

            case 'hits' :
                $orderBy = 't.hits DESC';
                break;

            case 'rhits' :
                $orderBy = 't.hits';
                break;

            default :
                $orderBy = 'RAND()';
                break;

        }
        return $orderBy;
    }
}
