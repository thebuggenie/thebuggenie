function getPermissionOptions(url, field)
{
	$(field).toggle();
	if ($(field).childElements().size() == 0)
	{
		new Ajax.Request(url, {
		asynchronous: true,
		method: "post",
		onLoading: function (transport) {
			$(field + '_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			$(field + '_indicator').hide();
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
			else
			{
				$(field).update(json.content);
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