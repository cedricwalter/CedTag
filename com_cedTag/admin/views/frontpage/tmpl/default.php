<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

// Load the javascript
JHtml::_('behavior.framework');
JHtml::_('behavior.modal', 'a.modal');

require_once JPATH_COMPONENT_SITE . '/helper/helper.php';
?>

<div class="tagpanel">

    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=tag"
                             title="<?php echo JText::_('TAG　MANAGER');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/tag.png"
            alt="<?php echo JText::_('TAG　MANAGER');?>"/> <span><?php echo JText::_('TAG　MANAGER');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=term"
                             title="<?php echo JText::_('TERM MANAGER');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/term.png"
            alt="<?php echo JText::_('TERM MANAGER');?>"/>
            <span><?php echo JText::_('TERM MANAGER');?></span></a></div>
    </div>

    <div style="float: left;">
        <div class="icon">
            <a class="modal"
               rel="{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}"
               href="index.php?option=com_config&view=component&component=com_cedtag&path=&tmpl=component"
               title="<?php echo JText::_('CONFIGURATION FOR CedTags');?>"> <img
                src="<? echo JURI::root() ?>/media/com_cedtag/images/config.png"
                alt="<?php echo JText::_('CONFIGURATION');?>"/>
                <span><?php echo JText::_('CONFIGURATION');?></span></a></div>
    </div>

    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=css"
                             title="<?php echo JText::_('TEMPLATE MANAGER');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/template.png"
            alt="<?php echo JText::_('TEMPLATE MANAGER');?>"/>
            <span><?php echo JText::_('TEMPLATE MANAGER');?></span></a></div>
    </div>


    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=import"
                             title="<?php echo JText::_('IMPORT TAGS FROM OTHER COMPONENTS');?>">
            <img src="<? echo JURI::root() ?>/media/com_cedtag/images/import.png"/>
            <span><?php echo JText::_('IMPORT TAGS');?></span></a></div>
    </div>

    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=export"
                             title="<?php echo JText::_('EXPORT TAGS TO OTHER COMPONENTS');?>">
            <img src="<? echo JURI::root() ?>/media/com_cedtag/images/import.png"/>
            <span><?php echo JText::_('EXPORT TAGS');?></span></a></div>
    </div>


    <div style="float: left;">
        <div class="icon"><a href="http://www.waltercedric.com" target="_blank"
                             title="<?php echo JText::_('CedTags HOME PAGE');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/frontpage.png"/>
            <span><?php echo JText::_('HOME PAGE');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon"><a
            href="http://wiki.waltercedric.com/index.php?title=Taxonomies_and_Tags_support_for_Joomla"
            target="_blank"
            title="<?php echo JText::_('CedTags MANUAL');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/manual.png"/>
            <span><?php echo JText::_('MANUAL');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon"><a
            href="http://forums.waltercedric.com"
            target="_blank"
            title="<?php echo JText::_('CedTags FORUM');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/forum.png"/>
            <span><?php echo JText::_('FORUM');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon"><a
            href="http://www.gnu.org/copyleft/gpl.html"
            target="_blank"
            title="<?php echo JText::_('LICENSE');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/license.png"/>
            <span><?php echo JText::_('LICENSE');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon">
            <a href="skype:cedric.walter?call"
               title="<?php echo JText::_('SKYPE ME');?>"> <img
                src="<? echo JURI::root() ?>/media/com_cedtag/images/skype.png"/>
                <span><?php echo JText::_('SKYPE ME');?></span></a>
        </div>
    </div>
</div>

<div class="tagversion">

    <p><a href="TODO" target="_blank">Joomla
        cedTag</a> v<?php echo(CedTagsHelper::getComponentVersion());?>
    </p>

    <p>

    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="48HP9A7JU7BVS">
        <img src="<? echo JURI::root() ?>/media/com_cedtag/images/paypal-donate.jpg"
             width="174px" heght="153px"
             border="0" name="submit" title="PayPal - The safer, easier way to pay online!"/>
        <img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1"
             height="1">
    </form>
    </p>

    <p>
        <?php echo JText::_('VOTE');?>
        <a target="_blank"
           href="TODO">Joomla
            Extensions Directory</a>
    </p>

    <p>
        &copy; 2012 <a href="www.waltercedric.com">www.waltercedric.com</a>
    </p>
</div>