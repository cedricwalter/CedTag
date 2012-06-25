<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

// userhelper for acl
require_once JPATH_SITE . '/administrator/components/com_users/helpers/users.php';
$canDo = UsersHelper::getActions();

// Load the javascript
JHtml::_('behavior.framework');
JHtml::_('behavior.modal', 'a.modal');

require_once JPATH_COMPONENT_SITE . '/helper/helper.php';
?>

<div class="tagpanel">

    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=tag"
                             title="<?php echo JText::_('Articles Manager');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/article.png"
            alt="<?php echo JText::_('Articles Manager');?>"/> <span><?php echo JText::_('Articles　Manager');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=term"
                             title="<?php echo JText::_('Tag Manager');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/tag.png"
            alt="<?php echo JText::_('TERM MANAGER');?>"/>
            <span><?php echo JText::_('Tag Manager');?></span></a></div>
    </div>

    <?php if ($canDo->get('core.admin')) { ?>
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
    <?php } ?>

    <?php if ($canDo->get('core.create')) { ?>
    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=css"
                             title="<?php echo JText::_('TEMPLATE MANAGER');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/css.png"
            alt="<?php echo JText::_('TEMPLATE MANAGER');?>"/>
            <span><?php echo JText::_('TEMPLATE MANAGER');?></span></a></div>
    </div>

    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=stopwords"
                             title="<?php echo JText::_('STOPWORDS');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/gear_forbidden.png"
            alt="<?php echo JText::_('STOPWORDS');?>"/>
            <span><?php echo JText::_('STOPWORDS');?></span></a></div>
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
            <img src="<? echo JURI::root() ?>/media/com_cedtag/images/export.png"/>
            <span><?php echo JText::_('EXPORT TAGS');?></span></a></div>
    </div>
    <?php } ?>
    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=statistics"
                             title="<?php echo JText::_('statistics');?>">
            <img src="<? echo JURI::root() ?>/media/com_cedtag/images/statistics.png"/>
            <span><?php echo JText::_('statistics');?></span></a></div>
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
    <div style="float: left;">
        <div class="icon">
            <a href="http://extensions.joomla.org/extensions/search-a-indexing/tags-a-clouds/20423"
               target="_blank"
               title="<?php echo JText::_('EXTENSIONS VOTE');?>"> <img
                src="<? echo JURI::root() ?>/media/com_cedtag/images/jed.png"/>
                <span><?php echo JText::_('EXTENSIONS VOTE');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon">
            <a href="http://extensions.joomla.org/extensions/owner/cedric_walter"
               target="_blank"
               title="<?php echo JText::_('Other Extensions By the Same Author');?>"> <img
                src="<? echo JURI::root() ?>/media/com_cedtag/images/jed.png"/>
                <span><?php echo JText::_('OTHER EXTENSIONS');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon">
            <a href="http://www.waltercedric.com/downloads/cedtags.html"
               target="_blank"
               title="<?php echo JText::_('Download Latest Version');?>"> <img
                src="<? echo JURI::root() ?>/media/com_cedtag/images/download.png"/>
                <span><?php echo JText::_('DOWNLOAD LATEST VERSION');?></span></a>
        </div>
    </div>
    <div style="float: left;">
            <div class="icon">
                <a href="index.php?option=com_cedtag&controller=diagnostic"
                    title="<?php echo JText::_('DIAGNOSTIC');?>"> <img
                    src="<? echo JURI::root() ?>/media/com_cedtag/images/diagnostic.png"/>
                    <span><?php echo JText::_('DIAGNOSTIC');?></span></a>
            </div>
    </div>
    <div style="float: left;">
        <div class="icon">
            <a href="https://www.facebook.com/pages/C%C3%A9dric-Walter-dot-com/113977625305022"
               target="_blank"
               title="<?php echo JText::_('Like waltercedric.com.co on Facebook');?>"> <img
                src="<? echo JURI::root() ?>/media/com_cedtag/images/facebook.png"/>
                <span><?php echo JText::_('Like Us on Facebook');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon">
            <a href="http://twitter.com/cedricwalter"
               target="_blank"
               title="<?php echo JText::_('Follow Me on Twitter, get the latest development news');?>"> <img
                src="<? echo JURI::root() ?>/media/com_cedtag/images/twitter.png"/>
                <span><?php echo JText::_('Follow Me on Twitter');?></span></a>
        </div>
    </div>



</div>

<div class="tagversion">
    <h1><img src="<?php echo JURI::root() ?>media/com_cedtag/images/tag_logo48.png"/>CedTag</h1>

    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="hosted_button_id" value="QH6UGCKWW9KVL">
    <input type="image" src="<? echo JURI::root() ?>/media/com_cedtag/images/paypal-donate.jpg" title="Thanks you for donations to waltercedric.com Joomla extensions development" name="submit" alt="PayPal - The safer, easier way to pay online!" style="width:174px;height:153px;">
    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>


    <p>
        &copy; 2012 <a href="http://www.waltercedric.com">www.waltercedric.com</a> GNU-GPL v3.0<br/>
        &copy; 2011 <a href="http://www.dreamcraft.ch">www.dreamcraft.ch</a> MIT for WordCloud<br/>
        &copy; 2010 <a href="http://www.joomlatags.org">www.joomlatags.org</a> GNU-GPL v2.0<br/>
    </p>
</div>