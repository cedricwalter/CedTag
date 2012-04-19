<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');
?>
<?php if (isset($this->cloud) && !empty($this->cloud)) { ?>
<div class="wordle">
    <div class="title">
        <?php echo JText::_('All Tags');?>
    </div>
    <div class="legend">
        <?php echo JText::_('A tag is like a subject or category. This page shows all tags in weighted order. The bigger the text, the more active the tag is.');?>
    </div>
    <div class="cloud">
        <img usemap="#mymap" src="data:image/png;base64,<?php echo $this->img64 ?>" border="0"/>
        <map name="mymap">
            <?php foreach ($this->cloud->get_image_map() as $map): ?>
            <area shape="rect" coords="<?php echo $map[1]->get_map_coords() ?>"
                  href="<?php echo $map[3] ?>"/>
            <?php endforeach ?>
        </map>
    </div>
    <div class="copyright">
        <a href="http://www.waltercedric.com"
           style="font: normal normal normal 10px/normal arial; color: rgb(187, 187, 187); border-bottom-style: none; border-bottom-width: initial; border-bottom-color: initial; text-decoration: none; "
           onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" target="_blank"><b>cedTag</b></a>
    </div>
</div>
<?php } ?>