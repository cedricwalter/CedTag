<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('Batch add terms, comma separated');?></legend>
            <ul class="adminformlist">
                <li>
                    <textarea id="names" name="names" rows="5" cols="60" placeholder="<?php echo JText::_('Enter terms separated by comma like for example: cedric,walter,joomla,extension');?>"></textarea>
                </li>
            </ul>
        </fieldset>
    </div>

    <input type="hidden" name="task" value="batchsave">
    <input type="hidden" name="controller" value="term">
    <input type="hidden" name="option" value="com_cedtag">
    <?php echo JHTML::_('form.token'); ?>
</form>

