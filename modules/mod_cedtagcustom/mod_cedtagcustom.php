<?php
/**
 * @package module cedCustomTagsCloud for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).'/helper.php');

$list = modCedCustomTagsCloudHelper::getList($params);

if (!count($list)) {
    return;
}

$layout = $params->get('layout', 'default');
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_cedtagcustom', $layout);