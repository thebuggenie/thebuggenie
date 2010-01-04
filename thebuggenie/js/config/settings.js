function submitSettings(url)
{
	var params = Form.serialize('config_settings');
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	parameters: params,
	onLoading: function (transport) {
		$('settings_save_indicator').show();
		$('submit_settings_button').disable();
	},
	onSuccess: function (transport) {
		$('settings_save_indicator').hide();
		var json = transport.responseJSON;
		if (json.failed)
		{
			failedMessage(json.error);
		}
		else
		{
			successMessage(json.title, json.message);
		}
		$('submit_settings_button').enable();
	},
	onFailure: function (transport) {
		$('settings_save_indicator').hide();
		$('submit_settings_button').enable();
		var json = transport.responseJSON;
		if (json && json.failed)
		{
			failedMessage(json.error);
		}
	}
	});
}

function switchTab(select_tab)
{
	$('general_settings').hide();
	$('tab_general_settings').removeClassName('selected');
	$('server_settings').hide();
	$('tab_server_settings').removeClassName('selected');
	$('reglang_settings').hide();
	$('tab_reglang_settings').removeClassName('selected');
	$('user_settings').hide();
	$('tab_user_settings').removeClassName('selected');
	$(select_tab+'_settings').show();
	$('tab_'+select_tab+'_settings').addClassName('selected');
}