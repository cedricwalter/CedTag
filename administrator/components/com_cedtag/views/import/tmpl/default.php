<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');
?>

<form action="<?php echo JRoute::_('index.php?controller=import&option=com_cedtag&task=import'); ?>" method="post" name="adminForm" id="adminForm"
      autocomplete="off">
    <div class="width-100 fltlft">
        <fieldset class="adminform">
            <ul class="adminformlist">
                <li>
                    <label for="sourcekey"><?php echo JText::_('Import from');?>:</label>
                    <select name="source" id="section" class="inputbox" size="1">
                        <option value=""></option>
                        <option value="meta-keys"><?php echo JText::_('Joomla content meta keywords');?></option>
                        <option value="jtags">JTags</option>
                        <option value="joomlatags">Joomlatags.org</option>
                        <option value="joomlatagsPhil">Joomla Tags from Phil Taylor</option>
                        <!--<option value="acesef">AceSef meta keywords</option>-->
                    </select>
                </li>
            </ul>
        </fieldset>
    </div>
    <input type="hidden" name="task" value="import">
    <input type="hidden" name="controller" value="import">
    <?php echo JHTML::_('form.token'); ?>
</form>
