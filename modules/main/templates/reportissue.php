<?php 

	$bugs_response->setTitle('Report an issue');
	$bugs_response->addJavascript('reportissue.js');
	
?>
<div style="text-align: center; margin-bottom: 10px;">
	<div class="report_issue_header">
		<?php echo __("What's the issue?"); ?>
	</div>
	<?php if (!empty($errors)): ?>
		<div class="rounded_box report_issue_desc red_borderless" style="margin-bottom: 5px;">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="vertical-align: middle; padding: 5px; color: #222;">
				<strong><?php echo __('One or more errors occured when trying to file your issue'); ?>:</strong>
				<ul>
					<?php foreach ($errors as $error): ?>
						<?php if (is_array($error)): ?>
							<?php foreach ($error as $suberror): ?>
								<li><?php echo $suberror; ?></li>
							<?php endforeach; ?>
						<?php else: ?>
							<li><?php echo $error; ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
				<?php echo __('Please try to fix the error described above, and then click the %file_issue% button again', array('%file_issue%' => '<b>'.__('File issue').'</b>')); ?>.
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
	<?php elseif ($issue instanceof BUGSissue): ?>
		<div class="rounded_box report_issue_desc green_borderless" style="margin-bottom: 10px;">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="vertical-align: middle; padding: 5px; color: #222;">
				<strong><?php echo __('The following issue was reported'); ?>:</strong> 
				<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), __('%issue_no% - %issue_title%', array('%issue_no%' => $issue->getFormattedIssueNo(true), '%issue_title%' => $issue->getTitle()))); ?><br>
				<?php echo __('Click the link to visit the reported issue'); ?>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
	<?php endif; ?>
	<form action="<?php echo make_url('reportissue'); ?>" method="post">
		<div class="rounded_box report_issue_desc borderless">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
				<?php if (count($projects) > 0): ?>
					<?php if (!$selected_project instanceof BUGSproject): ?>
						<p><?php echo __('Please select the project you are filing an issue for, as well as what kind of issue you are filing'); ?>.</p>
					<?php endif; ?>
					<div style="margin: 10px 0 0 0; clear: both; height: 25px;">
						<div style="float: left;">
							<?php if ($selected_project instanceof BUGSproject): ?>
								<span style="font-size: 14px;"><?php echo __('Reporting an issue for %project_name%', array('%project_name%' => '<b>' . $selected_project->getName() . '</b>'))?></span>
							<?php endif; ?>
							<label for="project_id" style="margin-right: 20px;<?php if ($selected_project instanceof BUGSproject): ?> display: none;<?php endif; ?>"><?php echo __('Select project'); ?></label>
							<select name="project_id" id="project_id" style="min-width: 300px; height: 25px;<?php if ($selected_project instanceof BUGSproject): ?> display: none;<?php endif; ?>" onchange="updateFields('<?php echo make_url('getreportissuefields'); ?>', '<?php echo make_url('getprojectmenustrip', array('page' => 'reportissue')); ?>');">
								<option value="0"><?php echo __('Please select a project from this list'); ?>...</option>
								<?php foreach ($projects as $project): ?>
									<option value="<?php echo $project->getID(); ?>"<?php if ($selected_project instanceof BUGSproject && $selected_project->getID() == $project->getID()): ?> selected<?php endif; ?>><?php echo $project->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div style="float: right;<?php if (!$selected_issuetype instanceof BUGSissuetype): ?> display: none;<?php endif; ?>" id="issuetype_dropdown">
							<label for="issuetype_id" style="margin-right: 20px;"><?php echo __('Select issue type'); ?></label>
							<select name="issuetype_id" id="issuetype_id" style="min-width: 300px; height: 25px;" onchange="updateFields('<?php echo make_url('getreportissuefields'); ?>', '');">
								<option value="0"><?php echo __('Please select an issue type from this list'); ?>...</option>
								<?php foreach ($issuetypes as $issue_type): ?>
									<?php if (!$issue_type->isReportable()) continue; ?>
									<option value="<?php echo $issue_type->getID(); ?>"<?php if ($selected_issuetype instanceof BUGSissuetype && $selected_issuetype->getID() == $issue_type->getID()): ?> selected<?php endif; ?>><?php echo $issue_type->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php else: ?>
					<p style="padding-bottom: 5px;">
						<b class="faded_dark"><?php echo __('There are no projects to choose from'); ?>.</b><br>
						<i><?php echo __('An administrator must create one or more projects before you can report any issues'); ?>.</i>
					</p>
				<?php endif; ?>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
		<?php if (count($projects) > 0 && count($issuetypes) > 0): ?>
			<ul class="issuetype_list" id="issuetype_list"<?php if ($selected_issuetype instanceof BUGSissuetype): ?> style="display: none;"<?php endif; ?>>
			<?php $left = true; ?>
			<?php foreach ($issuetypes as $issuetype): ?>
				<?php if (!$issuetype->isReportable()) continue; ?>
				<li class="rounded_box borderless" style="float: <?php if ($left): ?>left<?php else: ?>right<?php endif; ?>;">
					<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
					<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
						<?php echo image_tag($issuetype->getIcon() . '.png'); ?>
						<strong style="font-size: 14px;"><?php echo $issuetype->getName(); ?></strong><br>
						<?php echo $issuetype->getDescription(); ?>
						<div style="text-align: right; margin-top: 5px;">
							<a href="javascript:void(0);" onclick="$('issuetype_id').setValue(<?php echo $issuetype->getID(); ?>);updateFields('<?php echo make_url('getreportissuefields'); ?>');" style="font-size: 13px; font-weight: bold;"><?php echo __('Choose %issue_type%', array('%issue_type%' => strtolower($issuetype->getName()))); ?>&nbsp;&gt;&gt;</a>
						</div>
					</div>
					<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
				</li>
				<?php $left = !$left; ?>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<div style="clear: both;"></div>
		<?php if (count($projects) > 0 && count($issuetypes) > 0): ?>
			<div id="report_more_here"<?php if ($selected_issuetype instanceof BUGSissuetype && $selected_project instanceof BUGSproject): ?> style="display: none;"<?php endif; ?>><?php echo __('More options will appear here as soon as you select a project and an issue type above'); ?>...</div>
			<div class="report_form" id="report_form"<?php if (!$selected_project instanceof BUGSproject || !$selected_issuetype instanceof BUGSissuetype): ?> style="display: none;"<?php endif; ?>>
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td style="width: 150px;"><label for="title" class="required"><?php echo __('Short summary'); ?></label></td>
						<td style="text-align: right;"><input type="text" name="title" id="title" <?php if ((isset($title) && $title == $default_title) || !isset($title)): ?> class="title faded_medium"<?php endif; ?> value="<?php echo (isset($title)) ? $title : $default_title; ?>" onblur="if ($('title').getValue() == '') { $('title').value = '<?php echo $default_title; ?>'; $('title').addClassName('faded_medium'); }" onfocus="if ($('title').getValue() == '<?php echo $default_title; ?>') { $('title').clear(); } $('title').removeClassName('faded_medium');"></td>
					</tr>
				</table>
				<div id="report_issue_more_options_indicator">
					<?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')); ?>
					<div style="padding-top: 2px;"><?php echo __('Checking fields, please wait'); ?>...</div>					
				</div>
				<table cellpadding="0" cellspacing="0" id="description_div" style="display: none;">
					<tr>
						<td style="width: 150px; padding-top: 15px;"><label for="description" id="description_label" class="required"><?php echo __('Description'); ?></label></td>
						<td style="padding-top: 15px;" class="report_issue_help faded_dark"><?php echo __('Describe the issue in as much detail as possible. More is better.'); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<?php include_template('main/textarea', array('area_name' => 'description', 'height' => '250px', 'width' => '990px', 'value' => ((isset($description)) ? $description : null))); ?>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="reproduction_steps_div" style="display: none;">
					<tr>
						<td style="width: 150px; padding-top: 20px;"><label for="reproduction_steps" id="reproduction_steps_label"><?php echo __('Reproduction steps'); ?></label></td>
						<td style="padding-top: 20px;" class="report_issue_help faded_dark"><?php echo __('Enter the steps necessary to reproduce the issue, as detailed as possible.'); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<?php include_template('textarea', array('area_name' => 'reproduction_steps', 'height' => '250px', 'width' => '990px', 'value' => ((isset($reproduction_steps)) ? $reproduction_steps : null))); ?>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="edition_div" style="display: none;">
					<tr>
						<td style="width: 150px; padding-top: 20px;"><label for="edition_id" id="edition_label"><?php echo __('Edition'); ?></label></td>
						<td style="padding-top: 20px;" class="report_issue_help faded_dark"><?php echo __("Select which edition of the product you're using"); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="edition_id" id="edition_id" style="width: 100%;">
								<option value="0"><?php echo __('Not specified'); ?></option>
								<?php if ($selected_edition instanceof BUGSedition): ?>
									<option value="<?php echo $selected_edition->getID(); ?>"><?php echo $selected_edition->getName(); ?></option>
								<?php endif; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="build_div" style="display: none;">
					<tr>
						<td style="width: 150px; padding-top: 20px;"><label for="build_id" id="build_label"><?php echo __('Release'); ?></label></td>
						<td style="padding-top: 20px;" class="report_issue_help faded_dark"><?php echo __("Select which release you're using"); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="build_id" id="build_id" style="width: 100%;">
								<option value="0"><?php echo __('Not specified'); ?></option>
								<?php if ($selected_build instanceof BUGSbuild): ?>
									<option value="<?php echo $selected_build->getID(); ?>"><?php echo $selected_build->getName(); ?></option>
								<?php endif; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="component_div" style="display: none;">
					<tr>
						<td style="width: 150px; padding-top: 20px;"><label for="component_id" id="component_label"><?php echo __('Component'); ?></label></td>
						<td style="padding-top: 20px;" class="report_issue_help faded_dark"><?php echo __("Choose the component affected by this issue"); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="component_id" id="component_id" style="width: 100%;">
								<option value="0"><?php echo __('Not specified'); ?></option>
								<?php if ($selected_component instanceof BUGScomponent): ?>
									<option value="<?php echo $selected_component->getID(); ?>"><?php echo $selected_component->getName(); ?></option>
								<?php endif; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="estimated_time_div" style="display: none; margin-top: 15px;">
					<tr>
						<td style="width: 150px;"><label for="estimated_time_id" id="estimated_time_label"><?php echo __('Estimate'); ?></label></td>
						<td style="text-align: left;"><input type="text" name="estimated_time" id="estimated_time_id" style="width: 810px;" <?php if (($selected_estimated_time !== null && $selected_estimated_time == $default_estimated_time) || $selected_estimated_time === null): ?> class="faded_medium"<?php endif; ?> value="<?php echo ($selected_estimated_time !== null) ? $selected_estimated_time : $default_estimated_time; ?>" onblur="if ($('estimated_time_id').getValue() == '') { $('estimated_time_id').value = '<?php echo $default_estimated_time; ?>'; $('estimated_time_id').addClassName('faded_medium'); }" onfocus="if ($('estimated_time_id').getValue() == '<?php echo $default_estimated_time; ?>') { $('estimated_time_id').clear(); } $('estimated_time_id').removeClassName('faded_medium');"></td>
					</tr>
					<tr>
						<td style="padding-top: 5px;" class="report_issue_help faded_dark" colspan="2"><?php echo __('Type in your estimate here. Use keywords such as "points", "hours", "days", "weeks" and "months" to describe your estimate'); ?></td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="elapsed_time_div" style="display: none; margin-top: 15px;">
					<tr>
						<td style="width: 150px;"><label for="elapsed_time_id" id="elapsed_time_label"><?php echo __('Time spent'); ?></label></td>
						<td style="text-align: left;"><input type="text" name="elapsed_time" id="elapsed_time_id" style="width: 810px;" <?php if (($selected_elapsed_time !== null && $selected_elapsed_time == $default_elapsed_time) || $selected_elapsed_time === null): ?> class="faded_medium"<?php endif; ?> value="<?php echo ($selected_elapsed_time !== null) ? $selected_elapsed_time : $default_elapsed_time; ?>" onblur="if ($('elapsed_time_id').getValue() == '') { $('elapsed_time_id').value = '<?php echo $default_elapsed_time; ?>'; $('elapsed_time_id').addClassName('faded_medium'); }" onfocus="if ($('elapsed_time_id').getValue() == '<?php echo $default_elapsed_time; ?>') { $('elapsed_time_id').clear(); } $('elapsed_time_id').removeClassName('faded_medium');"></td>
					</tr>
					<tr>
						<td style="padding-top: 5px;" class="report_issue_help faded_dark" colspan="2"><?php echo __('Enter time spent on this issue here. Use keywords such as "points", "hours", "days", "weeks" and "months" to describe your estimate'); ?></td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="percent_complete_div" style="display: none; margin-top: 15px;">
					<tr>
						<td style="width: 150px;"><label for="percent_complete_id" id="percent_complete_label"><?php echo __('% completed'); ?></label></td>
						<td style="text-align: left;"><input type="text" name="percent_complete" id="percent_complete_id" style="width: 50px;"<?php if ($selected_percent_complete !== null): ?> value="<?php echo $selected_percent_complete; ?>"<?php endif; ?>></td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="status_div" style="display: none;">
					<tr>
						<td style="width: 150px; padding-top: 20px;"><label for="status_id" id="status_label"><?php echo __('Status'); ?></label></td>
						<td style="padding-top: 20px;" class="report_issue_help faded_dark"><?php echo __("Choose a status for this issue"); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="status_id" id="status_id" style="width: 100%;">
								<option value="0"><?php echo __('Not specified'); ?></option>
								<?php foreach ($statuses as $status): ?>
									<option value="<?php echo $status->getID(); ?>"<?php if ($selected_status instanceof BUGSstatus && $selected_status->getID() == $status->getID()): ?> selected<?php endif; ?>><?php echo $status->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="category_div" style="display: none;">
					<tr>
						<td style="width: 150px; padding-top: 20px;"><label for="category_id" id="category_label"><?php echo __('Category'); ?></label></td>
						<td style="padding-top: 20px;" class="report_issue_help faded_dark"><?php echo __("Choose a category for this issue"); ?></td>
					</tr>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="category_id" id="category_id" style="width: 100%;">
								<option value="0"><?php echo __('Not specified'); ?></option>
								<?php foreach ($categories as $category): ?>
									<option value="<?php echo $category->getID(); ?>"<?php if ($selected_category instanceof BUGScategory && $selected_category->getID() == $category->getID()): ?> selected<?php endif; ?>><?php echo $category->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="resolution_div" style="display: none;">
					<tr>
						<td style="width: 150px; padding-top: 20px;"><label for="resolution_id" id="resolution_label"><?php echo __('Resolution'); ?></label></td>
						<td style="padding-top: 20px;" class="report_issue_help faded_dark"><?php echo __("Choose a resolution for this issue"); ?></td>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="resolution_id" id="resolution_id" style="width: 100%;">
								<option value="0"><?php echo __('Not specified'); ?></option>
								<?php foreach ($resolutions as $resolution): ?>
									<option value="<?php echo $resolution->getID(); ?>"<?php if ($selected_resolution instanceof BUGSresolution && $selected_resolution->getID() == $resolution->getID()): ?> selected<?php endif; ?>><?php echo $resolution->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="reproducability_div" style="display: none;">
					<tr>
						<td style="width: 150px; padding-top: 20px;"><label for="reproducability_id" id="reproducability_label"><?php echo __('Reproducability'); ?></label></td>
						<td style="padding-top: 20px;" class="report_issue_help faded_dark"><?php echo __("Choose a how often you can reproduce this issue"); ?></td>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="reproducability_id" id="reproducability_id" style="width: 100%;">
								<option value="0"><?php echo __('Not specified'); ?></option>
								<?php foreach ($reproducabilities as $reproducability): ?>
									<option value="<?php echo $reproducability->getID(); ?>"<?php if ($selected_reproducability instanceof BUGSreproducability && $selected_reproducability->getID() == $reproducability->getID()): ?> selected<?php endif; ?>><?php echo $reproducability->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="priority_div" style="display: none;">
					<tr>
						<td style="width: 150px; padding-top: 20px;"><label for="priority_id" id="priority_label"><?php echo __('Priority'); ?></label></td>
						<td style="padding-top: 20px;" class="report_issue_help faded_dark"><?php echo __("Choose the priority of this issue"); ?></td>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="priority_id" id="priority_id" style="width: 100%;">
								<option value="0"><?php echo __('Not specified'); ?></option>
								<?php foreach ($priorities as $priority): ?>
									<option value="<?php echo $priority->getID(); ?>"<?php if ($selected_priority instanceof BUGSpriority && $selected_priority->getID() == $priority->getID()): ?> selected<?php endif; ?>><?php echo $priority->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" id="severity_div" style="display: none;">
					<tr>
						<td style="width: 150px; padding-top: 20px;"><label for="severity_id" id="severity_label"><?php echo __('Severity'); ?></label></td>
						<td style="padding-top: 20px;" class="report_issue_help faded_dark"><?php echo __("Choose a severity for this issue"); ?></td>
					<tr>
						<td colspan="2" style="padding-top: 5px;">
							<select name="severity_id" id="severity_id" style="width: 100%;">
								<option value="0"><?php echo __('Not specified'); ?></option>
								<?php foreach ($severities as $severity): ?>
									<option value="<?php echo $severity->getID(); ?>"<?php if ($selected_severity instanceof BUGSseverity && $selected_severity->getID() == $severity->getID()): ?> selected<?php endif; ?>><?php echo $severity->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<?php foreach (BUGScustomdatatype::getAll() as $customdatatype): ?>
					<table cellpadding="0" cellspacing="0" id="<?php echo $customdatatype->getKey(); ?>_div" style="display: none;">
						<tr>
							<td style="width: 150px; padding-top: 20px;"><label for="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_label"><?php echo __($customdatatype->getDescription()); ?></label></td>
							<td style="padding-top: 20px;" class="report_issue_help faded_dark"><?php echo __($customdatatype->getInstructions()); ?></td>
						<tr>
							<td colspan="2" style="padding-top: 5px;">
								<select name="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_id" style="width: 100%;">
									<?php foreach ($customdatatype->getOptions() as $option): ?>
									<option value="<?php echo $option->getValue(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof BUGScustomdatatypeoption && $selected_customdatatype[$customdatatype->getKey()]->getValue() == $option->getValue()): ?> selected<?php endif; ?>><?php echo $option->getName(); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					</table>
				<?php endforeach; ?>
				<?php if ($selected_issuetype != null && $selected_project != null): ?>
					<script type="text/javascript">updateFields('<?php echo make_url('getreportissuefields'); ?>');</script>
				<?php endif; ?>
				<div class="rounded_box report_issue_desc green_borderless" id="report_issue_add_extra">
					<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
					<div class="xboxcontent" style="vertical-align: middle; padding: 5px; height: 25px; font-size: 15px;">
						<div style="float: left; padding-top: 3px;"><?php echo __('When you are satisfied, click the %file_issue% button to file your issue', array('%file_issue%' => '<strong>'.__('File issue').'</strong>')); ?></div>
						<input type="submit" value="<?php echo __('File issue'); ?>" style="font-weight: bold; padding: 2px 10px 2px 10px; float: right; clear: none;">
					</div>
					<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
				</div>
				<div class="rounded_box report_issue_desc borderless" id="report_issue_add_extra">
					<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
					<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
						<strong><?php echo __('Add more information to your issue'); ?></strong><br>
						<p><?php echo __('Specify additional information by clicking the links below before submitting your issue'); ?></p>
						<ul id="reportissue_extrafields">
							<li><?php echo image_tag('icon_file.png'); ?><a href="#" class="faded_dark"><?php echo __('Attach a file'); ?></a></li>
							<li><?php echo image_tag('icon_link.png'); ?><a href="#" class="faded_dark"><?php echo __('Add a link'); ?></a></li>
							<li id="status_additional" style="display: none;">
								<?php echo image_tag('icon_status.png'); ?>
								<div id="status_link"<?php if ($selected_status instanceof BUGSstatus): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('status_link').hide();$('status_additional_div').show();"><?php echo __('Set initial status'); ?></a></div>
								<div id="status_additional_div"<?php if ($selected_status === null): ?> style="display: none;"<?php endif; ?>>
									<select name="status_id" id="status_id_additional">
										<option value="0"><?php echo __('Not specified'); ?></option>
										<?php foreach ($statuses as $status): ?>
											<option value="<?php echo $status->getID(); ?>"<?php if ($selected_status instanceof BUGSdatatype && $selected_status->getID() == $status->getID()): ?> selected<?php endif; ?>><?php echo $status->getName(); ?></option>
										<?php endforeach; ?>
									</select>
									<a href="javascript:void(0);" class="img" onclick="$('status_link').show();$('status_additional_div').hide();$('status_id_additional').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
								</div>
							</li>
							<li id="category_additional" style="display: none;">
								<?php echo image_tag('icon_category.png'); ?>
								<div id="category_link"<?php if ($selected_category instanceof BUGScategory): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('category_link').hide();$('category_additional_div').show();"><?php echo __('Specify category'); ?></a></div>
								<div id="category_additional_div"<?php if ($selected_category === null): ?> style="display: none;"<?php endif; ?>>
									<select name="category_id" id="category_id_additional">
										<option value="0"><?php echo __('Not specified'); ?></option>
										<?php foreach ($categories as $category): ?>
											<option value="<?php echo $category->getID(); ?>"<?php if ($selected_category instanceof BUGSdatatype && $selected_category->getID() == $category->getID()): ?> selected<?php endif; ?>><?php echo $category->getName(); ?></option>
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
							<li id="elapsed_time_additional" style="display: none;">
								<?php echo image_tag('icon_time.png'); ?>
								<div id="elapsed_time_link"<?php if ($selected_elapsed_time != ''): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('elapsed_time_link').hide();$('elapsed_time_additional_div').show();"><?php echo __('Estimate time to fix'); ?></a></div>
								<div id="elapsed_time_additional_div"<?php if ($selected_elapsed_time === null): ?> style="display: none;"<?php endif; ?>>
									<input name="elapsed_time" id="elapsed_time_id_additional" style="width: 100px;">
									<a href="javascript:void(0);" class="img" onclick="$('elapsed_time_link').show();$('elapsed_time_additional_div').hide();$('elapsed_time_id_additional').setValue('');"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
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
								<div id="priority_link"<?php if ($selected_priority instanceof BUGSpriority): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('priority_link').hide();$('priority_additional_div').show();"><?php echo __('Set priority'); ?></a></div>
								<div id="priority_additional_div"<?php if ($selected_priority === null): ?> style="display: none;"<?php endif; ?>>
									<select name="priority_id" id="priority_id_additional">
										<option value="0"><?php echo __('Not specified'); ?></option>
										<?php foreach ($priorities as $priority): ?>
											<option value="<?php echo $priority->getID(); ?>"><?php echo $priority->getName(); ?></option>
										<?php endforeach; ?>
									</select>
									<a href="javascript:void(0);" class="img" onclick="$('priority_link').show();$('priority_additional_div').hide();$('priority').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a> 
								</div>
							</li>
							<li id="reproducability_additional" style="display: none;">
								<?php echo image_tag('icon_repro.png'); ?>
								<div id="reproducability_link"<?php if ($selected_reproducability instanceof BUGSreproducability): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('reproducability_link').hide();$('reproducability_additional_div').show();"><?php echo __('Set reproducability'); ?></a></div>
								<div id="reproducability_additional_div"<?php if ($selected_reproducability === null): ?> style="display: none;"<?php endif; ?>>
									<select name="reproducability_id" id="reproducability_id_additional">
										<option value="0"><?php echo __('Not specified'); ?></option>
										<?php foreach ($reproducabilities as $reproducability): ?>
											<option value="<?php echo $reproducability->getID(); ?>"<?php if ($selected_reproducability instanceof BUGSdatatype && $selected_reproducability->getID() == $reproducability->getID()): ?> selected<?php endif; ?>><?php echo $reproducability->getName(); ?></option>
										<?php endforeach; ?>
									</select>
									<a href="javascript:void(0);" class="img" onclick="$('reproducability_link').show();$('reproducability_additional_div').hide();$('reproducability_id_additional').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a> 
								</div>
							</li>
							<li id="resolution_additional" style="display: none;">
								<?php echo image_tag('icon_resolution.png'); ?>
								<div id="resolution_link"<?php if ($selected_resolution instanceof BUGSresolution): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('resolution_link').hide();$('resolution_additional_div').show();"><?php echo __('Set resolution'); ?></a></div>
								<div id="resolution_additional_div"<?php if ($selected_resolution === null): ?> style="display: none;"<?php endif; ?>>
									<select name="resolution_id" id="resolution_id_additional">
										<option value="0"><?php echo __('Not specified'); ?></option>
										<?php foreach ($resolutions as $resolution): ?>
											<option value="<?php echo $resolution->getID(); ?>"><?php echo $resolution->getName(); ?></option>
										<?php endforeach; ?>
									</select>
									<a href="javascript:void(0);" class="img" onclick="$('resolution_link').show();$('resolution_additional_div').hide();$('resolution_id_additional').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a> 
								</div>
							</li>
							<li id="severity_additional" style="display: none;">
								<?php echo image_tag('icon_severity.png'); ?>
								<div id="severity_link"<?php if ($selected_severity instanceof BUGSseverity): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('severity_link').hide();$('severity_additional_div').show();"><?php echo __('Set severity'); ?></a></div>
								<div id="severity_additional_div"<?php if ($selected_severity === null): ?> style="display: none;"<?php endif; ?>>
									<select name="severity_id" id="severity_id_additional">
										<option value="0"><?php echo __('Not specified'); ?></option>
										<?php foreach ($severities as $severity): ?>
											<option value="<?php echo $severity->getID(); ?>"><?php echo $severity->getName(); ?></option>
										<?php endforeach; ?>
									</select>
									<a href="javascript:void(0);" class="img" onclick="$('severity_link').show();$('severity_additional_div').hide();$('severity_id_additional').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a> 
								</div>
							</li>
							<?php foreach (BUGScustomdatatype::getAll() as $customdatatype): ?>
								<li id="<?php echo $customdatatype->getKey(); ?>_additional" style="display: none;">
									<?php echo image_tag('icon_customdatatype.png'); ?>
									<div id="<?php echo $customdatatype->getKey(); ?>_link"<?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof BUGScustomdatatypeoption): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('<?php echo $customdatatype->getKey(); ?>_link').hide();$('<?php echo $customdatatype->getKey(); ?>_additional_div').show();"><?php echo __($customdatatype->getDescription()); ?></a></div>
									<div id="<?php echo $customdatatype->getKey(); ?>_additional_div"<?php if ($selected_customdatatype[$customdatatype->getKey()] === null): ?> style="display: none;"<?php endif; ?>>
										<select name="<?php echo $customdatatype->getKey(); ?>_id" id="<?php echo $customdatatype->getKey(); ?>_id_additional">
											<?php foreach ($customdatatype->getOptions() as $option): ?>
											<option value="<?php echo $option->getValue(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof BUGScustomdatatypeoption && $selected_customdatatype[$customdatatype->getKey()]->getValue() == $option->getValue()): ?> selected<?php endif; ?>><?php echo $option->getName(); ?></option>
											<?php endforeach; ?>
										</select>
										<a href="javascript:void(0);" class="img" onclick="$('<?php echo $customdatatype->getKey(); ?>_link').show();$('<?php echo $customdatatype->getKey(); ?>_additional_div').hide();$('<?php echo $customdatatype->getKey(); ?>_id_additional').setValue(0);"><?php echo image_tag('undo.png', array('style' => 'float: none; margin-left: 5px;')); ?></a>
									</div>
								</li>
							<?php endforeach; ?>
							<li><?php echo image_tag('icon_team.png'); ?><a href="#" class="faded_dark"><?php echo __('Set assignee'); ?></a></li>
							<li><?php echo image_tag('icon_team.png'); ?><a href="#" class="faded_dark"><?php echo __('Set owner'); ?></a></li>
							<?php
		
								BUGScontext::trigger('core', 'reportissue.listfields');
							
							?>
						</ul>
						<div style="clear: both;"> </div>
					</div>
					<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
				</div>
			</div>
		<?php endif; ?>
	</form>	
</div>