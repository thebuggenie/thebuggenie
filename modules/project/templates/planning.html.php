<?php

	$tbg_response->addBreadcrumb(__('Project information'));
	$tbg_response->addBreadcrumb(__('Planning'));
	$tbg_response->setTitle(__('"%project_name%" project planning', array('%project_name%' => $selected_project->getName())));

?>
<div style="width: 330px; padding: 5px; margin: 5px 3px 5px 3px;">
	<?php foreach ($recent_ideas as $idea): ?>
	<?php endforeach; ?>
</div>