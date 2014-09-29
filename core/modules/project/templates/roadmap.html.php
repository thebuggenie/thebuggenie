<?php

    $tbg_response->addBreadcrumb(__('Roadmap'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
    $tbg_response->setTitle(__('"%project_name" roadmap', array('%project_name' => $selected_project->getName())));
    $tbg_response->addJavascript(make_url('home').'js/excanvas.js', false);
    $tbg_response->addJavascript(make_url('home').'js/jquery.flot.min.js', false);
    $tbg_response->addJavascript(make_url('home').'js/jquery.flot.resize.min.js', false);
    $tbg_response->addJavascript(make_url('home').'js/jquery.flot.dashes.js', false);
    $tbg_response->addJavascript(make_url('home').'js/jquery.flot.time.min.js', false);
    include_template('project/projectheader', array('selected_project' => $selected_project, 'subpage' => __('Roadmap')));

?>
<div id="project_roadmap_page" class="<?php if ($mode == 'upcoming') echo 'upcoming'; ?> project_info_container">
    <div class="project_right_container">
        <div class="project_right" id="project_roadmap_container">
            <div id="project_roadmap" style="<?php if (isset($selected_milestone) && $selected_milestone instanceof TBGMilestone) echo 'display: none'; ?>">
                <?php if (count($milestones) == 0): ?>
                    <div style="padding: 15px; color: #AAA; font-size: 12px;"><?php echo __('There is no roadmap to be shown for this project, as it does not have any available milestones'); ?></div>
                <?php else: ?>
                    <?php foreach ($milestones as $milestone): ?>
                        <?php /*
                        <div class="roadmap_milestone <?php if ($milestone->isReached()) echo 'closed'; ?>" id="roadmap_milestone_<?php echo $milestone->getID(); ?>">
                            <h4>
                                <div class="button-group">
                                    <?php echo javascript_link_tag(image_tag('view_list_details.png', array('title' => __('Show issues'))), array('onclick' => "TBG.Project.Milestone.toggle('".make_url('project_roadmap_milestone_issues', array('project_key' => $selected_project->getKey(), 'milestone_id' => $milestone->getID()))."', ".$milestone->getID().");", 'class' => 'button-icon button button-silver')); ?>
                                    <?php echo javascript_link_tag(image_tag('refresh.png', array('title' => __('Update (regenerate) milestone details'))), array('onclick' => "TBG.Project.Milestone.refresh('".make_url('project_roadmap_milestone_refresh', array('project_key' => $selected_project->getKey(), 'milestone_id' => $milestone->getID()))."', ".$milestone->getID().");", 'class' => 'button-icon button button-silver')); ?>
                                    <?php echo link_tag(make_url('project_milestone_details', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID())), image_tag('show_sprint_details.png'), array('title' => __('Show milestone details'), 'class' => 'button button-icon button-silver')); ?>
                                </div>
                                <?php echo link_tag(make_url('project_milestone_details', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID())), $milestone->getName(), array('title' => __('Show milestone details'))); ?>
                                <div class="roadmap_dates" id="milestone_<?php echo $milestone->getID(); ?>_date_string"><?php echo $milestone->getDateString(); ?></div>
                            </h4>
                            <div class="roadmap_percentbar">
                                <div class="percentcontainer">
                                    <?php include_template('main/percentbar', array('rounded' => true, 'percent' => $milestone->getPercentComplete(), 'height' => 22, 'id' => 'milestone_'.$milestone->getID().'_percent')); ?>
                                </div>
                                <div class="roadmap_percentdescription">
                                    <?php if ($milestone->isSprint()): ?>
                                        <?php if ($milestone->countClosedIssues() == 1): ?>
                                            <?php echo __('%num_closed story (%closed_points pts) closed of %num_assigned (%assigned_points pts) assigned', array('%num_closed' => '<b id="milestone_'.$milestone->getID().'_closed_issues">'.$milestone->countClosedIssues().'</b>', '%closed_points' => '<i id="milestone_'.$milestone->getID().'_closed_points">'.$milestone->getPointsSpent().'</i>', '%num_assigned' => '<b id="milestone_'.$milestone->getID().'_assigned_issues">'.$milestone->countIssues().'</b>', '%assigned_points' => '<i id="milestone_'.$milestone->getID().'_assigned_points">'.$milestone->getPointsEstimated().'</i>')); ?>
                                        <?php else: ?>
                                            <?php echo __('%num_closed stories (%closed_points pts) closed of %num_assigned (%assigned_points pts) assigned', array('%num_closed' => '<b id="milestone_'.$milestone->getID().'_closed_issues">'.$milestone->countClosedIssues().'</b>', '%closed_points' => '<i id="milestone_'.$milestone->getID().'_closed_points">'.$milestone->getPointsSpent().'</i>', '%num_assigned' => '<b id="milestone_'.$milestone->getID().'_assigned_issues">'.$milestone->countIssues().'</b>', '%assigned_points' => '<i id="milestone_'.$milestone->getID().'_assigned_points">'.$milestone->getPointsEstimated().'</i>')); ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php echo __('%num_closed issue(s) closed of %num_assigned assigned', array('%num_closed' => '<b id="milestone_'.$milestone->getID().'_closed_issues">'.$milestone->countClosedIssues().'</b>', '%num_assigned' => '<b id="milestone_'.$milestone->getID().'_assigned_issues">'.$milestone->countIssues().'</b>')); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <br style="clear: both;">
                            <div id="milestone_<?php echo $milestone->getID(); ?>_changed" class="milestones_indicator" style="display: none;">
                                <?php echo __('Milestone details have changed. To see the updated list of issues, click the "Show issues" icon'); ?>...
                                <button onclick="$('milestone_<?php echo $milestone->getID(); ?>_changed').hide();"><?php echo __('Ok'); ?></button>
                            </div>
                            <div id="milestone_<?php echo $milestone->getID(); ?>_indicator" class="milestones_indicator" style="display: none;">
                                <?php echo image_tag('spinning_32.gif'); ?>
                                <?php echo __('Please wait'); ?>...
                            </div>
                            <div class="roadmap_issues" id="milestone_<?php echo $milestone->getID(); ?>_issues" style="display: none;"></div>
                        </div>
                        */ ?>
                        <?php include_component('milestonebox', array('milestone' => $milestone, 'include_counts' => true, 'include_buttons' => false)); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div id="milestone_details_overview" style="<?php if (!(isset($selected_milestone) && $selected_milestone instanceof TBGMilestone)) echo 'display: none'; ?>">
                <?php if (isset($selected_milestone) && $selected_milestone instanceof TBGMilestone) include_component('project/milestonedetails', array('milestone' => $selected_milestone)); ?>
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
                    <li class="milestone_details_link <?php if ($milestone->isReached()) echo 'closed'; ?> <?php if ($mode == 'milestone' && isset($selected_milestone) && $selected_milestone instanceof TBGMilestone && $selected_milestone->getID() == $milestone->getID()) echo 'selected'; ?>"><a href="javascript:void(0);" onclick="TBG.Project.showMilestoneDetails('<?php echo make_url('project_roadmap_milestone_details', array('project_key' => $selected_project->getKey(), 'milestone_id' => $milestone->getID())); ?>', <?php echo $milestone->getID(); ?>); TBG.Project.toggleLeftSelection(this);"><?php echo $milestone->getName(); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <br style="clear: both;">
</div>
