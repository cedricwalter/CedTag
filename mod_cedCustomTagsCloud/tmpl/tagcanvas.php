<?php
/**
 * @package module cedCustomTagsCloud for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_cedtag/helper/helper.php';

$uuid1 = "cedCustomTagsCloud1";//uniqid();
$uuid2 = "cedCustomTagsCloud2";//uniqid();
cedTagsHelper::addTagcanvasJavascript($uuid1, $uuid2);
?>
<?php if (isset($list) && !empty($list)) { ?>
<div class="tagCloud<?php echo $moduleclass_sfx; ?>">
    <div width="300" height="300" id="myCanvasContainer">
        <canvas id="<?php echo $uuid1; ?>">
            <p>Anything in here will be replaced on browsers that support the canvas element</p>
        </canvas>
    </div>
    <div id="<?php echo $uuid2; ?>">
        <ul>
            <?php    foreach ($list as $item) { ?>
            <li>
                <a href="<?php echo $item->link; ?>"
                   rel="tag"
                   font-size="<?php echo $item->size; ?>%"
                   target="_blank"
                   class="<?php echo $item->class; ?>"
                   title="<?php echo $item->frequency; ?> items tagged with <?php echo $item->name; ?> | <?php echo $item->hits; ?> hits"
                   alt="<?php echo $item->frequency; ?> items tagged with <?php echo $item->name; ?> | <?php echo $item->hits; ?> hits">
                    <?php echo $item->name; ?></a></li>
            <?php }?>
        </ul>
    </div>
</div><?php } ?>


