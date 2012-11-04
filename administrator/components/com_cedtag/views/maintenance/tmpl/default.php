<?php
/**
 * @package Component Tag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

$canDo = UsersHelper::getActions();
$coreCreate = $canDo->get('core.create');
$disabled = $coreCreate ? "" : "disabled";
?>

<form action="<?php echo JRoute::_('index.php?controller=maintenance&option=com_cedtag'); ?>" xmlns="http://www.w3.org/1999/html" method="post" name="adminForm"
      id="adminForm" autocomplete="off">
    <div class="width-100 fltlft">
        <fieldset class="adminform">
            <ul class="adminformlist">
                <li>
                    <?php if (!$coreCreate) { ?>
                    <p style="color:red;">
                        <?php echo JText::_("You do not have enough permissions: You need core.create permissions to be able to create/add/remove tags");?>
                    </p>
                    <?php } ?>

                    <?php echo JText::_('Operation');?>
                    <select id="task" name="task" class="" aria-invalid="false" size="1" <?echo $disabled ?>>
                        <option value="" selected="selected"></option>
                        <option value="replace">Replace Tag xxxx with Tag yyyy in all articles with Tag xxxx</option>
                        <option value="add">Add Tag yyyy to all articles which have also Tag xxxx</option>
                        <option value="remove">Remove Tag xxxx to all articles which have also Tag yyyy</option>
                    </select>

                    <p>

                    <h2><?php echo JText::_('Tags xxxx');?></h2></p>
                    <p>
                    <ul class="adminformlist">
                        <li>
                            <input type="text" name="tagxxxx" id="tagxxxx" value="" class="" aria-invalid="false" <?echo $disabled ?>>
                        </li>
                    </ul>
                    </p>

                    <p>
                    <h2><?php echo JText::_('Tags yyyy');?></h2></p>

                    <p>
                    <ul class="adminformlist">
                        <li>
                            <?php echo JText::_('Tag yyyy name');?>
                            <input style="margin: 0 2em;" class="inputbox" type="text" size="30" maxlength="100" name="tagyyyy"
                                   value="" <?echo $disabled ?>>
                            <?php echo JText::_('Tag yyyy weight (optional if tag already exist)');?>
                            <input class="inputbox" type="text" size="10" maxlength="10" name="tagyyyyweigtht"
                                   value="0" <?echo $disabled ?>>
                        </li>
                        <li>
                            <?php echo JText::_('Tad yyyy description (optional if tag already exist)');?>:
                            <?php
                            $params = array(
                                'smilies' => 0,
                                'style' => 1,
                                'layer' => 0,
                                'table' => 0,
                                'clear_entities' => 0
                            );
                            echo $this->editor->display('tagyyyydescription', "", '100%', '400px', '150', '20', true, $params); ?>
                        </li>
                    </ul>
                    </p>




                    <?php if ($coreCreate) { ?>
                    <input type="submit" name="execute" value="<?php echo JText::_('execute');?>" class="inputbox">
                    <?php
                }
                    ?>
                </li>
            </ul>
        </fieldset>
    </div>

    <input type="hidden" name="controller" value="maintenance">
    <?php echo JHTML::_('form.token'); ?>

</form>