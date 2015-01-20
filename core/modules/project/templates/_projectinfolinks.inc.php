<?php

    use thebuggenie\core\entities\DashboardView;

    if (!isset($selected_project))
    {
        $selected_project = \thebuggenie\core\framework\Context::getCurrentProject();
    }

    if (!isset($submenu)): $submenu = false;
    endif;
?>
<?php if ($tbg_user->hasProjectPageAccess('project_dashboard', $selected_project)): ?>
        <?php echo link_tag(make_url('project_dashboard', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Dashboard'), (($tbg_response->getPage() == 'project_dashboard') ? array('class' => 'selected') : null)); ?>
        <?php \thebuggenie\core\framework\Event::createNew('core', 'project_sidebar_links_dashboard')->trigger(array('submenu' => $submenu)); ?>
        <?php if (!($submenu) && $tbg_response->getPage() == 'project_dashboard' && $tbg_user->canEditProjectDetails($selected_project)): ?>
            <ul class="simple_list">
                <li><?php echo javascript_link_tag('<span>' . __('Customize') . '</span>', array('title' => __('Customize'), 'onclick' => "TBG.Main.Helpers.Backdrop.show('" . make_url('get_partial_for_backdrop', array('key' => 'dashboard_config', 'tid' => $selected_project->getID(), 'target_type' => DashboardView::TYPE_PROJECT, 'previous_route')) . "');")); ?></li>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_releases', $selected_project) && $selected_project->isBuildsEnabled()): ?>
        <?php echo link_tag(make_url('project_releases', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Releases'), (($tbg_response->getPage() == 'project_releases') ? array('class' => 'selected') : null)); ?>
        <?php \thebuggenie\core\framework\Event::createNew('core', 'project_sidebar_links_releases')->trigger(array('submenu' => $submenu)); ?>
    <?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_roadmap', $selected_project)): ?>
        <?php echo link_tag(make_url('project_roadmap', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Roadmap'), (($tbg_response->getPage() == 'project_roadmap') ? array('class' => 'selected') : array())); ?>
        <?php \thebuggenie\core\framework\Event::createNew('core', 'project_sidebar_links_roadmap')->trigger(array('submenu' => $submenu)); ?>
    <?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_team', $selected_project)): ?>
        <?php echo link_tag(make_url('project_team', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Team overview'), (($tbg_response->getPage() == 'project_team') ? array('class' => 'selected') : array())); ?>
        <?php \thebuggenie\core\framework\Event::createNew('core', 'project_sidebar_links_team')->trigger(array('submenu' => $submenu)); ?>
    <?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_statistics', $selected_project)): ?>
        <?php echo link_tag(make_url('project_statistics', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Statistics'), (($tbg_response->getPage() == 'project_statistics') ? array('class' => 'selected') : array())); ?>
        <?php if (!($submenu) && $tbg_response->getPage() == 'project_statistics'): ?>
            <ul class="simple_list">
                <li><b><?php echo __('Number of issues per:'); ?></b></li>
                <li><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_state')); ?>');"><?php echo __('%number_of_issues_per State (open / closed)', array('%number_of_issues_per' => '')); ?></a></li>
                <li><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_category')); ?>');"><?php echo __('%number_of_issues_per Category', array('%number_of_issues_per' => '')); ?></a></li>
                <li><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_priority')); ?>');"><?php echo __('%number_of_issues_per Priority level', array('%number_of_issues_per' => '')); ?></a></li>
                <li><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_resolution')); ?>');"><?php echo __('%number_of_issues_per Resolution', array('%number_of_issues_per' => '')); ?></a></li>
                <li><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_reproducability')); ?>');"><?php echo __('%number_of_issues_per Reproducability', array('%number_of_issues_per' => '')); ?></a></li>
                <li><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_status')); ?>');"><?php echo __('%number_of_issues_per Status type', array('%number_of_issues_per' => '')); ?></a></li>
            </ul>
            <?php \thebuggenie\core\framework\Event::createNew('core', 'projectstatistics_links', $selected_project)->trigger(); ?>
        <?php endif; ?>
        <?php \thebuggenie\core\framework\Event::createNew('core', 'project_sidebar_links_statistics')->trigger(array('submenu' => $submenu)); ?>
    <?php endif; ?>
<?php if ($tbg_user->hasProjectPageAccess('project_timeline', $selected_project)): ?>
        <?php echo link_tag(make_url('project_timeline_important', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Timeline'), (($tbg_response->getPage() == 'project_timeline') ? array('class' => 'selected') : null)); ?>
        <?php \thebuggenie\core\framework\Event::createNew('core', 'project_sidebar_links_timeline')->trigger(array('submenu' => $submenu)); ?>
    <?php endif; ?>
<?php $event = \thebuggenie\core\framework\Event::createNew('core', 'project_sidebar_links')->trigger(array('submenu' => $submenu)); ?>
<?php foreach ($event->getReturnList() as $menuitem): ?>
        <?php echo link_tag($menuitem['url'], $menuitem['title'], array('title' => $menuitem['title'])); ?>
    <?php endforeach; ?>
<?php if ($tbg_user->canEditProjectDetails($selected_project)): ?>
        <?php if ($selected_project->isBuildsEnabled()): ?>
            <?php echo link_tag(make_url('project_release_center', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Release center'), (($tbg_response->getPage() == 'project_release_center') ? array('class' => 'selected') : array())); ?>
        <?php endif; ?>
        <?php echo link_tag(make_url('project_settings', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Settings'), (($tbg_response->getPage() == 'project_settings') ? array('class' => 'selected') : array())); ?>
        <?php if (!($submenu) && $tbg_response->getPage() == 'project_settings'): ?>
            <?php if (!isset($selected_tab)) $selected_tab = 'info'; ?>
            <ul class="simple_list" id="project_config_menu">
                <li id="tab_information"<?php if ($selected_tab == 'info'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Project details'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_information', 'project_config_menu');")); ?></li>
                <li id="tab_other"<?php if ($selected_tab == 'other'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Display settings'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_other', 'project_config_menu');")); ?></li>
                <li id="tab_settings"<?php if ($selected_tab == 'settings'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Advanced settings'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_settings', 'project_config_menu');")); ?></li>
                <li id="tab_hierarchy"<?php if ($selected_tab == 'hierarchy'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Editions and components'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_hierarchy', 'project_config_menu');")); ?></li>
                <li id="tab_developers"<?php if ($selected_tab == 'developers'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Team'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_developers', 'project_config_menu');")); ?></li>
                <li id="tab_permissions"<?php if ($selected_tab == 'permissions'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(__('Roles and permissions'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_permissions', 'project_config_menu');")); ?></li>
            <?php \thebuggenie\core\framework\Event::createNew('core', 'config_project_tabs')->trigger(array('selected_tab' => $selected_tab)); ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
