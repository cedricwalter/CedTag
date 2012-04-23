<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');
?>
<form action="<?php echo JRoute::_('index.php?controller=export&option=com_cedtag'); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off">
    <div class="width-100 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('Export Tags to com_content');?></legend>
            <ul class="adminformlist">
                <li>
                    <?php echo JText::_('DESTINATION');?>
                    <select name="destination" id="section" class="inputbox" size="1">
                        <option value="meta-keys"><?php echo JText::_('META KEYWORDS');?></option>
                    </select>
                </li>
            </ul>
        </fieldset>
    </div>

    <input type="hidden" name="task" value="export">
    <input type="hidden" name="controller" value="export">
    <?php echo JHTML::_('form.token'); ?>

</form>