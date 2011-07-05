<?php

	$web_path = TBGContext::getModule('vcs_integration')->getSetting('web_path_' . $selected_project->getID());
	$web_repo = TBGContext::getModule('vcs_integration')->getSetting('web_repo_' . $selected_project->getID());

	if (!is_array($commits))
	{
		return; // silently quit
	}
	
	foreach ($commits as $commit)
	{
		include_template('vcs_integration/commitbox', array("projectId" => $selected_project->getID(), "commit" => $commit));
	}