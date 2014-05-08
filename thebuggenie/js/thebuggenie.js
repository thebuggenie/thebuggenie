function is_string(element) {
    return (typeof element == 'string');
}

Array.range= function(a, b, step){
    var A= [];
    if(typeof a== 'number'){
        A[0]= a;
        step= step || 1;
        while(a+step<= b){
            A[A.length]= a+= step;
        }
    }
    else{
        var s= 'abcdefghijklmnopqrstuvwxyz';
        if(a=== a.toUpperCase()){
            b=b.toUpperCase();
            s= s.toUpperCase();
        }
        s= s.substring(s.indexOf(a), s.indexOf(b)+ 1);
        A= s.split('');
    }
    return A;
}

// The core js class used by thebuggenie
var TBG = {
	Core: {
		AjaxCalls: []
	}, // The "Core" namespace is for functions used by thebuggenie core, not to be invoked outside the js class
	Main: { // The "Main" namespace contains regular functions in use across the site
		Helpers: {
			Message: {},
			Dialog: {},
			Backdrop: {}
		}, 
		Profile: {},
		Dashboard: {
			views: [],
			View: {}
		},
		Comment: {},
		Link: {},
		Menu: {},
		Login: {}
	},
	Chart: {},
	Modules: {},
	Project: {
		Statistics: {},
		Milestone: {},
		Planning: {},
		Timeline: {},
		Scrum: {
			Story: {},
			Sprint: {}
		},
		Roles: {},
		Build: {},
		Component: {},
		Edition: {
			Component: {}
		},
        Commits: {}
	},
	Config: {
		Permissions: {},
		Roles: {},
		User: {},
		Collection: {},
		Issuefields: {
			Options: {},
			Custom: {}
		},
		Issuetype: {
			Choices: {}
		},
		IssuetypeScheme: {},
		Workflows: {
			Workflow: {},
			Transition: {
				Actions: {},
				Validations: {}
			},
			Scheme: {}
		},
		Group: {},
		Team: {},
		Client: {},
		Import: {}
	}, // The "Config" namespace contains functions used in the configuration section
	Issues: {
		Link: {},
		File: {},
		Field: {
			Updaters: {}
		},
		ACL: {},
		Affected: {}
	}, // The "Issues" namespace contains functions used in direct relation to issues
	Search: {
		Filter: {},
		ResultViews: {}
	}, // The "Search" namespace contains functions related to searching
	Subscriptions: {}, // The "Subscription" namespace contains functionality related to subscribing to - and publishing js events
	effect_queues: {
		successmessage: 'TBG_successmessage',
		failedmessage: 'TBG_failedmessage'
	},
	debug: false,
	activated_popoutmenu: undefined,
	autocompleter_url: undefined,
	available_fields: ['description', 'user_pain', 'reproduction_steps', 'category', 'resolution', 'priority', 'reproducability', 'percent_complete', 'severity', 'edition', 'build', 'component', 'estimated_time', 'spent_time', 'milestone']
};

/**
 * Initializes the autocompleter
 */
TBG.Core._initializeAutocompleter = function() {
	if ($('searchfor')) {
		new Ajax.Autocompleter(
			"searchfor",
			"searchfor_autocomplete_choices",
			TBG.autocompleter_url,
			{
				paramName: "filters[text][value]",
				parameters: "filters[text][operator]==",
				minChars: 2,
				indicator: 'quicksearch_indicator',
				callback: function(element, entry) {
					$('quicksearch_submit').disable();
					$('quicksearch_submit').removeClassName('button-blue');
					$('quicksearch_submit').addClassName('button-silver');
					return entry;
				},
				afterUpdateChoices: function() {
					$('quicksearch_submit').enable();
					$('quicksearch_submit').removeClassName('button-silver');
					$('quicksearch_submit').addClassName('button-blue');
				},
				afterUpdateElement: TBG.Core._extractAutocompleteValue
			}
		);
	}
};

/**
 * Helper function to extract url from autocomplete response container
 */
TBG.Core._extractAutocompleteValue = function(elem, value) {
	var elements = value.select('.url');
	if (elements.size() == 1) {
		window.location = elements[0].innerHTML.unescapeHTML();
		$('quicksearch_indicator').show();
		$('quicksearch_submit').disable();
		$('quicksearch_submit').removeClassName('button-blue');
		$('quicksearch_submit').addClassName('button-silver');
		$('searchfor').blur();
	}
};

/**
 * Monitors viewport resize to adapt backdrops and dashboard containers
 */
TBG.Core._resizeWatcher = function() {
	if (($('attach_file') && $('attach_file').visible())) {
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
		var element_width = (dashboard_width > 600) ? ((dashboard_width / 2) - 5) : dashboard_width;
		$('dashboard').childElements().each(function(item) {
			item.setStyle({width: element_width + 'px'});
		});
	}
	if ($('issue_details')) {
		var il = $('issue_details').getLayout();
		var container_width = il.get('width') - il.get('padding-left') - il.get('padding-right');
		var element_width = (container_width > 650) ? parseInt(container_width / 2) : container_width;
		$('issue_details_fieldslist').childElements().each(function(item) {
			var l = $(item).getLayout();
			item.setStyle({width: element_width + 'px'});
		});
	}
	TBG.Core.popupVisiblizer();
};

TBG.Core.popupVisiblizer = function() {
	var visible_popups = $$('.dropdown_box').findAll(function(el) {return el.visible();});
	if (visible_popups.size()) {
		visible_popups.each(function (element) {
			var max_bottom = document.viewport.getHeight();
			var element_height = $(element).getHeight()
			var parent_offset = $(element).up().cumulativeOffset().top;
			var element_min_bottom = parent_offset + element_height + 35;
			if (max_bottom < element_min_bottom) {
				if ($(element).getStyle('position') != 'fixed') {
					jQuery(element).data({'top': $(element).getStyle('top')});
				}
				$(element).setStyle({'position': 'fixed', 'bottom': '5px', 'top': 'auto'});
			} else {
				$(element).setStyle({'position': 'absolute', 'bottom': 'auto', 'top': jQuery(element).data('top')});
			}
		});
	}
};

/**
 * Monitors viewport scrolling to adapt fixed positioners
 */
TBG.Core._scrollWatcher = function() {
	if ($('viewissue_header_container')) {
		var y = document.viewport.getScrollOffsets().top;
		if (y >= $('issue_view').offsetTop) {
			$('viewissue_header_container').addClassName('fixed');
			$('workflow_actions').addClassName('fixed');
			if (y >= $('viewissue_menu_panes').offsetTop) {
				if ($('comment_add_button') != undefined) {
					var button = $('comment_add_button').remove();
					$('workflow_actions').down('ul').insert(button);
				}
			} else if ($('comment_add_button') != undefined) {
				var button = $('comment_add_button').remove();
				$('add_comment_button_container').update(button);
			}
		}else {
			$('viewissue_header_container').removeClassName('fixed');
			$('workflow_actions').removeClassName('fixed');
			if ($('comment_add_button') != undefined) {
				var button = $('comment_add_button').remove();
				$('add_comment_button_container').update(button);
			}
		}
	}
	if ($('bulk_action_form_top')) {
		var y = document.viewport.getScrollOffsets().top;
		if (y >= $('bulk_action_form_top').up('.bulk_action_container').offsetTop) {
			$('bulk_action_form_top').addClassName('fixed');
		} else {
			$('bulk_action_form_top').removeClassName('fixed');
		}
	}
};

/**
 * Toggles one breadcrumb item in the breadcrumb bar
 */
TBG.Core._toggleBreadcrumbItem = function(item) {
	item.up('li').next().toggleClassName('popped_out');
	item.toggleClassName('activated');
};

/**
 * Toggles one breadcrumb item in the breadcrumb bar
 */
TBG.Core._hideBreadcrumbItem = function() {
	$('submenu').select('.popped_out').each(function(element) {
		element.removeClassName('popped_out');
		element.previous().down('.activated').removeClassName('activated');
	});
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
		}else {
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

TBG.Core._escapeWatcher = function(event) {
	if (Event.KEY_ESC != event.keyCode) return;
	TBG.Main.Helpers.Backdrop.reset();
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
	Event.observe(window, 'scroll', TBG.Core._scrollWatcher);
	TBG.Core._resizeWatcher();
	TBG.Core._scrollWatcher();
	if (TBG.Main.Dashboard.views.size() > 0) {
		TBG.Main.Dashboard.views.each(function(view_id) {
			TBG.Main.Dashboard.View.init(TBG.Main.Dashboard.url, view_id);
		});
	} else {
		$$('html')[0].setStyle({'cursor': 'default'});
	}
	$('fullpage_backdrop_content').observe('click', TBG.Core._resizeWatcher);
	document.observe('click', TBG.Main.toggleBreadcrumbMenuPopout);
	document.observe('keydown', TBG.Core._escapeWatcher);
	TBG.OpenID.init();
};

TBG.loadDebugInfo = function(debug_id, cb) {
	url = TBG.debugUrl.replace('___debugid___', debug_id);
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'tbg___DEBUGINFO___indicator'},
		success: {update: 'tbg___DEBUGINFO___'},
		complete: {
			callback: cb,
			show: 'tbg___DEBUGINFO___'
		},
		debug: false
	});
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

TBG.Main.Helpers.Dialog.show = function(title, content, options) {
	TBG.Main.Helpers.Message.clear();
	$('dialog_title').update(title);
	$('dialog_content').update(content);
	$('dialog_yes').setAttribute('href', 'javascript:void(0)');
	$('dialog_no').setAttribute('href', 'javascript:void(0)');
	$('dialog_yes').stopObserving('click');
	$('dialog_no').stopObserving('click');
	if (options['yes']['click']) {
		$('dialog_yes').observe('click', options['yes']['click']);
	}
	if (options['yes']['href']) {
		$('dialog_yes').setAttribute('href', options['yes']['href']);
	}
	if (options['no']['click']) {
		$('dialog_no').observe('click', options['no']['click']);
	}
	if (options['no']['href']) {
		$('dialog_no').setAttribute('href', options['no']['href']);
	}
	$('dialog_backdrop_content').show();
	$('dialog_backdrop').appear({duration: 0.2});
}

TBG.Main.Helpers.Dialog.dismiss = function() {
	$('dialog_backdrop_content').fade({duration: 0.2});
	$('dialog_backdrop').fade({duration: 0.2});
}

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
 *     replace: either an element id which will be replace with the value of the 
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
	if (options.form && options.form != undefined) params = Form.serialize(options.form);
	if (options.additional_params) params += options.additional_params;
	var url_method = (options.url_method) ? options.url_method : 'post';
	
	new Ajax.Request(url, {
		asynchronous: true,
		method: url_method,
		parameters: params,
		evalScripts: true,
		onLoading: function () {
			if (options.loading) {
				if (TBG.debug) {
					$('tbg___DEBUGINFO___indicator').show();
				}
				if ($(options.loading.indicator)) {
					$(options.loading.indicator).show();
				}
				TBG.Core._processCommonAjaxPostEvents(options.loading);
				if (options.loading.callback) {
					options.loading.callback();
				}
			}
		},
		onSuccess: function (response) {
			var json = response.responseJSON;
			if (json || (options.success && options.success.update)) {
				if (json && json.forward != undefined) {
					document.location = json.forward;
				} else {
					if (options.success && options.success.update) {
						var json_content_element = (is_string(options.success.update) || options.success.update.from == undefined) ? 'content' : options.success.update.from;
						var content = (json) ? json[json_content_element] : response.responseText;
						var update_element = (is_string(options.success.update)) ? options.success.update : options.success.update.element;
						if ($(update_element)) {
							var insertion = (is_string(options.success.update)) ? false : (options.success.update.insertion) ? options.success.update.insertion : false;
							if (insertion) {
								$(update_element).insert(content, 'after');
							} else {
								$(update_element).update(content);
							}
						}
						if (json && json.message) {
							TBG.Main.Helpers.Message.success(json.message);
						}
					} else if (options.success && options.success.replace) {
						var json_content_element = (is_string(options.success.replace) || options.success.replace.from == undefined) ? 'content' : options.success.replace.from;
						var content = (json) ? json[json_content_element] : response.responseText;
						var replace_element = (is_string(options.success.replace)) ? options.success.replace : options.success.replace.element;
						if ($(replace_element)) {
							Element.replace(replace_element, content);
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
		onFailure: function (response) {
			var json = (response.responseJSON) ? response.responseJSON : undefined;
			if (response.responseJSON && (json.error || json.message)) {
				TBG.Main.Helpers.Message.error(json.error, json.message);
			} else if (response.responseText) {
				TBG.Main.Helpers.Message.error(response.responseText);
			}
			if (options.failure) {
				TBG.Core._processCommonAjaxPostEvents(options.failure);
				if (options.failure.callback) {
					options.failure.callback(json);
				}
			}
		},
		onComplete: function (response) {
			if (TBG.debug) {
				$('tbg___DEBUGINFO___indicator').hide();
				var d = new Date(),
					d_id = response.getHeader('x-tbg-debugid');

				TBG.Core.AjaxCalls.push({location: url, time: d, debug_id: d_id});
				TBG.updateDebugInfo();
			}
			$(options.loading.indicator).hide();
			if (options.complete) {
				TBG.Core._processCommonAjaxPostEvents(options.complete);
				if (options.complete.callback) {
					var json = (response.responseJSON) ? response.responseJSON : undefined;
					options.complete.callback(json);
				}
			}
		}
	});
};

TBG.updateDebugInfo = function() {
	if ($('log_ajax_items')) $('log_ajax_items').update('');
	if ($('debug_ajax_count')) $('debug_ajax_count').update(TBG.Core.AjaxCalls.size());
	var ct = function(time) {
		return (time < 10) ? '0'+time : time;
	};
	TBG.Core.AjaxCalls.each(function(info) {
		var content = '<li style="clear: both;"><span class="faded_out dark small">'+ct(info.time.getHours())+':'+ct(info.time.getMinutes())+':'+ct(info.time.getSeconds())+'</span> '+info.location+' <a class="button button-silver" style="float: right;" href="javascript:void(0);" onclick="TBG.loadDebugInfo(\''+info.debug_id+'\');">Show debug info</a></li>';
		$('log_ajax_items').insert(content, 'top');
	});
};

TBG.Main.Helpers.formSubmit = function(url, form_id) {
	TBG.Main.Helpers.ajax(url, {
		form: form_id,
		loading: {indicator: form_id + '_indicator', disable: form_id + '_button'}, 
		success: {enable: form_id + '_button'},
		failure: {enable: form_id + '_button'}
	});
};

TBG.Main.Helpers.Backdrop.show = function(url) {
	$('fullpage_backdrop').appear({duration: 0.2});
	$$('body')[0].setStyle({'overflow': 'hidden'});
	$('fullpage_backdrop_indicator').show();

	if (url != undefined) {
		TBG.Main.Helpers.ajax(url, {
			url_method: 'get',
			loading: {indicator: 'fullpage_backdrop_indicator'},
			success: {
				update: 'fullpage_backdrop_content',
				callback: function () {
					$('fullpage_backdrop_content').appear({duration: 0.2});
					$('fullpage_backdrop_indicator').fade({duration: 0.2});
				}},
			failure: {hide: 'fullpage_backdrop'}
		});
	}
};

TBG.Main.Helpers.Backdrop.reset = function() {
	$$('body')[0].setStyle({'overflow': 'auto'});
	$('fullpage_backdrop').fade({duration: 0.2});
	TBG.Core._resizeWatcher();
};

TBG.Main.Helpers.tabSwitcher = function(visibletab, menu) {
	$(menu).childElements().each(function(item){item.removeClassName('selected');});
	$(visibletab).addClassName('selected');
	$(menu + '_panes').childElements().each(function(item){item.hide();});
	$(visibletab + '_pane').show();
};

TBG.Main.Helpers.MarkitUp = function(element) {
	element.markItUp({
		previewParserPath:	'', // path to your Wiki parser
		onShiftEnter:		{keepDefault:false, replaceWith:'\n\n'},
		markupSet: [
				{name:'Heading 1', key:'1', openWith:'=== ', closeWith:' ===', placeHolder:'Your title here...'},
				{name:'Heading 1', key:'2', openWith:'=== ', closeWith:' ===', placeHolder:'Your title here...'},
				{name:'Heading 2', key:'3', openWith:'====== ', closeWith:' ======', placeHolder:'Your title here...'},
				{name:'Heading 2', key:'4', openWith:'====== ', closeWith:' ======', placeHolder:'Your title here...'},
				{name:'Heading 2', key:'5', openWith:'====== ', closeWith:' ======', placeHolder:'Your title here...'},
			{separator:'---------------'},
			{name:'Bold', key:'B', openWith:"'''", closeWith:"'''"},
			{name:'Italic', key:'I', openWith:"''", closeWith:"''"},
			{name:'Stroke through', key:'S', openWith:'<strike>', closeWith:'</strike>'},
			{separator:'---------------'},
			{name:'Bulleted list', openWith:'(!(* |!|*)!)'},
			{name:'Numeric list', openWith:'(!(# |!|#)!)'},
			{separator:'---------------'},
			{name:'Picture', key:"P", replaceWith:'[[Image:[![Url:!:http://]!]|[![name]!]]]'},
			{name:'Link', key:"L", openWith:"[[[![Link]!]|", closeWith:']]', placeHolder:'Your text to link here...'},
			{name:'Url', openWith:"[[![Url:!:http://]!] ", closeWith:']', placeHolder:'Your text to link here...'},
			{separator:'---------------'},
			{name:'Quotes', openWith:'(!(> |!|>)!)', placeHolder:''},
			{name:'Code', openWith:'(!(<source lang="[![Language:!:php]!]">|!|<pre>)!)', closeWith:'(!(</source>|!|</pre>)!)'}
		]
	});
};

TBG.Main.toggleBreadcrumbMenuPopout = function(event) {
	var item = event.findElement('a');
	if (TBG.activated_popoutmenu != undefined && TBG.activated_popoutmenu != item && item != undefined) {
		TBG.Core._toggleBreadcrumbItem(TBG.activated_popoutmenu);
		TBG.activated_popoutmenu = undefined;
	}
	if (item != undefined && item.hasClassName('submenu_activator')) {
		TBG.Core._toggleBreadcrumbItem(item);
		TBG.activated_popoutmenu = item;
	} else {
		TBG.activated_popoutmenu = undefined;
	}

	if (item == undefined) {
		TBG.Core._hideBreadcrumbItem();
	}
};

TBG.Main.findIdentifiable = function(url, field) {
	TBG.Main.Helpers.ajax(url, { 
		form: field + '_form', 
		loading: {indicator: field + '_spinning'}, 
		success: {update: field + '_results'}
	});
};

TBG.Main.updatePercentageLayout = function(percent) {
	$('percent_complete_content').select('.percent_filled').first().style.width = percent + '%';
	$('percent_complete_content').select('.percent_unfilled').first().style.width = (100 - percent) + '%';
};

TBG.Main.submitIssue = function(url) {
	if ($('report_issue_submit_button').hasClassName('disabled')) return;
	
	TBG.Main.Helpers.ajax(url, {
		form: 'report_issue_form',
		url_method: 'post',
		loading: {
			indicator: 'report_issue_indicator',
			callback: function() {
				$('report_issue_submit_button').addClassName('disabled');
			}
		},
		success: {
			update: 'fullpage_backdrop_content'
		},
		complete: {
			callback: function() {
				$('report_issue_submit_button').removeClassName('disabled');
			}
		}
	});
}

TBG.Main.Link.add = function(url, target_type, target_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'attach_link_' + target_type + '_' + target_id + '_form',
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

TBG.Main.Menu.toggleEditMode = function(target_type, target_id, url) {
	if ($(target_type + '_' + target_id + '_container').hasClassName('menu_editing')) {
		Sortable.destroy(target_type + '_' + target_id + '_links');
	} else {
		Sortable.create(target_type + '_' + target_id + '_links', {constraint: '', onUpdate: function(container) { TBG.Main.Menu.saveOrder(container, target_type, target_id, url); }});
	}
	$(target_type + '_' + target_id + '_container').toggleClassName('menu_editing');
};

TBG.Main.Menu.saveOrder = function(container, target_type, target_id, url) {
	TBG.Main.Helpers.ajax(url, {
		additional_params: Sortable.serialize(container),
		loading: {
			indicator: target_type + '_' + target_id + '_indicator'
		}
	});
};

TBG.Main.detachFileFromArticle = function(url, file_id, article_name) {
	TBG.Core._detachFile(url, file_id, 'article_' + article_name + '_files_');
};

TBG.Main.reloadImage = function(id) {
   var src = $(id).src;
   var date = new Date();
   
   src = (src.indexOf('?') != -1) ? src.substr(0, pos) : src;
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

TBG.Main.Profile.checkUsernameAvailability = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'check_username_form',
		loading: {
			indicator: 'pick_username_indicator',
			hide: 'username_unavailable'
		},
		complete: {
			callback: function(json) {
				if (json.available) {
					TBG.Main.Helpers.Backdrop.show(json.url);
				} else {
					$('username_unavailable').show();
					$('username_unavailable').pulsate({pulses: 3, duration: 1});
				}
			}
		}
	});
};

TBG.Main.Profile.toggleNotificationSettings = function(preset) {
	if (preset == 'custom') {
		$('notification_settings_selectors').show();
	} else {
		$('notification_settings_selectors').hide();
	}
}

TBG.Main.Profile.removeOpenIDIdentity = function(url, oid) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'dialog_indicator'},
		success: {
			remove: 'openid_account_'+oid,
			callback: function () {
				if ($('openid_accounts_list').childElements().size() == 0) $('no_openid_accounts').show();
				if ($('openid_accounts_list').childElements().size() == 1 && $('pick_username_button')) $('openid_accounts_list').down('.button').remove();
				TBG.Main.Helpers.Dialog.dismiss();
			}
		}
	});
}

TBG.Main.Profile.cancelScopeMembership = function(url, sid) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'dialog_indicator'},
		success: {
			remove: 'account_scope_'+sid,
			callback: function () {
				if ($('pending_scope_memberships').childElements().size() == 0) $('no_pending_scope_memberships').show();
				TBG.Main.Helpers.Dialog.dismiss();
			}
		}
	});
}

TBG.Main.Profile.confirmScopeMembership = function(url, sid) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'dialog_indicator'},
		success: {
			callback: function () {
				$('confirmed_scope_memberships').insert({'bottom': $('account_scope_'+sid).remove()});
				$('account_scope_'+sid).down('.button-green').remove();
				$('account_scope_'+sid).down('.button-red').show();
				if ($('pending_scope_memberships').childElements().size() == 0) $('no_pending_scope_memberships').show();
				TBG.Main.Helpers.Dialog.dismiss();
			}
		}
	});
}

TBG.Main.Profile.clearPopupsAndButtons = function() {
	if ($('account_info_container')) {
		var pbuttons = $('account_info_container').down('.profile_buttons');
		pbuttons.select('.button').each(function(element) {
			$(element).removeClassName('button-pressed');
		});
	}
	$$('.popup_box').each(function(element) {
		$(element).hide();
	});
}

TBG.Main.Dashboard.View.swap = function(source_elm)
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

TBG.Main.Dashboard.View.add = function()
{
	var element_view = $('view_default').clone(true);
	element_view.id = 'view_' + new Date().getTime();
	$('views_list').insert(element_view);
	element_view = null;
	
	Sortable.create('views_list');
}

TBG.Main.Dashboard.View.init = function(url, view_id) {
	TBG.Main.Helpers.ajax(url, {
		method: 'get',
		additional_params: '&view_id=' + view_id,
		loading: {indicator: 'dashboard_' + view_id + '_indicator'},
		success: {update: 'dashboard_' + view_id},
		complete: {
			callback: function() {
				TBG.Core._resizeWatcher();
				TBG.Main.Dashboard.views.splice(0, 1);
				if (TBG.Main.Dashboard.views.size() == 0) {
					$$('html')[0].setStyle({'cursor': 'default'});
				}
			}
		}
	});
};

TBG.Main.Dashboard.save = function(url)
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

TBG.Main.Dashboard.sidebar = function (url, id)
{
	TBG.Main.setToggleState(url, !$(id).hasClassName('collapsed'));
	$(id).toggleClassName('collapsed');
	TBG.Core._resizeWatcher();
	TBG.Core._scrollWatcher();
}

TBG.Main.Profile.setState = function(url, ind) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: ind},
		success: {
			callback: function(json) {
				$$('.current_userstate').each(function(element) {
					$(element).update(json.userstate);
				});
			}
		}
	});
}

TBG.Main.Profile.addFriend = function(url, user_id, rnd_no) {
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'toggle_friend_' + user_id + '_' + rnd_no + '_indicator',
			hide: 'add_friend_' + user_id + '_' + rnd_no
		},
		success: {
			show: 'remove_friend_' + user_id + '_' + rnd_no
		},
		failure: {
			show: 'add_friend_' + user_id + '_' + rnd_no
		}
	});
}

TBG.Main.Profile.removeFriend = function(url, user_id, rnd_no) {
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'toggle_friend_' + user_id + '_' + rnd_no + '_indicator',
			hide: 'remove_friend_' + user_id + '_' + rnd_no
		},
		success: {
			show: 'add_friend_' + user_id + '_' + rnd_no
		},
		failure: {
			show: 'remove_friend_' + user_id + '_' + rnd_no
		}
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

TBG.Main.setToggleState = function (url, state) {
	url += '/' + (state ? '1' : 0);
	TBG.Main.Helpers.ajax(url, {});
}

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
			indicator: 'comment_edit_indicator_' + cid,
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

TBG.Main.Comment.reply = function(url, reply_comment_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'comment_reply_form_' + reply_comment_id,
		loading: {
			indicator: 'comment_reply_indicator_' + reply_comment_id,
			hide: 'comment_reply_controls_' + reply_comment_id
		},
		success: {
			hide: ['comment_reply_' + reply_comment_id],
			clear: 'comment_reply_bodybox_' + reply_comment_id,
			update: {element: 'comments_box', insertion: true, from: 'comment_data'},
			callback: function(json) {
				$('comment_reply_visibility_' + reply_comment_id).setValue(1);
			}
		},
		failure: {
			show: 'comment_reply_controls_' + reply_comment_id
		}
	});
};

TBG.Main.Login.register = function(url)
{
	TBG.Main.Helpers.ajax(url, {
		form: 'register_form',
		loading: {
			indicator: 'register_indicator',
			hide: 'register_button',
			callback: function() {
				$$('input.required').each(function(field) {
					$(field).setStyle({backgroundColor: ''});
				});
			}
		},
		success: {
			hide: 'register',
			update: {element: 'register_message', from: 'loginmessage'},
			show: 'register2'
		},
		failure: {
			show: 'register_button',
			callback: function(json) {
				json.fields.each(function(field) {
					$(field).setStyle({backgroundColor: '#FBB'});
				});	
			}
		}
	});
}

TBG.Main.Login.login = function(url)
{
	TBG.Main.Helpers.ajax(url, {
		form: 'login_form',
		loading: {
			indicator: 'login_indicator',
			hide: 'login_button'
		},
		failure: {
			show: 'login_button',
			reset: 'login_form'
		}
	});
}

TBG.Main.Login.resetForgotPassword = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'forgot_password_form',
		loading: {
			indicator: 'forgot_password_indicator',
			hide: 'forgot_password_button'
		},
		failure: {
			reset: 'forgot_password_form'
		},
		complete: {
			show: 'forgot_password_button'
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
	}else {
		$('milestone_' + milestone_id + '_issues').toggle();
	}
};

TBG.Project.Planning.toggleIssues = function(url, milestone_id) {
	if (!$('milestone_' + milestone_id + '_container').visible()) {
		if ($('milestone_' + milestone_id + '_list').childElements().size() == 0) {
			TBG.Main.Helpers.ajax(url, {
				loading: {indicator: 'milestone_' + milestone_id + '_issues_indicator'},
				success: {
					update: 'milestone_' + milestone_id + '_list',
					show: ['milestone_' + milestone_id + '_container', 'milestone_' + milestone_id + '_reload_button'],
					callback: function(json) {
						$('milestone_' + milestone_id + '_list').childElements().each(function(element) {
							new Draggable(element.id + '_draggable', {revert: true, handle: element.id + '_handle'});
						});
					}
				}
			});
		} else {
			$('milestone_' + milestone_id + '_container').toggle();
		}
	} else {
		$('milestone_' + milestone_id + '_container').hide();
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
		additional_params: "offset=" + $('timeline_offset').getValue(),
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

TBG.Project.Commits.update = function(url) {
	TBG.Main.Helpers.ajax(url, {
		url_method: 'get',
		additional_params: "offset=" + $('commits_offset').getValue(),
		loading: {
			indicator: 'commits_indicator',
			hide: 'commits_more_link'
		},
		success: {
			update: {element: 'commits', insertion: true},
			show: 'commits_more_link',
			callback: function(json) {
				$('commits_offset').setValue(json.offset)
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
				Droppables.add('milestone_' + json.sprint_id, {hoverclass: 'highlighted', onDrop: function (dragged, dropped, event) {TBG.Project.Planning.assign(assign_url, dragged, dropped)}});
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
			update: {element: 'milestone_0_list', insertion: true},
			hide: 'milestone_0_unassigned',
			callback: function(json) {
				new Draggable('issue_' + json.story_id, {revert: true});
			}
		}
	});
}

TBG.Project.Planning.assign = function(url, dragged, dropped)
{
	if (dropped.id == dragged.up('.milestone_box').id) return;
	
	TBG.Main.Helpers.ajax(url, {
		params: {story_id: $(dragged.down('input')).getValue(), sprint_id: $(dropped.id + '_id').getValue()},
		loading: {
			indicator: 'fullpage_backdrop',
			clear: 'fullpage_backdrop_content',
			show: 'fullpage_backdrop_indicator'
		},
		success: {
			callback: function(json) {
				var elm = Element.remove(dragged.up('tr'));
				if ($(dropped.id + '_list').childElements().size() > 0) {
					$(dropped.id + '_list').insert({'top':elm});
				}
				$('milestone_' + json.old_sprint_id + '_issues').update(json.old_issues);
				$('milestone_' + json.new_sprint_id + '_issues').update(json.new_issues);
				$('milestone_' + json.old_sprint_id + '_estimated_points').update(json.old_estimated_points);
				$('milestone_' + json.new_sprint_id + '_estimated_points').update(json.new_estimated_points);
				$('milestone_' + json.old_sprint_id + '_estimated_hours').update(json.old_estimated_hours);
				$('milestone_' + json.new_sprint_id + '_estimated_hours').update(json.new_estimated_hours);
				(json.old_issues == 0) ? $('milestone_' + json.old_sprint_id + '_unassigned').show() : $('milestone_' + json.old_sprint_id + '_unassigned').hide();
				(json.new_issues == 0) ? $('milestone_' + json.new_sprint_id + '_unassigned').show() : $('milestone_' + json.new_sprint_id + '_unassigned').hide();
			}
		}
	});
}

TBG.Project.Planning.updateIssues = function(url, milestone_id)
{
	TBG.Main.Helpers.ajax(url, {
		form: 'milestone_' + milestone_id + '_issues_form',
		loading: {
			indicator: 'milestone_'+milestone_id+'_update_issues_indicator',
		},
		success: {
			callback: function(json) {
				$('milestone_' + milestone_id + '_estimated_points').update(json.estimated_points);
				$('milestone_' + milestone_id + '_estimated_hours').update(json.estimated_hours);
			}
		}
	});
}

TBG.Project.Scrum.Story.setColor = function(url, story_id, color)
{
	TBG.Main.Helpers.ajax(url, {
		params: {color: color},
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

TBG.Project.updateLinks = function(json) {
	if ($('current_project_num_count')) $('current_project_num_count').update(json.total_count);
	(json.more_available) ? $('add_project_div').show() : $('add_project_div').hide();
}

TBG.Project.resetIcons = function(url) {
	TBG.Main.Helpers.ajax(url, {
		url_method: 'post',
		additional_params: '&clear_icons=1'
	});
};

TBG.Project.add = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'add_project_form',
		loading: {indicator: 'project_add_indicator'},
		success: {
			reset: 'add_project_form',
			update: {element: 'project_table', insertion: true},
			hide: 'noprojects_tr',
			callback: TBG.Project.updateLinks
		}
	});
}

TBG.Project.remove = function(url, pid) {
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'project_delete_indicator_' + pid,
			hide: 'project_delete_controls_' + pid
		},
		success: {
			remove: 'project_box_' + pid,
			callback: function(json) {
				if ($('project_table').childElements().size() == 0) $('noprojects_tr').show();
				TBG.Project.updateLinks(json);
				TBG.Main.Helpers.Dialog.dismiss();
			}
		},
		failure: {
			show: 'project_delete_error_' + pid
		},
		complete: {
			show: 'project_delete_controls_' + pid
		}
	});
}

TBG.Project.archive = function(url, pid) {
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'project_' + pid + '_archive_indicator',
			hide: 'project_' + pid + '_archive'
		},
		success: {
			remove: 'project_box_' + pid,
			update: {
				element: 'project_table',
				insertion: true,
				from: 'box'
			},
			callback: TBG.Main.Helpers.Dialog.dismiss
		}
	});
}

TBG.Project.unarchive = function(url, pid) {
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'project_' + pid + '_unarchive_indicator',
			hide: 'project_' + pid + '_unarchive'
		},
		success: {
			remove: 'project_box_' + pid,
			update: {
				element: 'project_table',
				insertion: true,
				from: 'box'
			}
		},
		failure: {
			show: 'project_' + pid + '_unarchive'
		}
	});
}

TBG.Project.Planning.sortMilestones = function(milestone_order) {
	if ($('milestone_list')) {
		milestone_order.each(function(milestone_id) {
			$('milestone_list').appendChild($('milestone_' + milestone_id));
		});
		$('milestone_list').appendChild($('milestone_0'));
	}
};

TBG.Project.Milestone.add = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'edit_milestone_form',
		loading: {indicator: 'milestone_edit_indicator'},
		success: {
			reset: 'add_milestone_form',
			hide: 'no_milestones',
			callback: function(json) {
				TBG.Project.Planning.sortMilestones(json.milestone_order);
				TBG.Main.Helpers.Backdrop.reset();
			},
			update: {element: 'milestone_list', insertion: true}
		}
	});
}

TBG.Project.Milestone.retrieve = function(url, milestone_id, issue_ids) {
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'fullpage_backdrop',
			clear: 'fullpage_backdrop_content',
			show: 'fullpage_backdrop_indicator'
		},
		success: {
			update: {element: 'milestone_list', insertion: true},
			callback: function(json) {
				TBG.Project.Planning.sortMilestones(json.milestone_order);
				if ($('bulk_action_assign_milestone_top') != undefined) {
					$('bulk_action_assign_milestone_top').insert('<option value="'+json.milestone_id+'" id="bulk_action_assign_milestone_top_'+milestone_id+'">'+json.milestone_name+'</option>');
				}
				if ($('bulk_action_assign_milestone_bottom') != undefined) {
					$('bulk_action_assign_milestone_bottom').insert('<option value="'+json.milestone_id+'" id="bulk_action_assign_milestone_bottom_'+milestone_id+'">'+json.milestone_name+'</option>');
				}
				issue_ids.each(function(issue_id) {
					var issue_elm = $('issue_' + issue_id);
					if (issue_elm != undefined) {
						$('milestone_' + milestone_id + '_list').insert({'top': issue_elm.remove()});
					}
				});
			}
		}
	});
}

TBG.Project.Milestone.update = function(url, milestone_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'edit_milestone_form',
		loading: {
			indicator: 'milestone_edit_indicator'
		},
		success: {
			callback: function(json) {
				TBG.Project.Planning.sortMilestones(json.milestone_order);
				if ($('bulk_action_assign_milestone_top_' + milestone_id) != undefined) {
					$('bulk_action_assign_milestone_top_' + milestone_id).update(json.milestone_name);
				}
				if ($('bulk_action_assign_milestone_bottom_' + milestone_id) != undefined) {
					$('bulk_action_assign_milestone_bottom_' + milestone_id).update(json.milestone_name);
				}
				TBG.Main.Helpers.Backdrop.reset();
			},
			update: {element: 'milestone_name', from: 'milestone_name'},
			replace: 'milestone_' + milestone_id + '_header'
		}
	});
}

TBG.Project.Milestone.remove = function (url, milestone_id) {
	TBG.Main.Helpers.Dialog.dismiss();
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'fullpage_backdrop',
			clear: 'fullpage_backdrop_content',
			show: 'fullpage_backdrop_indicator'
		},
		success: {
			callback: function(json) {
				if ($('milestone_0_list').childElements().size() > 0) {
					$('milestone_' + milestone_id + '_list').childElements().each(function(element) {
						$('milestone_0_list').insert({'top': element.remove()});
					});
				}
				$('milestone_' + milestone_id).remove();
				if ($('bulk_action_assign_milestone_top_' + milestone_id) != undefined) {
					$('bulk_action_assign_milestone_top_' + milestone_id).remove();
				}
				if ($('bulk_action_assign_milestone_bottom_' + milestone_id) != undefined) {
					$('bulk_action_assign_milestone_bottom_' + milestone_id).remove();
				}
				$('milestone_0_issues').update(json.issue_count);
				$('milestone_0_estimated_hours').update(json.hours);
				$('milestone_0_estimated_points').update(json.points);
				TBG.Main.Helpers.Backdrop.reset();
				if ($('milestone_list').childElements().size() == 0) $('no_milestones').show();
			}
		}
	});
}

TBG.Project.Build.doAction = function(url, bid, action, update) {
	var update_elm = (update == 'all') ? 'build_table' : 'build_list_' + bid;
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'build_'+bid+'_indicator',
			hide: 'build_'+bid+'_info'
		},
		success: {
			update: update_elm
		},
		complete: {
			show: 'build_'+bid+'_info'
		}
	});
}

TBG.Project.Build.update = function(url, bid) {
	TBG.Main.Helpers.ajax(url, {
		form: 'edit_build_'+bid,
		loading: {
			indicator: 'build_'+bid+'_indicator',
			hide: 'build_'+bid+'_info'
		},
		success: {
			update: 'build_list_'+bid
		},
		complete: {
			show: 'build_'+bid+'_info'
		}
	});
}

TBG.Project.Build.addToOpenIssues = function(url, bid) {
	TBG.Main.Helpers.ajax(url, {
		form: 'addtoopen_build_'+bid,
		loading: {
			indicator: 'build_'+bid+'_indicator',
			hide: 'build_'+bid+'_info'
		},
		success: {
			hide: 'addtoopen_build_'+bid
		},
		complete: {
			show: 'build_'+bid+'_info'
		}
	});
}

TBG.Project.Build.remove = function(url, bid, b_type, edition_id) {
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'build_'+bid+'_indicator',
			hide: 'build_'+bid+'_info',
			callback: function() {
				$('build_'+bid+'_indicator').addClassName('selected_red');
			}
		},
		success: {
			remove: ['show_build_'+bid],
			callback: function () {
				TBG.Main.Helpers.Dialog.dismiss();
				if ($(b_type + '_builds_' + edition_id).childElements().size() == 0) {
					$('no_' + b_type + '_builds_' + edition_id).show();
				}
			}
		},
		failure: {
			show: 'build_'+bid+'_info',
			hide: 'del_build_'+bid,
			callback: function() {
				$('build_'+bid+'_indicator').removeClassName('selected_red');
			}
		}
	});
}

TBG.Project.Build.add = function(url, edition_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'add_build_form',
		loading: {indicator: 'build_add_indicator'},
		success: {
			reset: 'add_build_form',
			hide: 'no_active_builds_' + edition_id,
			update: {element: 'active_builds_' + edition_id, insertion: true, from: 'html'}
		}
	});
}

TBG.Project.saveOther = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'project_other',
		loading: {indicator: 'settings_save_indicator'}
	});
}

TBG.Project.Edition.edit = function(url, edition_id)
{
	TBG.Main.Helpers.Backdrop.show(url);
}

TBG.Project.Edition.remove = function(url, eid) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'edition_'+eid+'_delete_indicator'},
		success: {
			remove: ['edition_'+eid+'_box', 'edition_'+eid+'_permissions'],
			callback: function(json) {
				if (json.itemcount == 0) $('no_editions').show();
				TBG.Main.Helpers.Dialog.dismiss();
			}
		},
		failure: {
			hide: 'del_edition_'+eid
		}
	});
}

TBG.Project.Edition.add = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'add_edition_form',
		loading: {indicator: 'edition_add_indicator'},
		success: {
			reset: 'add_edition_form',
			hide: ['no_editions', 'add_edition_form'],
			update: {element: 'edition_table', insertion: true, from: 'html'}
		}
	});
}

TBG.Project.Edition.submitSettings = function(url, edition_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'edition_settings_form',
		loading: {indicator: 'edition_save_indicator'},
		success: {
			update: {element: 'edition_' + edition_id + '_name', from: 'edition_name'}
		}
	});
}

TBG.Project.Edition.Component.add = function(url, cid) {
	TBG.Main.Helpers.ajax(url, {
		loading: {
			callback: function() {
				$('project_component_'+cid).fade();
			}
		},
		success: {
			callback: function() {
				$('edition_component_count').value++;
				$('edition_component_'+cid).appear();
			},
			hide: 'edition_no_components'
		},
		failure: {
			callback: function() {
				$('project_component_'+cid).appear();
			}
		}
	});
}

TBG.Project.Edition.Component.remove = function(url, cid) {
	TBG.Main.Helpers.ajax(url, {
		loading: {
			callback: function() {
				$('edition_component_'+cid).fade();
			}
		},
		success: {
			callback: function() {
				$('edition_component_count').value--;
				if ($('edition_component_count').value == 0) $('edition_no_components').appear();
				$('project_component_'+cid).show();
			}
		},
		failure: {
			callback: function() {
				$('edition_component_'+cid).appear();
			}
		}
	});
}

TBG.Project.Component.update = function(url, cid) {
	TBG.Main.Helpers.ajax(url, {
		form: 'edit_component_' + cid + '_form',
		loading: {
			indicator: 'component_'+cid+'_indicator'
		},
		success: {
			update: {element: 'component_'+cid+'_name', from: 'newname'},
			hide: 'edit_component_'+cid,
			show: 'show_component_'+cid
		}
	});
}

TBG.Project.Component.add = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'add_component_form',
		loading: {indicator: 'component_add_indicator'},
		success: {
			reset: 'add_component_form',
			hide: ['no_components', 'add_component_form'],
			update: {element: 'component_table', insertion: true, from: 'html'}
		}
	});
}

TBG.Project.Component.remove = function(url, cid) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'component_'+cid+'_delete_indicator'},
		success: {
			remove: ['show_component_'+cid, 'edit_component_'+cid, 'component_'+cid+'_permissions'],
			callback: function(json) {
				if (json.itemcount == 0) $('no_components').show();
				TBG.Main.Helpers.Dialog.dismiss();
			}
		},
		failure: {
			hide: 'del_component_'+cid
		}
	});
}

TBG.Project.submitAdvancedSettings = function(url) {
	TBG.Project._submitDetails(url, 'project_settings');
}

TBG.Project.submitDisplaySettings = function(url) {
	TBG.Project._submitDetails(url, 'project_other');
}

TBG.Project.submitInfo = function(url, pid) {
	TBG.Project._submitDetails(url, 'project_info', pid);
}

TBG.Project._submitDetails = function(url, form_id, pid) {
	TBG.Main.Helpers.ajax(url, {
		form: form_id,
		loading: {indicator: form_id + '_indicator'},
		success: {
			callback: function(json) {
				if ($('project_name_span')) $('project_name_span').update($('project_name_input').getValue());
				if ($('project_description_span')) {
					if ($('project_description_input').getValue()) {
						$('project_description_span').update(json.project_description);
						$('project_no_description').hide();
					} else {
						$('project_description_span').update('');
						$('project_no_description').show();
					}
				}
				if ($('project_key_span')) $('project_key_span').update(json.project_key);
				if ($('sidebar_link_scrum') && $('use_scrum').getValue() == 1) $('sidebar_link_scrum').show();
				else if ($('sidebar_link_scrum')) $('sidebar_link_scrum').hide();

				['edition', 'component'].each(function(element) {
					if ($('enable_'+element+'s').getValue() == 1) {
						$('add_'+element+'_button').show();
						$('project_'+element+'s').show();
						$('project_'+element+'s_disabled').hide();
					} else {
						$('add_'+element+'_button').hide();
						$('project_'+element+'s').hide();
						$('project_'+element+'s_disabled').show();
					}
				});
				
				if (pid != undefined && $('project_box_' + pid) != undefined) $('project_box_' + pid).update(json.content);
			}
		}
	});
}

TBG.Project.findDevelopers = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'find_dev_form',
		loading: {indicator: 'find_dev_indicator'},
		success: {update: 'find_dev_results'}
	});
}

TBG.Project._updateUserFromJSON = function(object, field) {
	if (object.id == 0) {
		$(field + '_name').hide();
		$('no_' + field).show();
	} else {
		$(field + '_name').update(object.name);
		$('no_' + field).hide();
		$(field + '_name').show();
	}
}

TBG.Project.setUser = function(url, field) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: field + '_spinning'},
		success: {
			hide: field + '_change',
			callback: function(json) {
				TBG.Project._updateUserFromJSON(json.field, field);
			}
		}
	});
}

TBG.Project.assign = function(url, container_id) {
	var role_id = $(container_id).down('select').getValue();
	var parameters = "&role_id="+role_id;
	TBG.Main.Helpers.ajax(url, {
		params: parameters,
		loading: {indicator: 'assign_dev_indicator'},
		success: {update: 'assignees_list'}
	});
}

TBG.Project.removeAssignee = function(url, type, id) {
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'remove_assignee_'+type+'_'+id+'_indicator',
			hide: 'assignee_'+type+'_'+id+'_link'
		},
		success: {
			remove: 'assignee_'+type+'_'+id+'_row',
			callback: function() {
				if ($('project_team_' + type + 's').childElements().size() == 0) {
					$('project_team_' + type + 's').hide();
					$('no_project_team_' + type + 's').show();
				}
			}
		}
	});
}

TBG.Project.edit = function(url) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'backdrop_detail_indicator'},
		success: {update: 'backdrop_detail_content'}
	});
}

TBG.Project.workflow = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'workflow_form2',
		loading: {indicator: 'update_workflow_indicator'},
		success: {callback: function() {TBG.Main.Helpers.Backdrop.reset();}}
	});
}

TBG.Project.workflowtable = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'workflow_form',
		loading: {
			indicator: 'change_workflow_spinner',
			hide: 'change_workflow_box'
		},
		success: {
			update: 'change_workflow_table',
			show: 'change_workflow_table'
		},
		failure: {
			show: 'change_workflow_box'
		}
	});
}

TBG.Project.updatePrefix = function(url, project_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'project_info',
		loading: {indicator: 'project_key_indicator'},
		success: {update: 'project_key_input'}
	});
}

TBG.Config.Import.importCSV = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'import_csv_form',
		loading: {
			indicator: 'csv_import_indicator',
			hide: 'csv_import_error'
		},
		failure: {
			show: 'csv_import_error',
			callback: function(json) {
				$('csv_import_error_detail').update(json.errordetail);
			}
		}
	});
}

TBG.Config.Import.getImportCsvIds = function(url) {
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'id_zone_indicator',
			hide: 'id_zone_content'
		},
		success: {
			update: 'id_zone_content',
			show: 'id_zone_content'
		}
	});
}

TBG.Config.updateCheck = function(url) {
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'update_spinner',
			hide: 'update_button'
		},
		success: {
			callback: function(json) {
				(json.uptodate) ? 
					TBG.Main.Helpers.Message.success(json.title, json.message) : 
					TBG.Main.Helpers.Message.error(json.title, json.message);
			}
		},
		complete: {
			show: 'update_button'
		}
	});
}

TBG.Config.Issuetype.showOptions = function(url, id) {
	$('issuetype_' + id + '_content').toggle();
	if ($('issuetype_' + id + '_content').childElements().size() == 0) {
		TBG.Main.Helpers.ajax(url, {
			loading: {indicator: 'issuetype_' + id + '_indicator'},
			success: {update: 'issuetype_' + id + '_content'}
		});
	}
}

TBG.Config.Issuetype.update = function(url, id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'edit_issuetype_' + id + '_form',
		loading: {indicator: 'edit_issuetype_' + id + '_indicator'},
		success: {
			hide: 'edit_issuetype_' + id + '_form',
			callback: function(json) {
				if (json.description != undefined) $('issuetype_' + id + '_description_span').update(json.description);
				if (json.name != undefined) {
					$('issuetype_' + id + '_name_span').update(json.name);
					if ($('issuetype_' + id + '_info')) $('issuetype_' + id + '_info').show();
				}
			}
		}
	});
}

TBG.Config.Issuetype.remove = function(url, id) {
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'fullpage_backdrop',
			clear: 'fullpage_backdrop_content',
			show: 'fullpage_backdrop_indicator'
		},
		success: {
			remove: 'issuetype_' + id + '_box',
			callback: TBG.Main.Helpers.Dialog.dismiss
		}
	});
}

TBG.Config.Issuetype.Choices.update = function(url, id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'update_' + id + '_choices_form',
		loading: {indicator: 'update_' + id + '_choices_indicator'},
		success: {hide: 'issuetype_' + id + '_content'}
	});
}

TBG.Config.Issuetype.add = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'add_issuetype_form',
		loading: {
			indicator: 'add_issuetype_indicator',
			hide: 'add_issuetype_button'
		},
		success: {
			reset: 'add_issuetype_form',
			update: {element: 'issuetypes_list', insertion: true}
		},
		complete: {
			show: 'add_issuetype_button'
		}
	});
}

TBG.Config.Issuetype.toggleForScheme = function(url, issuetype_id, scheme_id, action) {
	var hide_element = 'type_toggle_' + issuetype_id + '_' + action;
	var show_element = 'type_toggle_' + issuetype_id + '_' + ((action == 'enable') ? 'disable' : 'enable');
	var cb;
	if (action == 'enable') {
		cb = function(json) {$('issuetype_' + json.issuetype_id + '_box').addClassName("green");$('issuetype_' + json.issuetype_id + '_box').removeClassName("lightgrey");};
	} else {
		cb = function(json) {$('issuetype_' + json.issuetype_id + '_box').removeClassName("green");$('issuetype_' + json.issuetype_id + '_box').addClassName("lightgrey");};
	}
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'edit_issuetype_' + issuetype_id + '_indicator',
			hide: hide_element
		},
		success: {
			show: show_element,
			callback: cb
		}
	});
}

TBG.Config.IssuetypeScheme.copy = function(url, scheme_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'copy_issuetype_scheme_' + scheme_id + '_form',
		loading: {
			indicator: 'copy_issuetype_scheme_' + scheme_id + '_indicator'
		},
		success: {
			hide: 'copy_scheme_' + scheme_id + '_popup',
			update: {element: 'issuetype_schemes_list', insertion: true}
		}
	});
}

TBG.Config.IssuetypeScheme.remove = function(url, scheme_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'delete_issuetype_scheme_' + scheme_id + '_form',
		loading: {
			indicator: 'delete_issuetype_scheme_' + scheme_id + '_indicator'
		},
		success: {
			remove: ['delete_scheme_' + scheme_id + '_popup', 'copy_scheme_' + scheme_id + '_popup', 'issuetype_scheme_' + scheme_id],
			update: {element: 'issuetype_schemes_list', insertion: true}
		}
	});
}

TBG.Config.Issuefields.saveOrder = function(container, type, url) {
	TBG.Main.Helpers.ajax(url, {
		additional_params: Sortable.serialize(container),
		loading: {
			indicator: type + '_sort_indicator'
		}
	});
};

TBG.Config.Issuefields.Options.show = function(url, field) {
	$(field + '_content').toggle();
	if ($(field + '_content').childElements().size() == 0) {
		TBG.Main.Helpers.ajax(url, {
			loading: {indicator: field + '_indicator'},
			success: {update: field + '_content'}
		});
	}
}

TBG.Config.Issuefields.Options.add = function(url, type) {
	TBG.Main.Helpers.ajax(url, {
		form: 'add_' + type + '_form',
		loading: {indicator: 'add_' + type + '_indicator'},
		success: {
			reset: 'add_' + type + '_form',
			hide: 'no_' + type + '_items',
			update: {element: type + '_list', insertion: true}
		}
	});
}

TBG.Config.Issuefields.Options.update = function(url, type, id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'edit_' + type + '_' + id + '_form',
		loading: {indicator: 'edit_' + type + '_' + id + '_indicator'},
		success: {
			show: 'item_option_' + type + '_' + id + '_content',
			hide: 'edit_item_option_' + id,
			callback: function(json) {
				$(type + '_' + id + '_name').update($(type + '_' + id + '_name_input').getValue());
				if ($(type + '_' + id + '_itemdata_input') && $(type + '_' + id + '_itemdata')) $(type + '_' + id + '_itemdata').style.backgroundColor = $(type + '_' + id + '_itemdata_input').getValue();
				if ($(type + '_' + id + '_value_input') && $(type + '_' + id + '_value')) $(type + '_' + id + '_value').update($(type + '_' + id + '_value_input').getValue());
			}
		}
	});
}

TBG.Config.Issuefields.Options.remove = function(url, type, id) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'delete_' + type + '_' + id + '_indicator'},
		success: {
			remove: ['delete_item_option_' + id, 'item_option_' + type + '_' + id]
		}
	});
}

TBG.Config.Issuefields.Custom.add = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'add_custom_type_form',
		loading: {
			indicator: 'add_custom_type_indicator',
			hide: 'add_custom_type_button'
		},
		success: {
			reset: 'add_custom_type_form',
			update: {element: 'custom_types_list', insertion: true}
		},
		complete: {
			show: 'add_custom_type_button'
		}
	});
}

TBG.Config.Issuefields.Custom.update = function(url, type) {
	TBG.Main.Helpers.ajax(url, {
		form: 'edit_custom_type_' + type + '_form',
		loading: {indicator: 'edit_custom_type_' + type + '_indicator'},
		success: {
			hide: 'edit_custom_type_' + type + '_form',
			callback: function(json) {
				$('custom_type_' + type + '_description_span').update(json.description);
				$('custom_type_' + type + '_instructions_span').update(json.instructions);
				if (json.instructions != '') {
					$('custom_type_' + type + '_instructions_div').show();
					$('custom_type_' + type + '_no_instructions_div').hide();
				} else {
					$('custom_type_' + type + '_instructions_div').hide();
					$('custom_type_' + type + '_no_instructions_div').show();
				}
				$('custom_type_' + type + '_name_link').update(json.name);
			},
			show: 'custom_type_' + type + '_info'
		}
	});
}

TBG.Config.Issuefields.Custom.remove = function(url, type, id) {
	TBG.Config.Issuefields.Options.remove(url, type, id);
};

TBG.Config.Permissions.set = function(url, field) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: field + '_indicator'},
		success: {update: field}
	});
};

TBG.Config.Permissions.getOptions = function(url, field) {
	$(field).toggle();
	if ($(field).childElements().size() == 0) {
		TBG.Main.Helpers.ajax(url, {
			loading: {indicator: field + '_indicator'},
			success: {update: field}
		});
	}
}

TBG.Config.Roles.getPermissions = function(url, field) {
	$(field).toggle();
	if ($(field).childElements().size() == 0) {
		TBG.Main.Helpers.ajax(url, {
			url_method: 'get',
			loading: {indicator: field + '_indicator'},
			success: {update: field}
		});
	}
}

TBG.Config.Roles.getPermissionsEdit = function(url, field) {
	$(field).toggle();
	if ($(field).childElements().size() == 0) {
		TBG.Main.Helpers.ajax(url, {
			url_method: 'get',
			loading: {indicator: field + '_indicator'},
			success: {update: field}
		});
	}
}

TBG.Config.Roles.update = function(url, role_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'role_' + role_id + '_form',
		loading: {indicator: 'role_' + role_id + '_form_indicator'},
		success: {
			hide: 'role_' + role_id + '_permissions_edit',
			callback: function(json) {
				$('role_'+role_id+'_permissions_count').update(json.permissions_count);
				$('role_'+role_id+'_permissions_list').update('');
				$('role_'+role_id+'_permissions_list').hide();
				$('role_'+role_id+'_name').update(json.role_name);
			}
		}
	});
}

TBG.Config.Roles.remove = function(url, role_id) {
	TBG.Main.Helpers.ajax(url, {
		url_method: 'post',
		loading: {
			indicator: 'fullpage_backdrop',
			clear: 'fullpage_backdrop_content',
			show: 'fullpage_backdrop_indicator',
			hide: 'dialog_backdrop'
		},
		success: {
			callback: function() {
				var rc = $('role_' + role_id + '_container');
				if (rc.up('ul').childElements().size() == 2) {
					rc.up('ul').down('li.no_roles').show();
				}
				rc.remove();
			}
		}
	});
}

TBG.Config.Roles.add = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'new_role_form',
		loading: {indicator: 'new_role_form_indicator'},
		success: {
			update: {element: 'global_roles_list', insertion: true},
			hide: ['global_roles_no_roles', 'new_role']
		}
	});
};

TBG.Project.Roles.add = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'new_project_role_form',
		loading: {indicator: 'new_project_role_form_indicator'},
		success: {
			update: {element: 'project_roles_list', insertion: true},
			hide: ['project_roles_no_roles', 'new_project_role']
		}
	});
};

TBG.Config.User.show = function(url, findstring) {
	TBG.Main.Helpers.ajax(url, {
		params: '&findstring=' + findstring,
		loading: {indicator: 'find_users_indicator'},
		success: {update: 'users_results'}
	});
}

TBG.Config.User.add = function(url, callback_function_for_import) {
	TBG.Main.Helpers.ajax(url, {
		form: 'createuser_form',
		loading: {indicator: 'find_users_indicator'},
		success: {
			update: 'users_results',
			callback: TBG.Config.User._updateLinks
		},
		failure: {
			callback: function(json) {
				if (json.allow_import) {
					callback_function_for_import();
				}
			}
		}
	});
}

TBG.Config.User.addToScope = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'createuser_form',
		loading: {indicator: 'dialog_indicator'},
		success: {
			update: 'users_results',
			callback: function(json) {
				TBG.Main.Helpers.Dialog.dismiss();
				TBG.Config.User._updateLinks(json);
			}
		}
	});
}

TBG.Config.User.getEditForm = function(url, uid) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'user_' + uid + '_edit_spinning'},
		success: {
			update: 'user_' + uid + '_edit_td',
			show: 'user_' + uid + '_edit_tr'
		}
	});
}

TBG.Config.User.remove = function(url, user_id) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'delete_user_'+user_id+'_indicator'},
		success: {
			remove: ['users_results_user_'+user_id, 'user_'+user_id+'_edit_spinning', 'user_'+user_id+'_edit_tr', 'users_results_user_'+user_id+'_permissions_row'],
			callback: TBG.Config.User._updateLinks
		}
	});
}

TBG.Config.User._updateLinks = function(json) {
	if ($('current_user_num_count')) $('current_user_num_count').update(json.total_count);
	(json.more_available) ? $('adduser_div').show() : $('adduser_div').hide();
	TBG.Config.Collection.updateDetailsFromJSON(json);
}

TBG.Config.User.update = function(url, user_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'edituser_' + user_id + '_form',
		loading: {indicator: 'edit_user_' + user_id + '_indicator'},
		success: {
			update: 'users_results_user_' + user_id,
			show: 'users_results_user_' + user_id,
			hide: 'user_' + user_id + '_edit_tr',
			callback: function(json) {
				$('password_' + user_id + '_leave').checked = true;
				$('new_password_' + user_id + '_1').value = '';
				$('new_password_' + user_id + '_2').value = '';
				TBG.Config.Collection.updateDetailsFromJSON(json);
			}
		}
	});
}

TBG.Config.User.updateScopes = function(url, user_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'edituser_' + user_id + '_scopes_form',
		loading: {indicator: 'edit_user_' + user_id + '_scopes_form_indicator'},
		success: {
			callback: TBG.Main.Helpers.Backdrop.reset
		}
	});
}

TBG.Config.User.getPermissionsBlock = function(url, user_id) {
	$('users_results_user_' + user_id + '_permissions_row').toggle();
	if ($('users_results_user_' + user_id + '_permissions').innerHTML == '') {
		TBG.Main.Helpers.ajax(url, {
			loading: {
				indicator: 'permissions_' + user_id + '_indicator'
			},
			success: {
				update: 'users_results_user_' + user_id + '_permissions'
			}
		});
	}
}

TBG.Config.Collection.add = function(url, type, callback_function) {
	TBG.Main.Helpers.ajax(url, {
		form: 'create_' + type + '_form',
		loading: {indicator: 'create_' + type + '_indicator'},
		success: {
			update: {element: type + 'config_list', insertion: true},
			callback: callback_function
		}
	});
}

TBG.Config.Collection.remove = function(url, type, cid, callback_function) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'delete_' + type + '_' + cid + '_indicator'},
		success: {
			remove: type + 'box_' + cid,
			callback: function(json) {
				TBG.Main.Helpers.Dialog.dismiss();
				if (callback_function) callback_function(json);
			}
		}
	});
}

TBG.Config.Collection.clone = function(url, type, cid, callback_function) {
	TBG.Main.Helpers.ajax(url, {
		form: 'clone_' + type + '_' + cid + '_form',
		loading: {indicator: 'clone_' + type + '_' + cid + '_indicator'},
		success: {
			update: {element: type + 'config_list', insertion: true},
			hide: 'clone_' + type + '_' + cid,
			callback: callback_function
		}
	});
}

TBG.Config.Collection.showMembers = function(url, type, cid) {
	$(type + '_members_' + cid + '_container').toggle();
	if ($(type + '_members_' + cid + '_list').innerHTML == '') {
		TBG.Main.Helpers.ajax(url, {
			loading: {indicator: type + '_members_' + cid + '_indicator'},
			success: {update: type + '_members_' + cid + '_list'},
			failure: {hide: type + '_members_' + cid + '_container'}
		});
	}
}

TBG.Config.Collection.updateDetailsFromJSON = function(json) {
	if (json.update_groups) {
		json.update_groups.ids.each(function(group_id) {
			if ($('group_'+group_id+'_membercount')) $('group_'+group_id+'_membercount').update(json.update_groups.membercounts[group_id]);
			$('group_members_'+group_id+'_container').hide();
			$('group_members_'+group_id+'_list').update('');
		});
	}
	if (json.update_teams) {
		json.update_teams.ids.each(function(team_id) {
			if ($('team_'+team_id+'_membercount')) $('team_'+team_id+'_membercount').update(json.update_teams.membercounts[team_id]);
			$('team_members_'+team_id+'_container').hide();
			$('team_members_'+team_id+'_list').update('');
		});
	}
}

TBG.Config.Group.add = function(url) {
	TBG.Config.Collection.add(url, 'group');
}

TBG.Config.Group.remove = function(url, group_id) {
	TBG.Config.Collection.remove(url, 'group', group_id);
}

TBG.Config.Group.clone = function(url, group_id) {
	TBG.Config.Collection.clone(url, 'group', group_id);
}

TBG.Config.Group.showMembers = function(url, group_id) {
	TBG.Config.Collection.showMembers(url, 'group', group_id);
}

TBG.Config.Team.updateLinks = function(json) {
	if ($('current_team_num_count')) $('current_team_num_count').update(json.total_count);
	$$('.copy_team_link').each(function(element) {
		(json.more_available) ? $(element).show() : $(element).hide();
	});
	(json.more_available) ? $('add_team_div').show() : $('add_team_div').hide();
}

TBG.Config.Team.add = function(url) {
	TBG.Config.Collection.add(url, 'team', TBG.Config.Team.updateLinks);
}

TBG.Config.Team.remove = function(url, team_id) {
	TBG.Config.Collection.remove(url, 'team', team_id, TBG.Config.Team.updateLinks);
}

TBG.Config.Team.clone = function(url, team_id) {
	TBG.Config.Collection.clone(url, 'team', team_id, TBG.Config.Team.updateLinks);
}

TBG.Config.Team.showMembers = function(url, team_id) {
	TBG.Config.Collection.showMembers(url, 'team', team_id);
}

TBG.Config.Client.add = function(url) {
	TBG.Config.Collection.add(url, 'client');
}

TBG.Config.Client.remove = function(url, client_id) {
	TBG.Config.Collection.remove(url, 'client', client_id);
}

TBG.Config.Client.showMembers = function(url, client_id) {
	TBG.Config.Collection.showMembers(url, 'client', client_id);
}

TBG.Config.Client.update = function(url, client_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'edit_client_' + client_id + '_form',
		loading: {indicator: 'edit_client_' + client_id + '_indicator'},
		success: {
			hide: 'edit_client_' + client_id,
			update: 'clientbox_' + client_id
		}
	});
}

TBG.Config.Workflows.Transition.remove = function(url, transition_id, direction) {
	var trans_sib = $('transition_' + transition_id).next(1);
	var parameters = "&direction=" + direction;
	TBG.Main.Helpers.ajax(url, {
		params: parameters,
		loading: {indicator: 'delete_transition_' + transition_id + '_indicator'},
		success: {remove: ['transition_' + transition_id, trans_sib, 'delete_transition_' + transition_id + '_confirm']}
	});
}

TBG.Config.Workflows.Scheme.copy = function(url, scheme_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'copy_workflow_scheme_' + scheme_id + '_form',
		loading: {indicator: 'copy_workflow_scheme_' + scheme_id + '_indicator'},
		success: {
			hide: 'copy_scheme_' + scheme_id + '_popup',
			update: {element: 'workflow_schemes_list', insertion: true}
		}
	});
}

TBG.Config.Workflows.Scheme.remove = function(url, scheme_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'delete_workflow_scheme_' + scheme_id + '_form',
		loading: {indicator: 'delete_workflow_scheme_' + scheme_id + '_indicator'},
		success: {
			remove: ['delete_scheme_' + scheme_id + '_popup', 'copy_scheme_' + scheme_id + '_popup', 'workflow_scheme_' + scheme_id],
			update: {element: 'workflow_schemes_list', insertion: true}
		}
	});
}

TBG.Config.Workflows._updateLinks = function(json){
	if ($('current_workflow_num_count')) $('current_workflow_num_count').update(json.total_count);
	$$('.copy_workflow_link').each(function (element) {
		(json.more_available) ? $(element).show() : $(element).hide();
	});
}

TBG.Config.Workflows.Workflow.copy = function(url, workflow_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'copy_workflow_' + workflow_id + '_form',
		loading: {indicator: 'copy_workflow_' + workflow_id + '_indicator'},
		success: {
			hide: 'copy_workflow_' + workflow_id + '_popup',
			update: {element: 'workflows_list', insertion: true},
			callback: TBG.Config.Workflows._updateLinks
		}
	});
}

TBG.Config.Workflows.Workflow.remove = function(url, workflow_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'delete_workflow_' + workflow_id + '_form',
		loading: {indicator: 'delete_workflow_' + workflow_id + '_indicator'},
		success: {
			remove: ['delete_workflow_' + workflow_id + '_popup', 'copy_workflow_' + workflow_id + '_popup', 'workflow_' + workflow_id],
			update: {element: 'workflows_list', insertion: true},
			callback: TBG.Config.Workflows._updateLinks
		}
	});
}

TBG.Config.Workflows.Scheme.update = function(url, scheme_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'workflow_scheme_form',
		loading: {indicator: 'workflow_scheme_indicator'}
	});
}

TBG.Config.Workflows.Transition.Validations.add = function(url, mode) {
	TBG.Main.Helpers.ajax(url, {
		form: 'workflowtransition' + mode + 'validationrule_add_form',
		loading: {indicator: 'workflowtransition' + mode + 'validationrule_add_indicator'},
		success: {
			hide: ['no_workflowtransition' + mode + 'validationrules', 'add_workflowtransition' + mode + 'validationrule_' + $('workflowtransition' + mode + 'validationrule_add_type').getValue()],
			update: {element: 'workflowtransition' + mode + 'validationrules_list', insertion: true}
		}
	});
}

TBG.Config.Workflows.Transition.Validations.update = function(url, rule_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'workflowtransitionvalidationrule_' + rule_id + '_form',
		loading: {indicator: 'workflowtransitionvalidationrule_' + rule_id + '_indicator'},
		success: {
			hide: ['workflowtransitionvalidationrule_' + rule_id + '_cancel_button', 'workflowtransitionvalidationrule_' + rule_id + '_edit'],
			update: 'workflowtransitionvalidationrule_' + rule_id + '_value',
			show: ['workflowtransitionvalidationrule_' + rule_id + '_edit_button', 'workflowtransitionvalidationrule_' + rule_id + '_delete_button', 'workflowtransitionvalidationrule_' + rule_id + '_description']
		}
	});
}

TBG.Config.Workflows.Transition.Validations.remove = function(url, rule_id, type, mode) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'workflowtransitionvalidationrule_' + rule_id + '_delete_indicator'},
		success: {
			remove: ['workflowtransitionvalidationrule_' + rule_id + '_delete', 'workflowtransitionvalidationrule_' + rule_id],
			show: ['add_workflowtransition' + type + 'validationrule_' + mode]
		}
	});
}

TBG.Config.Workflows.Transition.Actions.add = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'workflowtransitionaction_add_form',
		loading: {indicator: 'workflowtransitionaction_add_indicator'},
		success: {
			hide: ['no_workflowtransitionactions', 'add_workflowtransitionaction_' + $('workflowtransitionaction_add_type').getValue()],
			update: {element: 'workflowtransitionactions_list', insertion: true}
		}
	});
}

TBG.Config.Workflows.Transition.Actions.update = function(url, action_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'workflowtransitionaction_' + action_id + '_form',
		loading: {indicator: 'workflowtransitionaction_' + action_id + '_indicator'},
		success: {
			hide: ['workflowtransitionaction_' + action_id + '_cancel_button', 'workflowtransitionaction_' + action_id + '_edit'],
			update: 'workflowtransitionaction_' + action_id + '_value',
			show: ['workflowtransitionaction_' + action_id + '_edit_button', 'workflowtransitionaction_' + action_id + '_delete_button', 'workflowtransitionaction_' + action_id + '_description']
		}
	});
}

TBG.Config.Workflows.Transition.Actions.remove = function(url, action_id, type) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'workflowtransitionaction_' + action_id + '_delete_indicator'},
		success: {
			hide: ['workflowtransitionaction_' + action_id + '_delete', 'workflowtransitionaction_' + action_id],
			show: ['add_workflowtransitionaction_' + type]
		}
	});
}

/**
 * This function updates available issue reporting fields on page to match 
 * those returned by thebuggenie
 */
TBG.Issues.updateFields = function(url)
{
	if ($('issuetype_id').getValue() != 0) {
		$('issuetype_list').hide();
	}
	if ($('project_id').getValue() != 0 && $('issuetype_id').getValue() != 0) {
		$('report_more_here').hide();
		$('report_form').show();
		
		TBG.Main.Helpers.ajax(url, {
			loading: {indicator: 'report_issue_more_options_indicator'},
			params: 'issuetype_id=' + $('issuetype_id').getValue(),
			success: {
				callback: function(json) {
					json.available_fields.each(function (fieldname, key)
					{
						if ($(fieldname + '_div')) {
							if (json.fields[fieldname]) {
								var prev_val = '';
								if (json.fields[fieldname].values) {
									if ($(fieldname + '_additional') && $(fieldname + '_additional').visible()) {
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
									if ($(fieldname + '_div')) $(fieldname + '_div').show();
									if ($(fieldname + '_id')) $(fieldname + '_id').enable();
									if ($(fieldname + '_value')) $(fieldname + '_value').enable();
									if ($(fieldname + '_id_additional')) $(fieldname + '_id_additional').disable();
									if ($(fieldname + '_value_additional')) $(fieldname + '_value_additional').disable();
									if ($(fieldname + '_additional')) $(fieldname + '_additional').hide();
									if (json.fields[fieldname].values) {
										if ($(fieldname + '_id')) {
											$(fieldname + '_id').update('');
											for (var opt in json.fields[fieldname].values) {
												$(fieldname + '_id').insert('<option value="'+opt+'">'+json.fields[fieldname].values[opt]+'</option>');
											}
											$(fieldname + '_id').setValue(prev_val);
										}
									}
								}
								(json.fields[fieldname].required) ? $(fieldname + '_label').addClassName('required') : $(fieldname + '_label').removeClassName('required');
							} else {
								if ($(fieldname + '_div')) $(fieldname + '_div').hide();
								if ($(fieldname + '_id')) $(fieldname + '_id').disable();
								if ($(fieldname + '_value')) $(fieldname + '_value').disable();
								if ($(fieldname + '_additional')) $(fieldname + '_additional').hide();
								if ($(fieldname + '_id_additional')) $(fieldname + '_id_additional').disable();
								if ($(fieldname + '_value_additional')) $(fieldname + '_value_additional').disable();
							}
						}
					});
					var visible_fields = false;
					$('reportissue_extrafields').childElements().each(function(elm) {
						if (elm.visible()) visible_fields = true;
					})
					if (visible_fields) {
						$('reportissue_extrafields_none').hide();
					} else {
						$('reportissue_extrafields_none').show();
					}
				}
			},
			complete: {
				callback: function() {
					$('title').focus();
					$('report_issue_more_options_indicator').hide();
				}
			}
		});
	} else {
		$('report_form').hide();
		$('report_more_here').show();
		$('issuetype_list').show();
	}
	
}

/**
 * Displays the workflow transition popup dialog
 */
TBG.Issues.showWorkflowTransition = function(transition_id) {
	var existing_container = $('workflow_transition_fullpage').down('.workflow_transition');
	if (existing_container) {
		existing_container.hide();
		$('workflow_transition_container').insert(existing_container);
	}
	var workflow_div = $('issue_transition_container_' + transition_id);
	$('workflow_transition_fullpage').insert(workflow_div);
	$('workflow_transition_fullpage').appear({duration: 0.2});
	workflow_div.appear({duration: 0.2, afterFinish: function() {
		if ($('duplicate_finder_transition_' + transition_id)) {
			$('viewissue_find_issue_' + transition_id + '_input').observe('keypress', function(event) {
				if (event.keyCode == Event.KEY_RETURN) {
					TBG.Issues.findDuplicate($('duplicate_finder_transition_' + transition_id).getValue(), transition_id);
					event.stop();
				}
			});
		}
			
	}});
};

TBG.Issues.refreshRelatedIssues = function(url) {
	if ($('related_child_issues_inline')) {
		TBG.Main.Helpers.ajax(url, {
			loading: {indicator: 'related_issues_indicator'},
			success: {
				hide: 'no_child_issues',
				update: {element: 'related_child_issues_inline'},
				callback: function() {
					var childcount = $('related_child_issues_inline').childElements().size();
					var parentcount = $('related_parent_issues_inline').childElements().size();
					$('viewissue_related_issues_count').update(childcount + parentcount);
				}
			}
		});
	}
};

TBG.Issues.findRelated = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'viewissue_find_issue_form',
		loading: {indicator: 'find_issue_indicator'},
		success: {update: 'viewissue_relation_results'}
	});
	return false;
};

TBG.Issues.findDuplicate = function(url, transition_id) {
	TBG.Main.Helpers.ajax(url, {
		additional_params: 'searchfor=' + $('viewissue_find_issue_' + transition_id + '_input').getValue(),
		loading: {indicator: 'find_issue_' + transition_id + '_indicator'},
		success: {update: 'viewissue_' + transition_id + '_duplicate_results'}
	});
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

TBG.Issues.removeRelated = function(url, issue_id) {
	TBG.Main.Helpers.ajax(url, {
		loading: {indicator: 'related_issues_indicator'},
		success: {
			remove: 'related_issue_'+issue_id,
			callback: function() {
				var childcount = $('related_child_issues_inline').childElements().size();
				var parentcount = $('related_parent_issues_inline').childElements().size();
				if (childcount == 0) $('no_child_issues').show();
				if (parentcount == 0) $('no_parent_issues').show();
				$('viewissue_related_issues_count').update(childcount + parentcount);
			}
		}
	});
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

TBG.Issues.toggleBlocking = function(url, issue_id)
{
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'fullpage_backdrop',
			clear: 'fullpage_backdrop_content',
			show: 'fullpage_backdrop_indicator'
		},
		success: {
			callback: function(json) {
				$('more_actions_mark_notblocking_link_'+issue_id).toggle();
				$('more_actions_mark_blocking_link_'+issue_id).toggle();

				if ($('blocking_div')) {
					$('blocking_div').toggle();
				}
				if ($('issue_'+issue_id)) {
					$('issue_'+issue_id).toggleClassName('blocking');
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
				TBG.Main.updatePercentageLayout(json.percent);
				(mode == 'set') ? TBG.Issues.markAsChanged('percent_complete') : TBG.Issues.markAsUnchanged('percent_complete');
			}
		}
	});
}

TBG.Issues.Field.Updaters.dualFromJSON = function(issue_id, dualfield, field) {
	if (dualfield.id == 0) {
		$(field + '_table').hide();
		$('no_' + field).show();
	} else {
		$(field + '_content').update(dualfield.name);
		if (field == 'status') $('status_'+issue_id+'_color').setStyle({backgroundColor: dualfield.color});
		else if (field == 'issuetype') $('issuetype_image').src = dualfield.src;
		if ($('no_' + field)) $('no_' + field).hide();
		if ($(field + '_table')) $(field + '_table').show();
	}
}

TBG.Issues.Field.Updaters.fromObject = function(issue_id, object, field) {
	var fn = field + '_' + issue_id + '_name';
	var nf = 'no_' + field + '_' + issue_id;
	if (!$(fn)) {
		fn = field + '_name';
		nf = 'no_' + field;
	}
	if ((Object.isUndefined(object.id) == false && object.id == 0) || (object.value && object.value == '')) {
		$(fn).hide();
		$(nf).show();
	} else {
		$(fn).update(object.name);
		if (object.url) $(fn).href = object.url;
		$(nf).hide();
		$(fn).show();
	}
}

TBG.Issues.Field.Updaters.timeFromObject = function(issue_id, object, values, field) {
	var fn = field + '_' + issue_id + '_name';
	var nf = 'no_' + field + '_' + issue_id;
	if ($(fn) && $(nf)) {
		if (object.id == 0) {
			$(fn).hide();
			$(nf).show();
		} else {
			$(fn).update(object.name);
			$(nf).hide();
			$(fn).show();
		}
	}
	['points', 'hours', 'days', 'weeks', 'months'].each(function(unit) {
		if ($(field + '_' + issue_id + '_' + unit + '_input'))
			$(field + '_' + issue_id + '_' + unit + '_input').setValue(values[unit]);

		if ($(field + '_' + issue_id + '_' + unit)) {
			$(field + '_' + issue_id + '_' + unit).update(values[unit]);
			if (values[unit] == 0) {
				$(field + '_' + issue_id + '_' + unit).addClassName('faded_out');
			} else {
 				$(field + '_' + issue_id + '_' + unit).removeClassName('faded_out');
			}
		}
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
	if (['description', 'reproduction_steps', 'title'].indexOf(field) != -1) {
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
					if (field == 'status' || field == 'issuetype') TBG.Issues.Field.Updaters.dualFromJSON(json.issue_id, json.field, field);
					else if (field == 'percent_complete') TBG.Main.updatePercentageLayout(json.percent);
					else if (field == 'estimated_time' || field == 'spent_time') {
						TBG.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
						$(field + '_' + json.issue_id + '_change').hide();
					}
					else TBG.Issues.Field.Updaters.fromObject(json.issue_id, json.field, field);
					
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
			hide: [field + '_change', loading_show]
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

TBG.Issues.Field.setTime = function(url, field, issue_id) {
	TBG.Main.Helpers.ajax(url, {
		form: field + '_' + issue_id + '_form',
		loading: {
			indicator: field + '_' + issue_id + '_spinning',
			clear: field + '_' + issue_id + '_change_error',
			hide: field + '_' + issue_id + '_change_error'
		},
		success: {
			callback: function(json) {
				TBG.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
				(json.changed == true) ? TBG.Issues.markAsChanged(field) : TBG.Issues.markAsUnchanged(field);
				if ($('issue_'+issue_id)) {
					var fields = $('issue_'+issue_id).select('.sc_'+field);
					if (fields.size() > 0) {
						fields.each(function(sc_element) {
							if (json.field.name) {
								$(sc_element).update(json.field.name);
								$(sc_element).removeClassName('faded_out');
							} else {
								$(sc_element).update('-');
								$(sc_element).addClassName('faded_out');
							}
						});
					}
				}
			},
			hide: field + '_' + issue_id + '_change'
		},
		failure: {
			update: field + '_' + issue_id + '_change_error',
			show: field + '_' + issue_id + '_change_error',
			callback: function(json) {
				new Effect.Pulsate($(field + '_' + issue_id + '_change_error'));
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
					if (field == 'status' || field == 'issuetype') TBG.Issues.Field.Updaters.dualFromJSON(json.issue_id, json.field, field);
					else if (field == 'estimated_time' || field == 'spent_time') TBG.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
					else if (field == 'percent_complete') TBG.Main.updatePercentageLayout(json.field);
					else TBG.Issues.Field.Updaters.fromObject(json.issue_id, json.field, field);
					
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
		Effect.Pulsate($('issue_info_container'), {pulses: 6, duration: 2});
	}
	
	$(field + '_field').addClassName('issue_detail_changed');
	
	if ($('comment_save_changes')) $('comment_save_changes').checked = true;
}

TBG.Issues.markAsUnchanged = function(field)
{
	if ($(field + '_field')) {
		$(field + '_field').removeClassName('issue_detail_changed');
		$(field + '_field').removeClassName('issue_detail_unmerged');
		if ($('issue_view').select('.issue_detail_changed').size() == 0) {
			$('viewissue_changed').hide();
			$('viewissue_merge_errors').hide();
			$('viewissue_unsaved').hide();
			if ($('comment_save_changes')) $('comment_save_changes').checked = false;
		}
	}
}

TBG.Issues.ACL.toggle_checkboxes = function(element, issue_id) {
	var val = element.getValue();
	var opp_val = (val == 'restricted') ? 'public' : 'restricted';
	if ($(element).checked) {
		$('acl_'+issue_id+'_'+val).show();
		$('acl_'+issue_id+'_'+opp_val).hide();
	} else {
		$('acl_'+issue_id+'_'+val).hide();
		$('acl_'+issue_id+'_'+opp_val).show();
	}
};

TBG.Issues.ACL.addTarget = function(url, issue_id) {
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'acl_indicator_'+issue_id
		},
		success: {
			update: {element: 'issue_'+issue_id+'_access_list', insertion: true},
			hide: ['popup_find_acl_'+issue_id, 'issue_'+issue_id+'_access_list_none']
		}
	});
};

TBG.Issues.ACL.set = function(url, issue_id, mode) {
	TBG.Main.Helpers.ajax(url, {
		form: 'acl_'+issue_id+'_'+mode+'form',
		loading: {
			indicator: 'acl_indicator_'+issue_id
		},
		success: {
			callback: TBG.Main.Helpers.Backdrop.reset
		}
	});
};

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

TBG.Issues.updateWorkflowAssignee = function(url, assignee_id, assignee_type, transition_id, teamup)
{
	teamup = (teamup == undefined) ? 0 : 1;
	TBG.Main.Helpers.ajax(url, {
		loading: {
			indicator: 'popup_assigned_to_name_indicator_'+transition_id,
			hide: 'popup_no_assigned_to_'+transition_id,
			show: 'popup_assigned_to_name_'+transition_id
		},
		success: {
			update: 'popup_assigned_to_name_'+transition_id
		},
		complete: {
			callback: function() {
				$('popup_assigned_to_id_'+transition_id).setValue(assignee_id);
				$('popup_assigned_to_type_'+transition_id).setValue(assignee_type);
				$('popup_assigned_to_teamup_'+transition_id).setValue(teamup);
				if (teamup) {
					$('popup_assigned_to_teamup_info_'+transition_id).show();
				} else {
					$('popup_assigned_to_teamup_info_'+transition_id).hide();
				}
			},
			hide: ['popup_assigned_to_teamup_info_'+transition_id, 'popup_assigned_to_change_'+transition_id]
		}
	});
}

TBG.Issues.updateWorkflowAssigneeTeamup = function(url, assignee_id, assignee_type, transition_id)
{
	TBG.Issues.updateWorkflowAssignee(url, assignee_id, assignee_type, transition_id, true);
}

TBG.Search.Filter.add = function(url) {
	TBG.Main.Helpers.ajax(url, {
		params: '&filter_name=' + $('add_search_filter_dropdown').value + '&key=' + $('max_filters').value,
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

TBG.Search.Filter.setIdentifiable = function(url, filter, key, i_id, i_type)
{
	TBG.Main.Helpers.ajax(url, {
		additional_params: '&i_id=' + i_id + '&i_type=' + i_type,
		loading: {
			indicator: 'filter_'+filter+'_'+key+'_indicator'
		},
		success: {
			update: 'filter_'+filter+'_'+key+'_name'
		},
		complete: {
			callback: function() {
				$('filter_'+filter+'_'+key).setValue(i_id);
				$('filter_'+filter+'_'+key+'_type').setValue(i_id);
			},
			hide: 'filter_'+filter+'_'+key+'_popup'
		}
	});
}

TBG.Search.Filter.setTimestamp = function(filter, key) {
	var d = new Date();
	d.setFullYear($('filter_'+filter+'_'+key+'_year').value, $('filter_'+filter+'_'+key+'_month').value, $('filter_'+filter+'_'+key+'_day').value);
	$('filter_'+filter+'_'+key).setValue(parseInt(+d / 1000));
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
		success: {update: 'search_results'},
		complete: {
			callback: function() {
				TableKit.load();
			}
		}
	});
};

TBG.Search.toggleColumn = function(column) {
	$$('.sc_' + column).each(function(element) {
		element.toggle();
	});
};

TBG.Search.resetColumns = function() {
	TBG.Search.ResultViews[TBG.Search.current_result_view].visible.each(function(column) {
		$$('.scs_' + column).each(function(element) {
			element.show();
			if (TBG.Search.ResultViews[TBG.Search.current_result_view].default_visible.indexOf(column) != -1) {
				element.down('input').checked = true;
			} else {
				element.down('input').checked = false;
			}
		});
		$$('.sc_' + column).each(function(element) {
			element.show();
		});
	});
};

TBG.Search.setColumns = function(resultview, available_columns, visible_columns, default_columns) {
	TBG.Search.current_result_view = resultview;
	TBG.Search.ResultViews[resultview] = {
		available: available_columns,
		visible: visible_columns,
		default_visible: default_columns
	};
	TBG.Search.ResultViews[resultview].available.each(function(column) {
		$$('.scs_' + column).each(function(element) {
			element.show();
			if (TBG.Search.ResultViews[resultview].visible.indexOf(column) != -1) {
				element.down('input').checked = true;
			}
		});
	});
	$('scs_current_template').setValue(resultview);
	$('search_column_settings_toggler').show();
	$('search_column_settings_notoggler').hide();
}

TBG.Search.saveVisibleColumns = function(url) {
	TBG.Main.Helpers.ajax(url, {
		form: 'scs_column_settings_form',
		loading: {
			indicator: 'search_column_settings_indicator',
			callback: function() {
				$('search_column_settings_save_button').addClassName('disabled');
			}
		},
		success: {
			hide: 'search_column_settings_container',
			callback: function() {
				$('search_column_settings_save_button').toggleClassName('button-pressed');
			}
		},
		complete: {
			callback: function() {
				$('search_column_settings_save_button').removeClassName('disabled');
			}
		}
	});
};

TBG.Search.checkToggledCheckboxes = function() {
	var num_checked = 0;
	$('search_results').select('input[type=checkbox]').each(function(elm) {
		if (elm.checked) num_checked++;
	});

	if (num_checked == 0) {
		$('search_bulk_container_top').addClassName('unavailable');
		$('search_bulk_container_bottom').addClassName('unavailable');
		$('bulk_action_submit_top').addClassName('disabled');
		$('bulk_action_submit_bottom').addClassName('disabled');
	} else {
		$('search_bulk_container_top').removeClassName('unavailable');
		$('search_bulk_container_bottom').removeClassName('unavailable');
		if ($('bulk_action_selector_top').getValue() != '') 
			$('bulk_action_submit_top').removeClassName('disabled');

		if ($('bulk_action_selector_bottom').getValue() != '')
			$('bulk_action_submit_bottom').removeClassName('disabled');
	}
}

TBG.Search.toggleCheckboxes = function(chk_box) {
	var do_check = true;

	if ($(chk_box).hasClassName('semi-checked')) {
		$(chk_box).removeClassName('semi-checked');
		$(chk_box).checked = true;
		do_check = true;
	} else {
		do_check = $(chk_box).checked;
	}

	$(chk_box).up('table').down('tbody').select('input[type=checkbox]').each(function(element) {
		element.checked = do_check;
	});

	TBG.Search.checkToggledCheckboxes();
};

TBG.Search.toggleCheckbox = function(element) {
	var num_unchecked = 0;
	var num_checked = 0;
	element.up('tbody').select('input[type=checkbox]').each(function(elm) {
		if (!elm.checked) num_unchecked++;
		if (elm.checked) num_checked++;
	});

	var chk_box = element.up('table').down('thead').down('input[type=checkbox]');
	if (num_unchecked == 0) {
		chk_box.checked = true;
		chk_box.removeClassName('semi-checked');
	} else if (num_checked > 0) {
		chk_box.checked = true;
		chk_box.addClassName('semi-checked');
	} else {
		chk_box.checked = false;
		chk_box.removeClassName('semi-checked');
	}

	TBG.Search.checkToggledCheckboxes();
};

TBG.Search.bulkContainerChanger = function(mode) {
	var sub_container_id = 'bulk_action_subcontainer_' + $('bulk_action_selector_' + mode).getValue();
	$('search_results').select('.bulk_action_subcontainer').each(function(element) {
		element.hide();
	});
	if ($(sub_container_id + '_top')) {
		$(sub_container_id + '_top').show();
		$('bulk_action_submit_top').removeClassName('disabled');
		$(sub_container_id + '_bottom').show();
		$('bulk_action_submit_bottom').removeClassName('disabled');
		var dropdown_element = $(sub_container_id + '_' + mode).down('.focusable');
		if (dropdown_element != undefined) dropdown_element.focus();
	} else {
		$('bulk_action_submit_top').addClassName('disabled');
		$('bulk_action_submit_bottom').addClassName('disabled');
	}
};

TBG.Search.bulkChanger = function(mode) {
	var sub_container_id = 'bulk_action_' + $('bulk_action_selector_' + mode).getValue();
	var opp_mode = (mode == 'top') ? 'bottom' : 'top';

	if ($('bulk_action_selector_' + mode).getValue() == '') {
		$('bulk_action_submit_' + mode).addClassName('disabled');
		$('bulk_action_submit_' + opp_mode).addClassName('disabled');
	} else if (!$('search_bulk_container_' + mode).hasClassName('unavailable')) {
		$('bulk_action_submit_' + mode).removeClassName('disabled');
		$('bulk_action_submit_' + opp_mode).removeClassName('disabled');
	}
	$(sub_container_id + '_' + opp_mode).value = $(sub_container_id + '_' + mode).getValue();
}

TBG.Search.bulkPostProcess = function(json) {
	if (json.last_updated) {
		if (json.milestone_name != undefined && json.milestone_id) {
			if ($('milestone_list') != undefined) {
				if ($('milestone_' + json.milestone_id) == undefined) {
					TBG.Project.Milestone.retrieve(json.milestone_url, json.milestone_id, json.issue_ids);
				}
			}
			if ($('bulk_action_assign_milestone_top') != undefined && $('bulk_action_assign_milestone_top_' + json.milestone_id) == undefined) {
				$('bulk_action_assign_milestone_top').insert('<option value="'+json.milestone_id+'" id="bulk_action_assign_milestone_top_'+json.milestone_id+'">'+json.milestone_name+'</option>');
				$('bulk_action_assign_milestone_top').setValue(json.milestone_id);
				$('bulk_action_assign_milestone_top_name').hide();
			}
			if ($('bulk_action_assign_milestone_bottom') != undefined && $('bulk_action_assign_milestone_bottom_' + json.milestone_id) == undefined) {
				$('bulk_action_assign_milestone_bottom').insert('<option value="'+json.milestone_id+'" id="bulk_action_assign_milestone_bottom_'+json.milestone_id+'">'+json.milestone_name+'</option>');
				$('bulk_action_assign_milestone_bottom').setValue(json.milestone_id);
				$('bulk_action_assign_milestone_bottom_name').hide();
			}
		} 
		json.issue_ids.each(function(issue_id) {
			var issue_elm = $('issue_' + issue_id);
			if (issue_elm != undefined) {
				if (json.milestone_name != undefined) {
					var milestone_container = issue_elm.down('.sc_milestone');
					if (milestone_container != undefined) {
						milestone_container.update(json.milestone_name);
						if (json.milestone_name != '-') {
							milestone_container.removeClassName('faded_out');
						} else {
							milestone_container.addClassName('faded_out');
						}
					}
				}
				if (json.status != undefined) {
					var status_container = issue_elm.down('.sc_status');
					if (status_container != undefined) {
						status_container.down('.sc_status_name').update(json.status['name']);
						var status_color_item = status_container.down('.sc_status_color');
						if (status_color_item) status_color_item.setStyle({backgroundColor: json.status['color']});
					}
				}
				['resolution', 'priority', 'category', 'severity'].each(function(action) {
					if (json[action] != undefined) {
						var data_container = issue_elm.down('.sc_' + action);
						if (data_container != undefined) {
							data_container.update(json[action]['name']);
							if (json[action]['name'] != '-') {
								data_container.removeClassName('faded_out');
							} else {
								data_container.addClassName('faded_out');
							}
						}
						if ($(action + '_selector_' + issue_id) != undefined) {
							$(action + '_selector_' + issue_id).setValue(json[action]['id']);
						}
					}
				});
				var last_updated_container = issue_elm.down('.sc_last_updated');
				if (last_updated_container != undefined) {
					last_updated_container.update(json.last_updated);
				}
				if (json.closed != undefined) {
					if (json.closed) {
						issue_elm.addClassName('closed');
					} else {
						issue_elm.removeClassName('closed');
					}
				}
			}
		});
	}
}

TBG.Search.bulkWorkflowTransition = function(url, transition_id) {
	TBG.Main.Helpers.ajax(url, {
		form: 'bulk_workflow_transition_form',
		loading: {
			indicator: 'transition_working_'+transition_id+'_indicator',
			callback: function() {
				$$('.workflow_transition_submit_button').each(function(element) {
					$(element).addClassName('disabled');
				});
			}
		},
		success: {
			callback: function(json) {
				TBG.Search.bulkPostProcess(json)
				TBG.Main.Helpers.Backdrop.reset();
			}
		},
		complete: {
			callback: function() {
				$$('.workflow_transition_submit_button').each(function(element) {
					$(element).removeClassName('disabled');
				});
			}
		}
	});
};

TBG.Search.bulkUpdate = function(url, mode) {
	if ($('bulk_action_selector_' + mode).getValue() == '') return;
	var issues = '';
	$('search_results').select('tbody input[type=checkbox]').each(function(element) {
		if (element.checked) issues += '&issue_ids['+element.getValue()+']='+element.getValue();
	});

	if ($('bulk_action_selector_' + mode).getValue() == 'perform_workflow_step') {
		TBG.Main.Helpers.Backdrop.show($('bulk_action_subcontainer_perform_workflow_step_' + mode + '_url').getValue() + issues);
	} else {
		TBG.Main.Helpers.ajax(url, {
			form: 'bulk_action_form_' + mode,
			additional_params: issues,
			loading: {
				indicator: 'fullpage_backdrop',
				clear: 'fullpage_backdrop_content',
				show: 'fullpage_backdrop_indicator'
			},
			success: {
				callback: TBG.Search.bulkPostProcess
			}
		});
	}
};

TBG.Search.moveDown = function(event) {
	var selected_elements = $('search_results').select('tr.selected');
	var old_selected_element = (selected_elements.size() == 0) ? undefined : selected_elements[0];
	var new_selected_element = (old_selected_element == undefined) ? $('search_results').select('table tbody tr')[0] : old_selected_element.next();

	TBG.Search.move(old_selected_element, new_selected_element, event, true);
};

TBG.Search.moveUp = function(event) {
	var selected_elements = $('search_results').select('tr.selected');
	var old_selected_element = (selected_elements.size() == 0) ? undefined : selected_elements[selected_elements.size() - 1];
	var new_selected_element = (old_selected_element == undefined) ? $('search_results').select('table tbody tr')[0] : old_selected_element.previous();

	TBG.Search.move(old_selected_element, new_selected_element, event, true);
};

TBG.Search.move = function(old_selected_element, new_selected_element, event, move) {
	if (old_selected_element && new_selected_element) {
		$(old_selected_element).removeClassName('selected');
	}
	if (new_selected_element) {
		var ns = $(new_selected_element);
		ns.addClassName('selected');
		var offsets = ns.cumulativeOffset();
		var dimensions = ($('bulk_action_form_top')) ? $('bulk_action_form_top').getDimensions() : ns.getDimensions();
		if (event) event.preventDefault();
		if (move) {
			var top = document.viewport.getScrollOffsets().top;
			var v_height = document.viewport.getDimensions().height;
			var bottom = top + v_height;
			var is_above = top > offsets.top - dimensions.height;
			var is_below = bottom < offsets.top + dimensions.height;
			if (is_above || is_below) {
				if (is_above) window.scrollTo(0, offsets.top - dimensions.height);
				if (is_below) window.scrollTo(0, offsets.top + dimensions.height - v_height);
			}
		}
	}
}

TBG.Search.moveTo = function(event) {
	var selected_elements = $('search_results').select('tr.selected');
	if (selected_elements.size() > 0) {
		var selected_issue = selected_elements[0];
		var link = selected_issue.select('a.issue_link')[0];
		if (link) {
			window.location = link.href;
			event.preventDefault();
		}
	}
};

TBG.Search.initializeKeyboardNavigation = function() {
	Event.observe(document, 'keydown', function(event) {
		if (['INPUT', 'TEXTAREA'].indexOf(event.target.nodeName) != -1) return;
		if (Event.KEY_DOWN == event.keyCode) {
			TBG.Search.moveDown(event);
		}
		else if (Event.KEY_PAGEDOWN == event.keyCode) {
			for (var cc = 1; cc <= 5; cc++) {
				TBG.Search.moveDown(event);
			}
		}
		else if (Event.KEY_UP == event.keyCode) {
			TBG.Search.moveUp(event);
		}
		else if (Event.KEY_PAGEUP == event.keyCode) {
			for (var cc = 1; cc <= 5; cc++) {
				TBG.Search.moveUp(event);
			}
		}
		else if (Event.KEY_RETURN == event.keyCode) {
			TBG.Search.moveTo(event);
		}
	});
	$('search_results').select('tr').each(function(element) {
		element.observe('click', function(event) {
			var selected_elements = $('search_results').select('tr.selected');
			var old_selected_element = (selected_elements.size() == 0) ? undefined : selected_elements[selected_elements.size() - 1];
			TBG.Search.move(old_selected_element, this, null, false);
		});
	});
}

/*
	Simple OpenID Plugin
	http://code.google.com/p/openid-selector/

	This code is licensed under the New BSD License.
*/

TBG.Chart.config = {
	y_config: {color: '#AAA', min: 0, tickDecimals: 0},
	x_config: {color: '#AAA', tickDecimals: 0},
	grid_config: {
		color: '#CCC',
		borderWidth: 1,
		backgroundColor: {colors: ["#FFF", "#EEE"]},
		hoverable: true,
		autoHighlight: true
	}
};

TBG.OpenID = {
	version : '1.3', // version constant
	demo : false,
	demo_text : null,
	cookie_expires : 6 * 30, // 6 months.
	cookie_name : 'openid_provider',
	cookie_path : '/',

	img_path : 'images/',
	locale : 'en', // is set in openid-<locale>.js
	sprite : 'en', // usually equals to locale, is set in
	// openid-<locale>.js
	signin_text : null, // text on submit button on the form
	all_small : false, // output large providers w/ small icons
	image_title : '%openid_provider_name%', // for image title

	input_id : 'openid_identifier',
	provider_url : null,
	provider_id : null,
	providers_small : null,
	providers_large : null,

	/**
	 * Class constructor
	 *
	 * @return {Void}
	 */
	init : function() {
		var openid_btns = $('openid_btns');
		if( $('openid_choice') ){
			$('openid_choice').setStyle({
				display: 'block'
			});
		}
		if( $('openid_input_area') ){
			$('openid_input_area').innerHTML = "";
		}
		var i = 0;
		// add box for each provider
		for (id in this.providers_large) {
			box = this.getBoxHTML(id, this.providers_large[id], (this.all_small ? 'small' : 'large'), i++);
			openid_btns.insert(box);
		}
		if (this.providers_small) {
			openid_btns.insert('<br/>');
			for (id in this.providers_small) {
				box = this.getBoxHTML(id, this.providers_small[id], 'small', i++);
				openid_btns.insert(box);
			}
		}
//		$('openid_form').submit = this.submit;
//		var box_id = this.readCookie();
//		if (box_id) {
//			this.signin(box_id, true);
//		}
	},

	/**
	 * @return {String}
	 */
	getBoxHTML : function(box_id, provider, box_size, index) {
		var image_ext = box_size == 'small' ? '.ico.png' : '.png';
		return '<a title="' + this.image_title.replace('%openid_provider_name%', provider["name"]) + '" href="javascript:TBG.OpenID.signin(\'' + box_id + '\');"'
				+ 'class="' + box_id + ' openid_' + box_size + '_btn button button-silver"><img src="' + TBG.basepath + 'iconsets/oxygen/openid_providers.' + box_size + '/' + box_id + image_ext + '"></a>';
	},

	/**
	 * Provider image click
	 *
	 * @return {Void}
	 */
	signin : function(box_id) {
		var provider = (this.providers_large[box_id]) ? this.providers_large[box_id] : this.providers_small[box_id];
		if (!provider) {
			return;
		}
		this.highlight(box_id);
		this.provider_id = box_id;
		this.provider_url = provider['url'];
		// prompt user for input?
		if (provider['label']) {
			this.useInputBox(provider);
		} else {
			$('openid_input_area').innerHTML = '';
			this.submit();
			$('openid_form').submit();
		}
	},

	/**
	 * Sign-in button click
	 *
	 * @return {Boolean}
	 */
	submit : function() {
		var url = this.provider_url;
		var username_field = $('openid_username');
		var username = username_field ? $('openid_username').getValue() : '';
		if (url) {
			url = url.replace('{username}', username);
			this.setOpenIdUrl(url);
		}
		return true;
	},

	/**
	 * @return {Void}
	 */
	setOpenIdUrl : function(url) {
		var hidden = document.getElementById(this.input_id);
		if (hidden != null) {
			hidden.value = url;
		} else {
			$('openid_form').insert('<input type="hidden" id="' + this.input_id + '" name="' + this.input_id + '" value="' + url + '"/>');
		}
	},

	/**
	 * @return {Void}
	 */
	highlight : function(box_id) {
		// remove previous highlight.
		var highlight = $$('.openid_highlight');
		if (highlight[0]) {
			highlight[0].removeClassName('button-pressed');
			highlight[0].removeClassName('openid_highlight');
		}
		// add new highlight.
		var box = $$('.' + box_id)[0];
		box.addClassName('openid_highlight');
		box.addClassName('button-pressed');
	},

	setCookie : function(value) {
		var date = new Date();
		date.setTime(date.getTime() + (this.cookie_expires * 24 * 60 * 60 * 1000));
		var expires = "; expires=" + date.toGMTString();
		document.cookie = this.cookie_name + "=" + value + expires + "; path=" + this.cookie_path;
	},

	readCookie : function() {
		var nameEQ = this.cookie_name + "=";
		var ca = document.cookie.split(';');
		for ( var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ')
				c = c.substring(1, c.length);
			if (c.indexOf(nameEQ) == 0)
				return c.substring(nameEQ.length, c.length);
		}
		return null;
	},

	/**
	 * @return {Void}
	 */
	useInputBox : function(provider) {
		var input_area = $('openid_input_area');
		var html = '';
		var id = 'openid_username';
		var value = '';
		var label = provider['label'];
		var style = '';
		if (provider['name'] == 'OpenID') {
			id = this.input_id;
			value = 'http://';
			style = 'background: #FFF url(' + TBG.basepath + 'iconsets/oxygen/openid-inputicon.gif) no-repeat scroll 0 50%; padding-left:18px;';
		}
		html = '<input id="' + id + '" type="text" style="' + style + '" name="' + id + '" value="' + value + '" />';
		if (label) {
			html += '<label for="' + id + '">' + label + '</label>';
		}
		input_area.innerHTML = html;
		$('openid_submit_button').show();

//		$('openid_submit').onclick = this.submit;
		$(id).focus();
	},

	setDemoMode : function(demoMode) {
		this.demo = demoMode;
	}
};

TBG.Chart.burndownChart = function(burndown_data, time) {
	var d_b_points = [];
	var d_b_hours = [];
	var eh_keys = [];
	var eh_values = [];
	var ep_keys = [];
	var ep_values = [];

	for(var d in burndown_data.burndown.points) {
		if(burndown_data.burndown.points.hasOwnProperty(d)) {
			d_b_points.push([d * 1000, burndown_data.burndown.points[d]]);
		}
	}

	for(var d in burndown_data.burndown.hours) {
		if(burndown_data.burndown.hours.hasOwnProperty(d)) {
			d_b_hours.push([d * 1000, burndown_data.burndown.hours[d]]);
		}
	}

	for(var d in burndown_data.estimations.hours) {
		if(burndown_data.estimations.hours.hasOwnProperty(d)) {
			eh_keys.push(d);
			eh_values.push(burndown_data.estimations.hours[d]);
		}
	}
	for(var d in burndown_data.estimations.points) {
		if(burndown_data.estimations.points.hasOwnProperty(d)) {
			ep_keys.push(d);
			ep_values.push(burndown_data.estimations.points[d]);
		}
	}
	var d_e_velocity_hours = [[eh_keys.min() * 1000, eh_values.max()], [eh_keys.max() * 1000, 0]];
	var d_e_velocity_points = [[ep_keys.min() * 1000, ep_values.max()], [ep_keys.max() * 1000, 0]];
	console.log(d_e_velocity_points);
	console.log(d_b_hours);
		var x_config = TBG.Chart.config.x_config;
		x_config.mode = 'time';
		var grid_config = TBG.Chart.config.grid_config;
		grid_config.markings = [{xaxis: {from: time, to: time}, color: '#955', lineWidth: 1}];
		jQuery.plot(jQuery("#selected_burndown_image"), [
			{
				data: d_e_velocity_hours,
				dashes: {show: true, lineWidth: 1},
				points: {show: false},
				color: '#66F',
				label: 'Estimated velocity (hours)'
			},
			{
				data: d_e_velocity_points,
				dashes: {show: true, lineWidth: 1},
				points: {show: false},
				color: '#333',
				label: 'Estimated velocity (points)'
			},
			{
				data: d_b_hours,
				lines: {show: true},
				points: {show: true},
				color: '#923A6F',
				label: 'Hours burndown'
			},
			{
				data: d_b_points,
				lines: {show: true},
				points: {show: true},
				color: '#F83A39',
				label: 'Points burndown'
			}
		], {
		yaxis: TBG.Chart.config.y_config,
		xaxis: x_config,
		grid: grid_config
		});
};

	jQuery(document).ready(function(){
		TBG.Main.Helpers.MarkitUp(jQuery('textarea'));
	});
