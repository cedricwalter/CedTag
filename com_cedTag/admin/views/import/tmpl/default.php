<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');
?>

<form action="<?php echo JRoute::_('index.php?controller=import&option=com_cedtag'); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off">
    <table class="admintable">
        <tr>
            <td class="key"><label for="source"><?php echo JText::_('SOURCE');?>:</label></td>

            <td><select name="source" id="section" class="inputbox" size="3">
                <option value="meta-keys"><?php echo JText::_('META KEYWORDS');?></option>
                <option value="jtags">JTags</option>
                <option value="joomlatags">Joomla tags</option>
                <option value="acesef">AceSef meta keywords</option>
            </select></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input
                type="submit" name="Import" value="<?php echo JText::_('IMPORT');?>" class="inputbox"></td>
        </tr>

    </table>
    <input type="hidden" name="task" value="import">
    <input type="hidden" name="controller" value="import">
    <?php echo JHTML::_('form.token'); ?>

</form>
