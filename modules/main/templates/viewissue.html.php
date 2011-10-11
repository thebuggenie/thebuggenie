<?php if ($issue instanceof TBGIssue): ?>
	<?php

		$tbg_response->addBreadcrumb(__('Issues'), make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), tbg_get_breadcrumblinks('project_summary', TBGContext::getCurrentProject()));
		$tbg_response->addBreadcrumb($issue->getFormattedIssueNo(true, true), make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issuelist);
		$tbg_response->setTitle('['.(($issue->isClosed()) ? mb_strtoupper(__('Closed')) : mb_strtoupper(__('Open'))) .'] ' . $issue->getFormattedIssueNo(true) . ' - ' . tbg_decodeUTF8($issue->getTitle()));
	
	?>
	<?php TBGEvent::createNew('core', 'viewissue_top', $issue)->trigger(); ?>
	<?php if (TBGSettings::isUploadsEnabled() && $issue->canAttachFiles()): ?>
		<?php include_component('main/uploader', array('issue' => $issue, 'mode' => 'issue')); ?>
	<?php endif; ?>
	<?php //include_component('main/hideableInfoBox', array('key' => 'viewissue_helpbox', 'title' => __('Editing issues'), 'content' => __('To edit any of the details in this issue, move your mouse over that detail and press the icon that appears. Changes you make will stay unsaved until you either press the "%save%" button that appears when you change the issue, or until you log out (the changes are then lost).', array('%save%' => __('Save'))))); ?>
	<div id="issuetype_indicator_fullpage" style="background-color: transparent; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; text-align: center; display: none;">
		<div style="position: absolute; top: 45%; left: 40%; z-index: 100001; color: #FFF; font-size: 15px; font-weight: bold;">
			<?php echo image_tag('spinning_32.gif'); ?><br>
			<?php echo __('Please wait while updating issue type'); ?>...
		</div>
		<div style="background-color: #000; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; z-index: 100000;" class="semi_transparent"> </div>
	</div>
	<div style="width: auto; text-align: left; padding: 5px; margin: 0 auto 0 auto;">
		<div id="viewissue_header_container">
			<table cellpadding=0 cellspacing=0 class="title_area" style="table-layout: fixed;">
				<tr>
					<td style="width: 80px;<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>" id="user_pain_additional">
						<div class="rounded_box green borderless" title="<?php echo __('This is the user pain value for this issue'); ?>" id="viewissue_triaging" style="margin: 0 5px 0 0; vertical-align: middle; padding: 5px; font-weight: bold; font-size: 13px; text-align: center">
							<div class="user_pain" id="issue_user_pain"><?php echo $issue->getUserPain(); ?></div>
							<div class="user_pain_calculated" id="issue_user_pain_calculated"><?php echo $issue->getUserPainDiffText(); ?></div>
						</div>
					</td>
					<td class="title_left_images">
						<?php if ($tbg_user->isGuest()): ?>
							<?php echo image_tag('star_faded.png', array('id' => 'issue_favourite_faded_'.$issue->getId(), 'title' => __('Please log in to bookmark issues'))); ?>
						<?php else: ?>
							<?php echo image_tag('spinning_20.gif', array('id' => 'issue_favourite_indicator_'.$issue->getId(), 'style' => 'display: none;')); ?>
							<?php echo image_tag('star_faded.png', array('id' => 'issue_favourite_faded_'.$issue->getId(), 'style' => 'cursor: pointer;'.(($tbg_user->isIssueStarred($issue->getID())) ? 'display: none;' : ''), 'title' => __('Click to start following this issue'), 'onclick' => "TBG.Issues.toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $issue->getID()))."', ".$issue->getID().");")); ?>
							<?php echo image_tag('star.png', array('id' => 'issue_favourite_normal_'.$issue->getId(), 'style' => 'cursor: pointer;'.((!$tbg_user->isIssueStarred($issue->getID())) ? 'display: none;' : ''), 'title' => __('Click to stop following this issue'), 'onclick' => "TBG.Issues.toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $issue->getID()))."', ".$issue->getID().");")); ?>
						<?php endif; ?>
					</td>
					<td class="title_left_images">
						<?php echo image_tag($issue->getIssueType()->getIcon() . '_small.png', array('id' => 'issuetype_image')); ?>
					</td>
					<td style="font-size: 17px; width: auto; padding: 5px 0 10px 7px;" id="title_field">
						<div class="viewissue_title hoverable">
							<span class="faded_out <?php if ($issue->isTitleChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isTitleMerged()): ?> issue_detail_unmerged<?php endif; ?>" id="title_header">
								<?php if ($issue->isEditable() && $issue->canEditTitle()): ?>
									<?php echo image_tag('icon_edit.png', array('class' => 'dropdown', 'id' => 'title_edit', 'onclick' => "$('title_change').show(); $('title_name').hide(); $('no_title').hide();")); ?>
									<a class="undo" href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'title')); ?>', 'title');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
									<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'title_undo_spinning')); ?>
								<?php endif; ?>
								<?php echo $issue->isClosed() ? mb_strtoupper(__('Closed')) : mb_strtoupper(__('Open')); ?>&nbsp;&nbsp;<b><?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), __('%issuetype% %issue_no%', array('%issuetype%' => $issue->getIssueType()->getName(), '%issue_no%' => $issue->getFormattedIssueNo(true)))); ?>&nbsp;&nbsp;-&nbsp;</b>
							</span>
							<span id="issue_title">
								<span id="title_content" class="<?php if ($issue->isTitleChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isTitleMerged()): ?> issue_detail_unmerged<?php endif; ?>">
									<span class="faded_out" id="no_title" <?php if ($issue->getTitle() != ''):?> style="display: none;" <?php endif; ?>><?php echo __('Nothing entered.'); ?></span>
									<span id="title_name" style="font-weight: bold;">
										<?php echo tbg_decodeUTF8($issue->getTitle()); ?>
									</span>
								</span>
							</span>
							<?php if ($issue->isEditable() && $issue->canEditTitle()): ?>
							<span id="title_change" style="display: none;">
								<form id="title_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'title')); ?>" method="post" onSubmit="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'title')) ?>', 'title'); return false;">
									<input type="text" name="value" value="<?php echo $issue->getTitle(); ?>" /><?php echo __('%save% or %cancel%', array('%save%' => '<input type="submit" value="'.__('Save').'">', '%cancel%' => '<a href="#" onClick="$(\'title_change\').hide(); $(\'title_name\').show(); return false;">'.__('cancel').'</a>')); ?>
								</form>
								<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'title_spinning')); ?>
								<span id="title_change_error" class="error_message" style="display: none;"></span>
							</span>
							<?php endif; ?>
						</div>
						<div style="font-size: 12px;">
							<?php echo '<b>' . __('Posted %posted_at_time% - updated %last_updated_at_time%', array('%posted_at_time%' => '</b><i title="'.tbg_formatTime($issue->getPosted(), 21).'">' . tbg_formatTime($issue->getPosted(), 20) . '</i><b>', '%last_updated_at_time%' => '</b><i title="'.tbg_formatTime($issue->getLastUpdatedTime(), 21).'">' . tbg_formatTime($issue->getLastUpdatedTime(), 20) . '</i>')); ?>
						</div>
					</td>
					<td style="width: 100px; text-align: right;<?php if (!$issue->isVotesVisible()): ?> display: none;<?php endif; ?>" id="votes_additional">
						<div id="viewissue_votes">
							<table align="right">
								<tr>
									<td id="vote_down">
										<?php $vote_down_options = ($issue->getProject()->isArchived() || $issue->hasUserVoted($tbg_user, false)) ? 'display: none;' : ''; ?>
										<?php $vote_down_faded_options = ($vote_down_options == '') ? 'display: none;' : ''; ?>
										<?php echo javascript_link_tag(image_tag('action_vote_minus.png'), array('onclick' => "TBG.Issues.voteDown('".make_url('issue_vote', array('issue_id' => $issue->getID(), 'vote' => 'down'))."');", 'id' => 'vote_down_link', 'class' => 'image', 'style' => $vote_down_options)); ?>
										<?php echo image_tag('spinning_16.gif', array('id' => 'vote_down_indicator', 'style' => 'display: none;')); ?>
										<?php echo image_tag('action_vote_minus_faded.png', array('id' => 'vote_down_faded', 'style' => $vote_down_faded_options)); ?>
									</td>
									<td class="votes">
										<div id="issue_votes"><?php echo $issue->getVotes(); ?></div>
										<div class="votes_header"><?php echo __('Votes'); ?></div>
									</td>
									<td id="vote_up">
										<?php $vote_up_options = ($issue->getProject()->isArchived() || $issue->hasUserVoted($tbg_user, true)) ? 'display: none;' : ''; ?>
										<?php $vote_up_faded_options = ($vote_up_options == '') ? 'display: none;' : ''; ?>
										<?php echo javascript_link_tag(image_tag('action_vote_plus.png'), array('onclick' => "TBG.Issues.voteUp('".make_url('issue_vote', array('issue_id' => $issue->getID(), 'vote' => 'up'))."');", 'id' => 'vote_up_link', 'class' => 'image', 'style' => $vote_up_options)); ?>
										<?php echo image_tag('spinning_16.gif', array('id' => 'vote_up_indicator', 'style' => 'display: none;')); ?>
										<?php echo image_tag('action_vote_plus_faded.png', array('id' => 'vote_up_faded', 'style' => $vote_up_faded_options)); ?>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
			</table>
			<div id="issue_info_container">
				<div class="issue_info error<?php if (isset($issue_unsaved)): ?> active<?php endif; ?>" id="viewissue_unsaved"<?php if (!isset($issue_unsaved)): ?> style="display: none;"<?php endif; ?>>
					<div class="header"><?php echo __('Could not save your changes'); ?></div>
				</div>
				<div class="issue_info error<?php if ($issue->hasMergeErrors()): ?> active<?php endif; ?>" id="viewissue_merge_errors"<?php if (!$issue->hasMergeErrors()): ?> style="display: none;"<?php endif; ?>>
					<div class="header"><?php echo __('This issue has been changed since you started editing it'); ?></div>
					<div class="content"><?php echo __('Data that has been changed is highlighted in red below. Undo your changes to see the updated information'); ?></div>
				</div>

				<div class="issue_info important" id="viewissue_changed" <?php if (!$issue->hasUnsavedChanges()): ?>style="display: none;"<?php endif; ?>>
					<form action="<?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?>" method="post">
						<div class="buttons">
							<input class="button button-silver" type="submit" value="<?php echo __('Save changes'); ?>">
							<button class="button button-silver" onclick="$('comment_add_button').hide(); $('comment_add').show();$('comment_save_changes').checked = true;$('comment_bodybox').focus();return false;"><?php echo __('Add comment and save changes'); ?></button>
						</div>
						<input type="hidden" name="issue_action" value="save">
					</form>
					<?php echo __("You have changed this issue, but haven't saved your changes yet. To save it, press the %save_changes% button to the right", array('%save_changes%' => '<b>' . __("Save changes") . '</b>')); ?>
				</div>
				<?php if (isset($error) && $error): ?>
					<div class="issue_info error" id="viewissue_error">
						<?php if ($error == 'transition_error'): ?>
							<div class="header"><?php echo __('There was an error trying to move this issue to the next step in the workflow'); ?></div>
							<div class="content" style="text-align: left;">
								<?php echo __('The following actions could not be performed because of missing or invalid values: %list%', array('%list%' => '')); ?><br>
								<ul>
									<?php foreach (TBGContext::getMessageAndClear('issue_workflow_errors') as $error_field): ?>
										<li><?php 

											switch ($error_field)
											{
												case TBGWorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES:
													echo __('Could not assign issue to the selected user because this users assigned issues limit is reached');
													break;
												case TBGWorkflowTransitionValidationRule::RULE_PRIORITY_VALID:
													echo __('Could not set priority');
													break;
												case TBGWorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID:
													echo __('Could not set reproducability');
													break;
												case TBGWorkflowTransitionValidationRule::RULE_RESOLUTION_VALID:
													echo __('Could not set resolution');
													break;
												case TBGWorkflowTransitionValidationRule::RULE_STATUS_VALID:
													echo __('Could not set status');
													break;
												case TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE:
													echo __('Could not assign issue to the any user or team because none were provided');
													break;
												case TBGWorkflowTransitionAction::ACTION_SET_MILESTONE:
													echo __('Could not assign the issue to a milestone because none was provided');
													break;
												case TBGWorkflowTransitionAction::ACTION_SET_PRIORITY:
													echo __('Could not set issue priority because none was provided');
													break;
												case TBGWorkflowTransitionAction::ACTION_SET_REPRODUCABILITY:
													echo __('Could not set issue reproducability because none was provided');
													break;
												case TBGWorkflowTransitionAction::ACTION_SET_RESOLUTION:
													echo __('Could not set issue resolution because none was provided');
													break;
												case TBGWorkflowTransitionAction::ACTION_SET_STATUS:
													echo __('Could not set issue status because none was provided');
													break;
												default:
													echo $error_field;
													break;
											}

										?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php else: ?>
							<div class="header"><?php echo __('There was an error trying to save changes to this issue'); ?></div>
							<div class="content">
								<?php if (isset($workflow_error) && $workflow_error): ?>
									<?php echo __('No workflow step matches this issue after changes are saved. Please either use the workflow action buttons, or make sure your changes are valid within the current project workflow for this issue type.'); ?>
								<?php else: ?>
									<?php echo $error; ?>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if (isset($issue_saved)): ?>
					<div class="issue_info successful" id="viewissue_saved">
						<?php echo __('Your changes has been saved'); ?>
						<div class="buttons">
							<button class="button button-silver" onclick="$('viewissue_saved').hide();"><?php echo __('OK'); ?></button>
						</div>
					</div>
				<?php endif; ?>
				<?php if (isset($issue_message)): ?>
					<div class="issue_info successful" id="viewissue_saved">
						<?php echo $issue_message; ?>
						<div class="buttons">
							<button class="button button-silver" onclick="$('viewissue_saved').hide();"><?php echo __('OK'); ?></button>
						</div>
					</div>
				<?php endif; ?>
				<?php if (isset($issue_file_uploaded)): ?>
					<div class="issue_info successful" id="viewissue_saved">
						<?php echo __('The file was attached to this issue'); ?>
						<div class="buttons">
							<button class="button button-silver" onclick="$('viewissue_saved').hide();"><?php echo __('OK'); ?></button>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($issue->isBeingWorkedOn() && $issue->isOpen()): ?>
					<div class="issue_info information" id="viewissue_being_worked_on">
						<?php if ($issue->getUserWorkingOnIssue()->getID() == $tbg_user->getID()): ?>
							<?php echo __('You have been working on this issue since %time%', array('%time%' => tbg_formatTime($issue->getWorkedOnSince(), 6))); ?>
						<?php elseif ($issue->getAssignee() instanceof TBGTeam): ?>
							<?php echo __('%teamname% has been working on this issue since %time%', array('%teamname%' => $issue->getAssignee()->getName(), '%time%' => tbg_formatTime($issue->getWorkedOnSince(), 6))); ?>
						<?php else: ?>
							<?php echo __('%user% has been working on this issue since %time%', array('%user%' => $issue->getUserWorkingOnIssue()->getNameWithUsername(), '%time%' => tbg_formatTime($issue->getWorkedOnSince(), 6))); ?>
						<?php endif; ?>
						<div class="buttons">
							<button class="button button-silver" onclick="$('viewissue_being_worked_on').hide();"><?php echo __('OK'); ?></button>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($issue->isBlocking()): ?>
					<div class="issue_info error" id="blocking_div">
						<?php echo __('This issue is blocking the next release'); ?>
					</div>
				<?php endif; ?>
				<?php if ($issue->isDuplicate()): ?>
					<div class="issue_info information" id="viewissue_duplicate">
						<?php echo image_tag('icon_info.png', array('style' => 'float: left; margin: 0 5px 0 5px;')); ?>
						<?php echo __('This issue is a duplicate of issue %link_to_duplicate_issue%', array('%link_to_duplicate_issue%' => link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getDuplicateOf()->getFormattedIssueNo())), $issue->getDuplicateOf()->getFormattedIssueNo(true)) . ' - "' . $issue->getDuplicateOf()->getTitle() . '"')); ?>
					</div>
				<?php endif; ?>
				<?php if ($issue->isClosed()): ?>
					<div class="issue_info information" id="viewissue_closed">
						<?php echo image_tag('icon_info.png', array('style' => 'float: left; margin: 0 5px 0 5px;')); ?>
						<?php echo __('This issue has been closed with status "%status_name%" and resolution "%resolution%".', array('%status_name%' => (($issue->getStatus() instanceof TBGStatus) ? $issue->getStatus()->getName() : __('Not determined')), '%resolution%' => (($issue->getResolution() instanceof TBGResolution) ? $issue->getResolution()->getName() : __('Not determined')))); ?>
					</div>
				<?php endif; ?>
				<?php if ($issue->getProject()->isArchived()): ?>
					<div class="issue_info important" id="viewissue_archived">
						<?php echo image_tag('icon_important.png', array('style' => 'float: left; margin: 0 5px 0 5px;')); ?>
						<?php echo __('The project this issue belongs to has been archived, and so this issue is now read only'); ?>
					</div>
				<?php endif; ?>
				<div class="issue_info_backdrop"></div>
			</div>
		</div>
		<div id="workflow_actions">
			<ul class="workflow_actions simple_list">
				<?php if ($issue->isWorkflowTransitionsAvailable()): ?>
					<?php $cc = 1; $num_transitions = count($issue->getAvailableWorkflowTransitions()); ?>
					<?php foreach ($issue->getAvailableWorkflowTransitions() as $transition): ?>
						<li class="workflow">
							<?php if ($transition->hasTemplate()): ?>
								<input class="button button-silver<?php if ($cc == 1): ?> first<?php endif; if ($cc == $num_transitions): ?> last<?php endif; ?>" type="button" value="<?php echo $transition->getName(); ?>" onclick="TBG.Issues.showWorkflowTransition(<?php echo $transition->getID(); ?>);">
							<?php else: ?>
								<form action="<?php echo make_url('transition_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'transition_id' => $transition->getID())); ?>" method="post">
									<input type="submit" class="button button-silver<?php if ($cc == 1): ?> first<?php endif; if ($cc == $num_transitions): ?> last<?php endif; ?>" value="<?php echo $transition->getName(); ?>">
								</form>
							<?php endif; ?>
						</li>
						<?php $cc++; ?>
					<?php endforeach; ?>
					<li class="more_actions">
						<input class="button button-silver first last" id="more_actions_button" type="button" value="<?php echo __('More actions'); ?>" onclick="$(this).toggleClassName('button-pressed');$('more_actions').toggle();">
					</li>
				</ul>
				<?php if (!$issue->getProject()->isArchived() && (TBGContext::getUser()->hasPermission('caneditissue') || TBGContext::getUser()->hasPermission('caneditissuebasic'))): ?>
					<ul id="more_actions" style="display: none; position: absolute; width: 300px; top: 21px; right: 0; z-index: 1000;" class="simple_list rounded_box white shadowed" onclick="$('more_actions_button').toggleClassName('button-pressed');$('more_actions').toggle();">
						<li class="header"><?php echo __('Additional actions available'); ?></li>
						<?php if ($issue->isOpen()): ?>
							<?php if ($issue->isBlocking()): ?>
								<li><?php echo link_tag(make_url('unblock', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getId())), image_tag('icon_unblock.png', array('style' => 'float: left; margin-right: 5px;')) . __("Mark as not blocking the next release")); ?></li>
							<?php else: ?>
								<li><?php echo link_tag(make_url('block', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getId())), image_tag('icon_block.png', array('style' => 'float: left; margin-right: 5px;')) . __("Mark as blocking the next release")); ?></li>
							<?php endif; ?>
						<?php endif; ?>
						<li><a href="javascript:void(0)" onClick="$('move_issue').toggle();"><?php echo image_tag('icon_move.png', array('style' => 'float: left; margin-right: 5px;')) . __("Move issue to another project"); ?></a></li>
						<li style="display: none;" id="move_issue">
							<form action="<?php echo make_url('move_issue', array('issue_id' => $issue->getID())); ?>" method="post">
								<div class="rounded_box borderless yellow" style="margin: 5px 0 20px 0;">
									<b><?php echo __('Move issue to a different project'); ?></b>
									<p><?php echo __('Please be aware that moving this issue to a different project will reset details such as status, category, etc., and may also make some fields invisible, depending on the issue type configuration for that project. The issue will also be renumbered.'); ?></p>
									<br>
									<label for="move_issue_project"><?php echo __('Move issue to'); ?></label><br>
									<select name="project_id">
										<?php foreach (TBGProject::getAll() as $project): ?>
											<?php if (!$tbg_user->canReportIssues($project) && $project->getID() != $issue->getProject()->getID()) continue; ?>
											<option value="<?php echo $project->getID(); ?>"<?php if ($project->getID() == $issue->getProject()->getID()): ?> selected<?php endif; ?>><?php echo $project->getName(); ?></option>
										<?php endforeach; ?>
									</select>
									<div style="text-align: right; padding-top: 5px;">
										<input type="submit" value="<?php echo __('Move issue'); ?>" onclick="$(this).hide();$('move_issue_indicator').show();">
										<?php echo image_tag('spinning_16.gif', array('id' => 'move_issue_indicator', 'style' => 'display: none; margin-right: 5px;')); ?>
										<?php echo __('%move_issue% or %cancel%', array('%move_issue%' => '', '%cancel%' => '')); ?>
										<a href="javascript:void(0)" onclick="$('move_issue').hide();"><?php echo __('cancel'); ?></a>
									</div>
								</div>
							</form>
						</li>
						<?php if (TBGContext::getUser()->hasPermission('candeleteissues') || TBGContext::getUser()->hasPermission('caneditissue')): ?>
							<li><a href="javascript:void(0)" onClick="TBG.Main.Helpers.Dialog.show('<?php echo __('Permanently delete this issue?'); ?>', '<?php echo __('Are you sure you wish to delete this issue? It will remain in the database for your records, but will not be accessible via The Bug Genie.'); ?>', {yes: {href: '<?php echo make_url('deleteissue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getId())); ?>' }, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo image_tag('icon_delete.png', array('style' => 'float: left; margin-right: 5px;')) . __("Permanently delete this issue"); ?></a></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<div id="viewissue_left_box_top">
			<div id="issue_view">
				<fieldset id="issue_details">
					<legend><?php echo __('Issue details'); ?></legend>
					<?php TBGEvent::createNew('core', 'viewissue_left_top', $issue)->trigger(); ?>
					<?php include_component('main/issuedetailslisteditable', array('issue' => $issue)); ?>
					<div style="clear: both; margin-bottom: 5px;"> </div>
					<?php TBGEvent::createNew('core', 'viewissue_left_bottom', $issue)->trigger(); ?>
				</fieldset>
				<div class="issue_main">
					<?php TBGEvent::createNew('core', 'viewissue_right_top', $issue)->trigger(); ?>
					<fieldset id="description_field"<?php if (!$issue->isDescriptionVisible()): ?> style="display: none;"<?php endif; ?> class="viewissue_description<?php if ($issue->isDescriptionChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isDescriptionMerged()): ?> issue_detail_unmerged<?php endif; ?> hoverable">
						<legend id="description_header">
							<?php if ($issue->isEditable() && $issue->canEditDescription()): ?>
								<a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'description')); ?>', 'description');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a> <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'description_undo_spinning')); ?>
								<?php echo image_tag('icon_edit.png', array('class' => 'dropdown', 'id' => 'description_edit', 'onclick' => "$('description_change').show(); $('description_name').hide(); $('no_description').hide();", 'title' => __('Click here to edit description'))); ?>
							<?php endif; ?>
							<?php echo __('Issue description'); ?>
						</legend>
						<div id="description_content" class="<?php if ($issue->isDescriptionChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isDescriptionMerged()): ?> issue_detail_unmerged<?php endif; ?>">
							<div class="faded_out" id="no_description" <?php if ($issue->getDescription() != ''):?> style="display: none;" <?php endif; ?>><?php echo __('Nothing entered.'); ?></div>
							<div id="description_name" class="issue_inline_description">
								<?php if ($issue->getDescription()): ?>
									<?php echo tbg_parse_text($issue->getDescription(), false, null, array('headers' => false, 'issue' => $issue)); ?>
								<?php endif; ?>
							</div>
						</div>
						<?php if ($issue->isEditable() && $issue->canEditDescription()): ?>
							<div id="description_change" style="display: none;">
								<form id="description_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'description')); ?>" method="post" onSubmit="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'description')) ?>', 'description'); return false;">
									<?php include_template('main/textarea', array('area_name' => 'value', 'area_id' => 'description_form_value', 'height' => '250px', 'width' => '100%', 'value' => ($issue->getDescription()))); ?>
									<br>
									<input class="button button-silver" style="float: left; margin: -3px 5px 0 0; font-weight: bold;" type="submit" value="<?php echo __('Save'); ?>"><?php echo __('%save% or %cancel%', array('%save%' => '', '%cancel%' => javascript_link_tag(__('cancel'), array('style' => 'font-weight: bold;', 'onclick' => "$('description_change').hide();".(($issue->getDescription() != '') ? "$('description_name').show();" : "$('no_description').show();")."return false;")))); ?>
								</form>
								<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'description_spinning')); ?>
								<div id="description_change_error" class="error_message" style="display: none;"></div>
							</div>
						<?php endif; ?>
					</fieldset>
					<fieldset id="reproduction_steps_field"<?php if (!$issue->isReproductionStepsVisible()): ?> style="display: none;"<?php endif; ?> class="hoverable<?php if ($issue->isReproduction_StepsChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isReproduction_StepsMerged()): ?> issue_detail_unmerged<?php endif; ?>">
						<legend id="reproduction_steps_header">
							<?php if ($issue->isEditable() && $issue->canEditReproductionSteps()): ?>
								<a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'reproduction_steps')); ?>', 'reproduction_steps');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a> <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'reproduction_steps_undo_spinning')); ?>
								<?php echo image_tag('icon_edit.png', array('class' => 'dropdown', 'id' => 'reproduction_steps_edit', 'onclick' => "$('reproduction_steps_change').show(); $('reproduction_steps_name').hide(); $('no_reproduction_steps').hide();", 'title' => __('Click here to edit reproduction steps'))); ?>
							<?php endif; ?>
							<?php echo __('Steps to reproduce this issue'); ?>
						</legend>
						<div id="reproduction_steps_content" class="<?php if ($issue->isReproduction_StepsChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isReproduction_StepsMerged()): ?> issue_detail_unmerged<?php endif; ?>">
							<div class="faded_out" id="no_reproduction_steps" <?php if ($issue->getReproductionSteps() != ''):?> style="display: none;" <?php endif; ?>><?php echo __('Nothing entered.'); ?></div>
							<div id="reproduction_steps_name" class="issue_inline_description">
								<?php if ($issue->getReproductionSteps()): ?>
									<?php echo tbg_parse_text($issue->getReproductionSteps(), false, null, array('headers' => false, 'issue' => $issue)); ?>
								<?php endif; ?>
							</div>
						</div>
						<?php if ($issue->isEditable() && $issue->canEditReproductionSteps()): ?>
							<div id="reproduction_steps_change" style="display: none;">
								<form id="reproduction_steps_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'reproduction_steps')); ?>" method="post" onSubmit="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'reproduction_steps')) ?>', 'reproduction_steps'); return false;">
									<?php include_template('main/textarea', array('area_name' => 'value', 'area_id' => 'reproduction_steps_form_value', 'height' => '250px', 'width' => '100%', 'value' => ($issue->getReproductionSteps()))); ?>
									<br>
									<input class="button button-silver" style="float: left; margin: -3px 5px 0 0; font-weight: bold;" type="submit" value="<?php echo __('Save'); ?>"><?php echo __('%save% or %cancel%', array('%save%' => '', '%cancel%' => javascript_link_tag(__('cancel'), array('style' => 'font-weight: bold;', 'onclick' => "$('reproduction_steps_change').hide();".(($issue->getReproductionSteps() != '') ? "$('reproduction_steps_name').show();" : "$('no_reproduction_steps').show();")."return false;")))); ?>
								</form>
								<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'reproduction_steps_spinning')); ?>
								<div id="reproduction_steps_change_error" class="error_message" style="display: none;"></div>
							</div>
						<?php endif; ?>
					</fieldset>
					<br />
					<?php include_component('main/issuemaincustomfields', array('issue' => $issue)); ?>
					<?php TBGEvent::createNew('core', 'viewissue_right_bottom', $issue)->trigger(); ?>
				</div>
			</div>
		</div>
		<?php TBGEvent::createNew('core', 'viewissue_before_tabs', $issue)->trigger(); ?>
		<div style="clear: both; height: 30px; margin: 20px 5px 0 5px;" class="tab_menu">
			<ul id="viewissue_menu">
				<li id="tab_comments" class="selected"><?php echo javascript_link_tag(image_tag('icon_comments.png', array('style' => 'float: left; margin-right: 5px;')) . __('Comments (%count%)', array('%count%' => '<span id="viewissue_comment_count">'.$issue->getCommentCount().'</span>')), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_comments', 'viewissue_menu');")); ?></li>
				<li id="tab_attached_information"><?php echo javascript_link_tag(image_tag('icon_attached_information.png', array('style' => 'float: left; margin-right: 5px;')) . __('Attached information (%count%)', array('%count%' => '<span id="viewissue_uploaded_attachments_count">'.(count($issue->getLinks()) + count($issue->getFiles())).'</span>')), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_attached_information', 'viewissue_menu');")); ?></li>
				<?php
					$editions = array();
					$components = array();
					$builds = array();
					
					if($issue->getProject()->isEditionsEnabled())
					{
						$editions = $issue->getEditions();
					}
					
					if($issue->getProject()->isComponentsEnabled())
					{
						$components = $issue->getComponents();
					}

					if($issue->getProject()->isBuildsEnabled())
					{
						$builds = $issue->getBuilds();
					}
					
					$count = count($editions) + count($components) + count($builds);				
				?>
				<li id="tab_affected"><?php echo javascript_link_tag(image_tag('cfg_icon_projecteditionsbuilds.png', array('style' => 'float: left; margin-right: 5px;')) . __('Affected items (%count%)', array('%count%' => '<span id="viewissue_affects_count">'.$count.'</span>')), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_affected', 'viewissue_menu');")); ?></li>
				<li id="tab_related_issues_and_tasks"><?php echo javascript_link_tag(image_tag('icon_related_issues.png', array('style' => 'float: left; margin-right: 5px;')) . __('Related issues and tasks (%count%)', array('%count%' => '<span id="viewissue_duplicate_issues_count">'.(count($issue->getParentIssues())+count($issue->getChildIssues())).'</span>')), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_related_issues_and_tasks', 'viewissue_menu');")); ?></li>
				<li id="tab_duplicate_issues"><?php echo javascript_link_tag(image_tag('icon_duplicate_issues.png', array('style' => 'float: left; margin-right: 5px;')) . __('Duplicate issues (%count%)', array('%count%' => '<span id="viewissue_duplicate_issues_count">'.(count($issue->getDuplicateIssues())).'</span>')), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_duplicate_issues', 'viewissue_menu');")); ?></li>
				<?php TBGEvent::createNew('core', 'viewissue_tabs', $issue)->trigger(); ?>
			</ul>
		</div>
		<div id="viewissue_menu_panes">
			<?php TBGEvent::createNew('core', 'viewissue_tab_panes_front', $issue)->trigger(); ?>
			<div id="tab_comments_pane" style="padding-top: 0; margin: 0 5px 0 5px;" class="comments">
				<div id="viewissue_comments">
					<?php include_template('main/comments', array('target_id' => $issue->getID(), 'target_type' => TBGComment::TYPE_ISSUE, 'comment_count_div' => 'viewissue_comment_count', 'save_changes_checked' => $issue->hasUnsavedChanges(), 'issue' => $issue, 'forward_url' => make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false))); ?>
				</div>
			</div>
			<div id="tab_attached_information_pane" style="padding-top: 0; margin: 0 5px 0 5px; display: none;">
				<div id="viewissue_attached_information">
					<?php if ($issue->isUpdateable() && ($issue->canAttachLinks() || (TBGSettings::isUploadsEnabled() && $issue->canAttachFiles()))): ?>
						<?php if ($issue->canAttachLinks()): ?>
							<button class="button button-green" style="margin: 5px 5px 5px 0; float: left;" id="attach_link_button" onclick="$('attach_link').toggle();"><?php echo __('Attach a link'); ?></button>
						<?php endif; ?>
						<?php if (TBGSettings::isUploadsEnabled() && $issue->canAttachFiles()): ?>
							<button class="button button-green" style="margin: 5px 5px 5px 0; float: left;" id="attach_file_button" onclick="$('attach_file').toggle();"><?php echo __('Attach a file'); ?></button>
						<?php else: ?>
							<button class="button button-green disabled" style="margin: 5px 5px 5px 0; float: left;" id="attach_file_button" onclick="TBG.Main.Helpers.Message.error('<?php echo __('File uploads are not enabled'); ?>');"><?php echo __('Attach a file'); ?></button>
						<?php endif; ?>
						<br style="clear: both;">
					<?php endif; ?>
					<div class="rounded_box mediumgrey shadowed" id="attach_link" style="margin: 5px 0 5px 0; display: none; position: absolute; width: 350px;">
						<div class="header_div" style="margin: 0 0 5px 0;"><?php echo __('Attach a link'); ?>:</div>
						<form action="<?php echo make_url('issue_attach_link', array('issue_id' => $issue->getID())); ?>" method="post" onsubmit="attachLink('<?php echo make_url('issue_attach_link', array('issue_id' => $issue->getID())); ?>');return false;" id="attach_link_form">
							<dl style="margin: 0; clear: both;">
								<dt style="width: 80px; padding-top: 3px;"><label for="attach_link_url"><?php echo __('URL'); ?>:</label></dt>
								<dd style="margin-bottom: 0px;"><input type="text" name="link_url" id="attach_link_url" style="width: 235px;"></dd>
								<dt style="width: 80px; font-size: 10px; padding-top: 4px;"><label for="attach_link_description"><?php echo __('Description'); ?>:</label></dt>
								<dd style="margin-bottom: 0px;"><input type="text" name="description" id="attach_link_description" style="width: 235px;"></dd>
							</dl>
							<br style="clear: both;">
							<div style="font-size: 12px; clear: both; padding: 15px 2px 10px 2px;"><?php echo __('Enter the link URL here, along with an optional description. Press "%attach_link%" to attach it to the issue.', array('%attach_link%' => __('Attach link'))); ?></div>
							<div style="text-align: center; padding: 10px; display: none;" id="attach_link_indicator"><?php echo image_tag('spinning_26.gif'); ?></div>
							<div style="text-align: center;"><input type="submit" value="<?php echo __('Attach link'); ?>" style="font-weight: bold;" id="attach_link_submit"><?php echo __('%attach_link% or %cancel%', array('%attach_link%' => '', '%cancel%' => '<b>'.javascript_link_tag(__('cancel'), array('onclick' => "$('attach_link').toggle();")).'</b>')); ?></div>
						</form>
					</div>
					<div class="no_items" id="viewissue_no_uploaded_files"<?php if (count($issue->getFiles()) + count($issue->getLinks()) > 0): ?> style="display: none;"<?php endif; ?>><?php echo __('There is nothing attached to this issue'); ?></div>
					<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0>
						<tbody id="viewissue_uploaded_links" class="hover_highlight">
							<?php foreach ($issue->getLinks() as $link_id => $link): ?>
								<?php include_template('attachedlink', array('issue' => $issue, 'link' => $link, 'link_id' => $link_id)); ?>
							<?php endforeach; ?>
						</tbody>
					</table>
					<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0>
						<tbody id="viewissue_uploaded_files" class="hover_highlight">
							<?php foreach ($issue->getFiles() as $file_id => $file): ?>
								<?php include_component('main/attachedfile', array('base_id' => 'viewissue_files', 'mode' => 'issue', 'issue' => $issue, 'file' => $file)); ?>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div id="tab_related_issues_and_tasks_pane" style="padding-top: 5px; margin: 0 5px 0 5px; display: none;">
				<div id="viewissue_related">
					<?php if ($issue->isUpdateable()): ?>
						<button id="add_task_button" style="margin-left: 5px; margin-right: 5px; float: left; margin-left: 0;" class="button button-green" onclick="$('viewissue_add_task_div').toggle();"><?php echo __('Add a task to this issue'); ?></button>
						<button id="relate_to_existing_issue_button" class="button button-green" style="margin-left: 5px; float: left; margin-left: 0;" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'relate_issue', 'issue_id' => $issue->getID())); ?>');"><?php echo __('Relate to an existing issue'); ?></button>
						<br style="clear: both;">
						<div class="rounded_box mediumgrey shadowed" id="viewissue_add_task_div" style="margin: 5px 0 5px 0; display: none; position: absolute; font-size: 12px; width: 400px;">
							<form id="viewissue_add_task_form" action="<?php echo make_url('project_scrum_story_addtask', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID(), 'mode' => 'issue')); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="TBG.Issues.addUserStoryTask('<?php echo make_url('project_scrum_story_addtask', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID(), 'mode' => 'issue')); ?>', <?php echo $issue->getID(); ?>, 'issue');return false;">
								<div>
									<label for="viewissue_task_name_input"><?php echo __('Add task'); ?>&nbsp;</label>
									<input type="text" name="task_name" id="viewissue_task_name_input">
									<input type="submit" value="<?php echo __('Add task'); ?>">
									<a class="close_micro_popup_link" href="javascript:void(0);" onclick="$('viewissue_add_task_div').toggle();"><?php echo __('Done'); ?></a>
									<?php echo image_tag('spinning_20.gif', array('id' => 'add_task_indicator', 'style' => 'display: none;')); ?><br>
								</div>
							</form>
						</div>
					<?php endif; ?>
					<table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
						<tr>
							<td id="related_parent_issues_inline" style="width: 360px;">
								<?php $p_issues = 0; ?>
								<?php foreach ($issue->getParentIssues() as $parent_issue): ?>
									<?php if ($parent_issue->hasAccess()): ?>
										<?php include_template('main/relatedissue', array('theIssue' => $issue, 'related_issue' => $parent_issue)); ?>
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
								<?php foreach ($issue->getChildIssues() as $child_issue): ?>
									<?php if ($child_issue->hasAccess()): ?>
										<?php include_template('main/relatedissue', array('theIssue' => $issue, 'related_issue' => $child_issue)); ?>
										<?php $c_issues++; ?>
									<?php endif; ?>
								<?php endforeach; ?>
								<div class="no_items" id="no_child_issues"<?php if ($c_issues > 0): ?> style="display: none;"<?php endif; ?>><?php echo __('This issue does not depend on any other issues'); ?></div>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div id="tab_duplicate_issues_pane" style="padding-top: 0; margin: 0 5px 0 5px; display: none;">
				<div id="viewissue_duplicates">
					<?php $data = $issue->getDuplicateIssues(); ?>
					<?php if (count($data) != 0): ?>
					<div class="header"><?php echo __('The following issues are duplicates of this issue:'); ?></div>
					<?php else: ?>
					<div class="no_items"><?php echo __('This issue has no duplicates'); ?></div>
					<?php endif; ?>
					<ul>
						<?php foreach ($data as $issue): ?>
							<?php include_template('main/duplicateissue', array('duplicate_issue' => $issue)); ?>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<div id="tab_affected_pane" style="padding-top: 0; margin: 0 5px 0 5px; display: none;">
				<div id="viewissue_affected">
					<?php include_component('main/issueaffected', array('issue' => $issue)); ?>
				</div>
			</div>
			<?php TBGEvent::createNew('core', 'viewissue_tab_panes_back', $issue)->trigger(); ?>
		</div>
		<?php TBGEvent::createNew('core', 'viewissue_after_tabs', $issue)->trigger(); ?>
	</div>
<?php else: ?>
	<div class="rounded_box red borderless" id="notfound_error">
		<div class="header"><?php echo __("You have specified an issue that can't be shown"); ?></div>
		<div class="content"><?php echo __("This could be because you the issue doesn't exist, has been deleted or you don't have permission to see it"); ?></div>
	</div>
	<?php return; ?>
<?php endif; ?>
<?php if ($issue->isWorkflowTransitionsAvailable()): ?>
	<?php foreach ($issue->getAvailableWorkflowTransitions() as $transition): ?>
		<?php if ($transition instanceof TBGWorkflowTransition && $transition->hasTemplate()): ?>
			<?php include_component($transition->getTemplate(), compact('issue', 'transition')); ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
