<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

?>

<form action="index.php" method="post" name="adminForm" id="adminForm"
      class="adminForm">
    <table>

        <tr>
            <td colspan="5"><?php echo JText::_('Batch add terms, seperator with comma.');?></td>
        </tr>
        <tr>
            <td colspan="5"><textarea id="names" name="names" rows="5" cols="60"></textarea></td>
        </tr>

    </table>

    <input type="hidden" name="task" value="batchsave">
    <input type="hidden" name="controller" value="term">
    <input type="hidden" name="option" value="com_cedtag">
    <?php echo JHTML::_('form.token'); ?>
</form>
