	<tr id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>">
		<?php if ($itemtype == 'component'): $itemtype2 = 'components'; else: $itemtype2 = $itemtype; endif; ?>
		<td><?php echo image_tag('icon_'.$itemtype2.'.png', array('alt' => $itemtypename)); ?></td>
		<td style="padding-left: 3px;" onmouseover="$('affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_delete_icon').show();" onmouseout="$('affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_delete_icon').hide();"><?php if ($issue->canEditIssue()): echo '<a href="javascript:void(0);" onClick="$(\'affected_'.$itemtype.'_'.$item['a_id'].'_delete\').toggle()">'.image_tag('icon_delete.png', array('id' => 'affected_'.$itemtype.'_'.$item['a_id'].'_delete_icon', 'style' => 'display: none;','alt' => __('Delete'))).'</a> '; endif; ?><?php echo $item[$itemtype]->getName(); ?><?php if ($itemtype == 'build'): ?> <span class="faded_out">(<?php print $item['build']->getVersionMajor(); ?>.<?php print $item['build']->getVersionMinor(); ?>.<?php print $item['build']->getVersionRevision(); ?>)</span><?php endif; ?></td><td style="width: 240px">
		<table style="table-layout: auto; width: 240px" cellpadding=0 cellspacing=0 id="status_table">
			<tr>
				<td style="width: 24px;"><div style="border: 1px solid #AAA; background-color: <?php echo $item['status']->getColor(); ?>; font-size: 1px; width: 20px; height: 15px; margin-right: 2px;" id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id'] ?>_status_colour">&nbsp;</div></td>
				<td style="padding-left: 5px;" id="status_content" onmouseover="$('affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_status_dropdown').show();" onmouseout="$('affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_status_dropdown').hide();"><?php if ($issue->canEditIssue()): echo image_tag('action_dropdown_small.png', array('id' => 'affected_'.$itemtype.'_'.$item['a_id'].'_status_dropdown', 'style' => 'display: none;', 'onClick' => "$('affected_".$itemtype.'_'.$item['a_id']."_status_change').toggle();")); endif; ?> <span id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id'] ?>_status_name"><?php echo $item['status']->getName(); ?></span>

				<div class="rounded_box white shadowed" id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_status_change" style="display: none; width: 280px; position: absolute; z-index: 10001; margin: 5px 0 5px 0; padding: 5px;">
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
		</td><td style="width: 90px; text-align: right; padding-top: 0px; padding-right: 3px; padding-bottom: 0px; padding-left: 0px;"><?php
		if ($item['confirmed']): $image = image_tag('action_ok_small.png', array('alt' => __('Yes'), 'id' => 'affected_'.$itemtype.'_'.$item['a_id'].'_confirmed_icon')); else: $image = image_tag('action_cancel_small.png', array('alt' => __('No'), 'id' => 'affected_'.$itemtype.'_'.$item['a_id'].'_confirmed_icon')); endif;
		if ($issue->canEditIssue()): $url = "<a href=\"javascript:void(0);\" onClick=\"TBG.Issues.Affected.toggleConfirmed('".make_url('confirm_affected', array('issue_id' => $issue->getID(), 'affected_type' => $itemtype, 'affected_id' => $item['a_id']))."', '".$itemtype.'_'.$item['a_id']."');\">".$image."</a>"; else: $url = $image; endif; echo $url; ?><span id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_confirmed_spinner" style="display: none;"> <?php echo image_tag('spinning_16.gif'); ?></span></td>
	</tr>
	<?php if ($issue->canEditIssue()): ?>
	<tr id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_delete" style="display: none;">
		<td colspan="4">
			<div class="rounded_box lightgrey">
				<b><?php echo __('Are you sure you want to remove \'%item%\'?', array('%item%' => $item[$itemtype]->getName())); ?></b><br>
				<?php echo __('This will remove it from the list of items affected by this issue. It can be readded at any time.'); ?><br>
				<a href="javascript:void(0);" onClick="TBG.Issues.Affected.remove('<?php echo make_url('remove_affected', array('issue_id' => $issue->getID(), 'affected_type' => $itemtype, 'affected_id' => $item['a_id'])).'\', '.'\''.$itemtype.'_'.$item['a_id']; ?>')"><?php echo __('Yes'); ?></a> - <a href="javascript:void(0);" onClick="$('affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_delete').hide()"><?php echo __('No'); ?></a><span id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_delete_spinner" style="display: none;"> <?php echo image_tag('spinning_16.gif'); ?></span>
			</div>
		</td>
	</tr>
	<?php endif; ?>
