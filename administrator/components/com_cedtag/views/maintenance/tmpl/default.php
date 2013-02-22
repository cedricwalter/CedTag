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
                    <select id="task" label="task" placeholder="<?php echo JText::_('Operation');?>"
                            name="task"
                            class=""
                            aria-invalid="false"
                            size="1" <?echo $disabled ?>>
                        <option value="" selected="selected"></option>
                        <!--
                            <option value="publish"><?php echo JText::_("Publish Tags having less than Z articles."); ?></option>
                        <option value="unpublish"><?php echo JText::_("Unpublish Tags having less than Z articles."); ?></option>
                            -->
                        <option value="replace"><?php echo JText::_("Replace Tag xxxx with Tag yyyy in all articles with Tag xxxx."); ?></option>
                        <option value="add"><?php echo JText::_("Add Tag yyyy to all articles which have also Tag xxxx."); ?></option>
                        <option value="remove"><?php echo JText::_("Remove Tag xxxx to all articles which have also Tag yyyy."); ?></option>
                    </select>

                    <p>
                    <ul class="adminformlist">
                        <!--
                        <li>
                            <input label="articles" type="text" name="articles" id="articles" value="" class="" <?echo $disabled ?>
                                   placeholder="<?php echo JText::_("Articles"); ?>">
                        </li> -->
                        <li>
                            <?php echo JText::_('Tag xxxx');?>
                        </li>

                        <li>
                            <input type="text"
                                   name="tagxxxx"
                                   id="tagxxxx"
                                   value=""
                                   placeholder="<?php echo JText::_('Tags xxxx');?>"
                                   class="" aria-invalid="false" <?echo $disabled ?>>
                        </li>
                        <li>
                            <?php echo JText::_('Tag yyyy');?>
                        </li>
                        <li>
                            <input class="inputbox"
                                   type="text"
                                   size="30" maxlength="100"
                                   placeholder="<?php echo JText::_('Tag yyyy');?>"
                                   name="tagyyyy"
                                   value="" <?echo $disabled ?>>
                            <input class="inputbox" type="text"
                                   size="30"
                                   maxlength="10"
                                   name="tagyyyyweigtht"
                                   placeholder="<?php echo JText::_('Tag yyyy weight (optional)');?>"
                                   value="" <?echo $disabled ?>>
                        </li>
                        <li>
                            <?php echo JText::_('Tag yyyy description (optional)');?>:
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