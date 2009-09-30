<?php

	$bugs_response->setTitle(__('Manage projects - %project% - %edition%', array('%project%' => $theEdition->getProject()->getName(), '%edition%' => $theEdition->getName())));
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<?php include_template('config_leftmenu', array('selected_section' => 10)); ?>
<td valign="top">
<script type="text/javascript" src="<?php echo BUGScontext::getTBGPath(); ?>js/config/projects_ajax.js"></script>
	<table style="width: 100%" cellpadding=0 cellspacing=0>
		<tr>
			<td style="padding-right: 10px;">
				<div class="configheader" style="width: 750px;"><?php echo __('Configure projects'); ?></div>
	            <div style="height: 60px; position: absolute;">
	            	<?php echo bugs_failureStrip('', '', 'message_failed', true); ?>
	            	<?php echo bugs_successStrip(__('The component has been added'), '', 'message_component_added', true); ?>
	            	<?php echo bugs_successStrip(__('The build has been added'), __('Remember to give other users/groups permission access to it if necessary.'), 'message_build_added', true, false); ?>
	            	<?php echo bugs_successStrip(__('The component name has been changed'), '', 'message_component_name_changed', true); ?>
	            	<?php echo bugs_successStrip(__('The build details has been updated'), '', 'message_build_details_updated', true); ?>
	            	<?php echo bugs_successStrip(__('The selected build has been deleted'), '', 'message_build_deleted', true); ?>
	            	<?php echo bugs_successStrip(__('The selected build has been added to open issues based on your selections'), '', 'message_build_added_to_open_issues', true); ?>
	            	<?php echo bugs_successStrip(__('The selected build has been marked as &laquo;Released&raquo;'), '', 'message_build_release', true); ?>
	            	<?php echo bugs_successStrip(__('The selected build has been marked as &laquo;Not released&raquo;'), '', 'message_build_retract', true); ?>
	            	<?php echo bugs_successStrip(__('The selected build is now locked for new issue reports'), '', 'message_build_lock', true); ?>
	            	<?php echo bugs_successStrip(__('The selected build is no longer locked for new issue reports'), '', 'message_build_unlock', true); ?>
	            	<?php echo bugs_successStrip(__('The selected build is now the initial default when reporting new issues for this edition'), '', 'message_build_markdefault', true); ?>
	            	<?php echo bugs_successStrip(__('Your changes has been saved'), '', 'message_changes_saved', true); ?>
	            </div>
				<p style="padding-top: 5px;">
					<?php echo __('More information about projects, editions, builds and components is available from the %bugs_online_help%.', array('%bugs_online_help%' => bugs_helpBrowserHelper('config_projects', __('The Bug Genie online help')))); ?>
				</p>
			</td>
		</tr>
	</table>
	<div class="rounded_box" style="margin: 15px 0px 15px 0px; width: 700px;">
		<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
		<div class="xboxcontent" style="vertical-align: middle; padding: 5px 10px 5px 10px; font-size: 12px;">
		<?php echo __('You are now looking at %project_name% &gt;&gt; %edition_name%', array('%project_name%' => '<b>'.$theEdition->getProject()->getName().'</b>', '%edition_name%' => '<span id="edition_name_span" style="font-weight: bold;">'.$theEdition->getName().'</span>')); ?><br>
		<b><?php echo link_tag(make_url('configure_project_editions_components', array('project_id' => $theEdition->getProject()->getID())), '&lt;&lt;&nbsp;'.__('Go back to project overview')); ?></b><?php echo __('%something% or %something_else%', array('%something%' => '', '%something_else%' => '')); ?><br>
		<b><?php echo link_tag(make_url('configure_projects'), '&lt;&lt;&nbsp;'.__('Go back to list of projects')); ?></b>
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>

	<div style="clear: both; margin-top: 10px; margin-bottom: 10px; width: 750px; clear: both; height: 30px;" class="tab_menu">
		<ul>
			<li<?php if ($selected_section == 'general'): ?> class="selected"<?php endif; ?> id="tab_edition_settings"><a href="javascript:void(0);" onclick="switchEditionTab('settings');"><?php echo image_tag('cfg_icon_editiondetails.png', array('style' => 'float: left;')).__('Details &amp; settings'); ?></a></li>
			<li<?php if ($selected_section == 'components'): ?> class="selected"<?php endif; ?> id="tab_edition_components"><a href="javascript:void(0);" onclick="switchEditionTab('components');"><?php echo image_tag('cfg_icon_components.png', array('style' => 'float: left;')).__('Components'); ?></a></li>
			<li<?php if ($selected_section == 'releases'): ?> class="selected"<?php endif; ?> id="tab_edition_builds"><a href="javascript:void(0);" onclick="switchEditionTab('builds');"><?php echo image_tag('cfg_icon_builds.png', array('style' => 'float: left;')).__('Builds / releases'); ?></a></li>
		</ul>
	</div>
	
	<div id="edition_settings"<?php if ($selected_section != 'general'): ?> style="display: none;"<?php endif; ?>>
		<div class="rounded_box" style="margin: 5px 0px 5px 0px; width: 700px;">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="vertical-align: middle;">
				<table style="width: 680px;" cellpadding=0 cellspacing=0>
					<tr class="canhover_dark">
						<td style="padding: 2px; width: 100px;">
							<b><?php echo __('Edition owner'); ?></b>
							<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
								<span id="edit_owner" style="display: none;">
								<?php include_template('main/identifiableselector', 
													   array('user_title' => __('Set lead by a user'),
													   		 'team_title' => __('Set lead by a team'),
													   		 'setuser_url' => make_url('configure_project_set_leadby', array('project_id' => $theEdition->getID(), 'lead_type' => 1)),
													   		 'setteam_url' => make_url('configure_project_set_leadby', array('project_id' => $theEdition->getID(), 'lead_type' => 2)),
															 'setuser_update_div' => 'project_leadby',
													   		 'setuser_update_url' => make_url('configure_project_get_leadby', array('project_id' => $theEdition->getID())),
													   		 'setteam_update_div' => 'project_leadby',
													   		 'setteam_update_url' => make_url('configure_project_get_leadby', array('project_id' => $theEdition->getID())),
													   		 'container_span' => 'edit_owner')); ?>
								</span>
							<?php endif; ?>
						</td>
						<td style="padding: 2px;" id="project_owner">
							<?php if ($theEdition->hasOwner()): ?>
								<?php echo $theEdition->getOwner()->getName(); ?>
							<?php else: ?>
								<span class="faded_dark"><?php echo __('None'); ?></span>
							<?php endif; ?>
						</td>
						<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
							<td style="padding: 2px; width: 20px;"><a href="javascript:void(0);" class="image" onclick="Effect.toggle('edit_owner', 'appear', { duration: 0.5 }); return false;" title="<?php echo __('Switch'); ?>"><?php echo image_tag('icon_switchassignee.png', array('alt' => __('Switch'), 'title' => __('Change'))); ?></a></td>
						<?php endif; ?>
					</tr>
					<tr><td colspan="3" class="description" style="padding-bottom: 10px;"><?php echo __('The edition owner has total control over this edition and can edit information, settings, and anything about it'); ?></td></tr>
					<tr class="canhover_dark">
						<td style="padding: 2px; width: 100px;">
							<b><?php echo __('Lead by'); ?></b>
							<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
								<span id="edit_leadby" style="display: none;">
								<?php bugs_AJAXuserteamselector(__('Set lead by a user'), 
																__('Set lead by a team'),
																'config.php?module=core&section=10&p_id=' . $theEdition->getID() . '&edit_settings=true&setleadby=true&lead_type=1', 
																'config.php?module=core&section=10&p_id=' . $theEdition->getID() . '&edit_settings=true&setleadby=true&lead_type=2',
																'project_leadby', 
																'config.php?module=core&section=10&p_id=' . $theEdition->getID() . '&edit_settings=true&getleadby=true',
																'project_leadby', 
																'config.php?module=core&section=10&p_id=' . $theEdition->getID() . '&edit_settings=true&getleadby=true',
																'edit_leadby'
																); ?>
								</span>
							<?php endif; ?>
						</td>
						<td style="padding: 2px;" id="project_leadby">
							<?php if ($theEdition->hasLeader()): ?>
								<?php echo $theEdition->getLeader()->getName(); ?>
							<?php else: ?>
								<span class="faded_dark"><?php echo __('None'); ?></span>
							<?php endif; ?>
						</td>
						<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
							<td style="padding: 2px; width: 20px;"><a href="javascript:void(0);" class="image" onclick="Effect.toggle('edit_leadby', 'appear', { duration: 0.5 }); return false;" title="<?php echo __('Switch'); ?>"><?php echo image_tag('icon_switchassignee.png', array('alt' => __('Switch'), 'title' => __('Change'))); ?></a></td>
						<?php endif; ?>
					</tr>
					<tr class="canhover_dark">
						<td style="padding: 2px; width: 100px;">
							<b><?php echo __('QA responsible'); ?></b>
							<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
								<span id="edit_qa" style="display: none;">
								<?php bugs_AJAXuserteamselector(__('Set lead by a user'), 
																__('Set lead by a team'),
																'config.php?module=core&section=10&p_id=' . $theEdition->getID() . '&edit_settings=true&setleadby=true&lead_type=1', 
																'config.php?module=core&section=10&p_id=' . $theEdition->getID() . '&edit_settings=true&setleadby=true&lead_type=2',
																'project_leadby', 
																'config.php?module=core&section=10&p_id=' . $theEdition->getID() . '&edit_settings=true&getleadby=true',
																'project_leadby', 
																'config.php?module=core&section=10&p_id=' . $theEdition->getID() . '&edit_settings=true&getleadby=true',
																'edit_qa'
																); ?>
								</span>
							<?php endif; ?>
						</td>
						<td style="padding: 2px;" id="project_qa">
							<?php if ($theEdition->hasQA()): ?>
								<?php echo $theEdition->getQA()->getName(); ?>
							<?php else: ?>
								<span class="faded_dark"><?php echo __('None'); ?></span>
							<?php endif; ?>
						</td>
						<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
							<td style="padding: 2px; width: 20px;"><a href="javascript:void(0);" class="image" onclick="Effect.toggle('edit_qa', 'appear', { duration: 0.5 }); return false;" title="<?php echo __('Switch'); ?>"><?php echo image_tag('icon_switchassignee.png', array('alt' => __('Change'), 'title' => __('Change'))); ?></a></td>
						<?php endif; ?>
					</tr>
				</table>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
		<table style="width: 700px;" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: auto; padding-right: 5px; vertical-align: top;">
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_edition', array('project_id' => $theEdition->getProject()->getID(), 'edition_id' => $theEdition->getID(), 'mode' => 'general')); ?>" method="post" id="edition_settings" onsubmit="submitEditionSettings('<?php echo make_url('configure_project_edition', array('project_id' => $theEdition->getProject()->getID(), 'edition_id' => $theEdition->getID(), 'mode' => 'general')); ?>');return false;">
					<table cellpadding=0 cellspacing=0 style="width: 100%; margin-top: 5px;">
						<tr>
							<td style="width: 120px;"><label for="edition_name"><?php echo __('Name:') ?></label></td>
							<td style="width: auto; padding: 2px;"><input type="text" style="width: 100%;" name="edition_name" id="edition_name" value="<?php print $theEdition->getName(); ?>"></td>
						</tr>
						<tr>
							<td><label for="description"><?php echo __('Description:') ?></label></td>
							<td style="padding: 2px;"><input type="text" style="width: 100%;" name="description" id="description" value="<?php print $theEdition->getDescription(); ?>"></td>
						</tr>
						<tr>
							<td><label for="doc_url"><?php echo __('Documentation:') ?></label></td>
							<td style="padding: 2px;"><input type="text" style="width: 100%;" name="doc_url" id="doc_url" value="<?php print $theEdition->getDocumentationURL(); ?>"></td>
						</tr>
						<tr>
							<td><label for="locked"><?php echo __('Can report issues:'); ?></label></td>
							<td style="padding: 2px;">
								<select style="width: 70px;" name="locked" id="locked">
									<option value=0<?php print (!$theEdition->isLocked()) ? " selected" : ""; ?>><?php echo __('Yes'); ?></option>
									<option value=1<?php print ($theEdition->isLocked()) ? " selected" : ""; ?>><?php echo __('No'); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td><label for="released"><?php echo __('Released:'); ?></label></td>
							<td style="padding: 2px;">
								<select style="width: 70px;" name="released" id="released">
									<option value=1<?php print ($theEdition->isReleased()) ? " selected" : ""; ?>><?php echo __('Yes'); ?></option>
									<option value=0<?php print (!$theEdition->isReleased()) ? " selected" : ""; ?>><?php echo __('No'); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td><label for="planned_release"><?php echo __('Planned release'); ?></label></td>
							<td style="padding: 2px;">
								<select name="planned_release" id="planned_release" style="width: 70px;" onchange="bB = document.getElementById('planned_release'); cB = document.getElementById('release_day'); dB = document.getElementById('release_month'); eB = document.getElementById('release_year'); if (bB.value == '0') { cB.disabled = true; dB.disabled = true; eB.disabled = true; } else { cB.disabled = false; dB.disabled = false; eB.disabled = false; }">
									<option value=1<?php if ($theEdition->isPlannedReleased()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
									<option value=0<?php if (!$theEdition->isPlannedReleased()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td><label for="release_month"><?php echo __('Release date'); ?></label></td>
							<td style="padding: 2px;">
								<select style="width: 85px;" name="release_month" id="release_month"<?php if (!$theEdition->isPlannedReleased()): ?> disabled<?php endif; ?>>
								<?php for($cc = 1;$cc <= 12;$cc++): ?>
									<option value=<?php print $cc; ?><?php print (($theEdition->getReleaseDateMonth() == $cc) ? " selected" : "") ?>><?php echo bugs_formatTime(mktime(0, 0, 0, $cc, 1), 15); ?></option>
								<?php endfor; ?>
								</select>
								<select style="width: 40px;" name="release_day" id="release_day"<?php if (!$theEdition->isPlannedReleased()): ?> disabled<?php endif; ?>>
								<?php for($cc = 1;$cc <= 31;$cc++): ?>
									<option value=<?php print $cc; ?><?php echo (($theEdition->getReleaseDateDay() == $cc) ? " selected" : "") ?>><?php echo $cc; ?></option>
								<?php endfor; ?>
								</select>
								<select style="width: 55px;" name="release_year" id="release_year"<?php if (!$theEdition->isPlannedReleased()): ?> disabled<?php endif; ?>>
								<?php for($cc = 2000;$cc <= (date("Y") + 5);$cc++): ?>
									<option value=<?php print $cc; ?><?php echo (($theEdition->getReleaseDateYear() == $cc) ? " selected" : "") ?>><?php echo $cc; ?></option>
								<?php endfor; ?>
								</select>
							</td>
						</tr>
					</table>
					<div class="rounded_box" style="margin: 5px 0px 5px 0px; width: 700px;">
						<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
						<div class="xboxcontent" style="vertical-align: middle; height: 23px; padding: 5px 10px 5px 10px;">
							<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "Save" when you are done, to save your changes'); ?></div>
							<input type="submit" id="edition_submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
							<span id="edition_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
						</div>
						<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
					</div>
					</form>
				<?php endif; ?>
				</td>
			</tr>
		</table>
	</div>
	<div id="edition_components"<?php if ($selected_section != 'components'): ?> style="display: none;"<?php endif; ?>>
	<?php if ($theEdition->getProject()->isComponentsEnabled()): ?>
		<table style="width: 700px;" cellpadding=0 cellspacing=0>
			<tr>
				<td style="<?php if ($access_level == configurationActions::ACCESS_FULL): ?> width: 350px; padding-right: 10px;<?php endif; ?> vertical-align: top;">
					<div style="width: 340px; padding: 3px; font-size: 12px; background-color: #FFF; border-bottom: 1px solid #DDD;"><b><?php echo __('Components for this edition'); ?></b></div>
					<table cellpadding=0 cellspacing=0 style="width: 100%;" id="edition_components">
					<?php foreach ($theEdition->getProject()->getComponents() as $aComponent): ?>
						<tr id="edition_component_<?php echo $aComponent->getID(); ?>"<?php if (!$theEdition->hasComponent($aComponent)): ?> style="display: none;"<?php endif; ?>>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_components.png'); ?></td>
							<td style="width: auto; padding: 2px;"><?php print $aComponent; ?></td>
						<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
							<td style="width: 70px; text-align: right;"><a href="javascript:void(0);" onclick="removeEditionComponent('<?php echo make_url('configure_edition_remove_component', array('project_id' => $theEdition->getProject()->getID(), 'edition_id' => $theEdition->getID(), 'component_id' => $aComponent->getID())); ?>', <?php echo $aComponent->getID(); ?>);"><?php echo __('Remove'); ?>&nbsp;&gt;&gt;</a></td>
						<?php endif; ?>
						</tr>
					<?php endforeach; ?>
					<?php if (count($theEdition->getComponents()) == 0): ?>
						<tr>
							<td style="padding: 3px; color: #AAA;" colspan=3><?php echo __('This edition has no components'); ?></td>
						</tr>
					<?php endif; ?>
					</table>
				</td>
			<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
				<td style="width: 340px; vertical-align: top; padding-left: 10px;">
					<div style="width: 340px; padding: 3px; font-size: 12px; background-color: #FFF; border-bottom: 1px solid #DDD;"><b><?php echo __('Add an existing component'); ?></b></div>
					<table cellpadding=0 cellspacing=0 style="width: 100%;" id="edition_components">
					<?php foreach ($theEdition->getProject()->getComponents() as $aComponent): ?>
						<tr id="project_component_<?php echo $aComponent->getID(); ?>"<?php if ($theEdition->hasComponent($aComponent)): ?> style="display: none;"<?php endif; ?>>
						<?php if ($access_level == configurationActions::ACCESS_FULL): ?> 
							<td style="width: 50px; text-align: left;"><a href="javascript:void(0);" onclick="addEditionComponent('<?php echo make_url('configure_edition_add_component', array('project_id' => $theEdition->getProject()->getID(), 'edition_id' => $theEdition->getID(), 'component_id' => $aComponent->getID())); ?>', <?php echo $aComponent->getID(); ?>);">&lt;&lt;&nbsp;<?php echo __('Add'); ?></a></td>
						<?php endif; ?>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_components.png'); ?></td>
							<td style="width: auto; padding: 2px;"><?php print $aComponent; ?></td>
						</tr>
					<?php endforeach; ?>
					<?php if (count($theEdition->getProject()->getComponents()) == 0): ?>
						<tr>
							<td style="padding: 3px; color: #AAA;" colspan=3><?php echo __('This project has no components'); ?></td>
						</tr>
					<?php endif; ?>
					</table>
				</td>
			<?php endif; ?>	
			</tr>
		</table>
		<div style="padding-top: 15px; font-size: 11px; width: 700px;"><?php echo __('You can only add existing project components. If this project does not have any components yet, go back to the project overview and add them there.'); ?></div>
	<?php else: ?>
		<div style="padding: 2px 5px 5px 5px;" class="faded_medium"><?php echo __('This project does not use components'); ?>.<br><?php echo __('Components can be enabled in project settings'); ?>.</div>
	<?php endif; ?>
	</div>
	<div id="edition_builds" style="width: 700px;<?php if ($selected_section != 'releases'): ?> display: none;<?php endif; ?>">
	<?php if ($theEdition->getProject()->isBuildsEnabled()): ?>
		<?php include_template('builds', array('parent' => $theEdition, 'access_level' => $access_level)); ?>	
	<?php else: ?>
		<div style="padding: 2px 5px 5px 5px;" class="faded_medium"><?php echo __('This project does not use builds / releases'); ?>.<br><?php echo __('Builds / releases can be enabled in project settings'); ?>.</div>
	<?php endif; ?>
	</div>
</td>
</tr>
</table>