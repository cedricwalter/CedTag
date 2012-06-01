<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');
require_once (JPATH_SITE . '/components/com_content/helpers/route.php');
require_once JPATH_COMPONENT_SITE . '/helper/themes.php';

$tag = JRequest::getVar('tag', null);

$tagKeyword = JText::_('Posts Tagged ').JText::_('‘') . $tag.JText::_('’');
$config = JFactory::getConfig();

$params = JComponentHelper::getParams('com_cedtag');
$comContentParams = JComponentHelper::getParams('com_content');

$ads_top_use = $params->get('ads_top_use');
$ads_bottom_use = $params->get('ads_bottom_use');
$showTagDescription = $params->get('description');

function readmore($item, $user)
{
   // if ($item->access <= $user->get('aid', 0)) {
        //$item->readmore_link = JRoute::_('index.php?view=article&catid='.$this->category->slug.'&id='.$item->slug);
        $item->readmore_link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
        $item->readmore_register = false;
    /*}
    else
    {
        $item->readmore_link = JRoute::_('index.php?option=com_user&view=login');
        $returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug), false);
        $fullURL = new JURI($item->readmore_link);
        $fullURL->setVar('return', base64_encode($returnURL));
        $item->readmore_link = $fullURL->toString();
        $item->readmore_register = true;
    }*/
    return $item;
}

?>

<h1><?php echo JText::_('Posts Tagged')." ".JText::_('‘') . $tag.JText::_('’') ?></h1>

<table class="blog" cellpadding="0" cellspacing="0">
    <tbody>

    <?php
    if (isset($showTagDescription) && $showTagDescription) {
        echo('<tr><td>' . $this->tagDescription . '</td></tr>');
    }
    if (isset($ads_top_use) && $ads_top_use) {
        $ads_top_content = $params->get('ads_top_content');
        echo('<tr><td>' . $ads_top_content . '</td></tr>');
    }
    ?>
    <tr>

        <td valign="top"><?php
            $count = $this->pagination->limitstart;
            if (isset($this->results) && !empty($this->results)) {
                foreach ($this->results as $item) {
                    $this->item = &$item;
                    echo $this->loadTemplate('item');
                }
            } ?></td>
    </tr>
    <tr>
        <?php
        if (isset($ads_bottom_use) && $ads_bottom_use) {
            $ads_bottom_content = $params->get('ads_bottom_content');
            echo('<tr><td>' . ads_bottom_content . '</td></tr>');
        }
        ?>
        <td>
            <div align="center"><?php echo $this->pagination->getPagesLinks(); ?>
            </div>
        </td>
    </tr>
    <!-- Tags for Joomla by www.waltercedric.com -->
    </tbody>

</table>
<?php if (($comContentParams->def('show_pagination', 1) == 1  || ($comContentParams->get('show_pagination') == 2)) && ($comContentParams->get('pages.total') > 1)) : ?>
		<div class="pagination">
						<?php  if ($comContentParams->def('show_pagination_results', 1)) : ?>
						<p class="counter">
								<?php echo $this->pagination->getPagesCounter(); ?>
						</p>

				<?php endif; ?>
				<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
<?php  endif; ?>


<?php
$document = JFactory::getDocument();
$cedTagsHelper = new CedTagsHelper();
if ($this->tagDescription) {
    $document->setDescription($cedTagsHelper->truncate($this->tagDescription));
} else {
    $document->setDescription($cedTagsHelper->truncate($tag));
}
$document->setTitle( JText::_('Items tagged with ') . $tag . ' | ' . $config->getValue('sitename'));
$document->setMetadata('keywords', $tag);
$CedTagThemes = new CedTagThemes();
$CedTagThemes->addCss();


?>
