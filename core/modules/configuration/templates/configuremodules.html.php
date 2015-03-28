<?php $tbg_response->setTitle(__('Configure modules')); ?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0 class="configuration_page">
    <tr>
        <?php include_component('leftmenu', array('selected_section' => \thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_MODULES)); ?>
        <td valign="top" style="padding-left: 15px;">
            <div style="width: 730px;" id="config_modules">
                <h3><?php echo __('Configure modules'); ?></h3>
                <div class="content faded_out">
                    <p>
                        <?php echo __('This is where you manage all modules available in this installation of The Bug Genie. You can find even more modules online.'); ?>
                    </p>
                </div>
                <?php if ($module_error !== null): ?>
                    <div class="redbox" style="margin: 5px 0px 5px 0px;" id="module_error">
                        <div class="header"><?php echo $module_error; ?></div>
                    </div>
                <?php endif; ?>
                <?php if ($module_message !== null): ?>
                    <div class="greenbox" style="margin: 5px 0px 5px 0px;" id="module_message">
                        <div class="header"><?php echo $module_message; ?></div>
                    </div>
                <?php endif; ?>
                <?php if (count($outdated_modules) > 0): ?>
                    <div class="rounded_box yellow borderless" style="margin: 5px 0px 5px 0px; width: 783px;" id="module_message">
                        <div class="header"><?php echo __('You have %count outdated modules. They have been disabled until you upgrade them, you can upgrade them on this page.', array('%count' => count($outdated_modules))); ?></div>
                    </div>
                <?php endif; ?>
                <div style="margin-top: 15px; clear: both;" class="tab_menu inset">
                    <ul id="modules_menu">
                        <li id="tab_installed" class="selected"><?php echo javascript_link_tag(__('Installed modules (%count)', array('%count' => count($modules))), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_installed', 'modules_menu');")); ?></li>
                        <li id="tab_outdated"><?php echo javascript_link_tag(__('Outdated modules (%count)', array('%count' => count($outdated_modules))), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_outdated', 'modules_menu');")); ?></li>
                        <li id="tab_install"><?php echo javascript_link_tag(__('Install new modules'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_install', 'modules_menu');")); ?></li>
                    </ul>
                </div>
                <div id="modules_menu_panes">
                    <div id="tab_installed_pane" style="padding-top: 0;">
                        <?php foreach ($modules as $module_key => $module): ?>
                            <?php if (!$module->isOutdated()): ?>
                                <?php include_component('modulebox', array('module' => $module)); ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div id="tab_install_pane" style="padding-top: 0; width: 100%; display: none;">
                        <?php if (count($uninstalled_modules) == 0): ?>
                            <div class="faded_out" style="margin-top: 5px;"><?php echo __('There are no uninstalled modules available'); ?></div>
                        <?php else: ?>
                            <h5 style="margin-bottom: 0; padding-bottom: 0;"><?php echo __('Installable modules'); ?></h5>
                            <div class="content faded_out">
                                <p>
                                    <?php echo __('This is a list of modules available, but not installed on this system.'); ?>
                                    <?php echo __('To install a module, select it from the dropdown list and press the %install-button', array('%install' => '<b>' . __('Install') . '</b>')); ?>
                                </p>
                            </div>
                            <form action="<?php echo make_url('configure_install_module'); ?>" method="post" accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>">
                                <select name="module_key" style="margin-top: 5px; width: 100%;">
                                <?php foreach ($uninstalled_modules as $module_key => $module): ?>
                                    <option value="<?php echo $module_key; ?>"><?php echo $module->getLongName() . ' (' . $module->getDescription() . ')'; ?></option>
                                <?php endforeach; ?>
                                </select><br>
                                <input type="submit" value="<?php echo __('Install'); ?>" style="font-weight: bold; margin: 5px 0 10px 0;">
                            </form>
                        <?php endif; ?>
                    </div>
                    <div id="tab_outdated_pane" style="padding-top: 0; width: 100%; display: none;">
                        <?php if (count($outdated_modules) == 0): ?>
                            <div class="faded_out" style="margin-top: 5px;"><?php echo __('There are no outdated modules to upgrade'); ?></div>
                        <?php else: ?>
                            <div class="content faded_out">
                                <p>
                                    <?php echo __('This is a list of modules available and installed, but have been disabled until you upgrade them.'); ?>
                                    <?php echo __('To upgrade a module, select it from the dropdown list and press the %upgrade-button. This will likely involve changes to the database, so you may want to back up your database first', array('%upgrade' => '<b>' . __('Upgrade') . '</b>')); ?>
                                </p>
                            </div>
                            <form action="<?php echo make_url('configure_update_module'); ?>" method="post" accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>">
                                <div class="rounded_box mediumgrey borderless" style="margin: 5px 0px 5px 0px; text-align: right; width: 783px;">
                                    <select name="module_key" style="margin-top: 5px; width: 100%;">
                                    <?php foreach ($outdated_modules as $module): ?>
                                        <option value="<?php echo $module->getName(); ?>"><?php echo $module->getLongName().__(' (version %ver)', array('%ver' => $module->getVersion())); ?></option>
                                    <?php endforeach; ?>
                                    </select><br>
                                    <input type="submit" value="<?php echo __('Upgrade'); ?>" style="font-weight: bold; margin: 5px 0 10px 0;">
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>
