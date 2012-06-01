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

require_once JPATH_SITE . '/components/com_cedtag/helper/helper.php';

class CedTagModelDiagnostic extends JModel
{
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public function getDiagnostic()
    {
        $diagnostic = new stdClass();
        $diagnostic->curl = in_array('curl', get_loaded_extensions());
        //ini_get('allow_url_fopen');

        return $diagnostic;
    }
}