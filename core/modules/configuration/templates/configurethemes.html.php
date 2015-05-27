<?php $tbg_response->setTitle(__('Configure theme(s)')); ?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0 class="configuration_page">
    <tr>
        <?php include_component('leftmenu', array('selected_section' => \thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_THEMES)); ?>
        <td valign="top" style="padding-left: 15px;">
            <div style="width: 730px;" id="config_themes" class="config_plugins">
                <h3><?php echo __('Configure theme(s)'); ?></h3>
                <div class="content faded_out">
                    <p>
                        <?php echo __('Select which theme to use for The Bug Genie from this page. You can also download and install new themes.'); ?>
                    </p>
                </div>
                <?php if ($theme_error !== null): ?>
                    <div class="redbox" style="margin: 5px 0px;" id="theme_error">
                        <div class="header"><?php echo $theme_error; ?></div>
                    </div>
                <?php endif; ?>
                <?php if (!$writable && $is_default_scope): ?>
                    <div class="lightyellowbox" style="margin: 5px 0px" id="theme_message_writable_failure">
                        <div class="header"><?php echo __('The themes folder (%themes_path) seems to not be writable. You may not be able to install new themes.', array('%themes_path' => THEBUGGENIE_PATH . 'themes')); ?></div>
                    </div>
                <?php endif; ?>
                <?php if (!$writable_link && $is_default_scope): ?>
                    <div class="lightyellowbox" style="margin: 5px 0px" id="theme_message_writable_link_failure">
                        <div class="header"><?php echo __('The themes public folder (%themes_public_path) seems to not be writable. You may not be able to install new themes.', array('%themes_public_path' => THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'themes')); ?></div>
                    </div>
                <?php endif; ?>
                <?php if ($theme_message !== null): ?>
                    <div class="greenbox" style="margin: 5px 0px;" id="theme_message">
                        <div class="header"><?php echo $theme_message; ?></div>
                    </div>
                <?php endif; ?>
                <div style="margin-top: 15px; clear: both;" class="tab_menu inset">
                    <ul id="themes_menu">
                        <li id="tab_installed" class="selected"><?php echo javascript_link_tag(image_tag('spinning_16.gif', array('id' => 'installed_themes_indicator', 'style' => 'display: none;')).__('Installed themes (%count)', array('%count' => count($themes))), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_installed', 'themes_menu');")); ?></li>
                        <li id="tab_install"><?php echo javascript_link_tag(__('Discover new themes'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_install', 'themes_menu');")); ?></li>
                    </ul>
                </div>
                <div id="themes_menu_panes">
                    <div id="tab_installed_pane" style="padding-top: 0;">
                        <ul class="themes-list installed plugins-list" id="installed-themes-list">
                            <?php foreach ($themes as $theme_key => $theme): ?>
                                <?php include_component('theme', array('theme' => $theme)); ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div id="tab_install_pane" style="padding-top: 0; width: 100%; display: none;">
                        <div id="available_themes_loading_indicator"><?php echo image_tag('spinning_16.gif'); ?></div>
                        <div id="available_themes_container" class="available_plugins_container">

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
                TBG.Themes.getAvailableOnline();
                TBG.Themes.getThemeUpdates();
            });
        });
    </script>
<?php endif; ?>
