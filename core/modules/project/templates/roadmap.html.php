<?php

    $tbg_response->addBreadcrumb(__('Roadmap'), make_url('project_roadmap', array('project_key' => $selected_project->getKey())));
    $tbg_response->setTitle(__('"%project_name" roadmap', array('%project_name' => $selected_project->getName())));
    $tbg_response->addJavascript('excanvas');
    $tbg_response->addJavascript('jquery.flot');
    $tbg_response->addJavascript('jquery.flot.resize');
    $tbg_response->addJavascript('jquery.flot.dashes');
    $tbg_response->addJavascript('jquery.flot.time');
    include_component('project/projectheader', array('selected_project' => $selected_project, 'subpage' => __('Roadmap')));

?>
<div id="project_roadmap_page" class="<?php if ($mode == 'upcoming') echo 'upcoming'; ?> project_info_container">
    <div class="project_right_container" id="project_planning">
        <div class="project_right" id="project_roadmap_container">
            <?php if ($tbg_user->canManageProjectReleases($selected_project)): ?>
                <div class="planning_indicator" id="milestone_0_indicator" style="display: none;"><?php echo image_tag('spinning_30.gif'); ?></div>
                <div class="project_save_container" id="project_planning_action_strip">
                    <?php echo javascript_link_tag(__('New milestone'), array('class' => 'button button-silver', 'onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'milestone', 'project_id' => $selected_project->getId()))."');")); ?>
                    <?php echo image_tag('spinning_16.gif', array('id' => 'retrieve_indicator', 'class' => 'indicator', 'style' => 'display: none;')); ?>
                    <?php echo image_tag('icon-mono-settings.png', array('class' => 'dropper dropdown_link planning_board_settings_gear', 'id' => 'planning_board_settings_gear')); ?>
                    <ul class="more_actions_dropdown popup_box">
                        <li><?php echo javascript_link_tag(__('Sort milestones'), array('onclick' => "TBG.Project.Planning.toggleMilestoneSorting();")); ?></li>
                    </ul>
                </div>
                <div class="project_save_container" id="milestone-sort-actions">
                    <button class="button button-silver" id="milestone_sort_toggler_button" onclick="TBG.Project.Planning.toggleMilestoneSorting();"><?php echo __('Done sorting'); ?></button>
                </div>
            <?php endif; ?>
            <div id="project_roadmap" style="<?php if (isset($selected_milestone) && $selected_milestone instanceof \thebuggenie\core\entities\Milestone) echo 'display: none'; ?>">
                <?php if (count($milestones) == 0): ?>
                    <div style="padding: 15px; color: #AAA; font-size: 12px;"><?php echo __('There is no roadmap to be shown for this project, as it does not have any available milestones'); ?></div>
                <?php else: ?>
                    <div id="milestone_list" class="jsortable" data-sort-url="<?php echo make_url('project_sort_milestones', array('project_key' => $selected_project->getKey())); ?>">
                        <?php foreach ($milestones as $milestone): ?>
                            <?php include_component('project/milestonebox', array('milestone' => $milestone, 'include_counts' => true, 'include_buttons' => true)); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div id="milestone_details_overview" style="<?php if (!(isset($selected_milestone) && $selected_milestone instanceof \thebuggenie\core\entities\Milestone)) echo 'display: none'; ?>">
                <?php if (isset($selected_milestone) && $selected_milestone instanceof \thebuggenie\core\entities\Milestone) include_component('project/milestonedetails', array('milestone' => $selected_milestone)); ?>
            </div>
            <div id="milestone_details_loading_indicator" class="fullpage_backdrop" style="display: none;">
                <?php echo image_tag('spinning_30.gif'); ?>
            </div>
        </div>
    </div>
    <div class="project_left_container">
        <div class="project_left">
            <h3><?php echo __('Roadmap filters'); ?></h3>
            <ul class="simple_list">
                <li class="<?php if ($mode == 'upcoming') echo 'selected'; ?>"><a href="javascript:void(0);" onclick="TBG.Project.clearRoadmapFilters(); $('project_roadmap_page').addClassName('upcoming');TBG.Project.toggleLeftSelection(this);TBG.Project.showRoadmap();"><?php echo __('Upcoming roadmap'); ?></a></li>
                <li class="<?php if ($mode == 'all') echo 'selected'; ?>"><a href="javascript:void(0);" onclick="TBG.Project.clearRoadmapFilters(); TBG.Project.toggleLeftSelection(this);TBG.Project.showRoadmap();"><?php echo __('Include past milestones'); ?></a></li>
                <li><h3><?php echo __('Milestone details'); ?></h3></li>
                <?php foreach ($milestones as $milestone): ?>
                    <li id="roadmap_milestone_<?php echo $milestone->getID(); ?>_details_link" class="milestone_details_link <?php if ($milestone->isReached()) echo 'closed'; ?> <?php if ($mode == 'milestone' && isset($selected_milestone) && $selected_milestone instanceof \thebuggenie\core\entities\Milestone && $selected_milestone->getID() == $milestone->getID()) echo 'selected'; ?>"><a href="javascript:void(0);" onclick="TBG.Project.showMilestoneDetails('<?php echo make_url('project_roadmap_milestone_details', array('project_key' => $selected_project->getKey(), 'milestone_id' => $milestone->getID())); ?>', <?php echo $milestone->getID(); ?>, true); TBG.Project.toggleLeftSelection(this);"><?php echo $milestone->getName(); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <br style="clear: both;">
</div>
<script type="text/javascript">
    var TBG;

    require(['domReady', 'thebuggenie/tbg', 'jquery'], function (domReady, tbgjs, jQuery) {
        domReady(function () {
            TBG = tbgjs;
            var hash = window.location.hash;

            if (hash != undefined && hash.indexOf('roadmap_milestone_') == 1) {
                jQuery(hash + '_details_link').eq(0).find('> a:first-child').trigger('click');
            }
        });
    });
</script>
