<?php if (!$issue->getProject()->isArchived() && ($tbg_user->hasPermission('caneditissue') || $tbg_user->hasPermission('caneditissuebasic'))): ?>
	<ul id="more_actions_<?php echo $issue->getID(); ?>" style="display: none; position: absolute; width: 300px; top: 0; right: 0; z-index: 1000;" class="simple_list rounded_box white shadowed more_actions_dropdown" onclick="$('more_actions_button').toggleClassName('button-pressed');$('more_actions_<?php echo $issue->getID(); ?>').toggle();">
		<?php if (!isset($multi) || !$multi): ?>
			<li class="header"><?php echo __('Additional actions available'); ?></li>
		<?php endif; ?>
		<?php if ($issue->isOpen()): ?>
			<li id="more_actions_mark_notblocking_link_<?php echo $issue->getID(); ?>"<?php if (!$issue->isBlocking()): ?> style="display: none;"<?php endif; ?>><?php echo javascript_link_tag(image_tag('icon_unblock.png').__("Mark as not blocking the next release"), array('onclick' => "TBG.Issues.toggleBlocking('".make_url('unblock', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getId()))."', ".$issue->getID().");")); ?></li>
			<li id="more_actions_mark_blocking_link_<?php echo $issue->getID(); ?>"<?php if ($issue->isBlocking()): ?> style="display: none;"<?php endif; ?>><?php echo javascript_link_tag(image_tag('icon_block.png').__("Mark as blocking the next release"), array('onclick' => "TBG.Issues.toggleBlocking('".make_url('block', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getId()))."', ".$issue->getID().");")); ?></li>
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
		<?php endif; ?>
		<li><?php echo javascript_link_tag(image_tag('action_add_task.png').__('Add a task to this issue'), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $issue->getProject()->getId(), 'parent_issue_id' => $issue->getID()))."');")); ?></li>
		<li><a href="javascript:void(0)" id="relate_to_existing_issue_button" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'relate_issue', 'issue_id' => $issue->getID())); ?>');"><?php echo image_tag('action_add_related.png').__('Relate to an existing issue'); ?></a></li>
		<li><a href="javascript:void(0)" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'move_issue', 'issue_id' => $issue->getID())); ?>');"><?php echo image_tag('icon_move.png').__("Move issue to another project"); ?></a></li>
		<?php if ($tbg_user->hasPermission('candeleteissues') || $tbg_user->hasPermission('caneditissue')): ?>
			<li><a href="javascript:void(0)" onClick="TBG.Main.Helpers.Dialog.show('<?php echo __('Permanently delete this issue?'); ?>', '<?php echo __('Are you sure you wish to delete this issue? It will remain in the database for your records, but will not be accessible via The Bug Genie.'); ?>', {yes: {href: '<?php echo make_url('deleteissue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getId())); ?>' }, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo image_tag('icon_delete.png').__("Permanently delete this issue"); ?></a></li>
		<?php endif; ?>
	</ul>
<?php endif; ?>