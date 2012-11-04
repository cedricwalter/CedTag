<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

// userhelper for acl
require_once JPATH_SITE . '/administrator/components/com_users/helpers/users.php';
$canDo = UsersHelper::getActions();

// Load the javascript
JHtml::_('behavior.framework');
JHtml::_('behavior.modal', 'a.modal');

require_once JPATH_SITE . '/components/com_cedtag/helpers/helper.php';
?>

<div class="tagpanel">
<!--
    <div style="float: left;">
            <div class="icon"><a href="index.php?option=com_cedtag&controller=diagnostic&task=toggle"
                                 title="<?php echo JText::_('Articles Manager');?>"> <img
                src="<? echo JURI::root() ?>/media/com_cedtag/images/on.png"
                alt="<?php echo JText::_('Switch Off');?>"/> <span><?php echo JText::_('Switch Off');?></span></a>
            </div>
    </div> -->

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
            alt="<?php echo JText::_('Tag Manager');?>"/>
            <span><?php echo JText::_('Tag Manager');?></span></a></div>
    </div>

    <?php if ($canDo->get('core.admin')) { ?>
    <div style="float: left;">
        <div class="icon">
            <a class="modal"
               rel="{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}"
               href="index.php?option=com_config&view=component&component=com_cedtag&path=&tmpl=component"
               title="<?php echo JText::_('Configuration');?>"> <img
                src="<? echo JURI::root() ?>/media/com_cedtag/images/config.png"
                alt="<?php echo JText::_('Configuration');?>"/>
                <span><?php echo JText::_('Configuration');?></span></a></div>
    </div>
    <?php } ?>

    <?php if ($canDo->get('core.create')) { ?>
    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=css"
                             title="<?php echo JText::_('Template Manager');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/css.png"
            alt="<?php echo JText::_('Template Manager');?>"/>
            <span><?php echo JText::_('Template Manager');?></span></a></div>
    </div>

    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=stopwords"
                             title="<?php echo JText::_('Stopwords');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/gear_forbidden.png"
            alt="<?php echo JText::_('Stopwords');?>"/>
            <span><?php echo JText::_('Stopwords');?></span></a></div>
    </div>
    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=import"
                             title="<?php echo JText::_('Import Tags From Other Components');?>">
            <img src="<? echo JURI::root() ?>/media/com_cedtag/images/import.png"/>
            <span><?php echo JText::_('Import Tags');?></span></a></div>
    </div>
    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=export"
                             title="<?php echo JText::_('Export Tags From Other Components');?>">
            <img src="<? echo JURI::root() ?>/media/com_cedtag/images/export.png"/>
            <span><?php echo JText::_('Export Tags');?></span></a></div>
    </div>
        <!--
    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=maintenance"
                             title="<?php echo JText::_('Maintenance');?>">
            <img src="<? echo JURI::root() ?>/media/com_cedtag/images/maintenance.png"/>
            <span><?php echo JText::_('Maintenance');?></span></a></div>
    </div> -->


    <?php } ?>
    <div style="float: left;">
        <div class="icon"><a href="index.php?option=com_cedtag&controller=statistics"
                             title="<?php echo JText::_('statistics');?>">
            <img src="<? echo JURI::root() ?>/media/com_cedtag/images/statistics.png"/>
            <span><?php echo JText::_('statistics');?></span></a></div>
    </div>
    <div style="float: left;">
        <div class="icon"><a href="http://www.waltercedric.com" target="_blank"
                             title="<?php echo JText::_('Home Page');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/frontpage.png"/>
            <span><?php echo JText::_('Home Page');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon"><a
            href="http://wiki.waltercedric.com/index.php?title=Taxonomies_and_Tags_support_for_Joomla"
            target="_blank"
            title="<?php echo JText::_('Manual');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/manual.png"/>
            <span><?php echo JText::_('Manual');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon"><a
            href="http://forums.waltercedric.com"
            target="_blank"
            title="<?php echo JText::_('Forums');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/forum.png"/>
            <span><?php echo JText::_('Forums');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon"><a
            href="http://www.gnu.org/copyleft/gpl.html"
            target="_blank"
            title="<?php echo JText::_('License');?>"> <img
            src="<? echo JURI::root() ?>/media/com_cedtag/images/license.png"/>
            <span><?php echo JText::_('License');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon">
            <a href="skype:cedric.walter?call"
               title="<?php echo JText::_('Skype me');?>"> <img
                src="<? echo JURI::root() ?>/media/com_cedtag/images/skype.png"/>
                <span><?php echo JText::_('Skype me');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon">
            <a href="http://extensions.joomla.org/extensions/search-a-indexing/tags-a-clouds/20423"
               target="_blank"
               title="<?php echo JText::_('Extensions Vote');?>"> <img
                src="<? echo JURI::root() ?>/media/com_cedtag/images/jed.png"/>
                <span><?php echo JText::_('Extensions Vote');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon">
            <a href="http://extensions.joomla.org/extensions/owner/cedric_walter"
               target="_blank"
               title="<?php echo JText::_('Other Extensions By the Same Author');?>"> <img
                src="<? echo JURI::root() ?>/media/com_cedtag/images/jed.png"/>
                <span><?php echo JText::_('Other Extensions');?></span></a>
        </div>
    </div>
    <div style="float: left;">
        <div class="icon">
            <a href="http://www.waltercedric.com/downloads/cedtags.html"
               target="_blank"
               title="<?php echo JText::_('Download Latest Version');?>"> <img
                src="<? echo JURI::root() ?>/media/com_cedtag/images/download.png"/>
                <span><?php echo JText::_('Download Latest Version');?></span></a>
        </div>
    </div>
    <div style="float: left;">
            <div class="icon">
                <a href="index.php?option=com_cedtag&controller=diagnostic"
                    title="<?php echo JText::_('Diagnostic');?>"> <img
                    src="<? echo JURI::root() ?>/media/com_cedtag/images/diagnostic.png"/>
                    <span><?php echo JText::_('Diagnostic');?></span></a>
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
    <div style="float: left;">
        <div class="icon">
            <a href="https://plus.google.com/u/0/104558366166000378462"
               target="_blank"
               title="<?php echo JText::_('Follow Me on Google+');?>"> <img
                src="<? echo JURI::root() ?>/media/com_cedtag/images/google.png"/>
                <span><?php echo JText::_('Follow Me on Google+');?></span></a>
        </div>
    </div>


</div>

<div class="tagversion">
    <h1><img src="<?php echo JURI::root() ?>media/com_cedtag/images/tag_logo48.png"/>CedTag <?php echo CedTagsHelper::getComponentVersion(); ?></h1>

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