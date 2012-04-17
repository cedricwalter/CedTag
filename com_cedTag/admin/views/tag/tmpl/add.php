<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

?>

<h1>Joomla cedtag</h1>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
    <table class="adminlist">
        <thead>
        <tr>
            <th><?php echo JText::_('TAGS');?></th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td><textarea id="tags" name="tags" rows="5" cols="60"><?php echo($this->tags);?></textarea></td>
        </tr>
        <tr>
            <td>
                <input type="submit" value="<?php echo JText::_('SAVE');?>"/>
                <input type="button" name="cancel" value="<?php echo JText::_('CANCEL'); ?>"
                       onClick="document.getElementById('sbox-window').close();"/>

            </td>
        </tr>

        </tbody>
    </table>
    <input type="hidden" name="cid" value="<?php echo strval(intval(JRequest::getString('article_id')))?>"/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="task" value="save">
    <input type="hidden" name="controller" value="cedtag">
    <input type="hidden" name="option" value="com_cedtag">
    <?php echo JHTML::_('form.token'); ?>
</form>