<ul id="more_actions_<?php echo $issue->getID(); ?>" style="display: none; position: absolute; width: 300px; top: 0; right: 0; z-index: 1000;" class="simple_list rounded_box white shadowed more_actions_dropdown dropdown_box popup_box" onclick="$('more_actions_<?php echo $issue->getID(); ?>_button').toggleClassName('button-pressed');TBG.Main.Profile.clearPopupsAndButtons();">
	<?php if (!$issue->getProject()->isArchived() && $issue->canEditIssueDetails()): ?>
		<?php if (!isset($multi) || !$multi): ?>
			<li class="header"><?php echo __('Additional actions available'); ?></li>
		<?php endif; ?>
		<?php if ($issue->canEditMilestone()): ?>
			<?php if ($issue->isOpen()): ?>
				<li id="more_actions_mark_notblocking_link_<?php echo $issue->getID(); ?>"<?php if (!$issue->isBlocking()): ?> style="display: none;"<?php endif; ?>><?php echo javascript_link_tag(image_tag('icon_unblock.png').__("Mark as not blocking the next release"), array('onclick' => "TBG.Issues.toggleBlocking('".make_url('unblock', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getId()))."', ".$issue->getID().");")); ?></li>
				<li id="more_actions_mark_blocking_link_<?php echo $issue->getID(); ?>"<?php if ($issue->isBlocking()): ?> style="display: none;"<?php endif; ?>><?php echo javascript_link_tag(image_tag('icon_block.png').__("Mark as blocking the next release"), array('onclick' => "TBG.Issues.toggleBlocking('".make_url('block', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getId()))."', ".$issue->getID().");")); ?></li>
			<?php else: ?>
				<li id="more_actions_mark_notblocking_link_<?php echo $issue->getID(); ?>"<?php if (!$issue->isBlocking()): ?> style="display: none;"<?php endif; ?> class="disabled"><a href="javascript:void(0);"><?php echo image_tag('icon_unblock.png').__("Mark as not blocking the next release"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available when this issue is closed'); ?></div></li>
				<li id="more_actions_mark_blocking_link_<?php echo $issue->getID(); ?>"<?php if ($issue->isBlocking()): ?> style="display: none;"<?php endif; ?> class="disabled"><a href="javascript:void(0);"><?php echo image_tag('icon_block.png').__("Mark as blocking the next release"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available when this issue is closed'); ?></div></li>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ((!isset($multi) || !$multi) && ($issue->isUpdateable() && ($issue->canAttachLinks() || (TBGSettings::isUploadsEnabled() && $issue->canAttachFiles())))): ?>
			<?php if ($issue->canAttachLinks()): ?>
				<li><a href="javascript:void(0);" id="attach_link_button" onclick="$('attach_link').toggle();"><?php echo image_tag('action_add_link.png').__('Attach a link'); ?></a></li>
			<?php endif; ?>
			<?php if (TBGSettings::isUploadsEnabled() && $issue->canAttachFiles()): ?>
				<li><a href="javascript:void(0);" id="attach_file_button" onclick="$('attach_file').toggle();"><?php echo image_tag('action_add_file.png').__('Attach a file'); ?></a></li>
			<?php else: ?>
				<li class="disabled"><a href="javascript:void(0);" id="attach_file_button" onclick="TBG.Main.Helpers.Message.error('<?php echo __('File uploads are not enabled'); ?>', '<?php echo __('Before you can upload attachments, file uploads needs to be activated'); ?>');"><?php echo image_tag('action_add_file.png').__('Attach a file'); ?></a></li>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($issue->isEditable()): ?>
			<?php if ($issue->canEditAffectedComponents() || $issue->canEditAffectedBuilds() || $issue->canEditAffectedEditions()): ?>
				<li><a id="affected_add_button" href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_add_item', 'issue_id' => $issue->getID())); ?>');"><?php echo image_tag('action_add_affected.png').__('Add affected item'); ?></a></li>
			<?php else: ?>
				<li class="disabled"><a id="affected_add_button" href="javascript:void(0);" onclick="TBG.Main.Helpers.Message.error('<?php echo __('You are not allowed to add an item to this list'); ?>');"><?php echo image_tag('action_add_affected.png').__('Add affected item'); ?></a></li>
			<?php endif; ?>
		<?php elseif ($issue->canEditAffectedComponents() || $issue->canEditAffectedBuilds() || $issue->canEditAffectedEditions()): ?>
			<li class="disabled"><a href="javascript:void(0);"><?php echo image_tag('action_add_affected.png').__("Add affected item"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available when this issue is closed'); ?></div></li>
		<?php endif; ?>
		<?php if ($issue->isUpdateable()): ?>
			<?php if ($issue->canAddRelatedIssues() && $tbg_user->canReportIssues($issue->getProject())): ?>
				<li><?php echo javascript_link_tag(image_tag('icon_new_related_issue.png').__('Create a new related issue'), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $issue->getProject()->getId(), 'parent_issue_id' => $issue->getID()))."');", 'title' => __('Create a new related issue'))); ?></li>
			<?php endif; ?>
			<?php if ($issue->canAddRelatedIssues()): ?>
				<li><a href="javascript:void(0)" id="relate_to_existing_issue_button" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'relate_issue', 'issue_id' => $issue->getID())); ?>');"><?php echo image_tag('action_add_related.png').__('Relate to an existing issue'); ?></a></li>
			<?php endif; ?>
			<?php if ($issue->canEditIssueDetails()): ?>
				<li><a href="javascript:void(0)" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'move_issue', 'issue_id' => $issue->getID())); ?>');"><?php echo image_tag('icon_move.png').__("Move issue to another project"); ?></a></li>
			<?php endif; ?>
		<?php else: ?>
			<?php if ($issue->canAddRelatedIssues() && $tbg_user->canReportIssues($issue->getProject())): ?>
				<li class="disabled"><a href="javascript:void(0);"><?php echo image_tag('icon_new_related_issue.png').__("Create a new related issue"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available at this stage in the workflow'); ?></div></li>
			<?php endif; ?>
			<?php if ($issue->canAddRelatedIssues()): ?>
				<li class="disabled"><a href="javascript:void(0);"><?php echo image_tag('action_add_related.png').__("Relate to an existing issue"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available at this stage in the workflow'); ?></div></li>
			<?php endif; ?>
			<li class="disabled"><a href="javascript:void(0);"><?php echo image_tag('icon_move.png').__("Move issue to another project"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available at this stage in the workflow'); ?></div></li>
		<?php endif; ?>
		<?php if ($issue->canEditAccessPolicy()): ?>
			<li><a href="javascript:void(0)" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_permissions', 'issue_id' => $issue->getID())); ?>');"><?php echo image_tag('icon_unlocked.png').__("Update issue access policy"); ?></a></li>
		<?php endif; ?>
		<?php if ($issue->canDeleteIssue()): ?>
			<li><a href="javascript:void(0)" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Permanently delete this issue?'); ?>', '<?php echo __('Are you sure you wish to delete this issue? It will remain in the database for your records, but will not be accessible via The Bug Genie.'); ?>', {yes: {href: '<?php echo make_url('deleteissue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getId())); ?>' }, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo image_tag('icon_delete.png').__("Permanently delete this issue"); ?></a></li>
		<?php endif; ?>
		<?php if (!isset($times) || $times): ?>
			<?php if ($issue->canEditEstimatedTime()): ?>
				<?php if ($issue->isUpdateable()): ?>
					<li><a href="javascript:void(0);" onclick="$('estimated_time_<?php echo $issue->getID(); ?>_change').toggle();" title="<?php echo __('Change estimate'); ?>"><?php echo image_tag('icon_estimated_time.png').__('Change estimate'); ?></a></li>
				<?php else: ?>
					<li class="disabled"><a href="javascript:void(0);"><?php echo image_tag('icon_estimated_time.png').__("Change estimate"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available at this stage in the workflow'); ?></div></li>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($issue->canEditSpentTime()): ?>
				<li><a href="javascript:void(0);" onclick="$('spent_time_<?php echo $issue->getID(); ?>_change').toggle();" title="<?php echo __('Change time spent'); ?>"><?php echo image_tag('icon_spent_time.png').__('Change time spent'); ?></a></li>
			<?php endif; ?>
		<?php endif; ?>
	<?php else: ?>
		<li class="disabled"><a href="#"><?php echo __('No additional actions available'); ?></a></li>
	<?php endif; ?>
</ul>
<?php if (!isset($times) || $times): ?>
	<?php if ($issue->canEditEstimatedTime()): ?>
		<?php include_component('main/issueestimator', array('issue' => $issue, 'field' => 'estimated_time', 'instant_save' => true)); ?>
	<?php endif; ?>
	<?php if ($issue->canEditSpentTime()): ?>
		<?php include_component('main/issueestimator', array('issue' => $issue, 'field' => 'spent_time', 'instant_save' => true)); ?>
	<?php endif; ?>
<?php endif; ?>