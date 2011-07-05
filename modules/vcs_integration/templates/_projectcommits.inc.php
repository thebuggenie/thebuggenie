<?php

	$web_path = TBGContext::getModule('vcs_integration')->getSetting('web_path_' . $selected_project->getID());
	$web_repo = TBGContext::getModule('vcs_integration')->getSetting('web_repo_' . $selected_project->getID());

	if (!is_array($commits))
	{
		return; // silently quit
	}
	
	foreach ($commits as $commit)
	{
		/* Build correct URLs */
		switch (TBGContext::getModule('vcs_integration')->getSetting('web_type_' . $selected_project->getID()))
		{
			case 'viewvc':
				$link_rev = $web_path . '/' . '?root=' . $web_repo . '&amp;view=rev&amp;revision=' . $commit->getRevision();
				break;
			case 'viewvc_repo':
				$link_rev = $web_path . '/' . '?view=rev&amp;revision=' . $commit->getRevision();
				break;
			case 'websvn':
				$link_rev = $web_path . '/revision.php?repname=' . $web_repo . '&amp;isdir=1&amp;rev=' . $commit->getRevision();
				break;
			case 'websvn_mv':
				$link_rev = $web_path . '/' . '?repname=' . $web_repo . '&amp;op=log&isdir=1&amp;rev=' . $commit->getRevision();
				break;
			case 'loggerhead':
				$link_rev = $web_path . '/' . $web_repo . '/revision/' . $commit->getRevision();
				break;
			case 'gitweb':
				$link_rev = $web_path . '/' . '?p=' . $web_repo . ';a=commitdiff;h=' . $commit->getRevision();
				break;
			case 'cgit':
				$link_rev = $web_path . '/' . $web_repo . '/commit/?id=' . $commit->getRevision();
				break;
			case 'hgweb':
				$link_rev = $web_path . '/' . $web_repo . '/rev/' . $commit->getRevision();
				break;
			case 'github':
				$link_rev = 'http://github.com/' . $web_repo . '/commit/' . $commit->getRevision();
				break;
		}
		
		/* Now we have everything, render the template */
		include_template('vcs_integration/commitbox', array("projectId" => $selected_project->getID(), "commit" => $commit));
	}