<div class="backdrop_box large">
	<div class="backdrop_detail_header">
		<?php echo __('Edit edition details'); ?>
	</div>
	<div id="backdrop_detail_content">
		<div style="clear: both; margin-top: 10px; margin-bottom: 10px; width: 790px; height: 30px;" class="tab_menu">
			<ul id="editions_menu">
				<li<?php if ($selected_section == 'general'): ?> class="selected"<?php endif; ?> id="edition_settings"><a href="javascript:void(0);" onclick="TBG.Main.Helpers.tabSwitcher('edition_settings', 'editions_menu');"><?php echo __('Details &amp; settings'); ?></a></li>
				<li<?php if ($selected_section == 'components'): ?> class="selected"<?php endif; ?> id="edition_components"><a href="javascript:void(0);" onclick="TBG.Main.Helpers.tabSwitcher('edition_components', 'editions_menu');"><?php echo __('Components'); ?></a></li>
				<li<?php if ($selected_section == 'team'): ?> class="selected"<?php endif; ?> id="edition_team"><a href="javascript:void(0);" onclick="TBG.Main.Helpers.tabSwitcher('edition_team', 'editions_menu');"><?php echo __('Team'); ?></a></li>
			</ul>
		</div>
		<div id="editions_menu_panes">
			<div id="edition_team_pane"<?php if ($selected_section != 'team'): ?> style="display: none;"<?php endif; ?>>
				<table style="width: 780px;" cellpadding=0 cellspacing=0>
					<tr class="hover_highlight">
						<td style="padding: 2px; width: 100px;">
							<b><?php echo __('Edition owner'); ?></b>
							<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
								<?php include_component('main/identifiableselector', array('html_id'		=> 'owned_by_change',
																						'header' 			=> __('Change / set owner'),
																						'clear_link_text'	=> __('Set owned by noone'),
																						'absolute'			=> true,
																						'style'				=> array('position' => 'absolute'),
																						'callback'		 	=> "TBG.Project.setUser('" . make_url('configure_edition_set_leadby', array('project_id' => $edition->getProject()->getID(), 'edition_id' => $edition->getID(), 'field' => 'owned_by', 'identifiable_type' => '%identifiable_type%', 'value' => '%identifiable_value%')) . "', 'owned_by');",
																						'base_id'			=> 'owned_by')); ?>
							<?php endif; ?>
						</td>
						<td style="<?php if (!$edition->hasOwner()): ?>display: none; <?php endif; ?>padding: 2px;" id="owned_by_name">
							<div style="width: 270px; display: <?php if ($edition->hasOwner()): ?>inline<?php else: ?>none<?php endif; ?>;" id="owned_by_name">
								<?php if ($edition->getOwnerType() == TBGIdentifiableClass::TYPE_USER): ?>
									<?php echo include_component('main/userdropdown', array('user' => $edition->getOwner())); ?>
								<?php elseif ($edition->getOwnerType() == TBGIdentifiableClass::TYPE_TEAM): ?>
									<?php echo include_component('main/teamdropdown', array('team' => $edition->getOwner())); ?>
								<?php endif; ?>
							</div>
						</td>
						<td style="<?php if ($edition->hasOwner()): ?>display: none; <?php endif; ?>padding: 2px;" class="faded_out" id="no_owned_by">
							<?php echo __('Noone'); ?>
						</td>
						<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
							<td style="padding: 2px; width: 100px; font-size: 0.9em; text-align: right;"><a href="javascript:void(0);" onclick="$('owned_by_change').toggle();" title="<?php echo __('Switch'); ?>"><?php echo __('Change / set'); ?></a></td>
						<?php endif; ?>
					</tr>
					<tr><td colspan="3" class="description faded_out" style="padding-bottom: 10px;"><?php echo __('The edition owner has total control over this edition and can edit information, settings, and anything about it'); ?></td></tr>
					<tr class="hover_highlight">
						<td style="padding: 2px; width: 100px;">
							<b><?php echo __('Lead by'); ?></b>
							<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
							<?php include_component('main/identifiableselector', array('html_id'		=> 'lead_by_change',
																					'header' 			=> __('Change / set leader'),
																					'clear_link_text'	=> __('Set lead by noone'),
																					'absolute'			=> true,
																					'style'				=> array('position' => 'absolute'),
																					'callback'		 	=> "TBG.Project.setUser('" . make_url('configure_edition_set_leadby', array('project_id' => $edition->getProject()->getID(), 'edition_id' => $edition->getID(), 'field' => 'lead_by', 'identifiable_type' => '%identifiable_type%', 'value' => '%identifiable_value%')) . "', 'lead_by');",
																					'base_id'			=> 'lead_by')); ?>
							<?php endif; ?>
						</td>
						<td style="<?php if (!$edition->hasLeader()): ?>display: none; <?php endif; ?>padding: 2px;" id="lead_by_name">
							<div style="width: 270px; display: <?php if ($edition->hasLeader()): ?>inline<?php else: ?>none<?php endif; ?>;" id="lead_by_name">
								<?php if ($edition->getLeaderType() == TBGIdentifiableClass::TYPE_USER): ?>
									<?php echo include_component('main/userdropdown', array('user' => $edition->getLeader())); ?>
								<?php elseif ($edition->getLeaderType() == TBGIdentifiableClass::TYPE_TEAM): ?>
									<?php echo include_component('main/teamdropdown', array('team' => $edition->getLeader())); ?>
								<?php endif; ?>
							</div>
						</td>
						<td style="<?php if ($edition->hasLeader()): ?>display: none; <?php endif; ?>padding: 2px;" class="faded_out" id="no_lead_by">
							<?php echo __('Noone'); ?>
						</td>
						<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
							<td style="padding: 2px; width: 100px; font-size: 0.9em; text-align: right;"><a href="javascript:void(0);" onclick="$('lead_by_change').toggle();" title="<?php echo __('Switch'); ?>"><?php echo __('Change / set'); ?></a></td>
						<?php endif; ?>
					</tr>
					<tr><td colspan="3" class="description faded_out" style="padding-bottom: 10px;"><?php echo __('If no default assignee is set on the component or project an issue is filed against, then the issue will automatically be assigned to the user you set here. This can be overridden when reporting the issue.'); ?></td></tr>
					<tr class="hover_highlight">
						<td style="padding: 2px; width: 100px;">
							<b><?php echo __('QA responsible'); ?></b>
							<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
								<?php include_component('main/identifiableselector', array('html_id'		=> 'qa_by_change',
																						'header' 			=> __('Change / set QA resp.'),
																						'clear_link_text'	=> __('Set QA resp. noone'),
																						'absolute'			=> true,
																						'style'				=> array('position' => 'absolute'),
																						'callback'		 	=> "TBG.Project.setUser('" . make_url('configure_edition_set_leadby', array('project_id' => $edition->getProject()->getID(), 'edition_id' => $edition->getID(), 'field' => 'qa_by', 'identifiable_type' => '%identifiable_type%', 'value' => '%identifiable_value%')) . "', 'qa_by');",
																						'base_id'			=> 'qa_by')); ?>
							<?php endif; ?>
						</td>
						<td style="<?php if (!$edition->hasQaResponsible()): ?>display: none; <?php endif; ?>padding: 2px;" id="qa_by_name">
							<div style="width: 270px; display: <?php if ($edition->hasQaResponsible()): ?>inline<?php else: ?>none<?php endif; ?>;" id="qa_by_name">
								<?php if ($edition->getQaResponsibleType() == TBGIdentifiableClass::TYPE_USER): ?>
									<?php echo include_component('main/userdropdown', array('user' => $edition->getQaResponsible())); ?>
								<?php elseif ($edition->getQaResponsibleType() == TBGIdentifiableClass::TYPE_TEAM): ?>
									<?php echo include_component('main/teamdropdown', array('team' => $edition->getQaResponsible())); ?>
								<?php endif; ?>
							</div>
						</td>
						<td style="<?php if ($edition->hasQaResponsible()): ?>display: none; <?php endif; ?>padding: 2px;" class="faded_out" id="no_qa_by">
							<?php echo __('Noone'); ?>
						</td>
						<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
							<td style="padding: 2px; width: 100px; font-size: 0.9em; text-align: right;"><a href="javascript:void(0);" onclick="$('qa_by_change').toggle();" title="<?php echo __('Switch'); ?>"><?php echo __('Change / set'); ?></a></td>
						<?php endif; ?>
					</tr>
				</table>
			</div>
			<div id="edition_settings_pane"<?php if ($selected_section != 'general'): ?> style="display: none;"<?php endif; ?>>
				<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
					<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_edition', array('project_id' => $edition->getProject()->getID(), 'edition_id' => $edition->getID(), 'mode' => 'general')); ?>" method="post" id="edition_settings_form" onsubmit="TBG.Project.Edition.submitSettings('<?php echo make_url('configure_project_edition', array('project_id' => $edition->getProject()->getID(), 'edition_id' => $edition->getID(), 'mode' => 'general')); ?>', <?php echo $edition->getID(); ?>);return false;">
						<table style="clear: both; width: 785px;" class="padded_table" cellpadding=0 cellspacing=0>
							<tr>
								<td style="width: 120px;"><label for="edition_name"><?php echo __('Name:') ?></label></td>
								<td style="width: auto; padding: 2px;"><input type="text" style="width: 100%;" name="edition_name" id="edition_name" value="<?php print $edition->getName(); ?>"></td>
							</tr>
							<tr>
								<td><label for="description"><?php echo __('Description:') ?></label></td>
								<td style="padding: 2px;"><input type="text" style="width: 100%;" name="description" id="description" value="<?php print $edition->getDescription(); ?>"></td>
							</tr>
							<tr>
								<td><label for="doc_url"><?php echo __('Documentation:') ?></label></td>
								<td style="padding: 2px;"><input type="text" style="width: 100%;" name="doc_url" id="doc_url" value="<?php print $edition->getDocumentationURL(); ?>"></td>
							</tr>
							<tr>
								<td><label for="locked"><?php echo __('Can report issues:'); ?></label></td>
								<td style="padding: 2px;">
									<select style="width: 70px;" name="locked" id="locked">
										<option value=0<?php print (!$edition->isLocked()) ? " selected" : ""; ?>><?php echo __('Yes'); ?></option>
										<option value=1<?php print ($edition->isLocked()) ? " selected" : ""; ?>><?php echo __('No'); ?></option>
									</select>
								</td>
							</tr>
							<tr>
								<td><label for="released"><?php echo __('Released:'); ?></label></td>
								<td style="padding: 2px;">
									<select style="width: 70px;" name="released" id="released">
										<option value=1<?php print ($edition->isReleased()) ? " selected" : ""; ?>><?php echo __('Yes'); ?></option>
										<option value=0<?php print (!$edition->isReleased()) ? " selected" : ""; ?>><?php echo __('No'); ?></option>
									</select>
								</td>
							</tr>
							<tr>
								<td><label for="planned_release"><?php echo __('Planned release'); ?></label></td>
								<td style="padding: 2px;">
									<select name="planned_release" id="planned_release" style="width: 70px;" onchange="bB = document.getElementById('planned_release'); cB = document.getElementById('release_day'); dB = document.getElementById('release_month'); eB = document.getElementById('release_year'); if (bB.value == '0') { cB.disabled = true; dB.disabled = true; eB.disabled = true; } else { cB.disabled = false; dB.disabled = false; eB.disabled = false; }">
										<option value=1<?php if ($edition->isPlannedReleased()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
										<option value=0<?php if (!$edition->isPlannedReleased()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
									</select>
								</td>
							</tr>
							<tr>
								<td><label for="release_month"><?php echo __('Release date'); ?></label></td>
								<td style="padding: 2px;">
									<select style="width: 85px;" name="release_month" id="release_month"<?php if (!$edition->isPlannedReleased()): ?> disabled<?php endif; ?>>
									<?php for($cc = 1;$cc <= 12;$cc++): ?>
										<option value=<?php print $cc; ?><?php print (($edition->getReleaseDateMonth() == $cc) ? " selected" : "") ?>><?php echo tbg_formatTime(mktime(0, 0, 0, $cc, 1), 15); ?></option>
									<?php endfor; ?>
									</select>
									<select style="width: 40px;" name="release_day" id="release_day"<?php if (!$edition->isPlannedReleased()): ?> disabled<?php endif; ?>>
									<?php for($cc = 1;$cc <= 31;$cc++): ?>
										<option value=<?php print $cc; ?><?php echo (($edition->getReleaseDateDay() == $cc) ? " selected" : "") ?>><?php echo $cc; ?></option>
									<?php endfor; ?>
									</select>
									<select style="width: 55px;" name="release_year" id="release_year"<?php if (!$edition->isPlannedReleased()): ?> disabled<?php endif; ?>>
									<?php for($cc = 2000;$cc <= (date("Y") + 5);$cc++): ?>
										<option value=<?php print $cc; ?><?php echo (($edition->getReleaseDateYear() == $cc) ? " selected" : "") ?>><?php echo $cc; ?></option>
									<?php endfor; ?>
									</select>
								</td>
							</tr>
						<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
							<tr>
								<td colspan="2" style="padding: 10px 0 10px 10px; text-align: right;">
									<div style="float: left; font-size: 13px; padding-top: 2px; font-style: italic;" class="config_explanation"><?php echo __('When you are done, click "%save%" to save your changes', array('%save%' => __('Save'))); ?></div>
									<div class="button button-green" style="float: right;">
										<input type="submit" id="edition_submit_settings_button" value="<?php echo __('Save'); ?>">
									</div>
									<span id="edition_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
								</td>
							</tr>
						<?php endif; ?>
						</table>
					</form>
				<?php endif; ?>
			</div>
			<div id="edition_components_pane" style="text-align: left;<?php if ($selected_section != 'components'): ?> display: none;<?php endif; ?>">
			<?php if ($edition->getProject()->isComponentsEnabled()): ?>
				<input id="edition_component_count" type="hidden" value="<?php echo count($edition->getComponents()); ?>">
				<table style="width: 785px;" cellpadding=0 cellspacing=0>
					<tr>
						<td style="<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> width: 395px; padding-right: 10px;<?php endif; ?> vertical-align: top;">
							<div style="width: 385px; padding: 3px; font-size: 12px; background-color: #FFF; border-bottom: 1px solid #DDD;"><b><?php echo __('Components for this edition'); ?></b></div>
							<div style="overflow: auto; height: 300px; overflow-x: hidden;">
							<table cellpadding=0 cellspacing=0 style="width: 100%;" id="edition_components">
							<?php foreach ($edition->getProject()->getComponents() as $aComponent): ?>
								<tr id="edition_component_<?php echo $aComponent->getID(); ?>"<?php if (!$edition->hasComponent($aComponent)): ?> style="display: none;"<?php endif; ?>>
									<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_components.png'); ?></td>
									<td style="width: auto; padding: 2px;"><?php print $aComponent; ?></td>
								<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
									<td style="width: 70px; text-align: right;"><a href="javascript:void(0);" onclick="TBG.Project.Edition.Component.remove('<?php echo make_url('configure_edition_remove_component', array('project_id' => $edition->getProject()->getID(), 'edition_id' => $edition->getID(), 'component_id' => $aComponent->getID())); ?>', <?php echo $aComponent->getID(); ?>);"><?php echo __('Remove'); ?>&nbsp;&gt;&gt;</a></td>
								<?php endif; ?>
								</tr>
							<?php endforeach; ?>
							<tr<?php if (count($edition->getComponents()) > 0): ?> style="display: none;"<?php endif; ?> id="edition_no_components">
								<td style="padding: 3px; color: #AAA;" colspan=3><?php echo __('This edition has no components'); ?></td>
							</tr>
							</table>
							</div>
						</td>
					<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
						<td style="width: 380px; vertical-align: top; padding-left: 10px;">
							<div style="width: 370px; padding: 3px; font-size: 12px; background-color: #FFF; border-bottom: 1px solid #DDD;"><b><?php echo __('Add an existing component'); ?></b></div>
							<div style="overflow: auto; height: 300px; overflow-x: hidden;">
							<table cellpadding=0 cellspacing=0 style="width: 100%;" id="edition_components">
							<?php foreach ($edition->getProject()->getComponents() as $aComponent): ?>
								<tr id="project_component_<?php echo $aComponent->getID(); ?>"<?php if ($edition->hasComponent($aComponent)): ?> style="display: none;"<?php endif; ?>>
								<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
									<td style="width: 50px; text-align: left;"><a href="javascript:void(0);" onclick="TBG.Project.Edition.Component.add('<?php echo make_url('configure_edition_add_component', array('project_id' => $edition->getProject()->getID(), 'edition_id' => $edition->getID(), 'component_id' => $aComponent->getID())); ?>', <?php echo $aComponent->getID(); ?>);">&lt;&lt;&nbsp;<?php echo __('Add'); ?></a></td>
								<?php endif; ?>
									<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_components.png'); ?></td>
									<td style="width: auto; padding: 2px;"><?php print $aComponent; ?></td>
								</tr>
							<?php endforeach; ?>
							<?php if (count($edition->getProject()->getComponents()) == 0): ?>
								<tr>
									<td style="padding: 3px; color: #AAA;" colspan=3><?php echo __('This project has no components'); ?></td>
								</tr>
							<?php endif; ?>
							</table>
							</div>
						</td>
					<?php endif; ?>
					</tr>
				</table>
				<div style="padding-top: 15px; font-size: 11px;"><?php echo __('You can only add existing project components. If this project does not have any components yet, go back to the project overview and add them there.'); ?></div>
			<?php else: ?>
				<div style="padding: 2px 5px 5px 5px;" class="faded_out"><?php echo __('This project does not use components'); ?>.<br><?php echo __('Components can be enabled in project settings'); ?>.</div>
			<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="backdrop_detail_footer">
		<?php echo javascript_link_tag(__('Close popup'), array('onclick' => 'TBG.Main.Helpers.Backdrop.reset();')); ?>
	</div>
</div>