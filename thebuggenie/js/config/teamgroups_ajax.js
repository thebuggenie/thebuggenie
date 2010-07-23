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