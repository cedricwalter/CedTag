<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @version $Id: com_tag.php 599 2010-06-06 23:26:33Z you $
 * @author waltercedric.com, Joomlatags.org
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_COMPONENT . '/controller.php');
require_once (JPATH_COMPONENT . '/controllers/tag.php');
require_once (JPATH_COMPONENT . '/controllers/term.php');
require_once (JPATH_COMPONENT . '/controllers/css.php');
require_once (JPATH_COMPONENT . '/controllers/import.php');
$document = & JFactory::getDocument();
$document->addStyleSheet(JURI::root(true) . '/administrator/components/com_tag/css/tag.css');

$jinput = JFactory::getApplication()->input;

$controller = JRequest::getVar('controller');
// Require specific controller if requested
// Create the controller
$classname = 'TagController' . $controller;

$controller = new $classname();

// Perform the Request task
$controller->execute(JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();

?>
