<div class="greybox" style="margin: 25px 0 0 0; height: 24px;">
    <div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save" to save changes in the "%module_settings_name" category', array('%save' => __('Save'), '%module_settings_name' => $module->getAccountSettingsName())); ?></div>
    <input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
    <span id="profile_<?php echo $module->getName(); ?>_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
</div>
