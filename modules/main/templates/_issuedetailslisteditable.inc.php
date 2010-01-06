<div id="issuetype_indicator_fullpage" style="background-color: transparent; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; text-align: center; display: none;">
	<div style="position: absolute; top: 45%; left: 40%; z-index: 100001; color: #FFF; font-size: 15px; font-weight: bold;">
		<?php echo image_tag('spinning_32.gif'); ?><br>
		<?php echo __('Please wait while updating issue type'); ?>...
	</div>
	<div style="background-color: #000; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; z-index: 100000;" class="semi_transparent"> </div>
</div>
<div class="rounded_box iceblue_borderless" id="viewissue_left_box_top">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="padding: 5px;">
		<div id="viewissue_left_box_issuetype">
			<div id="issuetype_header" class="<?php if ($issue->isIssuetypeChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isIssuetypeMerged()): ?> issue_detail_unmerged<?php endif; ?>">
				<a href="javascript:void(0);" onclick="revertField('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'issuetype')); ?>', 'issuetype');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
				<?php echo image_tag('spinning_20.gif', array('style' => 'display: none; float: left; margin: 5px 5px 0 0;', 'id' => 'issuetype_undo_spinning')); ?>
				<a href="javascript:void(0);" onclick="$('issuetype_change').toggle();" title="<?php echo __('Click to change issue type'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<table cellpadding=0 cellspacing=0 id="issuetype_table"<?php if (!$issue->getIssueType() instanceof BUGSdatatype): ?> style="display: none;"<?php endif; ?>>
					<tr>
						<td style="width: 24px; padding: 2px 0 0 0;"><?php echo image_tag($issue->getIssueType()->getIcon() . '_small.png', array('id' => 'issuetype_image')); ?></td>
						<td style="padding: 0 0 0 5px;" id="issuetype_content"><?php echo $issue->getIssueType()->getName(); ?></td>
					</tr>
				</table>
				<div class="faded_medium" id="no_issuetype"<?php if ($issue->getIssueType() instanceof BUGSdatatype): ?> style="display: none;"<?php endif; ?>><?php echo __('Unknown issue type'); ?></div>
			</div>
		</div>
		<div class="rounded_box white" id="issuetype_change" style="display: none; clear: both; width: 324px; margin: 5px 0 5px 0;">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="padding: 5px;">
				<div class="dropdown_header"><?php echo __('Set issue type'); ?></div>
				<div class="dropdown_content">
					<?php echo __('Select a new issue type'); ?>:<br>
					<table cellpadding="0" cellspacing="0">
						<?php foreach ($issuetypes as $issuetype): ?>
							<tr>
								<td style="width: 16px;"><?php echo image_tag($issuetype->getIcon() . '_tiny.png'); ?></td>
								<td style="padding-left: 5px;"><a href="javascript:void(0);" onclick="setField('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'issuetype', 'issuetype_id' => $issuetype->getID())); ?>', 'issuetype');"><?php echo $issuetype->getName(); ?></a></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<div id="issuetype_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
				</div>
				<div id="issuetype_change_error" class="error_message" style="display: none;"></div>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
		<div id="viewissue_left_box_status">
			<div id="status_header" class="<?php if ($issue->isStatusChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isStatusMerged()): ?> issue_detail_unmerged<?php endif; ?>">
				<a href="javascript:void(0);" onclick="revertField('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'status')); ?>', 'status');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
				<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'status_undo_spinning')); ?>
				<a href="javascript:void(0);" onclick="$('status_change').toggle();" title="<?php echo __('Click to change status'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<table style="table-layout: auto; width: 250px;<?php if (!$issue->getStatus() instanceof BUGSdatatype): ?> display: none;<?php endif; ?>" cellpadding=0 cellspacing=0 id="status_table">
					<tr>
						<td style="width: 24px;"><div style="border: 1px solid #AAA; background-color: <?php echo ($issue->getStatus() instanceof BUGSdatatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 20px; height: 15px; margin-right: 2px;" id="status_color">&nbsp;</div></td>
						<td style="padding-left: 5px;" id="status_content" class="<?php if ($issue->isStatusChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isStatusMerged()): ?> issue_detail_unmerged<?php endif; ?>"><?php if ($issue->getStatus() instanceof BUGSdatatype) echo $issue->getStatus()->getName(); ?></td>
					</tr>
				</table>
				<span class="faded_medium" id="no_status"<?php if ($issue->getStatus() instanceof BUGSdatatype): ?> style="display: none;"<?php endif; ?>><?php echo __('Status not determined'); ?></span>
			</div>
		</div>
		<div class="rounded_box white" id="status_change" style="display: none; clear: both; width: 324px; margin: 5px 0 5px 0;">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="padding: 5px;">
				<div class="dropdown_header"><?php echo __('Set status'); ?></div>
				<div class="dropdown_content">
					<a href="javascript:void(0);" onclick="setField('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'status', 'status_id' => 0)); ?>', 'status');"><?php echo __('Clear the status'); ?></a><br>
				</div>
				<div class="dropdown_content">
					<?php echo __('%clear_the_status% or click to select a new status', array('%clear_the_status%' => '')); ?>:<br>
					<table cellpadding="0" cellspacing="0">
						<?php foreach ($statuses as $status): ?>
							<?php if (!$status->canUserSet($bugs_user)) continue; ?>
							<tr>
								<td style="width: 16px;"><div style="border: 1px solid #AAA; background-color: <?php echo $status->getColor(); ?>; font-size: 1px; width: 16px; height: 15px; margin-right: 2px;">&nbsp;</div></td>
								<td style="padding-left: 5px;"><a href="javascript:void(0);" onclick="setField('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'status', 'status_id' => $status->getID())); ?>', 'status');"><?php echo $status->getName(); ?></a></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<div id="status_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
				</div>
				<div id="status_change_error" class="error_message" style="display: none;"></div>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
		<dl class="viewissue_list" id="assigned_to_field">
			<dt id="assigned_to_header" class="<?php if ($issue->isAssignedToChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isAssignedToMerged()): ?> issue_detail_unmerged<?php endif; ?>"><?php echo __('Assigned to'); ?></dt>
			<dd id="assigned_to_content" class="<?php if ($issue->isAssignedToChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isAssignedToMerged()): ?> issue_detail_unmerged<?php endif; ?>">
				<a href="javascript:void(0);" onclick="revertField('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to')); ?>', 'assigned_to');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
				<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'assigned_to_undo_spinning')); ?>
				<a href="javascript:void(0);" onclick="$('assigned_to_change').toggle();" title="<?php echo __('Click to change assignee'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<table style="width: 170px; display: <?php if ($issue->isAssigned()): ?>inline<?php else: ?>none<?php endif; ?>;" cellpadding=0 cellspacing=0 id="assigned_to_name">
					<?php if ($issue->getAssigneeType() == BUGSidentifiableclass::TYPE_USER): ?>
						<?php echo include_component('main/userdropdown', array('user' => $issue->getAssignee())); ?>
					<?php elseif ($issue->getAssigneeType() == BUGSidentifiableclass::TYPE_TEAM): ?>
						<?php echo include_component('main/teamdropdown', array('team' => $issue->getAssignee())); ?>
					<?php endif; ?>
				</table>
				<span class="faded_medium" id="no_assigned_to"<?php if ($issue->isAssigned()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not assigned to anyone'); ?></span>
			</dd>
		</dl>
		<?php include_component('identifiableselector', array(	'html_id' 			=> 'assigned_to_change', 
																'header' 			=> __('Assign this issue'),
																'callback'		 	=> "setField('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => '%identifiable_type%', 'value' => '%identifiable_value%')) . "', 'assigned_to');",
																'clear_link_text'	=> __('Clear current assignee'),
																'base_id'			=> 'assigned_to',
																'include_teams'		=> true)); ?>
		<dl class="viewissue_list" id="percent_complete_field"<?php if (!$issue->isPercentCompletedVisible()): ?> style="display: none;"<?php endif; ?>>
			<dt id="percent_header" class="<?php if ($issue->isPercentCompletedChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isPercentCompletedMerged()): ?> issue_detail_unmerged<?php endif; ?>"><?php echo __('Progress'); ?></dt>
			<dd id="percent_content" class="<?php if ($issue->isPercentCompletedChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isPercentCompletedMerged()): ?> issue_detail_unmerged<?php endif; ?>">
				<div style="width: 210px;">
					<?php if (!$issue->canEditPercentCompleted()): ?>
						<a href="javascript:void(0);" onclick="updatePercent('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'percent')); ?>', 'percent');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
						<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: right; margin-left: 5px;', 'id' => 'percent_spinning')); ?>
						<table style="table-layout: fixed; width: 165px;" cellpadding=0 cellspacing=0>
							<tr id="percentage_tds">
								<?php for ($cc = 0; $cc <= 100; $cc++): ?>
									<td class="<?php if ($issue->getPercentCompleted() <= $cc): ?>percent_unfilled<?php else: ?>percent_filled<?php endif; ?>" style="font-size: 1px; width: 1%; height: 14px;">
										<a href="javascript:void(0);" onclick="updatePercent('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'percent', 'percent' => $cc)); ?>', 'set');" title="<?php echo __('Set to %pct% percent', array('%pct%' => $cc)); ?>">&nbsp;</a>
									</td>
								<?php endfor; ?>
							</tr>
						</table>
					<?php else: ?>
						<?php include_template('main/percentbar', array('percent' => $issue->getPercentCompleted(), 'height' => 14)); ?>
					<?php endif; ?>
				</div>
			</dd>
		</dl>
		<?php if ($issue->getProject()->isVotesEnabled()): ?>
			<?php //TODO: Add a vote counter, and a "plus" button? ?>
			<?php if ($bugs_user->canVoteOnIssuesForProduct($issue->getProject()->getID()) && $bugs_user->canVoteForIssue($issue->getID())): /* ?>
				<div style="border-bottom: 1px solid #DDD; padding: 3px; font-size: 12px; margin-top: 5px;">
					<b>VOTE!</b>
				</div>
			<?php */ endif; ?>
		<?php endif; ?>
		
		<div style="clear: both;"> </div>
	</div>
</div>
<div class="rounded_box mediumgrey_borderless" style="margin: 0;" id="viewissue_left_box_bottom">
	<div class="xboxcontent" style="vertical-align: middle; padding: 0 10px 0 5px;">
		<dl class="viewissue_list" id="edition_field"<?php if (!($issue->getProject()->isEditionsEnabled() && $issue->isEditionsVisible())): ?> style="display: none;"<?php endif; ?>>
			<dt><?php echo __('Edition(s)'); ?></dt>
			<dd>
				<?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?>
				<?php if (count($issue->getEditions()) > 0): ?>
					<ul>
						<?php foreach ($issue->getEditions() as $edition): ?>
							<li id="issue_affected_edition_<?php $edition->getID(); ?>_inline"><?php $edition->getName(); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</dd>
		</dl>
		<dl class="viewissue_list" id="posted_by_field">
			<dt id="posted_by_header" class="<?php if ($issue->isPostedByChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isPostedByMerged()): ?> issue_detail_unmerged<?php endif; ?>"><?php echo __('Posted by'); ?></dt>
			<dd id="posted_by_content" class="<?php if ($issue->isPostedByChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isPostedByMerged()): ?> issue_detail_unmerged<?php endif; ?>">
				<a href="javascript:void(0);" onclick="revertField('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'posted_by')); ?>', 'posted_by');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
				<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'posted_by_undo_spinning')); ?>
				<a href="javascript:void(0);" onclick="$('posted_by_change').toggle();" title="<?php echo __('Click to change owner'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<table style="width: 170px; display: inline;" cellpadding=0 cellspacing=0 id="posted_by_name">
					<?php echo include_component('main/userdropdown', array('user' => $issue->getPostedBy())); ?>
				</table>
				<span id="no_posted_by" style="display: none;"> </span>
			</dd>
		</dl>
		<?php include_component('identifiableselector', array(	'html_id' 			=> 'posted_by_change', 
																'header' 			=> __('Change poster'),
																'allow_clear'		=> false,
																'clear_link_text'	=> '',
																'callback'		 	=> "setField('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'posted_by', 'value' => '%identifiable_value%')) . "', 'posted_by');",
																'base_id'			=> 'posted_by')); ?>
		<dl class="viewissue_list" id="owned_by_field">
			<dt id="owned_by_header" class="<?php if ($issue->isOwnedByChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isOwnedByMerged()): ?> issue_detail_unmerged<?php endif; ?>"><?php echo __('Owned by'); ?></dt>
			<dd id="owned_by_content" class="<?php if ($issue->isOwnedByChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isOwnedByMerged()): ?> issue_detail_unmerged<?php endif; ?>">
				<a href="javascript:void(0);" onclick="revertField('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by')); ?>', 'owned_by');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
				<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'owned_by_undo_spinning')); ?>
				<a href="javascript:void(0);" onclick="$('owned_by_change').toggle();" title="<?php echo __('Click to change owner'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<table style="width: 170px; display: <?php if ($issue->isOwned()): ?>inline<?php else: ?>none<?php endif; ?>;" cellpadding=0 cellspacing=0 id="owned_by_name">
					<?php if ($issue->getOwnerType() == BUGSidentifiableclass::TYPE_USER): ?>
						<?php echo include_component('main/userdropdown', array('user' => $issue->getOwner())); ?>
					<?php elseif ($issue->getOwnerType() == BUGSidentifiableclass::TYPE_TEAM): ?>
						<?php echo include_component('main/teamdropdown', array('team' => $issue->getOwner())); ?>
					<?php endif; ?>
				</table>
				<span class="faded_medium" id="no_owned_by"<?php if ($issue->isOwned()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not owned by anyone'); ?></span>
			</dd>
		</dl>
		<?php include_component('identifiableselector', array(	'html_id' 			=> 'owned_by_change', 
																'header' 			=> __('Change issue owner'),
																'callback'		 	=> "setField('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => '%identifiable_type%', 'value' => '%identifiable_value%')) . "', 'owned_by');",
																'clear_link_text'	=> __('Clear current owner'),
																'base_id'			=> 'owned_by',
																'include_teams'		=> true)); ?>
		<dl class="viewissue_list" id="estimated_time_field"<?php if (!$issue->isEstimatedTimeVisible()): ?> style="display: none;"<?php endif; ?>>
			<dt id="estimated_time_header" class="<?php if ($issue->isEstimatedTimeChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isEstimatedTimeMerged()): ?> issue_detail_unmerged<?php endif; ?>"><?php echo __('Estimated time'); ?></dt>
			<dd id="estimated_time_content" class="<?php if ($issue->isEstimatedTimeChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isEstimatedTimeMerged()): ?> issue_detail_unmerged<?php endif; ?>">
				<a href="javascript:void(0);" onclick="revertField('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'estimated_time')); ?>', 'estimated_time');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
				<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'status_undo_spinning')); ?>
				<a href="javascript:void(0);" onclick="$('estimated_time_change').toggle();" title="<?php echo __('Click to estimate this issue'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<span id="estimated_time_name"<?php if (!$issue->hasEstimatedTime()): ?> style="display: none;"<?php endif; ?>>
					<?php echo $issue->getFormattedTime($issue->getEstimatedTime()); ?>
				</span>
				<span class="faded_medium" id="no_estimated_time"<?php if ($issue->hasEstimatedTime()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not estimated'); ?></span>
			</dd>
		</dl>
		<div class="rounded_box white" id="estimated_time_change" style="clear: both; display: none; width: 324px; margin: 5px 0 5px 0;">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="padding: 5px;">
				<form id="estimated_time_form" method="post" accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="" onsubmit="setTimeField('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'estimated_time')); ?>', 'estimated_time');return false;">
					<div class="dropdown_header"><?php echo __('Estimate this issue'); ?></div>
					<div class="dropdown_content">
						<a href="javascript:void(0);" onclick="setField('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'estimated_time', 'value' => 0)); ?>', 'estimated_time');"><?php echo __('Clear current estimate'); ?></a><br>
					</div>
					<div class="dropdown_content">
						<label for="estimated_time_input"><?php echo trim(__('%clear_current_estimate% type a new estimate %or_select_below%', array('%clear_current_estimate%' => '', '%or_select_below%' => ''))); ?>:</label><br>
						<?php $estimated_time_title = __('Enter your estimate here'); ?>
						<input type="text" name="estimated_time" id="estimated_time_input" value="<?php echo $estimated_time_title; ?>" style="width: 240px; padding: 1px 1px 1px;" onblur="if (this.getValue() == '') { this.value = '<?php echo $estimated_time_title; ?>'; this.addClassName('faded_medium'); }" onfocus="if (this.getValue() == '<?php echo $estimated_time_title; ?>') { this.clear(); } this.removeClassName('faded_medium');" class="faded_medium">
						<input type="submit" style="width: 60px;" value="<?php echo __('Estimate'); ?>"></input>
						<div class="faded_medium" style="padding: 5px 0 5px 0;"><?php echo __('Enter an estimate in plain text, like "1 week, 2 hours", "3 months and 1 day", or similar'); ?>.</div>
					</div>
					<div class="dropdown_content">
						<label for="estimated_time_months"><?php echo __('or enter an estimate below'); ?>:</label><br>
						<input type="text" style="width: 20px;" value="<?php echo $issue->getEstimatedMonths(); ?>" name="estimated_time_months" id="estimated_time_months"><b><?php echo __('%number_of% months', array('%number_of%' => '')); ?></b><br>
						<input type="text" style="width: 20px;" value="<?php echo $issue->getEstimatedWeeks(); ?>" name="estimated_time_weeks" id="estimated_time_weeks"><b><?php echo __('%number_of% weeks', array('%number_of%' => '')); ?></b><br>
						<input type="text" style="width: 20px;" value="<?php echo $issue->getEstimatedDays(); ?>" name="estimated_time_days" id="estimated_time_days"><b><?php echo __('%number_of% days', array('%number_of%' => '')); ?></b><br>
						<input type="text" style="width: 20px;" value="<?php echo $issue->getEstimatedHours(); ?>" name="estimated_time_hours" id="estimated_time_hours"><b><?php echo __('%number_of% hours', array('%number_of%' => '')); ?></b><br>
						<input type="submit" style="width: 60px; float: right;" value="<?php echo __('Estimate'); ?>"></input>
						<input type="text" style="width: 20px;" value="<?php echo $issue->getEstimatedPoints(); ?>" name="estimated_time_points" id="estimated_time_points"><b><?php echo __('%number_of% points', array('%number_of%' => '')); ?></b><br>
					</div>
				</form>
				<div id="estimated_time_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
				<div id="estimated_time_change_error" class="error_message" style="display: none;"></div>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
		<dl class="viewissue_list" id="spent_time_field"<?php if (!$issue->isSpentTimeVisible()): ?> style="display: none;"<?php endif; ?>>
			<dt id="spent_time_header" class="<?php if ($issue->isSpentTimeChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isSpentTimeMerged()): ?> issue_detail_unmerged<?php endif; ?>"><?php echo __('Time spent'); ?></dt>
			<dd id="spent_time_content" class="<?php if ($issue->isSpentTimeChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isSpentTimeMerged()): ?> issue_detail_unmerged<?php endif; ?>">
				<a href="javascript:void(0);" onclick="revertField('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'spent_time')); ?>', 'spent_time');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
				<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'status_undo_spinning')); ?>
				<a href="javascript:void(0);" onclick="$('spent_time_change').toggle();" title="<?php echo __('Click to estimate this issue'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<span id="spent_time_name"<?php if (!$issue->hasSpentTime()): ?> style="display: none;"<?php endif; ?>>
					<?php echo $issue->getFormattedTime($issue->getSpentTime()); ?>
				</span>
				<span class="faded_medium" id="no_spent_time"<?php if ($issue->hasSpentTime()): ?> style="display: none;"<?php endif; ?>><?php echo __('No time spent'); ?></span>
			</dd>
		</dl>
		<div class="rounded_box white" id="spent_time_change" style="clear: both; display: none; width: 324px; margin: 5px 0 5px 0;">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="padding: 5px;">
				<form id="spent_time_form" method="post" accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="" onsubmit="setTimeField('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'spent_time')); ?>', 'spent_time');return false;">
					<div class="dropdown_header"><?php echo __('Set time spent on this issue'); ?></div>
					<div class="dropdown_content">
						<a href="javascript:void(0);" onclick="setField('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'spent_time', 'value' => 0)); ?>', 'spent_time');"><?php echo __('Clear time spent on this issue'); ?></a><br>
					</div>
					<div class="dropdown_content">
						<label for="spent_time_input"><?php echo trim(__("%clear_time_spent% enter how much time you've spent %or_select_below%", array('%clear_time_spent%' => '', '%or_select_below%' => ''))); ?>:</label><br>
						<?php $spent_time_title = __('Enter time spent here'); ?>
						<input type="text" name="spent_time" id="spent_time_input" value="<?php echo $spent_time_title; ?>" style="width: 220px; padding: 1px 1px 1px;" onblur="if (this.getValue() == '') { this.value = '<?php echo $spent_time_title; ?>'; this.addClassName('faded_medium'); }" onfocus="if (this.getValue() == '<?php echo $spent_time_title; ?>') { this.clear(); } this.removeClassName('faded_medium');" class="faded_medium">
						<input type="submit" style="width: 80px;" value="<?php echo __('Spend time'); ?>"></input><br>
						<input type="checkbox" checked="checked" name="spent_time_added_text" value="true" id="spent_time_added_text"><label for="spent_time_added_text"><?php echo __('Add entered time to total time spent') ?></label>
						<div class="faded_medium" style="padding: 5px 0 5px 0;"><?php echo __('Enter time spent as plain text, like "1 day, 2 hours", "12 hours / 2 points", or similar'); ?>.</div>
					</div>
					<div class="dropdown_content">
						<label for="spent_time_months"><?php echo __('or enter time spent below'); ?>:</label><br>
						<input type="text" style="width: 20px;" value="<?php echo $issue->getSpentMonths(); ?>" name="spent_time_months" id="spent_time_months"><b><?php echo __('%number_of% months', array('%number_of%' => '')); ?></b><br>
						<input type="text" style="width: 20px;" value="<?php echo $issue->getSpentWeeks(); ?>" name="spent_time_weeks" id="spent_time_weeks"><b><?php echo __('%number_of% weeks', array('%number_of%' => '')); ?></b><br>
						<input type="text" style="width: 20px;" value="<?php echo $issue->getSpentDays(); ?>" name="spent_time_days" id="spent_time_days"><b><?php echo __('%number_of% days', array('%number_of%' => '')); ?></b><br>
						<input type="text" style="width: 20px;" value="<?php echo $issue->getSpentHours(); ?>" name="spent_time_hours" id="spent_time_hours"><b><?php echo __('%number_of% hours', array('%number_of%' => '')); ?></b><br>
						<input type="submit" style="width: 80px; float: right;" value="<?php echo __('Spend time'); ?>"></input>
						<input type="text" style="width: 20px;" value="<?php echo $issue->getSpentPoints(); ?>" name="spent_time_points" id="spent_time_points"><b><?php echo __('%number_of% points', array('%number_of%' => '')); ?></b><br>
						<input type="checkbox" name="spent_time_added_input" value="true" id="spent_time_added_input"><label for="spent_time_added_text"><?php echo __('Add entered time to total time spent') ?></label>
					</div>
				</form>
				<div id="spent_time_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
				<div id="spent_time_change_error" class="error_message" style="display: none;"></div>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
		<?php foreach ($fields_list as $field => $info): ?>
			<dl class="viewissue_list" id="<?php echo $field; ?>_field"<?php if (!$info['visible']): ?> style="display: none;"<?php endif; ?>>
				<dt id="<?php echo $field; ?>_header" class="<?php if ($info['changed']): ?>issue_detail_changed<?php endif; ?><?php if (!$info['merged']): ?> issue_detail_unmerged<?php endif; ?>">
					<?php echo $info['title']; ?>
				</dt>
				<dd id="<?php echo $field; ?>_content" class="<?php if ($info['changed']): ?>issue_detail_changed<?php endif; ?><?php if (!$info['merged']): ?> issue_detail_unmerged<?php endif; ?>">
					<a href="javascript:void(0);" onclick="revertField('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>', '<?php echo $field; ?>');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
					<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => $field . '_undo_spinning')); ?>
					<a href="javascript:void(0);" onclick="$('<?php echo $field; ?>_change').toggle();" title="<?php echo $info['change_tip']; ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
					<?php if ($info['icon']): ?>
						<?php echo image_tag($info['icon_name'], array('style' => 'float: left; margin-right: 5px;')); ?>
					<?php endif; ?>
					<span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>><?php echo $info['name']; ?></span>
					<span class="faded_medium" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __('Not determined'); ?></span>
				</dd>
			</dl>
			<div style="clear: both;"> </div>
			<div class="rounded_box white" id="<?php echo $field; ?>_change" style="display: none; clear: both; width: 322px; margin: 5px 0 5px 0;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 5px;">
					<div class="dropdown_header"><?php echo $info['change_header']; ?></div>
					<div class="dropdown_content">
						<a href="javascript:void(0);" onclick="setField('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_id' => 0)); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a><br>
					</div>
					<div class="dropdown_content">
						<?php echo $info['select']; ?>:<br>
						<ul class="choices">
							<?php foreach ($info['choices'] as $choice): ?>
								<?php if ($choice instanceof BUGSdatatypebase && !$choice->canUserSet($bugs_user)) continue; ?>
								<li>
									<?php echo image_tag('icon_' . $field . '.png', array('style' => 'float: left; margin-right: 5px;')); ?><a href="javascript:void(0);" onclick="setField('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_id' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo $choice->getName(); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
						<div id="<?php echo $field; ?>_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
					</div>
					<div id="<?php echo $field; ?>_change_error" class="error_message" style="display: none;"></div>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
		<?php endforeach; ?>
		<?php foreach ($customfields_list as $field => $info): ?>
			<dl class="viewissue_list" id="<?php echo $field; ?>_field"<?php if (!$info['visible']): ?> style="display: none;"<?php endif; ?>>
				<dt id="<?php echo $field; ?>_header" class="<?php if ($info['changed']): ?>issue_detail_changed<?php endif; ?><?php if (!$info['merged']): ?> issue_detail_unmerged<?php endif; ?>">
					<?php echo $info['title']; ?>
				</dt>
				<dd id="<?php echo $field; ?>_content" class="<?php if ($info['changed']): ?>issue_detail_changed<?php endif; ?><?php if (!$info['merged']): ?> issue_detail_unmerged<?php endif; ?>">
					<a href="javascript:void(0);" onclick="revertField('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>', '<?php echo $field; ?>');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
					<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => $field . '_undo_spinning')); ?>
					<a href="javascript:void(0);" onclick="$('<?php echo $field; ?>_change').toggle();" title="<?php echo $info['change_tip']; ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
					<?php echo image_tag('icon_customdatatype.png', array('style' => 'float: left; margin-right: 5px;')); ?>
					<span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>><?php echo $info['name']; ?></span>
					<span class="faded_medium" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __('Not determined'); ?></span>
				</dd>
			</dl>
			<div style="clear: both;"> </div>
			<div class="rounded_box white" id="<?php echo $field; ?>_change" style="display: none; clear: both; width: 322px; margin: 5px 0 5px 0;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 5px;">
					<div class="dropdown_header"><?php echo $info['change_header']; ?></div>
					<div class="dropdown_content">
						<a href="javascript:void(0);" onclick="setField('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_id' => 0)); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a><br>
					</div>
					<div class="dropdown_content">
						<?php echo $info['select']; ?>:<br>
						<ul class="choices">
							<?php foreach ($info['choices'] as $choice): ?>
								<?php if (!$choice->canUserSet($bugs_user)) continue; ?>
								<li>
									<?php echo image_tag('icon_customdatatype.png', array('style' => 'float: left; margin-right: 5px;')); ?><a href="javascript:void(0);" onclick="setField('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getValue())); ?>', '<?php echo $field; ?>');"><?php echo $choice->getName(); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
						<div id="<?php echo $field; ?>_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
					</div>
					<div id="<?php echo $field; ?>_change_error" class="error_message" style="display: none;"></div>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
		<?php endforeach; ?>
		<div style="clear: both; margin-bottom: 5px;"> </div>
	</div>
</div>
<div class="rounded_box green_borderless" id="more_actions" style="display: none;">
	<div class="xboxcontent">
		<?php if (!$issue->isBeingWorkedOn() || ($issue->isBeingWorkedOn() && $issue->getUserWorkingOnIssue()->getID() != $bugs_user->getID())): ?>
		<ul>
			<li><?php echo link_tag(make_url('issue_startworking', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())), image_tag('action_start_working_small.png', array('style' => 'float: left; margin-right: 5px;')) . __('Start working on this issue')); ?></li>
		</ul>
		<?php elseif ($issue->isBeingWorkedOn() && $issue->getUserWorkingOnIssue()->getID() != $bugs_user->getID()): ?>
		<ul>
			<li><?php echo link_tag(make_url('issue_stopworking', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())), image_tag('action_start_working_small.png', array('style' => 'float: left; margin-right: 5px;')) . __('Take over this issue')); ?></li>
		</ul>
		<?php elseif ($issue->isBeingWorkedOn() && $issue->getUserWorkingOnIssue()->getID() == $bugs_user->getID()): ?>
			<div class="box_header"><?php echo __('You are working on this issue'); ?></div>
			<ul>
				<li><?php echo link_tag(make_url('issue_stopworking', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())), image_tag('action_stop_working_small.png', array('style' => 'float: left; margin-right: 5px;')) . __("I'm done working on it, add time spent")); ?></li>
				<li><?php echo link_tag(make_url('issue_stopworking', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'did' => 'nothing')), image_tag('action_stop_working_small.png', array('style' => 'float: left; margin-right: 5px;')) . __("I'm done working on it, don't add time spent")); ?></li>
			</ul>
		<?php endif; ?>
		<ul>
			<?php if ($issue->isOpen()): ?>
				<li><a href="javascript:void(0);" onclick="$('close_issue_fullpage').show();"><?php echo image_tag('action_close.png', array('style' => 'float: left; margin-right: 5px;')); ?><?php echo __('Close this issue'); ?></a></li>
			<?php else: ?>
				<li><?php echo link_tag(make_url('openissue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getId())), image_tag('action_reopen.png', array('style' => 'float: left; margin-right: 5px;')) . __("Reopen this issue")); ?></li>
			<?php endif; ?>
		</ul>
		<div style="text-align: center; font-size: 14px; width: 120px; margin: 5px auto 0 auto; padding: 5px 0 5px 0; height: 20px;">
			<a href="javascript:void(0);" onclick="$('more_actions').hide();$('more_actions_div').show();"><?php echo image_tag('action_remove_small.png', array('style' => 'float: left; margin-right: 5px;')); ?><span style="float: left;"><?php echo __('Less actions'); ?></span></a>
		</div>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<div id="close_issue_fullpage" style="display: none; background-color: transparent; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; text-align: center;">
	<div class="rounded_box white_borderless" style="position: absolute; top: 50%; left: 50%; z-index: 100001; clear: both; width: 400px; margin: -200px 0 0 -200px;">
		<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
		<div class="xboxcontent" style="padding: 5px; text-align: left; font-size: 13px;">
			<div class="viewissue_info_header"><?php echo __('Close this issue'); ?></div>
			<form action="<?php echo make_url('closeissue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>" method="post" accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>">
				<div class="viewissue_info_content">
					<?php echo __('Do you want to change some of these values as well?'); ?>
					<input type="hidden" name="issue_action" value="close">
					<ul>
						<li>
							<input type="checkbox" name="set_status" id="close_issue_set_status" value="1"><label for="close_issue_set_status"><?php echo __('Status'); ?></label>
							<select name="status_id">
								<option value="0"> </option>
								<?php foreach ($statuses as $status): ?>
									<option value="<?php echo $status->getID(); ?>"><?php echo $status->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</li>
						<li id="close_issue_resolution_div"<?php if (!$issue->isResolutionVisible()): ?> style="display: none;"<?php endif; ?>>
							<input type="checkbox" name="set_resolution" id="close_issue_set_resolution" value="1"><label for="close_issue_set_resolution"><?php echo __('Resolution'); ?></label>
							<select name="resolution_id">
								<option value="0"> </option>
								<?php foreach ($fields_list['resolution']['choices'] as $resolution): ?>
									<option value="<?php echo $resolution->getID(); ?>"><?php echo $resolution->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</li>
						<?php if (!$issue->isResolutionVisible()): ?>
							<li id="close_issue_resolution_link" class="faded_medium">
								<?php echo __("Resolution isn't visible for this issuetype / product combination"); ?>
								<a href="javascript:void(0);" onclick="$('close_issue_resolution_link').hide();$('close_issue_resolution_div').show();"><?php echo __('Set anyway'); ?></a>
							</li>
						<?php endif; ?>
						<li>
							<label for="close_comment"><?php echo __('Write a comment if you want it to be added'); ?></label>
							<textarea name="close_comment" id="close_comment" style="width: 372px; height: 50px;"></textarea>
						</li>
					</ul>
					<div style="text-align: right; margin-right: 5px;">
						<input type="submit" value="<?php echo __('Close issue'); ?>">
						&nbsp;<?php echo __('or %cancel%', array('%cancel%' => '<a href="javascript:void(0);" onclick="$(\'close_issue_fullpage\').hide();">' . __('cancel') . '</a>')); ?>
					</div>
				</div>
			</form>
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
	<div style="background-color: #000; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; z-index: 100000;" class="semi_transparent"> </div>
</div>
<div class="rounded_box mediumgrey_borderless" id="more_actions_div" style="">
	<div class="xboxcontent" style="padding: 5px;">
		<div style="text-align: center; font-size: 14px; width: 120px; margin: 5px auto 0 auto; padding: 5px 0 5px 0; height: 20px;">
			<a href="javascript:void(0);" onclick="$('more_actions').show();$('more_actions_div').hide();"><?php echo image_tag('action_add_small_faded.png', array('style' => 'float: left; margin-right: 5px;')); ?><span style="float: left;"><?php echo __('More actions'); ?></span></a>
		</div>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>