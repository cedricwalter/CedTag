<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @version $Id: com_tag.php 599 2010-06-06 23:26:33Z you $
 * @author waltercedric.com, Joomlatags.org
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.application.pathway');
jimport( 'joomla.application.input' );
require_once JPATH_SITE . '/components/com_tag/helper/helper.php';

class TagViewTag extends JView
{
    function display($tpl = null)
    {
        $layout = JFactory::getApplication()->input->get('layout', null, 'string');
        switch ($layout) {
            case 'add':
                $this->add($tpl);
                break;
            case 'warning':
                $this->warning($tpl);
                break;
            default:
                $this->defaultTpl($tpl);
        }

    }

    function defaultTpl($tpl = null)
    {
        $tag = JFactory::getApplication()->input->get('tag', null, 'string');
        //$tag = JRequest::getString('tag', null);
        //$tag=URLDecode($tag);
        $tag = JoomlaTagsHelper::unUrlTagname($tag);

        JFactory::getApplication()->input->set('tag', $tag);

        $results = & $this->get('Data');
        $total = & $this->get('Total');
        $pagination = & $this->get('Pagination');
        $tagDescription =& $this->get('TagDescription');
        $isTermExist = $this->get('TermExist');
        if (!$isTermExist) {
            //$layout=JFactory::getApplication()->input->set("layout","warning");
            //JFactory::getApplication()->input->set('tagsWarning','REQUEST_TAG_NOT_EXIST_WARNING');
            //$this->setLayout('warning');
            JError::raiseError(404, JText::_("Could not find tag \"$tag\""));
        } else {
            $this->assignRef('pagination', $pagination);
            $this->assignRef('results', $results);
            $this->assign('total', $total);
            $this->assign('tagDescription', $tagDescription);

            $params = JComponentHelper::getParams('com_tag');

            $defaultLayout = $params->get('layout', 'default');
            $layout = JInput::get('layout', $defaultLayout, 'STRING');

            $this->setLayout($layout);
            $showMeta = $params->get('contentMeta', '1');
            $description = $params->get('description', '1');
            $ads =& $params->get('ads', '');
            $this->assign('showMeta', $showMeta);
            $this->assign('showDescription', $description);
            $this->assignRef('ads', $ads);

        }
        parent::display($tpl);

    }

    function add($tpl = null)
    {
        $tags =& $this->getTagsForArticle();
        $this->assignRef('tags', $tags);
        parent::display($tpl);
    }

    function warning($tpl = null)
    {
        parent::display($tpl);
    }

    function getTagsForArticle()
    {
        $cid = JFactory::getApplication()->input->get('article_id', null, 'int');
        if (isset($cid)) {
            $db =& JFactory::getDBO();
            $query = 'select t.name from #__tag_term_content as tc left join #__tag_term as t on t.id=tc.tid where tc.cid=' . $db->quote($cid);
            $db->setQuery($query);
            $tagsInArray = $db->loadResultArray();
            if (isset($tagsInArray) && !empty($tagsInArray)) {
                return implode(',', $tagsInArray);
            }
        }

        return '';
    }


}
