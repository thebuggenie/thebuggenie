<?php

	$tbg_response->addBreadcrumb(__('Team overview'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" project team', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<div style="padding: 5px;" id="project_team_overview">
				<div class="rounded_box borderless" style="margin: 5px; width: 45%; float: left;">
					<?php if (count($assignees['users']) == 0): ?>
						<div style="padding: 5px; color: #AAA; font-size: 12px;"><?php echo __('There are no users assigned to this project'); ?></div>
					<?php else: ?>
						<div class="header_div" style="margin-top: 0; padding-top: 5px;"><?php echo __('Assigned users'); ?></div>
						<table cellpadding=0 cellspacing=0 width="100%">
							<?php foreach ($assignees['users'] as $u_id => $assigns): ?>
								<tr>
									<td style="vertical-align: top; padding: 5px; width: 250px; border-bottom: 1px solid #F1F1F1;">
										<?php echo include_component('main/userdropdown', array('user' => $u_id)); ?>
									</td>
									<td style="vertical-align: top; padding-top: 3px; border-bottom: 1px solid #F1F1F1; padding-bottom: 7px;">
										<?php if (array_key_exists('projects', $assigns)): ?>
											<?php foreach ($assigns['projects'] as $p_id => $types): ?>
												<?php $types_array = array(); ?>
												<?php $theProject = TBGContext::factory()->TBGProject($p_id); ?>
												<?php if (array_key_exists('editions', $assigns) || array_key_exists('components', $assigns)): ?>
													<b><?php echo $theProject->getName(); ?></b>:&nbsp;
												<?php endif; ?>
												<?php foreach ($types as $type => $bool): ?>
													<?php $types_array[] = TBGProjectAssigneesTable::getTypeName($type); ?>
												<?php endforeach; ?>
												<?php echo join(', ', $types_array); ?><br>
											<?php endforeach; ?>
										<?php endif; ?>
										<?php if (array_key_exists('editions', $assigns)): ?>
											<?php foreach ($assigns['editions'] as $e_id => $types): ?>
												<?php $types_array = array(); ?>
												<?php $theEdition = TBGContext::factory()->TBGEdition($e_id); ?>
												<b><?php echo $theEdition->getName(); ?></b>:&nbsp;
												<?php foreach ($types as $type => $bool): ?>
													<?php $types_array[] = TBGProjectAssigneesTable::getTypeName($type); ?>
												<?php endforeach; ?>
												<?php echo join(', ', $types_array); ?><br>
											<?php endforeach; ?>
										<?php endif; ?>
										<?php if (array_key_exists('components', $assigns)): ?>
											<?php foreach ($assigns['components'] as $cp_id => $types): ?>
												<?php $types_array = array(); ?>
												<?php $theComponent = TBGContext::factory()->TBGComponent($cp_id); ?>
												<b><?php echo $theComponent->getName(); ?></b>:&nbsp;
												<?php foreach ($types as $type => $bool): ?>
													<?php $types_array[] = TBGProjectAssigneesTable::getTypeName($type); ?>
												<?php endforeach; ?>
												<?php echo join(', ', $types_array); ?><br>
											<?php endforeach; ?>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
					<?php endif; ?>
				</div>
				<div class="rounded_box borderless" style="margin: 5px; width: 45%; float: left;">
					<?php if (count($assignees['teams']) == 0): ?>
						<div style="padding: 5px; color: #AAA; font-size: 12px;"><?php echo __('There are no teams assigned to this project'); ?></div>
					<?php else: ?>
						<div class="header_div" style="margin-top: 0; padding-top: 5px;"><?php echo __('Assigned teams'); ?></div>
						<table cellpadding=0 cellspacing=0 width="100%">
							<?php foreach ($assignees['teams'] as $c_id => $assigns): ?>
								<tr>
									<td style="vertical-align: top; padding: 5px; width: 250px; border-bottom: 1px solid #F1F1F1;">
										<table cellpadding=0 cellspacing=0 width="100%">
											<?php echo include_component('main/teamdropdown', array('team' => $c_id)); ?>
										</table>
									</td>
									<td style="vertical-align: top; padding-top: 3px; border-bottom: 1px solid #F1F1F1; padding-bottom: 7px;">
										<?php if (array_key_exists('projects', $assigns)): ?>
											<?php foreach ($assigns['projects'] as $p_id => $types): ?>
												<?php $types_array = array(); ?>
												<?php $theProject = TBGContext::factory()->TBGProject($p_id); ?>
												<?php if (array_key_exists('editions', $assigns) || array_key_exists('components', $assigns)): ?>
													<b><?php echo $theProject->getName(); ?></b>:&nbsp;
												<?php endif; ?>
												<?php foreach ($types as $type => $bool): ?>
													<?php $types_array[] = TBGProjectAssigneesTable::getTypeName($type); ?>
												<?php endforeach; ?>
												<?php echo join(', ', $types_array); ?><br>
											<?php endforeach; ?>
										<?php endif; ?>
										<?php if (array_key_exists('editions', $assigns)): ?>
											<?php foreach ($assigns['editions'] as $e_id => $types): ?>
												<?php $types_array = array(); ?>
												<?php $theEdition = TBGContext::factory()->TBGEdition($e_id); ?>
												<b><?php echo $theEdition->getName(); ?></b>:&nbsp;
												<?php foreach ($types as $type => $bool): ?>
													<?php $types_array[] = TBGProjectAssigneesTable::getTypeName($type); ?>
												<?php endforeach; ?>
												<?php echo join(', ', $types_array); ?><br>
											<?php endforeach; ?>
										<?php endif; ?>
										<?php if (array_key_exists('components', $assigns)): ?>
											<?php foreach ($assigns['components'] as $cp_id => $types): ?>
												<?php $types_array = array(); ?>
												<?php $theComponent = TBGContext::factory()->TBGComponent($cp_id); ?>
												<b><?php echo $theComponent->getName(); ?></b>:&nbsp;
												<?php foreach ($types as $type => $bool): ?>
													<?php $types_array[] = TBGProjectAssigneesTable::getTypeName($type); ?>
												<?php endforeach; ?>
												<?php echo join(', ', $types_array); ?><br>
											<?php endforeach; ?>
										<?php endif; ?>
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