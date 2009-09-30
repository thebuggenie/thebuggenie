<?php

	B2DB::loadNewTable(new B2tScopes());
	B2DB::loadNewTable(new B2tSettings());
	B2DB::loadNewTable(new B2tUserState());
	B2DB::loadNewTable(new B2tGroups());
	B2DB::loadNewTable(new B2tUsers());
	B2DB::loadNewTable(new B2tTeams());
	B2DB::loadNewTable(new B2tTeamMembers());
	B2DB::loadNewTable(new B2tPermissions());
	B2DB::loadNewTable(new B2tModules());
	B2DB::loadNewTable(new B2tModuleSections());
	B2DB::loadNewTable(new B2tModulePermissions());
	B2DB::loadNewTable(new B2tListTypes());
	B2DB::loadNewTable(new B2tIssueTypes());
	B2DB::loadNewTable(new B2tProjects());
	B2DB::loadNewTable(new B2tMilestones());
	B2DB::loadNewTable(new B2tIssues());
	B2DB::loadNewTable(new B2tEditions());
	B2DB::loadNewTable(new B2tBuilds());
	B2DB::loadNewTable(new B2tComponents());
	B2DB::loadNewTable(new B2tIssueAffectsEdition());
	B2DB::loadNewTable(new B2tIssueAffectsBuild());
	B2DB::loadNewTable(new B2tIssueAffectsComponent());
	B2DB::loadNewTable(new B2tIssueRelations());
	B2DB::loadNewTable(new B2tUserIssues());
	B2DB::loadNewTable(new B2tUserAssigns());
	B2DB::loadNewTable(new B2tEditionComponents());
	B2DB::loadNewTable(new B2tVotes());
	B2DB::loadNewTable(new B2tLinks());
	B2DB::loadNewTable(new B2tFiles());
	B2DB::loadNewTable(new B2tNotifications());
	B2DB::loadNewTable(new B2tBuddies());
	B2DB::loadNewTable(new B2tIssueTasks());
	B2DB::loadNewTable(new B2tComments());
	B2DB::loadNewTable(new B2tLog());
	B2DB::loadNewTable(new B2tPermissionsList());

?>