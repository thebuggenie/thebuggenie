<ul style="width: 100%;" class="issue_details simple_list" id="issue_details_fieldslist">
	<li id="issuetype_field" class="issue_detail_field <?php if ($issue->isIssuetypeChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isIssuetypeMerged()): ?> issue_detail_unmerged<?php endif; ?>">
		<dl class="viewissue_list">
			<dt id="issuetype_header">
				<?php echo __('Type of issue'); ?>
			</dt>
			<dd class="hoverable">
				<?php if ($issue->isEditable() && $issue->canEditIssuetype()): ?>
					<a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'issuetype')); ?>', 'issuetype');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
					<?php echo image_tag('spinning_20.gif', array('style' => 'display: none; float: left; margin: 5px 5px 0 0;', 'id' => 'issuetype_undo_spinning')); ?>
					<a href="javascript:void(0);" onclick="if ($('issuetype_change').visible()) { $$('div.dropdown_box').each(Element.hide); } else { $$('div.dropdown_box').each(Element.hide); $('issuetype_change').toggle(); }" title="<?php echo __('Click to change issue type'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<?php endif; ?>
				<span id="issuetype_content"><?php if ($issue->hasIssueType()) echo __($issue->getIssueType()->getName()); ?></span>
				<span class="faded_out" id="no_issuetype"<?php if ($issue->getIssueType() instanceof TBGIssuetype): ?> style="display: none;"<?php endif; ?>><?php echo __('Unknown issue type'); ?></span>
			</dd>
		</dl>
		<?php if ($issue->isEditable() && $issue->canEditIssuetype()): ?>
			<div id="issuetype_change" class="rounded_box white shadowed dropdown_box" style="display: none; width: 280px; position: absolute; z-index: 10001; margin: 5px 0 5px 0;">
				<div class="dropdown_header"><?php echo __('Set issue type'); ?></div>
				<div class="dropdown_content">
					<?php echo __('Select a new issue type'); ?>:<br>
					<table cellpadding="0" cellspacing="0">
						<?php foreach ($issuetypes as $issuetype): ?>
							<tr>
								<td style="width: 16px;"><?php echo image_tag($issuetype->getIcon() . '_tiny.png'); ?></td>
								<td style="padding-left: 5px;"><a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'issuetype', 'issuetype_id' => $issuetype->getID())); ?>', 'issuetype');"><?php echo __($issuetype->getName()); ?></a></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<div id="issuetype_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
				</div>
				<div id="issuetype_change_error" class="error_message" style="display: none;"></div>
			</div>
		<?php endif; ?>
	</li>
	<li id="status_field" class="issue_detail_field <?php if ($issue->isStatusChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isStatusMerged()): ?> issue_detail_unmerged<?php endif; ?>">
		<dl class="viewissue_list">
			<dt id="status_header">
				<?php echo __('Status'); ?>
			</dt>
			<dd class="hoverable">
				<?php if ($issue->isUpdateable() && $issue->canEditStatus()): ?>
					<a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'status')); ?>', 'status');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
					<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'status_undo_spinning')); ?>
					<a href="javascript:void(0);" onclick="if ($('status_change').visible()) { $$('div.dropdown_box').each(Element.hide); } else { $$('div.dropdown_box').each(Element.hide); $('status_change').toggle(); }" title="<?php echo __('Click to change status'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<?php endif; ?>
				<table style="table-layout: auto; display: inline; width: auto;<?php if (!$issue->getStatus() instanceof TBGDatatype): ?> display: none;<?php endif; ?>" cellpadding=0 cellspacing=0 id="status_table">
					<tr>
						<td style="width: 18px;"><div style="border: 1px solid rgba(0, 0, 0, 0.3); background-color: <?php echo ($issue->getStatus() instanceof TBGDatatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 15px; height: 15px; margin-right: 2px;" id="status_<?php echo $issue->getID(); ?>_color">&nbsp;</div></td>
						<td style="padding-left: 5px;" id="status_content"><?php if ($issue->getStatus() instanceof TBGDatatype) echo __($issue->getStatus()->getName()); ?></td>
					</tr>
				</table>
				<span class="faded_out" id="no_status"<?php if ($issue->getStatus() instanceof TBGDatatype): ?> style="display: none;"<?php endif; ?>><?php echo __('Status not determined'); ?></span>
			</dd>
		</dl>
		<?php if ($issue->isUpdateable() && $issue->canEditStatus()): ?>
			<div class="rounded_box white shadowed dropdown_box" id="status_change" style="display: none; width: 280px; position: absolute; z-index: 10001; margin: 5px 0 5px 0; padding: 5px;">
				<div class="dropdown_header"><?php echo __('Set status'); ?></div>
				<div class="dropdown_content">
					<a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'status', 'status_id' => 0)); ?>', 'status');"><?php echo __('Clear the status'); ?></a><br>
				</div>
				<div class="dropdown_content">
					<?php echo __('%clear_the_status% or click to select a new status', array('%clear_the_status%' => '')); ?>:<br>
					<table cellpadding="0" cellspacing="0">
						<?php foreach ($statuses as $status): ?>
							<?php if (!$status->canUserSet($tbg_user)) continue; ?>
							<tr>
								<td style="width: 16px;"><div style="border: 1px solid #AAA; background-color: <?php echo $status->getColor(); ?>; font-size: 1px; width: 16px; height: 15px; margin-right: 2px;">&nbsp;</div></td>
								<td style="padding-left: 5px;"><a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'status', 'status_id' => $status->getID())); ?>', 'status');"><?php echo __($status->getName()); ?></a></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<div id="status_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
				</div>
				<div id="status_change_error" class="error_message" style="display: none;"></div>
			</div>
		<?php endif; ?>
	</li>
	<li id="assigned_to_field" class="issue_detail_field<?php if ($issue->isAssigneeChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isAssigneeMerged()): ?> issue_detail_unmerged<?php endif; ?>">
		<dl class="viewissue_list">
			<dt id="assigned_to_header"><?php echo __('Assigned to'); ?></dt>
			<dd id="assigned_to_content" class="<?php if ($issue->isAssigneeChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isAssigneeMerged()): ?> issue_detail_unmerged<?php endif; ?>">
				<?php if ($issue->canEditAssignee() && $issue->isEditable()): ?>
					<a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to')); ?>', 'assigned_to');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
					<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'assigned_to_undo_spinning')); ?>
					<a href="javascript:void(0);" onclick="if ($('assigned_to_change').visible()) { $$('div.dropdown_box').each(Element.hide); } else { $$('div.dropdown_box').each(Element.hide); $('assigned_to_change').toggle(); }" title="<?php echo __('Click to change assignee'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<?php endif; ?>
				<div style="width: 170px; display: <?php if ($issue->isAssigned()): ?>inline<?php else: ?>none<?php endif; ?>;" id="assigned_to_name">
					<?php if ($issue->getAssignee() instanceof TBGUser): ?>
						<?php echo include_component('main/userdropdown', array('user' => $issue->getAssignee())); ?>
					<?php elseif ($issue->getAssignee() instanceof TBGTeam): ?>
						<?php echo include_component('main/teamdropdown', array('team' => $issue->getAssignee())); ?>
					<?php endif; ?>
				</div>
				<span class="faded_out" id="no_assigned_to"<?php if ($issue->isAssigned()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not assigned to anyone'); ?></span>
			</dd>
		</dl>
		<?php if ($issue->canEditAssignee() && $issue->isEditable()): ?>
			<?php include_component('identifiableselector', array(	'html_id' 			=> 'assigned_to_change', 
																	'header' 			=> __('Assign this issue'),
																	'callback'		 	=> "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => 'user', 'value' => '%identifiable_value%')) . "', 'assigned_to');",
																	'team_callback'	 	=> "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => 'team', 'value' => '%identifiable_value%')) . "', 'assigned_to');",
																	'teamup_callback' 	=> "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => 'team', 'value' => '%identifiable_value%', 'teamup' => true)) . "', 'assigned_to');",
																	'clear_link_text'	=> __('Clear current assignee'),
																	'base_id'			=> 'assigned_to',
																	'include_teams'		=> true,
																	'absolute'			=> true,
																	'classes'			=> 'dropdown_box')); ?>
		<?php endif; ?>
	</li>
	<li class="viewissue_list issue_detail_field<?php if ($issue->isPercentCompletedChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isPercentCompletedMerged()): ?> issue_detail_unmerged<?php endif; ?>" id="percent_complete_field"<?php if (!$issue->isPercentCompletedVisible()): ?> style="display: none;"<?php endif; ?>>
		<dl class="viewissue_list">
			<dt id="percent_complete_header"><?php echo __('Progress'); ?></dt>
			<dd id="percent_complete_content">
				<div style="width: 100%;">
					<?php if ($issue->canEditPercentage()): ?>
						<a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'percent_complete')); ?>', 'percent_complete');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
						<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'percent_complete_undo_spinning')); ?>
						<a href="javascript:void(0);" onclick="if ($('percent_complete_change').visible()) { $$('div.dropdown_box').each(Element.hide); } else { $$('div.dropdown_box').each(Element.hide); $('percent_complete_change').toggle(); }" title="<?php echo __('Click to set percent completed'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
					<?php endif; ?>
					<?php include_template('main/percentbar', array('percent' => $issue->getPercentCompleted(), 'height' => 14)); ?>
					<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'percent_complete_spinning')); ?>
				</div>
			</dd>
		</dl>
		<?php if ($issue->canEditPercentage()): ?>
			<div class="rounded_box white shadowed dropdown_box" id="percent_complete_change" style="display: none; width: 280px; position: absolute; z-index: 10001; margin: 5px 0 5px 0; padding: 5px;">
				<div class="dropdown_header"><?php echo __('Set percent completed'); ?></div>
				<div class="dropdown_content">
					<form id="percent_complete_form" method="post" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="" onsubmit="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'percent_complete')); ?>', 'percent_complete', 'percent_complete');return false;">
						<label for="set_percent"><?php echo __('Percent complete'); ?></label>&nbsp;<input type="text" style="width: 40px;" name="percent" id="set_percent">&percnt;
						<input type="submit" value="<?php echo __('Set'); ?>">
					</form>
					<?php echo __('%set_percent_completed% or %clear_percent_completed%', array('%set_percent_completed%' => '', '%clear_percent_completed%' => '')); ?><br>
					<a href="javascript:void(0);" onclick="TBG.Issues.Field.setPercent('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'percent_complete', 'percent' => 0)); ?>', 'set');"><?php echo __('Clear percent completed'); ?></a><br>
				</div>
			</div>
		<?php endif; ?>
	</li>
	<li id="pain_bug_type_field" class="issue_detail_field<?php if ($issue->isPainBugTypeChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isPainBugTypeMerged()): ?> issue_detail_unmerged<?php endif; ?>" style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>">
		<dl class="viewissue_list">
			<dt id="pain_bug_type_header"><?php echo __('Type of bug'); ?></dt>
			<dd id="pain_bug_type_content">
				<?php if ($issue->isUpdateable() && $issue->canEditUserPain()): ?>
					<a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_bug_type')); ?>', 'pain_bug_type');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
					<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'pain_bug_type_undo_spinning')); ?>
					<a href="javascript:void(0);" onclick="if ($('pain_bug_type_change').visible()) { $$('div.dropdown_box').each(Element.hide); } else { $$('div.dropdown_box').each(Element.hide); $('pain_bug_type_change').toggle(); }" title="<?php echo __('Click to triage type of bug'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<?php endif; ?>
				<span id="pain_bug_type_name"<?php if (!$issue->hasPainBugType()): ?> style="display: none;"<?php endif; ?>>
					<?php echo ($issue->hasPainBugType()) ? $issue->getPainBugTypeLabel() : ''; ?>
				</span>
				<span class="faded_out" id="no_pain_bug_type"<?php if ($issue->hasPainBugType()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not triaged'); ?></span>
			</dd>
		</dl>
		<?php if ($issue->isUpdateable() && $issue->canEditUserPain()): ?>
			<div class="rounded_box white shadowed dropdown_box" id="pain_bug_type_change" style="display: none; width: 280px; position: absolute; z-index: 10001; margin: 5px 0 5px 0; padding: 5px;">
				<div class="dropdown_header"><?php echo __('Triage bug type'); ?></div>
				<div class="dropdown_content">
					<a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_bug_type', 'pain_bug_type_id' => 0)); ?>', 'pain_bug_type');"><?php echo __('Clear bug type'); ?></a><br>
				</div>
				<div class="dropdown_content">
					<?php echo __('%clear_the_bugtype% or click to select a new bug type', array('%clear_the_bugtype%' => '')); ?>:<br>
					<ul class="choices">
						<?php foreach (TBGIssue::getPainTypesOrLabel('pain_bug_type') as $choice_id => $choice): ?>
							<li>
								<a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_bug_type', 'pain_bug_type_id' => $choice_id)); ?>', 'pain_bug_type');"><?php echo $choice; ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
					<div id="pain_bug_type_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
				</div>
				<div id="pain_bug_type_change_error" class="error_message" style="display: none;"></div>
			</div>
		<?php endif; ?>
	</li>
	<li id="pain_likelihood_field" class="issue_detail_field<?php if ($issue->isPainLikelihoodChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isPainLikelihoodMerged()): ?> issue_detail_unmerged<?php endif; ?>" style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>">
		<dl class="viewissue_list">
			<dt id="pain_likelihood_header"><?php echo __('Likelihood'); ?></dt>
			<dd id="pain_likelihood_content" class="<?php if ($issue->isPainLikelihoodChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isPainLikelihoodMerged()): ?> issue_detail_unmerged<?php endif; ?>">
				<?php if ($issue->isUpdateable() && $issue->canEditUserPain()): ?>
					<a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_likelihood')); ?>', 'pain_likelihood');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
					<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'pain_likelihood_undo_spinning')); ?>
					<a href="javascript:void(0);" onclick="if ($('pain_likelihood_change').visible()) { $$('div.dropdown_box').each(Element.hide); } else { $$('div.dropdown_box').each(Element.hide); $('pain_likelihood_change').toggle(); }" title="<?php echo __('Click to triage likelihood'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<?php endif; ?>
				<span id="pain_likelihood_name"<?php if (!$issue->hasPainLikelihood()): ?> style="display: none;"<?php endif; ?>>
					<?php echo ($issue->hasPainLikelihood()) ? $issue->getPainLikelihoodLabel() : ''; ?>
				</span>
				<span class="faded_out" id="no_pain_likelihood"<?php if ($issue->hasPainLikelihood()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not triaged'); ?></span>
			</dd>
		</dl>
		<?php if ($issue->isUpdateable() && $issue->canEditUserPain()): ?>
			<div class="rounded_box white shadowed dropdown_box" id="pain_likelihood_change" style="display: none; width: 280px; position: absolute; z-index: 10001; margin: 5px 0 5px 0; padding: 5px;">
				<div class="dropdown_header"><?php echo __('Triage likelihood'); ?></div>
				<div class="dropdown_content">
					<a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_likelihood', 'pain_likelihood_id' => 0)); ?>', 'pain_likelihood');"><?php echo __('Clear likelihood'); ?></a><br>
				</div>
				<div class="dropdown_content">
					<?php echo __('%clear_the_likelihood% or click to select a new likelihood', array('%clear_the_likelihood%' => '')); ?>:<br>
					<ul class="choices">
						<?php foreach (TBGIssue::getPainTypesOrLabel('pain_likelihood') as $choice_id => $choice): ?>
							<li>
								<?php //echo image_tag('icon_' . $field . '.png', array('style' => 'float: left; margin-right: 5px;')); ?><a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_likelihood', 'pain_likelihood_id' => $choice_id)); ?>', 'pain_likelihood');"><?php echo $choice; ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
					<div id="pain_likelihood_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
				</div>
				<div id="pain_likelihood_change_error" class="error_message" style="display: none;"></div>
			</div>
		<?php endif; ?>
	</li>
	<li id="pain_effect_field" class="issue_detail_field<?php if ($issue->isPainEffectChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isPainEffectMerged()): ?> issue_detail_unmerged<?php endif; ?>" style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>">
		<dl class="viewissue_list">
			<dt id="pain_effect_header"><?php echo __('Effect'); ?></dt>
			<dd id="pain_effect_content" class="<?php if ($issue->isPainEffectChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isPainEffectMerged()): ?> issue_detail_unmerged<?php endif; ?>">
				<?php if ($issue->isUpdateable() && $issue->canEditUserPain()): ?>
					<a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_effect')); ?>', 'pain_effect');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
					<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'pain_effect_undo_spinning')); ?>
					<a href="javascript:void(0);" onclick="if ($('pain_effect_change').visible()) { $$('div.dropdown_box').each(Element.hide); } else { $$('div.dropdown_box').each(Element.hide); $('pain_effect_change').toggle(); }" title="<?php echo __('Click to triage effect'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<?php endif; ?>
				<span id="pain_effect_name"<?php if (!$issue->hasPainEffect()): ?> style="display: none;"<?php endif; ?>>
					<?php echo ($issue->hasPainEffect()) ? $issue->getPainEffectLabel() : ''; ?>
				</span>
				<span class="faded_out" id="no_pain_effect"<?php if ($issue->hasPainEffect()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not triaged'); ?></span>
			</dd>
		</dl>
		<?php if ($issue->isUpdateable() && $issue->canEditUserPain()): ?>
			<div class="rounded_box white shadowed dropdown_box" id="pain_effect_change" style="display: none; width: 280px; position: absolute; z-index: 10001; margin: 5px 0 5px 0; padding: 5px;">
				<div class="dropdown_header"><?php echo __('Triage effect'); ?></div>
				<div class="dropdown_content">
					<a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_effect', 'pain_effect_id' => 0)); ?>', 'pain_effect');"><?php echo __('Clear effect'); ?></a><br>
				</div>
				<div class="dropdown_content">
					<?php echo __('%clear_the_effect% or click to select a new effect', array('%clear_the_effect%' => '')); ?>:<br>
					<ul class="choices">
						<?php foreach (TBGIssue::getPainTypesOrLabel('pain_effect') as $choice_id => $choice): ?>
							<li>
								<?php //echo image_tag('icon_' . $field . '.png', array('style' => 'float: left; margin-right: 5px;')); ?><a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_effect', 'pain_effect_id' => $choice_id)); ?>', 'pain_effect');"><?php echo $choice; ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
					<div id="pain_effect_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
				</div>
				<div id="pain_effect_change_error" class="error_message" style="display: none;"></div>
			</div>
		<?php endif; ?>
	</li>
	<li id="posted_by_field" class="issue_detail_field<?php if ($issue->isPostedByChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isPostedByMerged()): ?> issue_detail_unmerged<?php endif; ?>">
		<dl class="viewissue_list">
			<dt id="posted_by_header"><?php echo __('Posted by'); ?></dt>
			<dd id="posted_by_content" class="<?php if ($issue->isPostedByChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isPostedByMerged()): ?> issue_detail_unmerged<?php endif; ?>">
				<?php if ($issue->isEditable() && $issue->canEditPostedBy()): ?>
					<a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'posted_by')); ?>', 'posted_by');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
					<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'posted_by_undo_spinning')); ?>
					<a href="javascript:void(0);" onclick="if ($('posted_by_change').visible()) { $$('div.dropdown_box').each(Element.hide); } else { $$('div.dropdown_box').each(Element.hide); $('posted_by_change').toggle(); }" title="<?php echo __('Click to change owner'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<?php endif; ?>
				<div style="width: 170px; display: inline;" id="posted_by_name">
					<?php echo include_component('main/userdropdown', array('user' => $issue->getPostedBy())); ?>
				</div>
				<span id="no_posted_by" style="display: none;"> </span>
			</dd>
		</dl>
		<?php if ($issue->isEditable() && $issue->canEditPostedBy()): ?>
			<?php include_component('identifiableselector', array(	'html_id' 			=> 'posted_by_change', 
																	'header' 			=> __('Change poster'),
																	'allow_clear'		=> false,
																	'clear_link_text'	=> '',
																	'callback'		 	=> "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'posted_by', 'value' => '%identifiable_value%')) . "', 'posted_by');",
																	'base_id'			=> 'posted_by',
																	'absolute'			=> true,
																	'classes'			=> 'dropdown_box')); ?>
		<?php endif; ?>
	</li>
	<li id="owned_by_field" class="issue_detail_field<?php if ($issue->isOwnerChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isOwnerMerged()): ?> issue_detail_unmerged<?php endif; ?>">
		<dl class="viewissue_list">
			<dt id="owned_by_header"><?php echo __('Owned by'); ?></dt>
			<dd id="owned_by_content" class="<?php if ($issue->isOwnerChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isOwnerMerged()): ?> issue_detail_unmerged<?php endif; ?>">
				<?php if ($issue->isUpdateable() && $issue->canEditOwner()): ?>
					<a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by')); ?>', 'owned_by');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
					<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'owned_by_undo_spinning')); ?>
					<a href="javascript:void(0);" onclick="if ($('owned_by_change').visible()) { $$('div.dropdown_box').each(Element.hide); } else { $$('div.dropdown_box').each(Element.hide); $('owned_by_change').toggle(); }" title="<?php echo __('Click to change owner'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<?php endif; ?>
				<div style="width: 170px; display: <?php if ($issue->isOwned()): ?>inline<?php else: ?>none<?php endif; ?>;" id="owned_by_name">
					<?php if ($issue->getOwner() instanceof TBGUser): ?>
						<?php echo include_component('main/userdropdown', array('user' => $issue->getOwner())); ?>
					<?php elseif ($issue->getOwner() instanceof TBGTeam): ?>
						<?php echo include_component('main/teamdropdown', array('team' => $issue->getOwner())); ?>
					<?php endif; ?>
				</div>
				<span class="faded_out" id="no_owned_by"<?php if ($issue->isOwned()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not owned by anyone'); ?></span>
			</dd>
		</dl>
		<?php if ($issue->isUpdateable() && $issue->canEditOwner()): ?>
			<?php include_component('identifiableselector', array(	'html_id' 			=> 'owned_by_change', 
																	'header' 			=> __('Change issue owner'),
																	'callback'		 	=> "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => 'user', 'value' => '%identifiable_value%')) . "', 'owned_by');",
																	'team_callback'	 	=> "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value%')) . "', 'owned_by');",
																	'teamup_callback' 	=> "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value%', 'teamup' => true)) . "', 'owned_by');",
																	'clear_link_text'	=> __('Clear current owner'),
																	'base_id'			=> 'owned_by',
																	'include_teams'		=> true,
																	'absolute'			=> true,
																	'classes'			=> 'dropdown_box')); ?>
		<?php endif; ?>
	</li>
	<li id="estimated_time_field"<?php if (!$issue->isEstimatedTimeVisible()): ?> style="display: none;"<?php endif; ?> class="issue_detail_field<?php if ($issue->isEstimatedTimeChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isEstimatedTimeMerged()): ?> issue_detail_unmerged<?php endif; ?>">
		<dl class="viewissue_list">
			<dt id="estimated_time_header"><?php echo __('Estimated time'); ?></dt>
			<dd id="estimated_time_content">
				<?php if ($issue->isUpdateable() && $issue->canEditEstimatedTime()): ?>
					<a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'estimated_time')); ?>', 'estimated_time');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
					<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'estimated_time_undo_spinning')); ?>
					<a href="javascript:void(0);" onclick="if ($('estimated_time_<?php echo $issue->getID(); ?>_change').visible()) { $$('div.dropdown_box').each(Element.hide); } else { $$('div.dropdown_box').each(Element.hide); $('estimated_time_<?php echo $issue->getID(); ?>_change').toggle(); }" title="<?php echo __('Click to estimate this issue'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<?php endif; ?>
				<span id="estimated_time_<?php echo $issue->getID(); ?>_name"<?php if (!$issue->hasEstimatedTime()): ?> style="display: none;"<?php endif; ?>>
					<?php echo $issue->getFormattedTime($issue->getEstimatedTime()); ?>
				</span>
				<span class="faded_out" id="no_estimated_time_<?php echo $issue->getID(); ?>"<?php if ($issue->hasEstimatedTime()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not estimated'); ?></span>
			</dd>
		</dl>
		<?php if ($issue->isUpdateable() && $issue->canEditEstimatedTime()): ?>
			<?php include_component('main/issueestimator', array('issue' => $issue, 'field' => 'estimated_time', 'mode' => 'left')); ?>
		<?php endif; ?>
	</li>
	<li id="spent_time_field"<?php if (!$issue->isSpentTimeVisible()): ?> style="display: none;"<?php endif; ?> class="issue_detail_field<?php if ($issue->isSpentTimeChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isSpentTimeMerged()): ?> issue_detail_unmerged<?php endif; ?>">
		<dl class="viewissue_list">
			<dt id="spent_time_header"><?php echo __('Time spent'); ?></dt>
			<dd id="spent_time_content">
				<?php if ($issue->canEditSpentTime()): ?>
					<a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'spent_time')); ?>', 'spent_time');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
					<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'spent_time_undo_spinning')); ?>
					<a href="javascript:void(0);" onclick="if ($('spent_time_<?php echo $issue->getID(); ?>_change').visible()) { $$('div.dropdown_box').each(Element.hide); } else { $$('div.dropdown_box').each(Element.hide); $('spent_time_<?php echo $issue->getID(); ?>_change').toggle(); }" title="<?php echo __('Click to enter time spent on this issue'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
				<?php endif; ?>
				<span id="spent_time_<?php echo $issue->getID(); ?>_name"<?php if (!$issue->hasSpentTime()): ?> style="display: none;"<?php endif; ?>>
					<?php echo $issue->getFormattedTime($issue->getSpentTime()); ?>
				</span>
				<span class="faded_out" id="no_spent_time_<?php echo $issue->getID(); ?>"<?php if ($issue->hasSpentTime()): ?> style="display: none;"<?php endif; ?>><?php echo __('No time spent'); ?></span>
			</dd>
		</dl>
		<?php if ($issue->canEditSpentTime()): ?>
			<?php include_component('main/issueestimator', array('issue' => $issue, 'field' => 'spent_time', 'mode' => 'left')); ?>
		<?php endif; ?>
	</li>
	<?php foreach ($fields_list as $field => $info): ?>
		<li id="<?php echo $field; ?>_field" class="issue_detail_field<?php if (!$info['merged']): ?> issue_detail_unmerged<?php elseif ($info['changed']): ?> issue_detail_changed<?php endif; ?>"<?php if (!$info['visible']): ?> style="display: none;"<?php endif; ?>>
			<dl class="viewissue_list">
				<dt id="<?php echo $field; ?>_header">
					<?php echo $info['title']; ?>
				</dt>
				<dd id="<?php echo $field; ?>_content">
					<?php $canEditField = "canEdit".ucfirst($field); ?>
					<?php if (array_key_exists('choices', $info) && isset($info['choices']) && $issue->$canEditField()): ?>
						<a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>', '<?php echo $field; ?>');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
						<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => $field . '_undo_spinning')); ?>
						<a href="javascript:void(0);" onclick="if ($('<?php echo $field; ?>_change').visible()) { $$('div.dropdown_box').each(Element.hide); } else { $$('div.dropdown_box').each(Element.hide); $('<?php echo $field; ?>_change').toggle(); }" title="<?php echo $info['change_tip']; ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
					<?php endif; ?>
					<?php if ($info['icon']): ?>
						<?php echo image_tag($info['icon_name'], array('style' => 'float: left; margin-right: 5px;')); ?>
					<?php endif; ?>
					<?php if (array_key_exists('url', $info) && $info['url']): ?>
						<a id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?> target="_new" href="<?php echo $info['current_url']; ?>"><?php echo __($info['name']); ?></a>
					<?php else: ?>
						<span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __($info['name']); ?></span>
					<?php endif; ?>
					<span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __('Not determined'); ?></span>
				</dd>
			</dl>
			<div style="clear: both;"> </div>
			<?php if (array_key_exists('choices', $info) && isset($info['choices']) && $issue->$canEditField()): ?>
				<div class="rounded_box white shadowed dropdown_box" id="<?php echo $field; ?>_change" style="display: none; width: 280px; position: absolute; z-index: 10001; margin: 5px 0 5px 0; padding: 5px;">
					<div class="dropdown_header"><?php echo $info['change_header']; ?></div>
					<div class="dropdown_content">
						<a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_id' => 0)); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a><br>
					</div>
					<?php if (count($info['choices'])): ?>
						<div class="dropdown_content">
							<?php echo $info['select']; ?>:<br>
							<ul class="choices">
								<?php foreach ($info['choices'] as $choice): ?>
									<?php if ($choice instanceof TBGDatatypeBase && !$choice->canUserSet($tbg_user)) continue; ?>
									<li>
										<?php echo image_tag('icon_' . $field . '.png', array('style' => 'float: left; margin-right: 5px;')); ?><a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_id' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo __($choice->getName()); ?></a>
									</li>
								<?php endforeach; ?>
							</ul>
							<div id="<?php echo $field; ?>_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
						</div>
					<?php endif; ?>
					<div id="<?php echo $field; ?>_change_error" class="error_message" style="display: none;"></div>
				</div>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
	<?php foreach ($customfields_list as $field => $info): ?>
		<?php if ($info['type'] == TBGCustomDatatype::INPUT_TEXTAREA_MAIN): continue; endif; ?>
		<li id="<?php echo $field; ?>_field" class="issue_detail_field<?php if (!$info['merged']): ?> issue_detail_unmerged<?php elseif ($info['changed']): ?> issue_detail_changed<?php endif; ?>"<?php if (!$info['visible']): ?> style="display: none;"<?php endif; ?>>
			<dl class="viewissue_list">
				<dt id="<?php echo $field; ?>_header">
					<?php echo $info['title']; ?>
				</dt>
				<dd id="<?php echo $field; ?>_content">
					<?php if ($issue->isUpdateable() && $issue->canEditCustomFields() && $info['editable']): ?>
						<a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>', '<?php echo $field; ?>');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
						<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => $field . '_undo_spinning')); ?>
						<a href="javascript:void(0);" onclick="if ($('<?php echo $field; ?>_change').visible()) { $$('div.dropdown_box').each(Element.hide); } else { $$('div.dropdown_box').each(Element.hide); $('<?php echo $field; ?>_change').toggle(); }" title="<?php echo $info['change_tip']; ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
					<?php endif; ?>
					<?php echo image_tag('icon_customdatatype.png', array('style' => 'float: left; margin-right: 5px;')); ?>
					<?php
						switch ($info['type'])
						{
							case TBGCustomDatatype::INPUT_TEXTAREA_SMALL:
								?><span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>><?php echo tbg_parse_text($info['name'], false, null, array('headers' => false)); ?></span><span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __('Not determined'); ?></span><?php
								break;
							case TBGCustomDatatype::EDITIONS_CHOICE:
							case TBGCustomDatatype::COMPONENTS_CHOICE:
							case TBGCustomDatatype::RELEASES_CHOICE:
								$edition = null;
								$value = null;
								try
								{
									switch ($info['type'])
									{
										case TBGCustomDatatype::EDITIONS_CHOICE:
											$edition = new TBGEdition($info['name']);
											$value = $edition->getName();
											break;
										case TBGCustomDatatype::COMPONENTS_CHOICE:
											$edition = new TBGComponent($info['name']);
											$value = $edition->getName();
											break;
										case TBGCustomDatatype::RELEASES_CHOICE:
											$edition = new TBGBuild($info['name']);
											$value = $edition->getName();
											break;
									}
								}
								catch (Exception $e) { }
								?><span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>><?php echo $value; ?></span><span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __('Not determined'); ?></span><?php
								break;
							case TBGCustomDatatype::STATUS_CHOICE:
								$status = null;
								$value = null;
								$color = '#FFF';
								try
								{
									$status = new TBGStatus($info['name']);
									$value = $status->getName();
									$color = $status->getColor();
								}
								catch (Exception $e) { }
								?><span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>><div style="border: 1px solid #AAA; background-color: <?php echo $color; ?>; font-size: 1px; width: 20px; height: 15px; margin-right: 5px; float: left;" id="status_color">&nbsp;</div><?php echo __($value); ?></span><span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __('Not determined'); ?></span><?php
								break;
							default:
								?><span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>><?php echo (filter_var($info['name'], FILTER_VALIDATE_URL) !== false) ? link_tag($info['name'], $info['name']) : $info['name']; ?></span><span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __('Not determined'); ?></span><?php
								break;
						}
					?>
				</dd>
			</dl>
			<div style="clear: both;"> </div>
			<?php if ($issue->isUpdateable() && $issue->canEditCustomFields()): ?>
				<div class="rounded_box white shadowed dropdown_box" id="<?php echo $field; ?>_change" style="display: none; width: 280px; position: absolute; z-index: 10001; margin: 5px 0 5px 0; padding: 5px;">
					<div class="dropdown_header"><?php echo $info['change_header']; ?></div>
					<div class="dropdown_content">
						<?php if (array_key_exists('choices', $info)): ?>
							<?php echo $info['select']; ?>:<br>
							<a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a><br>
							<ul class="choices">
								<?php foreach ($info['choices'] as $choice): ?>
									<?php //if (!$choice->canUserSet($tbg_user)) continue; ?>
									<li>
										<?php echo image_tag('icon_customdatatype.png', array('style' => 'float: left; margin-right: 5px;')); ?><a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo __($choice->getName()); ?></a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php else: ?>
							<?php echo $info['select']; ?>:<br>
							<?php

							switch ($info['type'])
							{
								case TBGCustomDatatype::EDITIONS_CHOICE:
									?>
										<a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a><br>
										<ul class="choices">
											<?php foreach ($issue->getProject()->getEditions() as $choice): ?>
												<li>
													<?php echo image_tag('icon_edition.png', array('style' => 'float: left; margin-right: 5px;')); ?><a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo __($choice->getName()); ?></a>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php
									break;
								case TBGCustomDatatype::STATUS_CHOICE:
									?>
										<a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a><br>
										<ul class="choices">
											<?php foreach (TBGStatus::getAll($issue->getProject()->getID()) as $choice): ?>
												<li>
													<div style="border: 1px solid #AAA; background-color: <?php echo $choice->getColor(); ?>; font-size: 1px; width: 20px; height: 15px; margin-right: 7px; float: left;" id="status_color">&nbsp;</div><a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo __($choice->getName()); ?></a>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php
									break;
								case TBGCustomDatatype::COMPONENTS_CHOICE:
									?>
										<a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a><br>
										<ul class="choices">
											<?php foreach ($issue->getProject()->getComponents() as $choice): ?>
												<li>
													<?php echo image_tag('icon_components.png', array('style' => 'float: left; margin-right: 5px;')); ?><a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo $choice->getName(); ?></a>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php
									break;
								case TBGCustomDatatype::RELEASES_CHOICE:
									?>
										<a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a><br>
										<ul class="choices">
											<?php foreach ($issue->getProject()->getBuilds() as $choice): ?>
												<li>
													<?php echo image_tag('icon_build.png', array('style' => 'float: left; margin-right: 5px;')); ?><a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo $choice->getName(); ?></a>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php
									break;
								case TBGCustomDatatype::INPUT_TEXT:
									?>
										<a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a><br>
										<form id="<?php echo $field; ?>_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>" method="post" onSubmit="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)) ?>', '<?php echo $field; ?>', '<?php echo $field; ?>'); return false;">
											<input type="text" name="<?php echo $field; ?>_value" value="<?php echo $info['name'] ?>" /><?php echo __('%save% or %cancel%', array('%save%' => '<input type="submit" value="'.__('Save').'">', '%cancel%' => '<a href="#" onclick="$(\''.$field.'_change\').hide(); return false;">'.__('cancel').'</a>')); ?>
										</form>
									<?php
									break;
								case TBGCustomDatatype::INPUT_TEXTAREA_SMALL:
									?>
										<a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a><br>
										<form id="<?php echo $field; ?>_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>" method="post" onSubmit="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)) ?>', '<?php echo $field; ?>', '<?php echo $field; ?>'); return false;">
											<?php include_template('main/textarea', array('area_name' => $field.'_value', 'area_id' => $field.'_value', 'height' => '100px', 'width' => '100%', 'value' => $info['name'])); ?>
											<br><?php echo __('%save% or %cancel%', array('%save%' => '<input type="submit" value="'.__('Save').'">', '%cancel%' => '<a href="#" onclick="$(\''.$field.'_change\').hide(); return false;">'.__('cancel').'</a>')); ?>
										</form>
									<?php
									break;
							}

						endif; ?>
						<div id="<?php echo $field; ?>_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
					</div>
					<div id="<?php echo $field; ?>_change_error" class="error_message" style="display: none;"></div>
				</div>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>