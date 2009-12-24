function showIssuetypeOptions(url, id)
{
	$('issuetype_' + id + '_content').toggle();
	if ($('issuetype_' + id + '_content').childElements().size() == 0)
	{
		new Ajax.Updater('issuetype_' + id + '_content', url, {
		asynchronous: true,
		method: "post",
		onLoading: function (transport) {
			$('issuetype_' + id + '_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			$('issuetype_' + id + '_indicator').hide();
			if (json && json.failed)
			{
				failedMessage(json.error);
			}
		},
		onFailure: function (transport) {
			$('issuetype_' + id + '_indicator').hide();
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

function updateIssuetype(url, id)
{
	var params = Form.serialize('edit_issuetype_' + id + '_form');
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: params,
	onLoading: function (transport) {
		$('edit_issuetype_' + id + '_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			failedMessage(json.error);
			$('edit_issuetype_' + id + '_indicator').hide();
		}
		else
		{
			$('edit_issuetype_' + id + '_indicator').hide();
			$('edit_issuetype_' + id + '_form').hide();
			$('issuetype_' + id + '_description_span').update(json.description);
			$('issuetype_' + id + '_name_link').update(json.name);
			if (json.reportable)
			{
				$('issuetype_' + id + '_box').removeClassName('borderless');
				$('issuetype_' + id + '_box').addClassName('iceblue_borderless');
			}
			else
			{
				$('issuetype_' + id + '_box').addClassName('borderless');
				$('issuetype_' + id + '_box').removeClassName('iceblue_borderless');
			}
			$('issuetype_' + id + '_info').show();
			successMessage(json.title, '');
		}
	},
	onFailure: function (transport) {
		$('edit_issuetype_' + id + '_indicator').hide();
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

function updateIssuetypeChoices(url, id)
{
	var params = Form.serialize('update_' + id + '_choices_form');
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: params,
	onLoading: function (transport) {
		$('update_' + id + '_choices_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			failedMessage(json.error);
			$('update_' + id + '_choices_indicator').hide();
		}
		else
		{
			$('update_' + id + '_choices_indicator').hide();
			$('issuetype_' + id + '_content').hide();
			successMessage(json.title, '');
		}
	},
	onFailure: function (transport) {
		$('update_' + id + '_choices_indicator').hide();
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

function addIssuetype(url)
{
	var params = Form.serialize('add_issuetype_form');
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: params,
	onLoading: function (transport) {
		$('add_issuetype_indicator').show();
		$('add_issuetype_button').hide();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			failedMessage(json.error);
			$('add_issuetype_button').show();
			$('add_issuetype_indicator').hide();
		}
		else
		{
			$('add_issuetype_indicator').hide();
			$('add_issuetype_button').show();
			$('add_issuetype_form').reset();
			successMessage(json.title, '');
			$('issuetypes_list').insert({bottom: json.content});
		}
	},
	onFailure: function (transport) {
		$('add_issuetype_indicator').hide();
		$('add_issuetype_button').show();
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