<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com 2010- http://www.joomlatags.org
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');


if (isset($this->term)) {
    $name = $this->term->name;
    $weight = $this->term->weight;
    $description = $this->term->description;
    $id = $this->term->id;
} else {
    $name = '';
    $weight = 0;
    $description = '';
    $id = false;
}
?>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
    <table width="75%" align="left">
        <tr>
            <td>
                <?php echo JText::_('TERM NAME');?>:
                <input style="margin: 0 2em;" class="inputbox" type="text" size="30" maxlength="100" name="name"
                       value="<?php echo $name;?>">

                <?php echo JText::_('WEIGHT');?>:
                <input class="inputbox" type="text" size="10" maxlength="10" name="weight"
                       value="<?php echo $weight;?>">
            </td>
        </tr>
        <tr>
            <td><?php echo JText::_('TERM DESCRIPTION');?>:</td>
        </tr>
        <tr>
            <td>
                <?php
                $params = array(
                            'smilies' => 0,
                            'style' => 0,
                            'layer' => 0,
                            'table' => 0,
                            'clear_entities' => 0
                        );

                echo $this->editor->display('description', $description, '100%', '400px', '150', '20', true, $params); ?>
            </td>
        </tr>

    </table>

    <input type="hidden" name="controller" value="term"/>
    <input type="hidden" name="task" value="save">
    <input type="hidden" name="id" value="<?php  echo  $id;?>">
    <input type="hidden" name="cid[]" value="<?php  echo  $id;?>">
    <input type="hidden" name="option" value="com_cedtag">
    <?php echo JHTML::_('form.token'); ?>
</form>
