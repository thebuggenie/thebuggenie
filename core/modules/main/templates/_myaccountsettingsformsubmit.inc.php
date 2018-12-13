<div class="save-button-container">
    <div class="message"><?php echo __('Click "%save" to save changes in the "%module_settings_name" category', array('%save' => __('Save'), '%module_settings_name' => $module->getAccountSettingsName())); ?></div>
    <span id="profile_<?php echo $module->getName(); ?>_save_indicator" style="display: none;"><?php echo image_tag('spinning_20.gif'); ?></span>
    <input type="submit" id="submit_settings_button" value="<?php echo __('Save'); ?>">
</div>
