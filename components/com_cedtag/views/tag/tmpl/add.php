<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

?>

<form action="index.php" method="post" name="addTags" id="addTags">
    <div style="float: left;">
        <h1><?php if (isset($this->tags) && !empty($this->tags)) {
            echo(JText::_('EDIT TAGS'));
        } else {
            echo(JText::_('ADD TAGS'));
        }?></h1>
    </div>

    <div class="clr"></div>

    <div style="float: left;">
        <label for="tags">
            <textarea id="tags" name="tags" rows="5" cols="60"><?php echo($this->tags);?></textarea>
        </label>
    </div>

    <div class="clr"></div>

    <div style="float: left;">
        <input type="submit" name="Submit" value="<?php echo JText::_('SAVE');?>" class="button"/>
        <input type="button" name="cancel" value="<?php echo JText::_('CANCEL'); ?>"
               onClick="document.getElementById('sbox-window').close();" class="button"/>
    </div>

    <input type="hidden" name="cid" value="<?php echo JFactory::getApplication()->input->get('article_id', null, 'int');?>"/>
    <input type="hidden" name="refresh" value="<?php echo JFactory::getApplication()->input->get('refresh', null, 'string');?>"/>
    <input type="hidden" name="task" value="save">
    <input type="hidden" name="option" value="com_cedtag">
    <?php echo JHTML::_('form.token'); ?>
</form>