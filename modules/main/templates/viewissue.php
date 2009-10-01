<?php if ($theIssue instanceof BUGSissue): ?>
	<?php

		$bugs_response->addJavascript('viewissue.js');
		$bugs_response->setTitle('['.(($theIssue->isClosed()) ? strtoupper(__('Closed')) : strtoupper(__('Open'))) .'] ' . $theIssue->getFormattedIssueNo(true) . ' - ' . $theIssue->getTitle());
	
	?>
	<?php 

		BUGScontext::trigger('core', 'viewissue_top', $theIssue);
//		require_once(BUGScontext::getIncludePath() . 'js/viewissue_ajax.js.php');

	?>
	<div class="rounded_box red_borderless" id="viewissue_unsaved"<?php if (!isset($issue_unsaved)): ?> style="display: none;"<?php endif; ?>>
		<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
		<div class="xboxcontent" style="vertical-align: middle; padding: 5px; color: #222; font-weight: bold; font-size: 13px;">
			<div class="viewissue_info_header"><?php echo __('Could not save your changes'); ?></div>
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
	<div class="rounded_box red_borderless" id="viewissue_merge_errors"<?php if (!$theIssue->hasMergeErrors()): ?> style="display: none;"<?php endif; ?>>
		<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
		<div class="xboxcontent" style="vertical-align: middle; padding: 5px; color: #222; font-weight: bold; font-size: 13px;">
			<div class="viewissue_info_header"><?php echo __('This issue has been changed since you started editing it'); ?></div>
			<div class="viewissue_info_content"><?php echo __('Data that has been changed is highlighted in red below. Undo your changes to see the updated information'); ?></div>
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
	<?php if ($theIssue->isBeingWorkedOn()): ?>
	<form action="<?php echo make_url('issue_stopworking', array('project_key' => $theIssue->getProject()->getKey(), 'issue_id' => $theIssue->getFormattedIssueNo())); ?>" method="post">
		<div class="rounded_box yellow_borderless" id="viewissue_being_worked_on">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="vertical-align: middle; padding: 5px; color: #222; font-weight: bold; font-size: 13px;">
				<?php echo image_tag('action_start_working.png', array('style' => 'float: left; margin: 0 10px 0 5px;')); ?>
				<?php if ($theIssue->getUserWorkingOnIssue()->getID() == $bugs_user->getID()): ?>
					<div class="viewissue_info_header"><?php echo __('You have been working on this issue since %time%', array('%time%' => bugs_formatTime($theIssue->getWorkedOnSince(), 6))); ?></div>
					<div class="viewissue_info_content">
						<input type="submit" value="<?php echo __('Done'); ?>">
						<?php echo __('When you are finished working on this issue, click the %done% button to the right', array('%done%' => '<b>' . __('Done') . '</b>')); ?>
					</div>
				<?php else: ?>
					<div class="viewissue_info_header"><?php echo __('This issue has been worked on by %user% since %time%', array('%user%' => $theIssue->getUserWorkingOnIssue()->getNameWithUsername(), '%time%' => bugs_formatTime($theIssue->getWorkedOnSince(), 6))); ?></div>
					<div class="viewissue_info_content">
						<input type="hidden" name="perform_action" value="grab">
						<input type="submit" value="<?php echo __('Take over'); ?>">
						<?php echo __('If you want to start working on this issue instead, click the %take_over% button to the right', array('%take_over%' => '<b>' . __('Take over') . '</b>')); ?>
					</div>
				<?php endif; ?>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
	</form>
	<?php endif; ?>
	<form action="<?php echo make_url('saveissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())); ?>" method="post">
		<div class="rounded_box yellow_borderless" id="viewissue_changed" <?php if (!$theIssue->hasUnsavedChanges()): ?>style="display: none;"<?php endif; ?>>
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent">
				<div class="viewissue_info_header"><?php echo __('You have unsaved changes'); ?></div>
				<div class="viewissue_info_content">
					<input type="hidden" name="issue_action" value="save">
					<input type="submit" value="<?php echo __('Save changes'); ?>">
					<?php echo __("You have changed this issue, but haven't saved your changes yet. To save it, press the %save_changes% button to the right", array('%save_changes%' => '<b>' . __("Save changes") . '</b>')); ?>
				</div>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
	</form>
	<?php if (isset($issue_saved)): ?>
		<div class="rounded_box green_borderless" id="viewissue_saved">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent viewissue_info_header">
				<?php echo __('Your changes has been saved'); ?>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
	<?php endif; ?>
	<?php if (isset($upload_error)): ?>
		<div class="rounded_box red_borderless" id="upload_error_div">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="vertical-align: middle; padding: 5px; color: #222; font-weight: bold; font-size: 13px;">
				<?php echo __('There was an error with your upload: %error%', array('%error%' => $upload_error)); ?>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
	<?php endif; ?>
	<?php if ($theIssue->isBlocking()): ?>
		<div class="rounded_box red_borderless" id="blocking_div">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="vertical-align: middle; padding: 5px; color: #222; font-weight: bold; font-size: 13px;">
				<?php echo __('This issue is blocking the next release'); ?>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
	<?php endif; ?>
	<?php if ($theIssue->isDuplicate()): ?>
		<div class="rounded_box iceblue_borderless" id="viewissue_duplicate">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="vertical-align: middle; padding: 0;">
				<?php echo image_tag('icon_info_big.png'); ?>
				<div class="viewissue_info_header"><?php echo __('This issue is a duplicate of Issue %link_to_duplicate_issue%', array('%link_to_duplicate_issue%' => link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getDuplicateOf()->getFormattedIssueNo())), $theIssue->getDuplicateOf()->getFormattedIssueNo(true)) . ' - "' . $theIssue->getDuplicateOf()->getTitle() . '"')); ?></div>
				<div class="viewissue_info_content"><?php echo __('For more information you should visit the issue mentioned above, as this issue is not likely to be updated'); ?></div>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>								
	<?php endif; ?>
	<?php if ($theIssue->isClosed() && $theIssue->getPostedBy()->getID() == $bugs_user->getID() && !BUGSuser::isThisGuest()): ?>
		<div class="rounded_box iceblue_borderless" id="viewissue_closed_sameuser">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="vertical-align: middle; padding: 0;">
				<?php echo image_tag('icon_info_big.png'); ?>
				<div class="viewissue_info_header"><?php echo __('You reported this issue, and it was closed with status: %status_name%', array('%status_name%' => '<b style="color: ' . (($theIssue->getStatus() instanceof BUGSdatatype) ? $theIssue->getStatus()->getColor() : '#BBB') . '">' . (($theIssue->getStatus() instanceof BUGSdatatype) ? $theIssue->getStatus()->getName() : __('Not determined')) . '</b>')); ?></div>
				<div class="viewissue_info_content"><?php echo __('If you have new information and you think this issue should be reopened, then %reopen_the_issue%', array('%reopen_the_issue%' => link_tag(make_url('openissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_id' => $theIssue->getID())), __('reopen the issue')))); ?></div>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>								
	<?php elseif ($theIssue->isOpen() && $theIssue->getPostedBy()->getID() == $bugs_user->getID() && !BUGSuser::isThisGuest()): ?>
		<div class="rounded_box iceblue_borderless" id="viewissue_open_sameuser">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="vertical-align: middle; padding: 0;">
				<?php echo image_tag('icon_info_big.png'); ?>
				<div class="viewissue_info_header"><?php echo __('You reported this issue, and its status is currently: %status_name%', array('%status_name%' => '<b style="color: ' . (($theIssue->getStatus() instanceof BUGSdatatype) ? $theIssue->getStatus()->getColor() : '#BBB') . '">' . (($theIssue->getStatus() instanceof BUGSdatatype) ? $theIssue->getStatus()->getName() : __('Not determined')) . '</b>')); ?></div>
				<div class="viewissue_info_content"><?php echo __('If you think this issue should be closed without further investigation, then %close_the_issue%', array('%close_the_issue%' => link_tag(make_url('closeissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_id' => $theIssue->getID())), __('close the issue')))); ?></div>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>								
	<?php elseif ($theIssue->isClosed()): ?>
		<div class="rounded_box iceblue_borderless" id="viewissue_closed">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="vertical-align: middle; padding: 0;">
				<?php echo image_tag('icon_info_big.png'); ?>
				<div class="viewissue_info_header"><?php echo __('This issue has been closed with status: %status_name%.', array('%status_name%' => '<b style="color: ' . (($theIssue->getStatus() instanceof BUGSdatatype) ? $theIssue->getStatus()->getColor() : '#BBB') . '">' . (($theIssue->getStatus() instanceof BUGSdatatype) ? $theIssue->getStatus()->getName() : __('Not determined')) . '</b>')); ?></div>
				<div class="viewissue_info_content"><?php echo __('A closed issue will usually not be further updated - try %posting_a_comment%, or %report_a_new_issue%', array('%posting_a_comment%' => '<a href="#add_comment_location_core_1_' . $theIssue->getID() . '">' . __('posting a comment') . '</a>', '%report_a_new_issue%' => link_tag(make_url('reportissue'), __('report a new issue')))); ?></div>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>								
	<?php endif; ?>
	<table style="table-layout: auto; width: 100%; clear: both;" cellpadding=0 cellspacing=0 id="issue_view">
		<tr>
			<td id="issue_lefthand">
				<?php
				 
					BUGScontext::trigger('core', 'viewissue_left_top', $theIssue);
					
				?>
				<?php include_component('main/issuedetailslisteditable', array('issue' => $theIssue)); ?>
				<div style="clear: both; font-size: 1px;">&nbsp;</div>
				<div id="viewissue_attached_information">
					<div class="header_div">
						<?php echo __('Attached information'); ?>
					</div>
					<?php if (count($theIssue->getFiles()) + count($theIssue->getLinks()) == 0): ?>
						<div class="no_items"><?php echo __('There is nothing attached to this issue'); ?></div>
					<?php else: ?>
						<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0>
							<?php foreach ($allLinks as $aLink): ?>
								<tr>
									<td class="imgtd" style="width: 20px;"><?php echo image_tag('icon_link.png'); ?></td>
									<td><a href="<?php echo $aLink['url']; ?>" target="_blank"><?php echo $aLink['description']; ?></a></td>
									<?php if ($bugs_user->canRemoveAttachments()): ?>
										<td style="width: 15px;"><a href="viewissue.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&amp;links=true&amp;action=remove&amp;l_id=<?php echo $aLink['id']; ?>" class="image"><?php echo image_tag('action_cancel_small.png'); ?></a></td>
									<?php endif; ?>
								</tr>
							<?php endforeach; ?>
							<?php foreach ($allFiles as $aFile): ?>
								<tr>
									<td class="imgtd" style="width: 20px;"><?php echo image_tag('icon_file.png'); ?></td>
									<td><a href="<?php echo BUGScontext::getTBGPath() . 'files/' . $aFile['filename']; ?>" target="<?php echo $aFile['filename']; ?>"><?php echo $aFile['description']; ?></a></td>
									<?php if ($bugs_user->canRemoveAttachments()): ?>
										<td style="width: 15px;"><a href="viewissue.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&amp;files=true&amp;action=remove&amp;f_id=<?php echo $aFile['id']; ?>" class="image"><?php echo image_tag('action_cancel_small.png'); ?></a></td>
									<?php endif; ?>
								</tr>
							<?php endforeach; ?>
						</table>
					<?php endif; ?>
				</div>
				<?php if ($bugs_user->canAttachLinks()): ?>
					<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0>
						<tr>
							<td class="issuedetailstopheaderleft"><b><?php echo __('Attach a link to this issue'); ?></b></td>
							<td class="issuedetailstopheaderright" style="width: 15px; text-align: center;"><a class="image" href="javascript:void(0);" onclick="Element.show('attach_link');"><?php echo image_tag('icon_plus_tiny.png'); ?></a></td>
						</tr>
					</table>
					<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0>
						<tr>
							<td class="issuedetailscontentsleft" style="padding-top: 5px; padding-bottom: 5px; display: none;" id="attach_link">
								<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="viewissue.php" enctype="multipart/form-data" method="post" name="issue_add_links">
									<input type="hidden" name="issue_no" value="<?php echo $theIssue->getFormattedIssueNo(true); ?>">
									<input type="hidden" name="links" value="true">
									<input type="hidden" name="action" value="add">
									<table cellpadding=0 cellspacing=0 style="width: 100%;">
										<tr>
											<td style="width: 45px;"><b style="font-size: 10px;"><?php echo __('Descr.:'); ?></b></td>
											<td><input type="text" name="desc" value="" style="width: 100%;"></td>
										</tr>
									</table>
									<table cellpadding=0 cellspacing=0 style="width: 100%;">
										<tr>
											<td style="width: 45px;"><b><?php echo __('URL:') ?></b></td>
											<td><input type="text" name="url" value="" style="width: 100%;"></td>
											<td style="width: 30px;"><input type="submit" value="<?php echo __('Add'); ?>" style="width: 100%;"></td>
										</tr>
									</table>
								</form>
							</td>
						</tr>
					</table>
				<?php endif; ?>
				<?php if (BUGSsettings::isUploadsEnabled() && $bugs_user->canAttachFiles()): ?>
					<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0>
						<tr>
							<td class="issuedetailstopheaderleft"><b><?php echo __('Attach a file to this issue'); ?></b></td>
							<td class="issuedetailstopheaderright" style="width: 15px; text-align: center;"><a href="javascript:void(0);" onclick="Element.show('attach_file');" class="image"><?php echo image_tag('icon_plus_tiny.png'); ?></a></td>
						</tr>
					</table>
					<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0>
						<tr>
							<td class="issuedetailscontentsleft" style="padding-top: 5px; padding-bottom: 5px; display: none;" id="attach_file">
								<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="viewissue.php" enctype="multipart/form-data" method="post" name="issue_add_files">
									<input type="hidden" name="issue_no" value="<?php echo $theIssue->getFormattedIssueNo(true); ?>">
									<input type="hidden" name="files" value="true">
									<input type="hidden" name="action" value="add">
									<table cellpadding=0 cellspacing=0 style="width: 100%;">
										<tr>
											<td style="width: 45px;"><b style="font-size: 10px;"><?php echo __('Descr.:'); ?></b></td>
											<td><input type="text" name="desc" value="" style="width: 100%;"></td>
										</tr>
									</table>
									<table cellpadding=0 cellspacing=0 style="width: 100%;">
										<tr>
											<td style="width: 45px;"><b><?php echo __('File:') ?></b></td>
											<td><input type="file" name="file" value="" style="width: 50px;"></td>
										</tr>
										<tr>
											<td style="text-align: right;" colspan=2><input type="submit" value="<?php echo __('Add'); ?>" style="width: 30px;"></td>
										</tr>
									</table>
								</form>
							</td>
						</tr>
					</table>
				<?php endif; ?>
				<div class="header_div">
					<?php echo __('Related issues'); ?>
				</div>
				<div id="related_parent_issues_inline">
					<?php $p_issues = 0; ?>
					<?php foreach ($theIssue->getParentIssues() as $parent_issue): ?>
						<?php if ($parent_issue->hasAccess()): ?>
							<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
								<tr>
									<td style="width: 20px;"><div style="border: 1px solid #AAA; background-color: <?php echo $parent_issue->getStatus()->getColor(); ?>; font-size: 1px; width: 13px; height: 13px;" title="<?php echo $parent_issue->getStatus()->getName(); ?>">&nbsp;</div></td>
									<td style="padding: 1px; width: auto;" valign="middle"><a href="viewissue.php?issue_no=<?php echo $parent_issue->getFormattedIssueNo(true); ?>"><?php echo $parent_issue->getFormattedIssueNo(); ?></a> - <?php echo $parent_issue->getTitle(); ?></td>
									<td style="padding: 1px; width: 20px;" valign="middle"><?php echo image_tag('action_' . (($parent_issue->getState() == BUGSissue::STATE_CLOSED) ? "ok" : "cancel") . '_small.png', '', __('All these issues must be fixed before the issue relation is solved')); ?></td>
								</tr>
							</table>
							<?php $p_issues++; ?>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
				<div id="related_child_issues_inline">
					<?php $c_issues = 0; ?>
					<?php foreach ($theIssue->getChildIssues() as $child_issue): ?>
						<?php if ($child_issue->hasAccess()): ?>
							<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
								<tr>
									<td style="width: 20px;"><div style="border: 1px solid #AAA; background-color: <?php echo $child_issue->getStatus()->getColor(); ?>; font-size: 1px; width: 13px; height: 13px;" title="<?php echo $child_issue->getStatus()->getName(); ?>">&nbsp;</div></td>
									<td style="padding: 1px; width: auto;" valign="middle"><a href="viewissue.php?issue_no=<?php echo $child_issue->getFormattedIssueNo(true); ?>"><?php echo $child_issue->getFormattedIssueNo(); ?></a> - <?php echo $child_issue->getTitle(); ?><br></td>
									<td style="padding: 1px; width: 20px;" valign="middle"><?php echo image_tag('action_' . (($child_issue->getState() == BUGSissue::STATE_CLOSED) ? "ok" : "cancel") . '_small.png', '', __('All these issues must be fixed before the issue relation is solved')); ?></td>
								</tr>
							</table>
							<?php $c_issues++; ?>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
				<?php if ($c_issues + $p_issues == 0): ?> 
					<div class="no_items"><?php echo __('There are no issues related to this'); ?></div>
				<?php endif; ?>
				<?php
			
					BUGScontext::trigger('core', 'viewissue_left_middle_top', $theIssue);
					
				?>
				<?php if (!$bugs_user->isGuest()): ?>
					<?php if ($bugs_user->showFollowUps()): ?>
						<div class="header_div">
							<?php echo __('Your starred issues'); ?>
						</div>
						<?php if (count($bugs_user->getStarredIssues()) == 0): ?>
							<div class="no_items"><?php echo __("You don't have any issues on your list"); ?></div>
						<?php else: ?>
							<div class="issuedetailscontentsleft" style="padding-top: 5px; padding-bottom: 5px;">
								<table cellpadding=0 cellspacing=0>
									<?php foreach ($bugs_user->getStarredIssues() as $anIssue): ?>
										<tr class="<?php if ($anIssue->getState() == BUGSissue::STATE_CLOSED) echo 'issue_closed'; if ($anIssue->isBlocking()) echo ' issue_blocking'; ?>">
											<td class="imgtd"><?php echo image_tag('assigned_bugs.png'); ?></td>
											<td><a href="viewissue.php?issue_no=<?php echo $anIssue->getFormattedIssueNo(true); ?>"><?php echo $anIssue->getFormattedIssueNo(); ?></a> - <?php echo $anIssue->getTitle(); ?></td>
										</tr>
									<?php endforeach; ?>
								</table>
							</div>
						<?php endif; ?>
					<?php endif; ?>
					<?php if ($bugs_user->showAssigned()): ?>
						<div class="header_div">
							<?php echo __('Open issues assigned to you'); ?>
						</div>
						<div class="issuedetailscontentsleft" style="padding-top: 5px; padding-bottom: 5px;">
							<?php if (count($bugs_user->getUserAssignedIssues()) > 0): ?>
								<table cellpadding=0 cellspacing=0>
									<?php foreach ($bugs_user->getUserAssignedIssues() as $anIssue): ?>
										<tr class="<?php if ($savedIssue->getState() == BUGSissue::STATE_CLOSED) echo 'issue_closed'; if ($savedIssue->isBlocking()) echo ' issue_blocking'; ?>">
											<td class="imgtd"><?php echo image_tag('assigned_bugs.png'); ?></td>
											<td><a href="viewissue.php?issue_no=<?php print $savedIssue->getFormattedIssueNo(true); ?>"><?php print $savedIssue->getFormattedIssueNo(); ?></a> - <?php print (strlen($savedIssue->getTitle()) > 26) ? bugs_BBDecode(rtrim(substr($savedIssue->getTitle(), 0, 24))) . "..." : bugs_BBDecode($savedIssue->getTitle()); ?></td>
										</tr>
									<?php endforeach; ?>
								</table>
							<?php else: ?>
								<div class="no_items"><?php echo __('No issues are assigned to you'); ?></div>
							<?php endif; ?>
						</div>
						<?php if (count($bugs_user->getTeams()) > 0): ?>
							<?php foreach ($bugs_user->getTeams() as $tid => $theTeam): ?>
								<div class="header_div">
									<?php echo __('Open issues assigned to %teamname%', array('%teamname%' => $theTeam->getName())); ?>
								</div>
								<?php if (count($bugs_user->getUserTeamAssignedIssues) > 0): ?>
									<table cellpadding=0 cellspacing=0>
										<?php foreach ($bugs_user->getUserTeamAssignedIssues($tid) as $anIssue): ?>
											<tr class="<?php if ($savedIssue->getState() == BUGSissue::STATE_CLOSED) echo 'issue_closed'; if ($savedIssue->isBlocking()) echo ' issue_blocking'; ?>">
												<td class="imgtd"><?php echo image_tag('assigned_bugs.png'); ?></td>
												<td><a href="viewissue.php?issue_no=<?php print $savedIssue->getFormattedIssueNo(true); ?>"><?php print $savedIssue->getFormattedIssueNo(); ?></a> - <?php print (strlen($savedIssue->getTitle()) > 26) ? bugs_BBDecode(rtrim(substr($savedIssue->getTitle(), 0, 24))) . "..." : bugs_BBDecode($savedIssue->getTitle()); ?></td>
											</tr>
										<?php endforeach; ?>
									</table>
								<?php else: ?>
									<div class="no_items"><?php echo __('No issues assigned to this team'); ?></div>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
				<?php
			
					BUGScontext::trigger('core', 'viewissue_left_bottom', $theIssue);
			
				?>
			</td><?php /* end left column */ ?>
			<td valign="top" align="left" style="padding-right: 5px;" id="issue_main">
				<?php
			
					BUGScontext::trigger('core', 'viewissue_right_top', $theIssue);
			
				?>
				<div style="vertical-align: middle; padding: 5px 0 0 5px;">
					<table style="table-layout: fixed; width: 100%; margin: 10px 0 10px 0; background-color: transparent;" cellpadding=0 cellspacing=0>
						<tr>
							<td style="width: 22px; padding: 0 5px 0 5px;">
								<?php if ($bugs_user->isGuest()): ?>
									<?php echo image_tag('star_faded.png'); ?>
								<?php else: ?>
									<?php echo image_tag('spinning_20.gif', array('id' => 'issue_favourite_indicator', 'style' => 'display: none;')); ?>
									<?php echo image_tag('star_faded.png', array('id' => 'issue_favourite_faded', 'style' => 'cursor: pointer;'.(($bugs_user->isIssueStarred($theIssue->getID())) ? 'display: none;' : ''), 'onclick' => "toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $theIssue->getID()))."', ".$theIssue->getID().");")); ?>
									<?php echo image_tag('star.png', array('id' => 'issue_favourite_normal', 'style' => 'cursor: pointer;'.((!$bugs_user->isIssueStarred($theIssue->getID())) ? 'display: none;' : ''), 'onclick' => "toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $theIssue->getID()))."', ".$theIssue->getID().");")); ?>
								<?php endif; ?>
							</td>
							<td style="font-size: 19px; width: auto; padding: 0; padding-left: 7px;">
								<b><?php echo __('Issue %issue_no%', array('%issue_no%' => link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())), $theIssue->getFormattedIssueNo(true)))); ?>&nbsp;&nbsp;-&nbsp;&nbsp;<span id="issue_title"><?php echo $theIssue->getTitle(); ?></span></b><br>
								<div style="font-size: 13px;">
									<?php echo '<b>' . __('Posted %posted_at_time% - updated %last_updated_at_time%', array('%posted_at_time%' => '</b><i>' . bugs_formatTime($theIssue->getPosted(), 12) . '</i><b>', '%last_updated_at_time%' => '</b><i>' . bugs_formatTime($theIssue->getLastUpdatedTime(), 12) . '</i>')); ?>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<?php //TODO: require BUGScontext::getIncludePath() . 'include/issue_affected_inline.inc.php'; ?>
				<div class="rounded_box invisible" id="description_field" style="margin: 5px 0 5px 0;<?php if (!$theIssue->isDescriptionVisible()): ?> display: none;<?php endif; ?>">
					<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
					<div class="xboxcontent viewissue_description">
						<div class="viewissue_description_header"><?php echo __('Description'); ?>:</div>
						<?php if ($theIssue->getDescription() == ''): ?>
							<div class="faded_medium"><?php echo __('Nothing entered.'); ?></div>
						<?php else: ?>
							<?php echo bugs_BBDecode($theIssue->getDescription()); ?>
						<?php endif; ?>
					</div>
					<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
				</div>
				<div class="rounded_box invisible" id="reproduction_steps_field" style="margin: 5px 0 5px 0;<?php if (!$theIssue->isReproductionStepsVisible()): ?> display: none;<?php endif; ?>">
					<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
					<div class="xboxcontent viewissue_reproduction_steps">
						<div class="viewissue_reproduction_steps_header"><?php echo __('Reproduction steps'); ?>:</div>
						<?php if ($theIssue->getReproductionSteps() == ''): ?>
							<div class="faded_medium"><?php echo __('Nothing entered.'); ?></div>
						<?php else: ?>
							<?php echo bugs_BBDecode($theIssue->getReproductionSteps()); ?>
						<?php endif; ?>
					</div>
					<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
				</div>
				<?php if ($theIssue->getProject()->isTasksEnabled()): ?>
					<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0 id="taskslist">
						<tr>
							<td style="width: 100px; font-size: 12px; font-weight: bold; border-bottom: 1px solid #DDD; padding: 4px;"><b><?php echo __('Tasks'); ?></b></td>
							<td style="width: auto; font-size: 10px; border-bottom: 1px solid #DDD; padding: 4px;">&nbsp;(<?php echo __('click to view details'); ?>)</td>
							<td style="width: 150px; font-size: 10px; font-weight: bold; border-bottom: 1px solid #DDD; padding: 4px;"><b><?php echo __('Assigned to'); ?></b></td>
							<td style="width: 80px; font-size: 10px; font-weight: bold; border-bottom: 1px solid #DDD; padding: 4px;"><b><?php echo __('Last updated'); ?></b></td>
							<td style="width: 190px; font-size: 10px; font-weight: bold; border-bottom: 1px solid #DDD; padding: 4px;"><b><?php echo __('Status'); ?></b></td>
							<td style="width: 60px; font-size: 10px; font-weight: bold; border-bottom: 1px solid #DDD; padding: 4px;"><b><?php echo __('Completed'); ?></b></td>
						</tr>
					</table>
					<div id="new_task" style="display: none;">
						<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="viewissue.php" enctype="multipart/form-data" method="post" id="issue_add_task" onsubmit="return false">
							<input type="hidden" name="issue_no" id="issue_no" value="<?php echo $theIssue->getFormattedIssueNo(true); ?>">
							<input type="hidden" name="issue_new_task" id="issue_new_task" value="true">
							<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0 id="taskslist">
								<tr>
									<td class="issuedetailscontentsleft" style="width: auto;"><input type="text" name="issue_new_task_title" id="issue_new_task_title" style="width: 100%;" value="<?php echo __('Enter the task title here'); ?>"></td>
									<td class="issuedetailscontentscenter" style="width: 150px; font-size: 10px;"><?php echo __('Save the task to assign it'); ?></td>
									<td class="issuedetailscontentscenter" style="width: 80px; font-size: 10px; text-align: center;"><?php echo bugs_formatTime($_SERVER["REQUEST_TIME"], 4); ?></td>
									<td class="issuedetailscontentscenter" style="width: 190px; font-size: 10px;"><?php echo __('Save the task to set a status'); ?></td>
									<td class="issuedetailscontentsright" style="width: 60px; text-align: center;"><?php echo image_tag('action_cancel_small.png'); ?></td>
								</tr>
								<tr>
									<td colspan=3 class="issuedetailscontentsleft" style="border-bottom: 2px solid #CCC;"><?php echo bugs_newTextArea('issue_new_task_description', '100px', '100%', __('Enter the task details here')); ?></td>
									<td colspan=2 class="issuedetailscontentsright" style="border-bottom: 2px solid #CCC; text-align: center;">
										<button style="width: 100px;" onclick="addTask();"><?php echo __('Add this task'); ?></button><br>
										<div style="padding: 5px;"><a href="javascript:void(0);" onclick="Element.hide('new_task');"><?php echo __('Cancel'); ?></a></div>
									</td>
								</tr>
							</table>
						</form>
					</div>
					<div id="issue_tasks">
						<?php if (count($theIssue->getTasks()) == 0): ?>
							<div class="faded_medium"><?php echo __('No tasks are specified for this issue'); ?></div>
						<?php else: ?>
							<?php foreach ($theIssue->getTasks() as $theTask): ?>
								<?php require BUGScontext::getIncludePath() . 'include/issue_taskbox.inc.php'; ?>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php 
				
					//BUGScontext::trigger('core', 'viewissue_right_middle', $theIssue);
					
				?>
				<div style="margin-top: 10px;">
					<?php
					
					/*
					$canViewComments = BUGSComment::getCommentAccess($theIssue->getID());
					$canAddComments = BUGSComment::getCommentAccess($theIssue->getID(), 'add');
					$canEditComments = BUGSComment::getCommentAccess($theIssue->getID(), 'edit');
					
					if ($canViewComments || $canAddComments || $canEditComments)
					{
						if ($canAddComments)
						{
							echo BUGSComment::getCommentForm($theIssue->getID());
						}
						
						?>
						<div class="commentheadertop" style="clear: both;"><b><?php echo __('Comments and discussion'); ?></b><?php if (BUGSuser::isThisGuest() == false) { ?>&nbsp;&nbsp;(<a href="javascript:void(0);" onclick="Effect.Appear('filter_comments', { duration: 0.5 });" style="font-size: 10px;"><?php echo __('Filter comments'); ?></a><font style="font-size: 9px;">:<?php
					
							if ($doFilterSystemComments || $doFilterUserComments)
							{
								if ($doFilterSystemComments && !$doFilterUserComments) echo __('Filtering system comments');
								if ($doFilterSystemComments && $doFilterUserComments) echo __('Filtering system comments and user comments');
								if (!$doFilterSystemComments && $doFilterUserComments) echo __('Filtering user comments');
							}
							else
							{
								echo __('No filters applied');
							}
						
						?></font>)<?php } ?>
						<div id="filter_comments_core_1_<?php echo $theIssue->getID(); ?>" style="padding: 5px; left: 390px; position: absolute; display: none; background-color: #FFF; border: 1px solid #DDD; width: 200px;"><div style="padding-bottom: 3px;"><b><?php echo __('Apply filter to comments:'); ?></b></div>
						<?php if ($doFilterSystemComments == false) { ?><a href="viewissue.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&amp;hide_comments=system"><?php echo __('Hide system comments'); ?></a><br><?php } ?>
						<?php if ($doFilterSystemComments) { ?><a href="viewissue.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&amp;show_comments=system"><?php echo __('Show system comments'); ?></a><br><?php } ?>
						<?php if ($doFilterUserComments == false) { ?><a href="viewissue.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&amp;hide_comments=user"><?php echo __('Hide user comments'); ?></a><br><?php } ?>
						<?php if ($doFilterUserComments) { ?><a href="viewissue.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&amp;show_comments=user"><?php echo __('Show user comments'); ?></a><br><?php } ?>
						<div style="text-align: right;"><a href="javascript:void(0);" onclick="Effect.Fade('filter_comments', { duration: 0.5 });" style="font-size: 10px;"><?php echo __('Never mind'); ?></a></div>
						</div>
						</div>
						<?php
						echo '<span id="comments_span_core_1_' . $theIssue->getID()  . '">';
						$target_id = $theIssue->getID();
						$target_type = 1;
						$module = 'core';
						require BUGScontext::getIncludePath() . 'include/comments.inc.php';
						echo '</span>';
					}*/
			
					?>
				</div>
			</td>
		</tr>
	</table>
<?php else: ?>
	<div class="rounded_box red_borderless" id="viewissue_nonexisting">
		<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
		<div class="xboxcontent" style="vertical-align: middle; padding: 5px; color: #222; font-weight: bold; font-size: 13px;">
			<div class="viewissue_info_header"><?php echo __("You have specified an issue that can't be shown"); ?></div>
			<div class="viewissue_info_content"><?php echo __("This could be because you the issue doesn't exist, has been deleted or you don't have permission to see it"); ?></div>
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
<?php endif; ?>