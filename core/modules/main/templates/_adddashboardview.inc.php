<?php

    use thebuggenie\core\entities\Dashboard,
        thebuggenie\core\entities\DashboardView;

?>
<div class="backdrop_box large" id="add_dashboard_views">
    <div class="backdrop_detail_header"><?php echo __('Add dashboard view'); ?></div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="project_left">
            <ul class="simple_list" id="add_dashboard_views_menu">
                <li class="selected"><a href="javascript:void(0);" onclick="TBG.Main.Dashboard.toggleMenu(this);" data-section="information"><?php echo __('Project information'); ?></a></li>
                <?php if ($dashboard->getType() == Dashboard::TYPE_PROJECT): ?>
                        <li><a href="javascript:void(0);" onclick="TBG.Main.Dashboard.toggleMenu(this);" data-section="statistics"><?php echo __('Statistics'); ?></a></li>
    <?php endif; ?>
                <li><a href="javascript:void(0);" onclick="TBG.Main.Dashboard.toggleMenu(this);" data-section="predefined_searches"><?php echo __('Predefined searches'); ?></a></li>
                <li><a href="javascript:void(0);" onclick="TBG.Main.Dashboard.toggleMenu(this);" data-section="custom_searches"><?php echo __('Saved searches'); ?></a></li>
            </ul>
        </div>
        <div class="available_views_container" data-column="<?php echo $column; ?>" data-dashboard-id="<?php echo $dashboard->getID(); ?>">
            <ul class="available_views_list" data-section="information">
                <?php foreach ($views['info'] as $type => $details): ?>
                        <?php foreach ($details as $subtype => $detail): ?>
                            <?php include_component('main/adddashboardview_view', array('icon_type' => 'info', 'type' => $type, 'subtype' => $subtype, 'detail' => $detail)); ?>
                        <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
                <?php if ($dashboard->getType() == Dashboard::TYPE_PROJECT): ?>
                    <ul class="available_views_list" data-section="statistics" style="display: none;">
                        <?php foreach ($views['statistics'] as $type => $details): ?>
                            <?php foreach ($details as $subtype => $detail): ?>
                                <?php include_component('main/adddashboardview_view', array('icon_type' => 'statistics', 'type' => $type, 'subtype' => $subtype, 'detail' => $detail)); ?>
                            <?php endforeach; ?>
                    <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
            <ul class="available_views_list" data-section="predefined_searches" style="display: none;">
                <?php foreach ($views['searches'] as $type => $details): ?>
                        <?php foreach ($details as $subtype => $detail): ?>
                            <?php include_component('main/adddashboardview_view', array('icon_type' => 'searches', 'type' => $type, 'subtype' => $subtype, 'detail' => $detail)); ?>
                        <?php endforeach; ?>
    <?php endforeach; ?>
            </ul>
            <ul class="available_views_list" data-section="custom_searches" style="display: none;">
                <?php foreach ($savedsearches as $type => $searches): ?>
                        <?php foreach ($searches as $index => $search): ?>
                            <?php include_component('main/adddashboardview_view', array('icon_type' => 'searches', 'type' => DashboardView::VIEW_SAVED_SEARCH, 'subtype' => $search->getID(), 'detail' => array('title' => $search->getTitle(), 'description' => ($search->getDescription()) ? $search->getDescription() : __('Show a list of issues matching the saved search %searchname', array('%searchname' => $search->getTitle()))))); ?>
                        <?php endforeach; ?>
    <?php endforeach; ?>
            </ul>
        </div>
        <div class="backdrop_detail_footer">
            <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();" class="button button-silver"><?php echo __('Done'); ?></a>
        </div>
    </div>
</div>
