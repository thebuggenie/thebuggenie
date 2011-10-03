<?php foreach ($milestone->getIssues() as $issue): ?>
	<?php include_component('project/milestoneissue', array('issue' => $issue)); ?>
<?php endforeach; ?>
<?php if ($milestone->countIssues()): ?>
	<tr class="milestone_summary">
		<td colspan="4">
		</td>
		<td colspan="5" class="milestoneupdateable">
			<?php echo image_tag('spinning_20.gif', array('style' => 'margin: 1px 5px -6px 0; display: none;', 'id' => 'milestone_'.$milestone->getID().'_update_issues_indicator')); ?>
			<input type="submit" class="button button-silver" value="<?php echo __('Update issue details'); ?>">
		</td>
	</tr>
	<tr>
		<td colspan="9">&nbsp;</td>
	</tr>
<?php endif; ?>
