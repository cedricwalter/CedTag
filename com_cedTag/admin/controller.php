<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
jimport('joomla.application.component.controller');
jimport( 'joomla.application.input' );

/**
 * Joomla Tag component Controller
 *
 */
class CedTagController extends JController
{
    protected $default_view = 'frontpage';

    function display()
    {
        $view = JFactory::getApplication()->input->get('view');
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
