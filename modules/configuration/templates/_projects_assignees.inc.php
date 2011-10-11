<?php 

	TBGContext::loadLibrary('ui'); 
	$assignees = $project->getAssignees(); 

?>
<div class="config_header" style="border-bottom: 0; margin-top: 0; padding-top: 0;"><b><?php echo __('Assigned users'); ?></b></div>
<?php if (count($assignees['users']) == 0): ?>
	<div style="padding-left: 5px; padding-top: 3px; color: #AAA;"><?php echo __('There are no users assigned to this project'); ?></div>
<?php else: ?>
	<table cellpadding=0 cellspacing=0 width="100%">
		<?php foreach ($assignees['users'] as $u_id => $assigns): ?>
			<tr id="assignee_user_<?php echo $u_id; ?>_row" class="hoverable">
				<td style="width: 20px;">
					<?php echo javascript_link_tag(image_tag('action_delete.png'), array('class' => 'image', 'onclick' => "TBG.Project.removeAssignee('".make_url('configure_project_remove_assignee', array('project_id' => $project->getID(), 'assignee_type' => TBGIdentifiableClass::TYPE_USER, 'assignee_id' => $u_id))."', 'user', {$u_id});", 'id' => 'assignee_user_'.$u_id.'_link')); ?>
					<?php echo image_tag('spinning_16.gif', array('id' => 'remove_assignee_user_'.$u_id.'_indicator', 'style' => 'float: left; display: none;')); ?>
				</td>
				<td style="vertical-align: top; font-size: 0.9em;">
					<?php echo include_component('main/userdropdown', array('user' => $u_id)); ?>
				</td>
				<td style="vertical-align: top; padding: 3px; font-size: 0.9em;">
					<?php //if (array_key_exists('projects', $assigns)): ?>
						<?php // <b><?php echo $project->getName(); </b>:&nbsp; ?>
						<?php $types_array = array(); ?>
						<?php foreach ($assigns as $type => $bool): ?>
							<?php $types_array[] = TBGProjectAssigneesTable::getTypeName($type); ?>
						<?php endforeach; ?>
						<?php echo join(', ', $types_array); ?><br>
					<?php //endif; ?>
					<?php /*if (array_key_exists('editions', $assigns)): ?>
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
					<?php endif;*/ ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>
<div class="config_header" style="border-bottom: 0;"><b><?php echo __('Assigned teams'); ?></b></div>
<?php if (count($assignees['teams']) == 0): ?>
	<div style="padding-left: 5px; padding-top: 3px; color: #AAA;"><?php echo __('There are no teams assigned to this project'); ?></div>
<?php else: ?>
	<table cellpadding=0 cellspacing=0 width="100%">
		<?php foreach ($assignees['teams'] as $c_id => $assigns): ?>
			<tr id="assignee_team_<?php echo $c_id; ?>_row" style="font-size: 0.9em;" class="hoverable">
				<td style="width: 20px;">
					<?php echo javascript_link_tag(image_tag('action_delete.png'), array('class' => 'image', 'onclick' => "TBG.Project.removeAssignee('".make_url('configure_project_remove_assignee', array('project_id' => $project->getID(), 'assignee_type' => TBGIdentifiableClass::TYPE_TEAM, 'assignee_id' => $c_id))."', 'team', {$c_id});", 'style' => 'float: left;', 'id' => 'assignee_team_'.$c_id.'_link')); ?>
					<?php echo image_tag('spinning_16.gif', array('id' => 'remove_assignee_team_'.$c_id.'_indicator', 'style' => 'float: left; display: none;')); ?>
				</td>
				<td style="vertical-align: top; font-size: 0.9em;">
					<?php echo include_component('main/teamdropdown', array('team' => $c_id)); ?>
				</td>
				<td style="vertical-align: top; padding: 3px; font-size: 0.9em;">
					<?php //if (array_key_exists('projects', $assigns)): ?>
						<?php // <b><?php echo $project->getName(); </b>:&nbsp; ?>
						<?php $types_array = array(); ?>
						<?php foreach ($assigns as $type => $bool): ?>
							<?php $types_array[] = TBGProjectAssigneesTable::getTypeName($type); ?>
						<?php endforeach; ?>
						<?php echo join(', ', $types_array); ?><br>
					<?php //endif; ?>
					<?php /*if (array_key_exists('editions', $assigns)): ?>
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
					<?php endif;*/ ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>