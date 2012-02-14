<?php foreach ($issue->getChildIssues() as $child_issue): ?>
	<?php if ($child_issue->hasAccess()): ?>
		<?php include_template('main/relatedissue', array('issue' => $child_issue)); ?>
	<?php endif; ?>
<?php endforeach; ?>