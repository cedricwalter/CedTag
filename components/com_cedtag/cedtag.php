<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL v3.0 http://www.gnu.org/copyleft/gpl.html
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport( 'joomla.application.input' );

// Create the controller
$controller = JController::getInstance('CedTag');

$task = JFactory::getApplication()->input->get('task', 'default', 'string');

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();