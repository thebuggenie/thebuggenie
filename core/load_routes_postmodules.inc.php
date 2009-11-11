<?php

	$routes[] = array('viewissue', '/:project_key/issue/:issue_no', 'main', 'viewIssue');
	$routes[] = array('saveissue', '/:project_key/issue/:issue_no', 'main', 'viewIssue');
	$routes[] = array('closeissue', '/:project_key/issue/:issue_id/close', 'main', 'closeIssue');
	$routes[] = array('openissue', '/:project_key/issue/:issue_id/open', 'main', 'reopenIssue');
	$routes[] = array('issue_setfield', '/:project_key/issue/:issue_id/set/:field/*', 'main', 'issueSetField');
	$routes[] = array('issue_revertfield', '/:project_key/issue/:issue_id/revert/:field', 'main', 'issueRevertField');
	$routes[] = array('issue_startworking', '/:project_key/issue/:issue_id/startworking', 'main', 'issueStartWorking');
	$routes[] = array('issue_stopworking', '/:project_key/issue/:issue_id/stopworking/*', 'main', 'issueStopWorking');
	$routes[] = array('main_find_identifiable', '/find/identifiable/by/*', 'main', 'findIdentifiable');
	$routes[] = array('toggle_favourite_issue', '/toggle_favourite_issue/:issue_id', 'main', 'toggleFavouriteIssue');
	$routes[] = array('project_reportissue', '/:project_key/reportissue/*', 'main', 'reportIssue');
	$routes[] = array('project_planning', '/:project_key/planning', 'project', 'planning');
	$routes[] = array('project_scrum', '/:project_key/scrum', 'project', 'scrum');
	$routes[] = array('project_scrum_assign_story', '/:project_key/scrum/assign/story', 'project', 'scrumAssignStory');
	$routes[] = array('project_scrum_add_sprint', '/:project_key/scrum/add/sprint', 'project', 'scrumAddSprint');
	$routes[] = array('project_scrum_story_setcolor', '/:project_key/scrum/set/color/for/story/:story_id', 'project', 'scrumSetStoryDetail', array('detail' => 'color'));
	$routes[] = array('project_scrum_story_setestimates', '/:project_key/scrum/set/estimates/for/story/:story_id', 'project', 'scrumSetStoryDetail', array('detail' => 'estimates'));
	$routes[] = array('project_issues', '/:project_key/issues', 'project', 'issues');
	$routes[] = array('project_team', '/:project_key/team', 'project', 'team');
	$routes[] = array('project_statistics', '/:project_key/statistics', 'project', 'statistics');
	$routes[] = array('project_dashboard', '/:project_key', 'project', 'dashboard');

	foreach ($routes as $route)
	{
		if (isset($route[4]) && !empty($route[4]))
		{
			BUGScontext::getRouting()->addRoute($route[0], $route[1], $route[2], $route[3], $route[4]);
		}
		else
		{
			BUGScontext::getRouting()->addRoute($route[0], $route[1], $route[2], $route[3]);
		}
	}

?>
