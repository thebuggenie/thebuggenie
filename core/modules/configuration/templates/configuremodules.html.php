<?php $tbg_response->setTitle(__('Configure modules')); ?>
<table cellpadding=0 cellspacing=0 class="configuration_page">
    <tr>
        <?php include_component('leftmenu', array('selected_section' => \thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_MODULES)); ?>
        <td valign="top" class="main_configuration_content">
            <div style="width: 730px;" id="config_modules" class="config_plugins">
                <h3><?= __('Configure modules'); ?></h3>
                <?php if (!$license_ok): ?>
                    <div class="message-box type-info">
                        <span class="message"><?= fa_image_tag('receipt') . '<span>'.__('A valid subscription license enables additional features such as automatic module installation and upgrades.').'</span>'; ?></span>
                        <span class="actions"><a href="https://thebuggenie.com/register/self-hosted" target="_blank" class="button button-silver button-purchase"><?= __('Purchase a subscription'); ?></a></span>
                    </div>
                <?php endif; ?>
                <div class="content faded_out">
                    <p>
                        <?= __('Manage existing or download and install new modules for The Bug Genie here.'); ?>
                    </p>
                </div>
                <?php if ($module_error !== null): ?>
                    <div class="message-box type-error" id="module_error">
                        <span class="message"><?= fa_image_tag('times') . $module_error; ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($module_message !== null): ?>
                    <div class="message-box type-info" id="module_message">
                        <span class="message"><?= fa_image_tag('exclamation-circle') . $module_message; ?></span>
                    </div>
                <?php endif; ?>
                <?php if (count($outdated_modules) > 0): ?>
                    <div class="message-box type-warning" id="outdated_module_message">
                        <span class="message">
                            <?= fa_image_tag('exclamation-circle'); ?>
                            <?php if ($is_default_scope): ?>
                                <?= __('You have %count outdated modules. They have been disabled until you upgrade them, you can upgrade them on this page.', array('%count' => count($outdated_modules))); ?>
                            <?php else: ?>
                                <?= __('You have %count outdated modules. They have been disabled until they are updated by an administrator.', array('%count' => count($outdated_modules))); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                <?php endif; ?>
                <div style="margin-top: 15px; clear: both;" class="tab_menu inset">
                    <ul id="modules_menu">
                        <li id="tab_installed" class="selected"><?= javascript_link_tag(image_tag('spinning_16.gif', array('id' => 'installed_modules_indicator', 'style' => 'display: none;')).__('Installed modules (%count)', array('%count' => count($modules))), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_installed', 'modules_menu');")); ?></li>
                        <li id="tab_uninstalled"><?= javascript_link_tag(__('Installable local modules (%count)', array('%count' => count($uninstalled_modules))), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_uninstalled', 'modules_menu');")); ?></li>
                        <?php if ($is_default_scope): ?>
                            <li id="tab_install"><?= javascript_link_tag(__('Discover new modules'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_install', 'modules_menu');")); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div id="modules_menu_panes">
                    <div id="tab_installed_pane" style="padding-top: 0;">
                        <ul class="modules-list plugins-list installed" id="installed-modules-list">
                            <?php foreach ($modules as $module_key => $module): ?>
                                <?php include_component('modulebox', array('module' => $module, 'is_default_scope' => $is_default_scope)); ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div id="tab_uninstalled_pane" style="padding-top: 0; width: 100%; display: none;">
                        <?php if (count($uninstalled_modules) == 0): ?>
                            <div class="faded_out" style="margin-top: 5px;"><?= __('There are no uninstalled modules available'); ?></div>
                        <?php else: ?>
                            <div class="content">
                                <p><?= __('This is a list of modules that are available in the modules folder, but not currently installed on this system.'); ?></p>
                                <?php if (!$writable && $is_default_scope): ?>
                                    <div class="message-box type-warning" id="module_message_writable_failure">
                                        <span class="message"><?= fa_image_tag('folder') . __('The modules folder %modules_path seems to not be writable. You may not be able to install new modules.', array('%modules_path' => '<span class="command_box">' . THEBUGGENIE_MODULES_PATH . '</span>')); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <ul class="modules-list plugins-list installed" id="uninstalled-modules-list">
                                <?php foreach ($uninstalled_modules as $module_key => $module): ?>
                                    <?php include_component('modulebox', array('module' => $module, 'is_default_scope' => $is_default_scope)); ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <div id="tab_install_pane" style="padding-top: 0; width: 100%; display: none;">
                        <?php if (!$writable && $is_default_scope): ?>
                            <div class="message-box type-warning" id="module_message_writable_failure">
                                <span class="message"><?= fa_image_tag('folder') . __('The modules folder %modules_path seems to not be writable. You may not be able to install new modules.', array('%modules_path' => '<span class="command_box">' . THEBUGGENIE_MODULES_PATH . '</span>')); ?></span>
                            </div>
                        <?php endif; ?>
                        <div id="available_modules_loading_indicator"><?= image_tag('spinning_16.gif'); ?></div>
                        <div id="available_modules_container" class="available_plugins_container plugins-list">

                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>
<?php if ($is_default_scope): ?>
    <script>
        require(['domReady', 'thebuggenie/tbg'], function (domReady, TBG) {
            domReady(function () {
                TBG.Modules.getAvailableOnline();
                TBG.Modules.getModuleUpdates();
            });
        });
    </script>
<?php endif; ?>
