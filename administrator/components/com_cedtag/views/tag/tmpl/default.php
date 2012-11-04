<?php
/**
 * @package        Joomla.Administrator
 * @subpackage    com_content
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

require_once JPATH_SITE . '/components/com_cedtag/helpers/suggest.php';

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';

$canDo = UsersHelper::getActions();
$coreCreate = $canDo->get('core.create');
$disabled = $coreCreate ? "" : "disabled";
?>

<form action="<?php echo JRoute::_('index.php?option=com_cedtag&controller=tag');?>" method="post" name="adminForm" id="adminForm">
    <legend>
        <?php if (!$coreCreate) { ?>
            <p style="color:red;">
                      <?php echo JText::_("You do not have enough permissions: You need core.create permissions to be able to create/add/remove tags");?>
            </p>
        <?php } ?>
        <?php echo JText::_('Term that are not published will be never displayed here or after hitting save.');?><br/>
        <?php echo JText::_('New Terms that are not existing will be automatically created.');?>
    </legend>
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('Article Filter'); ?></label>
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
                   title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>"/>
            <button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        <div class="filter-search fltlft">
                    <label class="filter-search-lbl" for="tag_filter_search"><?php echo JText::_('Tag Filter'); ?></label>
                    <input type="text"
                           name="tag_filter_search"
                           placeholder="<?php echo JText::_('Tags separated by comma'); ?>"
                           id="tag_filter_search"
                           size="25"
                           value="<?php echo $this->escape($this->state->get('tag.filter.search')); ?>"
                           title="<?php echo JText::_('Search For Terms: multi-tag filtering is possible to simply select multiple tags and/or searching for one tag and then another within the previous results.'); ?>"/>

                    <button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
                    <button type="button" onclick="document.id('tag_filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        <div class="filter-select fltrt">
            <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
            </select>

            <select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', $this->state->get('filter.category_id'));?>
            </select>

            <select name="filter_level" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_MAX_LEVELS');?></option>
                <?php echo JHtml::_('select.options', $this->f_levels, 'value', 'text', $this->state->get('filter.level'));?>
            </select>

            <select name="filter_access" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
            </select>

            <select name="filter_author_id" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_AUTHOR');?></option>
                <?php echo JHtml::_('select.options', $this->authors, 'value', 'text', $this->state->get('filter.author_id'));?>
            </select>

            <select name="filter_language" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
            </select>
        </div>
    </fieldset>
    <div class="clr"></div>

    <table class="adminlist">
        <thead>
        <tr>
            <th>
                <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
            </th>
            <th width="5%">
                <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
            </th>
            <th width="5%">
                <?php echo JHtml::_('grid.sort', 'JFEATURED', 'a.featured', $listDirn, $listOrder, NULL, 'desc'); ?>
            </th>
            <th width="10%">
                <?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
            </th>
            <th width="10%">
                <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
            </th>
            <th width="10%">
                <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_CREATED_BY', 'a.created_by', $listDirn, $listOrder); ?>
            </th>
            <th width="5%">
                <?php echo JHtml::_('grid.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
            </th>
            <th width="5%">
                <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
            </th>
            <th width="1%" class="nowrap">
                <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
            </th>
            <th width="10%">
                Tags
            </th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="15">
                <?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
        </tfoot>
        <tbody>
        <?php
           $tabindex= 1;
           foreach ($this->items as $i => $item) :
            $item->max_ordering = 0; //??
            $ordering = ($listOrder == 'a.ordering');
            $canCreate = false;
            $canEdit = false;
            $canCheckin = false;
            $canEditOwn = false;
            $canChange = false;
            ?>
        <tr class="row<?php echo $i % 2; ?>">

            <td>
                <?php if ($item->checked_out) : ?>
                <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'articles.', $canCheckin); ?>
                <?php endif; ?>
                <?php echo $this->escape($item->title); ?>
                <p class="smallsub">
                    <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
            </td>
            <td class="center">
                <?php
                echo JHtml::_('jgrid.published', $item->state, $i, 'articles.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
            </td>
            <td class="center">
                <?php echo JHtml::_('contentadministrator.featured', $item->featured, $i, $canEdit); ?>
            </td>
            <td class="center">
                <?php echo $this->escape($item->category_title); ?>
            </td>
            <td class="center">
                <?php echo $this->escape($item->access_level); ?>
            </td>
            <td class="center">
                <?php echo $this->escape($item->author_name); ?>
            </td>
            <td class="center nowrap">
                <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
            </td>
            <td class="center">
                <?php if ($item->language == '*'): ?>
                <?php echo JText::alt('JALL', 'language'); ?>
                <?php else: ?>
                <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
                <?php endif;?>
            </td>
            <td class="center">
                <?php echo (int)$item->id; ?>
            </td>
            <td class="center">
                <?php
                $CedTagSuggest = new CedTagSuggest();
                $CedTagSuggest->addAdminJs($item->tagit, $item->id, $tabindex++);
                ?>
                <ul id="tags<?php echo (int)$item->id; ?>" class="tags" style="width: 400px;"></ul>

            </td>
        </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
