<?php if ($theIssue instanceof TBGIssue): ?>
	<?php

		$tbg_response->addJavascript('viewissue.js');
		$tbg_response->setTitle('['.(($theIssue->isClosed()) ? strtoupper(__('Closed')) : strtoupper(__('Open'))) .'] ' . $theIssue->getFormattedIssueNo(true) . ' - ' . $theIssue->getTitle());
	
	?>
	<?php TBGEvent::createNew('core', 'viewissue_top', $theIssue)->trigger(); ?>
	<?php if (TBGSettings::isUploadsEnabled() && $theIssue->canAttachFiles()): ?>
		<?php include_component('main/uploader', array('issue' => $theIssue, 'mode' => 'issue')); ?>
	<?php endif; ?>
	<?php include_component('main/hideableInfoBox', array('key' => 'viewissue_helpbox', 'title' => __('Editing issues'), 'content' => __('To edit any of the details in this issue, move your mouse over that detail and press the icon that appears. Changes you make will stay unsaved until you either press the "'.__('Save').'" button that appears when you change the issue, or until you log out (the changes are then lost).'))); ?>
	<div id="issuetype_indicator_fullpage" style="background-color: transparent; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; text-align: center; display: none;">
		<div style="position: absolute; top: 45%; left: 40%; z-index: 100001; color: #FFF; font-size: 15px; font-weight: bold;">
			<?php echo image_tag('spinning_32.gif'); ?><br>
			<?php echo __('Please wait while updating issue type'); ?>...
		</div>
		<div style="background-color: #000; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; z-index: 100000;" class="semi_transparent"> </div>
	</div>
	<div class="rounded_box red borderless" id="viewissue_unsaved"<?php if (!isset($issue_unsaved)): ?> style="display: none;"<?php endif; ?>>
		<div class="viewissue_info_header"><?php echo __('Could not save your changes'); ?></div>
	</div>
	<?php if (isset($error) && $error): ?>
		<div class="rounded_box red borderless" id="viewissue_error">
			<div class="viewissue_info_header"><?php echo __('There was an error trying to save changes to this issue'); ?></div>
			<div class="viewissue_info_content"><?php echo $error; ?></div>
		</div>
	<?php endif; ?>
	<div class="rounded_box red borderless" id="viewissue_merge_errors"<?php if (!$theIssue->hasMergeErrors()): ?> style="display: none;"<?php endif; ?>>
		<div class="viewissue_info_header"><?php echo __('This issue has been changed since you started editing it'); ?></div>
		<div class="viewissue_info_content"><?php echo __('Data that has been changed is highlighted in red below. Undo your changes to see the updated information'); ?></div>
	</div>
	<?php if ($theIssue->isBeingWorkedOn()): ?>
		<?php if ($theIssue->canEditSpentTime()): ?>
		<form action="<?php echo make_url('issue_stopworking', array('project_key' => $theIssue->getProject()->getKey(), 'issue_id' => $theIssue->getID())); ?>" method="post">
		<?php endif; ?>
			<div class="rounded_box yellow borderless" id="viewissue_being_worked_on" style="vertical-align: middle; padding: 5px; color: #222; font-weight: bold; font-size: 13px;">
				<?php echo image_tag('action_start_working.png', array('style' => 'float: left; margin: 0 10px 0 5px;')); ?>
				<?php if ($theIssue->getUserWorkingOnIssue()->getID() == $tbg_user->getID()): ?>
					<input type="submit" value="<?php echo __('Done'); ?>">
					<div class="viewissue_info_header"><?php echo __('You have been working on this issue since %time%', array('%time%' => tbg_formatTime($theIssue->getWorkedOnSince(), 6))); ?></div>
					<div class="viewissue_info_content">
						<?php echo __('When you are finished working on this issue, click the %done% button to the right', array('%done%' => '<b>' . __('Done') . '</b>')); ?>
					</div>
				<?php else: ?>
					<input type="submit" value="<?php echo __('Take over'); ?>">
					<div class="viewissue_info_header"><?php echo __('%user% has been working on this issue since %time%', array('%user%' => $theIssue->getUserWorkingOnIssue()->getNameWithUsername(), '%time%' => tbg_formatTime($theIssue->getWorkedOnSince(), 6))); ?></div>
					<?php if ($theIssue->canEditSpentTime()): ?>
						<div class="viewissue_info_content">
							<input type="hidden" name="perform_action" value="grab">
							<?php echo __('If you want to start working on this issue instead, click the %take_over% button to the right', array('%take_over%' => '<b>' . __('Take over') . '</b>')); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		<?php if ($theIssue->canEditSpentTime()): ?>
		</form>
		<?php endif; ?>
	<?php endif; ?>
	<div class="rounded_box yellow borderless" id="viewissue_changed" <?php if (!$theIssue->hasUnsavedChanges()): ?>style="display: none;"<?php endif; ?>>
		<button onclick="$('comment_add_button').hide(); $('comment_add').show();$('comment_title').focus();return false;"><?php echo __('Add comment and save changes'); ?></button>
		<form action="<?php echo make_url('saveissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getIssueNo())); ?>" method="post">
			<input type="submit" value="<?php echo __('Save changes'); ?>">
			<div class="viewissue_info_header"><?php echo __('You have unsaved changes'); ?></div>
			<div class="viewissue_info_content">
				<input type="hidden" name="issue_action" value="save">
				<?php echo __("You have changed this issue, but haven't saved your changes yet. To save it, press the %save_changes% button to the right", array('%save_changes%' => '<b>' . __("Save changes") . '</b>')); ?>
			</div>
		</form>
	</div>
	<?php if (isset($issue_saved)): ?>
		<div class="rounded_box green borderless viewissue_info_header" id="viewissue_saved">
			<?php echo __('Your changes has been saved'); ?>
		</div>
	<?php endif; ?>
	<?php if ($theIssue->isBlocking()): ?>
		<div class="rounded_box red borderless" id="blocking_div">
			<?php echo __('This issue is blocking the next release'); ?>
		</div>
	<?php endif; ?>
	<?php if ($theIssue->isDuplicate()): ?>
		<div class="rounded_box iceblue borderless infobox" id="viewissue_duplicate">
			<div style="padding: 5px;">
				<?php echo image_tag('icon_info_big.png', array('style' => 'float: left; margin: 0 5px 0 5px;')); ?>
				<div class="viewissue_info_header"><?php echo __('This issue is a duplicate of Issue %link_to_duplicate_issue%', array('%link_to_duplicate_issue%' => link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getDuplicateOf()->getIssueNo())), $theIssue->getDuplicateOf()->getFormattedIssueNo(true)) . ' - "' . $theIssue->getDuplicateOf()->getTitle() . '"')); ?></div>
				<div class="viewissue_info_content"><?php echo __('For more information you should visit the issue mentioned above, as this issue is not likely to be updated'); ?></div>
			</div>
		</div>								
	<?php endif; ?>
	<?php if ($theIssue->isClosed()): ?>
		<div class="rounded_box iceblue borderless infobox" id="viewissue_closed">
			<div style="padding: 5px;">
				<?php echo image_tag('icon_info_big.png', array('style' => 'float: left; margin: 0 5px 0 5px;')); ?>
				<div class="viewissue_info_header"><?php echo __('This issue has been closed with status "%status_name%" and resolution "%resolution%".', array('%status_name%' => (($theIssue->getStatus() instanceof TBGStatus) ? $theIssue->getStatus()->getName() : __('Not determined')), '%resolution%' => (($theIssue->getResolution() instanceof TBGResolution) ? $theIssue->getResolution()->getName() : __('Not determined')))); ?></div>
				<div class="viewissue_info_content">
					<?php if ($theIssue->canPostComments() && $tbg_user->canReportIssues($theIssue->getProjectID())): ?>
						<?php echo __('A closed issue will usually not be further updated - try %posting_a_comment%, or %report_a_new_issue%', array('%posting_a_comment%' => '<a href="#add_comment_location_core_1_' . $theIssue->getID() . '">' . __('posting a comment') . '</a>', '%report_a_new_issue%' => link_tag(make_url('reportissue'), __('report a new issue')))); ?>
					<?php elseif ($theIssue->canPostComments()): ?>
						<?php echo __('A closed issue will usually not be further updated - try %posting_a_comment%', array('%posting_a_comment%' => '<a href="#add_comment_location_core_1_' . $theIssue->getID() . '">' . __('posting a comment') . '</a>')); ?>
					<?php elseif ($tbg_user->canReportIssues($theIssue->getProjectID())): ?>
						<?php echo __('A closed issue will usually not be further updated - try %reporting_a_new_issue%', array('%reporting_a_new_issue%' => link_tag(make_url('reportissue'), __('reporting a new issue')))); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<div style="width: 1000px; padding: 5px; margin: 0 auto 0 auto;">
		<div style="vertical-align: middle; padding: 5px 0 0 0;">
			<table style="table-layout: fixed; width: 100%; margin: 0 0 10px 0; background-color: transparent;" cellpadding=0 cellspacing=0>
				<tr>
					<td style="width: 80px;<?php if (!$theIssue->isUserPainVisible()): ?> display: none;<?php endif; ?>" id="user_pain_additional">
						<div class="rounded_box green borderless" id="viewissue_triaging" style="margin: 0 5px 0 0; vertical-align: middle; padding: 5px; font-weight: bold; font-size: 13px; text-align: center">
							<div class="user_pain" id="issue_user_pain"><?php echo $theIssue->getUserPain(); ?></div>
							<div class="user_pain_calculated" id="issue_user_pain_calculated"><?php echo $theIssue->getUserPainDiffText(); ?></div>
						</div>
					</td>
					<td style="width: 22px; padding: 0 5px 0 5px;">
						<?php if ($tbg_user->isGuest()): ?>
							<?php echo image_tag('star_faded.png', array('id' => 'issue_favourite_faded', 'title' => __('Please log in to bookmark issues'))); ?>
						<?php else: ?>
							<?php echo image_tag('spinning_20.gif', array('id' => 'issue_favourite_indicator', 'style' => 'display: none;')); ?>
							<?php echo image_tag('star_faded.png', array('id' => 'issue_favourite_faded', 'style' => 'cursor: pointer;'.(($tbg_user->isIssueStarred($theIssue->getID())) ? 'display: none;' : ''), 'onclick' => "toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $theIssue->getID()))."', ".$theIssue->getID().");")); ?>
							<?php echo image_tag('star.png', array('id' => 'issue_favourite_normal', 'style' => 'cursor: pointer;'.((!$tbg_user->isIssueStarred($theIssue->getID())) ? 'display: none;' : ''), 'onclick' => "toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $theIssue->getID()))."', ".$theIssue->getID().");")); ?>
						<?php endif; ?>
					</td>
					<td style="font-size: 17px; width: auto; padding: 0; padding-left: 7px;" id="title_field">
						<div class="viewissue_title hoverable">
							<span class="faded_medium <?php if ($theIssue->isTitleChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$theIssue->isTitleMerged()): ?> issue_detail_unmerged<?php endif; ?>" id="title_header">
								<?php if ($theIssue->canEditTitle()): ?>
									<?php echo image_tag('icon_edit.png', array('class' => 'dropdown', 'id' => 'title_edit', 'onclick' => "$('title_change').show(); $('title_name').hide(); $('no_title').hide();")); ?>
									<a class="undo" href="javascript:void(0);" onclick="revertField('<?php echo make_url('issue_revertfield', array('project_key' => $theIssue->getProject()->getKey(), 'issue_id' => $theIssue->getID(), 'field' => 'title')); ?>', 'title');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
									<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'title_undo_spinning')); ?>
								<?php endif; ?>
								<?php echo $theIssue->isClosed() ? strtoupper(__('Closed')) : strtoupper(__('Open')); ?>&nbsp;&nbsp;<b><?php echo link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getIssueNo())), __('%issue_type% %issue_no%', array('%issue_type%' => $theIssue->getIssueType()->getName(), '%issue_no%' => $theIssue->getFormattedIssueNo(true)))); ?>&nbsp;&nbsp;-&nbsp;</b>
							</span>
							<span id="issue_title">
								<span id="title_content" class="<?php if ($theIssue->isTitleChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$theIssue->isTitleMerged()): ?> issue_detail_unmerged<?php endif; ?>">
									<span class="faded_medium" id="no_title" <?php if ($theIssue->getTitle() != ''):?> style="display: none;" <?php endif; ?>><?php echo __('Nothing entered.'); ?></span>
									<span id="title_name" style="font-weight: bold;">
										<?php echo $theIssue->getTitle(); ?>
									</span>
								</span>
							</span>
							<?php if ($theIssue->canEditTitle()) : ?>
							<span id="title_change" style="display: none;">
								<form id="title_form" action="<?php echo make_url('issue_setfield', array('project_key' => $theIssue->getProject()->getKey(), 'issue_id' => $theIssue->getID(), 'field' => 'title')); ?>" method="post" onSubmit="setField('<?php echo make_url('issue_setfield', array('project_key' => $theIssue->getProject()->getKey(), 'issue_id' => $theIssue->getID(), 'field' => 'title')) ?>', 'title'); return false;">
									<input type="text" name="value" value="<?php echo $theIssue->getTitle() ?>" /><?php echo __('%save% or %cancel%', array('%save%' => '<input type="submit" value="'.__('Save').'">', '%cancel%' => '<a href="#" onClick="$(\'title_change\').hide(); $(\'title_name\').show(); return false;">'.__('cancel').'</a>')); ?>
								</form>
								<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'title_spinning')); ?>
								<span id="title_change_error" class="error_message" style="display: none;"></span>
							</span>
							<?php endif; ?>
						</div>
						<div style="font-size: 12px;">
							<?php echo '<b>' . __('Posted %posted_at_time% - updated %last_updated_at_time%', array('%posted_at_time%' => '</b><i>' . tbg_formatTime($theIssue->getPosted(), 12) . '</i><b>', '%last_updated_at_time%' => '</b><i>' . tbg_formatTime($theIssue->getLastUpdatedTime(), 12) . '</i>')); ?>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div class="rounded_box verylightyellow shadowed" id="viewissue_left_box_top">
			<table style="table-layout: auto; width: 100%; clear: both;" cellpadding=0 cellspacing=0 id="issue_view">
				<tr>
					<td class="issue_lefthand">
						<?php TBGEvent::createNew('core', 'viewissue_left_top', $theIssue)->trigger(); ?>
						<?php include_component('main/issuedetailslisteditable', array('issue' => $theIssue)); ?>
						<?php TBGEvent::createNew('core', 'viewissue_left_bottom', $theIssue)->trigger(); ?>
					</td>
					<td valign="top" align="left" style="padding: 5px; height: 100%;" class="issue_main">
						<?php TBGEvent::createNew('core', 'viewissue_right_top', $theIssue)->trigger(); ?>
						<div id="description_field"<?php if (!$theIssue->isDescriptionVisible()): ?> style="display: none;"<?php endif; ?> class="hoverable">
							<div class="rounded_box invisible nohover viewissue_description<?php if ($theIssue->isDescriptionChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$theIssue->isDescriptionMerged()): ?> issue_detail_unmerged<?php endif; ?>" id="description_header" style="margin: 0;">
								<div class="viewissue_description_header">
									<?php if ($theIssue->canEditDescription()): ?>
										<a href="javascript:void(0);" onclick="revertField('<?php echo make_url('issue_revertfield', array('project_key' => $theIssue->getProject()->getKey(), 'issue_id' => $theIssue->getID(), 'field' => 'description')); ?>', 'description');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a> <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'description_undo_spinning')); ?>
										<?php echo image_tag('icon_edit.png', array('class' => 'dropdown', 'id' => 'description_edit', 'onclick' => "$('description_change').show(); $('description_name').hide(); $('no_description').hide();", 'title' => __('Click here to edit description'))); ?>
									<?php endif; ?>
									<?php echo __('Description'); ?>:
								</div>
								<div id="description_content" class="<?php if ($theIssue->isDescriptionChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$theIssue->isDescriptionMerged()): ?> issue_detail_unmerged<?php endif; ?>">
									<div class="faded_medium" id="no_description" <?php if ($theIssue->getDescription() != ''):?> style="display: none;" <?php endif; ?>><?php echo __('Nothing entered.'); ?></div>
									<div id="description_name">
										<?php if ($theIssue->getDescription()): ?>
											<?php echo tbg_parse_text($theIssue->getDescription(), false, null, array('headers' => false)); ?>
										<?php endif; ?>
									</div>
								</div>
								<?php if ($theIssue->canEditDescription()): ?>
								<div id="description_change" style="display: none;">
									<form id="description_form" action="<?php echo make_url('issue_setfield', array('project_key' => $theIssue->getProject()->getKey(), 'issue_id' => $theIssue->getID(), 'field' => 'description')); ?>" method="post" onSubmit="setField('<?php echo make_url('issue_setfield', array('project_key' => $theIssue->getProject()->getKey(), 'issue_id' => $theIssue->getID(), 'field' => 'description')) ?>', 'description'); return false;">
										<?php include_template('main/textarea', array('area_name' => 'value', 'area_id' => 'description_form_value', 'height' => '100px', 'width' => '100%', 'value' => ($theIssue->getDescription()))); ?>
										<br>
										<input type="submit" value="<?php echo __('Save'); ?>" style="font-weight: bold;"><?php echo __('%save% or %cancel%', array('%save%' => '', '%cancel%' => javascript_link_tag(__('cancel'), array('style' => 'font-weight: bold;', 'onclick' => "$('description_change').hide();".(($theIssue->getDescription() != '') ? "$('description_name').show();" : "$('no_description').show();")."return false;")))); ?>
									</form>
									<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'description_spinning')); ?>
									<div id="description_change_error" class="error_message" style="display: none;"></div>
								</div>
								<?php endif; ?>
							</div>
						</div>
						<br />
						<div id="reproduction_steps_field"<?php if (!$theIssue->isReproductionStepsVisible()): ?> style="display: none;"<?php endif; ?> class="hoverable">
							<div id="reproduction_steps_header" class="rounded_box invisible nohover viewissue_reproduction_steps<?php if ($theIssue->isReproduction_StepsChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$theIssue->isReproduction_StepsMerged()): ?> issue_detail_unmerged<?php endif; ?>" style="margin: 0;">
								<div class="viewissue_reproduction_steps_header">
									<?php if ($theIssue->canEditReproductionSteps()): ?>
										<a href="javascript:void(0);" onclick="revertField('<?php echo make_url('issue_revertfield', array('project_key' => $theIssue->getProject()->getKey(), 'issue_id' => $theIssue->getID(), 'field' => 'reproduction_steps')); ?>', 'reproduction_steps');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a> <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'reproduction_steps_undo_spinning')); ?>
										<?php echo image_tag('icon_edit.png', array('class' => 'dropdown', 'id' => 'reproduction_steps_edit', 'onclick' => "$('reproduction_steps_change').show(); $('reproduction_steps_name').hide(); $('no_reproduction_steps').hide();", 'title' => __('Click here to edit reproduction steps'))); ?>
									<?php endif; ?>
									<?php echo __('Reproduction steps'); ?>:
								</div>
								<div id="reproduction_steps_content" class="<?php if ($theIssue->isReproduction_StepsChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$theIssue->isReproduction_StepsMerged()): ?> issue_detail_unmerged<?php endif; ?>">
									<div class="faded_medium" id="no_reproduction_steps" <?php if ($theIssue->getReproductionSteps() != ''):?> style="display: none;" <?php endif; ?>><?php echo __('Nothing entered.'); ?></div>
									<div id="reproduction_steps_name">
										<?php if ($theIssue->getReproductionSteps()): ?>
											<?php echo tbg_parse_text($theIssue->getReproductionSteps(), false, null, array('headers' => false)); ?>
										<?php endif; ?>
									</div>
								</div>
								<?php if ($theIssue->canEditReproductionSteps()): ?>
								<div id="reproduction_steps_change" style="display: none;">
									<form id="reproduction_steps_form" action="<?php echo make_url('issue_setfield', array('project_key' => $theIssue->getProject()->getKey(), 'issue_id' => $theIssue->getID(), 'field' => 'reproduction_steps')); ?>" method="post" onSubmit="setField('<?php echo make_url('issue_setfield', array('project_key' => $theIssue->getProject()->getKey(), 'issue_id' => $theIssue->getID(), 'field' => 'reproduction_steps')) ?>', 'reproduction_steps'); return false;">
										<?php include_template('main/textarea', array('area_name' => 'value', 'area_id' => 'reproduction_steps_form_value', 'height' => '100px', 'width' => '100%', 'value' => ($theIssue->getReproductionSteps()))); ?>
										<br>
										<input type="submit" value="<?php echo __('Save'); ?>" style="font-weight: bold;"><?php echo __('%save% or %cancel%', array('%save%' => '', '%cancel%' => javascript_link_tag(__('cancel'), array('style' => 'font-weight: bold;', 'onclick' => "$('reproduction_steps_change').hide();".(($theIssue->getReproductionSteps() != '') ? "$('reproduction_steps_name').show();" : "$('no_reproduction_steps').show();")."return false;")))); ?>
									</form>
									<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'reproduction_steps_spinning')); ?>
									<div id="reproduction_steps_change_error" class="error_message" style="display: none;"></div>
								</div>
								<?php endif; ?>
							</div>
						</div>
							<br />
						<?php include_component('main/issuemaincustomfields', array('issue' => $theIssue)); ?>
						<?php TBGEvent::createNew('core', 'viewissue_right_bottom', $theIssue)->trigger(); ?>
					</td>
				</tr>
			</table>
		</div>
		<?php TBGEvent::createNew('core', 'viewissue_before_tabs', $theIssue)->trigger(); ?>
		<div style="clear: both; height: 30px; margin: 20px 5px 0 5px;" class="tab_menu">
			<ul id="viewissue_menu">
				<li id="tab_comments" class="selected"><?php echo javascript_link_tag(image_tag('icon_comments.png', array('style' => 'float: left; margin-right: 5px;')) . __('Comments (%count%)', array('%count%' => '<span id="viewissue_comment_count">'.$theIssue->getCommentCount().'</span>')), array('onclick' => "switchSubmenuTab('tab_comments', 'viewissue_menu');")); ?></li>
				<li id="tab_attached_information"><?php echo javascript_link_tag(image_tag('icon_attached_information.png', array('style' => 'float: left; margin-right: 5px;')) . __('Attached information (%count%)', array('%count%' => '<span id="viewissue_uploaded_attachments_count">'.(count($theIssue->getLinks()) + count($theIssue->getFiles())).'</span>')), array('onclick' => "switchSubmenuTab('tab_attached_information', 'viewissue_menu');")); ?></li>
				<li id="tab_related_issues_and_tasks"><?php echo javascript_link_tag(image_tag('icon_related_issues.png', array('style' => 'float: left; margin-right: 5px;')) . __('Related issues and tasks'), array('onclick' => "switchSubmenuTab('tab_related_issues_and_tasks', 'viewissue_menu');")); ?></li>
				<li id="tab_duplicate_issues"><?php echo javascript_link_tag(image_tag('icon_duplicate_issues.png', array('style' => 'float: left; margin-right: 5px;')) . __('Duplicate issues (%count%)', array('%count%' => '<span id="viewissue_duplicate_issues_count">'.(count($theIssue->getDuplicateIssues())).'</span>')), array('onclick' => "switchSubmenuTab('tab_duplicate_issues', 'viewissue_menu');")); ?></li>
				<?php TBGEvent::createNew('core', 'viewissue_tabs', $theIssue)->trigger(); ?>
			</ul>
		</div>
		<div id="viewissue_menu_panes">
			<?php TBGEvent::createNew('core', 'viewissue_tab_panes_front', $theIssue)->trigger(); ?>
			<div id="tab_comments_pane" style="padding-top: 0; margin: 0 5px 0 5px;" class="comments">
				<div id="viewissue_comments">
					<?php if ($tbg_user->canPostComments()): ?>
						<table border="0" cellpadding="0" cellspacing="0" style="margin: 5px;" id="comment_add_button"><tr><td class="nice_button" style="font-size: 13px; margin-left: 0;"><input type="button" onclick="$('comment_add_button').hide(); $('comment_add').show();$('comment_title').focus();" value="<?php echo __('Add new comment'); ?>"></td></tr></table>
						<div id="comment_add" class="comment_add" style="<?php if (!(isset($comment_error) && $comment_error)): ?>display: none; <?php endif; ?>margin-top: 5px;">
							<div class="comment_add_main">
								<div class="comment_add_title"><?php echo __('Create a comment'); ?></div><br>
								<form id="comment_form" action="<?php echo make_url('comment_add', array('project_id' => $theIssue->getProject()->getID(), 'comment_applies_id' => $theIssue->getID(), 'comment_applies_type' => 1, 'comment_module' => 'core')); ?>" method="post" onSubmit="return addComment('<?php echo make_url('comment_add', array('project_id' => $theIssue->getProject()->getID(), 'comment_applies_id' => $theIssue->getID(), 'comment_applies_type' => 1, 'comment_module' => 'core')); ?>', 'viewissue_comment_count');">
									<label for="comment_title"><?php echo __('Comment title'); ?> <span class="faded_medium">(<?php echo __('optional'); ?>)</span></label><br />
									<input type="text" class="comment_titlebox" id="comment_title" name="comment_title"<?php if (isset($comment_error) && $comment_error): ?>value="<?php echo $comment_error_title; ?>"<?php endif; ?> /><br />
									<label for="comment_visibility"><?php echo __('Visibility'); ?> <span class="faded_medium">(<?php echo __('whether to hide this comment for "regular users"'); ?>)</span></label><br />
									<select class="comment_visibilitybox" id="comment_visibility" name="comment_visibility">
										<option value="1"><?php echo __('Visible for all users'); ?></option>
										<option value="0"><?php echo __('Visible for me, developers and administrators only'); ?></option>
									</select>
									<br />
									<label for="comment_bodybox"><?php echo __('Comment'); ?></label><br />
									<?php include_template('main/textarea', array('area_name' => 'comment_body', 'area_id' => 'comment_bodybox', 'height' => '200px', 'width' => '970px', 'value' => ((isset($comment_error) && $comment_error) ? $comment_error_body : ''))); ?>

									<div id="comment_add_indicator" style="display: none;">
										<?php echo image_tag('spinning_16.gif', array('class' => 'spinning')); ?>
									</div>

									<div style="margin: 10px 0 10px 0; font-size: 12px;">
										<input type="checkbox" name="comment_save_changes" id="comment_save_changes" value="1"<?php if (isset($issue_unsaved) || (isset($comment_error) && $comment_error)): ?> checked<?php endif; ?>>&nbsp;<label for="comment_save_changes"><?php echo __('Save my changes with this comment'); ?></label>
									</div>

									<div id="comment_add_controls" class="comment_controls">
										<input type="hidden" name="forward_url" value="<?php echo make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getIssueNo())); ?>">
										<?php echo __('%create_comment% or %cancel%', array('%create_comment%' => '<input type="submit" class="comment_addsave" value="'.__('Create comment').'" />', '%cancel%' => '<a href="javascript:void(0)" onClick="$(\'comment_add\').hide();$(\'comment_add_button\').show();">'.__('cancel').'</a>')); ?>
									</div>
								</form>
							</div>
						</div>
					<?php endif; ?>
					<div class="faded_medium comments_none" id="comments_none" <?php if (count(TBGComment::getComments($theIssue->getID(), 1)) != 0): ?>style="display: none;"<?php endif; ?>><?php echo __('There are no comments'); ?></div>
					<div id="comments_box">
						<?php foreach (TBGComment::getComments($theIssue->getID(), 1) as $aComment): ?>
							<?php include_template('main/comment', array('aComment' => $aComment, 'theIssue' => $theIssue)); ?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<div id="tab_attached_information_pane" style="padding-top: 0; margin: 0 5px 0 5px; display: none;">
				<div id="viewissue_attached_information">
					<?php if ($theIssue->canAttachLinks() || (TBGSettings::isUploadsEnabled() && $theIssue->canAttachFiles())): ?>
						<?php if ($theIssue->canAttachLinks()): ?>
							<table border="0" cellpadding="0" cellspacing="0" style="margin: 5px; float: left;" id="comment_add_button"><tr><td class="nice_button" style="font-size: 13px; margin-left: 0;"><input type="button" onclick="$('attach_link').show();" value="<?php echo __('Attach a link'); ?>"></td></tr></table>
						<?php endif; ?>
						<?php if (TBGSettings::isUploadsEnabled() && $theIssue->canAttachFiles()): ?>
							<table border="0" cellpadding="0" cellspacing="0" style="margin: 5px; float: left;" id="comment_add_button"><tr><td class="nice_button" style="font-size: 13px; margin-left: 0;"><input type="button" onclick="$('attach_file').show();" value="<?php echo __('Attach a file'); ?>"></td></tr></table>
						<?php else: ?>
							<table border="0" cellpadding="0" cellspacing="0" style="margin: 5px; float: left;" id="comment_add_button"><tr><td class="nice_button disabled" style="font-size: 13px; margin-left: 0;"><input type="button" onclick="failedMessage('<?php echo __('File uploads are not enabled'); ?>');" value="<?php echo __('Attach a file'); ?>"></td></tr></table>
						<?php endif; ?>
						<br style="clear: both;">
					<?php endif; ?>
					<div class="rounded_box mediumgrey shadowed" id="attach_link" style="margin: 5px 0 5px 0; display: none; position: absolute; width: 350px;">
						<div class="header_div" style="margin: 0 0 5px 0;"><?php echo __('Attach a link'); ?>:</div>
						<form action="<?php echo make_url('issue_attach_link', array('issue_id' => $theIssue->getID())); ?>" method="post" onsubmit="attachLink('<?php echo make_url('issue_attach_link', array('issue_id' => $theIssue->getID())); ?>');return false;" id="attach_link_form">
							<dl style="margin: 0;">
								<dt style="width: 80px; padding-top: 3px;"><label for="attach_link_url"><?php echo __('URL'); ?>:</label></dt>
								<dd style="margin-bottom: 0px;"><input type="text" name="link_url" id="attach_link_url" style="width: 235px;"></dd>
								<dt style="width: 80px; font-size: 10px; padding-top: 4px;"><label for="attach_link_description"><?php echo __('Description'); ?>:</label></dt>
								<dd style="margin-bottom: 0px;"><input type="text" name="description" id="attach_link_description" style="width: 235px;"></dd>
							</dl>
							<div style="font-size: 12px; padding: 15px 2px 10px 2px;" class="faded_medium" id="attach_link_submit"><?php echo __('Enter the link URL here, along with an optional description. Press "%attach_link%" to attach it to the issue.', array('%attach_link%' => __('Attach link'))); ?></div>
							<div style="text-align: center; padding: 10px; display: none;" id="attach_link_indicator"><?php echo image_tag('spinning_26.gif'); ?></div>
							<div style="text-align: center;"><input type="submit" value="<?php echo __('Attach link'); ?>" style="font-weight: bold;"><?php echo __('%attach_link% or %cancel%', array('%attach_link%' => '', '%cancel%' => '<b>'.javascript_link_tag(__('cancel'), array('onclick' => "$('attach_link').toggle();")).'</b>')); ?></div>
						</form>
					</div>
					<div class="no_items" id="viewissue_no_uploaded_files"<?php if (count($theIssue->getFiles()) + count($theIssue->getLinks()) > 0): ?> style="display: none;"<?php endif; ?>><?php echo __('There is nothing attached to this issue'); ?></div>
					<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0>
						<tbody id="viewissue_uploaded_links" class="canhover_light">
							<?php foreach ($theIssue->getLinks() as $link_id => $link): ?>
								<?php include_template('attachedlink', array('issue' => $theIssue, 'link' => $link, 'link_id' => $link_id)); ?>
							<?php endforeach; ?>
						</tbody>
					</table>
					<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0>
						<tbody id="viewissue_uploaded_files" class="canhover_light">
							<?php foreach ($theIssue->getFiles() as $file_id => $file): ?>
								<?php include_template('attachedfile', array('base_id' => 'viewissue_files', 'mode' => 'issue', 'issue' => $theIssue, 'file' => $file, 'file_id' => $file_id)); ?>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div id="tab_related_issues_and_tasks_pane" style="padding-top: 5px; margin: 0 5px 0 5px; display: none;">
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 5px; float: left;" id="add_task_button"><tr><td class="nice_button" style="font-size: 13px; margin-left: 0;"><input type="button" onclick="$('viewissue_add_task_div').toggle();" value="<?php echo __('Add a task to this issue'); ?>"></td></tr></table>
				<table border="0" cellpadding="0" cellspacing="0" style="margin: 5px; float: left;" id="relate_to_existing_issue_button"><tr><td class="nice_button" style="font-size: 13px; margin-left: 0;"><input type="button" onclick="showFadedBackdrop('<?php echo make_url('get_partial_for_backdrop', array('key' => 'relate_issue', 'issue_id' => $theIssue->getID())); ?>');" value="<?php echo __('Relate to an existing issue'); ?>"></td></tr></table>
				<br style="clear: both;">
				<div class="rounded_box mediumgrey shadowed" id="viewissue_add_task_div" style="margin: 5px 0 5px 0; display: none; position: absolute; font-size: 12px; width: 400px;">
					<form id="viewissue_add_task_form" action="<?php echo make_url('project_scrum_story_addtask', array('project_key' => $theIssue->getProject()->getKey(), 'story_id' => $theIssue->getID(), 'mode' => 'issue')); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="addUserStoryTask('<?php echo make_url('project_scrum_story_addtask', array('project_key' => $theIssue->getProject()->getKey(), 'story_id' => $theIssue->getID(), 'mode' => 'issue')); ?>', <?php echo $theIssue->getID(); ?>, 'issue');return false;">
						<div>
							<label for="viewissue_task_name_input"><?php echo __('Add task'); ?>&nbsp;</label>
							<input type="text" name="task_name" id="viewissue_task_name_input">
							<input type="submit" value="<?php echo __('Add task'); ?>">
							<?php echo __('%add_task% or %cancel%', array('%add_task%' => '', '%cancel%' => '<a href="javascript:void(0);" onclick="$(\'viewissue_add_task_div\').toggle();">' . __('cancel') . '</a>')); ?>
							<?php echo image_tag('spinning_20.gif', array('id' => 'add_task_indicator', 'style' => 'display: none;')); ?><br>
						</div>
					</form>
				</div>
				<table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
					<tr>
						<td id="related_parent_issues_inline" style="width: 360px;">
							<?php $p_issues = 0; ?>
							<?php foreach ($theIssue->getParentIssues() as $parent_issue): ?>
								<?php if ($parent_issue->hasAccess()): ?>
									<?php include_template('main/relatedissue', array('theIssue' => $theIssue, 'related_issue' => $parent_issue)); ?>
									<?php $p_issues++; ?>
								<?php endif; ?>
							<?php endforeach; ?>
							<div class="no_items" id="no_parent_issues"<?php if ($p_issues > 0): ?> style="display: none;"<?php endif; ?>><?php echo __('No other issues depends on this issue'); ?></div>
						</td>
						<td style="width: 40px; text-align: center; padding: 0;"><?php echo image_tag('left.png'); ?></td>
						<td style="width: auto;">
							<div class="rounded_box mediumgrey borderless" id="related_issues_this_issue" style="margin: 5px auto 5px auto;">
								<?php echo __('This issue'); ?>
							</div>
						</td>
						<td style="width: 40px; text-align: center; padding: 0;"><?php echo image_tag('right.png'); ?></td>
						<td id="related_child_issues_inline" style="width: 360px;">
							<?php $c_issues = 0; ?>
							<?php foreach ($theIssue->getChildIssues() as $child_issue): ?>
								<?php if ($child_issue->hasAccess()): ?>
									<?php include_template('main/relatedissue', array('theIssue' => $theIssue, 'related_issue' => $child_issue)); ?>
									<?php $c_issues++; ?>
								<?php endif; ?>
							<?php endforeach; ?>
							<div class="no_items" id="no_child_issues"<?php if ($c_issues > 0): ?> style="display: none;"<?php endif; ?>><?php echo __('This issue does not depend on any other issues'); ?></div>
						</td>
					</tr>
				</table>
			</div>
			<div id="tab_duplicate_issues_pane" style="padding-top: 0; margin: 0 5px 0 5px; display: none;">
				<br>
				<?php $data = $theIssue->getDuplicateIssues(); ?>
				<?php if (count($data) != 0): ?>
				<div class="header"><?php echo __('The following issues are duplicates of this issue:'); ?></div>
				<?php else: ?>
				<div class="no_items"><?php echo __('This issue has no duplicates'); ?></div>
				<?php endif; ?>
				<ul>
					<?php
					foreach ($data as $issue)
					{
						include_template('main/duplicateissue', array('duplicate_issue' => $issue));
					}
					?>
				</ul>
			</div>
			<?php TBGEvent::createNew('core', 'viewissue_tab_panes_back', $theIssue)->trigger(); ?>
		</div>
		<?php TBGEvent::createNew('core', 'viewissue_after_tabs', $theIssue)->trigger(); ?>
	</div>
<?php else: ?>
	<div class="rounded_box red borderless" id="viewissue_nonexisting" style="vertical-align: middle; padding: 5px; color: #222; font-weight: bold; font-size: 13px;">
		<div class="viewissue_info_header"><?php echo __("You have specified an issue that can't be shown"); ?></div>
		<div class="viewissue_info_content"><?php echo __("This could be because you the issue doesn't exist, has been deleted or you don't have permission to see it"); ?></div>
	</div>
<?php endif; ?>
