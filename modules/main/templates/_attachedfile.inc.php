<tr>
	<td class="imgtd" style="width: 22px; text-align: center; vertical-align: middle;"><?php echo link_tag(make_url('downloadfile', array('id' => $file_id)), image_tag('icon_download.png'), array('class' => 'image')); ?></td>
	<td style="font-size: 13px; padding: 3px;">
		<?php echo link_tag(make_url('showfile', array('id' => $file_id)), (($file['description'] != '') ? $file['description'] : $file['filename'])); ?><br>
		<span class="faded_medium" style="font-size: 11px;"><?php echo bugs_formatTime($file['timestamp'], 13); ?></span>
	</td>
	<?php if ($issue->canRemoveAttachments()): ?>
		<td style="width: 20px;"><a href="#" class="image"><?php echo image_tag('action_cancel_small.png'); ?></a></td>
	<?php endif; ?>
</tr>