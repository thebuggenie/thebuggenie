<?php 

	TBGContext::loadLibrary('ui'); 
	$assignees = $project->getAssignees(); 

?>
<div class="config_header" style="margin-top: 20px;"><b><?php echo tbg_helpBrowserHelper('setup_build', image_tag('help.png', array('style' => "float: right;"))); ?><?php echo __('Assigned users'); ?></b></div>
<?php if (count($assignees['users']) == 0): ?>
	<div style="padding-left: 5px; padding-top: 3px; color: #AAA;"><?php echo __('There are no users assigned to this project'); ?></div>
<?php else: ?>
	<table cellpadding=0 cellspacing=0 width="100%">
		<?php foreach ($assignees['users'] as $u_id => $assigns): ?>
			<tr>
				<td style="vertical-align: top; width: 250px; border-bottom: 1px solid #F1F1F1;">
					<?php echo include_component('main/userdropdown', array('user' => $u_id)); ?>
				</td>
				<td style="vertical-align: top; padding-top: 3px; border-bottom: 1px solid #F1F1F1; padding-bottom: 7px;">
					<?php if (array_key_exists('projects', $assigns)): ?>
						<?php foreach ($assigns['projects'] as $p_id => $types): ?>
							<?php $types_array = array(); ?>
							<?php $theProject = TBGFactory::projectLab($p_id); ?>
							<b><?php echo $theProject->getName(); ?></b>:&nbsp;
							<?php foreach ($types as $type => $bool): ?>
								<?php $types_array[] = TBGProjectAssigneesTable::getTypeName($type); ?>
							<?php endforeach; ?>
							<?php echo join(', ', $types_array); ?><br>
						<?php endforeach; ?>
					<?php endif; ?>
					<?php if (array_key_exists('editions', $assigns)): ?>
						<?php foreach ($assigns['editions'] as $e_id => $types): ?>
							<?php $types_array = array(); ?>
							<?php $theEdition = TBGFactory::editionLab($e_id); ?>
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
							<?php $theComponent = TBGFactory::componentLab($cp_id); ?>
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
<div class="config_header" style="margin-top: 20px;"><b><?php echo tbg_helpBrowserHelper('setup_build', image_tag('help.png', array('style' => "float: right;"))); ?><?php echo __('Assigned teams'); ?></b></div>
<?php if (count($assignees['teams']) == 0): ?>
	<div style="padding-left: 5px; padding-top: 3px; color: #AAA;"><?php echo __('There are no teams assigned to this project'); ?></div>
<?php else: ?>
	<table cellpadding=0 cellspacing=0 width="100%">
		<?php foreach ($assignees['teams'] as $c_id => $assigns): ?>
			<tr>
				<td style="vertical-align: top; width: 250px; border-bottom: 1px solid #F1F1F1;">
					<table cellpadding=0 cellspacing=0 width="100%">
						<?php echo include_component('main/teamdropdown', array('team' => $c_id)); ?>
					</table>
				</td>
				<td style="vertical-align: top; padding-top: 3px; border-bottom: 1px solid #F1F1F1; padding-bottom: 7px;">
					<?php if (array_key_exists('projects', $assigns)): ?>
						<?php foreach ($assigns['projects'] as $p_id => $types): ?>
							<?php $types_array = array(); ?>
							<?php $theProject = TBGFactory::projectLab($p_id); ?>
							<b><?php echo $theProject->getName(); ?></b>:&nbsp;
							<?php foreach ($types as $type => $bool): ?>
								<?php $types_array[] = TBGProjectAssigneesTable::getTypeName($type); ?>
							<?php endforeach; ?>
							<?php echo join(', ', $types_array); ?><br>
						<?php endforeach; ?>
					<?php endif; ?>
					<?php if (array_key_exists('editions', $assigns)): ?>
						<?php foreach ($assigns['editions'] as $e_id => $types): ?>
							<?php $types_array = array(); ?>
							<?php $theEdition = TBGFactory::editionLab($e_id); ?>
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
							<?php $theComponent = TBGFactory::componentLab($cp_id); ?>
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