<table cellpadding=0 cellspacing=0 class="<?php echo $key; ?>_percentage" style="margin: 5px 0 10px 0; width: 100%;">
	<?php foreach ($items as $item_id => $item): ?>
		<tr class="hover_highlight">
			<td style="font-weight: normal; font-size: 13px; padding-left: 3px;"><?php echo (is_object($item)) ? $item->getName() : $item; ?></td>
			<td style="text-align: right; font-weight: bold; padding-right: 5px; vertical-align: middle;"><?php echo $counts[$item_id]['open']; ?></td>
			<td style="width: 40%; vertical-align: middle;"><?php include_template('main/percentbar', array('percent' => $counts[$item_id]['percentage'], 'height' => 14)); ?></td>
			<td style="text-align: right; font-weight: normal; font-size: 11px; padding-left: 5px; vertical-align: middle;">&nbsp;<?php echo (int) $counts[$item_id]['percentage']; ?>%</td>
		</tr>
	<?php endforeach; ?>
	<tr class="hover_highlight">
		<td style="font-weight: normal; font-size: 13px; padding-left: 3px;" class="faded_out"><?php echo __('Not set'); ?></td>
		<td style="text-align: right; font-weight: bold; padding-right: 5px; vertical-align: middle;" class="faded_out"><?php echo $counts[0]['open']; ?></td>
		<td style="width: 40%; vertical-align: middle;" class="faded_out"><?php include_template('main/percentbar', array('percent' => $counts[0]['percentage'], 'height' => 14)); ?></td>
		<td style="text-align: right; font-weight: normal; font-size: 11px; padding-left: 5px; vertical-align: middle;" class="faded_out">&nbsp;<?php echo (int) $counts[0]['percentage']; ?>%</td>
	</tr>
</table>
<?php echo link_tag(make_url('project_statistics', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Show more statistics'), array('class' => 'button button-silver', 'title' => __('More statistics'))); ?>
<?php echo link_tag(make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'search' => true, 'filters[state]' => array('operator' => '=', 'value' => TBGIssue::STATE_OPEN), 'groupby' => $key, 'grouporder' => 'desc')), __('Show details'), array('class' => 'button button-silver', 'title' => __('Show more issues'))); ?>
<br style="clear: both;">
