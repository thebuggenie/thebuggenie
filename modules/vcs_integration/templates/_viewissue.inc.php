<div id="tab_vcs_checkins_pane" style="padding-top: 0; margin: 0 5px 0 5px; display: none;">
<br>
<?php
	$crit = new B2DBCriteria();
	$crit->addWhere(TBGVCSIntegrationTable::ISSUE_NO, $theIssue->getID());
	$crit->addOrderBy(TBGVCSIntegrationTable::DATE, B2DBCriteria::SORT_DESC);
	$results = B2DB::getTable('TBGVCSIntegrationTable')->doSelect($crit);
	
	$web_path = TBGContext::getModule('vcs_integration')->getSetting('web_path_' . $theIssue->getProject()->getID());
	$web_repo = TBGContext::getModule('vcs_integration')->getSetting('web_repo_' . $theIssue->getProject()->getID());
	
	$data = array();

	if (!is_object($results))
	{
		echo '<div class="no_items">' . __('There are no code checkins for this issue') . '</div>';
	}
	else
	{
		echo '<div class="rounded_box mediumgrey borderless cut_bottom"></div>';
		/* Build revision details */
		while ($results->next())
		{
			$file = array($results->get(TBGVCSIntegrationTable::FILE_NAME), $results->get(TBGVCSIntegrationTable::ACTION), $results->get(TBGVCSIntegrationTable::NEW_REV), $results->get(TBGVCSIntegrationTable::OLD_REV));
			if (array_key_exists($results->get(TBGVCSIntegrationTable::NEW_REV), $data))
			{
				$data[$results->get(TBGVCSIntegrationTable::NEW_REV)][1][] = $file;
			}
			else
			{
				// one array for revision details, other for files
				$data[$results->get(TBGVCSIntegrationTable::NEW_REV)] = array(array(), array());
				$data[$results->get(TBGVCSIntegrationTable::NEW_REV)][0] = array($results->get(TBGVCSIntegrationTable::ID), $results->get(TBGVCSIntegrationTable::AUTHOR), $results->get(TBGVCSIntegrationTable::DATE), $results->get(TBGVCSIntegrationTable::LOG));
				$data[$results->get(TBGVCSIntegrationTable::NEW_REV)][1][] = $file;
			}
		}
		
		/* Now produce each box */
		foreach ($data as $revno => $entry)
		{
			$revision = $revno;
			/* Build correct URLs */
			switch (TBGContext::getModule('vcs_integration')->getSetting('web_type_' . $theIssue->getProject()->getID()))
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
			include_template('vcs_integration/commitbox', array("projectId" => $theIssue->getProject()->getID(), "id" => $entry[0][0], "revision" => $revision, "author" => $entry[0][1], "date" => $entry[0][2], "log" => $entry[0][3], "files" => $entry[1]));
		}
		echo '<div class="rounded_box mediumgrey borderless cut_top"></div>';
	}

?>
</div>