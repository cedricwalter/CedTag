<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

$params = JComponentHelper::getParams('com_cedtag');
$comContentParams = JComponentHelper::getParams('com_content');

$showMeta = $params->get('contentMeta', '1');

$readmore = $params->get('onlyIntro');
$readmore = $readmore && $this->item->readmore;
if ($readmore) {
    $user =& JFactory::getUser();
    $result = readmore($this->item, $user);
    $result->text =& $this->item->introtext;
} else {
    $result->text = $this->item->introtext . $this->item->fulltext;
    $result->readmore_link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catslug));
}
?>

<div>
    <div class="contentpaneopen">
        <h2 class="contentheading">
            <a href="<?php echo $result->readmore_link; ?>"
               class="contentpagetitle"> <?php echo $this->escape($this->item->title);?>
            </a>
        </h2>
    </div>


    <?php if ($comContentParams->get('show_category_title', 1) or $comContentParams->get('page_subheading')) : ?>
   	<h2>
   		<?php echo $this->escape($comContentParams->get('page_subheading')); ?>
   		<?php if ($comContentParams->get('show_category_title')) : ?>
   			<span class="subheading-category"><?php echo $this->category->title;?></span>
   		<?php endif; ?>
   	</h2>
   	<?php endif; ?>

    <?php if ($showMeta) { ?>
    <div class="article-tools">
        <div class="article-meta">
            <span class="createdate">
                <?php echo JHTML::_('date', $this->item->created, JText::_('DATE_FORMAT_LC1')); ?></span>
            <span class="createby">
                <?php JText::_('Written by');
                $author = $this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author;
                echo(' ' . $author);
                ?>
            </span>
        </div>
    </div>
    <?php };?>

    <div class="article-content">
        <?php echo $result->text; ?>
    </div>

    <?php if ($readmore) {
    //read more
    ?>
    <a href="<?php echo $result->readmore_link; ?>"
       class="readon"> <?php if ($result->readmore_register) {
        echo JText::_('Register to read more...');
    } else {
        echo JText::sprintf('Read more...');
    } ?>
    </a>
    <?php }?>
    <div class="item-separator"></div>
</div>
