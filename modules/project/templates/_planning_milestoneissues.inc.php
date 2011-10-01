<?php foreach ($milestone->getIssues() as $issue): ?>
	<?php include_component('project/milestoneissue', array('issue' => $issue)); ?>
<?php endforeach; ?>
<tr class="milestone_summary">
	<td colspan="8">
		<input type="submit" class="button button-silver" value="<?php echo __('Update issue details'); ?>">
	</td>
</tr>
