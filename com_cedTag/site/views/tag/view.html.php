<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.application.pathway');
jimport('joomla.application.input');
require_once JPATH_SITE . '/components/com_cedtag/helper/helper.php';

class CedTagViewTag extends JView
{
    function display($tpl = null)
    {
        $layout = JFactory::getApplication()->input->get('layout', null, 'string');
        switch ($layout) {
            case 'warning':
                $this->warning($tpl);
                break;
            default:
                $this->defaultTpl($tpl);
        }

    }

    private function defaultTpl($tpl = null)
    {
        $tag = JFactory::getApplication()->input->get('tag', null, 'string');

        $cedTagsHelper = new CedTagsHelper();
        $tag = $cedTagsHelper->unUrlTagname($tag);

        JFactory::getApplication()->input->set('tag', $tag);

        $results = $this->get('Data');
        $total = $this->get('Total');
        $pagination = $this->get('Pagination');
        $tagDescription = $this->get('TagDescription');
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

            $params = JComponentHelper::getParams('com_cedtag');

            $defaultLayout = $params->get('layout', 'default');
            $layout = JInput::get('layout', $defaultLayout, 'STRING');

            $this->setLayout($layout);

            $showMeta = $params->get('contentMeta', '1');
            $description = $params->get('description', '1');
            $ads = $params->get('ads', '');

            $this->assign('showMeta', $showMeta);
            $this->assign('showDescription', $description);
            $this->assignRef('ads', $ads);

        }
        parent::display($tpl);

    }

    private function warning($tpl = null)
    {
        parent::display($tpl);
    }


}
