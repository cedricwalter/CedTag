<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.installer.installer');
$lang = &JFactory::getLanguage();
$db = & JFactory::getDBO();
$status = new JObject();
$status->modules = array();
$status->plugins = array();
$src = $this->parent->getPath('source');

if (version_compare(JVERSION, '1.6.0', 'ge')) {

    $modules = &$this->manifest->xpath('modules/module');
    foreach ($modules as $module) {
        $mname = $module->getAttribute('module');
        $client = $module->getAttribute('client');
        if (is_null($client)) $client = 'site';
        ($client == 'administrator') ? $path = $src . DS . 'administrator' . DS . 'modules' . DS . $mname : $path = $src . DS . 'modules' . DS . $mname;
        $installer = new JInstaller;
        $result = $installer->install($path);
        $status->modules[] = array('name' => $mname, 'client' => $client, 'result' => $result);
    }
}
if (version_compare(JVERSION, '1.6.0', 'ge')) {

    $plugins = &$this->manifest->xpath('plugins');
    foreach ($plugins as $plugin) {
        $pname = $plugin->getAttribute('plugin');
        $pgroup = $plugin->getAttribute('group');
        $path = $src . DS . 'plugins' . DS . $pgroup;
        $installer = new JInstaller;
        $result = $installer->install($path);
        $status->plugins[] = array('name' => $pname, 'group' => $pgroup, 'result' => $result);
        $query = "UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element=" . $db->Quote($pname) . " AND folder=" . $db->Quote($pgroup);
        $db->setQuery($query);
        $db->query();
    }
}



?>