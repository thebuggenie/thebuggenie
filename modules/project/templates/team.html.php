<?php

	$tbg_response->addBreadcrumb(__('Team overview'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" project team', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<div style="padding: 5px;" id="project_team_overview">
				<div class="rounded_box borderless" style="margin: 5px; width: 45%; float: left;">
					<?php if (count($assigned_users) == 0): ?>
						<div style="padding: 5px; color: #AAA; font-size: 12px;"><?php echo __('There are no users assigned to this project'); ?></div>
					<?php else: ?>
						<div class="header_div" style="margin-top: 0; padding-top: 5px;"><?php echo __('Assigned users'); ?></div>
						<table cellpadding=0 cellspacing=0 width="100%">
							<?php foreach ($assigned_users as $user): ?>
								<tr>
									<td style="vertical-align: top; padding: 5px; width: 250px; border-bottom: 1px solid #F1F1F1;">
										<?php echo include_component('main/userdropdown', array('user' => $user)); ?>
									</td>
									<td style="vertical-align: top; padding-top: 3px; border-bottom: 1px solid #F1F1F1; padding-bottom: 7px;">
										<?php $roles = $selected_project->getRolesForUser($user); ?>
										<?php $role_names = array(); ?>
										<?php foreach ($roles as $role) $role_names[] = $role->getName(); ?>
										<?php echo join(',', $role_names); ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
					<?php endif; ?>
				</div>
				<div class="rounded_box borderless" style="margin: 5px; width: 45%; float: left;">
					<?php if (count($assigned_teams) == 0): ?>
						<div style="padding: 5px; color: #AAA; font-size: 12px;"><?php echo __('There are no teams assigned to this project'); ?></div>
					<?php else: ?>
						<div class="header_div" style="margin-top: 0; padding-top: 5px;"><?php echo __('Assigned teams'); ?></div>
						<table cellpadding=0 cellspacing=0 width="100%">
							<?php foreach ($assigned_teams as $team): ?>
								<tr>
									<td style="vertical-align: top; padding: 5px; width: 250px; border-bottom: 1px solid #F1F1F1;">
										<table cellpadding=0 cellspacing=0 width="100%">
											<?php echo include_component('main/teamdropdown', array('team' => $team)); ?>
										</table>
									</td>
									<td style="vertical-align: top; padding-top: 3px; border-bottom: 1px solid #F1F1F1; padding-bottom: 7px;">
										<?php $roles = $selected_project->getRolesForTeam($team); ?>
										<?php $role_names = array(); ?>
										<?php foreach ($roles as $role) $role_names[] = $role->getName(); ?>
										<?php echo join(',', $role_names); ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
					<?php endif; ?>
				</div>
				<br style="clear: both;">
			</div>
		</td>
	</tr>
</table>