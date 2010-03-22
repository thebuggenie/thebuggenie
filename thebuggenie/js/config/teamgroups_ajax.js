function showUsers(url, findstring)
{
	new Ajax.Updater('users_results', url, {
		asynchronous: true,
		method: "post",
		parameters: '&findstring=' + findstring,
		onLoading: function (transport) {
			$('find_users_indicator').show();
		},
		onComplete: function (transport) {
			$('find_users_indicator').hide();
			var json = transport.responseJSON;
			if (json && json.failed)
			{
				failedMessage(json.error);
			}
		},
		onFailure: function (transport) {
			$('find_users_indicator').hide();
			var json = transport.responseJSON;
			if (json && json.failed)
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

function editUser(url, user_id, message)
{
	var params = Form.serialize('edituser_' + user_id + '_form');
	new Ajax.Updater('users_results_user_' + user_id, url, {
		asynchronous: true,
		method: "post",
		parameters: params,
		onLoading: function (transport) {
			$('edit_user_' + user_id + '_indicator').show();
		},
		onComplete: function (transport) {
			$('edit_user_' + user_id + '_indicator').hide();
			var json = transport.responseJSON;
			if (json && json.failed)
			{
				failedMessage(json.error);
			}
			else
			{
				$('users_results_user_' + user_id).show();
				$('users_results_user_' + user_id + '_edit').hide();
				$('users_results_user_' + user_id + '_edit').toggleClassName('selected_green');
				successMessage(message);
			}
		},
		onFailure: function (transport) {
			$('edit_user_' + user_id + '_indicator').hide();
			var json = transport.responseJSON;
			if (json && json.failed)
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
