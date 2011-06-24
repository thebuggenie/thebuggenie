
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
		Profile: {
			Dashboard: {
				View: {}
			}
		},
		Comment: {},
		Link: {},
		Login: {}
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
	Issues: {
		Link: {},
		File: {},
		Field: {
			Updaters: {}
		},
		Affected: {}
	}, // The "Issues" namespace contains functions used in direct relation to issues
	Search: {
		Filter: {}
	}, // The "Search" namespace contains functions related to searching
	Subscriptions: {}, // The "Subscription" namespace contains functionality related to subscribing to - and publishing js events
	effect_queues: {
		successmessage: 'TBG_successmessage',
		failedmessage: 'TBG_failedmessage'
	},
	activated_popoutmenu: undefined,
	autocompleter_url: undefined,
	available_fields: ['description', 'user_pain', 'reproduction_steps', 'category', 'resolution', 'priority', 'reproducability', 'percent_complete', 'severity', 'edition', 'build', 'component', 'estimated_time', 'spent_time', 'milestone']
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
	TBG.Main.Helpers.ajax(url, {
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
TBG.Main.Helpers.ajax = function(url, options) {
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
				if (json.forward != undefined) {
					document.location = json.forward;
				} else {
					if (options.success && options.success.update) {
						var json_content_element = (is_string(options.success.update) || options.success.update.from == undefined) ? 'content' : options.success.update.from;
						console.log('updating ');
						console.log(json_content_element);
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
	TBG.Main.Helpers.ajax(url, {
		form: form_id,
		loading: {indicator: form_id + '_indicator', disable: form_id + '_button'}, 
		success: {enable: form_id + '_button'}
	});
};

TBG.Main.Helpers.Backdrop.show = function(url) {
	$('fullpage_backdrop').show();
	if (url != undefined) {
		TBG.Main.Helpers.ajax(url, {
			loading: {indicator: 'fullpage_backdrop_indicator'},
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
	TBG.Main.Helpers.ajax(url, { 
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

TBG.Main.Link.add = function(url, target_type, target_id) {
	TBG.Main.Helpers.ajax(url, {
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

TBG.Main.Link.remove = function(url, target_type, target_id, link_id) {
	TBG.Main.Helpers.ajax(url, {
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
	TBG.Main.Helpers.ajax(url, {
		form: 'profile_information_form',
		loading: {indicator: 'profile_save_indicator'}
	});
};

TBG.Main.Profile.updateModuleSettings = function(url, module_name) {
	TBG.Main.Helpers.ajax(url, {
		form: 'profile_' + module_name + '_form',
		loading: {indicator: 'profile_' + module_name + '_save_indicator'}
	});
};

TBG.Main.Profile.updateSettings = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'profile_settings_form',
		loading: {indicator: 'profile_settings_save_indicator'},
		success: {callback: function() {
			($('profile_use_gravatar_yes').checked) ? $('gravatar_change').show() : $('gravatar_change').hide();
		}}
	});
};

TBG.Main.Profile.changePassword = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'change_password_form',
		loading: {indicator: 'change_password_indicator'},
		success: {reset: 'change_password_form'}
	});
};

TBG.Main.Profile.Dashboard.View.swap = function(source_elm)
{
	source_elm = $(source_elm);
	var target_elm = source_elm.up('li').down('span');
	
	var orig_text = target_elm.innerHTML;
	var orig_id = target_elm.id
	
	target_elm.update(source_elm.innerHTML);
	target_elm.id = source_elm.id;
	
	source_elm.update(orig_text);
	source_elm.id = orig_id;
	
	source_elm.up('li').toggleClassName('verylightyellow');
	source_elm.up('li').toggleClassName('mediumgrey');

if (target_elm.hasClassName('template_view')) { 
		target_elm.removeClassName('template_view');
		source_elm.remove();
	}
}

TBG.Main.Profile.Dashboard.View.add = function()
{
	var element_view = $('view_default').clone(true);
	element_view.id = 'view_' + new Date().getTime();
	$('views_list').insert(element_view);
	element_view = null;
	
	Sortable.create('views_list');
}

TBG.Main.Profile.Dashboard.save = function(url)
{
	var parameters = 'id=';
	$('views_list').select('li').each(function (element) {
		parameters = parameters + element.down('span.dashboard_view_data').id + ';';
	});
	
	TBG.Main.Helpers.ajax(url, {
		params: parameters,
		loading: {
			indicator: 'save_dashboard_indicator',
			hide: 'save_dashboard'
		},
		complete: {show: 'save_dashboard'}
	});
}

TBG.Main.hideInfobox = function(url, boxkey) {
	if ($('close_me_' + boxkey).checked) {
		TBG.Main.Helpers.ajax(url, {
			loading: {indicator: 'infobox_' + boxkey + '_indicator'}
		});
	}
	$('infobox_' + boxkey).fade({duration: 0.3});
};

TBG.Main.Comment.remove = function(url, comment_id) {
	TBG.Main.Helpers.ajax(url, {
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
	TBG.Main.Helpers.ajax(url, {
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
	TBG.Main.Helpers.ajax(url, {
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

TBG.Project.Statistics.get = function(url) {
	TBG.Main.Helpers.ajax(url, {
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
		TBG.Main.Helpers.ajax(url, {
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
	TBG.Main.Helpers.ajax(url, {
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
	TBG.Main.Helpers.ajax(url, {
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
	TBG.Main.Helpers.ajax(url, {
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
	TBG.Main.Helpers.ajax(url, {
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
	TBG.Main.Helpers.ajax(url, {
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
	TBG.Main.Helpers.ajax(url, {
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
	
	TBG.Main.Helpers.ajax(url, {
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

TBG.Config.Permissions.set = function(url, field) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: field + '_indicator'}
	});
};

/**
 * This function updates available issue reporting fields on page to match 
 * those returned by thebuggenie
 */
TBG.Issues.updateFields = function(url)
{
	if ($('issuetype_id').getValue() != 0) {
		$('issuetype_list').hide();
		$('issuetype_dropdown').show();
	}
	if ($('project_id').getValue() != 0 && $('issuetype_id').getValue() != 0) {
		$('report_more_here').hide();
		$('report_form').show();
		
		TBG.Main.Helpers.ajax(url, {
			loading: {indicator: 'report_issue_more_options_indicator'},
			parameters: {project_id: $('project_id').getValue(), issuetype_id: $('issuetype_id').getValue()},
			success: {
				callback: function(json) {
					json.available_fields.each(function (fieldname, key)
					{
						if ($(fieldname + '_div')) {
							if (json.fields[fieldname]) {
								if (json.fields[fieldname].values) {
									var prev_val;
									if ($(fieldname + '_additional') && $(fieldname + '_additional_div').visible()) {
										prev_val = $(fieldname + '_id_additional').getValue();
									} else if ($(fieldname + '_div') && $(fieldname + '_div').visible()) {
										prev_val = $(fieldname + '_id').getValue();
									}
								}
								if (json.fields[fieldname].additional && $(fieldname + '_additional')) {
									$(fieldname + '_additional').show();
									$(fieldname + '_div').hide();
									if ($(fieldname + '_id_additional')) $(fieldname + '_id_additional').enable();
									if ($(fieldname + '_value_additional')) $(fieldname + '_value_additional').enable();
									if ($(fieldname + '_id')) $(fieldname + '_id').disable();
									if ($(fieldname + '_value')) $(fieldname + '_value').disable();
									if (json.fields[fieldname].values) {
										$(fieldname + '_id_additional').update('');
										for (var opt in json.fields[fieldname].values) {
											$(fieldname + '_id_additional').insert('<option value="'+opt+'">'+json.fields[fieldname].values[opt]+'</option>');
										}
										$(fieldname + '_id_additional').setValue(prev_val);
									}
								} else {
									$(fieldname + '_div').show();
									if ($(fieldname + '_id')) $(fieldname + '_id').enable();
									if ($(fieldname + '_value')) $(fieldname + '_value').enable();
									if ($(fieldname + '_id_additional')) $(fieldname + '_id_additional').disable();
									if ($(fieldname + '_value_additional')) $(fieldname + '_value_additional').disable();
									if ($(fieldname + '_additional')) $(fieldname + '_additional').hide();
									if (json.fields[fieldname].values) {
										$(fieldname + '_id').update('');
										for (var opt in json.fields[fieldname].values) {
											$(fieldname + '_id').insert('<option value="'+opt+'">'+json.fields[fieldname].values[opt]+'</option>');
										}
										$(fieldname + '_id').setValue(prev_val);
									}
								}
								(json.fields[fieldname].required) ? $(fieldname + '_label').addClassName('required') : $(fieldname + '_label').removeClassName('required');
							} else {
								$(fieldname + '_div').hide();
								if ($(fieldname + '_id')) $(fieldname + '_id').disable();
								if ($(fieldname + '_value')) $(fieldname + '_value').disable();
								if ($(fieldname + '_additional')) $(fieldname + '_additional').hide();
								if ($(fieldname + '_id_additional')) $(fieldname + '_id_additional').disable();
								if ($(fieldname + '_value_additional')) $(fieldname + '_value_additional').disable();
							}
						}
					});				
				}
			}
		});
	} else {
		$('report_form').hide();
		$('report_more_here').show();
	}
	
}

/**
 * Displays the workflow transition popup dialog
 */
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
	
	TBG.Main.Helpers.ajax(url, {
		form: prefix + '_add_task_form',
		loading: {indicator: indicator_prefix + '_indicator'},
		success: success_arr
	});
};

TBG.Issues.findRelated = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'viewissue_find_issue_form',
		loading: {indicator: 'find_issue_indicator'},
		success: {update: 'viewissue_relation_results'}
	});
	return false;
};

TBG.Issues.findDuplicate = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'viewissue_find_issue_form',
		loading: {indicator: 'find_issue_indicator'},
		success: {update: 'viewissue_duplicate_results'}
	});
	return false;
};

TBG.Issues.relate = function(url) {
	var hide_div = ($('relate_issue_with_selected').getValue() == 'relate_children') ? 'no_child_issues' : 'no_parent_issues';
	var update_div = ($('relate_issue_with_selected').getValue() == 'relate_children') ? 'related_child_issues_inline' : 'related_parent_issues_inline';
	
	TBG.Main.Helpers.ajax(url, {
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
	
	TBG.Main.Helpers.ajax(url, {
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

TBG.Issues.toggleFavourite = function(url, issue_id)
{
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'issue_favourite_indicator_' + issue_id,
			hide: ['issue_favourite_normal_' + issue_id, 'issue_favourite_faded_' + issue_id]
		},
		success: {
			callback: function(json) {
				if (json.starred) {
					$('issue_favourite_faded_' + issue_id).hide();
					$('issue_favourite_indicator_' + issue_id).hide();
					$('issue_favourite_normal_' + issue_id).show();
				} else {
					$('issue_favourite_normal_' + issue_id).hide();
					$('issue_favourite_indicator_' + issue_id).hide();
					$('issue_favourite_faded_' + issue_id).show();
				}
			}
		}
	});
}

TBG.Issues.Link.add = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'attach_link_form',
		loading: {
			indicator: 'attach_link_indicator',
			hide: 'attach_link_submit'
		},
		success: {
			reset: 'attach_link_form',
			hide: ['attach_link', 'viewissue_no_uploaded_files'],
			update: {element: 'viewissue_uploaded_links', insertion: true},
			callback: function(json) {
				$('viewissue_uploaded_attachments_count').update(json.attachmentcount);
			}
		},
		complete: {
			show: 'attach_link_submit'
		}
	});
}

TBG.Issues.Link.remove = function(url, link_id) {
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'viewissue_links_'+ link_id + '_remove_indicator',
			hide: 'viewissue_links_'+ link_id + '_remove_link'
		},
		success: {
			remove: ['viewissue_links_' + link_id, 'viewissue_links_' + link_id + '_remove_confirm'],
			callback: function(json) {
				if (json.attachmentcount == 0) $('viewissue_no_uploaded_files').show();
				$('viewissue_uploaded_attachments_count').update(json.attachmentcount);
			}
		},
		complete: {
			show: 'viewissue_links_'+ link_id + '_remove_link'
		}
	});
}

TBG.Issues.File.remove = function(url, file_id) {
	TBG.Core._detachFile(url, file_id, 'viewissue_files_');
}

TBG.Issues.Field.setPercent = function(url, mode) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'percent_spinning'},
		success: {
			callback: function(json) {
				TBG.Main.updatePercentageLayout('percentage_tds', json.percent);
				(mode == 'set') ? TBG.Issues.markAsChanged('percent') : TBG.Issues.markAsUnchanged('percent');
			}
		}
	});
}

TBG.Issues.Field.Updaters.dualFromJSON = function(dualfield, field) {
	if (dualfield.id == 0) {
		$(field + '_table').hide();
		$('no_' + field).show();
	} else {
		$(field + '_content').update(dualfield.name);
		if (field == 'status') $('status_color').setStyle({backgroundColor: dualfield.color});
		else if (field == 'issuetype') $('issuetype_image').src = dualfield.src;
		$('no_' + field).hide();
		$(field + '_table').show();
	}
}

TBG.Issues.Field.Updaters.fromObject = function(object, field) {
	if ((Object.isUndefined(object.id) == false && object.id == 0) || (object.value && object.value == '')) {
		$(field + '_name').hide();
		$('no_' + field).show();
	} else {
		$(field + '_name').update(object.name);
		$('no_' + field).hide();
		$(field + '_name').show();
	}
}

TBG.Issues.Field.Updaters.timeFromObject = function(object, values, field) {
	if (object.id == 0) {
		$(field + '_name').hide();
		$('no_' + field).show();
	} else {
		$(field + '_name').update(object.name);
		$('no_' + field).hide();
		$(field + '_name').show();
	}
	['points', 'hours', 'days', 'weeks', 'months'].each(function(unit) {
		$(field + '_' + unit).setValue(values[unit]);
	});
}

TBG.Issues.Field.Updaters.allVisible = function(visible_fields) {
	TBG.available_fields.each(function (field) 
	{
		if ($(field + '_field')) {
			if (visible_fields[field] != undefined)  {
				$(field + '_field').show();
				if ($(field + '_additional')) $(field + '_additional').show();
			} else {
				$(field + '_field').hide();
				if ($(field + '_additional')) $(field + '_additional').hide();
			}
		}
	});
}

/**
 * This function is triggered every time an issue is updated via the web interface
 * It sends a request that performs the update, and gets JSON back
 * 
 * Depending on the JSON return value, it updates fields, shows/hides boxes on the 
 * page, and sets some class values
 * 
 * @param url The URL to request
 * @param field The field that is being changed
 * @param serialize_form Whether a form is being serialized
 */
TBG.Issues.Field.set = function(url, field, serialize_form) {
	var post_form = undefined;
	if (['description', 'reproduction_steps', 'title'].indexOf(field)) {
		post_form = field + '_form';
	} else if (serialize_form != undefined) {
		post_form = serialize_form + '_form';
	}
	
	var loading_show = (field == 'issuetype') ? 'issuetype_indicator_fullpage' : undefined;
	
	TBG.Main.Helpers.ajax(url, {
		form: post_form,
		loading: {
			indicator: field + '_spinning',
			clear: field + '_change_error',
			hide: field + '_change_error',
			show: loading_show
		},
		success: {
			callback: function(json) {
				if (json.field != undefined)
				{
					if (field == 'status' || field == 'issuetype') TBG.Issues.Field.Updaters.dualFromJSON(json.field, field);
					else TBG.Issues.Field.Updaters.fromObject(json.field, field);
					
					if (field == 'issuetype') TBG.Issues.Field.Updaters.allVisible(json.visible_fields);
					else if (field == 'pain_bug_type' || field == 'pain_likelihood' || field == 'pain_effect')
					{
						$('issue_user_pain').update(json.user_pain);
						if (json.user_pain_diff_text != '') {
							$('issue_user_pain_calculated').update(json.user_pain_diff_text);
							$('issue_user_pain_calculated').show();
						} else {
							$('issue_user_pain_calculated').hide();
						}
					}
				}
				(json.changed == true) ? TBG.Issues.markAsChanged(field) : TBG.Issues.markAsUnchanged(field);
			},
			hide: field + '_change'
		},
		failure: {
			update: field + '_change_error',
			show: field + '_change_error',
			callback: function(json) {
				new Effect.Pulsate($(field + '_change_error'));
			}
		}
	});
}

TBG.Issues.Field.setTime = function(url, field) {
	TBG.Main.Helpers.ajax(url, {
		form: field + '_form',
		loading: {
			indicator: field + '_spinning',
			clear: field + '_change_error',
			hide: field + '_change_error'
		},
		success: {
			callback: function(json) {
				TBG.Issues.Field.Updaters.timeFromObject(json.field, json.values, field);
			},
			hide: field + '_change'
		},
		failure: {
			update: field + '_change_error',
			show: field + '_change_error',
			callback: function(json) {
				new Effect.Pulsate($(field + '_change_error'));
			}
		}
	});
}

TBG.Issues.Field.revert = function(url, field)
{
	var loading_show = (field == 'issuetype') ? 'issuetype_indicator_fullpage' : undefined;
	
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: field + '_undo_spinning',
			show: loading_show
		},
		success: {
			callback: function(json) {
				if (json.field != undefined) {
					if (field == 'status' || field == 'issuetype') TBG.Issues.Field.Updaters.dualFromJSON(json.field, field);
					else if (field == 'estimated_time' || field == 'spent_time') TBG.Issues.Field.Updaters.timeFromObject(json.field, json.values, field);
					else TBG.Issues.Field.Updaters.fromObject(json.field, field);
					
					if (field == 'issuetype') TBG.Issues.Field.Updaters.allVisible(json.visible_fields);
					else if (field == 'description' || field == 'reproduction_steps') $(field + '_form_value').update(json.form_value);
					else if (field == 'pain_bug_type' || field == 'pain_likelihood' || field == 'pain_effect') $('issue_user_pain').update(json.field.user_pain);

					TBG.Issues.markAsUnchanged(field);
				}
				
			}
		},
		complete: {
			hide: loading_show
		}
	});
}

TBG.Issues.markAsChanged = function(field)
{
	if (!$('viewissue_changed').visible()) {
		$('viewissue_changed').show();
		Effect.Pulsate($('viewissue_changed'));
	}
	
	$(field + '_header').addClassName('issue_detail_changed');
	$(field + '_content').addClassName('issue_detail_changed');
	
	if ($('comment_save_changes')) $('comment_save_changes').checked = true;
}

TBG.Issues.markAsUnchanged = function(field)
{
	$(field + '_header').removeClassName('issue_detail_changed');
	$(field + '_header').removeClassName('issue_detail_unmerged');
	$(field + '_content').removeClassName('issue_detail_changed');
	$(field + '_content').removeClassName('issue_detail_unmerged');
	if ($('issue_view').select('.issue_detail_changed').size() == 0) {
		$('viewissue_changed').hide();
		$('viewissue_merge_errors').hide();
		$('viewissue_unsaved').hide();
		if ($('comment_save_changes')) $('comment_save_changes').checked = false;
	}
}

TBG.Issues.Affected.toggleConfirmed = function(url, affected)
{
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'affected_' + affected + '_confirmed_spinner',
			hide: 'affected_' + affected + '_confirmed_icon'
		},
		success: {
			callback: function(json) {
				$('affected_' + affected + '_confirmed_icon').writeAttribute('alt', json.alt);
				$('affected_' + affected + '_confirmed_icon').writeAttribute('src', json.src);
			}
		},
		complete: {
			show: 'affected_' + affected + '_confirmed_icon'
		}
	});
}

TBG.Issues.Affected.remove = function(url, affected)
{
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'affected_' + affected + '_delete_spinner'
		},
		success: {
			update: {element: 'viewissue_affects_count', from: 'itemcount'},
			remove: ['affected_' + affected + '_delete', 'affected_' + affected],
			callback: function(json) {if (json.itemcount == 0) $('no_affected').show();}
		}
	});
}

TBG.Issues.Affected.setStatus = function(url, affected)
{
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'affected_' + affected + '_status_spinning'
		},
		success: {
			callback: function(json) {
				$('affected_' + affected + '_status_colour').setStyle({
					backgroundColor: json.colour,
					fontSize: '1px',
					width: '20px',
					height: '15px',
					marginRight: '2px'
				});
			},
			update: {element: 'affected_' + affected + '_status_name', from: 'name'},
			hide: 'affected_' + affected + '_status_change'
		},
		failure: {
			update: {element: 'affected_' + affected + '_status_error', from: 'error'},
			show: 'affected_' + affected + '_status_error',
			callback: function(json) {
				new Effect.Pulsate($('affected_' + affected + '_status_error'));
			}
		}
	});
}

TBG.Issues.Affected.add = function(url)
{
	TBG.Main.Helpers.ajax(url, {
		form: 'viewissue_add_item_form',
		loading: {
			indicator: 'add_affected_spinning'
		},
		success: {
			callback: function(json) {
				$('viewissue_affects_count').update(json.itemcount);
				if (json.itemcount != 0) $('no_affected').hide();
				TBG.Main.Helpers.Backdrop.reset();
			},
			update: {element: 'affected_list', insertion: true},
			hide: 'add_affected_spinning'
		}
	});
}

TBG.Issues.updateWorkflowAssignee = function(url, assignee_id, assignee_type, teamup)
{
	teamup = (teamup == undefined) ? 0 : 1;
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'popup_assigned_to_name_indicator',
			hide: 'popup_no_assigned_to',
			show: 'popup_assigned_to_name'
		},
		success: {
			update: 'popup_assigned_to_name'
		},
		complete: {
			callback: function() {
				$('popup_assigned_to_id').setValue(assignee_id);
				$('popup_assigned_to_type').setValue(assignee_type);
				$('popup_assigned_to_teamup').setValue(teamup);
			},
			hide: ['popup_assigned_to_teamup_info', 'popup_assigned_to_change']
		}
	});
}

TBG.Issues.updateWorkflowAssigneeTeamup = function(url, assignee_id, assignee_type)
{
	TBG.Issues.updateWorkflowAssignee(url, assignee_id, assignee_type, true);
}

TBG.Search.Filter.add = function(url) {
	TBG.Main.Helpers.ajax(url, {
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

TBG.Search.Filter.remove = function(key) {
	$('filter_' + key).remove();
	if ($('search_filters_list').childElements().size() == 0) {
		$('max_filters').value = 0;
	}
};

TBG.Search.deleteSavedSearch = function(url, id) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'delete_search_' + id + '_indicator'},
		success: {hide: 'saved_search_' + id + '_container'}
	});
};

TBG.Search.toPage = function(url, parameters, offset) {
	parameters += '&offset=' + offset;
	TBG.Main.Helpers.ajax(url, {
		params: parameters,
		loading: {indicator: 'paging_spinning'},
		success: {update: 'search_results'}
	});
};