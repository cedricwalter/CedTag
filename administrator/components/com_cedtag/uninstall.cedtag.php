<?php
/**
 * @version		$Id: uninstall.k2.php 1523 2012-03-09 12:01:49Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.installer.installer');

// Load K2 language file
$lang = &JFactory::getLanguage();
$lang->load('com_cedtag');

$status = new JObject();
$status->modules = array ();
$status->plugins = array ();

if(version_compare( JVERSION, '1.6.0', 'ge' )) {
	$modules = & $this->manifest->xpath('modules/module');
	$plugins = & $this->manifest->xpath('plugins/plugin');
	foreach($modules as $module){
		$mname = $module->getAttribute('module');
		$client = $module->getAttribute('client');
		$db = & JFactory::getDBO();
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='module' AND element = ".$db->Quote($mname)."";
		$db->setQuery($query);
		$IDs = $db->loadResultArray();
		if (count($IDs)) {
			foreach ($IDs as $id) {
				$installer = new JInstaller;
				$result = $installer->uninstall('module', $id);
			}
		}
		$status->modules[] = array ('name'=>$mname, 'client'=>$client, 'result'=>$result);
	}

	foreach ($plugins as $plugin) {

		$pname = $plugin->getAttribute('plugin');
		$pgroup = $plugin->getAttribute('group');
		$db = & JFactory::getDBO();



		$query = "SELECT `extension_id` FROM #__extensions WHERE `type`='plugin' AND element = ".$db->Quote($pname)." AND folder = ".$db->Quote($pgroup);
		$db->setQuery($query);
		$IDs = $db->loadResultArray();
		if (count($IDs)) {
			foreach ($IDs as $id) {
				$installer = new JInstaller;
				$result = $installer->uninstall('plugin', $id);
			}
		}
		$status->plugins[] = array ('name'=>$pname, 'group'=>$pgroup, 'result'=>$result);
	}
	
	
}
else {


	$modules = & $this->manifest->getElementByPath('modules');
	$plugins = & $this->manifest->getElementByPath('plugins');

	if (is_a($modules, 'JSimpleXMLElement') && count($modules->children())) {

		foreach ($modules->children() as $module) {

			$mname = $module->attributes('module');
			$client = $module->attributes('client');
			$db = & JFactory::getDBO();
			$query = "SELECT `id` FROM `#__modules` WHERE module = ".$db->Quote($mname)."";
			$db->setQuery($query);
			$modules = $db->loadResultArray();
			if (count($modules)) {
				foreach ($modules as $module) {
					$installer = new JInstaller;
					$result = $installer->uninstall('module', $module, 0);
				}
			}
			$status->modules[] = array ('name'=>$mname, 'client'=>$client, 'result'=>$result);
		}
	}

	if (is_a($plugins, 'JSimpleXMLElement') && count($plugins->children())) {

		foreach ($plugins->children() as $plugin) {

			$pname = $plugin->attributes('plugin');
			$pgroup = $plugin->attributes('group');
			if($pgroup == 'finder')
			{
				continue;
			}
			$db = & JFactory::getDBO();
			$query = 'SELECT `id` FROM #__plugins WHERE element = '.$db->Quote($pname).' AND folder = '.$db->Quote($pgroup);
			$db->setQuery($query);
			$plugins = $db->loadResultArray();
			if (count($plugins)) {
				foreach ($plugins as $plugin) {
					$installer = new JInstaller;
					$result = $installer->uninstall('plugin', $plugin, 0);
				}
			}
			$status->plugins[] = array ('name'=>$pname, 'group'=>$pgroup, 'result'=>$result);
		}
	}

	

}


?>

<?php $rows = 0; ?>
<h2><?php echo JText::_('CEDTAG_REMOVAL_STATUS'); ?></h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('CEDTAG_EXTENSION'); ?></th>
			<th width="30%"><?php echo JText::_('CEDTAG_STATUS'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'K2 '.JText::_('CEDTAG_COMPONENT'); ?></td>
			<td><strong><?php echo JText::_('CEDTAG_REMOVED'); ?></strong></td>
		</tr>
		<?php if (count($status->modules)): ?>
		<tr>
			<th><?php echo JText::_('CEDTAG_MODULE'); ?></th>
			<th><?php echo JText::_('CEDTAG_CLIENT'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->modules as $module): ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong><?php echo ($module['result'])?JText::_('CEDTAG_REMOVED'):JText::_('CEDTAG_NOT_REMOVED'); ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>

		<?php if (count($status->plugins)): ?>
		<tr>
			<th><?php echo JText::_('CEDTAG_PLUGIN'); ?></th>
			<th><?php echo JText::_('CEDTAG_GROUP'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->plugins as $plugin): ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong><?php echo ($plugin['result'])?JText::_('CEDTAG_REMOVED'):JText::_('CEDTAG_NOT_REMOVED'); ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
