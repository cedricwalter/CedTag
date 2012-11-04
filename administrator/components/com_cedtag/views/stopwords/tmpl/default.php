<?php
/**
 * @package Component CedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html v3.0
 **/
defined('_JEXEC') or die('Restricted access');
?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off">

    <div class="width-100 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('StopWords do not appear as Terms because they are either insignificant (i.e., articles, prepositions) or common');?></legend>
            <div class="width-60 fltlft">
                <fieldset class="adminform">
                    <ul class="adminformlist">
                        <li><?php echo JText::_('FILE');?>
                            <?php if ($this->isWritable) { ?>
                                <strong style="color: green;"><?php echo JText::_('WRITEABLE');?></strong>
                                <?php } else { ?>
                                <strong style="color: red"><?php echo JText::_('UNWRITEABLE');?></strong>
                                <?php };?>
                        </li>
                        <li>
                            <?php echo($this->FileName);?>
                        </li>
                        <li>

                                <textarea <?php if (!$this->isWritable) {
                                echo('readonly');
                            };?>
                                style="width: 100%; height: 600px;"
                                cols="80"
                                rows="25"
                                name="content"
                                class="inputbox"><?php echo($this->FileContent);?></textarea>
                        </li>
                    </ul>
                </fieldset>
            </div>
        </fieldset>
    </div>

    <input type="hidden" name="task" value="save">
    <input type="hidden" name="controller" value="stopwords">
    <input type="hidden" name="option" value="com_cedtag">
    <?php echo JHTML::_('form.token'); ?>
</form>
