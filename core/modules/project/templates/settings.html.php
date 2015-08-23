<?php

    $tbg_response->addBreadcrumb(__('Project settings'), make_url('project_settings', array('project_key' => $selected_project->getKey())));
    $tbg_response->setTitle(__('"%project_name" settings', array('%project_name' => $selected_project->getName())));
    include_component('project/projectheader', array('selected_project' => $selected_project, 'subpage' => __('Settings')));

?>
<div id="project_settings" class="project_info_container">
    <div class="project_right_container">
        <div class="project_right" id="project_settings_container">
            <div style="width: 100%; box-sizing: border-box; -moz-box-sizing: border-box;">
                <?php include_component('project/projectconfig', array('project' => $selected_project)); ?>
            </div>
        </div>
    </div>
    <div class="project_left_container">
        <div class="project_left">
            <h3><?php echo __('Project settings'); ?></h3>
            <?php if (!isset($selected_tab)) $selected_tab = 'info'; ?>
            <ul class="simple_list" id="project_config_menu">
                <li id="tab_information"<?php if ($selected_tab == 'info'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Project details'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_information', 'project_config_menu');")); ?></li>
                <li id="tab_other"<?php if ($selected_tab == 'other'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Display settings'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_other', 'project_config_menu');")); ?></li>
                <li id="tab_settings"<?php if ($selected_tab == 'settings'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Advanced settings'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_settings', 'project_config_menu');")); ?></li>
                <?php \thebuggenie\core\framework\Event::createNew('core', 'config_project_tabs_settings')->trigger(array('selected_tab' => $selected_tab)); ?>
                <li><h3><?php echo __('Other project details'); ?></h3></li>
                <li id="tab_hierarchy"<?php if ($selected_tab == 'hierarchy'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Editions and components'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_hierarchy', 'project_config_menu');")); ?></li>
                <li id="tab_developers"<?php if ($selected_tab == 'developers'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Team'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_developers', 'project_config_menu');")); ?></li>
                <li id="tab_permissions"<?php if ($selected_tab == 'permissions'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Roles and permissions'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_permissions', 'project_config_menu');")); ?></li>
                <?php \thebuggenie\core\framework\Event::createNew('core', 'config_project_tabs_other')->trigger(array('selected_tab' => $selected_tab)); ?>
            </ul>
            <?php if ($settings_saved): ?>
                <script type="text/javascript">
                    require(['domReady', 'thebuggenie/tbg'], function (domReady, TBG) {
                        domReady(function () {
                            TBG.Main.Helpers.Message.success('<?php echo __('Settings saved'); ?>', '<?php echo __('Project settings have been saved successfully'); ?>');
                        });
                    });
                </script>
            <?php endif; ?>
        </div>
    </div>
    <br style="clear: both;">
</div>
