<?php

	$routes[] = array('viewissue', '/:project_key/issue/:issue_no', 'main', 'viewIssue');
	$routes[] = array('viewissue_find_issue', '/:project_key/issue/:issue_id/find/:type', 'main', 'findIssue', array('format' => 'json'));
	$routes[] = array('viewissue_relate_issues', '/:project_key/issue/:issue_id/relate', 'main', 'relateIssues', array('format' => 'json'));
	$routes[] = array('saveissue', '/:project_key/issue/:issue_no', 'main', 'viewIssue');
	$routes[] = array('closeissue', '/:project_key/issue/:issue_id/close', 'main', 'closeIssue');
	$routes[] = array('markasduplicate', '/:project_key/issue/:issue_id/markasduplicate', 'main', 'markAsDuplicate');
	$routes[] = array('notduplicate', '/:project_key/issue/:issue_id/notduplicate', 'main', 'markAsNotDuplicate');
	$routes[] = array('openissue', '/:project_key/issue/:issue_id/open', 'main', 'reopenIssue');
	$routes[] = array('block', '/:project_key/issue/:issue_id/block', 'main', 'markAsBlocker');
	$routes[] = array('unblock', '/:project_key/issue/:issue_id/unblock', 'main', 'markAsNotBlocker');
	$routes[] = array('issue_setfield', '/:project_key/issue/:issue_id/set/:field/*', 'main', 'issueSetField');
	$routes[] = array('issue_revertfield', '/:project_key/issue/:issue_id/revert/:field', 'main', 'issueRevertField');
	$routes[] = array('issue_startworking', '/:project_key/issue/:issue_id/startworking', 'main', 'issueStartWorking');
	$routes[] = array('issue_stopworking', '/:project_key/issue/:issue_id/stopworking/*', 'main', 'issueStopWorking');
	$routes[] = array('main_find_identifiable', '/find/identifiable/by/*', 'main', 'findIdentifiable');
	$routes[] = array('toggle_favourite_issue', '/toggle_favourite_issue/:issue_id', 'main', 'toggleFavouriteIssue', array('format' => 'json'));
	$routes[] = array('project_reportissue', '/:project_key/reportissue/*', 'main', 'reportIssue');
	$routes[] = array('project_planning', '/:project_key/planning', 'project', 'planning');
	$routes[] = array('project_scrum', '/:project_key/scrum', 'project', 'scrum');
	$routes[] = array('project_scrum_sprint_details', '/:project_key/scrum/sprint/details/*', 'project', 'scrumShowDetails');
	$routes[] = array('project_scrum_release_burndown', '/:project_key/scrum/burndown/release', 'project', 'scrumShowBurndown', array('mode' => 'release'));
	$routes[] = array('project_scrum_sprint_burndown_image', '/:project_key/scrum/burndown/sprint/image/*', 'project', 'scrumShowBurndownImage', array('mode' => 'sprint'));
	$routes[] = array('project_scrum_release_burndown_image', '/:project_key/scrum/burndown/release/image/*', 'project', 'scrumShowBurndownImage', array('mode' => 'release'));
	$routes[] = array('project_scrum_assign_story', '/:project_key/scrum/assign/story', 'project', 'scrumAssignStory');
	$routes[] = array('project_scrum_add_sprint', '/:project_key/scrum/add/sprint', 'project', 'scrumAddSprint');
	$routes[] = array('project_scrum_story_setcolor', '/:project_key/scrum/set/color/for/story/:story_id', 'project', 'scrumSetStoryDetail', array('detail' => 'color'));
	$routes[] = array('project_scrum_story_setestimates', '/:project_key/scrum/set/estimates/for/story/:story_id', 'project', 'scrumSetStoryDetail', array('detail' => 'estimates'));
	$routes[] = array('project_scrum_story_addtask', '/:project_key/scrum/add/task/for/story/:story_id/*', 'project', 'scrumAddTask');
	$routes[] = array('project_search_paginated', '/:project_key/issues/paginated/*', 'search', 'findIssuesPaginated');
	$routes[] = array('project_search_add_filter', '/:project_key/issues/add/filter/*', 'search', 'addFilter');
	$routes[] = array('project_issues', '/:project_key/issues/*', 'search', 'findIssues');
	$routes[] = array('project_findthis', '/:project_key/issues/find/:searchfor/*', 'search', 'findIssues');
	$routes[] = array('project_quicksearch', '/:project_key/quicksearch/*', 'search', 'quickSearch');
	$routes[] = array('project_team', '/:project_key/team', 'project', 'team');
	$routes[] = array('project_statistics', '/:project_key/statistics', 'project', 'statistics');
	$routes[] = array('project_statistics_last_30', '/:project_key/statistics/last/30', 'project', 'statisticsLast30');
	$routes[] = array('project_statistics_imagesets', '/:project_key/statistics/imagesets/:set', 'project', 'statisticsImagesets');
	$routes[] = array('project_statistics_image', '/:project_key/statistics/image/:key/:mode/:image_number/*', 'project', 'statisticsGetImage');
	$routes[] = array('project_timeline', '/:project_key/timeline/*', 'project', 'timeline');
	$routes[] = array('project_files', '/:project_key/files', 'project', 'files');
	$routes[] = array('project_roadmap', '/:project_key/roadmap', 'project', 'roadmap');
	$routes[] = array('project_dashboard', '/:project_key', 'project', 'dashboard');

	foreach ($routes as $route)
	{
		if (isset($route[4]) && !empty($route[4]))
		{
			TBGContext::getRouting()->addRoute($route[0], $route[1], $route[2], $route[3], $route[4]);
		}
		else
		{
			TBGContext::getRouting()->addRoute($route[0], $route[1], $route[2], $route[3]);
		}
	}

