<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.installer.installer');

$lang = &JFactory::getLanguage();
$lang->load('com_cedtag');

$db = & JFactory::getDBO();
$status = new JObject();
$status->modules = array();
$status->plugins = array();
$src = $this->parent->getPath('source');
$isUpdate = JFile::exists(JPATH_SITE.'/components/com_cedtag/cedtag.php');

if(version_compare( JVERSION, '1.6.0', 'ge' )) {

	$modules = &$this->manifest->xpath('modules/module');
	foreach($modules as $module){
		$mname = $module->getAttribute('module');
		$client = $module->getAttribute('client');
		if(is_null($client)) $client = 'site';
		($client=='administrator')? $path=$src.'/administrator/modules/'.$mname: $path = $src.'/modules/'.$mname;
		$installer = new JInstaller;
		$result = $installer->install($path);
		$status->modules[] = array('name'=>$mname,'client'=>$client, 'result'=>$result);
	}
}
else {

	$modules = &$this->manifest->getElementByPath('modules');
	if (is_a($modules, 'JSimpleXMLElement') && count($modules->children())) {
		foreach ($modules->children() as $module) {
			$mname = $module->attributes('module');
			$client = $module->attributes('client');
			if(is_null($client)) $client = 'site';
			($client=='administrator')? $path=$src.'/administrator/modules/'.$mname: $path = $src.'/modules/'.$mname;
			$installer = new JInstaller;
			$result = $installer->install($path);
			$status->modules[] = array('name'=>$mname,'client'=>$client, 'result'=>$result);
		}
	}


}

if(version_compare( JVERSION, '1.6.0', 'ge' )) {
	
	$plugins = &$this->manifest->xpath('plugins/plugin');
	foreach($plugins as $plugin){
		$pname = $plugin->getAttribute('plugin');
		$pgroup = $plugin->getAttribute('group');
		if($pgroup == 'finder' && version_compare( JVERSION, '2.5.0', '<' ))
		{
			continue;
		}
		$path = $src.'/plugins/'.$pgroup;
		$installer = new JInstaller;
		$result = $installer->install($path);
		
		
		$query = "UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element=".$db->Quote($pname)." AND folder=".$db->Quote($pgroup);
		$db->setQuery($query);
		$db->query();
		
		$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'result'=>$result);
	}
}
else {
	$plugins = &$this->manifest->getElementByPath('plugins');
	if (is_a($plugins, 'JSimpleXMLElement') && count($plugins->children())) {

		foreach ($plugins->children() as $plugin) {
			$pname = $plugin->attributes('plugin');
			$pgroup = $plugin->attributes('group');
			if($pgroup == 'finder')
			{
				continue;
			}
			$path = $src.'/plugins/'.$pgroup;
			$installer = new JInstaller;
			$result = $installer->install($path);
			
			$query = "UPDATE #__plugins SET published=1 WHERE element=".$db->Quote($pname)." AND folder=".$db->Quote($pgroup);
			$db->setQuery($query);
			$db->query();
			
			$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'result'=>$result);
			
			
		}
	}



}

?>

<?php $rows = 0; ?>
<img src="<?php echo JURI::root(true); ?>/media/com_cedtag/images/tag_logo48.png" width="48" height="48" alt="CedTag Component" align="right" />
<h2><?php echo JText::_('CEDTAG_INSTALLATION_STATUS'); ?></h2>
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
			<td class="key" colspan="2"><?php echo JText::_('CEDTAG_COMPONENT'); ?></td>
			<td><strong><?php echo JText::_('CEDTAG_INSTALLED'); ?></strong></td>
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
			<td><strong><?php echo ($module['result'])?JText::_('CEDTAG_INSTALLED'):JText::_('CEDTAG_NOT_INSTALLED'); ?></strong></td>
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
			<td><strong><?php echo ($plugin['result'])?JText::_('CEDTAG_INSTALLED'):JText::_('CEDTAG_NOT_INSTALLED'); ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
