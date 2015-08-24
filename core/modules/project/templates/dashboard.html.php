<?php

    use thebuggenie\core\entities\Dashboard;

    $tbg_response->addBreadcrumb(__('Dashboard'), make_url('project_dashboard', array('project_key' => $selected_project->getKey())));
    $tbg_response->setTitle(__('"%project_name" project dashboard', array('%project_name' => $selected_project->getName())));
    $tbg_response->addFeed(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), __('"%project_name" project timeline', array('%project_name' => $selected_project->getName())));
    include_component('project/projectheader', array('selected_project' => $selected_project, 'subpage' => $dashboard->getName()));
?>
<div id="project_planning" class="project_info_container">
    <?php \thebuggenie\core\framework\Event::createNew('core', 'project_dashboard_top')->trigger(); ?>
    <?php if (!$dashboard instanceof Dashboard && $tbg_user->canEditProjectDetails($selected_project)) : ?>
            <div style="text-align: center; padding: 40px;">
                <p class="content faded_out"><?php echo __("This dashboard doesn't contain any views."); ?></p>
                <br>
                <form action="<?php echo make_url('project_dashboard', array('project_key' => $selected_project->getKey())); ?>" method="post">
                    <input type="hidden" name="setup_default_dashboard" value="1">
                    <input type="submit" value="<?php echo __('Setup project dashboard'); ?>" class="button button-green" style="font-size: 1.1em; padding: 5px !important;">
                </form>
            </div>
        <?php else: ?>
            <?php include_component($dashboard->getLayout(), compact('dashboard')); ?>
    <?php endif; ?>
    <?php \thebuggenie\core\framework\Event::createNew('core', 'project_dashboard_bottom')->trigger(); ?>
    <br style="clear: both;">
</div>
