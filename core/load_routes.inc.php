<?php

	$routes = array();
	$routes[] = array('home', '/', 'main', 'index');
	$routes[] = array('dashboard', '/dashboard', 'main', 'dashboard');
	$routes[] = array('get_javascript', '/js/:js_file', 'main', 'index');
	$routes[] = array('login', '/login', 'main', 'login');
	$routes[] = array('register1', '/register1', 'main', 'register1');
	$routes[] = array('register2', '/register2', 'main', 'register2');
	$routes[] = array('activate', '/activate/:user/:key', 'main', 'activate');
	$routes[] = array('logout', '/logout', 'main', 'logout');
	$routes[] = array('getprojectmenustrip', '/getprojectmenustrip/on/page/:page', 'project', 'getMenustrip');
	$routes[] = array('hide_infobox', '/hide/infobox/:key', 'main', 'hideInfobox');
	$routes[] = array('getreportissuefields', '/reportissue/get/fields/for/project/*', 'main', 'reportIssueGetFields');
	$routes[] = array('reportissue', '/reportissue/*', 'main', 'reportIssue');
	$routes[] = array('findthis', '/issues/find/:searchfor/*', 'search', 'findIssues');
	$routes[] = array('search_paginated', '/issues/paginated/*', 'search', 'findIssuesPaginated');
	$routes[] = array('search_add_filter', '/issues/add/filter/*', 'search', 'addFilter');
	$routes[] = array('search', '/issues/*', 'search', 'findIssues');
	$routes[] = array('quicksearch', '/quicksearch/*', 'search', 'quickSearch');
	$routes[] = array('about', '/about', 'main', 'about');
	$routes[] = array('soap', '/soapmeup', 'soap', 'soapHandler');
	$routes[] = array('wsdl', '/thebuggenie.wsdl', 'soap', 'getWSDL');
	$routes[] = array('account_change_password', '/my_account/change/password', 'main', 'accountChangePassword');
	$routes[] = array('account_save_information', '/my_account/save/information', 'main', 'myAccount', array('mode' => 'information'));
	$routes[] = array('account_save_settings', '/my_account/save/settings', 'main', 'myAccount', array('mode' => 'settings'));
	$routes[] = array('account', '/my_account/*', 'main', 'myAccount');
	$routes[] = array('configure', '/configure', 'configuration', 'index', array('section' => 0));
	$routes[] = array('configure_projects', '/configure/projects', 'configuration', 'configureProjects', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_projects_add_project', '/configure/projects/add/new', 'configuration', 'addProject', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_project_settings', '/configure/project/:project_id/settings', 'configuration', 'configureProjectSettings', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_project_delete', '/configure/project/:project_id/delete', 'configuration', 'deleteProject', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_project_developers', '/configure/project/:project_id/developers', 'configuration', 'configureProjectDevelopers', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_project_set_leadby', '/configure/project/:project_id/set/:field/*', 'configuration', 'setProjectLead', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_project_find_assignee', '/configure/project/:project_id/find/assignee/by/*', 'configuration', 'findAssignee', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_project_add_assignee', '/configure/project/:project_id/add/:assignee_type/:assignee_id/*', 'configuration', 'assignToProject', array('config_module' => 'core', 'section' => 10, 'mode' => 'user'));
	$routes[] = array('configure_projects_add_edition', '/configure/project/:project_id/add/edition', 'configuration', 'addEdition', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_projects_add_component', '/configure/project/:project_id/add/component', 'configuration', 'addComponent', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_projects_add_build', '/configure/project/:project_id/add/build', 'configuration', 'addBuild', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_build_action', '/configure/build/:build_id/do/:build_action', 'configuration', 'buildAction', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_edition_add_build', '/configure/project/:project_id/edition/:edition_id/add/build', 'configuration', 'addBuild', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_edition_add_component', '/configure/project/:project_id/edition/:edition_id/add/component/:component_id', 'configuration', 'editEditionComponent', array('config_module' => 'core', 'section' => 10, 'mode' => 'add'));
	$routes[] = array('configure_edition_remove_component', '/configure/project/:project_id/edition/:edition_id/remove/component/:component_id', 'configuration', 'editEditionComponent', array('config_module' => 'core', 'section' => 10, 'mode' => 'remove'));
	$routes[] = array('configure_update_component', '/configure/component/:component_id/update', 'configuration', 'editComponent', array('config_module' => 'core', 'section' => 10, 'mode' => 'update'));
	$routes[] = array('configure_delete_component', '/configure/component/:component_id/delete', 'configuration', 'editComponent', array('config_module' => 'core', 'section' => 10, 'mode' => 'delete'));
	$routes[] = array('configure_project_editions_components', '/configure/project/:project_id/editions_and_components', 'configuration', 'configureProjectEditionsAndComponents', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_project_other', '/configure/project/:project_id/other', 'configuration', 'configureProjectOther', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_project_updateother', '/configure/project/:project_id/update/other', 'configuration', 'configureProjectUpdateOther', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_project_edition', '/configure/project/:project_id/edition/:edition_id/:mode', 'configuration', 'configureProjectEdition', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_settings', '/configure/settings', 'configuration', 'settings', array('config_module' => 'core', 'section' => 12));
	$routes[] = array('configure_scopes', '/configure/scopes', 'configuration', 'index', array('config_module' => 'core', 'section' => 14));
	$routes[] = array('configure_files', '/configure/files', 'configuration', 'index', array('config_module' => 'core', 'section' => 3));
	$routes[] = array('configure_permissions', '/configure/permissions', 'configuration', 'index', array('config_module' => 'core', 'section' => 5));
	$routes[] = array('configure_import', '/configure/import', 'configuration', 'index', array('config_module' => 'core', 'section' => 16));
	$routes[] = array('configure_project_milestones', '/configure/milestones/for/project/:project_id', 'configuration', 'configureProjectMilestones', array('config_module' => 'core', 'section' => 9));
	$routes[] = array('configure_projects_add_milestone', '/configure/project/:project_id/add/milestone', 'configuration', 'addMilestone', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_project_milestone_action', '/configure/project/:project_id/milestone/:milestone_id/do/:milestone_action', 'configuration', 'milestoneAction', array('config_module' => 'core', 'section' => 10));
	$routes[] = array('configure_custom_types', '/configure/custom_types', 'configuration', 'index', array('config_module' => 'core', 'section' => 4, 'subsection' => 9));
	$routes[] = array('configure_issue_types', '/configure/issue_types', 'configuration', 'index', array('config_module' => 'core', 'section' => 4, 'subsection' => 8));
	$routes[] = array('configure_resolution_types', '/configure/resolution_types', 'configuration', 'index', array('config_module' => 'core', 'section' => 4, 'subsection' => 7));
	$routes[] = array('configure_priority_levels', '/configure/priority_levels', 'configuration', 'index', array('config_module' => 'core', 'section' => 4, 'subsection' => 6));
	$routes[] = array('configure_categories', '/configure/categories', 'configuration', 'index', array('config_module' => 'core', 'section' => 4, 'subsection' => 5));
	$routes[] = array('configure_reproduction_levels', '/configure/reproduction_levels', 'configuration', 'index', array('config_module' => 'core', 'section' => 4, 'subsection' => 4));
	$routes[] = array('configure_status_types', '/configure/status_types', 'configuration', 'index', array('config_module' => 'core', 'section' => 4, 'subsection' => 3));
	$routes[] = array('configure_severity_levels', '/configure/severity_levels', 'configuration', 'index', array('config_module' => 'core', 'section' => 4, 'subsection' => 2));
	$routes[] = array('configure_user_states', '/configure/user_states', 'configuration', 'index', array('config_module' => 'core', 'section' => 4, 'subsection' => 1));
	$routes[] = array('configure_users', '/configure/users', 'configuration', 'index', array('config_module' => 'core', 'section' => 2));
	$routes[] = array('configure_teams_groups', '/configure/teams_and_groups', 'configuration', 'index', array('config_module' => 'core', 'section' => 1));
	$routes[] = array('configure_modules', '/configure/modules', 'configuration', 'configureModules', array('config_module' => 'core', 'section' => 15));
	$routes[] = array('configure_disable_module', '/configure/modules/disable/:module_key', 'configuration', 'moduleAction', array('config_module' => 'core', 'section' => 15, 'mode' => 'disable'));
	$routes[] = array('configure_enable_module', '/configure/modules/enable/:module_key', 'configuration', 'moduleAction', array('config_module' => 'core', 'section' => 15, 'mode' => 'enable'));
	$routes[] = array('configure_uninstall_module', '/configure/modules/uninstall/:module_key', 'configuration', 'moduleAction', array('config_module' => 'core', 'section' => 15, 'mode' => 'uninstall'));
	$routes[] = array('configure_install_module', '/configure/modules/install', 'configuration', 'moduleAction', array('config_module' => 'core', 'section' => 15, 'mode' => 'install'));
	$routes[] = array('configure_module', '/configure/module/:config_module', 'configuration', 'configureModule', array('config_module' => 'core', 'section' => 15));
	$routes[] = array('configure_get_permissions', '/configure/permissions/:permissions_type/for/:permissions_target_type/:permissions_target', 'configuration', 'getPermissions', array('config_module' => 'core', 'section' => 5));
	
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
