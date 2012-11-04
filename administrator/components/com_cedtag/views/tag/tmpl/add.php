<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_cedtag/helpers/suggest.php';
$id = strval(intval(JRequest::getString('article_id')));
$CedTagSuggest = new CedTagSuggest();
$CedTagSuggest->addAdminJs($this->tags, $id);

?>

<h1><?php echo JText::_('Joomla cedTag');?></h1>

<form method="post" name="adminForm" id="adminForm">
    <?php
    echo JText::_('You type tags and when you hit Enter, Comma or Space they will become a little nice formatted box with the value. You can click on the boxes to remove them (a little x on the corner).');
    echo JText::_('You can also remove already typed tags with Backspace.');
    echo JText::_('There is also a autocomplete, so if you type, say "jav" it will appear java and javascript (for example).');
    ?>
    <div class="clr"></div>

    <ul id="tags<?php echo $id; ?>" style="width: 500px;"></ul>
    <div class="clr"></div>
    <input type="button" name="cancel" value="<?php echo JText::_('CANCEL'); ?>"
           onClick="document.getElementById('sbox-window').close();"/>

    <?php echo JHTML::_('form.token'); ?>

</form>
