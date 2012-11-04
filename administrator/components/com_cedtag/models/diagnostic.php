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

require_once JPATH_SITE . '/components/com_cedtag/helpers/helper.php';

class CedTagModelDiagnostic extends JModel
{
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public function getDiagnostic()
    {
        $diagnostics = array();

        $curlDiagnostic = new stdClass();
        $hasCurl = in_array('curl', get_loaded_extensions()) == 1;

        $curlDiagnostic->title = JText::_('Curl');
        $curlDiagnostic->optionnal = JText::_('Optionnal');
        $curlDiagnostic->color = $hasCurl ? 'green;' : 'red;';
        $curlDiagnostic->status = $hasCurl ? JText::_('JYES') : JText::_('JNO');
        $curlDiagnostic->recommendedValue = JText::_('JYES');
        $curlDiagnostic->usedBy = JText::_('WikiPedia import of terms descriptions');
        $curlDiagnostic->resolution = JText::_('Required Root access or contacting your hosting company to activate cURL. cURL is a computer software project providing a library and command-line tool for transferring data using various protocols. The cURL project produces two products, libcurl and cURL. It was first released in 1997.');
        $diagnostics[] = $curlDiagnostic;

        //ini_get('allow_url_fopen');


        return $diagnostics;
    }
}