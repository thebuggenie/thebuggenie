<?php $canedititem = (($itemtype == 'build' && $issue->canEditAffectedBuilds()) || ($itemtype == 'component' && $issue->canEditAffectedComponents()) || ($itemtype == 'edition' && $issue->canEditAffectedEditions())); ?>
<tr id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>">
	<td><?php echo image_tag('icon_'.$itemtype.'.png', array('alt' => $itemtypename)); ?></td>
	<td style="padding-left: 3px;" onmouseover="$('affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_delete_icon').show();" onmouseout="$('affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_delete_icon').hide();">
		<?php if ($canedititem): ?>
			<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Remove %itemname%?', array('%itemname%' => $item[$itemtype]->getName())); ?>', '<?php echo __('Please confirm that you want to remove this item from the list of items affected by this issue'); ?>', {yes: {click: function() {TBG.Issues.Affected.remove('<?php echo make_url('remove_affected', array('issue_id' => $issue->getID(), 'affected_type' => $itemtype, 'affected_id' => $item['a_id'])).'\', '.'\''.$itemtype.'_'.$item['a_id']; ?>');TBG.Main.Helpers.Dialog.dismiss();}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo image_tag('icon_delete.png', array('id' => 'affected_'.$itemtype.'_'.$item['a_id'].'_delete_icon', 'style' => 'display: none;','alt' => __('Delete'))); ?></a>
		<?php endif; ?>
		<?php echo $item[$itemtype]->getName(); ?>
		<?php if ($itemtype == 'build'): ?>
			<span class="faded_out">(<?php echo $item['build']->getVersionMajor().'.'.$item['build']->getVersionMinor().'.'.$item['build']->getVersionRevision(); ?>)</span>
		<?php endif; ?>
	</td>
	<td style="width: 240px;">
		<div style="position: relative;" id="status_table">
			<table style="table-layout: auto; width: 240px;" cellpadding=0 cellspacing=0>
				<tr>
					<td style="width: 24px;"><div style="border: 1px solid #AAA; background-color: <?php echo ($item['status'] instanceof TBGStatus) ? $item['status']->getColor() : '#FFF'; ?>; font-size: 1px; width: 20px; height: 15px; margin-right: 2px;" id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id'] ?>_status_colour">&nbsp;</div></td>
					<td style="padding-left: 5px;" id="status_content"><?php if ($issue->canEditIssue()): echo image_tag('action_dropdown_small.png', array('id' => 'affected_'.$itemtype.'_'.$item['a_id'].'_status_dropdown', 'class' => 'hover_visible', 'onclick' => "$('affected_".$itemtype.'_'.$item['a_id']."_status_change').toggle();")); endif; ?> <span id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id'] ?>_status_name"><?php echo ($item['status'] instanceof TBGStatus) ? $item['status']->getName() : __('Unknown'); ?></span>
					<div class="rounded_box white shadowed" id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_status_change" style="display: none; width: 280px; position: absolute; bottom: 0; z-index: 10001; margin: 5px 0 20px 0; padding: 5px;">
						<div class="dropdown_header"><?php echo __('Set status'); ?></div>
						<div class="dropdown_content">
							<table cellpadding="0" cellspacing="0">
								<?php foreach ($statuses as $status): ?>
									<?php if (!$status->canUserSet($tbg_user)) continue; ?>
									<tr>
										<td style="width: 16px;"><div style="border: 1px solid #AAA; background-color: <?php echo $status->getColor(); ?>; font-size: 1px; width: 16px; height: 15px; margin-right: 2px;">&nbsp;</div></td>
										<td style="padding-left: 5px;"><a href="javascript:void(0);" onclick="TBG.Issues.Affected.setStatus('<?php echo make_url('status_affected', array('issue_id' => $issue->getID(), 'affected_type' => $itemtype, 'affected_id' => $item['a_id'], 'status_id' => $status->getID())); ?>', '<?php echo $itemtype.'_'.$item['a_id']; ?>');"><?php echo $status->getName(); ?></a></td>
									</tr>
								<?php endforeach; ?>
							</table>
							<div id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_status_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
						</div>
						<div id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_status_error" class="error_message" style="display: none;"></div>
					</div>
					</td>
				</tr>
			</table>
		</div>
	</td>
	<td style="width: 90px; text-align: right; padding-top: 0px; padding-right: 3px; padding-bottom: 0px; padding-left: 0px;">
		<?php $image = image_tag('action_'.(($item['confirmed']) ? 'ok' : 'cancel').'_small.png', array('alt' => ($item['confirmed']) ? __('Yes') : __('No'), 'id' => 'affected_'.$itemtype.'_'.$item['a_id'].'_confirmed_icon')); ?>
		<?php if ($canedititem): ?>
			<a href="javascript:void(0);" onclick="TBG.Issues.Affected.toggleConfirmed('<?php echo make_url('confirm_affected', array('issue_id' => $issue->getID(), 'affected_type' => $itemtype, 'affected_id' => $item['a_id'])); ?>', '<?php echo $itemtype.'_'.$item['a_id']; ?>');"><?php echo $image; ?></a><span id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_confirmed_spinner" style="display: none;"> <?php echo image_tag('spinning_16.gif'); ?></span>
		<?php else: ?>
			<?php echo $image; ?>
		<?php endif; ?>
	</td>
</tr>