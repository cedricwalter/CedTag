<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');
require_once (JPATH_SITE . '/components/com_content/helpers/route.php');
$tag = JRequest::getVar('tag', null);

$tagKeyword = JText::_('Posts Tagged ').JText::_('‘') . $tag.JText::_('’');
$config =& JFactory::getConfig();

$params = JComponentHelper::getParams('com_cedtag');
$topAds = $params->get('topAds');
$bottomAds = $params->get('bottomAds');
$showTagDescription = $params->get('description');
$showMeta = $params->get('contentMeta', '1');
$user =& JFactory::getUser();

function readmore($item, $user)
{
    if ($item->access <= $user->get('aid', 0)) {
        //$item->readmore_link = JRoute::_('index.php?view=article&catid='.$this->category->slug.'&id='.$item->slug);
        $item->readmore_link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
        $item->readmore_register = false;
    }
    else
    {
        $item->readmore_link = JRoute::_('index.php?option=com_user&view=login');
        $returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug), false);
        $fullURL = new JURI($item->readmore_link);
        $fullURL->setVar('return', base64_encode($returnURL));
        $item->readmore_link = $fullURL->toString();
        $item->readmore_register = true;
    }
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
    if (isset($topAds) && $topAds) {
        echo('<tr><td>' . $topAds . '</td></tr>');
    }
    ?>
    <tr>

        <td valign="top"><?php
            $count = $this->pagination->limitstart;
            if (isset($this->results) && !empty($this->results)) {
                foreach ($this->results as $result) {
                    $readmore = $params->get('onlyIntro');
                    $readmore = $readmore && $result->readmore;
                    //echo($readmore);
                    if ($readmore) {
                        $result = readmore($result, $user);
                        $result->text =& $result->introtext;
                    } else {
                        $result->text = $result->introtext . $result->fulltext;
                        $result->readmore_link = JRoute::_(ContentHelperRoute::getArticleRoute($result->slug, $result->catslug));
                    }
                    ?>

                    <div>
                        <div class="contentpaneopen">
                            <h2 class="contentheading">
                                <a href="<?php echo $result->readmore_link; ?>"
                                   class="contentpagetitle"> <?php echo $this->escape($result->title);?>
                                </a>
                            </h2>
                        </div>

                        <?php if ($showMeta) { ?>
                        <div class="article-tools">
                            <div class="article-meta">
                                    <span class="createdate">
                                        <?php echo JHTML::_('date', $result->created, JText::_('DATE_FORMAT_LC1')); ?></span>
                                    <span class="createby">
                                        <?php JText::_('Written by');
                                        $author = $result->created_by_alias ? $result->created_by_alias : $result->author;
                                        echo(' ' . $author);
                                        ?>
                                    </span>
                            </div>
                        </div>
                        <?php };?>

                        <div class="article-content">
                            <?php echo $result->text; ?>

                            <?php
                              //echo $this->loadTemplate('item');
                            ?>

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
                        <span class="article_separator">&nbsp;</span>
                    </div>
                    <?php
                }
            } ?></td>
    </tr>
    <tr>
        <?php
        if (isset($bottomAds) && $bottomAds) {
            echo('<tr><td>' . $bottomAds . '</td></tr>');
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

<?php
$document =& JFactory::getDocument();
if ($this->tagDescription) {
    $document->setDescription(CedTagsHelper::truncate($this->tagDescription));
} else {
    $document->setDescription(CedTagsHelper::truncate($tag));
}
$document->setTitle( JText::_('Items tagged with ') . $tag . ' | ' . $config->getValue('sitename'));
$document->setMetadata('keywords', $tag);
$document->addStyleSheet(JURI::base() . 'media/com_cedtag/css/tagcloud.css');
?>
