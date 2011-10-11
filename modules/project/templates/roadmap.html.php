<?php

	$tbg_response->addBreadcrumb(__('Roadmap'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" roadmap', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<div id="project_roadmap">
				<?php if (count($milestones) == 0): ?>
					<div style="padding: 15px; color: #AAA; font-size: 12px;"><?php echo __('There is no roadmap to be shown for this project, as it does not have any available milestones'); ?></div>
				<?php else: ?>
					<?php foreach ($milestones as $milestone): ?>
						<div class="roadmap_milestone" id="roadmap_milestone_<?php echo $milestone->getID(); ?>">
							<div class="roadmap_header">
								<?php echo $milestone->getName(); ?>
								<div class="roadmap_dates" id="milestone_<?php echo $milestone->getID(); ?>_date_string"><?php echo $milestone->getDateString(); ?></div>
							</div>
							<div class="roadmap_percentbar">
								<div class="percentcontainer">
									<?php include_template('main/percentbar', array('rounded' => true, 'percent' => $milestone->getPercentComplete(), 'height' => 22, 'id' => 'milestone_'.$milestone->getID().'_percent')); ?>
								</div>
								<div class="roadmap_percentdescription">
									<?php if ($milestone->isSprint()): ?>
										<?php if ($milestone->countClosedIssues() == 1): ?>
											<?php echo __('%num_closed% story (%closed_points% pts) closed of %num_assigned% (%assigned_points% pts) assigned', array('%num_closed%' => '<b id="milestone_'.$milestone->getID().'_closed_issues">'.$milestone->countClosedIssues().'</b>', '%closed_points%' => '<i id="milestone_'.$milestone->getID().'_closed_points">'.$milestone->getPointsSpent().'</i>', '%num_assigned%' => '<b id="milestone_'.$milestone->getID().'_assigned_issues">'.$milestone->countIssues().'</b>', '%assigned_points%' => '<i id="milestone_'.$milestone->getID().'_assigned_points">'.$milestone->getPointsEstimated().'</i>')); ?>
										<?php else: ?>
											<?php echo __('%num_closed% stories (%closed_points% pts) closed of %num_assigned% (%assigned_points% pts) assigned', array('%num_closed%' => '<b id="milestone_'.$milestone->getID().'_closed_issues">'.$milestone->countClosedIssues().'</b>', '%closed_points%' => '<i id="milestone_'.$milestone->getID().'_closed_points">'.$milestone->getPointsSpent().'</i>', '%num_assigned%' => '<b id="milestone_'.$milestone->getID().'_assigned_issues">'.$milestone->countIssues().'</b>', '%assigned_points%' => '<i id="milestone_'.$milestone->getID().'_assigned_points">'.$milestone->getPointsEstimated().'</i>')); ?>
										<?php endif; ?>
									<?php else: ?>
										<?php echo __('%num_closed% issue(s) closed of %num_assigned% assigned', array('%num_closed%' => '<b id="milestone_'.$milestone->getID().'_closed_issues">'.$milestone->countClosedIssues().'</b>', '%num_assigned%' => '<b id="milestone_'.$milestone->getID().'_assigned_issues">'.$milestone->countIssues().'</b>')); ?>
									<?php endif; ?>
								</div>
							</div>
							<div class="roadmap_actions">
								<?php echo javascript_link_tag(image_tag('view_list_details.png', array('title' => __('Show issues'))), array('onclick' => "TBG.Project.Milestone.toggle('".make_url('project_roadmap_milestone_issues', array('project_key' => $selected_project->getKey(), 'milestone_id' => $milestone->getID()))."', ".$milestone->getID().");", 'class' => 'button-icon button button-silver')); ?>
								<?php echo javascript_link_tag(image_tag('refresh.png', array('title' => __('Update (regenerate) milestone details'))), array('onclick' => "TBG.Project.Milestone.refresh('".make_url('project_roadmap_milestone_refresh', array('project_key' => $selected_project->getKey(), 'milestone_id' => $milestone->getID()))."', ".$milestone->getID().");", 'class' => 'button-icon button button-silver')); ?>
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
							<div class="roadmap_issues" id="milestone_<?php echo $milestone->getID(); ?>_issues"<?php if (!$milestone->isCurrent()): ?> style="display: none;"<?php endif; ?>>
								<?php if ($milestone->isCurrent()): ?>
									<?php include_template('project/milestoneissues', array('milestone' => $milestone)); ?>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>