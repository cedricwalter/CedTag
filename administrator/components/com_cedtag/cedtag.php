<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_cedtag')) {
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 401);
}

// Include dependencies
jimport('joomla.application.component.controller');

require_once (dirname(__FILE__) . '/controller.php');
require_once (dirname(__FILE__) . '/controllers/tag.php');
require_once (dirname(__FILE__) . '/controllers/term.php');
require_once (dirname(__FILE__) . '/controllers/css.php');
require_once (dirname(__FILE__) . '/controllers/stopwords.php');
require_once (dirname(__FILE__) . '/controllers/import.php');
require_once (dirname(__FILE__) . '/controllers/export.php');
require_once (dirname(__FILE__) . '/controllers/statistics.php');
require_once (dirname(__FILE__) . '/controllers/diagnostic.php');
require_once (dirname(__FILE__) . '/controllers/maintenance.php');

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . '/media/com_cedtag/css/admintag.css');

$controller = JFactory::getApplication()->input->get('controller');
$task = JFactory::getApplication()->input->get('task');

// Create the controller
$class = 'CedTagController' . $controller;

$controller = new $class();

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
