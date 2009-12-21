function showIssuefieldOptions(url, field)
{
	$(field + '_content').toggle();
	if ($(field + '_content').childElements().size() == 0)
	{
		new Ajax.Updater(field + '_content', url, {
		asynchronous: true,
		method: "post",
		onLoading: function (transport) {
			$(field + '_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			$(field + '_indicator').hide();
			if (json && json.failed)
			{
				failedMessage(json.error);
			}
			else
			{
				successMessage(json.title, json.content);
			}
		},
		onFailure: function (transport) {
			$(field + '_indicator').hide();
			if (transport.responseJSON)
			{
				failedMessage(transport.responseJSON.error);
			}
			else
			{
				failedMessage(transport.responseText);
			}
		}
		});
	}
}

