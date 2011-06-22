
function _updateDivWithJSONFeedback(url, update_element, indicator, insertion, clear_update_element_before_loading, hide_element_while_loading, hide_elements_on_success, show_elements_on_success, url_method, params, onsuccess_callback, onfailure_callback, oncomplete_callback) {}
function _postFormWithJSONFeedback(url, formname, indicator, hide_divs_when_done, update_div, insertion, show_divs_when_done, update_form_elm, onsuccess_callback, onfailure_callback, oncomplete_callback) {}

function is_string(element) {
    return (typeof element == 'string');
}

// The core js class used by thebuggenie
var TBG = {
	Core: {}, // The "Core" namespace is for functions used by thebuggenie core, not to be invoked outside the js class
	Main: { // The "Main" namespace contains regular functions in use across the site
		Helpers: {
			Message: {},
			Backdrop: {}
		}, 
		Profile: {},
		Comment: {}
	},
	Project: {
		Statistics: {},
		Milestone: {},
		Timeline: {},
		Scrum: {
			Story: {},
			Sprint: {}
		}
	},
	Config: {
		Permissions: {}
	}, // The "Config" namespace contains functions used in the configuration section
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

/**
 * Initializes the autocompleter
 */
TBG.Core._initializeAutocompleter = function() {
	new Ajax.Autocompleter(
		"searchfor",
		"searchfor_autocomplete_choices",
		TBG.autocompleter_url,
		{
			paramName: "filters[text][value]",
			minChars: 2,
			indicator: 'quicksearch_indicator',
			afterUpdateElement: TBG.Core._extractAutocompleteValue
		}
	);
};

/**
 * Helper function to extract url from autocomplete response container
 */
TBG.Core._extractAutocompleteValue = function(elem, value) {
	var elements = value.select('.url');
	if (elements.size() == 1) {
		window.location = elements[0].innerHTML.unescapeHTML();
		$('quicksearch_indicator').show();
		$('searchfor').blur();
	}
};

/**
 * Monitors viewport resize to adapt backdrops and dashboard containers
 */
TBG.Core._resizeWatcher = function() {
	if (($('fullpage_backdrop') && $('fullpage_backdrop').visible()) || ($('attach_file') && $('attach_file').visible())) {
		var docheight = document.viewport.getHeight();
		var backdropheight = $('backdrop_detail_content').getHeight();
		if (backdropheight > (docheight - 100)) {
			$('backdrop_detail_content').setStyle({height: docheight - 100 + 'px', overflow: 'scroll'});
		} else {
			$('backdrop_detail_content').setStyle({height: 'auto', overflow: ''});
		}
	}
	if ($('dashboard')) {
		var dashboard_width = $('dashboard').getWidth();
		var element_width = (dashboard_width > 600) ? ((dashboard_width / 2) - 5) : (dashboard_width - 5);
		$('dashboard').childElements().each(function(item) {
			item.setStyle({width: element_width + 'px'});
		});
	}
};

/**
 * Toggles one breadcrumb item in the breadcrumb bar
 */
TBG.Core._toggleBreadcrumbItem = function(item) {
	item.up('li').next().toggleClassName('popped_out');
	item.toggleClassName('activated');
};

TBG.Core._detachFile = function(url, file_id, base_id) {
	TBG.Main.Helpers.Ajax(url, {
		loading: {
			indicator: base_id + file_id + '_remove_indicator',
			hide: [base_id + file_id + '_remove_link', 'uploaded_files_'+ file_id + '_remove_link'],
			show: 'uploaded_files_'+ file_id + '_remove_indicator'
		},
		success: {
			remove: [base_id + file_id, 'uploaded_files_' + file_id, base_id + file_id + '_remove_confirm', 'uploaded_files_' + file_id + '_remove_confirm'],
			callback: function(json) {
				if (json.attachmentcount == 0 && $('viewissue_no_uploaded_files')) $('viewissue_no_uploaded_files').show();
				if ($('viewissue_uploaded_attachments_count')) $('viewissue_uploaded_attachments_count').update(json.attachmentcount);
			}
		},
		failure: {
			show: [base_id + file_id + '_remove_link', 'uploaded_files_'+ file_id + '_remove_link'],
			hide: 'uploaded_files_'+ file_id + '_remove_indicator'			
		}
	});
};

TBG.Core._processCommonAjaxPostEvents = function(options) {
	if (options.remove) {
		if (is_string(options.remove)) {
			if ($(options.remove)) $(options.remove).remove();
		} else {
			options.remove.each(function(s) {if (is_string(s) && $(s)) $(s).remove();else if ($(s)) s.remove();});
		}
	}
	if (options.hide) {
		if (is_string(options.hide)) {
			if ($(options.hide)) $(options.hide).hide();
		} else {
			options.hide.each(function(s) {if (is_string(s) && $(s)) $(s).hide();else if ($(s)) s.hide();});
		}
	}
	if (options.show) {
		if (is_string(options.show)) {
			if ($(options.show)) $(options.show).show();
		} else {
			options.show.each(function(s) {if ($(s)) $(s).show();});
		}
	}
	if (options.enable) {
		if (is_string(options.enable)) {
			if ($(options.enable)) $(options.enable).enable();
		} else {
			options.enable.each(function(s) {if ($(s)) $(s).enable();});
		}
	}
	if (options.disable) {
		if (is_string(options.disable)) {
			if ($(options.disable)) $(options.disable).disable();
		} else {
			options.disable.each(function(s) {if ($(s)) $(s).disable();});
		}
	}
	if (options.reset) {
		if (is_string(options.reset)) {
			if ($(options.reset)) $(options.reset).reset();
		} else {
			options.reset.each(function(s) {if ($(s)) $(s).reset();});
		}
	}
	if (options.clear) {
		if (is_string(options.clear)) {
			if ($(options.clear)) $(options.clear).clear();
		} else {
			options.clear.each(function(s) {if ($(s)) $(s).clear();});
		}
	}
};

/**
 * Main initializer function
 * Sets up and initializes autocompleters, watchers, etc
 * 
 * @param {Object} options A {key: value} store with options to set
 */
TBG.initialize = function(options) {
	for(var key in options) {
		TBG[key] = options[key];
	}
	TBG.Core._initializeAutocompleter();
	Event.observe(window, 'resize', TBG.Core._resizeWatcher);
	document.observe('click', TBG.Main.toggleBreadcrumbMenuPopout);
};

/**
 * Clears all popup messages from the effect queue
 */
TBG.Main.Helpers.Message.clear = function() {
	Effect.Queues.get(TBG.effect_queues.successmessage).each(function(effect) {effect.cancel();});
	Effect.Queues.get(TBG.effect_queues.failedmessage).each(function(effect) {effect.cancel();});
	if ($('thebuggenie_successmessage').visible()) {
		$('thebuggenie_successmessage').fade({duration: 0.2});
	}
	if ($('thebuggenie_failuremessage').visible()) {
		$('thebuggenie_failuremessage').fade({duration: 0.2});
	}
};

/**
 * Shows an error popup message
 * 
 * @param title string The title to show
 * @param content string Error details
 */
TBG.Main.Helpers.Message.error = function(title, content) {
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

/**
 * Shows a "success"-style popup message
 * 
 * @param title string The title to show
 * @param content string Message details
 */
TBG.Main.Helpers.Message.success = function(title, content) {
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

/**
 * Convenience function for running an AJAX call and updating / showing / hiding
 * divs on json feedback
 * 
 * Available options:
 *   loading: {} Instructions for the onLoading event
 *   success: {} Instructions for the onSuccess event
 *   failure: {} Instructions for the onComplete event
 *   complete: {} Instructions for the onComplete event
 *   
 *   Common options for all on* events:
 *     hide: string/array A list of / element id(s) to hide
 *     reset: string/array A list of / element id(s) to reset
 *     show: string/array A list of / element id(s) to show
 *     clear: string/array A list of / element id(s) to clear
 *     remove: string/array A list of / element id(s) to remove
 *     enable: string/array A list of / element id(s) to enable
 *     disable: string/array A list of / element id(s) to disable
 *     callback: a function to call at the end of the event. For 
 *		         success/failure/complete events, the callback 
 *		         function retrieves the json object
 *   
 *   The loading.indicator element will be toggled off in the onComplete event 
 *    
 *   Options for the onSuccess event instruction set:
 *     update: either an element id which will receive the value of the 
 *             json.content property or an object with instructions:
 *             
 *     Available instructions for the success "update" object:
 *       element: the id of the element to update
 *       insertion: true / false / ommitted. If "true" the element will get the
 *                  content inserted after the existing content, instead of
 *                  the content replacing the existing content
 *       from: if the json return value does not contain a "content" key, 
 *			   specify which json key should be used
 *
 * @param url The URL to call
 * @param options An associated array of options
 */
TBG.Main.Helpers.Ajax = function(url, options) {
	var params = (options.params) ? options.params : '';
	if (options.form) params = Form.serialize(options.form);
	if (options.additional_params) params += options.additional_params;
	var url_method = (options.url_method) ? options.url_method : 'post';
	
	new Ajax.Request(url, {
		asynchronous: true,
		method: url_method,
		parameters: params,
		evalScripts: true,
		onLoading: function () {
			if (options.loading) {
				if ($(options.loading.indicator)) {
					$(options.loading.indicator).show();
				}
				TBG.Core._processCommonAjaxPostEvents(options.loading);
			}
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json) {
				if (options.success && options.success.update) {
					var json_content_element = (is_string(options.success.update) || options.success.update.from == undefined) ? content : options.success.update.from;
					var content = (json) ? json[json_content_element] : transport.responseText;
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
						TBG.Main.Helpers.Message.success(json.message);
					}
				} else if (json && (json.title || json.content)) {
					TBG.Main.Helpers.Message.success(json.title, json.content);
				} else if (json && (json.message)) {
					TBG.Main.Helpers.Message.success(json.message);
				}
				if (options.success) {
					TBG.Core._processCommonAjaxPostEvents(options.success);
					if (options.success.callback) {
						options.success.callback(json);
					}
				}
			}
		},
		onFailure: function (transport) {
			var json = (transport.responseJSON) ? transport.responeJSON : undefined;
			if (transport.responseJSON) {
				TBG.Main.Helpers.Message.error(json.error);
			} else {
				TBG.Main.Helpers.Message.error(transport.responseText);
			}
			if (options.failure) {
				TBG.Core._processCommonAjaxPostEvents(options.failure);
				if (options.failure.callback) {
					options.failure.callback(transport);
				}
			}
		},
		onComplete: function (transport) {
			$(options.loading.indicator).hide();
			if (options.complete) {
				TBG.Core._processCommonAjaxPostEvents(options.complete);
				if (options.complete.callback) {
					var json = (transport.responseJSON) ? transport.responeJSON : undefined;
					options.complete.callback(json);
				}
			}
		}
	});
};

TBG.Main.Helpers.formSubmit = function(url, form_id) {
	TBG.Main.Helpers.Ajax(url, {
		form: form_id,
		loading: {indicator: form_id + '_indicator', disable: form_id + '_button'}, 
		success: {enable: form_id + '_button'}
	});
};

TBG.Main.Helpers.Backdrop.show = function(url) {
	$('fullpage_backdrop').show();
	if (url != undefined) {
		TBG.Main.Helpers.Ajax(url, {
			loading: {indicator: 'fullpage_backdrop_indicator', hide: 'fullpage_backdrop'},
			success: {update: 'fullpage_backdrop_content'},
			failure: {hide: 'fullpage_backdrop'}
		});
	}
};

TBG.Main.Helpers.Backdrop.reset = function() {
	$('fullpage_backdrop').hide();
	$('fullpage_backdrop_indicator').show();
	$('fullpage_backdrop_content').update('');
};

TBG.Main.Helpers.tabSwitcher = function(visibletab, menu) {
	$(menu).childElements().each(function(item){item.removeClassName('selected');});
	$(visibletab).addClassName('selected');
	$(menu + '_panes').childElements().each(function(item){item.hide();});
	$(visibletab + '_pane').show();
};

TBG.Main.toggleBreadcrumbMenuPopout = function(event) {
	var item = event.findElement('a');
	if (TBG.activated_popoutmenu != undefined && TBG.activated_popoutmenu != item) {
		TBG.Core._toggleBreadcrumbItem(TBG.activated_popoutmenu);
		TBG.activated_popoutmenu = undefined;
	}
	if (item != undefined && item.hasClassName('submenu_activator')) {
		TBG.Core._toggleBreadcrumbItem(item);
		TBG.activated_popoutmenu = item;
	} else {
		TBG.activated_popoutmenu = undefined;
	}
};

TBG.Main.findIdentifiable = function(url, field) {
	TBG.Main.Helpers.Ajax(url, { 
		form: field + '_form', 
		loading: {indicator: field + '_spinning'}, 
		success: {update: field + '_results'}
	});
};

TBG.Main.updatePercentageLayout = function(tds, percent) {
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
};

TBG.Main.addLink = function(url, target_type, target_id) {
	TBG.Main.Helpers.Ajax(url, {
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
};

TBG.Main.removeLink = function(url, target_type, target_id, link_id) {
	TBG.Main.Helpers.Ajax(url, {
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
};

TBG.Main.detachFileFromArticle = function(url, file_id, article_name) {
	TBG.Core._detachFile(url, file_id, 'article_' + article_name + '_files_');
};

TBG.Main.reloadImage = function(id) {
   var src = $(id).src;
   var date = new Date();
   
   src = (src.indexOf('?') >= 0) ? src.substr(0, pos) : src;
   $(id).src = src + '?v=' + date.getTime();
   
   return false;
};

TBG.Main.Profile.updateInformation = function(url) {
	TBG.Main.Helpers.Ajax(url, {
		form: 'profile_information_form',
		loading: {indicator: 'profile_save_indicator'}
	});
};

TBG.Main.Profile.updateModuleSettings = function(url, module_name) {
	TBG.Main.Helpers.Ajax(url, {
		form: 'profile_' + module_name + '_form',
		loading: {indicator: 'profile_' + module_name + '_save_indicator'}
	});
};

TBG.Main.Profile.updateSettings = function(url) {
	TBG.Main.Helpers.Ajax(url, {
		form: 'profile_settings_form',
		loading: {indicator: 'profile_settings_save_indicator'},
		success: {callback: function() {
			($('profile_use_gravatar_yes').checked) ? $('gravatar_change').show() : $('gravatar_change').hide();
		}}
	});
};

TBG.Main.Profile.changePassword = function(url) {
	TBG.Main.Helpers.Ajax(url, {
		form: 'change_password_form',
		loading: {indicator: 'change_password_indicator'},
		success: {reset: 'change_password_form'}
	});
};

TBG.Main.hideInfobox = function(url, boxkey) {
	if ($('close_me_' + boxkey).checked) {
		TBG.Main.Helpers.Ajax(url, {
			loading: {indicator: 'infobox_' + boxkey + '_indicator'}
		});
	}
	$('infobox_' + boxkey).fade({duration: 0.3});
};

TBG.Project.Statistics.get = function(url) {
	TBG.Main.Helpers.Ajax(url, {
		loading: {
			show: 'statistics_main', 
			hide: 'statistics_help',
			callback: function() {
				$('statistics_main_image').src = '';
				for (var cc = 1; cc <= 3; cc++) {
					$('statistics_mini_image_' + cc).src = '';
				}
			}
		},
		success: {
			callback: function(json) {
				$('statistics_main_image').src = json.images.main;
				for (var cc = 1; cc <= 3; cc++) {
					var small_name = 'mini_' + cc + '_small';
					var large_name = 'mini_' + cc + '_large';
					if (json.images[small_name]) {
						$('statistics_mini_image_' + cc).show();
						$('statistics_mini_image_' + cc).src = json.images[small_name];
						$('statistics_mini_' + cc + '_main').setValue(json.images[large_name]);
					} else {
						$('statistics_mini_image_' + cc).hide();
						$('statistics_mini_' + cc + '_main').setValue('');
					}
				}
			}
		},
		failure: {show: 'statistics_help'}
	});
};

TBG.Project.Statistics.toggleImage = function(image) {
	$('statistics_main_image').src = '';
	$('statistics_main_image').src = $('statistics_mini_'+image+'_main').getValue();
};

TBG.Project.Milestone.toggle = function(url, milestone_id) {
	if ($('milestone_' + milestone_id + '_issues').childElements().size() == 0) {
		TBG.Main.Helpers.Ajax(url, {
			loading: {indicator: 'milestone_' + milestone_id + '_indicator'},
			success: {
				update: 'milestone_' + milestone_id + '_issues',
				show: 'milestone_' + milestone_id + '_issues'
			}
		});
	} else {
		$('milestone_' + milestone_id + '_issues').toggle();
	}
};

TBG.Project.Milestone.refresh = function(url, milestone_id) {
	var m_id = milestone_id;
	TBG.Main.Helpers.Ajax(url, {
		loading: {
			indicator: 'milestone_' + milestone_id + '_indicator'
		},
		success: {
			callback: function(json) {
				var must_reload_issue_list = false;
				if (json.percent) {
					TBG.Main.updatePercentageLayout('milestone_'+m_id+'_percent', json.percent);
				}
				if (json.closed_issues && $('milestone_'+m_id+'_closed_issues')) {
					if ($('milestone_'+m_id+'_closed_issues').innerHTML != json.closed_issues) {
						$('milestone_'+m_id+'_closed_issues').update(json.closed_issues);
						must_reload_issue_list = true;
					}
				}
				if (json.assigned_issues && $('milestone_'+m_id+'_assigned_issues')) {
					if ($('milestone_'+m_id+'_assigned_issues').innerHTML != json.assigned_issues) {
						$('milestone_'+m_id+'_assigned_issues').update(json.assigned_issues);
						must_reload_issue_list = true;
					}
				}
				if (json.assigned_points && $('milestone_'+m_id+'_assigned_points')) {
					if ($('milestone_'+m_id+'_assigned_points').innerHTML != json.assigned_points) {
						$('milestone_'+m_id+'_assigned_points').update(json.assigned_points);
						must_reload_issue_list = true;
					}
				}
				if (json.closed_points && $('milestone_'+m_id+'_closed_points')) {
					if ($('milestone_'+m_id+'_closed_points').innerHTML != json.closed_points) {
						$('milestone_'+m_id+'_closed_points').update(json.closed_points);
						must_reload_issue_list = true;
					}
				}
				if (json.date_string && $('milestone_'+m_id+'_date_string')) {
					if ($('milestone_'+m_id+'_date_string').innerHTML != json.date_string) {
						$('milestone_'+m_id+'_date_string').update(json.date_string);
						must_reload_issue_list = true;
					}
				}
				if (must_reload_issue_list) {
					$('milestone_'+m_id+'_changed').show();
					$('milestone_'+m_id+'_issues').update('');
				}
				
			}
		}
	});
};

TBG.Project.Timeline.update = function(url) {
	TBG.Main.Helpers.Ajax(url, {
		url_method: 'get',
		additional_params: {offset: $('timeline_offset').getValue()},
		loading: {
			indicator: 'timeline_indicator',
			hide: 'timeline_more_link'
		},
		success: {
			update: {element: 'timeline', insertion: true},
			show: 'timeline_more_link',
			callback: function(json) {
				$('timeline_offset').setValue(json.offset)
			}
		}
	});
};

TBG.Project.Scrum.Sprint.add = function(url, assign_url)
{
	TBG.Main.Helpers.Ajax(url, {
		form: 'add_sprint_form',
		loading: {indicator: 'sprint_add_indicator'},
		success: {
			reset: 'add_sprint_form',
			hide: 'no_sprints',
			update: {element: 'scrum_sprints', insertion: true},
			callback: function(json) {
				Droppables.add('scrum_sprint_' + json.sprint_id, {hoverclass: 'highlighted', onDrop: function (dragged, dropped, event) {TBG.Project.Scrum.Story.Assign(assign_url, dragged, dropped)}});
			}
		}
	});
}

TBG.Project.Scrum.Story.add = function(url)
{
	TBG.Main.Helpers.Ajax(url, {
		form: 'add_user_story_form',
		loading: {indicator: 'user_story_add_indicator'},
		success: {
			reset: 'add_user_story_form',
			update: {element: 'scrum_sprint_0_list', insertion: true},
			hide: 'scrum_sprint_0_unassigned',
			callback: function(json) {
				new Draggable('scrum_story_' + json.story_id, {revert: true});
			}
		}
	});
}

TBG.Project.Scrum.Story.assign = function(url, dragged, dropped)
{
	TBG.Main.Helpers.Ajax(url, {
		params: {story_id: $(dragged.id + '_id').getValue(), sprint_id: $(dropped.id + '_id').getValue()},
		loading: {indicator: dropped.id + '_indicator'},
		success: {
			callback: function(json) {
				$(dropped.id + '_list').insert(Element.remove(dragged), {insertion: Insertion.Bottom, queue: 'end'});
				$('scrum_sprint_' + json.old_sprint_id + '_issues').update(json.old_issues);
				$('scrum_sprint_' + json.new_sprint_id + '_issues').update(json.new_issues);
				$('scrum_sprint_' + json.old_sprint_id + '_estimated_points').update(json.old_estimated_points);
				$('scrum_sprint_' + json.new_sprint_id + '_estimated_points').update(json.new_estimated_points);
				$('scrum_sprint_' + json.old_sprint_id + '_estimated_hours').update(json.old_estimated_hours);
				$('scrum_sprint_' + json.new_sprint_id + '_estimated_hours').update(json.new_estimated_hours);
				($('scrum_sprint_' + json.old_sprint_id + '_list').childElements().size() == 0) ? $('scrum_sprint_' + json.old_sprint_id + '_unassigned').show() : $('scrum_sprint_' + json.old_sprint_id + '_unassigned').hide();
				($('scrum_sprint_' + json.new_sprint_id + '_list').childElements().size() == 0) ? $('scrum_sprint_' + json.new_sprint_id + '_unassigned').show() : $('scrum_sprint_' + json.new_sprint_id + '_unassigned').hide();
			}
		}
	});
}

TBG.Project.Scrum.Story.setColor = function(url, story_id, color)
{
	TBG.Main.Helpers.Ajax(url, {
		parameters: {color: color},
		loading: {indicator: 'color_selector_' + story_id + '_indicator'},
		success: {
			callback: function() {
				$('story_color_' + story_id).style.backgroundColor = color;
			}
		},
		complete: {
			hide: 'color_selector_' + story_id
		}
	});
}

TBG.Project.Scrum.Story.setEstimates = function(url, story_id)
{
	var params = {};
	if ($('scrum_story_' + story_id + '_points_input') && $('scrum_story_' + story_id + '_hours_input')) {
		params = {estimated_points: $('scrum_story_' + story_id + '_points_input').getValue(), estimated_hours: $('scrum_story_' + story_id + '_hours_input').getValue()};
	} else if ($('scrum_story_' + story_id + '_hours_input')) {
		params = {estimated_hours: $('scrum_story_' + story_id + '_hours_input').getValue()};
	} else if ($('scrum_story_' + story_id + '_points_input')) {
		params = {estimated_points: $('scrum_story_' + story_id + '_points_input').getValue()};
	}
	
	TBG.Main.Helpers.Ajax(url, {
		parameters: params,
		loading: {indicator: 'point_selector_' + story_id + '_indicator'},
		success: {
			callback: function(json) {
				if ($('scrum_story_' + story_id + '_points')) $('scrum_story_' + story_id + '_points').update(json.points);
				if ($('scrum_story_' + story_id + '_hours')) {
					$('scrum_story_' + story_id + '_hours').update(json.hours);
					if ($('selected_burndown_image')) TBG.Main.reloadImage('selected_burndown_image');
				}
				$('scrum_sprint_' + json.sprint_id + '_estimated_points').update(json.new_estimated_points);
				$('scrum_sprint_' + json.sprint_id + '_remaining_points').update(json.new_remaining_points);
				$('scrum_sprint_' + json.sprint_id + '_estimated_hours').update(json.new_estimated_hours);
				$('scrum_sprint_' + json.sprint_id + '_remaining_hours').update(json.new_remaining_hours);
			}
		},
		complete: {hide: 'scrum_story_' + story_id + '_estimation'}
	});
}

TBG.Main.Comment.remove = function(url, comment_id) {
	TBG.Main.Helpers.Ajax(url, {
		loading: {
			indicator: 'comment_delete_indicator_' + comment_id,
			hide: 'comment_delete_controls_' + comment_id
		},
		success: {
			remove: ['comment_delete_indicator_' + comment_id, 'comment_delete_confirm_' + comment_id, 'comment_' + comment_id],
			callback: function() {
				if ($('comments_box').childElements().size() == 0) $('comments_none').show();
			}
		},
		failure: {
			show: 'comment_delete_controls_' + comment_id
		}
	});
};

TBG.Main.Comment.update = function(url, cid) {
	TBG.Main.Helpers.Ajax(url, {
		form: 'comment_edit_form_' + cid,
		loading: {
			indicator: 'comment_edit_controls_' + cid,
			hide: 'comment_edit_controls_' + cid
		},
		success: {
			hide: ['comment_edit_indicator_' + cid, 'comment_edit_' + cid],
			show: ['comment_view_' + cid, 'comment_edit_controls_' + cid],
			update: {element: 'comment_' + cid + '_body', from: 'comment_body'}
		},
		failure: {
			show: ['comment_edit_controls_' + cid]
		}
	});
};

TBG.Main.Comment.add = function(url, commentcount_span) {
	TBG.Main.Helpers.Ajax(url, {
		form: 'comment_form',
		loading: {
			indicator: 'comment_add_indicator',
			hide: 'comment_add_controls'
		},
		success: {
			hide: ['comment_add_indicator', 'comment_add'],
			show: ['comment_add_button', 'comment_add_controls'],
			clear: 'comment_bodybox',
			update: {element: 'comments_box', insertion: true, from: 'comment_data'},
			callback: function(json) {
				if ($('comment_form').serialize(true).comment_save_changes == '1') {
					window.location = json.continue_url;
				} else if ($('comments_box').childElements().size() != 0) {
					$('comments_none').hide();
				}
				$('comment_visibility').setValue(1);
				$(commentcount_span).update(json.commentcount);
			}
		},
		failure: {
			show: 'comment_add_controls'
		}
	});
};

TBG.Config.Permissions.set = function(url, field) {
	TBG.Main.Helpers.Ajax(url, {
		loading: {indicator: field + '_indicator'}
	});
};

TBG.Issues.showWorkflowTransition = function(transition_id) {
	TBG.Main.Helpers.Backdrop.show();
	$('fullpage_backdrop_indicator').hide();
	var workflow_div = $('issue_transition_container_' + transition_id).clone(true);
	$('fullpage_backdrop_content').update(workflow_div);
	workflow_div.appear({duration: 0.2});
};

TBG.Issues.addUserStoryTask = function(url, story_id, mode) {
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
	
	TBG.Main.Helpers.Ajax(url, {
		form: prefix + '_add_task_form',
		loading: {indicator: indicator_prefix + '_indicator'},
		success: success_arr
	});
};

TBG.Issues.findRelated = function(url) {
	TBG.Main.Helpers.Ajax(url, {
		form: 'viewissue_find_issue_form',
		loading: {indicator: 'find_issue_indicator'},
		success: {update: 'viewissue_relation_results'}
	});
	return false;
};

TBG.Issues.findDuplicate = function(url) {
	TBG.Main.Helpers.Ajax(url, {
		form: 'viewissue_find_issue_form',
		loading: {indicator: 'find_issue_indicator'},
		success: {update: 'viewissue_duplicate_results'}
	});
	return false;
};

TBG.Issues.relate = function(url) {
	var hide_div = ($('relate_issue_with_selected').getValue() == 'relate_children') ? 'no_child_issues' : 'no_parent_issues';
	var update_div = ($('relate_issue_with_selected').getValue() == 'relate_children') ? 'related_child_issues_inline' : 'related_parent_issues_inline';
	
	TBG.Main.Helpers.Ajax(url, {
		form: 'viewissue_relate_issues_form',
		loading: {indicator: 'relate_issues_indicator'},
		success: {
			update: {element: update_div, insertion: true}, 
			hide: hide_div
		}
	});
	return false;
};

TBG.Issues._addVote = function(url, direction) {
	var opp_direction = (direction == 'up') ? 'down' : 'up';
	
	TBG.Main.Helpers.Ajax(url, {
		loading: {
			indicator: 'vote_' + direction + '_indicator', 
			hide: 'vote_' + direction + '_link'},
		success: {
			update: 'issue_votes',
			hide: ['vote_' + direction + '_link', 'vote_' + opp_direction + '_faded'],
			show: ['vote_' + direction + '_faded', 'vote_' + opp_direction + '_link']
		}
	});
};

TBG.Issues.voteUp = function(url) {
	TBG.Issues._addVote(url, 'up');
};

TBG.Issues.voteDown = function(url) {
	TBG.Issues._addVote(url, 'down');
};

TBG.Search.addFilter = function(url) {
	TBG.Main.Helpers.Ajax(url, {
		form: 'add_filter_form',
		additional_params: '&key=' + $('max_filters').value,
		loading: {indicator: 'add_filter_indicator'},
		success: {
			update: {element: 'search_filters_list', insertion: true},
			callback: function() {
				$('max_filters').value++;
			}
		}
	});
};

TBG.Search.removeFilter = function(key) {
	$('filter_' + key).remove();
	if ($('search_filters_list').childElements().size() == 0) {
		$('max_filters').value = 0;
	}
};

TBG.Search.deleteSavedSearch = function(url, id) {
	TBG.Main.Helpers.Ajax(url, {
		loading: {indicator: 'delete_search_' + id + '_indicator'},
		success: {hide: 'saved_search_' + id + '_container'}
	});
};

TBG.Search.toPage = function(url, parameters, offset) {
	parameters += '&offset=' + offset;
	TBG.Main.Helpers.Ajax(url, {
		params: parameters,
		loading: {indicator: 'paging_spinning'},
		success: {update: 'search_results'}
	});
};