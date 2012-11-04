<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE . '/components/com_content/helpers/route.php');
require_once JPATH_SITE . '/components/com_cedtag/helpers/themes.php';

// to reuse components/com_content/views/category/tmpl/blog_item.php
// when doing later $this->loadTemplate('item');
$this->addTemplatePath(JPATH_SITE . '/components/com_content/views/category/tmpl');
JHtml::addIncludePath(JPATH_SITE . '/components/com_cedtag/helpers');
//blog_item.php use icon.php
JHtml::addIncludePath(JPATH_SITE . '/components/com_content/helpers');

?>

<h1><?php echo JText::_('Posts Tagged') . " " . JText::_('\'') . $this->tag . JText::_('\'') ?></h1>

<?php if (isset($this->showDescription) && $this->showDescription && ($this->tagDescription != null)) { ?>
    <div class="category-desc">
       <?php echo $this->tagDescription; ?>
    </div>
<?php
}
?>

<?php if (isset($this->ads_top_use) && $this->ads_top_use) {
    echo $this->ads_top_content;
}
?>

<?php
if (isset($this->results) && !empty($this->results)) {
    ?>
    <ul>
    <?php
    foreach ($this->results as $item) {
        $this->item = &$item;
        echo $this->loadTemplate('item');
    }
    ?>
    </ul>
    <?php
} ?>

<?php
if (isset($this->ads_bottom_use) && $this->ads_bottom_use) { ?>
    <div class="bottomads">
        <?php echo $this->ads_bottom_content; ?>
    </div>
<?php
}
?>

<?php if (($this->show_pagination == 1  || ($this->show_pagination == 2)) && ($this->pagination->get('pages.total') > 1)) { ?>
    <div class="pagination">
        <?php if ($this->show_pagination_results) { ?>
            <p class="counter">
                <?php echo $this->pagination->getPagesCounter(); ?>
            </p>
        <?php } ?>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php } ?>
<!-- CedTag Free Tagging system for Joomla by www.waltercedric.com -->

<?php
    $document = JFactory::getDocument();
    $cedTagsHelper = new CedTagsHelper();
    if ($this->tagDescription) {
        $document->setDescription($cedTagsHelper->truncate($this->tagDescription));
    } else {
        $document->setDescription($cedTagsHelper->truncate($this->tag));
    }

    $config = JFactory::getConfig();
    $document->setTitle(JText::_('Items tagged with ') . $this->tag . ' | ' . $config->getValue('sitename'));
    $document->setMetadata('keywords', $this->tag);
    $CedTagThemes = new CedTagThemes();
    $CedTagThemes->addCss();
?>
