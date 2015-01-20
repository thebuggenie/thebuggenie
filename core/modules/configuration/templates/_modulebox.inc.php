<div class="<?php if ($module->isEnabled()): ?> bluebox<?php else: ?> greybox<?php endif; ?>" style="margin: 5px 0px 5px 0px; position: relative; vertical-align: middle; text-align: right; min-height: 40px;" id="module_<?php echo $module->getID(); ?>">
    <div class="header">
        <div class="module_status<?php if ($module->isCore()) echo ' core'; elseif ($module->isEnabled()) echo ' enabled'; ?>">
            <?php if (!$module->isCore()): ?>
                <?php if ($module->isEnabled()): ?>
                    <?php echo image_tag('icon_enabled.png') . __('Enabled'); ?>
                <?php else: ?>
                    <?php echo image_tag('icon_disabled.png') . __('Disabled'); ?>
                <?php endif; ?>
            <?php else: ?>
                <?php echo __('Core module'); ?>
            <?php endif; ?>
        </div>
        <?php echo __($module->getLongName()); ?><span class="module_shortname faded_out"> <?php echo $module->getVersion(); ?> (<?php echo $module->getName(); ?>) <?php if ($module->getType() == \thebuggenie\core\entities\Module::MODULE_AUTH): echo ' - '.__('Authentication module'); endif; ?></span>
    </div>
    <div class="content"><?php echo __($module->getDescription()); ?></div>
    <div style="position: absolute; right: 12px; top: 12px;">
        <button class="button button-silver dropper" id="module_<?php echo $module->getID(); ?>_more_actions"><?php echo __('Actions'); ?></button>
        <ul id="module_<?php echo $module->getID(); ?>_more_actions_dropdown" style="font-size: 1.1em; width: 200px; top: 23px; margin-top: 0; text-align: right; z-index: 1000;" class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();">
            <?php if ($module->hasConfigSettings()): ?>
                <li>
                    <?php echo link_tag(make_url('configure_module', array('config_module' => $module->getName())), __('Configure module')); ?>
                </li>
            <?php endif; ?>
            <li>
                <a href="javascript:void(0);" onclick="$('permissions_module_<?php echo($module->getID()); ?>').toggle();$('uninstall_module_<?php echo $module->getID(); ?>').hide();$('<?php if($module->isEnabled()): ?>disable<?php else: ?>enable<?php endif; ?>_module_<?php echo $module->getID(); ?>').hide();"><?php echo __('Edit permissions'); ?></a>
            </li>
            <?php if ($module->getType() !== \thebuggenie\core\entities\Module::MODULE_AUTH): ?>
                <li>
                    <?php if ($module->isEnabled()): ?>
                        <a href="javascript:void(0);" onclick="$('disable_module_<?php echo $module->getID(); ?>').toggle();$('permissions_module_<?php echo($module->getID()); ?>').hide();$('uninstall_module_<?php echo $module->getID(); ?>').hide();"><?php echo __('Disable module'); ?></a>
                    <?php else: ?>
                        <a href="javascript:void(0);" onclick="$('enable_module_<?php echo $module->getID(); ?>').toggle();$('permissions_module_<?php echo($module->getID()); ?>').hide();$('uninstall_module_<?php echo $module->getID(); ?>').hide();"><?php echo __('Enable module'); ?></a>
                    <?php endif; ?>
                </li>
            <?php endif; ?>
            <?php if (!$module->isCore() && \thebuggenie\core\framework\Context::getScope()->isDefault()): ?>
                <li><a href="javascript:void(0);" onclick="$('uninstall_module_<?php echo $module->getID(); ?>').toggle();$('permissions_module_<?php echo($module->getID()); ?>').hide();$('<?php if($module->isEnabled()): ?>disable<?php else: ?>enable<?php endif; ?>_module_<?php echo $module->getID(); ?>').hide();"><?php echo __('Uninstall module'); ?></a></li>
            <?php endif; ?>
        </ul>
    </div>
    <?php if (!$module->isCore()): ?>
        <?php if ($module->isEnabled()): ?>
            <div id="disable_module_<?php echo($module->getID()); ?>" style="display: none; margin-top: 10px; padding: 0 10px 5px 10px; text-align: left;" class="rounded_box white shadowed">
                <h4><?php echo __('Really disable "%module_name"?', array('%module_name' => $module->getLongname())); ?></h4>
                <span class="question_header"><?php echo __('Disabling this module will prevent users from accessing it or any associated data.'); ?></span><br>
                <div style="text-align: right;" id="disable_module_controls_<?php echo $module->getID(); ?>"><?php echo link_tag(make_url('configure_disable_module', array('module_key' => $module->getName())), __('Yes'), array('class' => 'xboxlink')); ?> :: <a href="javascript:void(0)" class="xboxlink" onclick="$('disable_module_<?php echo $module->getID(); ?>').hide();"><?php echo __('No'); ?></a></div>
            </div>
        <?php else: ?>
            <div id="enable_module_<?php echo($module->getID()); ?>" style="display: none; margin-top: 10px; padding: 0 10px 5px 10px; text-align: left;" class="rounded_box white shadowed">
                <h4><?php echo __('Really enable "%module_name"?', array('%module_name' => $module->getLongname())); ?></h4>
                <span class="question_header"><?php echo __('Enabling this module will give users access to it and all associated data.'); ?></span><br>
                <div style="text-align: right;" id="enable_module_controls_<?php echo $module->getID(); ?>"><?php echo link_tag(make_url('configure_enable_module', array('module_key' => $module->getName())), __('Yes'), array('class' => 'xboxlink')); ?> :: <a href="javascript:void(0)" class="xboxlink" onclick="$('enable_module_<?php echo $module->getID(); ?>').hide();"><?php echo __('No'); ?></a></div>
            </div>
        <?php endif; ?>
        <div id="uninstall_module_<?php echo($module->getID()); ?>" style="display: none; margin-top: 10px; padding: 0 10px 5px 10px; text-align: left;" class="rounded_box white shadowed">
            <h4><?php echo __('Really uninstall "%module_name"?', array('%module_name' => $module->getLongname())); ?></h4>
            <span class="question_header"><?php echo __('Uninstalling this module will permanently prevent users from accessing it or any associated data. If you just want to prevent access to the module temporarily, disable the module instead.'); ?></span><br>
            <div style="text-align: right;" id="uninstall_module_controls_<?php echo $module->getID(); ?>"><?php echo link_tag(make_url('configure_uninstall_module', array('module_key' => $module->getName())), __('Yes'), array('class' => 'xboxlink')); ?> :: <a href="javascript:void(0)" class="xboxlink" onclick="$('uninstall_module_<?php echo $module->getID(); ?>').hide();"><?php echo __('No'); ?></a></div>
        </div>
    <?php endif; ?>
    <div id="permissions_module_<?php echo($module->getID()); ?>" style="display: none; margin-top: 10px;" class="rounded_box white shadowed">
        <div class="permission_list" style="padding: 0 10px 5px 10px; text-align: left;">
            <div class="header_div" style="margin-top: 0;"><?php echo __('Available permissions'); ?></div>
            <ul id="module_permission_details_<?php echo $module->getName(); ?>">
                <?php include_component('configuration/permissionsblock', array('base_id' => 'module_' . $module->getName() . '_permissions', 'permissions_list' => $module->getAvailablePermissions(), 'mode' => 'module_permissions', 'target_id' => 0, 'module' => $module->getName(), 'access_level' => (($tbg_user->canSaveConfiguration(\thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_PERMISSIONS) ? \thebuggenie\core\framework\Settings::ACCESS_FULL : \thebuggenie\core\framework\Settings::ACCESS_READ)))); ?>
            </ul>
        </div>
    </div>
</div>
