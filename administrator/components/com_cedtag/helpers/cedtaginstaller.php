<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.log.log');

class CedTagInstaller
{
    var $src = null;

    public function CedTagInstaller($src)
    {
        $this->src = $src;
        JLog::addLogger(array());
    }

    public function installPlugins($plugins, &$status)
    {
        $db = JFactory::getDBO();
        if (is_a($plugins, 'JSimpleXMLElement') && count($plugins->children())) {

            foreach ($plugins->children() as $plugin) {
                $pluginName = $plugin->attributes('plugin');
                $pluginGroup = $plugin->attributes('group');
                if ($pluginGroup == 'finder') {
                    continue;
                }
                $path = $this->src . '/plugins/' . $pluginGroup;
                $installer = new JInstaller;
                $result = $installer->install($path);

                $query = "UPDATE #__plugins SET published=1 WHERE element=" . $db->Quote($pluginName) . " AND folder=" . $db->Quote($pluginGroup);
                $db->setQuery($query);
                $db->query();

                $status->plugins[] = array('name' => $pluginName, 'group' => $pluginGroup, 'result' => $result);
            }
        }
    }

    public function installModules($modules, &$status)
    {
        if (is_a($modules, 'JSimpleXMLElement') && count($modules->children())) {
            foreach ($modules->children() as $module) {
                $moduleName = $module->attributes('module');
                $client = $module->attributes('client');
                if (is_null($client)) {
                    $client = 'site';
                }
                if ($client == 'administrator') {
                    $path = $this->src . '/administrator/modules/' . $moduleName;
                } else {
                    $path = $this->src . '/modules/' . $moduleName;
                }
                $installer = new JInstaller;
                $result = $installer->install($path);
                $status->modules[] = array('name' => $moduleName, 'client' => $client, 'result' => $result);
            }
        }
    }

    public function removeModules($modules, &$status)
    {
        if (is_a($modules, 'JSimpleXMLElement') && count($modules->children())) {
            foreach ($modules->children() as $module) {
                $element = $module->attributes('module');
                $client = $module->attributes('client');
                $this->removeModule($element, $client, $status);
            }
        }
    }

    public function removeModule($element, $client_id, &$status)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);
        $query->select('extension_id');
        $query->from('#__extensions');
        $query->where($dbo->quoteName('type') . ' = ' . $dbo->quote('module'));
        $query->where($dbo->quoteName('element') . ' = ' . $dbo->quote($element));
        $query->where($dbo->quoteName('client_id') . ' = ' . $dbo->quote($client_id));

        $q = $query->dump();

        $dbo->setQuery($query);
        $result = false;
        $id = $dbo->loadResult();
        if ($id) {
            $installer = new JInstaller;
            $result = $installer->uninstall('module', $id, 0);
        }
        $status->modules[] = array('name' => $element, 'client' => $client_id, 'result' => $result);
    }

    public function removePlugins($pluginIds, &$status)
    {
        if (is_a($pluginIds, 'JSimpleXMLElement') && count($pluginIds->children())) {
            foreach ($pluginIds->children() as $plugin) {
                $pluginName = $plugin->attributes('plugin');
                $pluginGroup = $plugin->attributes('group');
                if ($pluginGroup == 'finder') {
                    continue;
                }
                $this->removePlugin($pluginName, $pluginGroup, $status);
            }
        }
    }

    public function removePlugin($element, $folder, &$status)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);
        $query->select('extension_id');
        $query->from('#__extensions');
        $query->where($dbo->quoteName('folder') . ' = ' . $dbo->quote($folder));
        $query->where($dbo->quoteName('type') . ' = ' . $dbo->quote('plugin'));
        $query->where($dbo->quoteName('element') . ' = ' . $dbo->quote($element));

        $q = $query->dump();
        JLog::add('removing plugin ' . $q);

        $result = false;
        $dbo->setQuery($query);
        $id = $dbo->loadResult();
        if ($id) {
            $installer = new JInstaller;
            $result = $installer->uninstall('plugin', intval($id), 1);
            JLog::add('removing plugin by id' . $id . ' done!');
        }
        $status->plugins[] = array('name' => $element, 'group' => $folder, 'result' => $result);
    }

}