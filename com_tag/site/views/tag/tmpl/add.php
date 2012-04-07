<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @version $Id: com_tag.php 599 2010-06-06 23:26:33Z you $
 * @author waltercedric.com, Joomlatags.org
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');
?>

<div align="center">
    <h1><?php if (isset($this->tags) && !empty($this->tags)) {
        echo(JText::_('EDIT TAGS'));
    } else {
        echo(JText::_('ADD TAGS'));
    }?></h1>
</div>
<form action="index.php" method="post" name="addTags" id="addTags">
    <div align="center">
        <textarea id="tags" name="tags" rows="5" cols="60"><?php echo($this->tags);?></textarea>
    </div>

    <div align="center">
        <input type="submit" name="Submit" value="<?php echo JText::_('SAVE');?>" class="button"/>
        <input type="button" name="cancel" value="<?php echo JText::_('CANCEL'); ?>"
               onClick="document.getElementById('sbox-window').close();" class="button"/>
    </div>

    <input type="hidden" name="cid" value="<?php echo JFactory::getApplication()->input->get('article_id', null, 'int');?>"/>
    <input type="hidden" name="refresh" value="<?php echo JFactory::getApplication()->input->get('refresh', null, 'string');?>"/>
    <input type="hidden" name="task" value="save">
    <input type="hidden" name="option" value="com_tag">
    <?php echo JHTML::_('form.token'); ?>
</form>