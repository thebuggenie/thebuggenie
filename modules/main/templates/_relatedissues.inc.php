<table style="width: 100%;" cellpadding="0" cellspacing="0" class="issue_affects">
	<thead>
		<tr>
			<th style="width: 16px; text-align: right; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 3px;"></th>
			<th><?php echo __('Name'); ?></th>
			<th><?php echo __('Status'); ?></th>
			<th><?php echo __('Assignee'); ?></th>
		</tr>
	</thead>
	<tbody id="related_parent_issues_inline">
		<?php foreach ($parent_issues as $parent_issue): ?>
			<?php include_template('main/relatedissue', array('issue' => $parent_issue, 'related_issue' => $issue)); ?>
		<?php endforeach; ?>
	</tbody>
	<tr class="no_items" id="no_parent_issues"<?php if (count($parent_issues)): ?> style="display: none;"<?php endif; ?>><td colspan="4"><?php echo __('No other issues depends on this issue'); ?></td></tr>
	<tr>
		<td colspan="4">
			<div style="margin: 5px auto 5px auto; width: 16px; padding: 0;"><?php echo image_tag('up.png', array('style' => 'margin: 0 auto;')); ?></div>
			<div class="rounded_box mediumgrey borderless" id="related_issues_this_issue" style="margin: 5px auto 5px auto; width: 300px;">
				<?php echo __('This issue'); ?>
			</div>
			<div style="margin: 5px auto 5px auto; width: 16px; padding: 0;"><?php echo image_tag('down.png', array('style' => 'margin: 0 auto;')); ?></div>
		</td>
	</tr>
	<tbody id="related_child_issues_inline">
		<?php foreach ($child_issues as $child_issue): ?>
			<?php include_template('main/relatedissue', array('issue' => $child_issue, 'related_issue' => $issue)); ?>
		<?php endforeach; ?>
	</tbody>
	<tr class="no_items" id="no_child_issues"<?php if (count($child_issues)): ?> style="display: none;"<?php endif; ?>><td colspan="4"><?php echo __('This issue does not depend on any other issues'); ?></td></tr>
</table>