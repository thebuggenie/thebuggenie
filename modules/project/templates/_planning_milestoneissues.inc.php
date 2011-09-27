<?php foreach ($milestone->getIssues() as $issue): ?>
	<?php include_component('project/milestoneissue', array('issue' => $issue)); ?>
<?php endforeach; ?>
