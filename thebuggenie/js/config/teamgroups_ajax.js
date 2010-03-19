function showUsers(url, letter)
{
	var params = Form.serialize('find_dev_form');
	new Ajax.Updater('find_dev_results', url, {
		asynchronous:true,
		method: "post",
		parameters: params,
		onLoading: function (transport) {
			$('find_dev_indicator').show();
		},
		onComplete: function (transport) {
			$('find_dev_indicator').hide();
			var json = transport.responseJSON;
			if (json && json.failed)
			{
				failedMessage(json.error);
			}
		},
		onFailure: function (transport) {
			$('find_dev_indicator').hide();
			var json = transport.responseJSON;
			if (json && json.failed)
			{
				failedMessage(json.error);
			}
		}
	});
}
