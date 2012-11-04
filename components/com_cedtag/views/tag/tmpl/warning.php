<?php

/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_cedtag/helpers/themes.php';

$firstWarning = JRequest::getVar('FirstWarning', true);
$warning = JRequest::getVar('tagsWarning', 'FIRST_SAVE_WARNING');
if ($firstWarning) {
    $CedTagThemes = new CedTagThemes();
    $CedTagThemes->addCss();
    ?>

<div class="warning">
</div>
<h1><?php echo $this->tag; ?></h1>
<h2><?php echo JText::_($warning);?></h2>
<!-- CedTag Free Tagging system for Joomla by www.waltercedric.com -->

<?php
}
JFactory::getApplication()->input->set('FirstWarning', false);
?>
