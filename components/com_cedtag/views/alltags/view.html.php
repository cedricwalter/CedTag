<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.application.pathway');

class CedTagViewAllTags extends JView
{

    function display($tpl = null)
    {
        $layout = Jrequest::getVar('layout');
        switch ($layout) {
            case 'wordle':
                $this->wordle($tpl);
                break;
            default:
                $this->allTags($tpl);
        }
    }

    function wordle($tpl = null)
    {
        //Get data from getWordle() in /models/allTags.php
        $result = $this->get('Wordle');
        $state = $this->get('State');

        //Make Data available to view
        $this->assignRef('cloud', $result['cloud']);
        $this->assignRef('img64', $result['img64']);
        $this->assignRef('params', $state->params);

        parent::display($tpl);
    }

    function allTags($tpl = null)
    {
        //Get data from getAllTags() in /models/allTags.php
        $allTags = $this->get('AllTags');
        $state = $this->get('State');

        //Make Data available to view
        $this->assignRef('list', $allTags);
        $this->assignRef('params', $state->params);

        parent::display($tpl);
    }
}
