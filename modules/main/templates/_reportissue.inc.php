<?php if (!empty($errors) || !(empty($permission_errors))): ?>
	<div class="rounded_box report_issue_desc red borderless" style="margin-bottom: 5px;">
		<strong><?php echo __('One or more errors occured when trying to file your issue'); ?>:</strong>
		<ul>
			<?php foreach ($errors as $key => $error): ?>
				<?php if (is_array($error)): ?>
					<?php foreach ($error as $suberror): ?>
						<li><?php echo $suberror; ?></li>
					<?php endforeach; ?>
				<?php elseif (is_bool($error)): ?>
					<li>
						<?php if ($key == 'title' || in_array($key, TBGDatatype::getAvailableFields(true)) || in_array($key, array('pain_bug_type', 'pain_likelihood', 'pain_effect'))): ?>
							<?php

								switch ($key)
								{
									case 'title':
										echo __('You have to specify a title');
										break;
									case 'description':
										echo __('You have to enter a description in the "%description%" field', array('%description%' => __('Description')));
										break;
									case 'reproduction_steps':
										echo __('You have to enter something in the "%steps_to_reproduce%" field', array('%steps_to_reproduce%' => __('Steps to reproduce')));
										break;
									case 'edition':
										echo __("Please specify a valid edition");
										break;
									case 'build':
										echo __("Please specify a valid version / release");
										break;
									case 'component':
										echo __("Please specify a valid component");
										break;
									case 'category':
										echo __("Please specify a valid category");
										break;
									case 'status':
										echo __("Please specify a valid status");
										break;
									case 'priority':
										echo __("Please specify a valid priority");
										break;
									case 'reproducability':
										echo __("Please specify a valid reproducability");
										break;
									case 'severity':
										echo __("Please specify a valid severity");
										break;
									case 'resolution':
										echo __("Please specify a valid resolution");
										break;
									case 'milestone':
										echo __("Please specify a valid milestone");
										break;
									case 'estimated_time':
										echo __("Please enter a valid estimate");
										break;
									case 'spent_time':
										echo __("Please enter time already spent working on this issue");
										break;
									case 'percent_complete':
										echo __("Please enter how many percent complete the issue already is");
										break;
									case 'pain_bug_type':
										echo __("Please enter a valid triaged bug type");
										break;
									case 'pain_likelihood':
										echo __("Please enter a valid triaged likelihood");
										break;
									case 'pain_effect':
										echo __("Please enter a valid triaged effect");
										break;
									default:
										echo __("Please triage the reported issue, so the user pain score can be properly calculated");
										break;
								}

							?>
						<?php elseif (TBGCustomDatatype::doesKeyExist($key)): ?>
							<?php echo __('Required field "%field_name%" is missing or invalid', array('%field_name%' => TBGCustomDatatype::getByKey($key)->getDescription())); ?>
						<?php else:

							$event = TBGEvent::createNew('core', 'reportissue.validationerror', $key);
							$event->setReturnValue($key);
							$event->triggerUntilProcessed();
							echo __('A validation error occured: %error%', array('%error%' => $event->getReturnValue()));

						?>
						<?php endif; ?>
					</li>
				<?php else: ?>
					<li><?php echo $error; ?></li>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php foreach ($permission_errors as $key => $p_error): ?>
				<?php if (is_array($p_error)): ?>
					<?php foreach ($p_error as $p_suberror): ?>
						<li><?php echo $p_suberror; ?></li>
					<?php endforeach; ?>
				<?php elseif (is_bool($p_error)): ?>
					<li>
						<?php if (in_array($key, TBGDatatype::getAvailableFields(true))): ?>
							<?php

								switch ($key)
								{
									case 'description':
										echo __("You don't have access to enter a description");
										break;
									case 'reproduction_steps':
										echo __("You don't have access to enter steps to reproduce");
										break;
									case 'edition':
										echo __("You don't have access to add edition information");
										break;
									case 'build':
										echo __("You don't have access to enter release information");
										break;
									case 'component':
										echo __("You don't have access to enter component information");
										break;
									case 'category':
										echo __("You don't have access to specify a category");
										break;
									case 'status':
										echo __("You don't have access to specify a status");
										break;
									case 'priority':
										echo __("You don't have access to specify a priority");
										break;
									case 'reproducability':
										echo __("You don't have access to specify reproducability");
										break;
									case 'severity':
										echo __("You don't have access to specify a severity");
										break;
									case 'resolution':
										echo __("You don't have access to specify a resolution");
										break;
									case 'estimated_time':
										echo __("You don't have access to estimate the issue");
										break;
									case 'spent_time':
										echo __("You don't have access to specify time already spent working on the issue");
										break;
									case 'percent_complete':
										echo __("You don't have access to specify how many percent complete the issue is");
										break;
								}

							?>
						<?php else: ?>
							<?php echo __('You don\'t have access to enter "%field_name%"', array('%field_name%' => TBGCustomDatatype::getByKey($key)->getDescription())); ?>
						<?php endif; ?>
					</li>
				<?php else: ?>
					<li><?php echo $p_error; ?></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		<?php echo __('Please try to fix the error described above, and then click the %file_issue% button again', array('%file_issue%' => '<b>'.__('File issue').'</b>')); ?>.
	</div>
<?php elseif ($issue instanceof TBGIssue): ?>
	<div class="rounded_box report_issue_desc <?php if (!$tbg_request->isAjaxCall()): ?>green<?php endif; ?> borderless" style="margin-bottom: 10px;" id="report_issue_reported_issue_details">
		<div style="font-size: 1.1em;">
			<strong><?php echo __('The following issue was reported: %issue_title%', array('%issue_title%' => '')); ?>:</strong>
			<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedIssueNo(true) . ' - ' . $issue->getTitle()); ?><br>
		</div>
		<span class="faded_out"><?php echo __('Click the link to visit the reported issue'); ?></span>
	</div>
<?php endif; ?>
<?php if ($tbg_request->isAjaxCall()): ?>
	<form action="<?php echo make_url('project_reportissue', array('project_key' => $selected_project->getKey())); ?>" method="post" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" onsubmit="TBG.Main.submitIssue('<?php echo make_url('project_reportissue', array('project_key' => $selected_project->getKey(), 'return_format' => 'planning')); ?>');return false;" id="report_issue_form">
<?php else: ?>
	<form action="<?php echo make_url('project_reportissue', array('project_key' => $selected_project->getKey())); ?>" method="post" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>">
<?php endif; ?>
	<input type="hidden" name="project_id" id="project_id" value="<?php echo $selected_project->getID(); ?>">
	<?php if (isset($selected_milestone) || isset($selected_build) || isset($parent_issue)): ?>
		<div class="rounded_box lightyellow borderless">
			<ul class="simple_list" style="padding: 5px; margin: 5px; font-size: 1.2em;">
				<?php if (isset($selected_milestone)): ?>
					<li>
						<?php echo __('You are adding an issue to %milestone_name%', array('%milestone_name%' => '<b>'.$selected_milestone->getName().'</b>')); ?>
						<input type="hidden" name="milestone_id" id="reportissue_selected_milestone_id" value="<?php echo $selected_milestone->getID(); ?>">
						<input type="hidden" name="milestone_fixed" value="1">
					</li>
				<?php endif; ?>
				<?php if (isset($parent_issue)): ?>
					<li>
						<?php echo __('Any issues you create will be related to %related_issue_title%', array('%related_issue_title%' => '<b>'.$parent_issue->getFormattedTitle().'</b>')); ?>
						<input type="hidden" name="parent_issue_id" id="reportissue_parent_issue_id" value="<?php echo $parent_issue->getID(); ?>">
						<?php if ($issue instanceof TBGIssue): ?>
						<script>
							TBG.Issues.refreshRelatedIssues('<?php echo make_url('viewissue_related_issues', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $parent_issue->getID())); ?>');
						</script>
						<?php endif; ?>
					</li>
				<?php endif; ?>
				<?php if (isset($selected_build)): ?>
					<li>
						<?php echo __('You are adding an issue to release %release_name%', array('%release_name%' => '<b>'.$selected_build->getName().'</b>')); ?>
						<input type="hidden" name="build_id" id="reportissue_selected_build_id" value="<?php echo $selected_build->getID(); ?>">
					</li>
				<?php endif; ?>
			</ul>
		</div>
	<?php endif; ?>
	<?php if (count($issuetypes) > 0): ?>
		<div class="issuetype_list" id="issuetype_list"<?php if ($selected_issuetype instanceof TBGIssuetype): ?> style="display: none;"<?php endif; ?>>
		<?php foreach ($issuetypes as $issuetype): ?>
			<?php if (!$selected_project->getIssuetypeScheme()->isIssuetypeReportable($issuetype) && !$tbg_request->isAjaxCall()) continue; ?>
			<a class="button button-silver" href="javascript:void(0);" onclick="$('issuetype_id').setValue(<?php echo $issuetype->getID(); ?>);TBG.Issues.updateFields('<?php echo make_url('getreportissuefields', array('project_key' => $selected_project->getKey())); ?>');" onmouseover="$('issuetype_description_help').hide();$('issuetype_<?php echo $issuetype->getKey(); ?>_description').show();" onmouseout="$('issuetype_<?php echo $issuetype->getKey(); ?>_description').hide();$('issuetype_description_help').show();" style="font-size: 13px; font-weight: bold;">
				<?php echo image_tag($issuetype->getIcon() . '.png'); ?>
				<?php echo __('Choose %issuetype_name%', array('%issuetype_name%' => '<br>'.$issuetype->getName())); ?>
			</a>
		<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<div style="clear: both;"></div>
	<?php if (count($issuetypes) > 0): ?>
		<?php if (!isset($description)) : ?>
			<?php $description = ''; ?>
		<?php endif; ?>
		<?php if (!isset($reproduction_steps)) : ?>
			<?php $reproduction_steps = ''; ?>
		<?php endif; ?>
		<div id="report_more_here"<?php if ($selected_issuetype instanceof TBGIssuetype && $selected_project instanceof TBGProject): ?> style="display: none;"<?php endif; ?>>
			<span id="issuetype_description_help"><?php echo __("Hold your mouse over an issuetype to see what it's used for"); ?></span>
			<?php foreach ($issuetypes as $issuetype): ?>
				<?php if (!$selected_project->getIssuetypeScheme()->isIssuetypeReportable($issuetype) && !$tbg_request->isAjaxCall()) continue; ?>
				<span id="issuetype_<?php echo $issuetype->getKey(); ?>_description" style="display: none;"><?php echo $issuetype->getDescription(); ?></span>
			<?php endforeach; ?>
		</div>
		<div class="report_form" id="report_form"<?php if (!$selected_project instanceof TBGProject || !$selected_issuetype instanceof TBGIssuetype): ?> style="display: none;"<?php endif; ?>>
			<table cellpadding="0" cellspacing="0"<?php if (array_key_exists('title', $errors)): ?> class="reportissue_error"<?php endif; ?>>
				<tr>
					<td style="width: 180px;"><label for="title" class="required"><span>* </span><?php echo __('Short summary'); ?></label></td>
					<td style="text-align: left;"><input type="text" name="title" id="title" class="title" value="<?php if (isset($title) && trim($title) != '') echo htmlspecialchars($title); ?>" placeholder="<?php echo __('Enter a short, but descriptive summary of the issue here'); ?>"></td>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" style="margin-top: 10px;">
				<tr>
					<td style="width: 180px;"><label for="issuetype_id"><?php echo __('Select issue type'); ?></label>
					<td style="text-align: left;">
						<select name="issuetype_id" id="issuetype_id" style="min-width: 300px; height: 25px;" onchange="TBG.Issues.updateFields('<?php echo make_url('getreportissuefields', array('project_key' => $selected_project->getKey())); ?>');">
							<option value="0"><?php echo __('Please select an issue type from this list'); ?>...</option>
							<?php foreach ($issuetypes as $issuetype): ?>
								<?php if (!$selected_project->getIssuetypeScheme()->isIssuetypeReportable($issuetype) && !$tbg_request->isAjaxCall()) continue; ?>
								<option value="<?php echo $issuetype->getID(); ?>"<?php if ($selected_issuetype instanceof TBGIssuetype && $selected_issuetype->getID() == $issuetype->getID()): ?> selected<?php endif; ?>><?php echo $issuetype->getName(); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			</table>
			<div id="report_issue_more_options_indicator">
				<?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')); ?>
				<div style="padding-top: 2px;"><?php echo __('Checking fields, please wait'); ?>...</div>
			</div>
			<table cellpadding="0" cellspacing="0" id="description_div" style="display: none; margin-top: 15px;"<?php if (array_key_exists('description', $errors)): ?> class="reportissue_error"<?php endif; ?>>
				<tr>
					<td style="width: 180px;"><label for="description" id="description_label"><span>* </span><?php echo __('Description'); ?></label></td>
					<td class="report_issue_help faded_out dark"><?php echo __('Describe the issue in as much detail as possible. More is better.'); ?></td>
				</tr>
				<tr>
					<td colspan="2" style="padding-top: 5px;">
						<?php include_template('main/textarea', array('area_name' => 'description', 'height' => ($tbg_request->isAjaxCall() ? '150px' : '250px'), 'width' => '990px', 'value' => ((isset($selected_description)) ? $selected_description : null))); ?>
					</td>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" id="reproduction_steps_div" style="display: none; margin-top: 15px;"<?php if (array_key_exists('reproduction_steps', $errors)): ?> class="reportissue_error"<?php endif; ?>>
				<tr>
					<td style="width: 180px;"><label for="reproduction_steps" id="reproduction_steps_label"><span>* </span><?php echo __('Reproduction steps'); ?></label></td>
					<td class="report_issue_help faded_out dark"><?php echo __('Enter the steps necessary to reproduce the issue, as detailed as possible.'); ?></td>
				</tr>
				<tr>
					<td colspan="2" style="padding-top: 5px;">
						<?php include_template('textarea', array('area_name' => 'reproduction_steps', 'height' => '250px', 'width' => '990px', 'value' => ((isset($selected_reproduction_steps)) ? $selected_reproduction_steps : null))); ?>
					</td>
				</tr>
			</table>
			<div class="reportissue_additional_information_container">
				<table cellpadding="0" cellspacing="0" id="edition_div" style="display: none;" class="additional_information<?php if (array_key_exists('edition', $errors)): ?> reportissue_error<?php endif; ?>">
					<tr>
						<td style="width: 180px;"><label for="edition_id" id="edition_label"><span>* </span><?php echo __('Edition'); ?></label></td>
						<td class="report_issue_help faded_out dark"><?php echo __("Select which edition of the product you're using"); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="edition_id" id="edition_id" style="width: 100%;">
								<option value=""<?php if (!$selected_edition instanceof TBGEdition) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
								<?php if ($selected_edition instanceof TBGEdition): ?>
									<option value="<?php echo $selected_edition->getID(); ?>"><?php echo $selected_edition->getName(); ?></option>
								<?php endif; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="build_div" style="display: none;" class="additional_information<?php if (array_key_exists('build', $errors)): ?> reportissue_error<?php endif; ?>">
					<tr>
						<td style="width: 180px;"><label for="build_id" id="build_label"><span>* </span><?php echo __('Release'); ?></label></td>
						<td class="report_issue_help faded_out dark"><?php echo __("Select which release you're using"); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="build_id" id="build_id" style="width: 100%;">
								<option value=""<?php if (!$selected_build instanceof TBGBuild) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
								<?php if ($selected_build instanceof TBGBuild): ?>
									<option value="<?php echo $selected_build->getID(); ?>"><?php echo $selected_build->getName(); ?> (<?php echo $selected_build->getVersion(); ?>)</option>
								<?php endif; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="component_div" style="display: none;" class="additional_information<?php if (array_key_exists('component', $errors)): ?> reportissue_error<?php endif; ?>">
					<tr>
						<td style="width: 180px;"><label for="component_id" id="component_label"><span>* </span><?php echo __('Component'); ?></label></td>
						<td class="report_issue_help faded_out dark"><?php echo __("Choose the component affected by this issue"); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="component_id" id="component_id" style="width: 100%;">
								<option value=""<?php if (!$selected_component instanceof TBGComponent) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
								<?php if ($selected_component instanceof TBGComponent): ?>
									<option value="<?php echo $selected_component->getID(); ?>"><?php echo $selected_component->getName(); ?></option>
								<?php endif; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="estimated_time_div" style="display: none;" class="additional_information<?php if (array_key_exists('estimated_time', $errors)): ?> reportissue_error<?php endif; ?>">
					<tr>
						<td style="width: 180px;"><label for="estimated_time_id" id="estimated_time_label"><span>* </span><?php echo __('Estimate'); ?></label></td>
						<td style="text-align: left;"><input type="text" name="estimated_time" id="estimated_time_id" style="width: 220px;" value="<?php if ($selected_estimated_time !== null) echo $selected_estimated_time; ?>" placeholder="<?php echo __('Enter an estimate here'); ?>"></td>
					</tr>
					<tr>
						<td style="padding-top: 5px;" class="report_issue_help faded_out dark" colspan="2"><?php echo __('Type in your estimate here. Use keywords such as "points", "hours", "days", "weeks" and "months" to describe your estimate'); ?></td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="spent_time_div" style="display: none;" class="additional_information<?php if (array_key_exists('spent_time', $errors)): ?> reportissue_error<?php endif; ?>">
					<tr>
						<td style="width: 180px;"><label for="spent_time_id" id="spent_time_label"><span>* </span><?php echo __('Time spent'); ?></label></td>
						<td style="text-align: left;"><input type="text" name="spent_time" id="spent_time_id" style="width: 220px;" value="<?php if ($selected_spent_time !== null) echo $selected_spent_time; ?>" placeholder="<?php echo __('Enter time spent here'); ?>"></td>
					</tr>
					<tr>
						<td style="padding-top: 5px;" class="report_issue_help faded_out dark" colspan="2"><?php echo __('Enter time spent on this issue here. Use keywords such as "points", "hours", "days", "weeks" and "months" to describe your estimate'); ?></td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="percent_complete_div" style="display: none;" class="additional_information<?php if (array_key_exists('percent_complete', $errors)): ?> reportissue_error<?php endif; ?>">
					<tr>
						<td style="width: 180px;"><label for="percent_complete_id" id="percent_complete_label"><span>* </span><?php echo __('Pct. completed'); ?></label></td>
						<td style="text-align: left; font-size: 16px;"><input type="text" name="percent_complete" id="percent_complete_id" style="width: 50px;"<?php if ($selected_percent_complete !== null): ?> value="<?php echo $selected_percent_complete; ?>"<?php endif; ?>> %</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="status_div" style="display: none;" class="additional_information<?php if (array_key_exists('status', $errors)): ?> reportissue_error<?php endif; ?>">
					<tr>
						<td style="width: 180px;"><label for="status_id" id="status_label"><span>* </span><?php echo __('Status'); ?></label></td>
						<td class="report_issue_help faded_out dark"><?php echo __("Choose a status for this issue"); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="status_id" id="status_id" style="width: 100%;">
								<option value=""<?php if (!$selected_status instanceof TBGStatus) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
								<?php foreach ($statuses as $status): ?>
									<option value="<?php echo $status->getID(); ?>"<?php if ($selected_status instanceof TBGStatus && $selected_status->getID() == $status->getID()): ?> selected<?php endif; ?>><?php echo $status->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="category_div" style="display: none;" class="additional_information<?php if (array_key_exists('category', $errors)): ?> reportissue_error<?php endif; ?>">
					<tr>
						<td style="width: 180px;"><label for="category_id" id="category_label"><span>* </span><?php echo __('Category'); ?></label></td>
						<td class="report_issue_help faded_out dark"><?php echo __("Choose a category for this issue"); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="category_id" id="category_id" style="width: 100%;">
								<option value=""<?php if (!$selected_category instanceof TBGCategory) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
								<?php foreach ($categories as $category): ?>
									<option value="<?php echo $category->getID(); ?>"<?php if ($selected_category instanceof TBGCategory && $selected_category->getID() == $category->getID()): ?> selected<?php endif; ?>><?php echo $category->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="resolution_div" style="display: none;" class="additional_information<?php if (array_key_exists('resolution', $errors)): ?> reportissue_error<?php endif; ?>">
					<tr>
						<td style="width: 180px;"><label for="resolution_id" id="resolution_label"><span>* </span><?php echo __('Resolution'); ?></label></td>
						<td class="report_issue_help faded_out dark"><?php echo __("Choose a resolution for this issue"); ?></td>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="resolution_id" id="resolution_id" style="width: 100%;">
								<option value=""<?php if (!$selected_resolution instanceof TBGResolution) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
								<?php foreach ($resolutions as $resolution): ?>
									<option value="<?php echo $resolution->getID(); ?>"<?php if ($selected_resolution instanceof TBGResolution && $selected_resolution->getID() == $resolution->getID()): ?> selected<?php endif; ?>><?php echo $resolution->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="reproducability_div" style="display: none;" class="additional_information<?php if (array_key_exists('reproducability', $errors)): ?> reportissue_error<?php endif; ?>">
					<tr>
						<td style="width: 180px;"><label for="reproducability_id" id="reproducability_label"><span>* </span><?php echo __('Reproducability'); ?></label></td>
						<td class="report_issue_help faded_out dark"><?php echo __("Choose a how often you can reproduce this issue"); ?></td>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="reproducability_id" id="reproducability_id" style="width: 100%;">
								<option value=""<?php if (!$selected_reproducability instanceof TBGReproducability) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
								<?php foreach ($reproducabilities as $reproducability): ?>
									<option value="<?php echo $reproducability->getID(); ?>"<?php if ($selected_reproducability instanceof TBGReproducability && $selected_reproducability->getID() == $reproducability->getID()): ?> selected<?php endif; ?>><?php echo $reproducability->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="priority_div" style="display: none;" class="additional_information<?php if (array_key_exists('priority', $errors)): ?> reportissue_error<?php endif; ?>">
					<tr>
						<td style="width: 180px;"><label for="priority_id" id="priority_label"><span>* </span><?php echo __('Priority'); ?></label></td>
						<td class="report_issue_help faded_out dark"><?php echo __("Choose the priority of this issue"); ?></td>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="priority_id" id="priority_id" style="width: 100%;">
								<option value=""<?php if (!$selected_priority instanceof TBGPriority) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
								<?php foreach ($priorities as $priority): ?>
									<option value="<?php echo $priority->getID(); ?>"<?php if ($selected_priority instanceof TBGPriority && $selected_priority->getID() == $priority->getID()): ?> selected<?php endif; ?>><?php echo $priority->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<?php if (!isset($selected_milestone)): ?>
					<table cellpadding="0" cellspacing="0" id="milestone_div" style="display: none;" class="additional_information<?php if (array_key_exists('milestone', $errors)): ?> reportissue_error<?php endif; ?>">
						<tr>
							<td style="width: 180px;"><label for="milestone_id" id="milestone_label"><span>* </span><?php echo __('Milestone'); ?></label></td>
							<td class="report_issue_help faded_out dark"><?php echo __("Choose the milestone of this issue"); ?></td>
						<tr>
							<td colspan="2" style="padding-top: 5px;">
								<select name="milestone_id" id="milestone_id" style="width: 100%;">
									<option value=""<?php if (!$selected_milestone instanceof TBGMilestone) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
									<?php foreach ($milestones as $milestone): ?>
										<option value="<?php echo $milestone->getID(); ?>"<?php if ($selected_milestone instanceof TBGMilestone && $selected_milestone->getID() == $milestone->getID()): ?> selected<?php endif; ?>><?php echo $milestone->getName(); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					</table>
				<?php endif; ?>
				<table cellpadding="0" cellspacing="0" id="pain_bug_type_div" style="display: none;" class="additional_information<?php if (array_key_exists('pain_bug_type', $errors)): ?> reportissue_error<?php endif; ?>">
					<tr>
						<td style="width: 160px;"><label for="pain_bug_type_id" id="pain_bug_type_label"><span>* </span><?php echo __('Triaging: Bug type'); ?></label></td>
						<td class="report_issue_help faded_out dark"><?php echo __("What type of bug is this?"); ?></td>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="pain_bug_type_id" id="pain_bug_type_id" style="width: 100%;">
								<option value=""<?php if (!$selected_pain_bug_type) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
								<?php foreach (TBGIssue::getPainTypesOrLabel('pain_bug_type') as $choice_id => $choice): ?>
									<option value="<?php echo $choice_id; ?>"<?php if ($selected_pain_bug_type == $choice_id): ?> selected<?php endif; ?>><?php echo $choice; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="pain_likelihood_div" style="display: none;" class="additional_information<?php if (array_key_exists('pain_likelihood', $errors)): ?> reportissue_error<?php endif; ?>">
					<tr>
						<td style="width: 320px;"><label for="pain_likelihood_id" id="pain_likelihood_label"><span>* </span><?php echo __('Triaging: Likelihood'); ?></label></td>
						<td class="report_issue_help faded_out dark"><?php echo __("How likely are users to experience the bug?"); ?></td>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="pain_likelihood_id" id="pain_likelihood_id" style="width: 100%;">
								<option value=""<?php if (!$selected_pain_likelihood) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
								<?php foreach (TBGIssue::getPainTypesOrLabel('pain_likelihood') as $choice_id => $choice): ?>
									<option value="<?php echo $choice_id; ?>"<?php if ($selected_pain_likelihood == $choice_id): ?> selected<?php endif; ?>><?php echo $choice; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="pain_effect_div" style="display: none;" class="additional_information<?php if (array_key_exists('pain_effect', $errors)): ?> reportissue_error<?php endif; ?>">
					<tr>
						<td style="width: 260px;"><label for="pain_effect_id" id="pain_effect_label"><span>* </span><?php echo __('Triaging: Effect'); ?></label></td>
						<td class="report_issue_help faded_out dark"><?php echo __("Of the people who experience the bug, how badly does it affect their experience?"); ?></td>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="pain_effect_id" id="pain_effect_id" style="width: 100%;">
								<option value=""<?php if (!$selected_pain_effect) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
								<?php foreach (TBGIssue::getPainTypesOrLabel('pain_effect') as $choice_id => $choice): ?>
									<option value="<?php echo $choice_id; ?>"<?php if ($selected_pain_effect == $choice_id): ?> selected<?php endif; ?>><?php echo $choice; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="severity_div" style="display: none;" class="additional_information<?php if (array_key_exists('severity', $errors)): ?> reportissue_error<?php endif; ?>">
					<tr>
						<td style="width: 180px;"><label for="severity_id" id="severity_label"><span>* </span><?php echo __('Severity'); ?></label></td>
						<td class="report_issue_help faded_out dark"><?php echo __("Choose a severity for this issue"); ?></td>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="severity_id" id="severity_id" style="width: 100%;">
								<option value=""<?php if (!$selected_severity instanceof TBGSeverity) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
								<?php foreach ($severities as $severity): ?>
									<option value="<?php echo $severity->getID(); ?>"<?php if ($selected_severity instanceof TBGSeverity && $selected_severity->getID() == $severity->getID()): ?> selected<?php endif; ?>><?php echo $severity->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<?php foreach (TBGCustomDatatype::getAll() as $customdatatype): ?>
					<table cellpadding="0" cellspacing="0" id="<?php echo $customdatatype->getKey(); ?>_div" style="display: none;" class="additional_information<?php if (array_key_exists($customdatatype->getKey(), $errors)): ?> reportissue_error<?php endif; ?>">
						<tr>
							<td style="width: 180px;"><label for="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_label"><span>* </span><?php echo __($customdatatype->getDescription()); ?></label></td>
							<td class="report_issue_help faded_out dark"><?php echo __($customdatatype->getInstructions()); ?></td>
						<tr>
							<td colspan="2" style="padding-top: 5px;">
								<?php
									switch ($customdatatype->getType())
									{
										case TBGCustomDatatype::DROPDOWN_CHOICE_TEXT: ?>
											<select name="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_id" style="width: 100%;">
												<option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof TBGCustomDatatypeOption) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
												<?php foreach ($customdatatype->getOptions() as $option): ?>
												<option value="<?php echo $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof TBGCustomDatatypeOption && $selected_customdatatype[$customdatatype->getKey()]->getID() == $option->getID()): ?> selected<?php endif; ?>><?php echo $option->getName(); ?></option>
												<?php endforeach; ?>
											</select>
											<?php
											break;
										case TBGCustomDatatype::EDITIONS_CHOICE: ?>
											<select name="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_id" style="width: 100%;">
												<option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof TBGEdition) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
												<?php if ($selected_project instanceof TBGProject): ?>
													<?php foreach ($selected_project->getEditions() as $option): ?>
													<option value="<?php echo $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?php echo $option->getName(); ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
											<?php
											break;
										case TBGCustomDatatype::STATUS_CHOICE: ?>
											<select name="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_id" style="width: 100%;">
												<option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof TBGStatus) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
												<?php foreach (TBGStatus::getAll() as $option): ?>
													<option value="<?php echo $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?php echo $option->getName(); ?></option>
												<?php endforeach; ?>
											</select>
											<?php
											break;
										case TBGCustomDatatype::COMPONENTS_CHOICE: ?>
											<select name="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_id" style="width: 100%;">
												<option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof TBGComponent) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
												<?php if ($selected_project instanceof TBGProject): ?>
													<?php foreach ($selected_project->getComponents() as $option): ?>
														<option value="<?php echo $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?php echo $option->getName(); ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
											<?php
											break;
										case TBGCustomDatatype::RELEASES_CHOICE: ?>
											<select name="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_id" style="width: 100%;">
												<option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof TBGBuild) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
												<?php if ($selected_project instanceof TBGProject): ?>
													<?php foreach ($selected_project->getBuilds() as $option): ?>
														<option value="<?php echo $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?php echo $option->getName(); ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
											<?php
											break;
										case TBGCustomDatatype::RADIO_CHOICE: ?>
											<input type="radio" name="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_0" value="" <?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof TBGCustomDatatypeOption): ?> selected<?php endif; ?> /> <label for="<?php echo $customdatatype->getKey(); ?>_0"><?php echo __('Not specified'); ?></label><br>
											<?php foreach ($customdatatype->getOptions() as $option): ?>
												<input type="radio" name="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_<?php echo $option->getID(); ?>" value="<?php echo $option->getID(); ?>" <?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof TBGCustomDatatypeOption && $selected_customdatatype[$customdatatype->getKey()]->getID() == $option->getID()): ?> selected<?php endif; ?> /> <label for="<?php echo $customdatatype->getKey(); ?>_<?php echo $option->getID(); ?>"><?php echo $option->getName(); ?></label><br>
											<?php endforeach; ?>
											<?php
											break;
										case TBGCustomDatatype::INPUT_TEXT:
											?>
											<input type="text" name="<?php echo $customdatatype->getKey(); ?>_value" value="<?php echo $selected_customdatatype[$customdatatype->getKey()]; ?>" id="<?php echo $customdatatype->getKey(); ?>_value" /><br>
											<?php
											break;
										case TBGCustomDatatype::INPUT_TEXTAREA_SMALL:
										case TBGCustomDatatype::INPUT_TEXTAREA_MAIN:
											?>
											<?php include_template('main/textarea', array('area_name' => $customdatatype->getKey().'_value', 'area_id' => $customdatatype->getKey().'_value', 'height' => '75px', 'width' => '100%', 'value' => $selected_customdatatype[$customdatatype->getKey()])); ?>
											<?php
											break;
									}
								?>
							</td>
						</tr>
					</table>
				<?php endforeach; ?>
				<br style="clear: both;">
			</div>
			<?php if ($selected_issuetype != null && $selected_project != null): ?>
				<script type="text/javascript">
					TBG.Issues.updateFields('<?php echo make_url('getreportissuefields', array('project_key' => $selected_project->getKey())); ?>');
				</script>
			<?php endif; ?>
			<?php TBGEvent::createNew('core', 'reportissue.prefile')->trigger(); ?>
			<div class="rounded_box report_issue_submit_container report_issue_desc green borderless" style="clear: both; vertical-align: middle; margin-top: 10px; padding: 5px; height: 25px;">
				<div style="float: left; padding-top: 3px;"><?php echo __('When you are satisfied, click the %file_issue% button to file your issue', array('%file_issue%' => '<strong>'.__('File issue').'</strong>')); ?></div>
				<input type="submit" class="button button-silver" value="<?php echo __('File issue'); ?>" id="report_issue_submit_button">
				<?php echo image_tag('spinning_20.gif', array('id' => 'report_issue_indicator', 'style' => 'display: none;')); ?>
			</div>
			<div class="rounded_box report_issue_desc borderless lightgrey" id="report_issue_add_extra" style="vertical-align: middle; padding: 5px;">
				<strong><?php echo __('Add more information to your issue'); ?></strong><br>
				<p><?php echo __('Specify additional information by clicking the links below before submitting your issue'); ?></p>
				<p id="reportissue_extrafields_none" style="display: none;"><?php echo __('No additional actions available'); ?></p>
				<ul id="reportissue_extrafields">
					<?php /*<li><?php echo image_tag('icon_file.png'); ?><a href="javascript:void(0);" onclick="TBG.Main.Helpers.Message.success('<?php echo __('Please file the issue before attaching a file'); ?>');"><?php echo __('Attach a file'); ?></a></li>
					<li><?php echo image_tag('icon_link.png'); ?><a href="javascript:void(0);" onclick="TBG.Main.Helpers.Message.success('<?php echo __('Please file the issue before attaching a link'); ?>');"><?php echo __('Add a link'); ?></a></li> */ ?>
					<li id="status_additional" style="display: none;">
						<?php echo image_tag('icon_status.png'); ?>
						<div id="status_link"<?php if ($selected_status instanceof TBGStatus): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('status_link').hide();$('status_additional_div').show();"><?php echo __('Set initial status'); ?></a></div>
						<div id="status_additional_div"<?php if ($selected_status === null): ?> style="display: none;"<?php endif; ?>>
							<select name="status_id" id="status_id_additional">
								<option value=""><?php echo __('Not specified'); ?></option>
								<?php foreach ($statuses as $status): ?>
									<option value="<?php echo $status->getID(); ?>"<?php if ($selected_status instanceof TBGDatatype && $selected_status->getID() == $status->getID()): ?> selected<?php endif; ?>><?php echo $status->getName(); ?></option>
								<?php endforeach; ?>
							</select>
							<a href="javascript:void(0);" class="img" onclick="$('status_link').show();$('status_additional_div').hide();$('status_id_additional').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
						</div>
					</li>
					<li id="edition_additional" style="display: none;">
						<?php echo image_tag('icon_edition.png'); ?>
						<div id="edition_link"<?php if ($selected_edition instanceof TBGEdition): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('edition_link').hide();$('edition_additional_div').show();"><?php echo __('Specify edition'); ?></a></div>
						<div id="edition_additional_div"<?php if ($selected_edition === null): ?> style="display: none;"<?php endif; ?>>
							<select name="edition_id" id="edition_id_additional">
								<option value=""<?php if (!$selected_edition instanceof TBGEdition) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
								<?php if ($selected_edition instanceof TBGEdition): ?>
									<option value="<?php echo $selected_edition->getID(); ?>"><?php echo $selected_edition->getName(); ?> (<?php echo $selected_edition->getVersion(); ?>)</option>
								<?php endif; ?>
							</select>
							<a href="javascript:void(0);" class="img" onclick="$('edition_link').show();$('edition_additional_div').hide();$('edition_id_additional').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
						</div>
					</li>
					<li id="build_additional" style="display: none;">
						<?php echo image_tag('icon_build.png'); ?>
						<div id="build_link"<?php if ($selected_build instanceof TBGBuild): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('build_link').hide();$('build_additional_div').show();"><?php echo __('Specify release'); ?></a></div>
						<div id="build_additional_div"<?php if ($selected_build === null): ?> style="display: none;"<?php endif; ?>>
							<select name="build_id" id="build_id_additional">
								<option value=""<?php if (!$selected_build instanceof TBGBuild) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
								<?php if ($selected_build instanceof TBGBuild): ?>
									<option value="<?php echo $selected_build->getID(); ?>"><?php echo $selected_build->getName(); ?> (<?php echo $selected_build->getVersion(); ?>)</option>
								<?php endif; ?>
							</select>
							<a href="javascript:void(0);" class="img" onclick="$('build_link').show();$('build_additional_div').hide();$('build_id_additional').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
						</div>
					</li>
					<li id="component_additional" style="display: none;">
						<?php echo image_tag('icon_component.png'); ?>
						<div id="component_link"<?php if ($selected_component instanceof TBGComponent): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('component_link').hide();$('component_additional_div').show();"><?php echo __('Specify component'); ?></a></div>
						<div id="component_additional_div"<?php if ($selected_component === null): ?> style="display: none;"<?php endif; ?>>
							<select name="component_id" id="component_id_additional">
								<option value=""<?php if (!$selected_component instanceof TBGComponent) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
								<?php if ($selected_component instanceof TBGComponent): ?>
									<option value="<?php echo $selected_component->getID(); ?>"><?php echo $selected_component->getName(); ?> (<?php echo $selected_component->getVersion(); ?>)</option>
								<?php endif; ?>
							</select>
							<a href="javascript:void(0);" class="img" onclick="$('component_link').show();$('component_additional_div').hide();$('component_id_additional').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
						</div>
					</li>
					<?php if (!isset($selected_milestone)): ?>
						<li id="milestone_additional" style="display: none;">
							<?php echo image_tag('icon_milestone.png'); ?>
							<div id="milestone_link"<?php if ($selected_milestone instanceof TBGMilestone): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('milestone_link').hide();$('milestone_additional_div').show();"><?php echo __('Specify milestone'); ?></a></div>
							<div id="milestone_additional_div"<?php if ($selected_milestone === null): ?> style="display: none;"<?php endif; ?>>
								<select name="milestone_id" id="milestone_id_additional">
									<option value=""<?php if (!$selected_milestone instanceof TBGMilestone) echo ' selected'; ?>><?php echo __('Not specified'); ?></option>
									<?php if ($selected_milestone instanceof TBGMilestone): ?>
										<option value="<?php echo $selected_milestone->getID(); ?>"><?php echo $selected_milestone->getName(); ?></option>
									<?php endif; ?>
								</select>
								<a href="javascript:void(0);" class="img" onclick="$('milestone_link').show();$('milestone_additional_div').hide();$('milestone_id_additional').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
							</div>
						</li>
					<?php endif; ?>
					<li id="category_additional" style="display: none;">
						<?php echo image_tag('icon_category.png'); ?>
						<div id="category_link"<?php if ($selected_category instanceof TBGCategory): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('category_link').hide();$('category_additional_div').show();"><?php echo __('Specify category'); ?></a></div>
						<div id="category_additional_div"<?php if ($selected_category === null): ?> style="display: none;"<?php endif; ?>>
							<select name="category_id" id="category_id_additional">
								<option value=""><?php echo __('Not specified'); ?></option>
								<?php foreach ($categories as $category): ?>
									<option value="<?php echo $category->getID(); ?>"<?php if ($selected_category instanceof TBGDatatype && $selected_category->getID() == $category->getID()): ?> selected<?php endif; ?>><?php echo $category->getName(); ?></option>
								<?php endforeach; ?>
							</select>
							<a href="javascript:void(0);" class="img" onclick="$('category_link').show();$('category_additional_div').hide();$('category_id_additional').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
						</div>
					</li>
					<li id="estimated_time_additional" style="display: none;">
						<?php echo image_tag('icon_time.png'); ?>
						<div id="estimated_time_link"<?php if ($selected_estimated_time != ''): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('estimated_time_link').hide();$('estimated_time_additional_div').show();"><?php echo __('Estimate time to fix'); ?></a></div>
						<div id="estimated_time_additional_div"<?php if ($selected_estimated_time === null): ?> style="display: none;"<?php endif; ?>>
							<input name="estimated_time" id="estimated_time_id_additional" style="width: 100px;">
							<a href="javascript:void(0);" class="img" onclick="$('estimated_time_link').show();$('estimated_time_additional_div').hide();$('estimated_time_id_additional').setValue('');"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
						</div>
					</li>
					<li id="spent_time_additional" style="display: none;">
						<?php echo image_tag('icon_time.png'); ?>
						<div id="spent_time_link"<?php if ($selected_spent_time != ''): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('spent_time_link').hide();$('spent_time_additional_div').show();"><?php echo __('Estimate time to fix'); ?></a></div>
						<div id="spent_time_additional_div"<?php if ($selected_spent_time === null): ?> style="display: none;"<?php endif; ?>>
							<input name="spent_time" id="spent_time_id_additional" style="width: 100px;">
							<a href="javascript:void(0);" class="img" onclick="$('spent_time_link').show();$('spent_time_additional_div').hide();$('spent_time_id_additional').setValue('');"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
						</div>
					</li>
					<li id="percent_complete_additional" style="display: none;">
						<?php echo image_tag('icon_percent.png'); ?>
						<div id="percent_complete_link"<?php if ($selected_percent_complete != ''): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('percent_complete_link').hide();$('percent_complete_additional_div').show();"><?php echo __('Set percent completed'); ?></a></div>
						<div id="percent_complete_additional_div"<?php if ($selected_percent_complete === null): ?> style="display: none;"<?php endif; ?>>
							<input name="percent_complete" id="percent_complete_id_additional" style="width: 30px;"<?php if ($selected_percent_complete !== null): ?> value="<?php echo $selected_percent_complete; ?>"<?php endif; ?>>
							<a href="javascript:void(0);" class="img" onclick="$('percent_complete_link').show();$('percent_complete_additional_div').hide();$('percent_complete_id_additional').setValue('');"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
						</div>
					</li>
					<li id="priority_additional">
						<?php echo image_tag('icon_priority.png'); ?>
						<div id="priority_link"<?php if ($selected_priority instanceof TBGPriority): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('priority_link').hide();$('priority_additional_div').show();"><?php echo __('Set priority'); ?></a></div>
						<div id="priority_additional_div"<?php if ($selected_priority === null): ?> style="display: none;"<?php endif; ?>>
							<select name="priority_id" id="priority_id_additional">
								<option value=""><?php echo __('Not specified'); ?></option>
								<?php foreach ($priorities as $priority): ?>
									<option value="<?php echo $priority->getID(); ?>"><?php echo $priority->getName(); ?></option>
								<?php endforeach; ?>
							</select>
							<a href="javascript:void(0);" class="img" onclick="$('priority_link').show();$('priority_additional_div').hide();$('priority').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
						</div>
					</li>
					<li id="reproducability_additional" style="display: none;">
						<?php echo image_tag('icon_reproducability.png'); ?>
						<div id="reproducability_link"<?php if ($selected_reproducability instanceof TBGReproducability): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('reproducability_link').hide();$('reproducability_additional_div').show();"><?php echo __('Set reproducability'); ?></a></div>
						<div id="reproducability_additional_div"<?php if ($selected_reproducability === null): ?> style="display: none;"<?php endif; ?>>
							<select name="reproducability_id" id="reproducability_id_additional">
								<option value=""><?php echo __('Not specified'); ?></option>
								<?php foreach ($reproducabilities as $reproducability): ?>
									<option value="<?php echo $reproducability->getID(); ?>"<?php if ($selected_reproducability instanceof TBGDatatype && $selected_reproducability->getID() == $reproducability->getID()): ?> selected<?php endif; ?>><?php echo $reproducability->getName(); ?></option>
								<?php endforeach; ?>
							</select>
							<a href="javascript:void(0);" class="img" onclick="$('reproducability_link').show();$('reproducability_additional_div').hide();$('reproducability_id_additional').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
						</div>
					</li>
					<li id="resolution_additional" style="display: none;">
						<?php echo image_tag('icon_resolution.png'); ?>
						<div id="resolution_link"<?php if ($selected_resolution instanceof TBGResolution): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('resolution_link').hide();$('resolution_additional_div').show();"><?php echo __('Set resolution'); ?></a></div>
						<div id="resolution_additional_div"<?php if ($selected_resolution === null): ?> style="display: none;"<?php endif; ?>>
							<select name="resolution_id" id="resolution_id_additional">
								<option value=""><?php echo __('Not specified'); ?></option>
								<?php foreach ($resolutions as $resolution): ?>
									<option value="<?php echo $resolution->getID(); ?>"><?php echo $resolution->getName(); ?></option>
								<?php endforeach; ?>
							</select>
							<a href="javascript:void(0);" class="img" onclick="$('resolution_link').show();$('resolution_additional_div').hide();$('resolution_id_additional').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
						</div>
					</li>
					<li id="severity_additional" style="display: none;">
						<?php echo image_tag('icon_severity.png'); ?>
						<div id="severity_link"<?php if ($selected_severity instanceof TBGSeverity): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('severity_link').hide();$('severity_additional_div').show();"><?php echo __('Set severity'); ?></a></div>
						<div id="severity_additional_div"<?php if ($selected_severity === null): ?> style="display: none;"<?php endif; ?>>
							<select name="severity_id" id="severity_id_additional">
								<option value=""><?php echo __('Not specified'); ?></option>
								<?php foreach ($severities as $severity): ?>
									<option value="<?php echo $severity->getID(); ?>"><?php echo $severity->getName(); ?></option>
								<?php endforeach; ?>
							</select>
							<a href="javascript:void(0);" class="img" onclick="$('severity_link').show();$('severity_additional_div').hide();$('severity_id_additional').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
						</div>
					</li>
					<?php foreach (TBGCustomDatatype::getAll() as $customdatatype): ?>
						<li id="<?php echo $customdatatype->getKey(); ?>_additional" style="display: none;">
							<?php echo image_tag('icon_customdatatype.png'); ?>
							<div id="<?php echo $customdatatype->getKey(); ?>_link"<?php if ($selected_customdatatype[$customdatatype->getKey()] !== null): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('<?php echo $customdatatype->getKey(); ?>_link').hide();$('<?php echo $customdatatype->getKey(); ?>_additional_div').show();"><?php echo __($customdatatype->getDescription()); ?></a></div>
							<div id="<?php echo $customdatatype->getKey(); ?>_additional_div"<?php if ($selected_customdatatype[$customdatatype->getKey()] === null): ?> style="display: none;"<?php endif; ?>>
								<?php
									switch ($customdatatype->getType())
									{
										case TBGCustomDatatype::DROPDOWN_CHOICE_TEXT:
											?>
											<select name="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_id_additional">
												<?php foreach ($customdatatype->getOptions() as $option): ?>
												<option value="<?php echo $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof TBGCustomDatatypeOption && $selected_customdatatype[$customdatatype->getKey()]->getID() == $option->getID()): ?> selected<?php endif; ?>><?php echo $option->getName(); ?></option>
												<?php endforeach; ?>
											</select>
											<?php
											break;
										case TBGCustomDatatype::EDITIONS_CHOICE:
											?>
											<select name="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_id_additional">
												<?php if ($selected_project instanceof TBGProject): ?>
													<?php foreach ($selected_project->getEditions() as $option): ?>
													<option value="<?php echo $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?php echo $option->getName(); ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
											<?php
											break;
										case TBGCustomDatatype::STATUS_CHOICE:
											?>
											<select name="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_id_additional">
												<?php foreach (TBGStatus::getAll() as $option): ?>
												<option value="<?php echo $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?php echo $option->getName(); ?></option>
												<?php endforeach; ?>
											</select>
											<?php
											break;
										case TBGCustomDatatype::COMPONENTS_CHOICE:
											?>
											<select name="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_id_additional">
												<?php if ($selected_project instanceof TBGProject): ?>
													<?php foreach ($selected_project->getComponents() as $option): ?>
													<option value="<?php echo $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?php echo $option->getName(); ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
											<?php
											break;
										case TBGCustomDatatype::RELEASES_CHOICE:
											?>
											<select name="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_id_additional">
												<?php if ($selected_project instanceof TBGProject): ?>
													<?php foreach ($selected_project->getBuilds() as $option): ?>
													<option value="<?php echo $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?php echo $option->getName(); ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
											<?php
											break;
										case TBGCustomDatatype::RADIO_CHOICE:
											?>
											<label for="<?php echo $customdatatype->getKey(); ?>_id_additional"><?php echo $customdatatype->getDescription(); ?></label>
											<br>
											<?php foreach ($customdatatype->getOptions() as $option): ?>
												<input type="radio" name="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_id_additional" value="<?php echo $option->getID(); ?>" <?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof TBGCustomDatatypeOption && $selected_customdatatype[$customdatatype->getKey()]->getID() == $option->getID()): ?> selected<?php endif; ?> /> <?php echo $option->getName(); ?><br>
											<?php
											endforeach;
											break;
										case TBGCustomDatatype::INPUT_TEXT:
											?>
											<input type="text" name="<?php echo $customdatatype->getKey(); ?>_value" class="field_additional" value="<?php echo $selected_customdatatype[$customdatatype->getKey()]; ?>" id="<?php echo $customdatatype->getKey(); ?>_value_additional" />
											<?php
											break;
										case TBGCustomDatatype::INPUT_TEXTAREA_SMALL:
										case TBGCustomDatatype::INPUT_TEXTAREA_MAIN:
											?>
											<label for="<?php echo $customdatatype->getKey(); ?>_value_additional"><?php echo $customdatatype->getDescription(); ?></label>
											<br>
											<?php include_template('main/textarea', array('area_name' => $customdatatype->getKey().'_value', 'area_id' => $customdatatype->getKey().'_value_additional', 'height' => '125px', 'width' => '100%', 'value' => $selected_customdatatype[$customdatatype->getKey()])); ?>
											<?php
											break;
									}
									if (!$customdatatype->hasCustomOptions())
									{
										?>
										<a href="javascript:void(0);" class="img" onclick="$('<?php echo $customdatatype->getKey(); ?>_link').show();$('<?php echo $customdatatype->getKey(); ?>_additional_div').hide();$('<?php echo $customdatatype->getKey(); ?>_value_additional').setValue('');"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
										<?php
									}
									else
									{
										?>
										<a href="javascript:void(0);" class="img" onclick="$('<?php echo $customdatatype->getKey(); ?>_link').show();$('<?php echo $customdatatype->getKey(); ?>_additional_div').hide();$('<?php echo $customdatatype->getKey(); ?>_id_additional').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
										<?php
									}
									?>
							</div>
						</li>
					<?php endforeach; ?>
					<?php TBGEvent::createNew('core', 'reportissue.listfields')->trigger(); ?>
				</ul>
				<div style="clear: both;"> </div>
			</div>
		</div>
	<?php endif; ?>
</form>
