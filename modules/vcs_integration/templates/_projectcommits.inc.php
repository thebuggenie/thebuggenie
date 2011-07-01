<?php

	$web_path = TBGContext::getModule('vcs_integration')->getSetting('web_path_' . $selected_project->getID());
	$web_repo = TBGContext::getModule('vcs_integration')->getSetting('web_repo_' . $selected_project->getID());

	if (!is_array($commits))
	{
		return; // silently quit
	}
	
	foreach ($commits as $revno => $entry)
	{
		$revision = $revno;
		/* Build correct URLs */
		switch (TBGContext::getModule('vcs_integration')->getSetting('web_type_' . $selected_project->getID()))
		{
			case 'viewvc':
				$link_rev = $web_path . '/' . '?root=' . $web_repo . '&amp;view=rev&amp;revision=' . $revision;
				break;
			case 'viewvc_repo':
				$link_rev = $web_path . '/' . '?view=rev&amp;revision=' . $revision;
				break;
			case 'websvn':
				$link_rev = $web_path . '/revision.php?repname=' . $web_repo . '&amp;isdir=1&amp;rev=' . $revision;
				break;
			case 'websvn_mv':
				$link_rev = $web_path . '/' . '?repname=' . $web_repo . '&amp;op=log&isdir=1&amp;rev=' . $revision;
				break;
			case 'loggerhead':
				$link_rev = $web_path . '/' . $web_repo . '/revision/' . $revision;
				break;
			case 'gitweb':
				$link_rev = $web_path . '/' . '?p=' . $web_repo . ';a=commitdiff;h=' . $revision;
				break;
			case 'cgit':
				$link_rev = $web_path . '/' . $web_repo . '/commit/?id=' . $revision;
				break;
			case 'hgweb':
				$link_rev = $web_path . '/' . $web_repo . '/rev/' . $revision;
				break;
			case 'github':
				$link_rev = 'http://github.com/' . $web_repo . '/commit/' . $revision;
				break;
		}
		
		/* Now we have everything, render the template */
		include_template('vcs_integration/commitbox', array("projectId" => $selected_project->getID(), "issue_no" => $entry[0][4], "id" => $entry[0][0], "revision" => $revision, "author" => $entry[0][1], "date" => $entry[0][2], "log" => $entry[0][3], "files" => $entry[1], "projectmode" => true));
	}