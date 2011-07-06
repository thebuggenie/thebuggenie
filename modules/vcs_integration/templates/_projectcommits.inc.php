<?php
	if (!is_array($commits))
	{
		return; // silently quit
	}
	
	foreach ($commits as $commit)
	{
		include_template('vcs_integration/commitbox', array("projectId" => $selected_project->getID(), "commit" => $commit));
	}