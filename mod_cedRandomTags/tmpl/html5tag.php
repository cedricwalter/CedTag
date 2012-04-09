<?php
/**
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
// no direct access
defined('_JEXEC') or die('Restricted access');

//http://www.goat1000.com/tagcanvas.php

$document =& JFactory::getDocument();
$document->addScript(JURI::base() . 'media/com_cedtag/js/tagcanvas.js');
$document->addScriptDeclaration("window.onload = function () {
        try {
            TagCanvas.Start('myCanvas', 'tags', {
                textColour:'#ff0000',
                outlineColour:'#ff00ff',
                reverse:true,
                depth:0.8,
                weight:true,
                maxSpeed:0.05
            });
        } catch (e) {
            // something went wrong, hide the canvas container
            document.getElementById('myCanvasContainer').style.display = 'none';
        }
    };");
?>
<div class="tagCloud<?php echo $moduleclass_sfx; ?>">
    <div id="myCanvasContainer">
        <canvas width="200" height="auto" id="myCanvas">
            <p>Anything in here will be replaced on browsers that support the canvas element</p>
        </canvas>
    </div>
    <div id="tags">
        <ul>
            <?php    foreach ($list as $item) { ?>
                <li><a
                    href="<?php echo $item->link; ?>"
                    rel="tag"
                    style="font-size: <?php echo $item->size; ?>%"
                    class="<?php echo $item->class; ?>"
                    title="<?php echo $item->ct; ?> items tagged with <?php echo $item->name; ?> | <?php echo $item->created; ?> | <?php echo $item->hits; ?> hits">
                    <?php echo $item->name; ?>
                </a></li>
            <?php }?>
        </ul>
    </div>
</div>