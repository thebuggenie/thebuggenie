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
			if (json && (json.failed || json.error))
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
			if (json.description)
			{
				$('issuetype_' + id + '_description_span').update(json.description);
			}
			if (json.name)
			{
				$('issuetype_' + id + '_name_span').update(json.name);
				if ($('issuetype_' + id + '_info'))
				{
					$('issuetype_' + id + '_info').show();
				}
			}
			successMessage(json.title);
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

function toggleIssuetypeForScheme(url, issuetype_id, scheme_id, action)
{
	var hide_element = 'type_toggle_' + issuetype_id + '_' + action;
	var show_element = 'type_toggle_' + issuetype_id + '_' + ((action == 'enable') ? 'disable' : 'enable');
	if (action == 'enable')
	{
		var onsuccess_callback = function (json) { $('issuetype_' + json.issuetype_id + '_box').addClassName("green"); $('issuetype_' + json.issuetype_id + '_box').removeClassName("lightgrey"); };
	}
	else
	{
		var onsuccess_callback = function (json) { $('issuetype_' + json.issuetype_id + '_box').removeClassName("green"); $('issuetype_' + json.issuetype_id + '_box').addClassName("lightgrey"); };
	}
	_updateDivWithJSONFeedback(url, null, 'edit_issuetype_' + issuetype_id + '_indicator', null, null, [hide_element], [hide_element], [show_element], 'post', null, onsuccess_callback);
}
