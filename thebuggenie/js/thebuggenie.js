
function removeSearchFilter(key)
{
	$('filter_' + key).remove();
	if ($('search_filters_list').childElements().size() == 0)
	{
		$('max_filters').value = 0;
	}
}

function deleteSavedSearch(url, id)
{
	_updateDivWithJSONFeedback(url, null, 'delete_search_'+id+'_indicator', null, null, null, ['saved_search_'+id+'_container'], null, 'post');
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
				TBG.Main.failedMessage(json.error);
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
				TBG.Main.failedMessage(transport.responseJSON.error);
			}
			else
			{
				TBG.Main.failedMessage(transport.responseText);
			}
		}
	});
}

function searchPage(url, parameters, offset)
{
	//var params = Form.serialize('find_issues_form');
	var params = parameters + '&offset=' + offset;
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
			TBG.Main.failedMessage(json.error);
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
			TBG.Main.failedMessage(transport.responseText);
			$('statistics_help').show();
			$('statistics_main').hide();
		}
	},
	onFailure: function (transport) {
		var json = transport.responseJSON;
		if (json && (json.failed || json.error))
		{
			TBG.Main.failedMessage(json.error);
		}
		else
		{
			TBG.Main.failedMessage(transport.responseText);
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

function updateTimeline(url)
{
	_updateDivWithJSONFeedback(url, 'timeline', 'timeline_indicator', true, false, 'timeline_more_link', null, ['timeline_more_link'], 'get', {offset: $('timeline_offset').getValue()}, function (json) {$('timeline_offset').setValue(json.offset)});
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
			TBG.Main.failedMessage(json.error);
		}
		else if (json)
		{
			var must_reload_issue_list = false;
			if (json.percent)
			{
				TBG.Main.updatePercentageLayout('milestone_'+milestone_id+'_percent', json.percent);
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
			TBG.Main.failedMessage(transport.responseText);
		}
		$('milestone_' + milestone_id + '_indicator').hide();
	},
	onFailure: function (transport) {
		var json = transport.responseJSON;
		if (json && (json.failed || json.error))
		{
			TBG.Main.failedMessage(json.error);
		}
		else
		{
			TBG.Main.failedMessage(transport.responseText);
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
				TBG.Main.successMessage(json.message);
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
					TBG.Main.failedMessage(json.error);
				}
				else
				{
					TBG.Main.failedMessage(transport.responseText);
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
				TBG.Main.failedMessage(json.error);
			}
			else
			{
				TBG.Main.failedMessage(transport.responseText);
			}
		}
	});
}

function deleteComment(url, cid)
{
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	requestHeaders: {Accept: 'application/json'},
	onLoading: function (transport) {
		$('comment_delete_controls_' + cid).hide();
		$('comment_delete_indicator_' + cid).show();
	},
	onSuccess: function(transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			$('comment_delete_controls_' + cid).show();
			$('comment_delete_indicator_' + cid).hide();
			TBG.Main.failedMessage(json.error);
		}
		else
		{
			$('comment_delete_indicator_' + cid).remove();
			$('comment_delete_confirm_' + cid).remove();
			$('comment_' + cid).remove();
			if ($('comments_box').childElements().size() == 0)
			{
				$('comments_none').show();
			}
			TBG.Main.successMessage(json.title);
		}
	},
	onFailure: function (transport) {
		$('comment_delete_indicator_' + cid).hide();
		$('comment_delete_controls_' + cid).show();
		var json = transport.responseJSON;
		if (json && (json.failed || json.error))
		{
			TBG.Main.failedMessage(json.error);
		}
	}
	});
}

function updateComment(url, cid)
{
	params = $('comment_edit_form_' + cid).serialize();
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	parameters: params,
	requestHeaders: {Accept: 'application/json'},
	onLoading: function () {
		$('comment_edit_controls_' + cid).hide();
		$('comment_edit_indicator_' + cid).show();
	},
	onSuccess: function(transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			$('comment_edit_controls_' + cid).show();
			$('comment_edit_indicator_' + cid).hide();
			TBG.Main.failedMessage(json.error);
		}
		else
		{
			$('comment_edit_indicator_' + cid).hide();
			$('comment_edit_' + cid).hide();
			$('comment_' + cid + '_body').update(json.comment_body);

			$('comment_view_' + cid).show();
			$('comment_edit_controls_' + cid).show();

			TBG.Main.successMessage(json.title);
		}
	},
	onFailure: function (transport) {
		$('comment_edit_controls_' + cid).show();
		$('comment_edit_indicator_' + cid).hide();
		var json = transport.responseJSON;
		if (json && json.error)
		{
			TBG.Main.failedMessage(json.error);
		}
	}
	});
}

function addComment(url, commentcount_span)
{
	params = $('comment_form').serialize();
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	parameters: params,
	requestHeaders: {Accept: 'application/json'},
	onLoading: function () {
		$('comment_add_controls').hide();
		$('comment_add_indicator').show();
	},
	onSuccess: function(transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			$('comment_add_controls').show();
			$('comment_add_indicator').hide();
			TBG.Main.failedMessage(json.error);
		}
		else
		{
			params2 = $('comment_form').serialize(true);
			if (params2.comment_save_changes == '1')
			{
				window.location = json.continue_url;
				return;
			}

			$('comment_add_indicator').hide();
			$('comment_add').hide();
			$('comment_add_button').show();

			$('comments_box').insert({bottom: json.comment_data});

			if ($('comments_box').childElements().size() != 0)
			{
				$('comments_none').hide();
			}

			$('comment_add_controls').show();

			//$('comment_title').clear();
			$('comment_bodybox').clear();
			$('comment_visibility').setValue(1);
			$(commentcount_span).update(json.commentcount);

			TBG.Main.successMessage(json.title);
		}
	},
	onFailure: function (transport) {
		$('comment_add_controls').show();
		$('comment_add_indicator').hide();
		var json = transport.responseJSON;
		if (json && json.error)
		{
			TBG.Main.failedMessage(json.error);
		}
	}
	});
	return false;
}

// The core js class used by thebuggenie
var TBG = {
	Core: {}, // The "Core" namespace is for functions used by thebuggenie core, not to be invoked outside the js class
	Main: {}, // The "Main" namespace contains regular functions in use across the site
	Issues: {}, // The "Issues" namespace contains functions used in direct relation to issues
	Search: {}, // The "Search" namespace contains functions related to searching
	Subscriptions: {}, // The "Subscription" namespace contains functionality related to subscribing to - and publishing js events
	effect_queues: {
		successmessage: 'TBG_successmessage',
		failedmessage: 'TBG_failedmessage'
	},
	activated_popoutmenu: undefined,
	autocompleter_url: undefined
};

TBG.Core.initializeAutocompleter = function() {
	new Ajax.Autocompleter(
		"searchfor",
		"searchfor_autocomplete_choices",
		TBG.autocompleter_url,
		{
			paramName: "filters[text][value]",
			minChars: 2,
			indicator: 'quicksearch_indicator',
			afterUpdateElement: TBG.Core.extractAutocompleteValue
		}
	);
};

TBG.Core.extractAutocompleteValue = function(elem, value) {
	var elements = value.select('.url');
	if (elements.size() == 1) {
		window.location = elements[0].innerHTML.unescapeHTML();
		$('quicksearch_indicator').show();
		$('searchfor').blur();
	}
};

TBG.Core.resizeWatcher = function() {
	if (($('fullpage_backdrop') && $('fullpage_backdrop').visible()) || ($('attach_file') && $('attach_file').visible())) {
		var docheight = document.viewport.getHeight();
		var backdropheight = $('backdrop_detail_content').getHeight();
		if (backdropheight > (docheight - 100)) {
			$('backdrop_detail_content').setStyle({height: docheight - 100 + 'px', overflow: 'scroll'});
		} else {
			$('backdrop_detail_content').setStyle({height: 'auto', overflow: ''});
		}
	}
};

TBG.Core.toggleBreadcrumbItem = function(item) {
	item.up('li').next().toggleClassName('popped_out');
	item.toggleClassName('activated');
};

TBG.initialize = function(options) {
	for(var key in options) {
		TBG[key] = options[key];
	}
	TBG.Core.initializeAutocompleter();
	Event.observe(window, 'resize', TBG.Core.resizeWatcher);
	document.observe('click', TBG.Main.toggleBreadcrumbMenuPopout);
};

TBG.Main.clearPopupMessages = function() {
	Effect.Queues.get(TBG.effect_queues.successmessage).each(function(effect) {effect.cancel();});
	Effect.Queues.get(TBG.effect_queues.failedmessage).each(function(effect) {effect.cancel();});
	if ($('thebuggenie_successmessage').visible()) {
		$('thebuggenie_successmessage').fade({duration: 0.2});
	}
	if ($('thebuggenie_failuremessage').visible()) {
		$('thebuggenie_failuremessage').fade({duration: 0.2});
	}
};

TBG.Main.failedMessage = function(title, content) {
	$('thebuggenie_failuremessage_title').update(title);
	$('thebuggenie_failuremessage_content').update(content);
	if ($('thebuggenie_successmessage').visible()) {
		Effect.Queues.get(TBG.effect_queues.successmessage).each(function(effect) {effect.cancel();});
		new Effect.Fade('thebuggenie_successmessage', {queue: {position: 'end', scope: TBG.effect_queues.successmessage, limit: 2}, duration: 0.2});
	}
	if ($('thebuggenie_failuremessage').visible()) {
		Effect.Queues.get(TBG.effect_queues.failedmessage).each(function(effect) {effect.cancel();});
		new Effect.Pulsate('thebuggenie_failuremessage', {duration: 1, pulses: 4});
	} else {
		new Effect.Appear('thebuggenie_failuremessage', {queue: {position: 'end', scope: TBG.effect_queues.failedmessage, limit: 2}, duration: 0.2});
	}
	new Effect.Fade('thebuggenie_failuremessage', {queue: {position: 'end', scope: TBG.effect_queues.failedmessage, limit: 2}, delay: 30, duration: 0.2});
};

TBG.Main.successMessage = function(title, content) {
	$('thebuggenie_successmessage_title').update(title);
	$('thebuggenie_successmessage_content').update(content);
	if (title || content) {
		if ($('thebuggenie_failuremessage').visible()) {
			Effect.Queues.get(TBG.effect_queues.failedmessage).each(function(effect) {effect.cancel();});
			new Effect.Fade('thebuggenie_failuremessage', {queue: {position: 'end', scope: TBG.effect_queues.failedmessage, limit: 2}, duration: 0.2});
		}
		if ($('thebuggenie_successmessage').visible()) {
			Effect.Queues.get(TBG.effect_queues.successmessage).each(function(effect) {effect.cancel();});
			new Effect.Pulsate('thebuggenie_successmessage', {duration: 1, pulses: 4});
		} else {
			new Effect.Appear('thebuggenie_successmessage', {queue: {position: 'end', scope: TBG.effect_queues.successmessage, limit: 2}, duration: 0.2});
		}
		new Effect.Fade('thebuggenie_successmessage', {queue: {position: 'end', scope: TBG.effect_queues.successmessage, limit: 2}, delay: 10, duration: 0.2});
	} else if ($('thebuggenie_successmessage').visible()) {
		Effect.Queues.get(TBG.effect_queues.successmessage).each(function(effect) {effect.cancel();});
		new Effect.Fade('thebuggenie_successmessage', {queue: {position: 'end', scope: TBG.effect_queues.successmessage, limit: 2}, duration: 0.2});
	}
};

TBG.Main.toggleBreadcrumbMenuPopout = function(event) {
	var item = event.findElement('a');
	if (TBG.activated_popoutmenu != undefined && TBG.activated_popoutmenu != item) {
		TBG.Core.toggleBreadcrumbItem(TBG.activated_popoutmenu);
		TBG.activated_popoutmenu = undefined;
	}
	if (item != undefined && item.hasClassName('submenu_activator')) {
		TBG.Core.toggleBreadcrumbItem(item);
		TBG.activated_popoutmenu = item;
	} else {
		TBG.activated_popoutmenu = undefined;
	}
};

/**
 * Convenience function for running an AJAX call and updating / showing / hiding
 * divs on json feedback
 *
 * @param url The URL to call
 * @param options An associated array of options
 */
TBG.Main.AjaxHelper = function(url, options) {
	var params = (options.params) ? options.params : '';
	if (options.form) params = Form.serialize(options.form);
	if (options.additional_params) params += options.additional_params;
	var url_method = (options.url_method) ? options.url_method : "get";
	
	new Ajax.Request(url, {
		asynchronous: true,
		method: url_method,
		parameters: params,
		evalScripts: true,
		onLoading: function () {
			$(options.loading.indicator).show();
			var update_element = (is_string(options.success.update)) ? options.success.update : options.success.update.element;
			if (options.loading.clear && $(update_element)) {
				$(update_element).update('');
			}
			if (options.loading.hide) {
				if (is_string(options.loading.hide) && $(options.loading.hide)) {
					$(options.loading.hide).hide();
				} else {
					options.loading.hide.each(function (element) {
						element.hide();
					});
				}
			}
			if (options.loading.disable && $(options.loading.disable)) {
				$(options.loading.disable).disable();
			}
			if (options.loading.reset && $(options.loading.reset)) {
				$(options.loading.reset).reset();
			}
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			$(options.loading.indicator).hide();
			if (json && json.failed) {
				TBG.Main.failedMessage(json.error);
			} else {
				if (options.success && options.success.update) {
					var content = (json) ? json.content : transport.responseText;
					var update_element = (is_string(options.success.update)) ? options.success.update : options.success.update.element;
					if ($(update_element)) {
						var insertion = (is_string(options.success.update)) ? false : (options.success.insertion) ? options.success.insertion : false;
						if (insertion) {
							$(update_element).insert(content, 'after');
						} else {
							$(update_element).update(content);
						}
					}
					if (json && json.message) {
						TBG.Main.successMessage(json.message);
					}
				} else if (json && (json.title || json.content)) {
					TBG.Main.successMessage(json.title, json.content);
				} else if (json && (json.message)) {
					TBG.Main.successMessage(json.message);
				}
				if (options.success.remove) {
					if (is_string(options.success.remove)) {
						if ($(options.success.remove)) $(options.success.remove).remove();
					} else {
						options.success.remove.each(function(s) {if (is_string(s) && $(s)) $(s).remove();else if ($(s)) s.remove();});
					}
				}
				if (options.success.hide) {
					if (is_string(options.success.hide)) {
						if ($(options.success.hide)) $(options.success.hide).hide();
					} else {
						options.success.hide.each(function(s) {if (is_string(s) && $(s)) $(s).hide();else if ($(s)) s.hide();});
					}
				}
				if (options.success.show) {
					if (is_string(options.success.show)) {
						if ($(options.success.show)) $(options.success.show).show();
					} else {
						options.success.show.each(function(s) {if ($(s)) $(s).show();});
					}
				}
				if (options.success.enable && $(options.success.enable)) {
					$(options.success.enable).enable();
				}
				if (options.success.reset && $(options.success.reset)) {
					$(options.success.reset).reset();
				}
				if (options.success.callback) {
					options.success.callback(json);
				}
			}
		},
		onFailure: function (transport) {
			$(options.loading.indicator).hide();
			var json = (transport.responseJSON) ? transport.responeJSON : undefined;
			if (transport.responseJSON) {
				TBG.Main.failedMessage(json.error);
			} else {
				TBG.Main.failedMessage(transport.responseText);
			}
			if (options.loading && options.loading.disable && $(options.loading.disable)) {
				$(options.loading.disable).enable();
			}
			if (options.failure && options.failure.hide && $(options.failure.hide)) {
				$(options.failure.hide).hide();
			}
			if (options.failure.callback) {
				options.failure.callback(transport);
			}
		},
		onComplete: function (transport) {
			var json = (transport.responseJSON) ? transport.responeJSON : undefined;
			if (options.loading.hide && (options.loading.hide != options.success.hide || !options.success || !options.success.hide) && $(options.loading.hide)) {
				$(options.loading.hide).show();
			}
			if (options.complete && options.complete.callback) {
				options.complete.callback(json);
			}
		}
	});
};

TBG.Main.formSubmitHelper = function(url, form_id) {
	TBG.Main.AjaxHelper(url, {
		form: form_id,
		url_method: 'post',
		loading: {indicator: form_id + '_indicator', disable: form_id + '_button'}, 
		success: {enable: form_id + '_button'}
	});
};

TBG.Main.showFadedBackdrop = function(url) {
	$('fullpage_backdrop').show();
	if (url != undefined) {
		TBG.Main.AjaxHelper(url, {
			url_method: 'post',
			loading: {indicator: 'fullpage_backdrop_indicator', hide: 'fullpage_backdrop'},
			success: {update: 'fullpage_backdrop_content'},
			failure: {hide: 'fullpage_backdrop'}
		});
	}
};

TBG.Main.resetFadedBackdrop = function() {
	$('fullpage_backdrop').hide();
	$('fullpage_backdrop_indicator').show();
	$('fullpage_backdrop_content').update('');
};

TBG.Main.findIdentifiable = function(url, field) {
	TBG.Main.AjaxHelper(url, { 
		form: field + '_form', 
		url_method: 'post', 
		loading: {indicator: field + '_spinning'}, 
		success: {update: field + '_results'}
	});
};

TBG.Main.switchSubmenuTab = function(visibletab, menu)
{
	$(menu).childElements().each(function(item){item.removeClassName('selected');});
	$(visibletab).addClassName('selected');
	$(menu + '_panes').childElements().each(function(item){item.hide();});
	$(visibletab + '_pane').show();
}

TBG.Main.updatePercentageLayout = function(tds, percent)
{
	cc = 0;
	$(tds).childElements().each(function(elm) {
		if ($(tds).childElements().size() == 2) {
			$(tds).childElements().first().style.width = percent + '%';
			$(tds).childElements().last().style.width = (100 - percent) + '%';
		} else {
			elm.removeClassName("percent_filled");
			elm.removeClassName("percent_unfilled");
			switch (true) {
				case (percent > 0 && percent < 100):
					(cc <= percent) ? elm.addClassName("percent_filled") : elm.addClassName("percent_unfilled");
					break;
				case (percent == 0):
					elm.addClassName("percent_unfilled");
					break;
				case (percent == 100):
					elm.addClassName("percent_filled");
					break;
			}
			cc++;
		}
	});
}

TBG.Main.addLink = function(url, target_type, target_id)
{
	TBG.Main.AjaxHelper(url, {
		loading: {
			indicator: 'attach_link_' + target_type + '_' + target_id + '_indicator',
			hide: 'attach_link_' + target_type + '_' + target_id + '_submit'
		},
		success: {
			reset: 'attach_link_' + target_type + '_' + target_id + '_form',
			hide: ['attach_link_' + target_type + '_' + target_id, target_type + '_' + target_id + '_no_links'],
			update: {element: target_type + '_' + target_id + '_links', insertion: true}
		},
		complete: {
			show: 'attach_link_' + target_type + '_' + target_id + '_submit'
		}
	});
}

TBG.Main.removeLink = function(url, target_type, target_id, link_id)
{
	TBG.Main.AjaxHelper(url, {
		url_method: 'post',
		loading: {
			hide: target_type + '_' + target_id + '_links_'+ link_id + '_remove_link',
			indicator: target_type + '_' + target_id + '_links_'+ link_id + '_remove_indicator'
		},
		success: {
			remove: [target_type + '_' + target_id + '_links_' + link_id, target_type + '_' + target_id + '_links_' + link_id + '_remove_confirm'],
			callback: function (json) {
				if ($(json.target_type + '_' + json.target_id + '_links').childElements().size() == 0) {
					$(json.target_type + '_' + json.target_id + '_no_links').show();
				}
			}
		},
		failure: {
			show: target_type + '_' + target_id + '_links_'+ link_id + '_remove_link'
		}
	});
}

TBG.Main.reloadImage = function(id) {
   var src = $(id).src;
   var date = new Date();
   
   src = (src.indexOf('?') >= 0) ? src.substr(0, pos) : src;
   $(id).src = src + '?v=' + date.getTime();
   
   return false;
}

TBG.Issues.showWorkflowTransition = function(transition_id) {
	TBG.Main.showFadedBackdrop();
	$('fullpage_backdrop_indicator').hide();
	var workflow_div = $('issue_transition_container_' + transition_id).clone(true);
	$('fullpage_backdrop_content').update(workflow_div);
	workflow_div.appear({duration: 0.2});
};

TBG.Issues.addUserStoryTask = function(url, story_id, mode)
{
	var prefix = (mode == 'scrum') ? 'scrum_story_' + story_id : 'viewissue';
	var indicator_prefix = (mode == 'scrum') ? 'add_task_' + story_id : 'add_task';
	var success_arr = {};
	
	if (mode == scrum) {
		success_arr = {
			reset: prefix + '_add_task_form',
			hide: 'no_tasks_' + story_id,
			callback: function(json) {
				$(prefix + '_tasks').insert({bottom: json.content});
				$(prefix + '_tasks_count').update(json.count);
			}
		};
	} else {
		success_arr = {
			reset: prefix + '_add_task_form',
			hide: 'no_child_issues',
			callback: function(json) {
				$('related_child_issues_inline').insert({bottom: json.content});
				if (json.comment) {
					$('comments_box').insert({bottom: json.comment});
					if ($('comments_box').childElements().size() != 0) {
						$('comments_none').hide();
					}
				}
			}
		};
	}
	
	TBG.Main.AjaxHelper(url, {
		form: prefix + '_add_task_form',
		loading: {indicator: indicator_prefix + '_indicator'},
		success: success_arr
	});
}

TBG.Search.addFilter = function(url)
{
	TBG.Main.AjaxHelper(url, {
		form: 'add_filter_form',
		additional_params: '&key=' + $('max_filters').value,
		url_method: 'post',
		loading: {indicator: 'add_filter_indicator'},
		success: {
			update: {element: 'search_filters_list', insertion: true},
			callback: function() {
				$('max_filters').value++;
			}
		}
	});
}