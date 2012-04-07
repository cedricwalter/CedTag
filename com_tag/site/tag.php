<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @version $Id: com_tag.php 599 2010-06-06 23:26:33Z you $
 * @author waltercedric.com, Joomlatags.org
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport( 'joomla.application.input' );

// Create the controller
$controller = JController::getInstance('Tag');

$task = JFactory::getApplication()->input->get('task', 'default', 'string');

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();