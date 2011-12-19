<h3><?php echo __('Project administration'); ?></h3>
<p class="faded_out" style="margin-bottom: 10px;">
	<?php echo __('These are the people in charge of different areas of the project. The project owner has total control over this project and can edit information, settings, and anything about it. The project leader does not have this power, but will be notified of anything happening in the project. The QA responsible role does not grant any special privileges, it is purely an informational setting.'); ?>
</p>
<table style="table-layout: fixed; width: 790px;" cellpadding=0 cellspacing=0>
	<tr class="hover_highlight">
		<td style="padding: 4px; width: 192px;">
			<b><?php echo __('Project owner'); ?></b>
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<?php include_component('main/identifiableselector', array(	'html_id'		=> 'owned_by_change',
																		'header' 			=> __('Change / set owner'),
																		'clear_link_text'	=> __('Set owned by noone'),
																		'style'				=> array('position' => 'absolute'),
																		'callback'		 	=> "TBG.Project.setUser('" . make_url('configure_project_set_leadby', array('project_id' => $project->getID(), 'field' => 'owned_by', 'identifiable_type' => 'user', 'value' => '%identifiable_value%')) . "', 'owned_by');",
																		'team_callback'	 	=> "TBG.Project.setUser('" . make_url('configure_project_set_leadby', array('project_id' => $project->getID(), 'field' => 'owned_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value%')) . "', 'owned_by');",
																		'base_id'			=> 'owned_by',
																		'absolute'			=> true,
																		'include_teams'		=> true)); ?>
			<?php endif; ?>
		</td>
		<td style="<?php if (!$project->hasOwner()): ?>display: none; <?php endif; ?>padding: 2px; width: 470px;" id="owned_by_name">
			<div style="width: 270px; display: <?php if ($project->hasOwner()): ?>inline<?php else: ?>none<?php endif; ?>;" id="owned_by_name">
				<?php if ($project->getOwner() instanceof TBGUser): ?>
					<?php echo include_component('main/userdropdown', array('user' => $project->getOwner())); ?>
				<?php elseif ($project->getOwner() instanceof TBGTeam): ?>
					<?php echo include_component('main/teamdropdown', array('team' => $project->getOwner())); ?>
				<?php endif; ?>
			</div>
		</td>
		<td style="<?php if ($project->hasOwner()): ?>display: none; <?php endif; ?>padding: 2px;" class="faded_out" id="no_owned_by">
			<?php echo __('Noone'); ?>
		</td>
		<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
			<td style="padding: 2px; width: 100px; font-size: 0.9em; text-align: right;"><a href="javascript:void(0);" class="image" onclick="Effect.toggle('owned_by_change', 'appear', { duration: 0.5 }); return false;" title="<?php echo __('Change project owner'); ?>"><?php echo __('Change / set'); ?></a></td>
		<?php endif; ?>
	</tr>
	<tr class="hover_highlight">
		<td style="padding: 4px;">
			<b><?php echo __('Lead by'); ?></b>
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<?php include_component('main/identifiableselector', array(	'html_id'		=> 'lead_by_change',
																		'header' 			=> __('Change / set leader'),
																		'clear_link_text'	=> __('Set lead by noone'),
																		'style'				=> array('position' => 'absolute'),
																		'callback'		 	=> "TBG.Project.setUser('" . make_url('configure_project_set_leadby', array('project_id' => $project->getID(), 'field' => 'lead_by', 'identifiable_type' => 'user', 'value' => '%identifiable_value%')) . "', 'lead_by');",
																		'team_callback'	 	=> "TBG.Project.setUser('" . make_url('configure_project_set_leadby', array('project_id' => $project->getID(), 'field' => 'lead_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value%')) . "', 'lead_by');",
																		'base_id'			=> 'lead_by',
																		'absolute'			=> true,
																		'include_teams'		=> true)); ?>
			<?php endif; ?>
		</td>
		<td style="<?php if (!$project->hasLeader()): ?>display: none; <?php endif; ?>padding: 2px;" id="lead_by_name">
			<div style="width: 270px; display: <?php if ($project->hasLeader()): ?>inline<?php else: ?>none<?php endif; ?>;" id="lead_by_name">
				<?php if ($project->getLeader() instanceof TBGUser): ?>
					<?php echo include_component('main/userdropdown', array('user' => $project->getLeader())); ?>
				<?php elseif ($project->getLeader() instanceof TBGTeam): ?>
					<?php echo include_component('main/teamdropdown', array('team' => $project->getLeader())); ?>
				<?php endif; ?>
			</div>
		</td>
		<td style="<?php if ($project->hasLeader()): ?>display: none; <?php endif; ?>padding: 2px;" class="faded_out" id="no_lead_by">
			<?php echo __('Noone'); ?>
		</td>
		<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
			<td style="padding: 2px; width: 100px; font-size: 0.9em; text-align: right;"><a href="javascript:void(0);" class="image" onclick="Effect.toggle('lead_by_change', 'appear', { duration: 0.5 }); return false;" title="<?php echo __('Change project leader'); ?>"><?php echo __('Change / set'); ?></a></td>
		<?php endif; ?>
	</tr>
<?php /*	<tr><td colspan="3" class="config_explanation" style="padding-bottom: 10px;"><?php echo __('The project lead will automatically be assigned issues. This can be overriden by component and edition leads, as well as manually specifing when creating an issue.'); ?></td></tr> */ ?>
	<tr class="hover_highlight">
		<td style="padding: 4px;">
			<b><?php echo __('QA responsible'); ?></b>
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<?php include_component('main/identifiableselector', array(	'html_id'		=> 'qa_by_change',
																		'header' 			=> __('Change / set QA responsible'),
																		'clear_link_text'	=> __('Set QA responsible to noone'),
																		'style'				=> array('position' => 'absolute'),
																		'callback'		 	=> "TBG.Project.setUser('" . make_url('configure_project_set_leadby', array('project_id' => $project->getID(), 'field' => 'qa_by', 'identifiable_type' => 'user', 'value' => '%identifiable_value%')) . "', 'qa_by');",
																		'team_callback'	 	=> "TBG.Project.setUser('" . make_url('configure_project_set_leadby', array('project_id' => $project->getID(), 'field' => 'qa_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value%')) . "', 'qa_by');",
																		'base_id'			=> 'qa_by',
																		'absolute'			=> true,
																		'include_teams'		=> true)); ?>
			<?php endif; ?>
		</td>
		<td style="<?php if (!$project->hasQaResponsible()): ?>display: none; <?php endif; ?>padding: 2px;" id="qa_by_name">
			<div style="width: 270px; display: <?php if ($project->hasQaResponsible()): ?>inline<?php else: ?>none<?php endif; ?>;" id="qa_by_name">
				<?php if ($project->getQaResponsible() instanceof TBGUser): ?>
					<?php echo include_component('main/userdropdown', array('user' => $project->getQaResponsible())); ?>
				<?php elseif ($project->getQaResponsible() instanceof TBGTeam): ?>
					<?php echo include_component('main/teamdropdown', array('team' => $project->getQaResponsible())); ?>
				<?php endif; ?>
			</div>
		</td>
		<td style="<?php if ($project->hasQaResponsible()): ?>display: none; <?php endif; ?>padding: 2px;" class="faded_out" id="no_qa_by">
			<?php echo __('Noone'); ?>
		</td>
		<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
			<td style="padding: 2px; width: 100px; font-size: 0.9em; text-align: right;"><a href="javascript:void(0);" class="image" onclick="Effect.toggle('qa_by_change', 'appear', { duration: 0.5 }); return false;" title="<?php echo __('Change Qa responsible'); ?>"><?php echo __('Change / set'); ?></a></td>
		<?php endif; ?>
	</tr>
</table>
<h3 style="margin-top: 20px;">
	<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
	<div class="button button-green" style="float: right; margin-top: -5px;" onclick="$('add_people_to_project_container').toggle();"><span><?php echo __('Add people to this project'); ?></span></div>
	<?php endif; ?>
	<?php echo __('Project team'); ?>
</h3>
<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
	<div class="rounded_box lightgrey" style="margin: 0 0 10px 0; width: 765px; padding: 5px 10px 5px 10px; display: none;" id="add_people_to_project_container">
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_find_assignee', array('project_id' => $project->getID())); ?>" method="post" onsubmit="TBG.Project.findDevelopers('<?php echo make_url('configure_project_find_assignee', array('project_id' => $project->getID())); ?>');return false;" id="find_dev_form">
			<table style="width: 100%; margin-top: 3px;" cellpadding=0 cellspacing=0 id="find_user">
				<tr>
					<td style="width: 200px; padding: 2px; text-align: left;"><label for="find_by"><?php echo __('Find team or user'); ?></label></td>
					<td style="width: auto; padding: 2px;"><input type="text" name="find_by" id="find_by" value="" style="width: 100%;"></td>
					<td style="width: 50px; padding: 2px; text-align: right;"><input type="submit" value="<?php echo __('Find'); ?>" style="width: 45px;"></td>
				</tr>
			</table>
		</form>
		<div style="padding: 10px 0 10px 0; display: none;" id="find_dev_indicator"><span style="float: left;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
		<div id="find_dev_results">
			<div class="faded_out" style="padding: 4px;"><?php echo __('To add people to this project, enter the name of a user or team to search for it'); ?></div>
		</div>
	</div>
<?php endif; ?>
<div id="assignees_list">
	<?php include_template('project/projects_assignees', array('project' => $project)); ?>
</div>