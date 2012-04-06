<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @version $Id: com_tag.php 599 2010-06-06 23:26:33Z you $
 * @author waltercedric.com, Joomlatags.org
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
jimport('joomla.application.component.controller');
jimport( 'joomla.application.input' );

/**
 * Joomla Tag component Controller
 *
 */
class TagController extends JController
{
    protected $default_view = 'frontpage';


    function display()
    {
        $view = JRequest::getVar('view');
        if (!isset($view)) {
            JFactory::getApplication()->input->set('view', 'frontpage');
        }
        parent::display();
    }

    public function display2($cachable = false, $urlparams = false)
    {
        //	require_once JPATH_COMPONENT.'/helpers/search.php';

        // Load the submenu.
        //	SearchHelper::addSubmenu(JRequest::getCmd('view', 'searches'));

        parent::display();
    }


}

?>
