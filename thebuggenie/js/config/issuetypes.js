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

function addIssuefieldCustom(url)
{
	var params = Form.serialize('add_custom_type_form');
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: params,
	onLoading: function (transport) {
		$('add_custom_type_indicator').show();
		$('add_custom_type_button').hide();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			failedMessage(json.error);
			$('add_custom_type_button').show();
			$('add_custom_type_indicator').hide();
		}
		else
		{
			$('add_custom_type_indicator').hide();
			$('add_custom_type_button').show();
			$('add_custom_type_form').reset();
			successMessage(json.title, '');
			$('custom_types_list').insert({bottom: json.content});
		}
	},
	onFailure: function (transport) {
		$('add_custom_type_indicator').hide();
		$('add_custom_type_button').show();
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

function updateIssuefieldCustom(url, type)
{
	var params = Form.serialize('edit_custom_type_' + type + '_form');
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: params,
	onLoading: function (transport) {
		$('edit_custom_type_' + type + '_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			failedMessage(json.error);
			$('edit_custom_type_' + type + '_indicator').hide();
		}
		else
		{
			$('edit_custom_type_' + type + '_indicator').hide();
			$('edit_custom_type_' + type + '_form').hide();
			$('custom_type_' + type + '_description_span').update(json.description);
			$('custom_type_' + type + '_instructions_span').update(json.instructions);
			if (json.instructions != '')
			{
				$('custom_type_' + type + '_instructions_div').show();
				$('custom_type_' + type + '_no_instructions_div').hide();
			}
			else
			{
				$('custom_type_' + type + '_instructions_div').hide();
				$('custom_type_' + type + '_no_instructions_div').show();
			}
			$('custom_type_' + type + '_name_link').update(json.name);
			$('custom_type_' + type + '_info').show();
			successMessage(json.title, '');
		}
	},
	onFailure: function (transport) {
		$('edit_custom_type_' + type + '_indicator').hide();
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

function editIssuefieldOption(url, type, id)
{
	var params = Form.serialize('edit_' + type + '_' + id + '_form');
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: params,
	onLoading: function (transport) {
		$('edit_' + type + '_' + id + '_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			failedMessage(json.error);
			$('edit_' + type + '_' + id + '_indicator').hide();
		}
		else
		{
			$('edit_' + type + '_' + id + '_indicator').hide();
			successMessage(json.title, '');
			$(type + '_' + id + '_name').update($(type + '_' + id + '_name_input').getValue());
			if (type == 'status')
			{
				$(type + '_' + id + '_itemdata').update($(type + '_' + id + '_itemdata_input').getValue());
			}
			$('item_' + type + '_' + id).show();
			$('edit_item_' + id).hide();
		}
	},
	onFailure: function (transport) {
		$('edit_' + type + '_' + id + '_indicator').hide();
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