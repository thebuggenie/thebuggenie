function submitSettings(url)
{
	var params = Form.serialize('config_settings');
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	parameters: params,
	onLoading: function (request) {
		$('settings_save_indicator').show();
		$('submit_settings_button').disable();
	},
	onSuccess: function (request) {
		$('settings_save_indicator').hide();
		$('message_changes_saved').show();
		new Effect.Fade('message_changes_saved', {delay: 20} );
		$('submit_settings_button').enable();
	},
	onFailure: function (request) {
		$('settings_save_indicator').hide();
		$('submit_settings_button').enable();
	}
	});
}

function failedMessage(title, content)
{
	$('message_failed_title').update(title);
	$('message_failed_content').update(content);
	$('message_failed').show();
	new Effect.Pulsate('message_failed');
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