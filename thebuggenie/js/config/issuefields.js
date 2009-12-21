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

function addIssuefieldOption(url, type)
{
	var params = Form.serialize('add_' + type + '_form');
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: params,
	onLoading: function (transport) {
		$('add_' + type + '_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			failedMessage(json.error);
			$('add_' + type + '_indicator').hide();
		}
		else
		{
			$('add_' + type + '_indicator').hide();
			$('add_' + type + '_form').reset();
			successMessage(json.title, '');
			$('no_' + type + '_items').hide();
			$(type + '_list').insert({bottom: json.content});
		}
	},
	onFailure: function (transport) {
		$('add_' + type + '_indicator').hide();
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

function deleteIssuefieldOption(url, type, id)
{
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	onLoading: function (transport) {
		$('delete_' + type + '_' + id + '_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			failedMessage(json.error);
			$('delete_' + type + '_' + id + '_indicator').hide();
		}
		else
		{
			$('delete_' + type + '_' + id + '_indicator').hide();
			successMessage(json.title, '');
			$('item_' + type + '_' + id).remove();
			$('delete_item_' + id).remove();
			if ($(type + '_list').childElements().size() == 0)
			{
				$('no_' + type + '_items').show();
			}
		}
	},
	onFailure: function (transport) {
		$('delete_' + type + '_' + id + '_indicator').hide();
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