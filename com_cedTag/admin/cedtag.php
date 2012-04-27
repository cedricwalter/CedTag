<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

// Include dependencies
jimport('joomla.application.component.controller');
require_once (JPATH_COMPONENT . '/controller.php');
require_once (JPATH_COMPONENT . '/controllers/tag.php');
require_once (JPATH_COMPONENT . '/controllers/term.php');
require_once (JPATH_COMPONENT . '/controllers/css.php');
require_once (JPATH_COMPONENT . '/controllers/stopwords.php');
require_once (JPATH_COMPONENT . '/controllers/import.php');
require_once (JPATH_COMPONENT . '/controllers/export.php');
require_once (JPATH_COMPONENT . '/controllers/statistics.php');

$document = & JFactory::getDocument();
$document->addStyleSheet(JURI::root() . '/media/com_cedtag/css/admintag.css');

$jinput = JFactory::getApplication()->input;

$controller = JFactory::getApplication()->input->get('controller');
$task = JFactory::getApplication()->input->get('task');

// Create the controller
$classname = 'CedTagController' . $controller;

$controller = new $classname();

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();

?>
