<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');


$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
$mainframe = JFactory::getApplication();
$search = $mainframe->getUserStateFromRequest('articleelement.search', 'search', '', 'string');
$search = JString::strtolower($search);
$rows = $this->termList->list;

require_once JPATH_SITE . '/components/com_cedtag/helpers/suggest.php';
$CedTagSuggest = new CedTagSuggest();
$CedTagSuggest->addAdminJs(array(), null);
?>

<form action="<?php echo JRoute::_('index.php?controller=term&option=com_cedtag'); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label for="search"><input
                    type="text"
                    placeholder="<?php echo JText::_('Filter by Term'); ?>"
                    name="search" id="search"
                    value="<?php echo($search);?>"
                    class="text_area" onchange="document.adminForm.submit();"/></label>
            <button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
            <button
                    onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
        </div>
        <div class="filter-search fltlft">
            <?php echo JText::_('Quick way to create new tags and press enter to create'); ?>:<ul id="tags" class="tags"></ul>
        </div>
        <div class="filter-select fltrt">
            <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
            </select>
        </div>
    </fieldset>


    <table class="adminlist">
        <thead>
        <tr>
            <th width="10" align="left"><?php echo JText::_('Num'); ?></th>
            <th width="20"><input type="checkbox" name="toggle" value=""
                                  onclick="checkAll(<?php echo count($rows);?>);"/></th>
            <th class="title"><?php echo JText::_('TERM');?></th>
            <th class="title"><?php echo JText::_('STATE');?></th>
            <th width="40%"><?php echo JText::_('DESCRIPTION');?></th>
            <th width="3%"><?php echo JText::_('WEIGHT');?></th>
            <th width="7%"><?php echo JText::_('HITS');?></th>
            <th width="15%"><?php echo JText::_('DATE');?></th>
            <th width="10%"><?php echo JText::_('ARTICLE NUMBERS');?></th>
            <th width="2%" nowrap="nowrap">Id</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="9" align="center">
                <div align="center"><?php echo $this->termList->page->getPagesLinks(); ?>
                </div>
            </td>
        </tr>
        </tfoot>
        <tbody>
        <?php
        $k = 0;
        $cedTagsHelper = new CedTagsHelper();
        if (count($rows)) {
            for ($i = 0, $n = count($rows); $i < $n; $i++) {
                $row = & $rows[$i];
                JFilterOutput::objectHtmlSafe($row);
                $row->description = $cedTagsHelper->truncate($row->description);
                $link = 'index.php?option=com_cedtag&controller=term&task=edit&cid[]=' . $row->id;
                $checked = JHTML::_('grid.id', $i, $row->id);

                ?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $limitstart + $i + 1  ?></td>
                <td><?php echo $checked; ?></td>
                <td><a href="<?php echo JRoute::_($link); ?>"> <?php echo $row->name; ?></a></td>
                <td class="order"><?php echo $row->published; ?></td>
                <td class="order"><?php echo $row->description; ?></td>
                <td align="center"><?php echo $row->weight; ?></td>
                <td align="center"><?php echo $row->hits; ?></td>
                <td align="center"><?php echo $row->created; ?></td>
                <td align="center"><?php echo $row->count; ?></td>
                <td align="center"><?php echo $row->id; ?></td>
                <?php
            }
            $k = 1 - $k;
            ?>
		</tr>

		<?php
        } else {
            ?>
        <tr>
            <td colspan="9"><?php echo JText::_('THERE ARE NO TERMS'); ?></td>
        </tr>
            <?php
        }

        ?>
        </tbody>
    </table>

    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="task" value="">
    <input type="hidden" name="controller" value="term">
    <?php echo JHTML::_('form.token'); ?>
    <input type="hidden" name="limitstart"></form>