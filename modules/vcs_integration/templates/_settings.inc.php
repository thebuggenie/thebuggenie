<p><?php echo __('Use this page to configure the interface between The Bug Genie and your VCS system. Note that further configuration is necessary to use this feature - please refer to the %help% for further details on these settings and other necessary configuration.', array('%help%' => link_tag(make_url('publish_article', array('article_name' => 'VCSIntegration')), __('help'), array('target' => '_blank')))); ?></p>
<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_module', array('config_module' => $module->getName())); ?>" enctype="multipart/form-data" method="post">
<div style="margin-top: 5px; width: 750px; clear: both; height: 30px;" class="tab_menu">
	<ul id="vcsintegration_settings_menu">
		<li class="selected" id="tab_general_settings"><a onclick="switchSubmenuTab('tab_general_settings', 'vcsintegration_settings_menu');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_general.png', array('style' => 'float: left;')).__('General settings'); ?></a></li>
		<li id="tab_project_settings"><a onclick="switchSubmenuTab('tab_project_settings', 'vcsintegration_settings_menu');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_projects.png', array('style' => 'float: left;')).__('Project settings'); ?></a></li>
	</ul>
</div>
<div id="vcsintegration_settings_menu_panes">
	<div id="tab_general_settings_pane" class="rounded_box borderless mediumgrey<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> cut_bottom<?php endif; ?>" style="margin: 10px 0 0 0; width: 700px;<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> border-bottom: 0;<?php endif; ?>">
		<div class="header"><?php echo __('General settings'); ?></div>
		<div class="content" style="padding-bottom: 10px;"><?php echo __('These are the settings that apply to all communications between The Bug Genie and any VCS, regardless of the project.'); ?></div>
		<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0 id="vcsintegration_settings_table">
			<tr>
				<td style="padding: 5px;"><label for="use_web_interface"><?php echo __('Access method'); ?></label></td>
				<td>
					<select name="use_web_interface" id="use_web_interface" onchange="if ($(this).getValue() == 0) { $('vcs_passkey').disable(); } else { $('vcs_passkey').enable(); }">
						<option value="1"<?php if ($module->isUsingHTTPMethod()): ?> selected<?php endif; ?>><?php echo __('Use the HTTP access method'); ?></option>
						<option value="0"<?php if (!$module->isUsingHTTPMethod()): ?> selected<?php endif; ?>><?php echo __('Use the direct access method'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('The Bug Genie can be notified of new commits by either a direct access call, or via HTTP. Select the method you wish to use here.'); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="vcs_passkey"><?php echo __('Passkey for HTTP access'); ?></label></td>
				<td><input type="text" name="vcs_passkey" id="vcs_passkey" value="<?php echo $module->getSetting('vcs_passkey'); ?>" style="width: 100%;"<?php if (!$module->isUsingHTTPMethod()): ?> disabled="disabled"<?php endif; ?>></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('If the HTTP method has been chosen, a passkey must be entered so that malicious users can not add fake commit details.'); ?></td>
			</tr>
		</table>
	</div>
	<div id="tab_project_settings_pane" class="rounded_box borderless mediumgrey<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> cut_bottom<?php endif; ?>" style="margin: 10px 0 0 0; display: none; width: 700px;<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> border-bottom: 0;<?php endif; ?>">
		<div class="header"><?php echo __('Project settings'); ?></div>
		<div class="content" style="padding-bottom: 10px;"><?php echo __('These settings apply to each individual project.'); ?></div>
		<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0 id="vcsintegration_settings_table">
			<?php
			foreach ($allProjects as $aProject)
			{
				?>
				<tr>
					<td>
						<div class="rounded_box white">
							<div class="header"><?php echo image_tag('cfg_icon_projects.png').' '.$aProject->getName(); ?> (id: <?php echo $aProject->getID(); ?>)</div>
							<table style="width: 650px;" class="padded_table" cellpadding=0 cellspacing=0 id="vcsintegration_project_<?php echo $aProject->getID(); ?>_table">
								<tr>
									<td style="padding: 5px;"><label for="web_type_<?php echo $aProject->getID(); ?>"><?php echo __('Repository browser'); ?></label></td>
									<td>
										<select name="web_type_<?php echo $aProject->getID(); ?>" id="web_type_<?php echo $aProject->getID(); ?>"onchange="if ($('web_type_<?php echo $aProject->getID(); ?>').getValue() == 'github') { $('web_path_<?php echo $aProject->getID(); ?>').disable();$('web_path_<?php echo $aProject->getID(); ?>').writeAttribute('value', 'http://github.com'); } else { $('web_path_<?php echo $aProject->getID(); ?>').enable(); }">
											<optgroup label="<?php echo __('Multi-system'); ?>">
												<option value='viewvc' <?php print ($module->getSetting('web_type_' . $aProject->getID()) == 'viewvc') ? 'selected' : ''; ?>>CVS/SVN - ViewVC <?php echo __('with project\'s repository set as default'); ?></option>
												<option value='viewvc_repo' <?php print ($module->getSetting('web_type_' . $aProject->getID()) == 'viewvc_repo') ? 'selected' : ''; ?>>CVS/SVN - ViewVC (<?php echo __('manually specified repository'); ?>)</option>
											</optgroup>
											<optgroup label="Subversion">
												<option value='websvn' <?php print ($module->getSetting('web_type_' . $aProject->getID()) == 'websvn') ? 'selected' : ''; ?>>WebSVN</option>
												<option value='websvn_mv' <?php print ($module->getSetting('web_type_' . $aProject->getID()) == 'websvn_mv') ? 'selected' : ''; ?>>WebSVN <?php echo __('using MultiViews'); ?></option>
											</optgroup>
											<optgroup label="Mercurial">
												<option value='hgweb' <?php print ($module->getSetting('web_type_' . $aProject->getID()) == 'hgweb') ? 'selected' : ''; ?>>hgweb</option>
											</optgroup>
											<optgroup label="Git">
												<option value='gitweb' <?php print ($module->getSetting('web_type_' . $aProject->getID()) == 'gitweb') ? 'selected' : ''; ?>>gitweb</option>
												<option value='cgit' <?php print ($module->getSetting('web_type_' . $aProject->getID()) == 'cgit') ? 'selected' : ''; ?>>cgit</option>
												<option value='github' <?php print ($module->getSetting('web_type_' . $aProject->getID()) == 'github') ? 'selected' : ''; ?>>Github</option>
											</optgroup>
											<optgroup label="Bazaar">
												<option value='loggerhead' <?php print ($module->getSetting('web_type_' . $aProject->getID()) == 'loggerhead') ? 'selected' : ''; ?>>Loggerhead</option>
											</optgroup>
										</select>
									</td>
								</tr>
								<tr>
									<td class="config_explanation" colspan="2"><?php echo __('A number of different VCS systems and source code browsers are available. Please select the one you use.'); ?></td>
								</tr>
								<tr>
									<td style="padding: 5px;"><label for="web_path_<?php echo $aProject->getID(); ?>"><?php echo __('URL to repository browser'); ?></label></td>
									<td><input type="text" name="web_path_<?php echo $aProject->getID(); ?>" id="web_path_<?php echo $aProject->getID(); ?>" value="<?php echo $module->getSetting('web_path_'.$aProject->getID()); ?>" style="width: 100%;"></td>
								</tr>
								<tr>
									<td class="config_explanation" colspan="2"><?php echo __('The path to the main page of the repository browser, so links can be correctly generated (<i>see help for details</i>).'); ?></td>
								</tr>
								<tr>
									<td style="padding: 5px;"><label for="web_repo_<?php echo $aProject->getID(); ?>"><?php echo __('Repository name'); ?></label></td>
									<td><input type="text" name="web_repo_<?php echo $aProject->getID(); ?>" id="web_repo_<?php echo $aProject->getID(); ?>" value="<?php echo $module->getSetting('web_repo_'.$aProject->getID()); ?>" style="width: 100%;"></td>
								</tr>
								<tr>
									<td class="config_explanation" colspan="2"><?php echo __('The name of the repository in use, so the correct one can be chosen for viewing commit details. This is not required under certain conditions, see help for details.'); ?></td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
			<?php
			}
			?>
		</table>
	</div>
</div>
<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
	<div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 700px; border-top: 0; padding: 8px 5px 2px 5px; height: 25px;">
		<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save%" to save the settings on both tabs', array('%save%' => __('Save'))); ?></div>
		<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
	</div>
<?php endif; ?>
</form>