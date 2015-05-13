<?php

    $tbg_response->addBreadcrumb(__('Roadmap'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
    $tbg_response->setTitle(__('"%project_name" roadmap', array('%project_name' => $selected_project->getName())));
    $tbg_response->addJavascript('excanvas');
    $tbg_response->addJavascript('jquery.flot');
    $tbg_response->addJavascript('jquery.flot.resize');
    $tbg_response->addJavascript('jquery.flot.dashes');
    $tbg_response->addJavascript('jquery.flot.time');
    include_component('project/projectheader', array('selected_project' => $selected_project, 'subpage' => __('Roadmap')));

?>
<div id="project_roadmap_page" class="<?php if ($mode == 'upcoming') echo 'upcoming'; ?> project_info_container">
    <div class="project_right_container">
        <div class="project_right" id="project_roadmap_container">
            <div id="project_roadmap" style="<?php if (isset($selected_milestone) && $selected_milestone instanceof \thebuggenie\core\entities\Milestone) echo 'display: none'; ?>">
                <?php if (count($milestones) == 0): ?>
                    <div style="padding: 15px; color: #AAA; font-size: 12px;"><?php echo __('There is no roadmap to be shown for this project, as it does not have any available milestones'); ?></div>
                <?php else: ?>
                    <?php foreach ($milestones as $milestone): ?>
                        <?php include_component('agile/milestonebox', array('milestone' => $milestone, 'include_counts' => true, 'include_buttons' => false)); ?>
                    <?php endforeach; ?>
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
                    <li class="milestone_details_link <?php if ($milestone->isReached()) echo 'closed'; ?> <?php if ($mode == 'milestone' && isset($selected_milestone) && $selected_milestone instanceof \thebuggenie\core\entities\Milestone && $selected_milestone->getID() == $milestone->getID()) echo 'selected'; ?>"><a href="javascript:void(0);" onclick="TBG.Project.showMilestoneDetails('<?php echo make_url('project_roadmap_milestone_details', array('project_key' => $selected_project->getKey(), 'milestone_id' => $milestone->getID())); ?>', <?php echo $milestone->getID(); ?>, true); TBG.Project.toggleLeftSelection(this);"><?php echo $milestone->getName(); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <br style="clear: both;">
</div>
