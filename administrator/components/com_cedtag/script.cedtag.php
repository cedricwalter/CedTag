<?php

// no direct access
defined('_JEXEC') or die;

//http://docs.joomla.org/Developing_a_Model-View-Controller_Component/2.5/Adding_an_install-uninstall-update_script_file
class com_CedTagInstallerScript
{

    function preflight($type, $parent)
    {
    }

    public function postflight($type, $parent)
    {
        $db = JFactory::getDBO();

        $status = new stdClass;
        $status->modules = array();
        $status->plugins = array();
        $status->libraries = array();


        $src = $parent->getParent()->getPath('source');
        $manifest = $parent->getParent()->manifest;

        $plugins = $manifest->xpath('plugins/plugin');
        foreach ($plugins as $plugin) {
            $name = (string)$plugin->attributes()->plugin;
            $group = (string)$plugin->attributes()->group;
            $path = $src . '/plugins/' . $group;
            if (JFolder::exists($src . '/plugins/' . $group . '/' . $name)) {
                $path = $src . '/plugins/' . $group . '/' . $name;
            }
            $installer = new JInstaller;
            $result = $installer->install($path);
            $query = "UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element=" . $db->Quote($name) . " AND folder=" . $db->Quote($group);
            $db->setQuery($query);
            $db->query();
            $status->plugins[] = array('name' => $name, 'group' => $group, 'result' => $result);
        }
        $modules = $manifest->xpath('modules/module');
        foreach ($modules as $module) {
            $name = (string)$module->attributes()->module;
            $client = (string)$module->attributes()->client;
            if (is_null($client)) {
                $client = 'site';
            }
            ($client == 'administrator') ? $path = $src . '/administrator/modules/' . $name : $path = $src . '/modules/' . $name;

            if ($client == 'administrator') {
                $db->setQuery("SELECT id FROM #__modules WHERE `module` = " . $db->quote($name));
                $isUpdate = (int)$db->loadResult();
            }

            $installer = new JInstaller;
            $result = $installer->install($path);

            $status->modules[] = array('name' => $name, 'client' => $client, 'result' => $result);
            if ($client == 'administrator' && !$isUpdate) {
                $position = version_compare(JVERSION, '3.0', '<') && $name == 'mod_k2_quickicons' ? 'icon' : 'cpanel';
                $db->setQuery("UPDATE #__modules SET `position`=" . $db->quote($position) . ",`published`='1' WHERE `module`=" . $db->quote($name));
                $db->query();

                $db->setQuery("SELECT id FROM #__modules WHERE `module` = " . $db->quote($name));
                $id = (int)$db->loadResult();

                $db->setQuery("INSERT IGNORE INTO #__modules_menu (`moduleid`,`menuid`) VALUES (" . $id . ", 0)");
                $db->query();
            }
        }

        $libraries = $manifest->xpath('library/library');
        foreach ($libraries as $library) {
            $name = (string)$library->attributes()->name;
            $path = $src . '/libraries/' . $name;
            $installer = new JInstaller;
            $result = $installer->install($path);

            $status->libraries[] = array('name' => $name, 'client' => '', 'result' => $result);
        }

        $this->installationResults($status);
    }

    public function uninstall($parent)
    {
        $db = JFactory::getDBO();
        $status = new stdClass;
        $status->modules = array();
        $status->plugins = array();
        $manifest = $parent->getParent()->manifest;

        $plugins = $manifest->xpath('plugins/plugin');
        foreach ($plugins as $plugin) {
            $name = (string)$plugin->attributes()->plugin;
            $group = (string)$plugin->attributes()->group;
            $query = "SELECT `extension_id` FROM #__extensions WHERE `type`='plugin' AND element = " . $db->Quote($name) . " AND folder = " . $db->Quote($group);
            $db->setQuery($query);
            $extensions = $db->loadColumn();
            if (count($extensions)) {
                foreach ($extensions as $id) {
                    $installer = new JInstaller;
                    $result = $installer->uninstall('plugin', $id);
                }
                $status->plugins[] = array('name' => $name, 'group' => $group, 'result' => $result);
            }

        }
        $modules = $manifest->xpath('modules/module');
        foreach ($modules as $module) {
            $name = (string)$module->attributes()->module;
            $client = (string)$module->attributes()->client;
            $db = JFactory::getDBO();
            $query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='module' AND element = " . $db->Quote($name) . "";
            $db->setQuery($query);
            $extensions = $db->loadColumn();
            if (count($extensions)) {
                foreach ($extensions as $id) {
                    $installer = new JInstaller;
                    $result = $installer->uninstall('module', $id);
                }
                $status->modules[] = array('name' => $name, 'client' => $client, 'result' => $result);
            }

        }
        $this->uninstallationResults($status);
    }

    public function update($type)
    {
        //$this->removeCedTagBefore260();
    }

    private function installationResults($status)
    {
        $language = JFactory::getLanguage();
        $language->load('com_cedtag');
        $rows = 0; ?>
    <img src="<?php echo JURI::root(true); ?>/media/com_cedtag/images/logo.png" alt="logo" align="right"/>
    <h2><?php echo JText::_('CEDTAG_INSTALLATION_STATUS'); ?></h2>
    <table class="adminlist table table-striped">
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
            <tr class="row<?php echo(++$rows % 2); ?>">
                <td class="key"><?php echo $module['name']; ?></td>
                <td class="key"><?php echo ucfirst($module['client']); ?></td>
                <td><strong><?php echo ($module['result']) ? JText::_('CEDTAG_INSTALLED') : JText::_('CEDTAG_NOT_INSTALLED'); ?></strong></td>
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
            <tr class="row<?php echo(++$rows % 2); ?>">
                <td class="key"><?php echo ucfirst($plugin['name']); ?></td>
                <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
                <td><strong><?php echo ($plugin['result']) ? JText::_('CEDTAG_INSTALLED') : JText::_('CEDTAG_NOT_INSTALLED'); ?></strong></td>
            </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
    }

    private function uninstallationResults($status)
    {
        $language = JFactory::getLanguage();
        $language->load('com_cedtag');
        $rows = 0;
        ?>
    <h2><?php echo JText::_('REMOVAL_STATUS'); ?></h2>
    <table class="adminlist table table-striped">
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
            <td class="key" colspan="2"><?php echo 'CedTag ' . JText::_('CEDTAG_COMPONENT'); ?></td>
            <td><strong><?php echo JText::_('CEDTAG_REMOVED'); ?></strong></td>
        </tr>
            <?php if (count($status->modules)): ?>
        <tr>
            <th><?php echo JText::_('CEDTAG_MODULE'); ?></th>
            <th><?php echo JText::_('CEDTAG_CLIENT'); ?></th>
            <th></th>
        </tr>
            <?php foreach ($status->modules as $module): ?>
            <tr class="row<?php echo(++$rows % 2); ?>">
                <td class="key"><?php echo $module['name']; ?></td>
                <td class="key"><?php echo ucfirst($module['client']); ?></td>
                <td><strong><?php echo ($module['result']) ? JText::_('CEDTAG_REMOVED') : JText::_('CEDTAG_NOT_REMOVED'); ?></strong></td>
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
            <tr class="row<?php echo(++$rows % 2); ?>">
                <td class="key"><?php echo ucfirst($plugin['name']); ?></td>
                <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
                <td><strong><?php echo ($plugin['result']) ? JText::_('CEDTAG_REMOVED') : JText::_('CEDTAG_NOT_REMOVED'); ?></strong></td>
            </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
    }

    private function removeCedTagBefore260()
    {
        //error_log(JText::_('Custom Install script start'));
        $status = new JObject();
        $status->modules = array();
        $status->plugins = array();


        //Order is important first suppress plugins
        //error_log(JText::_('Custom Install script remove old plugins'));
        $this->removePlugin("cedTagSef", "system", $status);
        $this->removePlugin("cedSearchTags", "search", $status);
        $this->removePlugin("cedTags", "content", $status);
        $this->removePlugin("cedAddTags", "editors-xtd", $status);

        //Order is important then suppress modules
        //error_log(JText::_('Custom Install script remove old modules'));
        $this->removeModule("mod_cedLatestTags", "0", $status);
        $this->removeModule("mod_cedMostPopularTags", "0", $status);
        $this->removeModule("mod_cedMostReadTags", "0", $status);
        $this->removeModule("mod_cedRandomTags", "0", $status);
        $this->removeModule("mod_cedCustomTagsCloud", "0", $status);

        //error_log(JText::_('Custom Install script end'));

        $installer = new JInstaller;
        $installer->uninstall('component', 'com_cedTag', 0);
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
        error_log('removing module ' . $q);

        $dbo->setQuery($query);
        $result = false;
        $id = $dbo->loadResult();
        if ($id) {
            $installer = new JInstaller;
            $result = $installer->uninstall('module', $id, 0);
            error_log('removing module by id' . $id . ' done!');
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
        error_log('removing plugin ' . $q);

        $result = false;
        $dbo->setQuery($query);
        $id = $dbo->loadResult();
        if ($id) {
            $installer = new JInstaller;
            $result = $installer->uninstall('plugin', intval($id), 1);
            error_log('removing plugin by id' . $id . ' done!');
        }
        $status->plugins[] = array('name' => $element, 'group' => $folder, 'result' => $result);
    }

}