<?php

	$can_remove = false;
	if ($mode == 'issue' && $issue->canRemoveAttachments())
		$can_remove = true;
	if ($mode == 'article' && $article->canEdit())
		$can_remove = true;

?>
<?php if ($file instanceof TBGFile): ?>
	<tr id="<?php echo $base_id . '_' . $file_id; ?>" class="attached_item">
		<td class="imgtd"><?php echo link_tag(make_url('downloadfile', array('id' => $file_id)), image_tag('icon_download.png'), array('class' => 'image')); ?></td>
		<td>
			<?php echo link_tag(make_url('showfile', array('id' => $file_id)), (($file->hasDescription()) ? $file->getDescription() : $file->getOriginalFilename())); ?>
			<div class="upload_details">
				<?php echo __('%filename%, uploaded %date%', array('%filename%' => '<span class="filename">'.$file->getOriginalFilename().'</span>', '%date%' => tbg_formatTime($file->getUploadedAt(), 13))); ?>
				<?php if ($mode == 'article' && $article->canEdit() && $file->isImage()): ?>
					<br>
					<?php echo __('Use this tag to include this image: [[Image:%filename%|thumb|Image description]]', array('%filename%' => $file->getOriginalFilename())); ?>
				<?php endif; ?>
			</div>
		</td>
		<?php if ($can_remove): ?>
			<td style="width: 20px;">
				<?php echo javascript_link_tag(image_tag('action_delete.png'), array('class' => 'image', 'id' => $base_id . '_' . $file_id . '_remove_link', 'onclick' => "$('{$base_id}_{$file_id}_remove_confirm').toggle();")); ?>
				<?php echo image_tag('spinning_16.gif', array('id' => $base_id . '_' . $file_id . '_remove_indicator', 'style' => 'display: none;')); ?>
			</td>
		<?php endif; ?>
	</tr>
	<?php if ($can_remove): ?>
		<tr id="<?php echo $base_id . '_' . $file_id; ?>_remove_confirm" style="display: none;" class="remove_container">
			<td colspan="3">
				<div class="rounded_box lightgrey" style="position: relative; clear: both; left: auto; top: auto; margin-bottom: 10px; width: auto;">
					<div class="header_div" style="margin-top: 0;"><?php echo __('Do you really want to detach this file?'); ?></div>
					<div class="content" style="padding: 3px;">
						<?php echo __('If you detach this file, it will be deleted. This action cannot be undone. Are you sure you want to remove this file?'); ?>
						<div style="text-align: right; font-size: 12px;">
							<?php if ($mode == 'issue'): ?>
								<?php echo javascript_link_tag(__('Yes'), array('onclick' => "$('{$base_id}_{$file_id}_remove_confirm').toggle();TBG.Issues.File.remove('".make_url('issue_detach_file', array('issue_id' => $issue->getID(), 'file_id' => $file_id))."', ".$file_id.");")); ?> ::
							<?php elseif ($mode == 'article'): ?>
								<?php echo javascript_link_tag(__('Yes'), array('onclick' => "$('{$base_id}_{$file_id}_remove_confirm').toggle();TBG.Main.detachFileFromArticle('".make_url('article_detach_file', array('article_name' => $article->getName(), 'file_id' => $file_id))."', ".$file_id.", '".mb_strtolower($article->getName())."');")); ?> ::
							<?php endif; ?>
							<?php echo javascript_link_tag('<b>'.__('No').'</b>', array('onclick' => "$('{$base_id}_{$file_id}_remove_confirm').toggle();")); ?>
						</div>
					</div>
				</div>
			</td>
		</tr>
	<?php endif; ?>
<?php else: ?>
	<div class="faded_out"><?php echo __('Invalid file'); ?></div>
<?php endif; ?>