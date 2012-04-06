<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @version $Id: com_tag.php 599 2010-06-06 23:26:33Z you $
 * @author waltercedric.com, Joomlatags.org
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once JPATH_COMPONENT_SITE . DS . 'helper' . DS . 'helper.php';

class TagModelAllTags extends JModel
{
    function getAllTags()
    {
        $order = JoomlaTagsHelper::param('tagOrder');
        $orderby = $this->_buildOrderBy($order);
        $query = 'select count(*) as ct,name from #__tag_term_content as tc inner join #__tag_term as t on t.id=tc.tid  group by(tid) order by ' . $orderby;
        return $this->_getList($query);
    }

    function _buildOrderBy($order)
    {
        $orderBy = 'RAND()';
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
