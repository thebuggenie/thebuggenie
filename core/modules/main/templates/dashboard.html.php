<?php

    $tbg_response->setTitle(__('Dashboard'));
    $tbg_response->addBreadcrumb(__('Personal dashboard'), make_url('dashboard'));
    $tbg_response->addFeed(make_url('my_reported_issues', array('format' => 'rss')), __('Issues reported by me'));
    $tbg_response->addFeed(make_url('my_assigned_issues', array('format' => 'rss')), __('Open issues assigned to you'));
    $tbg_response->addFeed(make_url('my_teams_assigned_issues', array('format' => 'rss')), __('Open issues assigned to your teams'));

?>
<?php include_component('main/hideableInfoBoxModal', array('key' => 'dashboard_didyouknow', 'title' => __('Get started using The Bug Genie'), 'template' => 'main/profile_dashboard')); ?>
<div id="dashboard_lefthand" class="side_bar <?php echo \thebuggenie\core\framework\Settings::getToggle('dashboard_lefthand') ? ' collapsed' : ''; ?>">
    <?php \thebuggenie\core\framework\Event::createNew('core', 'dashboard_left_top')->trigger(); ?>
    <div class="collapser_link" onclick="TBG.Main.Dashboard.sidebar('<?php echo make_url('set_toggle_state', array('key' => 'dashboard_lefthand', 'state' => '')); ?>', 'dashboard_lefthand');">
        <a href="javascript:void(0);">
            <?php echo image_tag('sidebar_collapse.png', array('class' => 'collapser')); ?>
            <?php echo image_tag('sidebar_expand.png', array('class' => 'expander')); ?>
        </a>
    </div>
    <div class="container_divs_wrapper">
        <div class="container_div">
            <?php include_component('main/myfriends'); ?>
        </div>
    </div>
    <?php \thebuggenie\core\framework\Event::createNew('core', 'dashboard_left_bottom')->trigger(); ?>
</div>
<div class="main_area">
    <?php \thebuggenie\core\framework\Event::createNew('core', 'dashboard_main_top')->trigger(); ?>
    <?php include_component($dashboard->getLayout(), compact('dashboard')); ?>
    <?php \thebuggenie\core\framework\Event::createNew('core', 'dashboard_main_bottom')->trigger(); ?>
</div>
