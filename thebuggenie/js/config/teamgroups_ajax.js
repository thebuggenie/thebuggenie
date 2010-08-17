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
				failedMessage(json.error);
			}
		},
		onFailure: function (transport) {
			$('find_users_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
			else
			{
				failedMessage(transport.responseText);
			}
		}
	});
}

function createUser(url)
{
	_postFormWithJSONFeedback(url, 'createuser_form', 'find_users_indicator', '', 'users_results');
	return true;
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
				successMessage(json.message);
			}
		},
		onComplete: function (transport) {
			$('delete_group_' + group_id + '_indicator').hide();
			var json = transport.responseJSON;
			if (json && (!json.failed || json.success) && json.message)
			{
				successMessage(json.message);
			}
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
		},
		onFailure: function (transport) {
			$('delete_group_' + group_id + '_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
			else
			{
				failedMessage(transport.responseText);
			}
		}
	});
}

function cloneGroup(url, group_id)
{
	_postFormWithJSONFeedback(url, 'clone_group_' + group_id + '_form', 'clone_group_' + group_id + '_indicator', 'clone_group_' + group_id, 'groupconfig_list', true);
	return true;
}

function editUser(url, user_id, message)
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
				failedMessage(json.error);
			}
			else
			{
				$('users_results_user_' + user_id).update(transport.responseText);
				$('users_results_user_' + user_id).show();
				$('users_results_user_' + user_id + '_edit').hide();
				$('users_results_user_' + user_id + '_edit').toggleClassName('selected_green');
				successMessage(message);
			}
		},
		onFailure: function (transport) {
			$('edit_user_' + user_id + '_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
			else
			{
				failedMessage(transport.responseText);
			}
		}
	});
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
					failedMessage(json.error);
				}
			}
		});
	}
	else
	{
		$('users_results_user_' + user_id + '_permissions_row').toggle();
	}
}

function showGroupMembers(url, group_id)
{
	new Ajax.Request(url, {
		asynchronous: true,
		method: "post",
		onLoading: function (transport) {
			$('group_members_' + group_id + '_indicator').show();
			$('group_members_' + group_id + '_container').show();
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
				successMessage(json.message);
			}
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
		},
		onFailure: function (transport) {
			$('group_members_' + group_id + '_indicator').hide();
			$('group_members_' + group_id + '_container').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
			else
			{
				failedMessage(transport.responseText);
			}
		}
	});
}