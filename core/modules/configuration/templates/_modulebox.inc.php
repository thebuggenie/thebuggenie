<li class="plugin module <?php if (!$module->isEnabled()) echo ' disabled'; ?> <?php if ($module->isOutdated()) echo ' can-update out-of-date'; ?>" id="module_<?php echo $module->getID(); ?>" data-id="<?php echo $module->getID(); ?>" data-module-key="<?php echo $module->getName(); ?>" data-version="<?php echo $module->getVersion(); ?>">
    <?php echo __('%module_name version %version by %author', array(
        '%module_name' => '<h1>'.$module->getLongName().'</h1>',
        '%version' => '<span class="version">'.$module->getVersion().'</span>',
        '%author' => '<a href="http://www.thebuggenie.com" class="author-link">zegenie Studios</a>'
    )); ?>
    <p class="description"><?php echo __($module->getDescription()); ?></p>
    <?php if ($module->getType() == \thebuggenie\core\entities\Module::MODULE_AUTH): ?>
        <div class="status_badge authentication-module">
            <?php echo image_tag('cfg_icon_authentication.png') . __('Authentication module'); ?>
        </div>
    <?php endif; ?>
    <?php if ($module->getID()): ?>
        <div class="status_badge module_status plugin_status<?php echo ($module->isEnabled()) ? ' enabled' : ' disabled'; ?>">
            <?php echo ($module->isEnabled()) ? __('Enabled') : __('Disabled'); ?>
        </div>
        <div class="status_badge module_status plugin_status outofdate">
            <?php echo __('Needs update'); ?>
        </div>
    <?php else: ?>
        <div class="status_badge module_status plugin_status outofdate">
            <?php echo __('Not installed'); ?>
        </div>
    <?php endif; ?>
    <?php if ($module->isCore()): ?>
        <div class="status_badge module_status plugin_status core">
            <?php echo __('Core module'); ?>
        </div>
    <?php endif; ?>
    <?php if ($module->getID() && $is_default_scope): ?>
        <div id="update_module_help_<?php echo $module->getID(); ?>" class="fullpage_backdrop" style="display: none;">
            <div class="backdrop_box medium">
                <h1><?php echo __('Install downloaded module update file'); ?></h1>
                <p>
                    <?php echo __('Please click the download link below and download the update file. Place the downloaded file in the cache folder (%cache_folder) on this server. As soon as the file has been verified, the %update button below will be enabled, and you can press the button to update the module.',
                        array('%cache_folder' => '<span class="command_box">'.THEBUGGENIE_CACHE_PATH.$module->getName().'.zip</span>', '%update' => '"'.__('Update').'"'));
                    ?>
                </p>
                <form id="module_<?php echo $module->getName(); ?>_perform_update" style="display: inline-block; float: right; padding: 10px;" action="<?php echo make_url('configuration_module_update', array('module_key' => $module->getName())); ?>">
                    <a href="javascript:void(0);" onclick="TBG.Core.cancelManualUpdatePoller();$('update_module_help_<?php echo $module->getID(); ?>').hide();"><?php echo __('Cancel'); ?></a>
                    <?php echo __('%cancel or %update_module', array('%cancel' => '', '%update_module' => '')); ?>
                    <input type="submit" disabled value="<?php echo __('Update module'); ?>" class="button button-lightblue">
                </form>
                <div style="display: inline-block; float: none; padding: 10px;">
                    <a id="module_<?php echo $module->getName(); ?>_download_location" class="button button-silver" href="#" target="_blank"><?php echo __('Download update file'); ?></a>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="module-actions plugin-actions">
        <?php if ($module->getID()): ?>
            <?php if ($is_default_scope): ?>
                <button class="button button-lightblue update-button dropper" id="module_<?php echo $module->getID(); ?>_update" data-key="<?php echo $module->getName(); ?>"><?php echo __('Update'); ?></button>
                <ul id="module_<?php echo $module->getID(); ?>_update_dropdown" style="font-size: 1.1em;" class="popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();">
                    <?php if ($module->isOutdated()): ?>
                        <li>
                            <?php echo link_tag(make_url('configuration_module_update', array('module_key' => $module->getName())), __('Update to latest version')); ?>
                        </li>
                    <?php endif; ?>
                    <li>
                        <?php echo link_tag(make_url('configuration_download_module_update', array('module_key' => $module->getName())), __('Install latest version')); ?>
                    </li>
                    <li><a href="javascript:void(0);" class="update-module-menu-item"><?php echo __('Manual update'); ?></a></li>
                </ul>
            <?php endif; ?>
            <button class="button button-silver dropper" id="module_<?php echo $module->getID(); ?>_more_actions"><?php echo __('Actions'); ?></button>
            <ul id="module_<?php echo $module->getID(); ?>_more_actions_dropdown" style="font-size: 1.1em;" class="popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();">
                <?php if ($module->hasConfigSettings()): ?>
                    <li>
                        <?php echo link_tag(make_url('configure_module', array('config_module' => $module->getName())), __('Configure module')); ?>
                    </li>
                <?php endif; ?>
                <?php if ($module->getType() !== \thebuggenie\core\entities\Module::MODULE_AUTH): ?>
                    <li>
                        <?php if ($module->isEnabled()): ?>
                            <a href="javascript:void(0);" onclick="$('disable_module_<?php echo $module->getID(); ?>').toggle();$('permissions_module_<?php echo($module->getID()); ?>').hide();$('uninstall_module_<?php echo $module->getID(); ?>').hide();"><?php echo __('Disable module'); ?></a>
                        <?php else: ?>
                            <a href="javascript:void(0);" onclick="$('enable_module_<?php echo $module->getID(); ?>').toggle();$('permissions_module_<?php echo($module->getID()); ?>').hide();$('uninstall_module_<?php echo $module->getID(); ?>').hide();"><?php echo __('Enable module'); ?></a>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
                <?php if (!$module->isCore()): ?>
                    <li><a href="javascript:void(0);" onclick="$('uninstall_module_<?php echo $module->getID(); ?>').toggle();$('permissions_module_<?php echo($module->getID()); ?>').hide();$('<?php if($module->isEnabled()): ?>disable<?php else: ?>enable<?php endif; ?>_module_<?php echo $module->getID(); ?>').hide();"><?php echo __('Uninstall module'); ?></a></li>
                <?php endif; ?>
            </ul>
        <?php else: ?>
            <?php echo link_tag(make_url('configure_install_module', array('module_key' => $module->getName())), __('Install'), array('class' => 'button button-silver')); ?>
        <?php endif; ?>
    </div>
    <?php if (!$module->isCore() && $module->getID()): ?>
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
</li>
