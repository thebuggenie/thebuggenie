<?php

	if ($count > 0)
	{
		foreach ($issues as $issue)
		{
			$return_issues[$issue->getID()] = array('id' => $issue->getID(),
													'title' => $issue->getTitle(),
													'state' => $issue->getState(),
													'issue_no' => $issue->getFormattedIssueNo(true, true),
													'posted_by' => ($issue->getPostedBy() instanceof TBGIdentifiable) ? $issue->getPostedBy()->getUsername() : __('Unknown'),
													'assigned_to' => ($issue->getAssignee() instanceof TBGIdentifiable) ? $issue->getAssignee()->getName() : __('Noone'),
													'created_at' => $issue->getPosted(),
													'status' => ($issue->getStatus() instanceof TBGStatus) ? $issue->getStatus()->getName() : __('Unknown'),
													'last_updated' => $issue->getLastUpdatedTime());
		}
	}

	echo json_encode(array('count' => $count, 'issues' => $return_issues));
