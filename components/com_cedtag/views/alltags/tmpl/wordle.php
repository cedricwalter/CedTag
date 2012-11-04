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
    <h1><?php echo JText::_('COM_CEDTAG_WORDLE_H1');?></h1>
    <h2><?php echo JText::_('COM_CEDTAG_WORDLE_H2');?></h2>
    <h3><?php echo JText::_('COM_CEDTAG_WORDLE_H3');?></h3>

    <div class="legend">
        <?php echo JText::_('COM_CEDTAG_WORDLE_TEXT');?>
    </div>
    <div class="cloud">
        <img usemap="#mymap" src="data:image/png;base64,<?php echo $this->img64 ?>" border="0"/>
        <map name="mymap">
            <?php foreach ($this->cloud->get_image_map() as $map): ?>
            <area shape="rect"
                  coords="<?php echo $map[1]->get_map_coords() ?>"
                  title="<?php echo $map[0] ?>"
                  href="<?php echo JRoute::_('index.php?option=com_cedtag&task=tag&tag=' . CedTagsHelper::urlTagname($map[0])); ?>"
                />
            <?php endforeach ?>
        </map>
    </div>
    <div class="copyright">
        <a href="http://www.waltercedric.com"
           style="font: normal normal normal 10px/normal arial; color: rgb(187, 187, 187); border-bottom-style: none; border-bottom-width: inherit; border-bottom-color: inherit; text-decoration: none; "
           onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" target="_blank"><b>cedTag</b></a>
    </div>
    <!-- CedTag Free Tagging system for Joomla by www.waltercedric.com -->
</div>
<?php } ?>