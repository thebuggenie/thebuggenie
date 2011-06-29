<tr id="viewissue_links_<?php echo $link_id; ?>" class="attached_item">
	<td class="imgtd"><?php echo image_tag('icon_link.png'); ?></td>
	<td style="font-size: 13px; padding: 3px;">
		<?php echo link_tag($link['url'], (($link['description'] != '') ? $link['description'] : $link['url']), array('title' => $link['url'])); ?><br>
	</td>
	<?php if ($issue->canRemoveAttachments()): ?>
		<td style="width: 20px;">
			<?php echo javascript_link_tag(image_tag('action_delete.png'), array('class' => 'image', 'id' => 'viewissue_links_' . $link_id . '_remove_link', 'onclick' => "$('viewissue_links_{$link_id}_remove_confirm').toggle();")); ?>
			<?php echo image_tag('spinning_16.gif', array('id' => 'viewissue_links_' . $link_id . '_remove_indicator', 'style' => 'display: none;')); ?>
		</td>
	<?php endif; ?>
</tr>
<?php if ($issue->canRemoveAttachments()): ?>
	<tr id="viewissue_links_<?php echo $link_id; ?>_remove_confirm" style="display: none;">
		<td colspan="3">
			<div class="rounded_box lightgrey" style="position: relative; clear: both; left: auto; top: auto; margin-bottom: 10px; width: auto;">
				<div class="header_div" style="margin-top: 0;"><?php echo __('Do you really want to remove this link?'); ?></div>
				<div class="content" style="padding: 3px;">
					<?php echo __('This aciton cannot be reversed. Are you sure you want to do this?'); ?>
					<div style="text-align: right; font-size: 12px;">
						<?php echo javascript_link_tag(__('Yes'), array('onclick' => "$('viewissue_links_{$link_id}_remove_confirm').toggle();TBG.Issues.Link.remove('".make_url('issue_remove_link', array('issue_id' => $issue->getID(), 'link_id' => $link_id))."', ".$link_id.");")); ?> ::
						<?php echo javascript_link_tag('<b>'.__('No').'</b>', array('onclick' => "$('viewissue_links_{$link_id}_remove_confirm').toggle();")); ?>
					</div>
				</div>
			</div>
		</td>
	</tr>
<?php endif; ?>