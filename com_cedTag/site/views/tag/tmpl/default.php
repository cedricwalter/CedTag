<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

$tag = JFactory::getApplication()->input->get('tag', null, 'string');

$tagKeyword = JText::_('TAG:') . $tag;

$params = JComponentHelper::getParams('com_cedtag');
$topAds = $params->get('topAds');
$bottomAds = $params->get('bottomAds');
$showTagDescription = $params->get('description');
$config =& JFactory::getConfig();
?>
<h1><?php echo($tag);?></h1>

<table class="contentpaneopen" border="0" cellpadding="0"
       cellspacing="0" width="100%">
    <?php
    if (isset($showTagDescription) && $showTagDescription) {
        echo('<tr><td>' . $this->tagDescription . '</td></tr>');
    }
    if (isset($topAds) && $topAds) {
        echo('<tr><td>' . $topAds . '</td></tr>');
    }

    $count = $this->pagination->limitstart;
    if (isset($this->results) && !empty($this->results)) {
        require_once (JPATH_SITE . '/components/com_content/helpers/route.php');
        $odd = 0;
        foreach ($this->results as $result) {
            ?>
            <tr class="sectiontableentry<?php echo($odd + 1);?>">
                <td>
                    <div>
                        <span class="small">
                            <?php echo (++$count) . '. ';?>
                        </span>
                        <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($result->slug, $result->catslug)); ?>">
                            <?php echo $this->escape($result->title);?>
                        </a>
                    </div>
                </td>
            </tr>
            <?php
            $odd = 1 - $odd;
        }
    } ?>

    <?php
    if (isset($bottomAds) && $bottomAds) {
        echo('<tr><td>' . $bottomAds . '</td></tr>');
    }
    ?>
    <!-- Tags for Joomla by www.waltercedric.com -->
</table>
<div class="pagination">
    <p class="counter">
        <?php echo $this->pagination->getPagesCounter(); ?>
    </p>
    <?php echo $this->pagination->getPagesLinks(); ?>
</div>



<?php
$document =& JFactory::getDocument();
if ($this->tagDescription) {
    $document->setDescription(CedTagsHelper::truncate($this->tagDescription));
} else {
    $document->setDescription(CedTagsHelper::truncate($tag));
}
$document->setTitle($tag . ' | ' . $config->get('sitename'));
$document->setMetadata('keywords', $tag);
$document->addStyleSheet(JURI::base() . 'media/com_cedtag/css/tagcloud.css');
?>

