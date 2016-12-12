<?php

    $tbg_response->setTitle(__('Configure settings'));
    
?>
<table cellpadding=0 cellspacing=0 class="configuration_page">
    <tr>
        <?php include_component('leftmenu', array('selected_section' => \thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_SETTINGS)); ?>
        <td valign="top" class="main_configuration_content">
            <div style="width: 730px;">
                <h3>
                    <?= __('Configure settings'); ?>
                </h3>
                <div class="content faded_out">
                    <p><?= __("These are all the different settings defining most of the behaviour of The Bug Genie. Changing any of these settings will apply globally and immediately, without the need to log out and back in, reboot or anything to that effect."); ?></p>
                </div>
                <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                    <form accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_settings'); ?>" method="post" onsubmit="TBG.Main.Helpers.formSubmit('<?= make_url('configure_settings'); ?>', 'config_settings'); return false;" id="config_settings">
                <?php endif; ?>
                <div style="margin-top: 15px; clear: both;" class="tab_menu inset">
                    <ul id="settings_menu">
                        <li class="selected" id="tab_general_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_general_settings', 'settings_menu');" href="javascript:void(0);"><?= fa_image_tag('cog'); ?><span><?= __('General', array(), true); ?></span></a></li>
                        <li id="tab_reglang_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_reglang_settings', 'settings_menu');" href="javascript:void(0);"><?= fa_image_tag('globe'); ?><span><?= __('Regional & language'); ?></span></a></li>
                        <li id="tab_user_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_user_settings', 'settings_menu');" href="javascript:void(0);"><?= fa_image_tag('unlock-alt'); ?><span><?= __('Users & security'); ?></span></a></li>
                        <li id="tab_offline_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_offline_settings', 'settings_menu');" href="javascript:void(0);"><?= fa_image_tag('coffee'); ?><span><?= __('Maintenance mode', array(), true); ?></span></a></li>
                    </ul>
                </div>
                <div id="settings_menu_panes">
                    <div id="tab_general_settings_pane"><?php include_component('general', array('access_level' => $access_level)); ?></div>
                    <div id="tab_reglang_settings_pane" style="display: none;"><?php include_component('reglang', array('access_level' => $access_level)); ?></div>
                    <div id="tab_user_settings_pane" style="display: none;"><?php include_component('user', array('access_level' => $access_level)); ?></div>
                    <div id="tab_offline_settings_pane" style="display: none;"><?php include_component('offline', array('access_level' => $access_level)); ?></div>
                </div>
                <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                    <div class="greybox" style="margin: 5px 0px 5px 0px; height: 25px; padding: 5px 10px 5px 10px;">
                        <div style="float: left; font-size: 13px; padding-top: 2px;"><?= __('Click "%save" to save your changes in all categories', array('%save' => __('Save'))); ?></div>
                        <input type="submit" id="config_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?= __('Save'); ?>">
                        <span id="config_settings_indicator" style="display: none; float: right;"><?= image_tag('spinning_20.gif'); ?></span>
                    </div>
                <?php endif; ?>
                </form>
            </div>
        </td>
    </tr>
</table>
