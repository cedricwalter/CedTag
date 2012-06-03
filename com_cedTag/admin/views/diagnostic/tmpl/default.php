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

require_once JPATH_COMPONENT_SITE . '/helper/helper.php';
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
        <tr>
            <td><?php echo JText::_('Curl') ?></td>
            <td><?php echo JText::_('Optionnal') ?></td>
            <td style="background-color: <?php echo $this->diagnostic->curl ? 'green;' : 'red;' ?>"><?php echo $this->diagnostic->curl ? JText::_('JYES') : JText::_('JNO') ?></td>
            <td><?php echo JText::_('JYES') ?></td>
            <td><?php echo JText::_('WikiPedia import of terms descriptions') ?></td>
            <td><?php echo $this->diagnostic->curl == 0 ? JText::_('Required Root access or contacting your hosting company to activate cURL. cURL is a computer software project providing a library and command-line tool for transferring data using various protocols. The cURL project produces two products, libcurl and cURL. It was first released in 1997.') : '' ?></td>
        </tr>

    </table>


</div>