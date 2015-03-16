<div id="item_<?php echo $type_key; ?>_<?php echo $type->getID(); ?>" class="greybox" style="margin: 5px 0 0 0; position: relative;">
    <div style="position: absolute; right: 5px; top: 30px;">
        <button class="button button-silver dropper" id="<?php echo $type_key; ?>_<?php echo $type->getID(); ?>_more_actions"><?php echo __('Actions'); ?></button>
        <ul id="<?php echo $type_key; ?>_<?php echo $type->getID(); ?>_more_actions_dropdown" style="font-size: 1.1em; width: 200px; top: 23px; margin-top: 0; text-align: right; z-index: 1000;" class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();">
            <li>
                <a href="javascript:void(0);" onclick="$('edit_custom_type_<?php echo $type_key; ?>_form').toggle();$('custom_type_<?php echo $type_key; ?>_info').toggle();"><?php echo __('Edit this issue field'); ?></a>
            </li>
            <?php
                if ($type->hasCustomOptions()) {
            ?>
                <li><a href="javascript:void(0);" onclick="TBG.Config.Issuefields.Options.show('<?php echo make_url('configure_issuefields_getoptions', array('type' => $type_key)); ?>', '<?php echo $type_key; ?>');"><?php echo __('Show and edit available choices'); ?></a></li>
            <?php
                }
            ?>
            <li>
                <?php echo javascript_link_tag(__('Delete'), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to delete this issue field?')."', '".__('This will also remove the value of this issue field from all issues, along with any possible options this field can have.')."', {yes: {click: function() {TBG.Config.Issuefields.Custom.remove('".make_url('configure_issuefields_delete_customtype', array('type' => $type_key))."', '".$type_key."', '".$type->getID()."'); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});")); ?>
            </li>
        </ul>
    </div>
    <?php echo image_tag('spinning_32.gif', array('style' => 'float: right; margin-left: 5px; display: none;', 'id' => $type_key . '_indicator')); ?>
    <div class="header">
        <span id="custom_type_<?php echo $type_key; ?>_name"><?php echo $type->getName(); ?></span>&nbsp;<span class="faded_out dark" style="font-weight: normal; font-size: 12px;"><?php echo $type_key; ?></span>
    </div>
    <div class="content">
        <b><?php echo __('Type'); ?>:</b>&nbsp;<?php echo $type->getTypeDescription(); ?>
    </div>
    <div id="delete_item_<?php echo $type->getID(); ?>" class="rounded_box white shadowed" style="margin: 5px 0 10px 0; font-size: 12px; display: none;">
        <div class="header"><?php echo __('Really delete "%itemname"?', array('%itemname' => $type->getName())); ?></div>
        <div class="content">
            <?php echo __('Are you really sure you want to delete this item?'); ?><br>
            <?php echo __('This will also remove the value of this custom field from all issues, along with any possible options this field can take.')?>
            <div style="text-align: right; font-size: 13px;">
                <a href="javascript:void(0);" onclick="TBG.Config.Issuefields.Custom.remove('<?php echo make_url('configure_issuefields_delete_customtype', array('type' => $type_key)); ?>', '<?php echo $type_key; ?>', '<?php echo $type->getID(); ?>');return false;"><?php echo __('Yes'); ?></a> ::
                <a href="javascript:void(0);" onclick="$('delete_item_<?php echo $type->getID(); ?>').toggle();"><b><?php echo __('No'); ?></b></a>
            </div>
            <?php echo image_tag('spinning_20.gif', array('id' => 'delete_'.$type_key.'_'.$type->getID().'_indicator', 'style' => 'margin-left: 5px; display: none;')); ?>
        </div>
    </div>
    <div id="custom_type_<?php echo $type_key; ?>_info">
        <b><?php echo __('Label'); ?>:</b>&nbsp;<span id="custom_type_<?php echo $type_key; ?>_description_span"><?php echo $type->getDescription(); ?></span><br>
        <span id="custom_type_<?php echo $type_key; ?>_instructions_div"<?php if (!$type->hasInstructions()): ?> style="display: none;"<?php endif; ?>>
            <b><?php echo __('Instructions'); ?>:</b>&nbsp;<span id="custom_type_<?php echo $type_key; ?>_instructions_span"><?php echo $type->getInstructions(); ?></span>
        </span>
        <span id="custom_type_<?php echo $type_key; ?>_no_instructions_div" class="faded_out dark"<?php if ($type->hasInstructions()): ?> style="display: none;"<?php endif; ?>><?php echo __("This custom type doesn't have any instructions"); ?></span>
    </div>
    <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_update_customtype', array('type' => $type_key)); ?>" onsubmit="TBG.Config.Issuefields.Custom.update('<?php echo make_url('configure_issuefields_update_customtype', array('type' => $type_key)); ?>', '<?php echo $type_key; ?>');return false;" id="edit_custom_type_<?php echo $type_key; ?>_form" style="display: none;">
        <div class="rounded_box white" style="margin: 5px 0 0 0; padding: 3px; font-size: 12px;">
            <label for="custom_type_<?php echo $type_key; ?>_name"><?php echo __('Name'); ?></label>
            <input type="text" name="name" id="custom_type_<?php echo $type_key; ?>_name" value="<?php echo $type->getName(); ?>" style="width: 250px;">
            <label for="custom_type_<?php echo $type_key; ?>_description"><?php echo __('Label'); ?></label>
            <input type="text" name="description" id="custom_type_<?php echo $type_key; ?>_description" value="<?php echo $type->getDescription(); ?>" style="width: 250px;"><br>
            <div class="faded_out" style="margin-bottom: 10px; padding: 2px;"><?php echo __('Users see the label, not the name'); ?></div>
            <label for="custom_type_<?php echo $type_key; ?>_instructions"><?php echo __('Instructions'); ?></label>&nbsp;<span class="faded_out"><?php echo __('Optional instruction that will be displayed to users'); ?></span><br>
            <textarea name="instructions" id="custom_type_<?php echo $type_key; ?>_instructions" style="width: 500px; height: 70px;" cols="70" rows="5"><?php echo $type->getInstructions(); ?></textarea><br>
            <input type="submit" value="<?php echo __('Update details'); ?>" style="font-weight: bold; font-size: 13px;">
            <?php echo __('%update_details or %cancel', array('%update_details' => '', '%cancel' => '<a href="javascript:void(0);" onclick="$(\'edit_custom_type_' . $type_key . '_form\').toggle();$(\'custom_type_' . $type_key . '_info\').toggle();"><b>' . __('cancel') . '</b></a>')); ?>
            <?php echo image_tag('spinning_20.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'edit_custom_type_' . $type_key . '_indicator')); ?>
        </div>
    </form>
    <div class="content" id="<?php echo $type_key; ?>_content" style="display: none;"> </div>
</div>
