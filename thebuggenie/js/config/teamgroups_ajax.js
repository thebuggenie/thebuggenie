function showUsers(url, findstring)
{
	new Ajax.Request(url, {
		asynchronous: true,
		method: "post",
		parameters: '&findstring=' + findstring,
		onLoading: function (transport) {
			$('find_users_indicator').show();
		},
		onSuccess: function (transport) {
			$('find_users_indicator').hide();
			$('users_results').update(transport.responseText);
		},
		onComplete: function (transport) {
			$('find_users_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				TBG.Main.Helpers.Message.error(json.error);
			}
		},
		onFailure: function (transport) {
			$('find_users_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				TBG.Main.Helpers.Message.error(json.error);
			}
			else
			{
				TBG.Main.Helpers.Message.error(transport.responseText);
			}
		}
	});
}

function createUser(url)
{
	_postFormWithJSONFeedback(url, 'createuser_form', 'find_users_indicator', '', 'users_results');
	return true;
}

function loadUserEditForm(url, uid)
{
	new Ajax.Request(url, {
		asynchronous: true,
		method: "post",
		onLoading: function (transport) {
			$('user_' + uid + '_edit_spinning').show();
		},
		onSuccess: function (transport) {
			$('user_' + uid + '_edit_spinning').hide();
			var json = transport.responseJSON;
			$('user_' + uid + '_edit_tr').show();
			$('user_' + uid + '_edit_td').update(json.content);
		}
	});
}

function createGroup(url)
{
	_postFormWithJSONFeedback(url, 'create_group_form', 'create_group_indicator', '', 'groupconfig_list', true);
	return true;
}

function deleteGroup(url, group_id)
{
	new Ajax.Request(url, {
		asynchronous: true,
		method: "post",
		onLoading: function (transport) {
			$('delete_group_' + group_id + '_indicator').show();
		},
		onSuccess: function (transport) {
			$('delete_group_' + group_id + '_indicator').hide();
			$('groupbox_' + group_id).remove();
			var json = transport.responseJSON;
			if (json && (!json.failed || json.success) && json.message)
			{
				TBG.Main.Helpers.Message.success(json.message);
			}
		},
		onComplete: function (transport) {
			$('delete_group_' + group_id + '_indicator').hide();
			var json = transport.responseJSON;
			if (json && (!json.failed || json.success) && json.message)
			{
				TBG.Main.Helpers.Message.success(json.message);
			}
			if (json && (json.failed || json.error))
			{
				TBG.Main.Helpers.Message.error(json.error);
			}
		},
		onFailure: function (transport) {
			$('delete_group_' + group_id + '_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				TBG.Main.Helpers.Message.error(json.error);
			}
			else
			{
				TBG.Main.Helpers.Message.error(transport.responseText);
			}
		}
	});
}

function updateUserLinks(json)
{
	if ($('current_user_num_count'))
	{
		$('current_user_num_count').update(json.total_count);
	}
	if (json.more_available)
	{
		$('adduser_div').show();
	}
	else
	{
		$('adduser_div').hide();
	}
	updateTeamAndGroupDetailsFromJSON(json);
}

function deleteUser(url, user_id)
{
	_updateDivWithJSONFeedback(url, null, 'delete_user_'+user_id+'_indicator', null, null, null, ['users_results_user_'+user_id, 'user_'+user_id+'_edit_spinning', 'user_'+user_id+'_edit_tr', 'users_results_user_'+user_id+'_permissions_row'], null, 'post', null, updateUserLinks);
}

function cloneGroup(url, group_id)
{
	_postFormWithJSONFeedback(url, 'clone_group_' + group_id + '_form', 'clone_group_' + group_id + '_indicator', 'clone_group_' + group_id, 'groupconfig_list', true);
	return true;
}

function showGroupMembers(url, group_id)
{
	$('group_members_' + group_id + '_container').toggle();
	if ($('group_members_' + group_id + '_list').innerHTML == '')
	{
		new Ajax.Request(url, {
			asynchronous: true,
			method: "post",
			onLoading: function (transport) {
				$('group_members_' + group_id + '_indicator').show();
			},
			onSuccess: function (transport) {
				$('group_members_' + group_id + '_indicator').hide();
				var json = transport.responseJSON;
				if (json && json.content)
				{
					$('group_members_' + group_id + '_list').update(json.content);
				}
			},
			onComplete: function (transport) {
				$('group_members_' + group_id + '_indicator').hide();
				var json = transport.responseJSON;
				if (json && (!json.failed || json.success) && json.message)
				{
					TBG.Main.Helpers.Message.success(json.message);
				}
				if (json && (json.failed || json.error))
				{
					TBG.Main.Helpers.Message.error(json.error);
				}
			},
			onFailure: function (transport) {
				$('group_members_' + group_id + '_indicator').hide();
				$('group_members_' + group_id + '_container').hide();
				var json = transport.responseJSON;
				if (json && (json.failed || json.error))
				{
					TBG.Main.Helpers.Message.error(json.error);
				}
				else
				{
					TBG.Main.Helpers.Message.error(transport.responseText);
				}
			}
		});
	}
}

function updateTeamLinks(json)
{
	if ($('current_team_num_count'))
	{
		$('current_team_num_count').update(json.total_count);
	}
	$$('.copy_team_link').each(function (element) {
		if (json.more_available)
		{
			$(element).show();
		}
		else
		{
			$(element).hide();
		}
	});
	if (json.more_available)
	{
		$('add_team_div').show();
	}
	else
	{
		$('add_team_div').hide();
	}
}

function createTeam(url)
{
	_postFormWithJSONFeedback(url, 'create_team_form', 'create_team_indicator', '', 'teamconfig_list', true, null, null, updateTeamLinks);
	return true;
}

function deleteTeam(url, team_id)
{
	_updateDivWithJSONFeedback(url, null, 'delete_team_' + team_id + '_indicator', null, null, null, ['teambox_' + team_id], null, null, null, updateTeamLinks);
}

function cloneTeam(url, team_id)
{
	_postFormWithJSONFeedback(url, 'clone_team_' + team_id + '_form', 'clone_team_' + team_id + '_indicator', 'clone_team_' + team_id, 'teamconfig_list', true, null, null, updateTeamLinks);
	return false;
}

function showTeamMembers(url, team_id)
{
	$('team_members_' + team_id + '_container').toggle();
	if ($('team_members_' + team_id + '_list').innerHTML == '')
	{
		new Ajax.Request(url, {
			asynchronous: true,
			method: "post",
			onLoading: function (transport) {
				$('team_members_' + team_id + '_indicator').show();
			},
			onSuccess: function (transport) {
				$('team_members_' + team_id + '_indicator').hide();
				var json = transport.responseJSON;
				if (json && json.content)
				{
					$('team_members_' + team_id + '_list').update(json.content);
				}
			},
			onComplete: function (transport) {
				$('team_members_' + team_id + '_indicator').hide();
				var json = transport.responseJSON;
				if (json && (!json.failed || json.success) && json.message)
				{
					TBG.Main.Helpers.Message.success(json.message);
				}
				if (json && (json.failed || json.error))
				{
					TBG.Main.Helpers.Message.error(json.error);
				}
			},
			onFailure: function (transport) {
				$('team_members_' + team_id + '_indicator').hide();
				$('team_members_' + team_id + '_container').hide();
				var json = transport.responseJSON;
				if (json && (json.failed || json.error))
				{
					TBG.Main.Helpers.Message.error(json.error);
				}
				else
				{
					TBG.Main.Helpers.Message.error(transport.responseText);
				}
			}
		});
	}
}

function createClient(url)
{
	_postFormWithJSONFeedback(url, 'create_client_form', 'create_client_indicator', '', 'clientconfig_list', true);
	return true;
}

function deleteClient(url, client_id)
{
	new Ajax.Request(url, {
		asynchronous: true,
		method: "post",
		onLoading: function (transport) {
			$('delete_client_' + client_id + '_indicator').show();
		},
		onSuccess: function (transport) {
			$('delete_client_' + client_id + '_indicator').hide();
			$('clientbox_' + client_id).remove();
			var json = transport.responseJSON;
			if (json && (!json.failed || json.success) && json.message)
			{
				TBG.Main.Helpers.Message.success(json.message);
			}
		},
		onComplete: function (transport) {
			$('delete_client_' + client_id + '_indicator').hide();
			var json = transport.responseJSON;
			if (json && (!json.failed || json.success) && json.message)
			{
				TBG.Main.Helpers.Message.success(json.message);
			}
			if (json && (json.failed || json.error))
			{
				TBG.Main.Helpers.Message.error(json.error);
			}
		},
		onFailure: function (transport) {
			$('delete_client_' + client_id + '_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				TBG.Main.Helpers.Message.error(json.error);
			}
			else
			{
				TBG.Main.Helpers.Message.error(transport.responseText);
			}
		}
	});
}

function showClientMembers(url, client_id)
{
	$('client_members_' + client_id + '_container').toggle();
	if ($('client_members_' + client_id + '_list').innerHTML == '')
	{
		new Ajax.Request(url, {
			asynchronous: true,
			method: "post",
			onLoading: function (transport) {
				$('client_members_' + client_id + '_indicator').show();
			},
			onSuccess: function (transport) {
				$('client_members_' + client_id + '_indicator').hide();
				var json = transport.responseJSON;
				if (json && json.content)
				{
					$('client_members_' + client_id + '_list').update(json.content);
				}
			},
			onComplete: function (transport) {
				$('client_members_' + client_id + '_indicator').hide();
				var json = transport.responseJSON;
				if (json && (!json.failed || json.success) && json.message)
				{
					TBG.Main.Helpers.Message.success(json.message);
				}
				if (json && (json.failed || json.error))
				{
					TBG.Main.Helpers.Message.error(json.error);
				}
			},
			onFailure: function (transport) {
				$('client_members_' + client_id + '_indicator').hide();
				$('client_members_' + client_id + '_container').hide();
				var json = transport.responseJSON;
				if (json && (json.failed || json.error))
				{
					TBG.Main.Helpers.Message.error(json.error);
				}
				else
				{
					TBG.Main.Helpers.Message.error(transport.responseText);
				}
			}
		});
	}
}

function editClient(url, client_id)
{
	var params = Form.serialize('edit_client_' + client_id + '_form');
	new Ajax.Request(url, {
		asynchronous: true,
		method: "post",
		parameters: params,
		onLoading: function (transport) {
			$('edit_client_' + client_id + '_indicator').show();
		},
		onSuccess: function (transport) {
			$('edit_client_' + client_id + '_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				TBG.Main.Helpers.Message.error(json.error);
			}
			else if (json)
			{
				$('clientbox_' + client_id).update(json.content);
				$('edit_client_' + client_id).hide();

				TBG.Main.Helpers.Message.success(json.message);
			}
		},
		onFailure: function (transport) {
			$('edit_client_' + client_id + '_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				TBG.Main.Helpers.Message.error(json.error);
			}
			else
			{
				TBG.Main.Helpers.Message.error(transport.responseText);
			}
		}
	});
}

function editUser(url, user_id)
{
	var params = Form.serialize('edituser_' + user_id + '_form');
	new Ajax.Request(url, {
		asynchronous: true,
		method: "post",
		parameters: params,
		onLoading: function (transport) {
			$('edit_user_' + user_id + '_indicator').show();
		},
		onSuccess: function (transport) {
			$('edit_user_' + user_id + '_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				TBG.Main.Helpers.Message.error(json.error);
			}
			else if (json)
			{
				$('password_' + user_id + '_leave').checked = true;
				$('new_password_' + user_id + '_1').value = '';
				$('new_password_' + user_id + '_2').value = '';
				$('users_results_user_' + user_id).update(json.content);
				$('users_results_user_' + user_id).show();
				$('user_' + user_id + '_edit_tr').hide();
				updateTeamAndGroupDetailsFromJSON(json);
				TBG.Main.Helpers.Message.success(json.title, json.message);
			}
		},
		onFailure: function (transport) {
			$('edit_user_' + user_id + '_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				TBG.Main.Helpers.Message.error(json.error);
			}
			else
			{
				TBG.Main.Helpers.Message.error(transport.responseText);
			}
		}
	});
}

function updateTeamAndGroupDetailsFromJSON(json)
{
	if (json.update_groups)
	{
		json.update_groups.ids.each(function(group_id)
		{
			if ($('group_'+group_id+'_membercount'))
			{
				$('group_'+group_id+'_membercount').update(json.update_groups.membercounts[group_id]);
			}
			$('group_members_' + group_id + '_container').hide();
			$('group_members_'+group_id+'_list').update('');
		});
	}
	if (json.update_teams)
	{
		json.update_teams.ids.each(function(team_id)
		{
			if ($('team_'+team_id+'_membercount'))
			{
				$('team_'+team_id+'_membercount').update(json.update_teams.membercounts[team_id]);
			}
			$('team_members_' + team_id + '_container').hide();
			$('team_members_'+team_id+'_list').update('');
		});
	}
}

function getUserPermissionsBlock(url, user_id)
{
	if ($('users_results_user_' + user_id + '_permissions').innerHTML == '')
	{
		$('users_results_user_' + user_id + '_permissions_row').toggle();
		new Ajax.Request(url, {
			asynchronous: true,
			method: "post",
			onLoading: function (transport) {
				$('permissions_' + user_id + '_indicator').show();
				$('permissions_' + user_id + '_link').hide();
			},
			onSuccess: function (transport) {
				$('permissions_' + user_id + '_indicator').hide();
				$('permissions_' + user_id + '_link').show();
				$('users_results_user_' + user_id + '_permissions').update(transport.responseText);
			},
			onComplete: function (transport) {
				$('permissions_' + user_id + '_indicator').hide();
				$('permissions_' + user_id + '_link').show();
				var json = transport.responseJSON;
				if (json && (json.failed || json.error))
				{
					TBG.Main.Helpers.Message.error(json.error);
				}
			}
		});
	}
	else
	{
		$('users_results_user_' + user_id + '_permissions_row').toggle();
	}
}

