<div id="tab_vcs_pane"<?php if ($selected_tab != 'vcs'): ?> style="display: none;"<?php endif; ?>>
	<?php if ($access_level != TBGSettings::ACCESS_FULL): ?>
		<div class="rounded_box red" style="margin-top: 10px;">
			<?php echo __('You do not have the relevant permissions to access VCS Integration settings'); ?>
		</div>
	<?php else: ?>
		<div class="tab_menu">
			<ul id="vcs_config_menu">
				<li id="tab_vcs_general" class="selected"><?php echo javascript_link_tag(__('General'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_vcs_general', 'vcs_config_menu');")); ?></li>
				<li id="tab_vcs_connection"><?php echo javascript_link_tag(__('Connection'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_vcs_connection', 'vcs_config_menu');")); ?></li>
				<li id="tab_vcs_browser"><?php echo javascript_link_tag(__('Browser'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_vcs_browser', 'vcs_config_menu');")); ?></li>
			</ul>
		</div>
		<div id="vcs_config_menu_panes">
			<div id="tab_vcs_general_pane">
				<?php echo __('Please see the help before setting up VCS Integration'); ?>
				<div class="rounded_box iceblue" style="margin-top: 10px;">
					<table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
						<tr>
							<td style="width: 200px;"><label for="vcs_mode"><?php echo __('Enable VCS Integration?'); ?></label></td>
							<td style="width: 580px;">
								<select name="vcs_mode" id="vcs_mode">
									<option value="0">Disable for this project</option>
									<option value="1">Enable for commits applying to existing issues only</option>
									<option value="2">Enable for all commits</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="config_explanation" colspan="2"><?php echo __('If VCS Integration is enabled, in both cases commits that match to an issue will be noted on the issue, and on the project Commits tab. If the All Commits mode is selected, non-matching commits will also be recorded on the project page.'); ?></td>
						</tr>
					</table>
				</div>
				<table style="clear: both; width: 780px; margin-top: 10px" class="padded_table" cellpadding=0 cellspacing=0>
					<tr>
						<td style="width: 200px;"><label for="match_keywords"><?php echo __('Issue matching keywords'); ?></label></td>
						<td style="width: 580px;">
							<input type="text" name="match_keywords" id="match_keywords" value="" style="width: 100%;">
						</td>
					</tr>
					<tr>
						<td class="config_explanation" colspan="2"><?php echo __('Enter, separated by a comma but no space, keywords which should be prefixed to an issue number in a commit message for The Bug Genie to match a commit to an issue. If left blank, the default set (as detailed in the help file) will be used'); ?></td>
					</tr>
				</table>
			</div>
			<div id="tab_vcs_connection_pane" style="display: none">
				<table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
					<tr>
						<td style="width: 200px;"><label for="access_method"><?php echo __('Access method'); ?></label></td>
						<td style="width: 580px;">
							<select name="access_method" id="access_method">
								<option value="0"><?php echo __('Direct Access (via a call to tbg_cli)'); ?></option>
								<option value="1"><?php echo __('HTTP Access (via a call to a URL'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="config_explanation" colspan="2"><?php echo __('If, in the hook, you can access tbg_cli directly via shell access, please choose the Direct method here. Otherwise choose HTTP access - you will need to then set a passkey below.'); ?></td>
					</tr>
					<tr>
						<td style="width: 200px;"><label for="access_passkey"><?php echo __('HTTP Passkey'); ?></label></td>
						<td style="width: 580px; position: relative;">
							<input type="text" name="access_passkey" id="access_passkey" value="" style="width: 100%;">
						</td>
					</tr>
				</table>
				<div class="rounded_box yellow" style="margin-top: 10px;">
					<?php echo __('If you are intending to use Github or Gitorious support, you must choose the HTTP method'); ?>
				</div>
			</div>
			<div id="tab_vcs_browser_pane" style="display: none">
				<table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
					<tr>
						<td style="width: 200px;"><label for="browser_url"><?php echo __('URL to repository browser'); ?></label></td>
						<td style="width: 580px; position: relative;">
							<input type="text" name="browser_url" id="browser_url" value="" style="width: 100%;">
						</td>
					</tr>
					<tr>
						<td class="config_explanation" colspan="2"><?php echo __('If the repository name is part of the URL (e.g. http://www.example.com/viewvc/myrepo), please include it as part of this field.'); ?></td>
					</tr>
					<tr>
						<td style="width: 200px;"><label for="browser_type"><?php echo __('Repository browser type'); ?></label></td>
						<td style="width: 580px; position: relative;">
							<select name="browser_type" id="browser_type" onchange="if ($('browser_type').getValue() == 'websvn' || $('browser_type').getValue() == 'websvn_mv') { $('repository_box').show(); } else { $('repository_box').hide(); } if ($('browser_type').getValue() == 'other') { $('vcs_custom_urls').show(); } else { $('vcs_custom_urls').hide(); }">
								<optgroup label="<?php echo __('Multi-system'); ?>">
									<option value='viewvc'>CVS/SVN - ViewVC</option>
									<option value='other'>Other - <?php echo __('Set URLs manually'); ?></option>
								</optgroup>
								<optgroup label="Subversion">
									<option value='websvn'>WebSVN</option>
									<option value='websvn_mv'>WebSVN <?php echo __('using MultiViews'); ?></option>
								</optgroup>
								<optgroup label="Mercurial">
									<option value='hgweb'>hgweb</option>
								</optgroup>
								<optgroup label="Git">
									<option value='gitweb'>gitweb</option>
									<option value='cgit' >cgit</option>
									<option value='gitorious'>Gitorious (<?php echo __('locally hosted'); ?>)</option>
									<option value='github'>Github</option>
								</optgroup>
								<optgroup label="Bazaar">
									<option value='loggerhead'>Loggerhead</option>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<td class="config_explanation" colspan="2"><?php echo __('Choosing the repository browser will use will automatically set the URLs for the pages The Bug Genie needs to access. If you choose Custom you can manually set these URLs. You may be asked for a repository name, as in some cases this will be needed to produce the required URLs. If so, a box to enter one will be shown.'); ?></td>
					</tr>
					<tr id="repository_box" style="display: none">
						<td style="width: 200px;"><label for="repository"><?php echo __('Repository name'); ?></label></td>
						<td style="width: 580px; position: relative;">
							<input type="text" name="repository" id="repository" value="" style="width: 100%;">
						</td>
					</tr>
					<tr>
						<td colspan="2" id="vcs_custom_urls" style="display: none;">
							<div class="tab_header"><?php echo __('Custom browser URLs'); ?></div>
							<div class="rounded_box lightgrey left" style="margin-top: 10px">
								<div class="header"><?php echo __('In the Commit details page fields, this parameter will be replaced with a real value when link is generated:'); ?></div>
								<ul>
									<li>%revno% - <?php echo __('Revision number of either the current or previous revision (the one to use is automatically chosen as appropriate)'); ?></li>
								</ul>
								<div class="header"><?php echo __('In the other fields, these parameters will be replaced with real values when links are generated:'); ?></div>
								<ul>
									<li>%revno% - <?php echo __('Revision number'); ?></li>
									<li>%oldrev% - <?php echo __('Revision number of previous commit'); ?></li>
									<li>%file% - <?php echo __('Filename and path, from root of repository'); ?></li>
								</ul>
							</div>
							<table style="clear: both; width: 780px; margin-top: 10px" class="padded_table" cellpadding=0 cellspacing=0>
								<tr>
									<td style="width: 200px;"><label for="commit_url"><?php echo __('Commit details page'); ?></label></td>
									<td style="width: 580px; position: relative;">
										<input type="text" name="commit_url" id="commit_url" value="" style="width: 100%;">
									</td>
								</tr>
								<tr>
									<td style="width: 200px;"><label for="log_url"><?php echo __('File log page'); ?></label></td>
									<td style="width: 580px; position: relative;">
										<input type="text" name="log_url" id="log_url" value="" style="width: 100%;">
									</td>
								</tr>
								<tr>
									<td style="width: 200px;"><label for="blob_url"><?php echo __('File blob/view page'); ?></label></td>
									<td style="width: 580px; position: relative;">
										<input type="text" name="blob_url" id="blob_url" value="" style="width: 100%;">
									</td>
								</tr>
								<tr>
									<td style="width: 200px;"><label for="diff_url"><?php echo __('Diff page'); ?></label></td>
									<td style="width: 580px; position: relative;">
										<input type="text" name="diff_url" id="diff_url" value="" style="width: 100%;">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</div>
	<?php endif; ?>
</div>