<?php

	$can_remove = false;
	if ($mode == 'issue' && $issue->canRemoveAttachments())
		$can_remove = true;
	if ($mode == 'article' && $article->canEdit())
		$can_remove = true;

?>
<?php if ($file instanceof TBGFile): ?>
	<li id="<?php echo $base_id . '_' . $file_id; ?>" class="attached_item">
		<a href="<?php echo make_url('downloadfile', array('id' => $file_id)); ?>" class="downloadlink">
			<?php echo image_tag('icon_download.png'); ?>
			<?php echo ($file->hasDescription()) ? $file->getDescription() : $file->getOriginalFilename(); ?>
		</a>
		<?php if ($can_remove): ?>
			<div class="removelink">
				<?php if ($mode == 'issue'): ?>
					<?php echo javascript_link_tag(image_tag('action_delete.png'), array('id' => $base_id . '_' . $file_id . '_remove_link', 'onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to detach this file?')."', '".__('If you detach this file, it will be deleted. This action cannot be undone. Are you sure you want to remove this file?')."', {yes: {click: function() {TBG.Issues.File.remove('".make_url('issue_detach_file', array('issue_id' => $issue->getID(), 'file_id' => $file_id))."', ".$file_id."); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});")); ?>
				<?php elseif ($mode == 'article'): ?>
					<?php echo javascript_link_tag(image_tag('action_delete.png'), array('id' => $base_id . '_' . $file_id . '_remove_link', 'onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to detach this file?')."', '".__('If you detach this file, it will be deleted. This action cannot be undone. Are you sure you want to remove this file?')."', {yes: {click: function() {TBG.Main.detachFileFromArticle('".make_url('article_detach_file', array('article_name' => $article->getName(), 'file_id' => $file_id))."', ".$file_id.", '".mb_strtolower($article->getName())."'); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});")); ?>
				<?php endif; ?>
				<?php echo image_tag('spinning_16.gif', array('id' => $base_id . '_' . $file_id . '_remove_indicator', 'style' => 'display: none;')); ?>
			</div>
		<?php endif; ?>
		<div class="upload_details">
			<?php echo __('%filename, uploaded %date', array('%filename' => '<span class="filename">'.$file->getOriginalFilename().'</span>', '%date' => tbg_formatTime($file->getUploadedAt(), 23))); ?>
			<?php if ($mode == 'article' && $article->canEdit() && $file->isImage()): ?>
				<br>
				<?php echo __('Use this tag to include this image: [[Image:%filename|thumb|Image description]]', array('%filename' => $file->getOriginalFilename())); ?>
			<?php endif; ?>
		</div>
	</li>
<?php else: ?>
	<li class="faded_out"><?php echo __('Invalid file'); ?></li>
<?php endif; ?>
