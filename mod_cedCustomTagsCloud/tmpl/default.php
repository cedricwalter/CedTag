<?php
/**
 * @package module cedCustomTagsCloud for Joomla! 2.5
 * @author waltercedric.com, Joomlatags.org
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<?php if (isset($list) && !empty($list)) { ?>
<div class="tagCloud<?php echo $moduleclass_sfx; ?>">
    <?php    foreach ($list as $item) { ?>
    <a
        href="<?php echo $item->link; ?>"
        rel="tag"
        class="<?php echo $item->class; ?>">
        <?php echo $item->name; ?>
    </a>
    <?php }?>
</div><?php } ?>
