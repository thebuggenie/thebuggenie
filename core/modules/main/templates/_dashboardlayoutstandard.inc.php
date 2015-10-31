<ul id="dashboard_<?php echo $dashboard->getID(); ?>" class="dashboard layout_standard" data-url="<?php echo make_url("dashboard_view", array('view_id' => '')); ?>/{view_id}" <?php if ($dashboard->canEdit()): ?> data-post-url="<?php echo make_url('dashboard_specific', array('dashboard_id' => $dashboard->getID())); ?>" data-add-view-url="<?php echo make_url('get_partial_for_backdrop', array('key' => 'add_dashboard_view', 'dashboard_id' => $dashboard->getID())); ?>" data-sort-url="<?php echo make_url('dashboard_sort', array('dashboard_id' => $dashboard->getID())); ?>"<?php endif; ?>>
    <ul class="dashboard_column column_1 <?php if ($dashboard->canEdit()) echo 'jsortable'; ?>" data-dashboard-id="<?php echo $dashboard->getID(); ?>" data-column="1">
        <div class="dashboard_indicator" style="display: none;"><?php echo image_tag('spinning_16.gif'); ?></div>
        <li class="dashboard_add_view_container">
            <div>
                <?php echo __('Click here to add a new view to this column'); ?>
            </div>
        </li>
        <?php foreach($dashboard->getViews() as $id => $view): ?>
            <?php if ($view->getColumn() != 1) continue; ?>
            <?php include_component('main/dashboardview', array('view' => $view, 'show' => false)); ?>
        <?php endforeach; ?>
    </ul>
    <ul class="dashboard_column column_2 <?php if ($dashboard->canEdit()) echo 'jsortable'; ?>" data-dashboard-id="<?php echo $dashboard->getID(); ?>" data-column="2">
        <div class="dashboard_indicator" style="display: none;"><?php echo image_tag('spinning_16.gif'); ?></div>
        <li class="dashboard_add_view_container">
            <div>
                <?php echo __('Click here to add a new view to this column'); ?>
            </div>
        </li>
        <?php foreach($dashboard->getViews() as $id => $view): ?>
            <?php if ($view->getColumn() != 2) continue; ?>
            <?php include_component('main/dashboardview', array('view' => $view, 'show' => false)); ?>
        <?php endforeach; ?>
    </ul>
</ul>
<?php include_component('main/dashboardjavascript'); ?>
<?php if (!$dashboard->countViews()): ?>
    <script>
        $$('.dashboard').each(function (elm) { elm.toggleClassName('editable');});
    </script>
<?php endif; ?>
