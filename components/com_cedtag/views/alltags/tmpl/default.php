<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_cedtag/helpers/helper.php';
require_once JPATH_SITE . '/components/com_cedtag/helpers/themes.php';

$CedTagThemes = new CedTagThemes();
$CedTagThemes->addCss();
?>
<?php if (isset($this->list) && !empty($this->list)) {   ?>
<div class="alltags">
    <div class="title">
        <?php echo JText::_('All Tags');?>
    </div>
    <div class="legend">
        <?php echo JText::_('A tag is like a subject or category. This page shows all tags in weighted order. The bigger the text, the more active the tag is.');?>
    </div>

    <div class="cloud">
        <?php foreach ($this->list as $item) { ?>
        <a  href="<?php echo $item->link; ?>" rel="tag"
            style="font-size: <?php echo $item->size; ?>%;"
            class="<?php echo $item->class; ?>"
            title="<?php echo $item->frequency; ?> items tagged with <?php echo $item->name; ?> | <?php echo $item->hits; ?> hits">
            <?php echo $item->name; ?></a>
        <?php }?>
    </div>
    <div class="copyright">
        <a href="http://www.waltercedric.com"
           style="font: normal normal normal 10px/normal arial; color: rgb(187, 187, 187); border-bottom-style: none; border-bottom-width: inherit; border-bottom-color: inherit; text-decoration: none; "
           onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" target="_blank"><b>cedTag</b></a>
    </div>

    <!-- CedTag Free Tagging system for Joomla by www.waltercedric.com -->
</div>
<?php } ?>


