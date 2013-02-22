<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport( 'joomla.application.input' );

/**
 * Joomla Tag component Controller
 *
 */
class CedTagController extends JController
{
    protected $default_view = 'frontpage';

    public function display($cacheable = false, $urlParams = false)
    {
        $view = JFactory::getApplication()->input->get('view');
        if (!isset($view)) {
            JFactory::getApplication()->input->set('view', 'frontpage');
        }
        parent::display($cacheable,$urlParams);
    }
}
