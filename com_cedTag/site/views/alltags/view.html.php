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

class CedTagViewAllTags extends JView
{
    function display($tpl = null)
    {
        $allTags = $this->get('AllTags');
        $this->assignRef('allTags', $allTags);
        $this->defaultTpl($tpl);
    }

    function defaultTpl($tpl = null)
    {
        parent::display($tpl);
    }
}
