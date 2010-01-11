<tr id="<?php echo $base_id . '_' . $file_id; ?>">
	<td class="imgtd" style="width: 22px; text-align: center; vertical-align: middle;"><?php echo link_tag(make_url('downloadfile', array('id' => $file_id)), image_tag('icon_download.png'), array('class' => 'image')); ?></td>
	<td style="font-size: 13px; padding: 3px;">
		<?php echo link_tag(make_url('showfile', array('id' => $file_id)), (($file['description'] != '') ? $file['description'] : $file['filename'])); ?><br>
		<span class="faded_medium" style="font-size: 11px;"><?php echo tbg_formatTime($file['timestamp'], 13); ?></span>
	</td>
	<?php if ($mode == 'issue' && $issue->canRemoveAttachments()): ?>
		<td style="width: 20px;">
			<?php echo javascript_link_tag(image_tag('action_cancel_small.png'), array('class' => 'image', 'id' => $base_id . '_' . $file_id . '_remove_link', 'onclick' => "detachFileFromIssue('".make_url('issue_detach_file', array('issue_id' => $issue->getID(), 'file_id' => $file_id))."', ".$file_id.");")); ?>
			<?php echo image_tag('spinning_16.gif', array('id' => $base_id . '_' . $file_id . '_remove_indicator', 'style' => 'display: none;')); ?>
		</td>
	<?php endif; ?>
</tr>