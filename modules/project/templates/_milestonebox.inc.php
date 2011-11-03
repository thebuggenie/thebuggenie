<div id="milestone_<?php echo $milestone->getID(); ?>" class="milestone_box">
	<?php include_template('project/milestoneboxheader', array('milestone' => $milestone)); ?>
	<div id="milestone_<?php echo $milestone->getID(); ?>_container" style="display: none;">
		<form action="<?php echo make_url('project_planning_update_milestone_issues', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID())); ?>" onsubmit="TBG.Project.Planning.updateIssues('<?php echo make_url('project_planning_update_milestone_issues', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID())); ?>', <?php echo $milestone->getID(); ?>);return false;" method="post" id="milestone_<?php echo $milestone->getID(); ?>_issues_form">
			<table cellpadding="0" cellspacing="0" class="milestone_issues">
				<thead>
					<tr>
						<?php if ($tbg_user->canEditProjectDetails(TBGContext::getCurrentProject())): ?>
							<th class="nosort" style="width: 20px; padding: 1px !important;"><input type="checkbox" onclick="TBG.Search.toggleCheckboxes(this);"></th>
						<?php endif; ?>
						<th><?php echo __('Issue'); ?></th>
						<th><?php echo __('Assigned to'); ?></th>
						<th><?php echo __('Status'); ?></th>
						<th><?php echo __('Priority'); ?></th>
						<th class="pointsandtime"><?php echo __('Est. hrs'); ?></th>
						<th class="pointsandtime"><?php echo __('Est. pts'); ?></th>
						<th class="pointsandtime"><?php echo __('Spent hrs'); ?></th>
						<th class="pointsandtime"><?php echo __('Spent pts'); ?></th>
					</tr>
				</thead>
				<tbody id="milestone_<?php echo $milestone->getID(); ?>_list" class="milestone_issues_container"></tbody> 
			</table>
		</form>
		<input type="hidden" id="milestone_<?php echo $milestone->getID(); ?>_id" value="<?php echo $milestone->getID(); ?>">
		<div class="faded_out" style="font-size: 13px;<?php if ($milestone->countIssues() > 0): ?> display: none;<?php endif; ?>" id="milestone_<?php echo $milestone->getID(); ?>_unassigned"><?php echo __('No issues assigned to this milestone'); ?></div>
	</div>
</div>