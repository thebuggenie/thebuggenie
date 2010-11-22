<table style="width: 790px;" cellpadding=0 cellspacing=0>
	<tr class="hover_highlight">
		<td style="padding: 2px; width: 200px;">
			<b><?php echo __('Project owner'); ?></b>
			<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
				<?php include_component('main/identifiableselector', array(	'html_id'		=> 'owned_by_change',
																		'header' 			=> __('Change / set owner'),
																		'clear_link_text'	=> __('Set owned by noone'),
																		'style'				=> array('position' => 'absolute'),
																		'callback'		 	=> "setUser('" . make_url('configure_project_set_leadby', array('project_id' => $project->getID(), 'field' => 'owned_by', 'identifiable_type' => '%identifiable_type%', 'value' => '%identifiable_value%')) . "', 'owned_by');",
																		'base_id'			=> 'owned_by',
																		'absolute'			=> true)); ?>
			<?php endif; ?>
		</td>
		<td style="<?php if (!$project->hasOwner()): ?>display: none; <?php endif; ?>padding: 2px;" id="owned_by_name">
			<div style="width: 270px; display: <?php if ($project->hasOwner()): ?>inline<?php else: ?>none<?php endif; ?>;" id="owned_by_name">
				<?php if ($project->getOwnerType() == TBGIdentifiableClass::TYPE_USER): ?>
					<?php echo include_component('main/userdropdown', array('user' => $project->getOwner())); ?>
				<?php elseif ($project->getOwnerType() == TBGIdentifiableClass::TYPE_TEAM): ?>
					<?php echo include_component('main/teamdropdown', array('team' => $project->getOwner())); ?>
				<?php endif; ?>
			</div>
		</td>
		<td style="<?php if ($project->hasOwner()): ?>display: none; <?php endif; ?>padding: 2px;" class="faded_out" id="no_owned_by">
			<?php echo __('Noone'); ?>
		</td>
		<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
			<td style="padding: 2px; width: 100px; font-size: 0.9em; text-align: right;"><a href="javascript:void(0);" class="image" onclick="Effect.toggle('owned_by_change', 'appear', { duration: 0.5 }); return false;" title="<?php echo __('Change project owner'); ?>"><?php echo __('Change / set'); ?></a></td>
		<?php endif; ?>
	</tr>
	<tr><td colspan="3" class="config_explanation" style="padding-bottom: 10px;"><?php echo __('The project owner has total control over this project and can edit information, settings, and anything about it'); ?></td></tr>
	<tr class="hover_highlight">
		<td style="padding: 2px;">
			<b><?php echo __('Lead by'); ?></b>
			<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
				<?php include_component('main/identifiableselector', array(	'html_id'		=> 'lead_by_change',
																		'header' 			=> __('Change / set leader'),
																		'clear_link_text'	=> __('Set lead by noone'),
																		'style'				=> array('position' => 'absolute'),
																		'callback'		 	=> "setUser('" . make_url('configure_project_set_leadby', array('project_id' => $project->getID(), 'field' => 'lead_by', 'identifiable_type' => '%identifiable_type%', 'value' => '%identifiable_value%')) . "', 'lead_by');",
																		'base_id'			=> 'lead_by',
																		'absolute'			=> true)); ?>
			<?php endif; ?>
		</td>
		<td style="<?php if (!$project->hasLeader()): ?>display: none; <?php endif; ?>padding: 2px;" id="lead_by_name">
			<div style="width: 270px; display: <?php if ($project->hasLeader()): ?>inline<?php else: ?>none<?php endif; ?>;" id="lead_by_name">
				<?php if ($project->getLeaderType() == TBGIdentifiableClass::TYPE_USER): ?>
					<?php echo include_component('main/userdropdown', array('user' => $project->getLeader())); ?>
				<?php elseif ($project->getLeaderType() == TBGIdentifiableClass::TYPE_TEAM): ?>
					<?php echo include_component('main/teamdropdown', array('team' => $project->getLeader())); ?>
				<?php endif; ?>
			</div>
		</td>
		<td style="<?php if ($project->hasLeader()): ?>display: none; <?php endif; ?>padding: 2px;" class="faded_out" id="no_lead_by">
			<?php echo __('Noone'); ?>
		</td>
		<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
			<td style="padding: 2px; width: 100px; font-size: 0.9em; text-align: right;"><a href="javascript:void(0);" class="image" onclick="Effect.toggle('lead_by_change', 'appear', { duration: 0.5 }); return false;" title="<?php echo __('Change project leader'); ?>"><?php echo __('Change / set'); ?></a></td>
		<?php endif; ?>
	</tr>
	<tr><td colspan="3" class="config_explanation" style="padding-bottom: 10px;"><?php echo __('The project lead will automatically be assigned issues if workflows are disabled. This can be overriden by component and edition leads, as well as manually specifing when creating an issue.'); ?></td></tr>
	<tr class="hover_highlight">
		<td style="padding: 2px;">
			<b><?php echo __('QA responsible'); ?></b>
			<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
				<?php include_component('main/identifiableselector', array(	'html_id'		=> 'qa_by_change',
																		'header' 			=> __('Change / set QA responsible'),
																		'clear_link_text'	=> __('Set QA responsible to noone'),
																		'style'				=> array('position' => 'absolute'),
																		'callback'		 	=> "setUser('" . make_url('configure_project_set_leadby', array('project_id' => $project->getID(), 'field' => 'qa_by', 'identifiable_type' => '%identifiable_type%', 'value' => '%identifiable_value%')) . "', 'qa_by');",
																		'base_id'			=> 'qa_by',
																		'absolute'			=> true)); ?>
			<?php endif; ?>
		</td>
		<td style="<?php if (!$project->hasQaResponsible()): ?>display: none; <?php endif; ?>padding: 2px;" id="qa_by_name">
			<div style="width: 270px; display: <?php if ($project->hasQaResponsible()): ?>inline<?php else: ?>none<?php endif; ?>;" id="qa_by_name">
				<?php if ($project->getQaResponsibleType() == TBGIdentifiableClass::TYPE_USER): ?>
					<?php echo include_component('main/userdropdown', array('user' => $project->getQaResponsible())); ?>
				<?php elseif ($project->getQaResponsibleType() == TBGIdentifiableClass::TYPE_TEAM): ?>
					<?php echo include_component('main/teamdropdown', array('team' => $project->getQaResponsible())); ?>
				<?php endif; ?>
			</div>
		</td>
		<td style="<?php if ($project->hasQaResponsible()): ?>display: none; <?php endif; ?>padding: 2px;" class="faded_out" id="no_qa_by">
			<?php echo __('Noone'); ?>
		</td>
		<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
			<td style="padding: 2px; width: 100px; font-size: 0.9em; text-align: right;;"><a href="javascript:void(0);" class="image" onclick="Effect.toggle('qa_by_change', 'appear', { duration: 0.5 }); return false;" title="<?php echo __('Change Qa responsible'); ?>"><?php echo __('Change / set'); ?></a></td>
		<?php endif; ?>
	</tr>
</table>

<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_settings', array('project_id' => $project->getID())); ?>" method="post" onsubmit="submitProjectInfo('<?php echo make_url('configure_project_settings', array('project_id' => $project->getID())); ?>'); return false;" id="project_info">
<?php endif; ?>
<table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
		<tr>
			<td><label for="client"><?php echo __('Client'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<select name="client" id="client" style="width: 100%">
						<option value="0"<?php if ($project->getClient() == null): ?> selected<?php endif; ?>><?php echo __('No client'); ?></option>
						<?php foreach (TBGClient::getAll() as $client): ?>
							<option value=<?php echo $client->getID(); ?><?php if (($project->getClient() instanceof TBGClient) && $project->getClient()->getID() == $client->getID()): ?> selected<?php endif; ?>><?php echo $client->getName(); ?></option>
						<?php endforeach; ?>
					</select>
				<?php else: ?>
					<?php if ($project->getClient() == null): echo __('No client'); else: echo $project->getClient()->getName(); endif; ?>
				<?php endif; ?>
			</td>
		</tr>
	<tr>
		<td style="width: 200px;"><label for="project_name"><?php echo __('Project name'); ?></label></td>
		<td style="width: 580px;">
			<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
				<input type="text" name="project_name" id="project_name_input" value="<?php print $project->getName(); ?>" style="width: 100%;">
			<?php else: ?>
				<?php echo $project->getName(); ?>
			<?php endif; ?>
		</td>
	</tr>
	<?php TBGEvent::createNew('core', 'configuration/projectinfo', $project)->trigger(); ?>
	<tr>
		<td><label for="use_prefix"><?php echo __('Use prefix'); ?></label></td>
		<td>
			<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
				<select name="use_prefix" id="use_prefix" style="width: 70px;" onchange="if ($('use_prefix').getValue() == 1) { $('prefix').enable(); } else { $('prefix').disable(); }">
					<option value=1<?php if ($project->usePrefix()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
					<option value=0<?php if (!$project->usePrefix()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
				</select>
			<?php else: ?>
				<?php echo ($project->usePrefix()) ? __('Yes') : __('No'); ?>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td><label for="prefix"><?php echo __('Project prefix'); ?></label></td>
		<td>
			<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
				<input type="text" name="prefix" id="prefix" value="<?php print $project->getPrefix(); ?>" style="width: 70px;"<?php if (!$project->usePrefix()): ?> disabled<?php endif; ?>>
			<?php elseif ($project->hasPrefix()): ?>
				<?php echo $project->getPrefix(); ?>
			<?php else: ?>
				<span class="faded_out"><?php echo __('No prefix set'); ?></span>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('With prefix enabled, issues will be prefixed with the specified text. Ex: If you enable prefix and set "MYPROJ" as the prefix, issues will be named "MYPROJ-1", "MYPROJ-2", and so on. Without prefix enabled, issues will be name #1, #2, and so on.'); ?></td>
	</tr>
	<tr>
		<td><label for="description"><?php echo __('Project description'); ?></label></td>
		<td>
			<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
				<?php include_template('main/textarea', array('area_name' => 'description', 'area_id' => 'project_description_input', 'height' => '75px', 'width' => '100%', 'value' => $project->getDescription(), 'hide_hint' => true)); ?>
			<?php elseif ($project->hasDescription()): ?>
				<?php echo $project->getDescription(); ?>
			<?php else: ?>
				<span class="faded_out"><?php echo __('No description set'); ?></span>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td><label for="homepage"><?php echo __('Homepage'); ?></label></td>
		<td>
			<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
				<input type="text" name="homepage" id="homepage" value="<?php echo $project->getHomepage(); ?>" style="width: 100%;">
			<?php elseif ($project->hasHomepage()): ?>
				<a href="<?php echo $project->getHomepage(); ?>"><?php echo $project->getHomepage(); ?></a>
			<?php else: ?>
				<span class="faded_out"><?php echo __('No homepage set'); ?></span>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td><label for="doc_url"><?php echo __('Documentation URL'); ?></label></td>
		<td>
			<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
				<input type="text" name="doc_url" id="doc_url" value="<?php echo $project->getDocumentationURL(); ?>" style="width: 100%;">
			<?php elseif ($project->hasDocumentationURL()): ?>
				<a href="<?php echo $project->getDocumentationURL(); ?>"><?php echo $project->getDocumentationURL(); ?></a>
			<?php else: ?>
				<span class="faded_out"><?php echo __('No documentation URL provided'); ?></span>
			<?php endif; ?>
		</td>
	</tr>
<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
	<tr>
		<td colspan="2" style="padding: 10px 0 10px 10px; text-align: right;">
			<div style="float: left; font-size: 13px; padding-top: 2px; font-style: italic;" class="config_explanation"><?php echo __('When you are done, click "Save" to save your changes'); ?></div>
			<input type="submit" id="project_submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
			<span id="project_info_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
		</td>
	</tr>
<?php endif; ?>
</table>
<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
</form>
<?php endif; ?>