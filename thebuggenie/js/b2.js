
function failedMessage(title, content)
{
	$('thebuggenie_failuremessage_title').update(title);
	$('thebuggenie_failuremessage_content').update(content);
	if ($('thebuggenie_successmessage').visible())
	{
		var success_queue = Effect.Queues.get('successmessage');
		success_queue.each(function(effect) {effect.cancel();});
		new Effect.Fade('thebuggenie_successmessage', {queue: {position: 'end', scope: 'successmessage', limit: 2}, duration: 0.2});
	}
	if ($('thebuggenie_failuremessage').visible())
	{
		var failed_queue = Effect.Queues.get('failedmessage');
		failed_queue.each(function(effect) {effect.cancel();});
		new Effect.Pulsate('thebuggenie_failuremessage');
	}
	else
	{
		new Effect.Appear('thebuggenie_failuremessage', {queue: {position: 'end', scope: 'failedmessage', limit: 2}, duration: 0.2});
	}
	new Effect.Fade('thebuggenie_failuremessage', {queue: {position: 'end', scope: 'failedmessage', limit: 2}, delay: 30, duration: 0.2});
}

function successMessage(title, content)
{
	$('thebuggenie_successmessage_title').update(title);
	$('thebuggenie_successmessage_content').update(content);
	if (title || content)
	{
		if ($('thebuggenie_failuremessage').visible())
		{
			var failed_queue = Effect.Queues.get('failedmessage');
			failed_queue.each(function(effect) {effect.cancel();});
			new Effect.Fade('thebuggenie_failuremessage', {queue: {position: 'end', scope: 'failedmessage', limit: 2}, duration: 0.2});
		}
		if ($('thebuggenie_successmessage').visible())
		{
			var success_queue = Effect.Queues.get('successmessage');
			success_queue.each(function(effect) {effect.cancel();});
			new Effect.Pulsate('thebuggenie_successmessage');
		}
		else
		{
			new Effect.Appear('thebuggenie_successmessage', {queue: {position: 'end', scope: 'successmessage', limit: 2}, duration: 0.2});
		}
		new Effect.Fade('thebuggenie_successmessage', {queue: {position: 'end', scope: 'successmessage', limit: 2}, delay: 10, duration: 0.2});
	}
	else if ($('thebuggenie_successmessage').visible())
	{
		var success_queue = Effect.Queues.get('successmessage');
		success_queue.each(function(effect) {effect.cancel();});
		new Effect.Fade('thebuggenie_successmessage', {queue: {position: 'end', scope: 'successmessage', limit: 2}, duration: 0.2});
	}
}

function clearPopupMessages()
{
	var success_queue = Effect.Queues.get('successmessage');
	success_queue.each(function(effect) {effect.cancel();});
	var failed_queue = Effect.Queues.get('failedmessage');
	failed_queue.each(function(effect) {effect.cancel();});
	if ($('thebuggenie_successmessage').visible())
	{
		$('thebuggenie_successmessage').fade({duration: 0.2});
	}
	if ($('thebuggenie_failuremessage').visible())
	{
		$('thebuggenie_failuremessage').fade({duration: 0.2});
	}
}

/**
 * Convenience function for running an AJAX call and updating / showing / hiding
 * divs on json feedback
 *
 * @param url The URL to call
 * @param update_element Id of the element to update (or null if none)
 * @param indicator The id of an indicator element to show while running the ajax call
 * @param insertion If update_div is provided, specify insertion to add to bottom/top of the update_element, or null to replace contents
 * @param clear_update_element_before_loading boolean to say whether or not to clear the update_element before loading or null to ignore
 * @param hide_element_while_loading Id of an element to hide while the ajax is loading or null to ignore
 * @param hide_elements_on_success An array of element ids to hide if the request was successful or null to ignore
 * @param show_elements_on_success An array of element ids to show if the request was successful or null to ignore
 * @param url_method get or post the url or null to use "get"
 * @param params Optional parameters to pass with the url or null to ignore
 */
function _updateDivWithJSONFeedback(url, update_element, indicator, insertion, clear_update_element_before_loading, hide_element_while_loading, hide_elements_on_success, show_elements_on_success, url_method, params, onsuccess_callback, onfailure_callback, oncomplete_callback)
{
	params = (params) ? params : '';
	url_method = (url_method) ? url_method : "get";
	new Ajax.Request(url, {
	asynchronous:true,
	method: url_method,
	parameters: params,
	evalScripts: true,
	onLoading: function (transport) {
		$(indicator).show();
		if (clear_update_element_before_loading && $(update_element))
		{
			$(update_element).update('');
		}
		if (hide_element_while_loading && $(hide_element_while_loading))
		{
			$(hide_element_while_loading).hide();
		}
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json && json.failed)
		{
			failedMessage(json.error);
			$(indicator).hide();
		}
		else
		{
			$(indicator).hide();
			if ($(update_element))
			{
				content = (json) ? json.content : transport.responseText;
				if (insertion == true)
				{
					$(update_element).insert(content, 'after');
				}
				else
				{
					$(update_element).update(content);
				}
				if (json && json.message)
				{
					successMessage(json.message);
				}
			}
			else if (json && (json.title || json.content))
			{
				successMessage(json.title, json.content);
			}
			else if (json && (json.message))
			{
				successMessage(json.message);
			}
			if (hide_elements_on_success)
			{
				hide_elements_on_success.each(function(s)
				{
					if (is_string(s) && $(s))
					{
						$(s).hide();
					}
					else if ($(s)) s.hide();
				});
			}
			if (show_elements_on_success)
			{
				show_elements_on_success.each(function(s)
				{
					if ($(s)) $(s).show();
				});
			}
			if (onsuccess_callback)
			{
				onsuccess_callback(json);
			}
		}
	},
	onFailure: function (transport) {
		$(indicator).hide();
		if (transport.responseJSON)
		{
			failedMessage(transport.responseJSON.error);
		}
		else
		{
			failedMessage(transport.responseText);
		}
		if (onfailure_callback)
		{
			onfailure_callback(json);
		}
	},
	onComplete: function (transport) {
		if (hide_element_while_loading && hide_element_while_loading != hide_div_on_success && $(hide_element_while_loading))
		{
			$(hide_element_while_loading).show();
		}
		if (oncomplete_callback)
		{
			oncomplete_callback(json);
		}
	}
	});
}

function _postFormWithJSONFeedback(url, formname, indicator, hide_divs_when_done, update_div, insertion, show_divs_when_done, update_form_elm)
{
	var params = Form.serialize(formname);
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: params,
	onLoading: function (transport) {
		$(indicator).show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json && json.failed)
		{
			failedMessage(json.error);
			$(indicator).hide();
		}
		else
		{
			$(indicator).hide();
			if (update_div != '' && $(update_div))
			{
				content = (json) ? json.content : transport.responseText;
				if (insertion == true)
				{
					$(update_div).insert(content, 'after');
				}
				else
				{
					$(update_div).update(content);
				}
				if (json && json.message)
				{
					successMessage(json.message);
				}
			}
			if (update_form_elm != '' && $(update_form_elm))
			{
				content = (json) ? json.content : transport.responseText;
				$(update_form_elm).setValue(content);
				if (json && json.message)
				{
					successMessage(json.message);
				}
			}
			else if (json)
			{
				if (json.message)
				{
					successMessage(json.message);
				}
				else
				{
					successMessage(json.title, json.content);
				}
			}
			if (is_string(hide_divs_when_done) && $(hide_divs_when_done))
			{
				$(hide_divs_when_done).hide();
			}
			else if (hide_divs_when_done) 
			{
				hide_divs_when_done.each(function(s)
				{
					if (is_string(s) && $(s))
					{
						$(s).hide();
					}
					else if ($(s)) s.hide();
				});
			}
			if (is_string(show_divs_when_done) && $(show_divs_when_done))
			{
				$(show_divs_when_done).show();
			}
			else if (show_divs_when_done) 
			{
				show_divs_when_done.each(function(s)
				{
					if (is_string(s) && $(s))
					{
						$(s).show();
					}
					else if ($(s)) s.show();
				});
			}
		}
	},
	onFailure: function (transport) {
		$(indicator).hide();
		if (transport.responseJSON)
		{
			if (transport.responseJSON.error && transport.responseJSON.message)
			{
				failedMessage(transport.responseJSON.message, transport.responseJSON.error);
			}
			else if (transport.responseJSON.error)
			{
				failedMessage(transport.responseJSON.error);
			}
			else if (transport.responseJSON.message)
			{
				failedMessage(transport.responseJSON.message);
			}
		}
		else
		{
			failedMessage(transport.responseText);
		}
	}
	});
}

function findIdentifiable(url, field)
{
	var params = Form.serialize(field + '_form');
	new Ajax.Updater(field + '_results', url, {
	asynchronous: true,
	method: "post",
	parameters: params,
	onLoading: function () {$(field + '_spinning').show();},
	onComplete: function () {$(field + '_spinning').hide();}
	});
}

function submitForm(url, form_id)
{
	var params = Form.serialize(form_id);
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	parameters: params,
	onLoading: function (transport) {
		$(form_id + '_indicator').show();
		$(form_id + '_button').disable();
	},
	onSuccess: function (transport) {
		$(form_id + '_indicator').hide();
		var json = transport.responseJSON;
		if (json && (json.failed || json.error))
		{
			failedMessage(json.error);
		}
		else if (json && json.title)
		{
			successMessage(json.title, json.message);
		}
		else
		{
			failedMessage(transport.responseText);
		}
		$(form_id + '_button').enable();
	},
	onFailure: function (transport) {
		$(form_id + '_indicator').hide();
		$(form_id + '_button').enable();
		var json = transport.responseJSON;
		if (json && (json.failed || json.error))
		{
			failedMessage(json.error);
		}
	}
	});
}

function switchSubmenuTab(visibletab, menu)
{
  $(menu).childElements().each(function(item){item.removeClassName('selected');});
  $(visibletab).addClassName('selected');
  $(menu + '_panes').childElements().each(function(item){item.hide();});
  $(visibletab + '_pane').show();
}

function showFadedBackdrop(url)
{
	$('fullpage_backdrop').show();
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	onLoading: function (transport) {
		$('fullpage_backdrop_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json && (json.failed || json.error))
		{
			failedMessage(json.error);
			$('fullpage_backdrop_indicator').hide();
			$('fullpage_backdrop').hide();
		}
		else if (json)
		{
			$('fullpage_backdrop_indicator').hide();
			$('fullpage_backdrop_content').update(json.content);
		}
		else
		{
			failedMessage(transport.responseText);
			$('fullpage_backdrop_indicator').hide();
			$('fullpage_backdrop').hide();
		}
	},
	onFailure: function (transport) {
		var json = transport.responseJSON;
		if (json && (json.failed || json.error))
		{
			failedMessage(json.error);
		}
		else
		{
			failedMessage(transport.responseText);
		}
		$('fullpage_backdrop_indicator').hide();
		$('fullpage_backdrop').hide();
	}
	});
}

function resetFadedBackdrop()
{
	$('fullpage_backdrop').hide();
	$('fullpage_backdrop_indicator').show();
	$('fullpage_backdrop_content').update('');
}

function updatePercentageFromNumber(tds, percent)
{
	cc = 0;
	$(tds).childElements().each(function(elm) {
		if ($(tds).childElements().size() == 2)
		{
			$(tds).childElements().first().style.width = percent + '%';
			$(tds).childElements().last().style.width = (100 - percent) + '%';
		}
		else
		{
			elm.removeClassName("percent_filled");
			elm.removeClassName("percent_unfilled");
			if (percent > 0 && percent < 100)
			{
				(cc <= percent) ? elm.addClassName("percent_filled") : elm.addClassName("percent_unfilled");
			}
			else if (percent == 0)
			{
				elm.addClassName("percent_unfilled");
			}
			else if (percent == 100)
			{
				elm.addClassName("percent_filled");
			}
			cc++;
		}
	});
}

function addLink(url, target_type, target_id)
{
	var params = $('attach_link_' + target_type + '_' + target_id + '_form').serialize();
	$('attach_link_' + target_type + '_' + target_id + '_indicator').show();
	$('attach_link_' + target_type + '_' + target_id + '_submit').hide();
	new Ajax.Request(url, {
		method: 'post',
		parameters: params,
		requestHeaders: {Accept: 'application/json'},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			if (json && !json.failed)
			{
				$('attach_link_' + target_type + '_' + target_id + '_form').reset();
				$('attach_link_' + target_type + '_' + target_id).hide();
				$(target_type + '_' + target_id + '_no_links').hide();
				$(target_type + '_' + target_id + '_links').insert({bottom: json.content});
				successMessage(json.message);
			}
			else if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
			else
			{
				failedMessage(transport.responseText);
			}
			$('attach_link_' + target_type + '_' + target_id + '_indicator').hide();
			$('attach_link_' + target_type + '_' + target_id + '_submit').show();
		},
		onFailure: function(transport) {
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
			else
			{
				failedMessage(transport.responseText);
			}
			$('attach_link_' + target_type + '_' + target_id + '_indicator').hide();
			$('attach_link_' + target_type + '_' + target_id + '_submit').show();
		}
	});
}

function removeLink(url, target_type, target_id, link_id)
{
	new Ajax.Request(url, {
		method: 'post',
		requestHeaders: {Accept: 'application/json'},
		onLoading: function() {
			$(target_type + '_' + target_id + '_links_'+ link_id + '_remove_link').hide();
			$(target_type + '_' + target_id + '_links_'+ link_id + '_remove_indicator').show();
		},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			if (json && json.failed == false)
			{
				$(target_type + '_' + target_id + '_links_' + link_id).remove();
				$(target_type + '_' + target_id + '_links_' + link_id + '_remove_confirm').remove();
				successMessage(json.message);
				if ($(target_type + '_' + target_id + '_links').childElements().size() == 0)
				{
					$(target_type + '_' + target_id + '_no_links').show();
				}
			}
			else
			{
				if (json && (json.failed || json.error))
				{
					failedMessage(json.error);
				}
				else
				{
					failedMessage(transport.responseText);
				}
				$(target_type + '_' + target_id + '_links_'+ link_id + '_remove_link').show();
				$(target_type + '_' + target_id + '_links_'+ link_id + '_remove_indicator').hide();
			}
		},
		onFailure: function(transport) {
			$(target_type + '_' + target_id + '_links_'+ link_id + '_remove_link').show();
			$(target_type + '_' + target_id + '_links_'+ link_id + '_remove_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
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

function reloadImage(id) {
   var src = $(id).src;
   var pos = src.indexOf('?');
   if (pos >= 0) {
      src = src.substr(0, pos);
   }
   var date = new Date();
   $(id).src = src + '?v=' + date.getTime();
   return false;
}

function addUserStoryTask(url, story_id, mode)
{
	if (mode == 'scrum')
	{
		var prefix = 'scrum_story_' + story_id;
		var indicator_prefix = 'add_task_' + story_id;
	}
	else if (mode == 'issue')
	{
		var prefix = 'viewissue';
		var indicator_prefix = 'add_task';
	}
	var params = Form.serialize(prefix + '_add_task_form');
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: params,
	onLoading: function (transport) {
		$(indicator_prefix + '_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json && !json.failed)
		{
			Form.reset(prefix + '_add_task_form');
			$(indicator_prefix + '_indicator').hide();
			if (mode == 'scrum')
			{
				$('no_tasks_' + story_id).hide();
				$(prefix + '_tasks').insert({bottom: json.content});
				$(prefix + '_tasks_count').update(json.count);
			}
			else if (mode == 'issue')
			{
				$('related_child_issues_inline').insert({bottom: json.content});
				$('no_child_issues').hide();
				successMessage(json.message);
				if (json.comment)
				{
					$('comments_box').insert({bottom: json.comment});

					if ($('comments_box').childElements().size() != 0)
					{
						$('comments_none').hide();
					}
				}
			}
		}
		else
		{
			if (json && json.error)
			{
				failedMessage(json.error);
			}
			else
			{
				failedMessage(transport.responseText);
			}
			$(indicator_prefix + '_indicator').hide();
		}
	},
	onFailure: function (transport) {
		var json = transport.responseJSON;
		if (json && json.error)
		{
			failedMessage(json.error);
		}
		else
		{
			failedMessage(transport.responseText);
		}
		$(indicator_prefix + '_indicator').hide();
	}
	});
}

function addSearchFilter(url)
{
	var params = Form.serialize('add_filter_form');
	params += '&key=' + $('max_filters').value;
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: params,
	onLoading: function (transport) {
		$('add_filter_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json && (json.failed || json.error))
		{
			failedMessage(json.error);
			$('add_filter_indicator').hide();
		}
		else if (json)
		{
			$('add_filter_indicator').hide();
			$('search_filters_list').insert({bottom: json.content});
			$('max_filters').value++;
		}
		else
		{
			failedMessage(transport.responseText);
			$('add_filter_indicator').hide();
		}
	},
	onFailure: function (transport) {
		var json = transport.responseJSON;
		if (json && (json.failed || json.error))
		{
			failedMessage(json.error);
		}
		$('add_filter_indicator').hide();
	}
	});
}

function removeSearchFilter(key)
{
	$('filter_' + key).remove();
	if ($('search_filters_list').childElements().size() == 0)
	{
		$('max_filters').value = 0;
	}
}

function updateProfileInformation(url)
{
	_postFormWithJSONFeedback(url, 'profile_information_form', 'profile_save_indicator');
	return true;
}

function updateProfileModuleSettings(url, module_name)
{
	_postFormWithJSONFeedback(url, 'profile_' + module_name + '_form', 'profile_' + module_name + '_save_indicator');
	return true;
}

function updateProfileSettings(url)
{
	_postFormWithJSONFeedback(url, 'profile_settings_form', 'profile_settings_save_indicator');
	if ($('profile_use_gravatar_yes').checked)
	{
		$('gravatar_change').show();
	}
	else
	{
		$('gravatar_change').hide();
	}
	return true;
}

function changePassword(url)
{
	_postFormWithJSONFeedback(url, 'change_password_form', 'change_password_indicator', 'change_password_form');
	return true;
}

function hideInfobox(url, boxkey)
{
	if ($('close_me_' + boxkey).checked)
	{
		new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		onLoading: function (transport) {
			$('infobox_' + boxkey + '_indicator').show();
		},
		onComplete: function (transport) {
			$('infobox_' + boxkey + '_indicator').hide();
		}
		});
	}
	$('infobox_' + boxkey).fade({duration: 0.3});
}

function updateProjectMenuStrip(url, project_id)
{
	new Ajax.Updater('project_menustrip', url, {
		asynchronous: true,
		parameters: {project_id: project_id},
		evalScripts: true,
		method: "post",
		onLoading: function(transport) {
			$('project_menustrip_change').hide();
			$('project_menustrip_indicator').show();
			$('project_menustrip_name').hide();
		},
		onComplete: function(transport) {
			$('project_menustrip_indicator').hide();
			$('project_menustrip_name').show();
		}
	});
}

function setPermission(url, field)
{
	new Ajax.Request(url, {
		asynchronous: true,
		evalScripts: true,
		method: "post",
		onLoading: function(transport) {
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

function searchPage(url, offset)
{
	//var params = Form.serialize('find_issues_form');
	var params = '&offset=' + offset;
	new Ajax.Updater('search_results', url, {
	asynchronous: true,
	method: "post",
	parameters: params,
	onLoading: function () {$('paging_spinning').show();},
	onComplete: function () {$('paging_spinning').hide();}
	});
}

function getUserStateList()
{
	new Ajax.Updater('user_statelist', 'ajax_handler.php?getuserstatelist=true', {
	asynchronous:true,
	method: "post"
	});
}

function setUserState(sid)
{
	new Ajax.Request('ajax_handler.php?getuserstatelist=true', {
	asynchronous:true,
	method: "post",
	parameters: {setuserstate: sid}
	});
}

function setEmailPrivacy(priv)
{
	new Ajax.Updater('account_email', 'ajax_handler.php?setemailprivacy=true', {
	asynchronous:true,
	method: "post",
	parameters: {setting: priv}
	});
}

function showFollowUps(priv)
{
	new Ajax.Updater('account_followups', 'ajax_handler.php?showfollowups=true', {
	asynchronous:true,
	method: "post",
	parameters: {setting: priv}
	});
}

function showAssigned(priv)
{
	new Ajax.Updater('account_showassigned', 'ajax_handler.php?showassigned=true', {
	asynchronous:true,
	method: "post",
	parameters: {setting: priv}
	});
}

function submitNewPassword()
{
	var params = Form.serialize('changepassword_form');
	new Ajax.Updater('password_changed_span', 'ajax_handler.php?change_password=true', {
	asynchronous:true,
	method: "post",
	parameters: params,
	evalScripts: true
	});
	Element.show('password_changed_span');
}

function getStatistics(url)
{
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	onLoading: function (transport) {
		$('statistics_help').hide();
		$('statistics_main').show();
		$('statistics_main_image').src = '';
		$('statistics_mini_image_1').src = '';
		$('statistics_mini_image_2').src = '';
		$('statistics_mini_image_3').src = '';
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json && (json.failed || json.error))
		{
			failedMessage(json.error);
			$('statistics_help').show();
			$('statistics_main').hide();
		}
		else if (json)
		{
			$('statistics_main_image').src = json.images.main;
			if (json.images.mini_1_small)
			{
				$('statistics_mini_image_1').show();
				$('statistics_mini_image_1').src = json.images.mini_1_small;
				$('statistics_mini_1_main').setValue(json.images.mini_1_large);
			}
			else
			{
				$('statistics_mini_image_1').hide();
				$('statistics_mini_1_main').setValue('');
			}
			if (json.images.mini_2_small)
			{
				$('statistics_mini_image_2').show();
				$('statistics_mini_image_2').src = json.images.mini_2_small;
				$('statistics_mini_2_main').setValue(json.images.mini_2_large);
			}
			else
			{
				$('statistics_mini_image_2').hide();
				$('statistics_mini_2_main').setValue('');
			}
			if (json.images.mini_3_small)
			{
				$('statistics_mini_image_3').show();
				$('statistics_mini_image_3').src = json.images.mini_3_small;
				$('statistics_mini_3_main').setValue(json.images.mini_3_large);
			}
			else
			{
				$('statistics_mini_image_3').hide();
				$('statistics_mini_3_main').setValue('');
			}
		}
		else
		{
			failedMessage(transport.responseText);
			$('statistics_help').show();
			$('statistics_main').hide();
		}
	},
	onFailure: function (transport) {
		var json = transport.responseJSON;
		if (json && (json.failed || json.error))
		{
			failedMessage(json.error);
		}
		else
		{
			failedMessage(transport.responseText);
		}
		$('statistics_help').show();
		$('statistics_main').hide();
	}
	});

}

function toggleStatisticsMainImage(image)
{
	$('statistics_main_image').src = '';
	$('statistics_main_image').src = $('statistics_mini_'+image+'_main').getValue();
}

function findRelatedIssues(url)
{
	_postFormWithJSONFeedback(url, 'viewissue_find_issue_form', 'find_issue_indicator', '', 'viewissue_relation_results');
	return false;
}

function findDuplicateIssues(url)
{
	_postFormWithJSONFeedback(url, 'viewissue_find_issue_form', 'find_issue_indicator', '', 'viewissue_duplicate_results');
	return false;
}

function relateIssues(url)
{
	if ($('relate_issue_with_selected').getValue() == 'relate_children')
	{
		_postFormWithJSONFeedback(url, 'viewissue_relate_issues_form', 'relate_issues_indicator', 'no_child_issues', 'related_child_issues_inline', true);
	}
	else
	{
		_postFormWithJSONFeedback(url, 'viewissue_relate_issues_form', 'relate_issues_indicator', 'no_parent_issues', 'related_parent_issues_inline', true);
	}
	return false;
}

function _addVote(url, direction)
{
	var opp_direction = (direction == 'up') ? 'down' : 'up';
	_updateDivWithJSONFeedback(url, 'issue_votes', 'vote_' + direction + '_indicator', null, false, 'vote_' + direction + '_link', ['vote_' + direction + '_link', 'vote_' + opp_direction + '_faded'], ['vote_' + direction + '_faded', 'vote_' + opp_direction + '_link']);
}

function voteUp(url)
{
	_addVote(url, 'up');
}

function voteDown(url)
{
	_addVote(url, 'down');
}

function toggleMilestoneIssues(url, milestone_id)
{
	if ($('milestone_' + milestone_id + '_issues').childElements().size() == 0)
	{
		_updateDivWithJSONFeedback(url, 'milestone_' + milestone_id + '_issues', 'milestone_' + milestone_id + '_indicator', null, null, null, null, ['milestone_' + milestone_id + '_issues']);
	}
	else
	{
		$('milestone_' + milestone_id + '_issues').toggle();
	}
}

function refreshMilestoneDetails(url, milestone_id)
{
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	onLoading: function (transport) {
		$('milestone_' + milestone_id + '_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json && (json.failed || json.error))
		{
			failedMessage(json.error);
		}
		else if (json)
		{
			var must_reload_issue_list = false;
			if (json.percent)
			{
				updatePercentageFromNumber('milestone_'+milestone_id+'_percent', json.percent);
			}
			if (json.closed_issues && $('milestone_'+milestone_id+'_closed_issues'))
			{
				if ($('milestone_'+milestone_id+'_closed_issues').innerHTML != json.closed_issues)
				{
					$('milestone_'+milestone_id+'_closed_issues').update(json.closed_issues);
					must_reload_issue_list = true;
				}
			}
			if (json.assigned_issues && $('milestone_'+milestone_id+'_assigned_issues'))
			{
				if ($('milestone_'+milestone_id+'_assigned_issues').innerHTML != json.assigned_issues)
				{
					$('milestone_'+milestone_id+'_assigned_issues').update(json.assigned_issues);
					must_reload_issue_list = true;
				}
			}
			if (json.assigned_points && $('milestone_'+milestone_id+'_assigned_points'))
			{
				if ($('milestone_'+milestone_id+'_assigned_points').innerHTML != json.assigned_points)
				{
					$('milestone_'+milestone_id+'_assigned_points').update(json.assigned_points);
					must_reload_issue_list = true;
				}
			}
			if (json.closed_points && $('milestone_'+milestone_id+'_closed_points'))
			{
				if ($('milestone_'+milestone_id+'_closed_points').innerHTML != json.closed_points)
				{
					$('milestone_'+milestone_id+'_closed_points').update(json.closed_points);
					must_reload_issue_list = true;
				}
			}
			if (json.date_string && $('milestone_'+milestone_id+'_date_string'))
			{
				if ($('milestone_'+milestone_id+'_date_string').innerHTML != json.date_string)
				{
					$('milestone_'+milestone_id+'_date_string').update(json.date_string);
					must_reload_issue_list = true;
				}
			}
			if (must_reload_issue_list)
			{
				$('milestone_'+milestone_id+'_changed').show();
				$('milestone_'+milestone_id+'_issues').update('');
			}
		}
		else
		{
			failedMessage(transport.responseText);
		}
		$('milestone_' + milestone_id + '_indicator').hide();
	},
	onFailure: function (transport) {
		var json = transport.responseJSON;
		if (json && (json.failed || json.error))
		{
			failedMessage(json.error);
		}
		else
		{
			failedMessage(transport.responseText);
		}
		$('milestone_' + milestone_id + '_indicator').hide();
	}
	});

}

function is_string(elm)
{
    return typeof elm == 'string';
}

function dashboardResize()
{
	var dashboard_width = $('dashboard').getWidth();
	var element_width = (dashboard_width > 600) ? ((dashboard_width / 2) - 5) : (dashboard_width - 5);
	$('dashboard').childElements().each(function(item) {
		item.setStyle({width: element_width + 'px'});
	});
}

function detachFileFromArticle(url, file_id, article_name)
{
	_detachFile(url, file_id, 'article_' + article_name + '_files_');
}

function _detachFile(url, file_id, base_id)
{
	new Ajax.Request(url, {
		method: 'post',
		requestHeaders: {Accept: 'application/json'},
		onLoading: function() {
			$(base_id + file_id + '_remove_link').hide();
			$(base_id + file_id + '_remove_indicator').show();
			$('uploaded_files_'+ file_id + '_remove_link').hide();
			$('uploaded_files_'+ file_id + '_remove_indicator').show();
		},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			if (json && json.failed == false)
			{
				$(base_id + file_id).remove();
				$('uploaded_files_' + file_id).remove();
				$(base_id + file_id + '_remove_confirm').remove();
				$('uploaded_files_' + file_id + '_remove_confirm').remove();
				successMessage(json.message);
				if (json.attachmentcount == 0)
				{
					if ($('viewissue_no_uploaded_files'))
						$('viewissue_no_uploaded_files').show();
				}
				if ($('viewissue_uploaded_attachments_count'))
				{
					$('viewissue_uploaded_attachments_count').update(json.attachmentcount);
				}
			}
			else
			{
				if (json && (json.failed || json.error))
				{
					failedMessage(json.error);
				}
				else
				{
					failedMessage(transport.responseText);
				}
				$(base_id + file_id + '_remove_link').show();
				$(base_id + file_id + '_remove_indicator').hide();
				$('uploaded_files_'+ file_id + '_remove_link').show();
				$('uploaded_files_'+ file_id + '_remove_indicator').hide();
			}
		},
		onFailure: function(transport) {
			$(base_id + file_id + '_remove_link').show();
			$(base_id + file_id + '_remove_indicator').hide();
			$('uploaded_files_'+ file_id + '_remove_link').show();
			$('uploaded_files_'+ file_id + '_remove_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
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
