<?php

	$tbg_response->setTitle(__('Manage projects - %project% - settings', array('%project%' => $theProject->getName())));
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<?php include_component('configleftmenu', array('selected_section' => 10)); ?>
<td valign="top">
	<?php include_template('configuration/project_header', array('theProject' => $theProject, 'mode' => 2)); ?>

	<div class="rounded_box lightgrey" style="margin: 5px 0px 5px 0px; width: 740px;" id="project_leads">
		<table style="width: 730px;" cellpadding=0 cellspacing=0>
			<tr class="canhover_dark">
				<td style="padding: 2px; width: 100px;">
					<b><?php echo __('Project owner'); ?></b>
					<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
						<?php include_component('main/identifiableselector', array(	'html_id'		=> 'owned_by_change',
																				'header' 			=> __('Change / set owner'),
																				'clear_link_text'	=> __('Set owned by noone'),
																				'style'				=> array('position' => 'absolute'),
																				'callback'		 	=> "setUser('" . make_url('configure_project_set_leadby', array('project_id' => $theProject->getID(), 'field' => 'owned_by', 'identifiable_type' => '%identifiable_type%', 'value' => '%identifiable_value%')) . "', 'owned_by');",
																				'base_id'			=> 'owned_by',
																				'absolute'			=> true)); ?>
					<?php endif; ?>
				</td>
				<td style="<?php if (!$theProject->hasOwner()): ?>display: none; <?php endif; ?>padding: 2px;" id="owned_by_name">
					<table style="width: 270px; display: <?php if ($theProject->hasOwner()): ?>inline<?php else: ?>none<?php endif; ?>;" cellpadding=0 cellspacing=0 id="owned_by_name">
						<?php if ($theProject->getOwnerType() == TBGIdentifiableClass::TYPE_USER): ?>
							<?php echo include_component('main/userdropdown', array('user' => $theProject->getOwner())); ?>
						<?php elseif ($theProject->getOwnerType() == TBGIdentifiableClass::TYPE_TEAM): ?>
							<?php echo include_component('main/teamdropdown', array('team' => $theProject->getOwner())); ?>
						<?php endif; ?>
					</table>
				</td>
				<td style="<?php if ($theProject->hasOwner()): ?>display: none; <?php endif; ?>padding: 2px;" class="faded_medium" id="no_owned_by">
					<?php echo __('Noone'); ?>
				</td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<td style="padding: 2px; width: 20px;"><a href="javascript:void(0);" class="image" onclick="Effect.toggle('owned_by_change', 'appear', { duration: 0.5 }); return false;" title="<?php echo __('Switch'); ?>"><?php echo image_tag('icon_switchassignee.png', array('alt' => __('Switch'), 'title' => __('Change'))); ?></a></td>
				<?php endif; ?>
			</tr>
			<tr><td colspan="3" class="description" style="padding-bottom: 10px;"><?php echo __('The project owner has total control over this project and can edit information, settings, and anything about it'); ?></td></tr>
			<tr class="canhover_dark">
				<td style="padding: 2px; width: 100px;">
					<b><?php echo __('Lead by'); ?></b>
					<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
						<?php include_component('main/identifiableselector', array(	'html_id'		=> 'lead_by_change',
																				'header' 			=> __('Change / set leader'),
																				'clear_link_text'	=> __('Set lead by noone'),
																				'style'				=> array('position' => 'absolute'),
																				'callback'		 	=> "setUser('" . make_url('configure_project_set_leadby', array('project_id' => $theProject->getID(), 'field' => 'lead_by', 'identifiable_type' => '%identifiable_type%', 'value' => '%identifiable_value%')) . "', 'lead_by');",
																				'base_id'			=> 'lead_by',
																				'absolute'			=> true)); ?>
					<?php endif; ?>
				</td>
				<td style="<?php if (!$theProject->hasLeader()): ?>display: none; <?php endif; ?>padding: 2px;" id="lead_by_name">
					<table style="width: 270px; display: <?php if ($theProject->hasLeader()): ?>inline<?php else: ?>none<?php endif; ?>;" cellpadding=0 cellspacing=0 id="lead_by_name">
						<?php if ($theProject->getLeaderType() == TBGIdentifiableClass::TYPE_USER): ?>
							<?php echo include_component('main/userdropdown', array('user' => $theProject->getLeader())); ?>
						<?php elseif ($theProject->getLeaderType() == TBGIdentifiableClass::TYPE_TEAM): ?>
							<?php echo include_component('main/teamdropdown', array('team' => $theProject->getLeader())); ?>
						<?php endif; ?>
					</table>
				</td>
				<td style="<?php if ($theProject->hasLeader()): ?>display: none; <?php endif; ?>padding: 2px;" class="faded_medium" id="no_lead_by">
					<?php echo __('Noone'); ?>
				</td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<td style="padding: 2px; width: 20px;"><a href="javascript:void(0);" class="image" onclick="Effect.toggle('lead_by_change', 'appear', { duration: 0.5 }); return false;" title="<?php echo __('Switch'); ?>"><?php echo image_tag('icon_switchassignee.png', array('alt' => __('Switch'), 'title' => __('Change'))); ?></a></td>
				<?php endif; ?>
			</tr>
			<tr class="canhover_dark">
				<td style="padding: 2px; width: 100px;">
					<b><?php echo __('QA responsible'); ?></b>
					<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
						<?php include_component('main/identifiableselector', array(	'html_id'		=> 'qa_by_change',
																				'header' 			=> __('Change / set QA responsible'),
																				'clear_link_text'	=> __('Set QA responsible to noone'),
																				'style'				=> array('position' => 'absolute'),
																				'callback'		 	=> "setUser('" . make_url('configure_project_set_leadby', array('project_id' => $theProject->getID(), 'field' => 'qa_by', 'identifiable_type' => '%identifiable_type%', 'value' => '%identifiable_value%')) . "', 'qa_by');",
																				'base_id'			=> 'qa_by',
																				'absolute'			=> true)); ?>
					<?php endif; ?>
				</td>
				<td style="<?php if (!$theProject->hasQaResponsible()): ?>display: none; <?php endif; ?>padding: 2px;" id="qa_by_name">
					<table style="width: 270px; display: <?php if ($theProject->hasQaResponsible()): ?>inline<?php else: ?>none<?php endif; ?>;" cellpadding=0 cellspacing=0 id="qa_by_name">
						<?php if ($theProject->getQaResponsibleType() == TBGIdentifiableClass::TYPE_USER): ?>
							<?php echo include_component('main/userdropdown', array('user' => $theProject->getQaResponsible())); ?>
						<?php elseif ($theProject->getQaResponsibleType() == TBGIdentifiableClass::TYPE_TEAM): ?>
							<?php echo include_component('main/teamdropdown', array('team' => $theProject->getQaResponsible())); ?>
						<?php endif; ?>
					</table>
				</td>
				<td style="<?php if ($theProject->hasQaResponsible()): ?>display: none; <?php endif; ?>padding: 2px;" class="faded_medium" id="no_qa_by">
					<?php echo __('Noone'); ?>
				</td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<td style="padding: 2px; width: 20px;"><a href="javascript:void(0);" class="image" onclick="Effect.toggle('qa_by_change', 'appear', { duration: 0.5 }); return false;" title="<?php echo __('Switch'); ?>"><?php echo image_tag('icon_switchassignee.png', array('alt' => __('Switch'), 'title' => __('Change'))); ?></a></td>
				<?php endif; ?>
			</tr>
		</table>
	</div>

	<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_settings', array('project_id' => $theProject->getID())); ?>" method="post" onsubmit="submitProjectSettings('<?php echo make_url('configure_project_settings', array('project_id' => $theProject->getID())); ?>'); return false;" id="project_settings">
	<?php endif; ?>
	<table style="clear: both; width: 700px;" class="padded_table" cellpadding=0 cellspacing=0>
		<tr>
			<td style="width: 200px;"><label for="project_name"><?php echo __('Project name'); ?></label></td>
			<td style="width: auto;">
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<input type="text" name="project_name" id="project_name" value="<?php print $theProject->getName(); ?>" style="width: 100%;">
				<?php else: ?>
					<?php echo $theProject->getName(); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><label for="use_prefix"><?php echo __('Use prefix'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<select name="use_prefix" id="use_prefix" style="width: 70px;" onchange="if ($('use_prefix').getValue() == 1) { $('prefix').enable(); } else { $('prefix').disable(); }">
						<option value=1<?php if ($theProject->usePrefix()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
						<option value=0<?php if (!$theProject->usePrefix()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
					</select>
				<?php else: ?>
					<?php echo ($theProject->usePrefix()) ? __('Yes') : __('No'); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><label for="prefix"><?php echo __('Prefix'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<input type="text" name="prefix" id="prefix" value="<?php print $theProject->getPrefix(); ?>" style="width: 70px;"<?php if (!$theProject->usePrefix()): ?> disabled<?php endif; ?>>
				<?php elseif ($theProject->hasPrefix()): ?>
					<?php echo $theProject->getPrefix(); ?>
				<?php else: ?>
					<span class="faded_light"><?php echo __('No prefix set'); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><label for="description"><?php echo __('Description'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<?php include_template('main/textarea', array('area_name' => 'description', 'height' => '75px', 'width' => '100%', 'value' => $theProject->getDescription(), 'hide_hint' => true)); ?>
				<?php elseif ($theProject->hasDescription()): ?>
					<?php echo $theProject->getDescription(); ?>
				<?php else: ?>
					<span class="faded_light"><?php echo __('No description set'); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><label for="homepage"><?php echo __('Homepage'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<input type="text" name="homepage" id="homepage" value="<?php echo $theProject->getHomepage(); ?>" style="width: 100%;">
				<?php elseif ($theProject->hasHomepage()): ?>
					<a href="<?php echo $theProject->getHomepage(); ?>"><?php echo $theProject->getHomepage(); ?></a>
				<?php else: ?>
					<span class="faded_light"><?php echo __('No homepage set'); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><label for="doc_url"><?php echo __('Documentation URL'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<input type="text" name="doc_url" id="doc_url" value="<?php echo $theProject->getDocumentationURL(); ?>" style="width: 100%;">
				<?php elseif ($theProject->hasDocumentationURL()): ?>
					<a href="<?php echo $theProject->getDocumentationURL(); ?>"><?php echo $theProject->getDocumentationURL(); ?></a>
				<?php else: ?>
					<span class="faded_light"><?php echo __('No documentation URL provided'); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="time_unit"><?php echo __('Time measuring'); ?></label>
			</td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<select name="time_unit" id="time_unit" style="width: 300px;">
						<option value=0<?php if ($theProject->getTimeUnit() == TBGProject::TIME_UNIT_HOURS): ?> selected<?php endif; ?>><?php echo __('Hours only'); ?></option>
						<option value=1<?php if ($theProject->getTimeUnit() == TBGProject::TIME_UNIT_HOURS_DAYS): ?> selected<?php endif; ?>><?php echo __('Hours and days'); ?></option>
						<option value=2<?php if ($theProject->getTimeUnit() == TBGProject::TIME_UNIT_HOURS_DAYS_WEEKS): ?> selected<?php endif; ?>><?php echo __('Hours, days and weeks'); ?></option>
						<option value=3<?php if ($theProject->getTimeUnit() == TBGProject::TIME_UNIT_DAYS): ?> selected<?php endif; ?>><?php echo __('Days only'); ?></option>
						<option value=4<?php if ($theProject->getTimeUnit() == TBGProject::TIME_UNIT_DAYS_WEEKS): ?> selected<?php endif; ?>><?php echo __('Days and weeks'); ?></option>
						<option value=5<?php if ($theProject->getTimeUnit() == TBGProject::TIME_UNIT_WEEKS): ?> selected<?php endif; ?>><?php echo __('Weeks only'); ?></option>
						<option value=6<?php if ($theProject->getTimeUnit() == TBGProject::TIME_UNIT_POINTS): ?> selected<?php endif; ?>><?php echo __('Points only'); ?></option>
						<option value=7<?php if ($theProject->getTimeUnit() == TBGProject::TIME_UNIT_POINTS_HOURS): ?> selected<?php endif; ?>><?php echo __('Points for issues, hours for tasks'); ?></option>
					</select>
				<?php else: ?>
					<?php

						switch ($theProject->getTimeUnit())
						{
							case TBGProject::TIME_UNIT_HOURS:
								echo __('Hours only');
								break;
							case TBGProject::TIME_UNIT_HOURS_DAYS:
								echo __('Hours and days');
								break;
							case TBGProject::TIME_UNIT_HOURS_DAYS_WEEKS:
								echo __('Hours, days and weeks');
								break;
							case TBGProject::TIME_UNIT_DAYS:
								echo __('Days only');
								break;
							case TBGProject::TIME_UNIT_DAYS_WEEKS:
								echo __('Days and weeks');
								break;
							case TBGProject::TIME_UNIT_WEEKS:
								echo __('Weeks only');
								break;
							case TBGProject::TIME_UNIT_POINTS:
								echo __('Points only');
								break;
							case TBGProject::TIME_UNIT_POINTS_HOURS:
								echo __('Points for issues, hours for tasks');
								break;
						}

					?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td class="config_explanation" colspan="2"><?php echo __('This is how the units you use for estimates and time used are being shown'); ?></td>
		</tr>
		<tr>
			<td><label for="defaultstatus"><?php echo __('Default status for new issues'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<select name="defaultstatus" id="defaultstatus" style="width: 200px;">
						<option value="0"><?php echo __('Not determined'); ?></option>
					<?php foreach ($statustypes as $aStatus): ?>
						<option style="color: <?php echo $aStatus->getItemdata(); ?>" value=<?php echo $aStatus->getID(); ?><?php if ($theProject->getDefaultStatusID() == $aStatus->getID()): ?> selected<?php endif; ?>><?php echo $aStatus->getName(); ?></option>
					<?php endforeach; ?>
					</select>
				<?php else: ?>
					<?php echo ($theProject->getDefaultStatus() instanceof TBGStatus) ? $theProject->getDefaultStatus()->getName() : __('Not determined'); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><label for="planned_release"><?php echo __('Allow freelancing'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<select name="allow_changing_without_working">
						<option value=1<?php if ($theProject->canChangeIssuesWithoutWorkingOnThem()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
						<option value=0<?php if (!$theProject->canChangeIssuesWithoutWorkingOnThem()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
					</select>
				<?php else: ?>
					<?php echo ($theProject->canChangeIssuesWithoutWorkingOnThem()) ? __('Yes') : __('No'); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td class="config_explanation" colspan="2"><?php echo __('Whether or not developers can change details on an issue without marking themselves as working on the issue'); ?></td>
		</tr>
		<tr>
			<td><label for="hrs_pr_day"><?php echo __('Hours per day'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<input type="text" name="hrs_pr_day" id="hrs_pr_day" style="width: 50px;" value="<?php echo $theProject->getHoursPerDay(); ?>">
				<?php else: ?>
					<?php echo $theProject->getHoursPerDay(); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><label for="planned_release"><?php echo __('Planned release'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<select name="planned_release" id="planned_release" style="width: 70px;" onchange="bB = $('planned_release'); cB = $('release_day'); dB = $('release_month'); eB = $('release_year'); if (bB.value == '0') { cB.disabled = true; dB.disabled = true; eB.disabled = true; } else { cB.disabled = false; dB.disabled = false; eB.disabled = false; }">
						<option value=1<?php if ($theProject->isPlannedReleased()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
						<option value=0<?php if (!$theProject->isPlannedReleased()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
					</select>
				<?php else: ?>
					<?php echo ($theProject->isPlannedReleased()) ? __('Yes') : __('No'); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><label for="release_month"><?php echo __('Release date'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<select style="width: 85px;" name="release_month" id="release_month"<?php if (!$theProject->isPlannedReleased()): ?> disabled<?php endif; ?>>
					<?php for ($cc = 1;$cc <= 12;$cc++): ?>
						<option value=<?php print $cc; ?><?php echo (($theProject->getReleaseDateMonth() == $cc) ? " selected" : "") ?>><?php echo tbg_formatTime(mktime(0, 0, 0, $cc, 1), 15); ?></option>
					<?php endfor; ?>
					</select>
					<select style="width: 40px;" name="release_day" id="release_day"<?php if (!$theProject->isPlannedReleased()): ?> disabled<?php endif; ?>>
					<?php for ($cc = 1;$cc <= 31;$cc++): ?>
						<option value=<?php print $cc; ?><?php echo (($theProject->getReleaseDateDay() == $cc) ? " selected" : "") ?>><?php echo $cc; ?></option>
					<?php endfor; ?>
					</select>
					<select style="width: 55px;" name="release_year" id="release_year"<?php if (!$theProject->isPlannedReleased()): ?> disabled<?php endif; ?>>
					<?php for ($cc = 2000;$cc <= (date("Y") + 5);$cc++): ?>
						<option value=<?php print $cc; ?><?php echo (($theProject->getReleaseDateYear() == $cc) ? " selected" : "") ?>><?php echo $cc; ?></option>
					<?php endfor; ?>
					</select>
				<?php elseif ($theProject->isPlannedReleased()): ?>
					<?php echo tbg_formatTime($theProject->getReleaseDate(), 14); ?>
				<?php else: ?>
					<span class="faded_medium"><?php echo __('No planned release date'); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><label for="released"><?php echo __('Released:'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<select name="released" id="released" style="width: 70px;">
						<option value=1<?php if ($theProject->isReleased()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
						<option value=0<?php if (!$theProject->isReleased()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
					</select>
				<?php else: ?>
					<?php echo ($theProject->isReleased()) ? __('Yes') : __('No'); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><label for="locked"><?php echo __('Allow issues to be reported'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<select name="locked" id="locked" style="width: 70px;">
						<option value=0<?php if (!$theProject->isLocked()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
						<option value=1<?php if ($theProject->isLocked()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
					</select>
				<?php else: ?>
					<?php echo (!$theProject->isLocked()) ? __('Yes') : __('No'); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><label for="enable_tasks"><?php echo __('Use tasks in issue reports'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<select name="enable_tasks" id="enable_tasks" style="width: 70px;">
						<option value=1<?php if ($theProject->isTasksEnabled()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
						<option value=0<?php if (!$theProject->isTasksEnabled()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
					</select>
				<?php else: ?>
					<?php echo ($theProject->isTasksEnabled()) ? __('Yes') : __('No'); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><label for="votes"><?php echo __('Allow voting for issues'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<select name="votes" id="votes" style="width: 70px;">
						<option value=1<?php if ($theProject->isVotesEnabled()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
						<option value=0<?php if (!$theProject->isVotesEnabled()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
					</select>
				<?php else: ?>
					<?php echo ($theProject->isVotesEnabled()) ? __('Yes') : __('No'); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td><label for="enable_builds"><?php echo __('Use builds'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<select name="enable_builds" id="enable_builds" style="width: 70px;">
						<option value=1<?php if ($theProject->isBuildsEnabled()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
						<option value=0<?php if (!$theProject->isBuildsEnabled()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
					</select>
				<?php else: ?>
					<?php echo ($theProject->isBuildsEnabled()) ? __('Yes') : __('No'); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td class="config_explanation" colspan="2"><?php echo __('If this project has regular new main- or test-releases, you should enable builds (releases)'); ?></td>
		</tr>
		<tr>
			<td><label for="enable_editions"><?php echo __('Use editions'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<select name="enable_editions" id="enable_editions" style="width: 70px;">
						<option value=1<?php if ($theProject->isEditionsEnabled()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
						<option value=0<?php if (!$theProject->isEditionsEnabled()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
					</select>
				<?php else: ?>
					<?php echo ($theProject->isEditionsEnabled()) ? __('Yes') : __('No'); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td class="config_explanation" colspan="2"><?php echo __('If the project has more than one edition which differ in features or capabilities, you should enable editions'); ?></td>
		</tr>
		<tr>
			<td><label for="enable_components"><?php echo __('Use components'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<select name="enable_components" id="enable_components" style="width: 70px;">
						<option value=1<?php if ($theProject->isComponentsEnabled()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
						<option value=0<?php if (!$theProject->isComponentsEnabled()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
					</select>
				<?php else: ?>
					<?php echo ($theProject->isComponentsEnabled()) ? __('Yes') : __('No'); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td class="config_explanation" colspan="2" style="padding-bottom: 10px;"><?php echo __('If the project consists of several easily identifiable sub-parts, you should enable components'); ?></td>
		</tr>
		<tr>
			<td><label for="descr_template"><?php echo __('Description template'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<?php include_template('main/textarea', array('area_name' => 'descr_template', 'height' => '75px', 'width' => '100%', 'value' => ($theProject->getDescrTemplate()))); ?>
				<?php elseif ($theProject->getDescrTemplate() != ''): ?>
					<?php echo tbg_parse_text($theProject->getDescrTemplate()); ?>
				<?php else: ?>
					<span class="faded_light"><?php echo __('No template provided'); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td class="config_explanation" colspan="2" style="padding-bottom: 10px;"><?php echo __('To help users structure the description field of their issue, enter some predefined content to appear in the field, acting as a template'); ?></td>
		</tr>
		<tr>
			<td><label for="repro_template"><?php echo __('Reproduction steps template'); ?></label></td>
			<td>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<?php include_template('main/textarea', array('area_name' => 'repro_template', 'height' => '75px', 'width' => '100%', 'value' => ($theProject->getReproTemplate()))); ?>
				<?php elseif ($theProject->getReproTemplate() != ''): ?>
					<?php echo tbg_parse_text($theProject->getReproTemplate()); ?>
				<?php else: ?>
					<span class="faded_light"><?php echo __('No template provided'); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td class="config_explanation" colspan="2" style="padding-bottom: 10px;"><?php echo __('To help users structure the reproduction steps field of their issue, enter some predefined content to appear in the field, acting as a template'); ?></td>
		</tr>
	</table>
	<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
		<div class="rounded_box mediumgrey" style="margin: 5px 0px 5px 0px; width: 700px; height: 23px; padding: 5px 10px 5px 10px;">
			<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "Save" when you are done, to save your changes'); ?></div>
			<input type="submit" id="project_submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
			<span id="project_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
		</div>
	<?php endif; ?>
	<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
	</form>
	<?php endif; ?>
</td>
</tr>
</table>
