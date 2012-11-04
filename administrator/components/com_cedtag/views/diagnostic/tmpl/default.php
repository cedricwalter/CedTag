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

    <table width="100%" border="0" cellpadding="5">
        <thead>
        <tr>
            <th style="text-align:left;">Item</th>
            <th style="text-align:left;">Optionnal/Required</th>
            <th style="text-align:left;">Status</th>
            <th style="text-align:left;">Recommended Value</th>
            <th style="text-align:left;">Used by</th>
            <th style="text-align:left;">Resolution</th>
        </tr>

        </thead>
        <?php foreach ($this->diagnostics as $diagnostic) { ?>
        <tr>
            <td><?php echo $diagnostic->title ?></td>
            <td><?php echo $diagnostic->optionnal ?></td>
            <td style="background-color: <?php echo $diagnostic->color ?>"><?php echo $diagnostic->status ?></td>
            <td><?php echo $diagnostic->recommendedValue ?></td>
            <td><?php echo $diagnostic->usedBy ?></td>
            <td><?php echo $diagnostic->resolution ?></td>
        </tr>
        <?php } ?>

    </table>


</div>