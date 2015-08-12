define(['prototype', 'effects', 'controls', 'scriptaculous', 'jquery', 'jquery-ui', 'jquery.markitup', 'spectrum'],
    function (prototype, effects, controls, scriptaculous, jQuery, spectrum) {

        var TBG = {
            Core: {
                AjaxCalls: [],
                Pollers: {
                    Callbacks: {},
                    Locks: {}
                }
            }, // The "Core" namespace is for functions used by thebuggenie core, not to be invoked outside the js class
            Tutorial: {
                Stories: {}
            },
            Main: {// The "Main" namespace contains regular functions in use across the site
                Helpers: {
                    Message: {},
                    Dialog: {},
                    Backdrop: {}
                },
                Profile: {},
                Notifications: {},
                Dashboard: {
                    views: [],
                    View: {}
                },
                Comment: {},
                Link: {},
                Menu: {},
                Login: {},
                parent_articles: []
            },
            Chart: {},
            Modules: {},
            Themes: {},
            Project: {
                Statistics: {},
                Milestone: {},
                Planning: {
                    Whiteboard: {}
                },
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
            effect_queues: {
                successmessage: 'TBG_successmessage',
                failedmessage: 'TBG_failedmessage'
            },
            debug: false,
            activated_popoutmenu: undefined,
            autocompleter_url: undefined,
            available_fields: ['shortname', 'description', 'user_pain', 'reproduction_steps', 'category', 'resolution', 'priority', 'reproducability', 'percent_complete', 'severity', 'edition', 'build', 'component', 'estimated_time', 'spent_time', 'milestone', 'owned_by']
        };

        /**
         * Initializes the autocompleter
         */
        TBG.Core._initializeAutocompleter = function () {
            if ($('searchfor') == null)
                return;
            new Ajax.Autocompleter(
                "searchfor",
                "searchfor_autocomplete_choices",
                TBG.autocompleter_url,
                {
                    paramName: "fs[text][v]",
                    parameters: "fs[text][o]==",
                    minChars: 2,
                    indicator: 'quicksearch_indicator',
                    callback: function (element, entry) {
                        $('quicksearch_submit').disable();
                        $('quicksearch_submit').removeClassName('button-blue');
                        $('quicksearch_submit').addClassName('button-silver');
                        return entry;
                    },
                    afterUpdateChoices: function () {
                        $('quicksearch_submit').enable();
                        $('quicksearch_submit').removeClassName('button-silver');
                        $('quicksearch_submit').addClassName('button-blue');
                    },
                    afterUpdateElement: TBG.Core._extractAutocompleteValue
                }
            );
        };

        /**
         * Helper function to extract url from autocomplete response container
         */
        TBG.Core._extractAutocompleteValue = function (elem, value, event) {
            var elements = value.select('.url');
            if (elements.size() == 1) {
                window.location = elements[0].innerHTML.unescapeHTML();
                $('quicksearch_indicator').show();
                $('quicksearch_submit').disable();
                $('quicksearch_submit').removeClassName('button-blue');
                $('quicksearch_submit').addClassName('button-silver');
                $('searchfor').blur();
                $('searchfor').setValue('');
            } else {
                var cb_elements = value.select('.backdrop');
                if (cb_elements.size() == 1) {
                    var elm = cb_elements[0];
                    var backdrop_url = elm.down('.backdrop_url').innerHTML;
                    TBG.Main.Helpers.Backdrop.show(backdrop_url);
                    $('searchfor').blur();
                    $('searchfor').setValue('');
                    event.stopPropagation();
                    event.preventDefault();
                }
            }
        };

        /**
         * Monitors viewport resize to adapt backdrops
         */
        TBG.Core._resizeWatcher = function () {
            return;
            // TBG.Core._vp_width = document.viewport.getWidth();
            // TBG.Core._vp_height = document.viewport.getHeight();
            // if (($('attach_file') && $('attach_file').visible())) {
            //     var backdropheight = $('backdrop_detail_content').getHeight();
            //     if (backdropheight > (TBG.Core._vp_height - 100)) {
            //         $('backdrop_detail_content').setStyle({height: TBG.Core._vp_height - 100 + 'px', overflow: 'scroll'});
            //     } else {
            //         $('backdrop_detail_content').setStyle({height: 'auto', overflow: ''});
            //     }
            // }
            // TBG.Core.popupVisiblizer();
        };

        TBG.Core.popupVisiblizer = function () {
            return;
            // var visible_popups = $$('.dropdown_box').findAll(function (el) {
            //     return el.visible();
            // });
            // if (visible_popups.size()) {
            //     visible_popups.each(function (element) {
            //         if ($(element).hasClassName("user_popup"))
            //             return;
            //         var max_bottom = document.viewport.getHeight();
            //         var element_height = $(element).getHeight();
            //         var parent_offset = $(element).up().cumulativeOffset().top;
            //         var element_min_bottom = parent_offset + element_height + 35;
            //         if (max_bottom < element_min_bottom) {
            //             if ($(element).getStyle('position') != 'fixed') {
            //                 jQuery(element).data({'top': $(element).getStyle('top')});
            //             }
            //             $(element).setStyle({'position': 'fixed', 'bottom': '5px', 'top': 'auto'});
            //         } else {
            //             $(element).setStyle({'position': 'absolute', 'bottom': 'auto', 'top': jQuery(element).data('top')});
            //         }
            //     });
            // }
        };

        /**
         * Monitors viewport scrolling to adapt fixed positioners
         */
        TBG.Core._scrollWatcher = function () {
            var vhc = $('viewissue_header_container');
            if (vhc) {
                var iv = $('issue_view');
                var y = document.viewport.getScrollOffsets().top;
                var vihc = $('viewissue_header_container');
                var vihcl = vihc.getLayout();
                var compare_coord = (vihc.hasClassName('fixed')) ? iv.offsetTop : vihc.offsetTop;
                if (y >= compare_coord) {
                    $('issue_main_container').setStyle({marginTop: vihcl.get('height')+vihcl.get('margin-top')+vihcl.get('margin-bottom')+'px'});
                    $('issue_details_container').setStyle({marginTop: vihcl.get('height')+vihcl.get('margin-top')+vihcl.get('margin-bottom')+'px'});
                    vhc.addClassName('fixed');
                    $('workflow_actions').addClassName('fixed');
                    if ($('votes_additional').visible() && $('votes_additional').hasClassName('visible')) $('votes_additional').hide();
                    if ($('user_pain_additional').visible() && $('user_pain_additional').hasClassName('visible')) $('user_pain_additional').hide();
                    var vhc_layout = vhc.getLayout();
                    var vhc_height = vhc_layout.get('height') + vhc_layout.get('padding-top') + vhc_layout.get('padding-bottom');
                    if (y >= $('viewissue_comment_count').offsetTop) {
                        if ($('comment_add_button') != undefined) {
                            var button = $('comment_add_button').remove();
                            $('workflow_actions').down('ul').insert(button);
                        }
                    } else if ($('comment_add_button') != undefined) {
                        var button = $('comment_add_button').remove();
                        $('add_comment_button_container').update(button);
                    }
                } else {
                    $('issue_main_container').setStyle({marginTop: 0});
                    $('issue_details_container').setStyle({marginTop: 0});
                    vhc.removeClassName('fixed');
                    $('workflow_actions').removeClassName('fixed');
                    if (! $('votes_additional').visible() && $('votes_additional').hasClassName('visible')) $('votes_additional').show();
                    if (! $('user_pain_additional').visible() && $('user_pain_additional').hasClassName('visible')) $('user_pain_additional').show();
                    if ($('comment_add_button') != undefined) {
                        var button = $('comment_add_button').remove();
                        $('add_comment_button_container').update(button);
                    }
                }
            }
            if ($('bulk_action_form_top')) {
                var y = document.viewport.getScrollOffsets().top;
                var co = $('bulk_action_form_top').up('.bulk_action_container').cumulativeOffset();
                if (y >= co.top) {
                    $('bulk_action_form_top').addClassName('fixed');
                } else {
                    $('bulk_action_form_top').removeClassName('fixed');
                }
            }
            if ($('whiteboard')) {
                var y = document.viewport.getScrollOffsets().top;
                var co = $('whiteboard').cumulativeOffset();
                if (y >= co.top) {
                    $('whiteboard').addClassName('fixedheader');
                } else {
                    $('whiteboard').removeClassName('fixedheader');
                }
            }
            if ($('issues_paginator')) {
                var ip = $('issues_paginator');
                var ipl = ip.getLayout();
                var ip_height = ipl.get('height') + ipl.get('padding-top') + ipl.get('padding-bottom');

                var y = document.viewport.getScrollOffsets().top + document.viewport.getHeight();
                var y2 = $('body').scrollHeight;
                if (y >= y2 - ip_height) {
                    ip.removeClassName('visible');
                } else {
                    ip.addClassName('visible');
                }
            }
        };

        /**
         * Toggles one breadcrumb item in the breadcrumb bar
         */
        TBG.Core._toggleBreadcrumbItem = function (item) {
            item.up('li').next().toggleClassName('popped_out');
            item.toggleClassName('activated');
        };

        /**
         * Toggles one breadcrumb item in the breadcrumb bar
         */
        TBG.Core._hideBreadcrumbItem = function () {
            if ($('submenu')) {
                $('submenu').select('.popped_out').each(function (element) {
                    element.removeClassName('popped_out');
                    element.previous().down('.activated').removeClassName('activated');
                });
            }
        };

        TBG.Core._detachFile = function (url, file_id, base_id, loading_indicator) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: typeof(loading_indicator) != 'undefined' ? loading_indicator : base_id + file_id + '_remove_indicator',
                    hide: [base_id + file_id + '_remove_link', 'uploaded_files_' + file_id + '_remove_link'],
                    show: 'uploaded_files_' + file_id + '_remove_indicator'
                },
                success: {
                    remove: [base_id + file_id, 'uploaded_files_' + file_id, base_id + file_id + '_remove_confirm', 'uploaded_files_' + file_id + '_remove_confirm'],
                    callback: function (json) {
                        if (json.attachmentcount == 0 && $('viewissue_no_uploaded_files'))
                            $('viewissue_no_uploaded_files').show();
                        if ($('viewissue_uploaded_attachments_count'))
                            $('viewissue_uploaded_attachments_count').update(json.attachmentcount);
                        TBG.Main.Helpers.Dialog.dismiss();
                    }
                },
                failure: {
                    show: [base_id + file_id + '_remove_link', 'uploaded_files_' + file_id + '_remove_link'],
                    hide: 'uploaded_files_' + file_id + '_remove_indicator'
                }
            });
        };

        TBG.Core._processCommonAjaxPostEvents = function (options) {
            if (options.remove) {
                if (is_string(options.remove)) {
                    if ($(options.remove))
                        $(options.remove).remove();
                } else {
                    options.remove.each(function (s) {
                        if (is_string(s) && $(s))
                            $(s).remove();
                        else if ($(s))
                            s.remove();
                    });
                }
            }
            if (options.hide) {
                if (is_string(options.hide)) {
                    if ($(options.hide))
                        $(options.hide).hide();
                } else {
                    options.hide.each(function (s) {
                        if (is_string(s) && $(s))
                            $(s).hide();
                        else if ($(s))
                            s.hide();
                    });
                }
            }
            if (options.show) {
                if (is_string(options.show)) {
                    if ($(options.show))
                        $(options.show).show();
                } else {
                    options.show.each(function (s) {
                        if ($(s))
                            $(s).show();
                    });
                }
            }
            if (options.enable) {
                if (is_string(options.enable)) {
                    if ($(options.enable))
                        $(options.enable).enable();
                } else {
                    options.enable.each(function (s) {
                        if ($(s))
                            $(s).enable();
                    });
                }
            }
            if (options.disable) {
                if (is_string(options.disable)) {
                    if ($(options.disable))
                        $(options.disable).disable();
                } else {
                    options.disable.each(function (s) {
                        if ($(s))
                            $(s).disable();
                    });
                }
            }
            if (options.reset) {
                if (is_string(options.reset)) {
                    if ($(options.reset))
                        $(options.reset).reset();
                } else {
                    options.reset.each(function (s) {
                        if ($(s))
                            $(s).reset();
                    });
                }
            }
            if (options.clear) {
                if (is_string(options.clear)) {
                    if ($(options.clear))
                        $(options.clear).clear();
                } else {
                    options.clear.each(function (s) {
                        if ($(s))
                            $(s).clear();
                    });
                }
            }
        };

        TBG.Core._escapeWatcher = function (event) {
            if (Event.KEY_ESC != event.keyCode)
                return;
            TBG.Main.Helpers.Backdrop.reset();
        };

        /**
         * Main initializer function
         * Sets up and initializes autocompleters, watchers, etc
         *
         * @param {Object} options A {key: value} store with options to set
         */
        TBG.initialize = function (options) {
            for (var key in options) {
                TBG[key] = options[key];
            }
            TBG.Core._initializeAutocompleter();
            Event.observe(window, 'resize', TBG.Core._resizeWatcher);
            Event.observe(window, 'scroll', TBG.Core._scrollWatcher);
            TBG.Core._resizeWatcher();
            TBG.Core._scrollWatcher();
            if ($$('.dashboard_view_container').size() > 0) {
                $$('.dashboard_view_container').each(function (view) {
                    TBG.Main.Dashboard.View.init(parseInt(view.dataset.viewId));
                });
            } else {
                $$('html')[0].setStyle({'cursor': 'default'});
            }
            $('fullpage_backdrop_content').observe('click', TBG.Core._resizeWatcher);
            document.observe('keydown', TBG.Core._escapeWatcher);

            TBG.Core.Pollers.Callbacks.dataPoller();
            TBG.OpenID.init();
            // Mimick browser scroll to element with id as hash once header get 'fixed' class
            // from _scrollWatcher method.
            setTimeout(function () {
                var hash = window.location.hash;
                if (hash != undefined && hash.indexOf('comment_') == 1 && typeof(window.location.href) == 'string') {
                    window.location.href = window.location.href;
                }
            }, 1000);
        };

        TBG.Core.Pollers.Callbacks.dataPoller = function () {
            if (!TBG.Core.Pollers.Locks.datapoller) {
                TBG.Core.Pollers.Locks.datapoller = true;
                TBG.Main.Helpers.ajax(TBG.data_url, {
                    url_method: 'get',
                    success: {
                        callback: function (json) {
                            var unc = $('user_notifications_count');
                            if (unc) {
                                if (parseInt(json.unread_notifications) != parseInt(unc.innerHTML)) {
                                    unc.update(json.unread_notifications);
                                    if (parseInt(json.unread_notifications) > 0) {
                                        unc.addClassName('unread');
                                    } else {
                                        unc.removeClassName('unread');
                                    }
                                }
                            }
                            TBG.Core.Pollers.Locks.datapoller = false;
                            if (TBG.Core.Pollers.datapoller != null)
                                TBG.Core.Pollers.datapoller.stop();
                            var interval = parseInt(json.poll_interval);
                            TBG.Core.Pollers.datapoller = interval > 0 ? new PeriodicalExecuter(TBG.Core.Pollers.Callbacks.dataPoller, interval) : null;
                        }
                    }
                });
            }
        };

        TBG.Main.Profile.toggleNotifications = function () {
            var un = $('user_notifications');
            var unc = $('user_notifications_container');
            unc.toggleClassName('active');
            if (un.hasClassName('active')) {
                un.removeClassName('active');
            } else {
                un.style.right = (jQuery(window).width() - (jQuery('#user_notifications_container').offset().left + jQuery('#user_notifications_container').outerWidth()) - parseInt(jQuery('#user_notifications').css('border-right-width'), 10)) + 'px';
                un.addClassName('active');
                if ($('user_notifications_list').childElements().size() == 0) {
                    TBG.Main.Helpers.ajax($('user_notifications_list').dataset.notificationsUrl, {
                        url_method: 'get',
                        loading: {
                            indicator: 'user_notifications_loading_indicator'
                        },
                        success: {
                            update: 'user_notifications_list',
                            callback: function () {
                                jQuery('#user_notifications_list_wrapper_nano').nanoScroller();
                                jQuery('#user_notifications_list_wrapper_nano').bind('scrollend', TBG.Main.Notifications.loadMore);
                            }
                        }
                    });
                }
            }
        };

        TBG.loadDebugInfo = function (debug_id, cb) {
            var url = TBG.debugUrl.replace('___debugid___', debug_id);
            TBG.Main.Helpers.ajax(url, {
                url_method: 'get',
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
        TBG.Main.Helpers.Message.clear = function () {
            Effect.Queues.get(TBG.effect_queues.successmessage).each(function (effect) {
                effect.cancel();
            });
            Effect.Queues.get(TBG.effect_queues.failedmessage).each(function (effect) {
                effect.cancel();
            });
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
        TBG.Main.Helpers.Message.error = function (title, content) {
            $('thebuggenie_failuremessage_title').update(title);
            $('thebuggenie_failuremessage_content').update(content);
            if ($('thebuggenie_successmessage').visible()) {
                Effect.Queues.get(TBG.effect_queues.successmessage).each(function (effect) {
                    effect.cancel();
                });
                new Effect.Fade('thebuggenie_successmessage', {queue: {position: 'end', scope: TBG.effect_queues.successmessage, limit: 2}, duration: 0.2});
            }
            if ($('thebuggenie_failuremessage').visible()) {
                Effect.Queues.get(TBG.effect_queues.failedmessage).each(function (effect) {
                    effect.cancel();
                });
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
        TBG.Main.Helpers.Message.success = function (title, content) {
            $('thebuggenie_successmessage_title').update(title);
            $('thebuggenie_successmessage_content').update(content);
            if (title || content) {
                if ($('thebuggenie_failuremessage').visible()) {
                    Effect.Queues.get(TBG.effect_queues.failedmessage).each(function (effect) {
                        effect.cancel();
                    });
                    new Effect.Fade('thebuggenie_failuremessage', {queue: {position: 'end', scope: TBG.effect_queues.failedmessage, limit: 2}, duration: 0.2});
                }
                if ($('thebuggenie_successmessage').visible()) {
                    Effect.Queues.get(TBG.effect_queues.successmessage).each(function (effect) {
                        effect.cancel();
                    });
                    new Effect.Pulsate('thebuggenie_successmessage', {duration: 1, pulses: 4});
                } else {
                    new Effect.Appear('thebuggenie_successmessage', {queue: {position: 'end', scope: TBG.effect_queues.successmessage, limit: 2}, duration: 0.2});
                }
                new Effect.Fade('thebuggenie_successmessage', {queue: {position: 'end', scope: TBG.effect_queues.successmessage, limit: 2}, delay: 10, duration: 0.2});
            } else if ($('thebuggenie_successmessage').visible()) {
                Effect.Queues.get(TBG.effect_queues.successmessage).each(function (effect) {
                    effect.cancel();
                });
                new Effect.Fade('thebuggenie_successmessage', {queue: {position: 'end', scope: TBG.effect_queues.successmessage, limit: 2}, duration: 0.2});
            }
        };

        TBG.Main.Helpers.Dialog.show = function (title, content, options) {
            TBG.Main.Helpers.Message.clear();
            $('dialog_title').update(title);
            $('dialog_content').update(content);
            $('dialog_yes').setAttribute('href', 'javascript:void(0)');
            $('dialog_no').setAttribute('href', 'javascript:void(0)');
            $('dialog_yes').stopObserving('click');
            $('dialog_no').stopObserving('click');
            $('dialog_yes').removeClassName('disabled');
            $('dialog_no').removeClassName('disabled');
            if (options.yes.click) {
                $('dialog_yes').observe('click', options.yes.click);
            }
            if (options.yes.href) {
                $('dialog_yes').setAttribute('href', options.yes.href);
            }
            if (options.no.click) {
                $('dialog_no').observe('click', options.no.click);
            }
            if (options.no.href) {
                $('dialog_no').setAttribute('href', options.no.href);
            }
            $('dialog_backdrop_content').show();
            $('dialog_backdrop').appear({duration: 0.2});
        };
        TBG.Main.Helpers.Dialog.showModal = function (title, content) {
            TBG.Main.Helpers.Message.clear();
            $('dialog_modal_title').update(title);
            $('dialog_modal_content').update(content);
            $('dialog_backdrop_modal_content').show();
            $('dialog_backdrop_modal').appear({duration: 0.2});
        };

        TBG.Main.Helpers.Dialog.dismiss = function () {
            $('dialog_backdrop_content').fade({duration: 0.2});
            $('dialog_backdrop').fade({duration: 0.2});
        };
        TBG.Main.Helpers.Dialog.dismissModal = function () {
            $('dialog_backdrop_modal_content').fade({duration: 0.2});
            $('dialog_backdrop_modal').fade({duration: 0.2});
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
        TBG.Main.Helpers.ajax = function (url, options) {
            var params = (options.params) ? options.params : '';
            if (options.form && options.form != undefined)
                params = Form.serialize(options.form);
            if (options.additional_params)
                params += options.additional_params;
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
                            d_id = response.getHeader('x-tbg-debugid'),
                            d_time = response.getHeader('x-tbg-loadtime');

                        TBG.Core.AjaxCalls.push({location: url, time: d, debug_id: d_id, loadtime: d_time});
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

        TBG.updateDebugInfo = function () {
            var lai = $('log_ajax_items');
            if (lai) {
                $('log_ajax_items').update('');
                if ($('debug_ajax_count'))
                    $('debug_ajax_count').update(TBG.Core.AjaxCalls.size());
                var ct = function (time) {
                    return (time < 10) ? '0' + time : time;
                };
                TBG.Core.AjaxCalls.each(function (info) {
                    var content = '<li><span class="badge timestamp">' + ct(info.time.getHours()) + ':' + ct(info.time.getMinutes()) + ':' + ct(info.time.getSeconds()) + '.' + ct(info.time.getMilliseconds()) + '</span><span class="badge timing">' + info.loadtime + '</span><span class="partial">' + info.location + '</span> <a class="button button-silver" style="float: right;" href="javascript:void(0);" onclick="TBG.loadDebugInfo(\'' + info.debug_id + '\');">Debug</a></li>';
                    lai.insert(content, 'top');
                });
            }
        };

        TBG.Main.Helpers.formSubmit = function (url, form_id) {
            TBG.Main.Helpers.ajax(url, {
                form: form_id,
                loading: {indicator: form_id + '_indicator', disable: form_id + '_button'},
                success: {enable: form_id + '_button'},
                failure: {enable: form_id + '_button'}
            });
        };

        TBG.Main.Helpers.Backdrop.show = function (url, callback) {
            $('fullpage_backdrop_content').fade({duration: 0});
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
                            TBG.Main.Helpers.MarkitUp($$('textarea.markuppable'));
                            setTimeout(TBG.Main.Helpers.initializeFancyFilters, 300);
                            if (callback)
                                setTimeout((callback)(), 300);
                        }},
                    failure: {hide: 'fullpage_backdrop'}
                });
            }
        };

        TBG.Main.Helpers.Backdrop.reset = function (callback) {
            $$('body')[0].setStyle({'overflow': 'auto'});
            $('fullpage_backdrop').fade({duration: 0.2});
            TBG.Core._resizeWatcher();
            if (callback) callback();
        };

        TBG.Main.Helpers.tabSwitcher = function (visibletab, menu) {
            if ($(menu)) {
                $(menu).childElements().each(function (item) {
                    item.removeClassName('selected');
                });
                $(visibletab).addClassName('selected');
                $(menu + '_panes').childElements().each(function (item) {
                    item.hide();
                });
                $(visibletab + '_pane').show();
            }
        };

        TBG.Main.Helpers.MarkitUp = function (element) {
            var elements = (element.hasClassName) ? [element] : element;

            elements.each(function (elm) {
                if ($(elm).hasClassName('syntax_mw')) {
                    var ms = [
                        {name: 'Heading 1', key: '1', openWith: '== ', closeWith: ' ==', placeHolder: 'Your title here...'},
                        {name: 'Heading 2', key: '2', openWith: '=== ', closeWith: ' ===', placeHolder: 'Your title here...'},
                        {name: 'Heading 3', key: '3', openWith: '==== ', closeWith: ' ====', placeHolder: 'Your title here...'},
                        {name: 'Heading 4', key: '4', openWith: '===== ', closeWith: ' =====', placeHolder: 'Your title here...'},
                        {name: 'Heading 5', key: '5', openWith: '====== ', closeWith: ' ======', placeHolder: 'Your title here...'},
                        {separator: '---------------'},
                        {name: 'Bold', key: 'B', openWith: "'''", closeWith: "'''"},
                        {name: 'Italic', key: 'I', openWith: "''", closeWith: "''"},
                        {name: 'Stroke through', key: 'S', openWith: '<strike>', closeWith: '</strike>'},
                        {separator: '---------------'},
                        {name: 'Bulleted list', openWith: '(!(* |!|*)!)'},
                        {name: 'Numeric list', openWith: '(!(# |!|#)!)'},
                        {separator: '---------------'},
                        {name: 'Picture', key: "P", replaceWith: '[[Image:[![Url:!:http://]!]|[![name]!]]]'},
                        {name: 'Link', key: "L", openWith: "[[[![Url:!:http://]!]|", closeWith: ']]', placeHolder: 'Your text to link here...'},
                        {name: 'Url', openWith: "[[![Url:!:http://]!] ", closeWith: ']', placeHolder: 'Your text to link here...'},
                        {separator: '---------------'},
                        {name: 'Quotes', openWith: '(!(> |!|>)!)', placeHolder: ''},
                        {name: 'Code', openWith: '(!(<source lang="[![Language:!:php]!]">|!|<pre>)!)', closeWith: '(!(</source>|!|</pre>)!)'}
                    ];
                } else {
                    var ms = [
                        {name: 'First Level Heading', key: '1', placeHolder: 'Your title here...', closeWith: function (markItUp) {
                            return TBG.Main.Helpers.miu.markdownTitle(markItUp, '=')
                        }},
                        {name: 'Second Level Heading', key: '2', placeHolder: 'Your title here...', closeWith: function (markItUp) {
                            return TBG.Main.Helpers.miu.markdownTitle(markItUp, '-')
                        }},
                        {name: 'Heading 3', key: '3', openWith: '### ', placeHolder: 'Your title here...'},
                        {name: 'Heading 4', key: '4', openWith: '#### ', placeHolder: 'Your title here...'},
                        {name: 'Heading 5', key: '5', openWith: '##### ', placeHolder: 'Your title here...'},
                        {separator: '---------------'},
                        {name: 'Bold', key: 'B', openWith: '**', closeWith: '**'},
                        {name: 'Italic', key: 'I', openWith: '_', closeWith: '_'},
                        {name: 'Stroke through', key: 'S', openWith: '~~', closeWith: '~~'},
                        {separator: '---------------'},
                        {name: 'Bulleted List', openWith: '- '},
                        {name: 'Numeric List', openWith: function (markItUp) {
                            return markItUp.line + '. ';
                        }},
                        {separator: '---------------'},
                        {name: 'Picture', key: 'P', replaceWith: '![[![Alternative text]!]]([![Url:!:http://]!] "[![Title]!]")'},
                        {name: 'Link', key: 'L', openWith: '[', closeWith: ']([![Url:!:http://]!] "[![Title]!]")', placeHolder: 'Your text to link here...'},
                        {name: 'Url', openWith: '[', closeWith: ']([![Url:!:http://]!])', placeHolder: 'Your text to link here...'},
                        {separator: '---------------'},
                        {name: 'Quotes', openWith: '> '},
                        {name: 'Code', openWith: '(!(\t|!|`)!)', closeWith: '(!(`)!)'}
                    ];
                }
                jQuery(elm).markItUpRemove();
                jQuery(elm).markItUp({
                    previewParserPath: '', // path to your Wiki parser
                    onShiftEnter: {keepDefault: false, replaceWith: '\n\n'},
                    markupSet: ms
                });
            });
        };

    // mIu nameSpace to avoid conflict.
        TBG.Main.Helpers.miu = {
            markdownTitle: function (markItUp, char) {
                heading = '';
                n = jQuery.trim(markItUp.selection || markItUp.placeHolder).length;
                for (i = 0; i < n; i++) {
                    heading += char;
                }
                return '\n' + heading + '\n';
            }
        };

        TBG.Main.Helpers.setSyntax = function (base_id, syntax) {
            var ce = $(base_id);
            var cec = $(base_id).up('.textarea_container');

            ['mw', 'md', 'pt'].each(function (sntx) {
                ce.removeClassName('syntax_' + sntx);
                cec.removeClassName('syntax_' + sntx);
            });

            ce.addClassName('syntax_' + syntax);
            cec.addClassName('syntax_' + syntax);

            $(base_id + '_syntax').setValue(syntax);

            $(base_id + '_syntax_picker').childElements().each(function (elm) {
                if (elm.hasClassName(syntax)) {
                    elm.addClassName('selected');
                    $(base_id + '_selected_syntax').update(elm.dataset.syntaxName);
                } else {
                    elm.removeClassName('selected');
                }
            });

            TBG.Main.Helpers.MarkitUp(ce);
        };

        TBG.Main.toggleBreadcrumbMenuPopout = function (event) {
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

        TBG.Main.findIdentifiable = function (url, field) {
            TBG.Main.Helpers.ajax(url, {
                form: field + '_form',
                loading: {indicator: field + '_spinning'},
                success: {
                    update: field + '_results',
                    show: field + '_results_container'
                }
            });
        };

        TBG.Main.updatePercentageLayout = function (arg1, arg2) {
            if (isNaN(arg1))
            {
                $(arg1).style.width = arg2 + "%";
            } else {
                $('percent_complete_content').select('.percent_filled').first().style.width = arg1 + '%';
            }
        };

        TBG.Main.showUploader = function (url) {
            if (window.File && window.FileList && window.FileReader) {
                url += '&uploader=dynamic';
            } else {
                url += '&uploader=legacy';
            }
            TBG.Main.Helpers.Backdrop.show(url);
        };

        TBG.Main.updateAttachments = function (form) {
            var url = form.action;
            TBG.Main.Helpers.ajax(url, {
                form: form,
                url_method: 'post',
                loading: {
                    indicator: 'attachments_indicator',
                    callback: function () {
                        $('dynamic_uploader_submit').addClassName('disabled');
                        $('dynamic_uploader_submit').disable();
                    }
                },
                success: {
                    callback: function (json) {
                        TBG.Main.Helpers.Backdrop.reset();
                        var base = $(json.container_id);
                        if (base !== undefined) {
                            base.update('');
                            json.files.each(function (file_elm) {
                                base.insert(file_elm);
                            });
                            if (json.files.length) {
                                if ($('viewissue_uploaded_attachments_count')) $('viewissue_uploaded_attachments_count').update(json.files.length);
                                $('viewissue_no_uploaded_files').hide();
                            }
                        }
                    }
                },
                complete: {
                    callback: function () {
                        $('dynamic_uploader_submit').addClassName('disabled');
                        $('dynamic_uploader_submit').enable();
                    }
                }
            });

        };

        TBG.Main.uploadFile = function (url, file, is_last) {
            var is_last = is_last != undefined ? is_last : true;
            var fileSize = 0;
            if (file.size > 1024 * 1024) {
                fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
            } else {
                fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
            }
            var ful = $('file_upload_list');
            var elm = '<li><span class="imagepreview"><img src="' + ful.dataset.previewSrc + '"></span>';
            var isimage = false;
            if (file.type.indexOf("image") == 0) {
                isimage = true;
            }
            elm += '<label>' + ful.dataset.filenameLabel + '</label><span class="filename">' + file.name + '</span> <span class="filesize">' + fileSize + '</span><br><label>' + ful.dataset.descriptionLabel + '</label><input type="text" class="file_description" value="" placeholder="' + ful.dataset.descriptionPlaceholder + '"> <span class="progress"></span></li>';
            ful.insert(elm);
            var inserted_elm = $('file_upload_list').childElements().last();
            if (isimage) {
                var image_elm = inserted_elm.down('img');
                var reader = new FileReader();
                reader.onload = function (e) {
                    image_elm.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
            var progress_elm = inserted_elm.down('.progress');
            var formData = new FormData();
            formData.append(file.name, file);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', url, true);
            xhr.onload = function (e) {
                var data = JSON.parse(this.response);
                if (data.file_id != undefined) {
                    inserted_elm.insert('<input type="hidden" name="files[' + data.file_id + ']" value="' + data.file_id + '">');
                    inserted_elm.down('.file_description').name = "file_description[" + data.file_id + ']';
                } else {
                    inserted_elm.remove();
                    TBG.Main.Helpers.Message.error(json.error);
                }
                if (is_last && $('dynamic_uploader_submit') && $('dynamic_uploader_submit').disabled) $('dynamic_uploader_submit').enable();
            };

            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    var percent = (e.loaded / e.total) * 100;
                    progress_elm.setStyle({width: percent + '%'});
                    if (percent == 100) {
                        progress_elm.addClassName('completed');
    //					progressBar.textContent = progressBar.value; // Fallback for unsupported browsers.
                        $('file_upload_dummy').value = null;
                    }
                }
            };

            if ($('dynamic_uploader_submit') && !$('dynamic_uploader_submit').disabled) $('dynamic_uploader_submit').disable();
            xhr.send(formData);
        };

        TBG.Main.selectFiles = function (elm) {
            var files = $(elm).files;
            var url = elm.dataset.uploadUrl;
            if (files.length > 0) {
                for (var i = 0, file; file = files[i]; i++) {
                    TBG.Main.uploadFile(url, file, i == files.length - 1);
                }
            }
        };

        TBG.Main.dragOverFiles = function (evt) {
            evt.stopPropagation();
            evt.preventDefault();
            if (evt.type == "dragover") {
                $(evt.target).addClassName("file_hover");
            } else {
                $(evt.target).removeClassName("file_hover");
            }
            evt.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
        };

        TBG.Main.dropFiles = function (evt) {
            var elm = $('file_upload_dummy');
            var url = elm.dataset.uploadUrl;
            var files = evt.target.files || evt.dataTransfer.files;
            TBG.Main.dragOverFiles(evt);
            if (files.length > 0) {
                for (var i = 0, file; file = files[i]; i++) {
                    TBG.Main.uploadFile(url, file, i == files.length - 1);
                }
            }
        };

        TBG.Main.submitIssue = function (url) {
            if ($('report_issue_submit_button').hasClassName('disabled'))
                return;

            TBG.Main.Helpers.ajax(url, {
                form: 'report_issue_form',
                url_method: 'post',
                loading: {
                    indicator: 'report_issue_indicator',
                    callback: function () {
                        $('report_issue_submit_button').addClassName('disabled');
                    }
                },
                success: {
                    update: 'fullpage_backdrop_content'
                },
                complete: {
                    callback: function () {
                        $('report_issue_submit_button').removeClassName('disabled');
                    }
                }
            });
        };

        TBG.Main.Link.add = function (url, target_type, target_id) {
            TBG.Main.Helpers.ajax(url, {
                form: 'attach_link_' + target_type + '_' + target_id + '_form',
                loading: {
                    indicator: 'attach_link_' + target_type + '_' + target_id + '_indicator',
                    callback: function () {
                        $('attach_link_' + target_type + '_' + target_id + '_submit').disable();
                    }
                },
                success: {
                    reset: 'attach_link_' + target_type + '_' + target_id + '_form',
                    hide: ['attach_link_' + target_type + '_' + target_id, target_type + '_' + target_id + '_no_links'],
                    update: {element: target_type + '_' + target_id + '_links', insertion: true},
                    callback: function () {
                        if ($(target_type + '_' + target_id + '_container').hasClassName('menu_editing')) {
                            jQuery('#toggle_' + target_type + '_' + target_id +'_edit_mode').trigger('click');
                            jQuery('#toggle_' + target_type + '_' + target_id +'_edit_mode').trigger('click');
                        }
                    }
                },
                complete: {
                    callback: function () {
                        $('attach_link_' + target_type + '_' + target_id + '_submit').enable();
                    }
                }
            });
        };

        TBG.Main.Link.remove = function (url, target_type, target_id, link_id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    hide: target_type + '_' + target_id + '_links_' + link_id + '_remove_link',
                    indicator: 'dialog_indicator'
                },
                success: {
                    remove: [target_type + '_' + target_id + '_links_' + link_id, target_type + '_' + target_id + '_links_' + link_id + '_remove_confirm'],
                    callback: function (json) {
                        TBG.Main.Helpers.Dialog.dismiss();
                        if ($(json.target_type + '_' + json.target_id + '_links').childElements().size() == 0) {
                            $(json.target_type + '_' + json.target_id + '_no_links').show();
                        }
                    }
                },
                failure: {
                    show: target_type + '_' + target_id + '_links_' + link_id + '_remove_link'
                }
            });
        };

        TBG.Main.Menu.toggleEditMode = function (target_type, target_id, url) {
            if ($(target_type + '_' + target_id + '_container').hasClassName('menu_editing')) {
                Sortable.destroy(target_type + '_' + target_id + '_links');
            } else {
                Sortable.create(target_type + '_' + target_id + '_links', {constraint: '', onUpdate: function (container) {
                    TBG.Main.Menu.saveOrder(container, target_type, target_id, url);
                }});
            }
            $(target_type + '_' + target_id + '_container').toggleClassName('menu_editing');
        };

        TBG.Main.Menu.saveOrder = function (container, target_type, target_id, url) {
            TBG.Main.Helpers.ajax(url, {
                additional_params: Sortable.serialize(container),
                loading: {
                    indicator: target_type + '_' + target_id + '_indicator'
                }
            });
        };

        TBG.Main.detachFileFromArticle = function (url, file_id, article_id) {
            TBG.Core._detachFile(url, file_id, 'article_' + article_id + '_files_', 'dialog_indicator');
        };

        TBG.Main.toggleFavouriteArticle = function (url, article_id)
        {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'article_favourite_indicator_' + article_id,
                    hide: ['article_favourite_normal_' + article_id, 'article_favourite_faded_' + article_id]
                },
                success: {
                    callback: function (json) {
                        if ($('article_favourite_faded_' + article_id)) {
                            if (json.starred) {
                                $('article_favourite_faded_' + article_id).hide();
                                $('article_favourite_indicator_' + article_id).hide();
                                $('article_favourite_normal_' + article_id).show();
                            } else {
                                $('article_favourite_normal_' + article_id).hide();
                                $('article_favourite_indicator_' + article_id).hide();
                                $('article_favourite_faded_' + article_id).show();
                            }
                        } else if (json.subscriber != '') {
                            $('subscribers_list').insert(json.subscriber);
                        }
                    }
                }
            });
        };

        TBG.Main.deleteArticle = function (url) {
            TBG.Main.Helpers.ajax(url, {
                method: 'post',
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    callback: function () {
                        location.reload();
                    }
                }
            });
        };

        TBG.Main.reloadImage = function (id) {
            var src = $(id).src;
            var date = new Date();

            src = (src.indexOf('?') != -1) ? src.substr(0, pos) : src;
            $(id).src = src + '?v=' + date.getTime();

            return false;
        };

        TBG.Main.Profile.updateInformation = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'profile_information_form',
                loading: {indicator: 'profile_save_indicator'}
            });
        };

        TBG.Main.Profile.updateModuleSettings = function (url, module_name) {
            TBG.Main.Helpers.ajax(url, {
                form: 'profile_' + module_name + '_form',
                loading: {indicator: 'profile_' + module_name + '_save_indicator'}
            });
        };

        TBG.Main.Profile.updateSettings = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'profile_settings_form',
                loading: {indicator: 'profile_notificationsettings_save_indicator'},
                success: {callback: function () {
                    ($('profile_use_gravatar_yes').checked) ? $('gravatar_change').show() : $('gravatar_change').hide();
                }}
            });
        };

        TBG.Main.Profile.updateNotificationSettings = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'profile_notificationsettings_form',
                loading: {indicator: 'profile_notificationsettings_save_indicator'}
            });
        };

        TBG.Main.Profile.changePassword = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'change_password_form',
                loading: {indicator: 'change_password_indicator'},
                success: {reset: 'change_password_form', hide: 'change_password_div'}
            });
        };

        TBG.Main.Profile.addApplicationPassword = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'add_application_password_form',
                loading: {indicator: 'add_application_password_indicator'},
                success: {
                    hide: 'add_application_password_container',
                    update: {element: 'application_password_preview', from: 'password'},
                    show: 'add_application_password_response'
                }
            });
        };

        TBG.Main.Profile.removeApplicationPassword = function (url, p_id) {
            TBG.Main.Helpers.ajax(url, {
                method: 'post',
                loading: {
                    callback: function () {
                        $('application_password_' + p_id).down('button').disable();
                    }
                },
                success: {
                    remove: 'application_password_' + p_id,
                    callback: function () {
                        TBG.Main.Helpers.Dialog.dismiss();
                    }
                },
                failure: {
                    callback: function () {
                        $('application_password_' + p_id).down('button').enable();
                    }
                }
            });
        };

        TBG.Main.Profile.checkUsernameAvailability = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'check_username_form',
                loading: {
                    indicator: 'pick_username_indicator',
                    hide: 'username_unavailable'
                },
                complete: {
                    callback: function (json) {
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

        TBG.Main.Profile.toggleNotificationSettings = function (preset) {
            if (preset == 'custom') {
                $('notification_settings_selectors').show();
            } else {
                $('notification_settings_selectors').hide();
            }
        };

        TBG.Main.Profile.removeOpenIDIdentity = function (url, oid) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: 'dialog_indicator'},
                success: {
                    remove: 'openid_account_' + oid,
                    callback: function () {
                        if ($('openid_accounts_list').childElements().size() == 0)
                            $('no_openid_accounts').show();
                        if ($('openid_accounts_list').childElements().size() == 1 && $('pick_username_button'))
                            $('openid_accounts_list').down('.button').remove();
                        TBG.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        };

        TBG.Main.Profile.cancelScopeMembership = function (url, sid) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: 'dialog_indicator'},
                success: {
                    remove: 'account_scope_' + sid,
                    callback: function () {
                        if ($('pending_scope_memberships').childElements().size() == 0)
                            $('no_pending_scope_memberships').show();
                        TBG.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        };

        TBG.Main.Profile.confirmScopeMembership = function (url, sid) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: 'dialog_indicator'},
                success: {
                    callback: function () {
                        $('confirmed_scope_memberships').insert({'bottom': $('account_scope_' + sid).remove()});
                        $('account_scope_' + sid).down('.button-green').remove();
                        $('account_scope_' + sid).down('.button-red').show();
                        if ($('pending_scope_memberships').childElements().size() == 0)
                            $('no_pending_scope_memberships').show();
                        TBG.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        };

        TBG.Main.Profile.clearPopupsAndButtons = function (event) {
            $$('.popup_box').each(function (element) {
                var prev = $(element).previous('.button-pressed');
                if (prev) {
                    prev.removeClassName('button-pressed');
                } else if (element.id != '' && jQuery('.dropper[data-target='+element.id+']')) {
                    jQuery('.dropper[data-target='+element.id+']').removeClass('button-pressed');
                }
                $(element).hide();
            });
        };

        TBG.Main.Dashboard.View.init = function (view_id) {
            var dashboard_element = $('dashboard_container_' + view_id),
                dashboard_container = dashboard_element.up('.dashboard'),
                url = dashboard_container.dataset.url.replace('{view_id}', view_id);

            if (dashboard_element.dataset.preloaded == "0") {
                TBG.Main.Helpers.ajax(url, {
                    url_method: 'get',
                    loading: {indicator: 'dashboard_view_' + view_id + '_indicator'},
                    success: {update: 'dashboard_view_' + view_id},
                    complete: {
                        callback: function () {
                            TBG.Core._resizeWatcher();
                            TBG.Main.Dashboard.views.splice(0, 1);
                            if (TBG.Main.Dashboard.views.size() == 0) {
                                $$('html')[0].setStyle({'cursor': 'default'});
                            }
                        }
                    }
                });
            }
        };

        TBG.Main.Dashboard.sort = function (event) {
            var list = $(event.target);
            var url = list.up('.dashboard').dataset.sortUrl;
            var items = '&column=' + list.dataset.column;
            list.childElements().each(function (view) {
                if (view.dataset.viewId !== undefined) {
                    items += '&view_ids[]=' + view.dataset.viewId;
                }
            });
            TBG.Main.Helpers.ajax(url, {
                additional_params: items,
                loading: {indicator: list.down('.dashboard_indicator')}
            });
        };

        TBG.Main.Dashboard.initializeSorting = function ($) {
            $('.dashboard_column.jsortable').sortable({
                handle: '.dashboardhandle',
                connectWith: '.dashboard_column'
            }).bind('sortupdate', TBG.Main.Dashboard.sort);
        };

        TBG.Main.Dashboard.addView = function (element) {
            var dashboard_element = element.up('.dashboard_view');
            element.disable();
            var dashboard_views_container = dashboard_element.up('.available_views_container');
            var dashboard_container = $('dashboard_' + dashboard_views_container.dataset.dashboardId);
            var url = dashboard_container.dataset.postUrl;
            var column = dashboard_views_container.dataset.column;
            TBG.Main.Helpers.ajax(url, {
                url_method: 'post',
                params: 'mode=add_view&view_type=' + dashboard_element.dataset.viewType + '&view_subtype=' + dashboard_element.dataset.viewSubtype + '&column=' + column,
                loading: {
                    indicator: dashboard_element.down('.view_indicator'),
                },
                success: {
                    callback: function (json) {
                        var column_container = dashboard_container.down('.dashboard_column.column_' + column);
                        column_container.insert({bottom: json.view_content});
                        TBG.Main.Dashboard.views.push(json.view_id);
                        TBG.Main.Dashboard.View.init(json.view_id);
                        element.enable();
                        TBG.Main.Dashboard.initializeSorting(jQuery);
                    }
                }
            });
        };

        TBG.Main.Dashboard.removeView = function (event, element) {
            var view_id = element.up('.dashboard_view_container').dataset.viewId;
            var column = element.up('.dashboard_column');
            var dashboard_container = element.up('.dashboard');
            var url = dashboard_container.dataset.postUrl;
            TBG.Main.Helpers.ajax(url, {
                params: '&mode=remove_view&view_id=' + view_id,
                loading: {indicator: element.up('.dashboard_view_container').down('.dashboard_indicator')},
                success: {
                    remove: 'dashboard_container_' + view_id
                }
            });
        };

        TBG.Main.Dashboard.addViewPopup = function (event, element) {
            event.stopPropagation();
            var backdrop_url = element.up('.dashboard').dataset.addViewUrl;
            backdrop_url += '&column=' + element.up('.dashboard_column').dataset.column;
            TBG.Main.Helpers.Backdrop.show(backdrop_url);
        };

        TBG.Main.Dashboard.toggleMenu = function (link) {
            var section = $(link).dataset.section;
            $(link).up('ul').childElements().each(function (menu_elm) {
                menu_elm.removeClassName('selected');
            })
            $(link).up('li').addClassName('selected');
            $(link).up('.backdrop_detail_content').down('.available_views_container').childElements().each(function (view_list) {
                ($(view_list).dataset.section == section) ? $(view_list).show() : $(view_list).hide();
            });

        };

        TBG.Main.Dashboard.sidebar = function (url, id)
        {
            TBG.Main.setToggleState(url, !$(id).hasClassName('collapsed'));
            $(id).toggleClassName('collapsed');
            TBG.Core._resizeWatcher();
            TBG.Core._scrollWatcher();
        }

        TBG.Main.Profile.setState = function (url, ind) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: ind},
                success: {
                    callback: function (json) {
                        $$('.current_userstate').each(function (element) {
                            $(element).update(json.userstate);
                        });
                    }
                }
            });
        }

        TBG.Main.Profile.addFriend = function (url, user_id, rnd_no) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'toggle_friend_' + user_id + '_' + rnd_no + '_indicator',
                    hide: ['add_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
                },
                success: {
                    show: ['remove_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
                },
                failure: {
                    show: ['add_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
                }
            });
        }

        TBG.Main.Profile.removeFriend = function (url, user_id, rnd_no) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'toggle_friend_' + user_id + '_' + rnd_no + '_indicator',
                    hide: ['remove_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
                },
                success: {
                    show: ['add_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
                },
                failure: {
                    show: ['remove_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
                }
            });
        }

        TBG.Main.hideInfobox = function (url, boxkey) {
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

        TBG.Main.Comment.showPost = function () {
            $$('.comment_editor').each(Element.hide);
            $('comment_add_button').hide();
            $('comment_add').show();
            $('comment_bodybox').focus();
        }

        TBG.Main.Comment.remove = function (url, comment_id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'dialog_indicator'
                },
                success: {
                    remove: 'comment_' + comment_id,
                    callback: function () {
                        TBG.Main.Helpers.Dialog.dismiss();
                        if ($('comments_box').childElements().size() == 0) $('comments_none').show();
                    }
                }
            });
        };

        TBG.Main.Comment.update = function (url, cid) {
            TBG.Main.Helpers.ajax(url, {
                form: 'comment_edit_form_' + cid,
                loading: {
                    indicator: 'comment_edit_indicator_' + cid,
                    hide: 'comment_edit_controls_' + cid
                },
                success: {
                    hide: ['comment_edit_indicator_' + cid, 'comment_edit_' + cid],
                    show: ['comment_view_' + cid, 'comment_edit_controls_' + cid, 'comment_add_button'],
                    update: {element: 'comment_' + cid + '_content', from: 'comment_body'}
                },
                failure: {
                    show: ['comment_edit_controls_' + cid]
                }
            });
        };

        TBG.Main.Comment.add = function (url, commentcount_span) {
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
                    callback: function (json) {
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

        TBG.Main.Comment.reply = function (url, reply_comment_id) {
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
                    show: ['comment_reply_controls_' + reply_comment_id, 'comment_add_button'],
                    callback: function (json) {
                        $('comment_reply_visibility_' + reply_comment_id).setValue(1);
                    }
                },
                failure: {
                    show: 'comment_reply_controls_' + reply_comment_id
                }
            });
        };

        TBG.Main.Login.register = function (url)
        {
            TBG.Main.Helpers.ajax(url, {
                form: 'register_form',
                loading: {
                    indicator: 'register_indicator',
                    hide: 'register_button',
                    callback: function () {
                        $$('input.required').each(function (field) {
                            $(field).setStyle({backgroundColor: ''});
                        });
                    }
                },
                success: {
                    hide: 'register_form',
                    update: {element: 'register_message', from: 'loginmessage'},
                    callback: function (json) {
                        if (json.activated) {
                            $('register_username_hidden').setValue($('fieldusername').getValue());
                            $('register_password_hidden').setValue(json.one_time_password);
                            $('register_auto_form').show();
                        } else {
                            $('register_confirm_back').show();
                        }
                        $('register_confirmation').show();
                    }
                },
                failure: {
                    show: 'register_button',
                    callback: function (json) {
                        json.fields.each(function (field) {
                            $(field).setStyle({backgroundColor: '#FBB'});
                        });
                    }
                }
            });
        };

        TBG.Main.Login.checkUsernameAvailability = function (url)
        {
            TBG.Main.Helpers.ajax(url, {
                form: 'register_form',
                loading: {
                    indicator: 'username_check_indicator',
                    callback: function () {
                        $('register_button').disable();
                        $('username_check_indicator').show();
                    }
                },
                complete: {
                    callback: function (json) {
                        $('username_check_indicator').hide();
                        if (json.available) {
                            $('fieldusername').removeClassName('invalid');
                            $('fieldusername').addClassName('valid');
                            $('register_button').enable();
                        } else {
                            $('fieldusername').removeClassName('valid');
                            $('fieldusername').addClassName('invalid');
                        }
                    }
                }
            });
        };

        TBG.Main.Login.registerAutologin = function (url)
        {
            TBG.Main.Helpers.ajax(url, {
                form: 'register_auto_form',
                loading: {
                    indicator: 'register_autologin_indicator',
                    callback: function () {
                        $('register_autologin_button').disable();
                        $('register_autologin_indicator').show();
                    }
                },
                complete: {
                    callback: function () {
                        $('register_autologin_indicator').hide();
                        $('register_autologin_button').enable();
                    }
                }
            });
        };

        TBG.Main.Login.login = function (url)
        {
            TBG.Main.Helpers.ajax(url, {
                form: 'login_form',
                loading: {
                    indicator: 'login_indicator',
                    callback: function () {
                        $('login_button').disable();
                        $('login_indicator').show();
                    }
                },
                complete: {
                    callback: function () {
                        $('login_indicator').hide();
                        $('login_button').enable();
                    }
                }
            });
        };

        TBG.Main.Login.elevatedLogin = function (url)
        {
            TBG.Main.Helpers.ajax(url, {
                form: 'login_form',
                loading: {
                    indicator: 'elevated_login_indicator',
                    callback: function () {
                        $('login_button').disable();
                        $('elevated_login_indicator').show();
                    }
                },
                complete: {
                    callback: function (json) {
                        $('elevated_login_indicator').hide();
                        if (json.elevated) {
                            window.location.reload(true);
                        } else {
                            TBG.Main.Helpers.Message.error(json.error);
                            $('login_button').enable();
                        }
                    }
                }
            });
        };

        TBG.Main.Login.resetForgotPassword = function (url) {
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
                    show: 'forgot_password_button',
                    callback: function () {
                        $('regular_login_container').up().select('.logindiv').each(function (elm) {
                            elm.removeClassName('active');
                        });
                        $('regular_login_container').addClassName('active');
                    }
                }
            });
        };

        TBG.Main.Login.showLogin = function (section) {
            $('login_backdrop').select('.logindiv').each(function (elm) {
                elm.removeClassName('active');
            });
            $(section).addClassName('active');
            if (section != 'register' && $('registration-button-container')) {
                $('registration-button-container').addClassName('active');
            }
            $('login_backdrop').show();
            setTimeout(function () {
                if (section == 'register') {
                    $('fieldusername').focus();
                } else if (section == 'regular_login_container') {
                    $('tbg3_username').focus();
                }
            }, 250);
        };

        TBG.Main.Login.forgotToggle = function () {
            $('regular_login_container').up().select('.logindiv').each(function (elm) {
                elm.removeClassName('active');
            });
            $('forgot_password_container').addClassName('active');
        };

        TBG.Project.Statistics.get = function (url, section) {
            $('statistics_selector').childElements().each(function (elm) {
                elm.removeClassName('selected');
            });
            $('statistics_per_' + section + '_selector').addClassName('selected');
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    show: 'statistics_main',
                    hide: 'statistics_help',
                    callback: function () {
                        $('statistics_main_image').src = '';
                        for (var cc = 1; cc <= 3; cc++) {
                            $('statistics_mini_image_' + cc).src = '';
                        }
                    }
                },
                success: {
                    callback: function (json) {
                        $('statistics_main_image').src = json.images.main;
                        ecc = 1;
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
                                ecc++;
                            }
                        }
                        if (ecc == cc) {
                            $('statistics_main_image_div').next().hide();
                            $('statistics_main_image_div').next().next().hide();
                        }
                        else {
                            $('statistics_main_image_div').next().show();
                            $('statistics_main_image_div').next().next().show();
                        }
                    }
                },
                failure: {show: 'statistics_help'}
            });
        };

        TBG.Project.Statistics.toggleImage = function (image) {
            $('statistics_main_image').src = '';
            $('statistics_main_image').src = $('statistics_mini_' + image + '_main').getValue();
        };

        TBG.Project.Milestone.refresh = function (url, milestone_id) {
            var m_id = milestone_id;
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'milestone_' + milestone_id + '_indicator'
                },
                success: {
                    callback: function (json) {
                        var must_reload_issue_list = false;
                        if (json.percent) {
                            TBG.Main.updatePercentageLayout('milestone_' + m_id + '_percent', json.percent);
                            delete json.percent;
                        }
                        for (var item in json)
                        {
                            var existing = $('milestone_' + m_id + '_' + item);
                            if (existing)
                            {
                                if (existing.innerHTML != json[item])
                                {
                                    existing.update(json[item]);
                                    must_reload_issue_list = true;
                                }
                            }
                        }
                        if (must_reload_issue_list) {
                            $('milestone_' + m_id + '_changed').show();
                            $('milestone_' + m_id + '_issues').update('');
                        }

                    }
                }
            });
        };

        TBG.Project.Timeline.update = function (url) {
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
                    callback: function (json) {
                        $('timeline_offset').setValue(json.offset)
                    }
                }
            });
        };

        TBG.Project.Commits.update = function (url) {
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
                    callback: function (json) {
                        $('commits_offset').setValue(json.offset)
                    }
                }
            });
        };

        TBG.Project.Scrum.Sprint.add = function (url, assign_url)
        {
            TBG.Main.Helpers.ajax(url, {
                form: 'add_sprint_form',
                loading: {indicator: 'sprint_add_indicator'},
                success: {
                    reset: 'add_sprint_form',
                    hide: 'no_sprints',
                    update: {element: 'scrum_sprints', insertion: true}
                }
            });
        }

        TBG.Project.Scrum.Story.setColor = function (url, story_id, color, event)
        {
            event.stopPropagation();
            TBG.Main.Helpers.ajax(url, {
                params: {color: color},
                loading: {indicator: 'color_selector_' + story_id + '_indicator'},
                success: {
                    callback: function (json) {
                        $('story_color_' + story_id).style.backgroundColor = color;
                        $('story_color_' + story_id).style.color = json.text_color;
                        $$('.epic_badge').each(function (badge) {
                            if (badge.dataset.parentEpicId == story_id) {
                                badge.style.backgroundColor = color;
                                badge.style.color = json.text_color;
                            }
                        });
                    }
                },
                complete: {
                    callback: function () {
                        TBG.Main.Profile.clearPopupsAndButtons();
                    }
                }
            });
        }

        TBG.Project.updateLinks = function (json) {
            if ($('current_project_num_count'))
                $('current_project_num_count').update(json.total_count);
            (json.more_available) ? $('add_project_div').show() : $('add_project_div').hide();
        }

        TBG.Project.resetIcons = function (url) {
            TBG.Main.Helpers.ajax(url, {
                url_method: 'post',
                additional_params: '&clear_icons=1'
            });
        };

        TBG.Project.add = function (url) {
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

        TBG.Project.remove = function (url, pid) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop', 'project_delete_controls_' + pid]
                },
                success: {
                    remove: 'project_box_' + pid,
                    callback: function (json) {
                        if ($('project_table').childElements().size() == 0)
                            $('noprojects_tr').show();
                        if ($('project_table_archived').childElements().size() == 0)
                            $('noprojects_tr_archived').show();
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

        TBG.Project.archive = function (url, pid) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'project_' + pid + '_archive_indicator'
                },
                success: {
                    remove: 'project_box_' + pid,
                    hide: 'noprojects_tr_archived',
                    callback: function (json) {
                        if ($('project_table').childElements().size() == 0)
                            $('noprojects_tr').show();
                        $('project_table_archived').insert({top: json.box});
                        TBG.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        }

        TBG.Project.unarchive = function (url, pid) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'project_' + pid + '_archive_indicator'
                },
                success: {
                    remove: 'project_box_' + pid,
                    hide: 'noprojects_tr',
                    callback: function (json) {
                        if ($('project_table_archived').childElements().size() == 0)
                            $('noprojects_tr_archived').show();
                        if (json.parent_id != 0) {
                            $('project_' + json.parent_id + '_children').insert({bottom: json.box});
                        } else {
                            $('project_table').insert({bottom: json.box});
                        }
                    }
                },
                failure: {
                    show: 'project_' + pid + '_unarchive'
                }
            });
        };

        TBG.Project.Planning.initializeMilestoneDragDropSorting = function (milestone) {
            var milestone_issues = jQuery(milestone).find('.milestone_issues.jsortable');
            if (milestone_issues.hasClass('ui-sortable')) {
                milestone_issues.sortable('destroy');
            }
            milestone_issues.sortable({
                handle: '.draggable',
                connectWith: '.jsortable.intersortable',
                update: TBG.Project.Planning.sortMilestoneIssues,
                receive: TBG.Project.Planning.moveIssue,
                sort: TBG.Project.Planning.calculateNewBacklogMilestoneDetails,
                tolerance: 'pointer'
            });
        };

        TBG.Project.Planning.initializeReleaseDroptargets = function () {
            jQuery('#builds_list .release').not('ui-droppable').droppable({
                drop: TBG.Project.Planning.assignRelease,
                accept: '.milestone_issue',
                tolerance: 'pointer',
                hoverClass: 'drop-hover'
            });
        };

        TBG.Project.Planning.initializeEpicDroptargets = function () {
            jQuery('#epics_list .epic').not('.ui-droppable').droppable({
                drop: TBG.Project.Planning.assignEpic,
                accept: '.milestone_issue',
                tolerance: 'pointer',
                hoverClass: 'drop-hover'
            });
        };

        TBG.Project.Planning.toggleReleaseFilter = function (release) {
            if (release !== 'auto' && $('epics_list') && $('epics_list').hasClassName('filtered'))
                TBG.Project.Planning.toggleEpicFilter('auto');
            if ($('builds_list').hasClassName('filtered') && (release == 'auto' || ($(release) && $(release).hasClassName('selected')))) {
                $('builds_list').removeClassName('filtered');
                $('builds_list').childElements().each(function (rel) {
                    rel.removeClassName('selected');
                });
                $$('.milestone_issue').each(function (issue) {
                    issue.removeClassName('filtered');
                });
            } else if ($(release)) {
                $('builds_list').addClassName('filtered');
                $('builds_list').childElements().each(function (rel) {
                    rel.removeClassName('selected');
                });
                $(release).addClassName('selected');
                var release_id = $(release).dataset.releaseId;
                $$('.milestone_issue').each(function (issue) {
                    (issue.dataset['release-' + release_id] === undefined) ? issue.addClassName('filtered') : issue.removeClassName('filtered');
                });
            }

            TBG.Project.Planning.calculateAllMilestonesVisibilityDetails();
        };

        TBG.Project.Planning.toggleEpicFilter = function (epic) {
            if (epic !== 'auto' && $('builds_list') && $('builds_list').hasClassName('filtered'))
                TBG.Project.Planning.toggleReleaseFilter('auto');
            if ($('epics_list').hasClassName('filtered') && (epic == 'auto' || ($(epic) && $(epic).hasClassName('selected')))) {
                $('epics_list').removeClassName('filtered');
                $('epics_list').childElements().each(function (ep) {
                    ep.removeClassName('selected');
                });
                $$('.milestone_issue').each(function (issue) {
                    issue.removeClassName('filtered');
                });
            } else if ($(epic)) {
                $('epics_list').addClassName('filtered');
                $('epics_list').childElements().each(function (ep) {
                    ep.removeClassName('selected');
                });
                $(epic).addClassName('selected');
                var epic_id = $(epic).dataset.issueId;
                $$('.milestone_issue').each(function (issue) {
                    (issue.dataset['parent-' + epic_id] === undefined) ? issue.addClassName('filtered') : issue.removeClassName('filtered');
                });
            }

            TBG.Project.Planning.calculateAllMilestonesVisibilityDetails();
        };

        TBG.Project.Planning.toggleClosedIssues = function (button) {
            $('milestone_list').toggleClassName('show_closed');
            TBG.Project.Planning.calculateAllMilestonesVisibilityDetails();
            TBG.Project.Planning.calculateNewBacklogMilestoneDetails();
            TBG.Main.Profile.clearPopupsAndButtons();
        };

        TBG.Project.Planning.assignRelease = function (event, ui) {
            var issue = $(ui.draggable[0]);
            issue.dataset.sortCancel = true;
            if (issue.hasClassName('milestone_issue')) {
                var release = $(event.target);
                var release_id = $(event.target).dataset.releaseId;
                var url = release.dataset.assignIssueUrl;
                TBG.Main.Helpers.ajax(url, {
                    additional_params: 'issue_id=' + issue.dataset.issueId,
                    loading: {indicator: release.down('.planning_indicator')},
                    complete: {
                        callback: function (json) {
                            $('release_' + release_id + '_percentage_filler').setStyle({width: json.closed_pct + '%'});
                            TBG.Core.Pollers.Callbacks.planningPoller();
                            issue.dataset['release-' + release_id] = true;
                        }
                    }
                });
            }
        };

        TBG.Project.Planning.updateNewMilestoneIssues = function () {
            var num_issues = jQuery('.milestone_issue.included').size();
            $('milestone_include_num_issues').update(num_issues);
            $('milestone_include_issues').show();
            $('include_selected_issues').setValue(1);
        };

        TBG.Project.Planning.addEpic = function (form) {
            var url = form.action;
            TBG.Main.Helpers.ajax(url, {
                form: form,
                loading: {indicator: 'new_epic_indicator'},
                success: {
                    callback: function (json) {
                        TBG.Core.Pollers.Callbacks.planningPoller();
                        $(form).up('li').removeClassName('selected');
                    }
                }
            });
        };

        TBG.Project.Planning.assignEpic = function (event, ui) {
            var issue = $(ui.draggable[0]);
            issue.dataset.sortCancel = true;
            if (issue.hasClassName('milestone_issue')) {
                var epic = $(event.target);
                var epic_id = $(event.target).dataset.issueId;
                var url = epic.dataset.assignIssueUrl;
                TBG.Main.Helpers.ajax(url, {
                    additional_params: 'issue_id=' + issue.dataset.issueId,
                    loading: {indicator: epic.down('.planning_indicator')},
                    complete: {
                        callback: function (json) {
                            $('epic_' + epic_id + '_percentage_filler').setStyle({width: json.closed_pct + '%'});
                            $('epic_' + epic_id + '_estimate').update(json.estimate);
                            $('epic_' + epic_id + '_child_issues_count').update(json.num_child_issues);
                            issue.dataset['parent-' + epic_id] = true;
                            TBG.Core.Pollers.Callbacks.planningPoller();
                        }
                    }
                });
            }
        };

        TBG.Project.Planning.destroyMilestoneDropSorting = function (milestone) {
            if (milestone === undefined) {
                jQuery('.milestone_issues.ui-sortable').sortable('destroy');
            } else {
                jQuery(milestone).select('.milestone_issues.ui-sortable').sortable('destroy');
            }
        };

        TBG.Project.Planning.getMilestoneIssues = function (milestone) {
            if (!milestone.hasClassName('initialized')) {
                var milestone_id = milestone.dataset.milestoneId;
                TBG.Main.Helpers.ajax(milestone.dataset.issuesUrl, {
                    url_method: 'get',
                    success: {
                        update: 'milestone_' + milestone_id + '_issues',
                        callback: function (json) {
                            milestone.addClassName('initialized');

                            var ti_button = milestone.down('.toggle-issues');
                            if (ti_button) ti_button.enable();
                            if (TBG.Project.Planning.options.dragdrop) {
                                TBG.Project.Planning.initializeMilestoneDragDropSorting(milestone);
                            }

                            if (milestone.hasClassName('backlog_milestone')) {
                                $('project_planning').removeClassName('left_toggled');
                            }

                            if (milestone.hasClassName('available')) {
                                var completed_milestones = $$('.milestone_box.available.initialized');
                                var multiplier = 100 / TBG.Project.Planning.options.milestone_count;
                                var pct = Math.floor(completed_milestones.size() * multiplier);
                                $('planning_percentage_filler').setStyle({width: pct + '%'});

                                if (completed_milestones.size() == (TBG.Project.Planning.options.milestone_count - 1)) {
                                    $('planning_loading_progress_indicator').hide();
                                    if (!TBG.Core.Pollers.planningpoller)
                                        TBG.Core.Pollers.planningpoller = new PeriodicalExecuter(TBG.Core.Pollers.Callbacks.planningPoller, 6);

                                    TBG.Project.Planning.calculateAllMilestonesVisibilityDetails();

                                    $('planning_indicator').hide();
                                    $('planning_filter_title_input').enable();
                                }
                            }

                            if (! milestone.down('.planning_indicator').hidden) milestone.down('.planning_indicator').hide();
                        }
                    },
                    failure: {
                        callback: function () {
                            milestone.addClassName('initialized');
                            milestone.down('.milestone_error_issues').show();
                        }
                    }
                });
            }
        };

        TBG.Project.Planning.Whiteboard.addColumn = function(button) {
            TBG.Main.Helpers.ajax(button.dataset.url, {
                loading: {
                    indicator: 'planning_indicator'
                },
                url_method: 'get',
                success: {
                    callback: function(json) {
                        $('planning_whiteboard_columns_form_row').insert({bottom: json.component});
                        if (json.status_element_id != undefined) {
                            TBG.Main.Helpers.initializeFancyFilters($(json.status_element_id));
                            TBG.Main.Helpers.recalculateFancyFilters($(json.status_element_id));
                        }
                        else {
                            TBG.Main.Helpers.initializeFancyFilters();
                            TBG.Main.Helpers.recalculateFancyFilters();
                        }
                        TBG.Project.Planning.Whiteboard.setSortOrder();
                    }
                }
            });
        };

        TBG.Project.Planning.Whiteboard.toggleEditMode = function() {
            $('project_planning').toggleClassName('edit-mode');
            TBG.Main.Profile.clearPopupsAndButtons();
        };

        TBG.Project.Planning.Whiteboard.saveColumns = function(form) {
            $('planning_indicator').show();
            TBG.Main.Helpers.ajax(form.action, {
                url_method: 'post',
                form: form,
                failure: {
                    hide: 'planning_indicator'
                }
            });
        };

        TBG.Project.Planning.Whiteboard.calculateColumnCounts = function() {
            $$('#whiteboard-headers .td').each(function (column, index) {
                var counts = 0;
                var status_counts = [];
                column.select('.status_badge').each(function (status) {
                    status_counts[parseInt(status.dataset.statusId)] = 0;
                });
                $$('#whiteboard .tbody .tr').each(function (row) {
                    row.childElements().each(function (subcolumn, subindex) {
                        if (subindex == index) {
                            var issues = subcolumn.select('.whiteboard-issue');
                            issues.each(function (issue) {
                                status_counts[parseInt(issue.dataset.statusId)]++;
                            });
                            counts += issues.size();
                        }
                    });
                });
                if (column.down('.column_count.primary')) column.down('.column_count.primary').update(counts);
                if (column.down('.column_count .count')) column.down('.column_count .count').update(counts);
                column.select('.status_badge').each(function (status) {
                    status.update(status_counts[parseInt(status.dataset.statusId)]);
                });
                if ($('project_planning').hasClassName('type-kanban')) {
                    var min_wi = parseInt(column.dataset.minWorkitems);
                    var max_wi = parseInt(column.dataset.maxWorkitems);
                    if (min_wi !== 0 && counts < min_wi) {
                        column.down('.under_count').update(counts);
                        column.removeClassName('over-workitems');
                        column.addClassName('under-workitems');
                        $$('#whiteboard .tbody .tr').each(function (row) {
                            row.childElements().each(function (subcolumn, subindex) {
                                if (!subcolumn.hasClassName('swimlane-header') && subindex == index) {
                                    subcolumn.removeClassName('over-workitems');
                                    subcolumn.addClassName('under-workitems');
                                }
                            });
                        });
                    }
                    if (max_wi !== 0 && counts > max_wi) {
                        column.down('.over_count').update(counts);
                        column.removeClassName('under-workitems');
                        column.addClassName('over-workitems');
                        $$('#whiteboard .tbody .tr').each(function (row) {
                            row.childElements().each(function (subcolumn, subindex) {
                                if (!subcolumn.hasClassName('swimlane-header') && subindex == index) {
                                    subcolumn.removeClassName('under-workitems');
                                    subcolumn.addClassName('over-workitems');
                                }
                            });
                        });
                    }
                }
            });
        }

        TBG.Project.Planning.Whiteboard.calculateSwimlaneCounts = function() {
            $$('#whiteboard .tbody').each(function (swimlane) {
                swimlane_rows = swimlane.select('.tr');

                if (swimlane_rows.size() != 2) return;

                swimlane_rows[0].down('.swimlane_count').update(swimlane_rows[1].select('.whiteboard-issue').size());
            });
        }

        TBG.Project.Planning.Whiteboard.retrieveWhiteboard = function() {
            var wb = $('whiteboard');
            wb.removeClassName('initialized');
            var mi = $('selected_milestone_input');
            var milestone_id = parseInt(mi.dataset.selectedValue);

            TBG.Main.Helpers.ajax(wb.dataset.whiteboardUrl, {
                additional_params: '&milestone_id=' + milestone_id,
                url_method: 'get',
                loading: {
                    indicator: 'whiteboard_indicator',
                    callback: function() {
                        $('whiteboard').select('.thead .column_count.primary').each(function (cc) {
                            cc.update('-');
                        });
                        wb.dataset.milestoneId = milestone_id;
                    }
                },
                success: {
                    callback: function(json) {
                        if (json.swimlanes) {
                            wb.removeClassName('no-swimlanes');
                            wb.addClassName('swimlanes');
                        }
                        else {
                            wb.removeClassName('swimlanes');
                            wb.addClassName('no-swimlanes');
                        }
                        wb.addClassName('initialized');
                        wb.select('.tbody').each(Element.remove);
                        $('whiteboard-headers').insert({after: json.component});
                        setTimeout(function () {
                            TBG.Project.Planning.Whiteboard.calculateColumnCounts();
                            TBG.Project.Planning.Whiteboard.calculateSwimlaneCounts();
                            TBG.Project.Planning.Whiteboard.initializeDragDrop();
                        }, 250);
                    }
                }
            });
        };

        TBG.Project.Planning.Whiteboard.retrieveMilestoneStatus = function(event, item) {
            var mi = $('selected_milestone_input');
            var milestone_id = (event) ? $(item).dataset.inputValue : mi.dataset.selectedValue;
            TBG.Main.Helpers.ajax(mi.dataset.statusUrl, {
                additional_params: '&milestone_id=' + parseInt(milestone_id),
                url_method: 'get',
                loading: {
                    hide: 'selected_milestone_status_details',
                    indicator: 'selected_milestone_status_indicator'
                },
                success: {
                    update: 'selected_milestone_status_details',
                    show: 'selected_milestone_status_details',
                    callback: function () {
                        $('reportissue_button').dataset.milestoneId = milestone_id;
                    }
                }
            });
        };

        TBG.Project.Planning.Whiteboard.setSortOrder = function() {
            $('planning_whiteboard_columns_form_row').childElements().each(function(column, index) {
                column.down('input.sortorder').setValue(index + 1);
            });
        };

        TBG.Project.Planning.Whiteboard.setViewMode = function(button, mode) {
            $(button).up('.button-group').childElements().each(function (elm) {
                elm.removeClassName('button-pressed');
            });
            $(button).addClassName('button-pressed');
            var wb = $('whiteboard');
            ['simple', 'detailed'].each(function (viewmode) {
                wb.removeClassName('viewmode-'+viewmode);
            });
            wb.addClassName('viewmode-'+mode);
        };

        TBG.Project.Planning.Whiteboard.updateIssueColumn = function(event, ui) {
            var issue = jQuery(ui.draggable);
            var column = jQuery(event.target);

            TBG.Project.Planning.Whiteboard.moveIssueColumn(issue, column)
        };

        TBG.Project.Planning.Whiteboard.moveIssueColumn = function(issue, column, transition_id, original_column, issue_index) {
            if (transition_id == undefined & issue.data('column-id') == column.data('column-id')) {
                issue.css({left: '0', top: '0'});
                return;
            }

            if (! original_column) var original_column = issue.parents('.column');
            if (! issue_index) var issue_index = issue.index();

            if (issue) {
                issue.detach().css({left: '0', top: '0'}).prependTo(column);
            }

            var wb = jQuery('#whiteboard');
            var parameters = '&issue_id=' + parseInt(issue.data('issue-id')) + '&column_id=' + parseInt(column.data('column-id')) + '&milestone_id=' + parseInt(jQuery('#selected_milestone_input').data('selected-value')) + '&swimlane_identifier=' + issue.parents('.tbody').data('swimlane-identifier');
            var revertIssuePosition = function () {
                issue.css({left: '0', top: '0'});

                if (issue_index <= 0) {
                    issue.prependTo(original_column);
                }
                else {
                    issue.insertAfter(original_column.children().eq(issue_index - 1));
                }
            };
            var customEscapeWatcher = function (event) {
                if (event.keyCode != undefined && event.keyCode != 0 && Event.KEY_ESC != event.keyCode) return;
                TBG.Main.Helpers.Backdrop.reset(revertIssuePosition);
                $('workflow_transition_fullpage').hide();
                setTimeout(function() {
                    document.stopObserving('keydown', customEscapeWatcher);
                    document.observe('keydown', TBG.Core._escapeWatcher);
                }, 350);
            };

            if (transition_id) parameters += '&transition_id=' + transition_id;

            TBG.Main.Helpers.ajax(wb.data('whiteboard-url'), {
                additional_params: parameters,
                url_method: 'post',
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    callback: function(json) {
                        if (json.transition_id && json.component) {
                            document.stopObserving('keydown', TBG.Core._escapeWatcher);
                            document.observe('keydown', customEscapeWatcher);
                            $('moving_issue_workflow_transition').update(json.component);
                            TBG.Issues.showWorkflowTransition(json.transition_id);
                            $('transition_working_' + json.transition_id + '_cancel').observe('click', function (event) {
                                Event.stop(event);
                                customEscapeWatcher(event);
                            });
                            $('transition_working_' + json.transition_id + '_submit').observe('click', function (event) {
                                Event.stop(event);
                                TBG.Issues.submitWorkflowTransition($('workflow_transition_' + json.transition_id + '_form'), function () {
                                    issue.draggable('destroy');
                                    TBG.Core.Pollers.Callbacks.whiteboardPlanningPoller();
                                });
                            });
                        } else if (json.component) {
                            document.stopObserving('keydown', TBG.Core._escapeWatcher);
                            document.observe('keydown', customEscapeWatcher);
                            $('fullpage_backdrop').appear({duration: 0.2});
                            $('fullpage_backdrop_content').update(json.component);
                            $('fullpage_backdrop_content').appear({duration: 0.2});
                            $('fullpage_backdrop_indicator').fade({duration: 0.2});
                            $('transition-selector-close-link').observe('click', customEscapeWatcher);
                            $$('.transition-selector-button').each(function (elem) {
                                elem.observe('click', function (event) {
                                    TBG.Project.Planning.Whiteboard.moveIssueColumn(jQuery('#whiteboard_issue_' + elem.dataset.issueId), jQuery('#swimlane_' + elem.dataset.swimlaneIdentifier + '_column_' + elem.dataset.columnId), elem.dataset.transitionId, original_column, issue_index);
                                });
                            });
                        } else {
                            $('fullpage_backdrop_content').update('');
                            $('fullpage_backdrop').fade({duration: 0.2});
                            if (!issue) {
                                jQuery(json.issue).prependTo(column);
                            }
                            else {
                                issue.draggable('destroy');
                            }
                            TBG.Core.Pollers.Callbacks.whiteboardPlanningPoller();
                        }
                    }
                },
                failure: {
                    show: issue,
                    callback: function(json) {
                        if (json.error != undefined && typeof(json.error) == 'string' && json.error.length) {
                            revertIssuePosition();
                        }
                    }
                }
            });

        };

        TBG.Project.Planning.Whiteboard.resetAvailableDropColumns = function(event, ui) {
            jQuery('.column.ui-droppable-disabled').each(function (index) {
                jQuery(this).droppable("enable");
            });
            jQuery('.column.drop-valid').each(function (index) {
                jQuery(this).removeClass('drop-valid');
            });
        };

        TBG.Project.Planning.Whiteboard.detectAvailableDropColumns = function(event, ui) {
            var issue = $(event.target);
            var issue_statuses = issue.dataset.validStatusIds.split(',');
            issue.up('.tr').childElements().each(function (column) {
                var column_statuses = column.dataset.statusIds.split(',');
                var has_status = false;
                issue_statuses.each(function (status) {
                    if (column_statuses.indexOf(status) != -1) {
                        has_status = true;
                    }
                });

                if (!has_status) {
                    jQuery(column).droppable("disable");
                } else {
                    column.addClassName('drop-valid');
                }
            });
        };

        TBG.Project.Planning.Whiteboard.initializeDragDrop = function () {
            $('whiteboard').select('.tbody .td.column').each(function (column) {
                var swimlane_identifier = column.up('.tbody').dataset.swimlaneIdentifier;
                jQuery(column).not('ui-droppable').droppable({
                    drop: TBG.Project.Planning.Whiteboard.updateIssueColumn,
                    scope: swimlane_identifier,
                    accept: '.whiteboard-issue',
                    tolerance: 'intersect',
                    hoverClass: 'drop-hover'
                });
                jQuery(column).find('.whiteboard-issue').not('ui-draggable').draggable({
                    scope: swimlane_identifier,
                    start: TBG.Project.Planning.Whiteboard.detectAvailableDropColumns,
                    stop: TBG.Project.Planning.Whiteboard.resetAvailableDropColumns,
                    axis: 'x',
                    revert: 'invalid',
                    containment: '#whiteboard'
                });
            });

            if (!TBG.Core.Pollers.planningpoller)
                TBG.Core.Pollers.planningpoller = new PeriodicalExecuter(TBG.Core.Pollers.Callbacks.whiteboardPlanningPoller, 6);
        };

        TBG.Project.Planning.Whiteboard.retrieveIssue = function (issue_id, url, existing_element) {
            var milestone_id = $('whiteboard').dataset.milestoneId;
            var swimlane_type = $('whiteboard').dataset.swimlaneType;
            var column_id = ($(existing_element) != null && $(existing_element).dataset.columnId != undefined) ? $(existing_element).dataset.columnId : '';

            if ($(existing_element) != null) {
                if ($(existing_element).hasClassName('tbody')) {
                    var swimlane_identifier = $(existing_element).dataset.swimlaneIdentifier;
                }
                else {
                    var swimlane_identifier = $(existing_element).up('.tbody').dataset.swimlaneIdentifier;
                }
            }
            else {
                var swimlane_identifier = $('whiteboard').down('.tbody').dataset.swimlaneIdentifier;
            }

            TBG.Main.Helpers.ajax(url, {
                params: 'issue_id=' + issue_id + '&milestone_id=' + milestone_id + '&swimlane_type=' + swimlane_type + '&column_id=' + column_id + '&swimlane_identifier=' + swimlane_identifier,
                url_method: 'get',
                loading: {indicator: (!$(existing_element)) ? 'retrieve_indicator' : 'issue_' + issue_id + '_indicator'},
                success: {
                    callback: function (json) {
                        if (swimlane_type != json.swimlane_type) {
                            TBG.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                            TBG.Project.Planning.Whiteboard.retrieveWhiteboard();
                            return;
                        }
                        if (!$(existing_element)) {
                            if (json.issue_details.milestone && json.issue_details.milestone.id == milestone_id && json.component != '') {
                                if ($('whiteboard').hasClassName('initialized')) {
                                    if ($('swimlane_'+json.swimlane_identifier+'_column_'+json.column_id)) {
                                        $('swimlane_'+json.swimlane_identifier+'_column_'+json.column_id).insert({top: json.component});
                                    } else {
                                        if (json.child_issue == '0') {
                                            $('whiteboard-headers').insert({after: json.component});
                                        }
                                    }
                                    TBG.Project.Planning.Whiteboard.initializeDragDrop();
                                    TBG.Project.Planning.Whiteboard.calculateColumnCounts();
                                    TBG.Project.Planning.Whiteboard.calculateSwimlaneCounts();
                                    TBG.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                                }
                            }
                        } else {
                            var json_milestone_id = (json.issue_details.milestone && json.issue_details.milestone.id != undefined) ? parseInt(json.issue_details.milestone.id) : 0;
                            if (json_milestone_id == 0 || json.component == '') {
                                $(existing_element).remove();
                                TBG.Project.Planning.Whiteboard.calculateColumnCounts();
                                TBG.Project.Planning.Whiteboard.calculateSwimlaneCounts();
                                TBG.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                            } else if (json_milestone_id != milestone_id || json.swimlane_identifier != swimlane_identifier || json.column_id != column_id) {
                                $(existing_element).remove();
                                if ($('whiteboard').hasClassName('initialized')) {
                                    if ($('swimlane_'+json.swimlane_identifier+'_column_'+json.column_id)) {
                                        $('swimlane_'+json.swimlane_identifier+'_column_'+json.column_id).insert({top: json.component});
                                    } else {
                                        if (json.child_issue == '0') {
                                            $('whiteboard-headers').insert({after: json.component});
                                        }
                                    }
                                    TBG.Project.Planning.Whiteboard.initializeDragDrop();
                                }
                                TBG.Project.Planning.Whiteboard.calculateColumnCounts();
                                TBG.Project.Planning.Whiteboard.calculateSwimlaneCounts();
                                TBG.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                            } else {
                                $(existing_element).replace(json.component);
                                TBG.Project.Planning.Whiteboard.initializeDragDrop();
                                TBG.Project.Planning.Whiteboard.calculateColumnCounts();
                                TBG.Project.Planning.Whiteboard.calculateSwimlaneCounts();
                                TBG.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                            }
                        }
                    }
                }
            });
        };

        TBG.Core.Pollers.Callbacks.whiteboardPlanningPoller = function () {
            if (!TBG.Core.Pollers.Locks.planningpoller && $('whiteboard').hasClassName('initialized')) {
                TBG.Core.Pollers.Locks.planningpoller = true;
                var pc = $('project_planning');
                var wb = $('whiteboard');
                var data_url = pc.dataset.pollUrl;
                var retrieve_url = pc.dataset.retrieveIssueUrl;
                var last_refreshed = pc.dataset.lastRefreshed;
                TBG.Main.Helpers.ajax(data_url, {
                    url_method: 'get',
                    params: 'last_refreshed=' + last_refreshed + '&milestone_id=' + wb.dataset.milestoneId,
                    success: {
                        callback: function (json) {
                            if (parseInt(json.milestone_id) == parseInt(wb.dataset.milestoneId)) {
                                for (var i in json.ids) {
                                    if (json.ids.hasOwnProperty(i)) {
                                        var issue_details = json.ids[i];
                                        var issue_element = $('whiteboard_issue_' + issue_details.issue_id);
                                        if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
                                            TBG.Project.Planning.Whiteboard.retrieveIssue(issue_details.issue_id, retrieve_url, 'whiteboard_issue_' + issue_details.issue_id);
                                        }
                                    }
                                }
                                for (var i in json.backlog_ids) {
                                    if (json.backlog_ids.hasOwnProperty(i)) {
                                        var issue_details = json.backlog_ids[i];
                                        var issue_element = $('whiteboard_issue_' + issue_details.issue_id);
                                        if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
                                            TBG.Project.Planning.Whiteboard.retrieveIssue(issue_details.issue_id, retrieve_url, 'whiteboard_issue_' + issue_details.issue_id);
                                        }
                                    }
                                }
                            }

                            pc.dataset.lastRefreshed = get_current_timestamp();
                            TBG.Core.Pollers.Locks.planningpoller = false;
                        }
                    }
                });
            }
        };

        TBG.Project.Planning.Whiteboard.checkNav = function() {
            if (window.location.hash) {
                if (parseInt($('selected_milestone_input').dataset.selectedValue) != parseInt(window.location.hash)) {
                    var hasharray = window.location.hash.substr(1).split('/');
                    var milestone_id = parseInt(hasharray[0]);
                    $('selected_milestone_input').childElements().each(function(milestone_li) {
                        if (parseInt(milestone_li.dataset.inputValue) == milestone_id) {
                            TBG.Main.setFancyDropdownValue(milestone_li);
                            setTimeout(function () {
                                TBG.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                                TBG.Project.Planning.Whiteboard.retrieveWhiteboard();
                            }, 150);
                        }
                    });
                }
            }
        }

        TBG.Project.Planning.Whiteboard.initialize = function (options) {
            $('body').on('click', '#selected_milestone_input li', TBG.Project.Planning.Whiteboard.retrieveMilestoneStatus);
            Event.observe(window, 'hashchange', TBG.Project.Planning.Whiteboard.checkNav);
            TBG.Project.Planning._initializeFilterSearch(true);
            if (window.location.hash) {
                TBG.Project.Planning.Whiteboard.checkNav();
            } else {
                TBG.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                TBG.Project.Planning.Whiteboard.retrieveWhiteboard();
            }
            TBG.Main.Helpers.initializeFancyFilters();

            jQuery('#planning_whiteboard_columns_form_row').sortable({
                handle: '.draggable',
                tolerance: 'intersect',
                update: TBG.Project.Planning.Whiteboard.setSortOrder
            });

            $('planning_indicator').hide();
            $('planning_filter_title_input').enable();
        };

        TBG.Project.Planning._initializeFilterSearch = function(whiteboard) {
            TBG.ift_observers = {};
            var pfti = $('planning_filter_title_input');
            pfti.dataset.previousValue = '';
            var fk = 'pfti';
            if (whiteboard == undefined) whiteboard = false;
            pfti.on('keyup', function (event, element) {
                if (TBG.ift_observers[fk])
                    clearTimeout(TBG.ift_observers[fk]);
                if ((pfti.getValue().length >= 3 || pfti.getValue().length == 0) && pfti.getValue() != pfti.dataset.lastValue) {
                    TBG.ift_observers[fk] = setTimeout(function () {
                        TBG.Project.Planning.filterTitles(pfti.getValue(), whiteboard);
                        pfti.dataset.lastValue = pfti.getValue();
                    }, 500);
                }
            });
        };

        TBG.Project.Planning.toggleMilestoneIssues = function(milestone_id) {
            var mi_issues = $('milestone_'+milestone_id+'_issues');
            var mi = $('milestone_'+milestone_id);
            mi.down('.toggle-issues').toggleClassName('button-pressed');
            if (!mi.hasClassName('initialized')) {
                mi.down('.toggle-issues').disable();
                mi_issues.removeClassName('collapsed');
                TBG.Project.Planning.getMilestoneIssues(mi);
            } else {
                $('milestone_'+milestone_id+'_issues').toggleClassName('collapsed');
            }
        };

        TBG.Project.Planning.toggleMilestoneSorting = function() {
            if ($('project_planning').hasClassName('milestone-sort')) {
                $('project_planning').removeClassName('milestone-sort left_toggled');
                jQuery('#milestone_list').sortable("destroy");
                jQuery('.milestone_issues.ui-sortable').sortable('enable');
            } else {
                $('project_planning').addClassName('milestone-sort left_toggled');

                jQuery('.milestone_issues.ui-sortable').sortable('disable');

                jQuery('#milestone_list').sortable({
                    update: TBG.Project.Planning.sortMilestones,
                    axis: 'y',
                    items: '> .milestone_box',
                    helper: 'original',
                    tolerance: 'intersect'
                });
            }
        };

        TBG.Project.Planning.initialize = function (options) {
            TBG.Project.Planning.options = options;

            $$('.milestone_box.unavailable').each(TBG.Project.Planning.initializeMilestoneDragDropSorting);
            var milestone_boxes = $$('.milestone_box.available');
            TBG.Project.Planning.options.milestone_count = milestone_boxes.size() + 1;
            milestone_boxes.each(TBG.Project.Planning.getMilestoneIssues);

            TBG.Project.Planning._initializeFilterSearch();

            if ($('epics_list')) {
                TBG.Main.Helpers.ajax($('epics_list').dataset.epicsUrl, {
                    url_method: 'get',
                    success: {
                        update: 'epics_list',
                        callback: function (json) {
                            var completed_milestones = $$('.milestone_box.available.initialized');
                            var multiplier = 100 / TBG.Project.Planning.options.milestone_count;
                            var pct = Math.floor((completed_milestones.size() + 1) * multiplier);
                            $('planning_percentage_filler').setStyle({width: pct + '%'});

                            $('epics_toggler_button').enable();
                            TBG.Project.Planning.initializeEpicDroptargets();
                            jQuery('body').on('click', '.epic', function (e) {
                                TBG.Project.Planning.toggleEpicFilter(this);
                            });
                        }
                    }
                });
            }

            if ($('builds_list')) {
                TBG.Main.Helpers.ajax($('builds_list').dataset.releasesUrl, {
                    url_method: 'get',
                    success: {
                        update: 'builds_list',
                        callback: function (json) {
                            TBG.Project.Planning.initializeReleaseDroptargets();
                            jQuery('body').on('click', '.release', function (e) {
                                TBG.Project.Planning.toggleReleaseFilter(this);
                            });
                        }
                    }
                });
            }
        };

        TBG.Project.Planning.filterTitles = function (title, whiteboard) {
            $('planning_indicator').show();
            if (title !== '') {
                var matching = new RegExp(title, "i");
                $('project_planning').addClassName('issue_title_filtered');
                $$(whiteboard ? '.whiteboard-issue' : '.milestone_issue').each(function (issue) {
                    if (whiteboard) {
                        if (issue.down('.issue_header').innerHTML.search(matching) !== -1) {
                            issue.addClassName('title_unfiltered');
                        } else {
                            issue.removeClassName('title_unfiltered');
                        }
                    }
                    else {
                        if (issue.down('.issue_link').down('a').innerHTML.search(matching) !== -1) {
                            issue.addClassName('title_unfiltered');
                        } else {
                            issue.removeClassName('title_unfiltered');
                        }
                    }
                });
            } else {
                $('project_planning').removeClassName('issue_title_filtered');
                $$(whiteboard ? '.whiteboard-issue' : '.milestone_issue').each(function (issue) {
                    issue.removeClassName('title_unfiltered');
                });
            }
            $('planning_indicator').hide();
        };

        TBG.Project.Planning.insertIntoMilestone = function (milestone_id, content, recalculate) {
            var milestone_list = $('milestone_' + milestone_id + '_issues');
            milestone_list.removeClassName('empty');
            $('milestone_' + milestone_id + '_unassigned').hide();
            if (milestone_id == 0) {
                milestone_list.insert({bottom: content});
            } else {
                milestone_list.insert({top: content});
                setTimeout(TBG.Project.Planning.sortMilestoneIssues({target: 'milestone_' + milestone_id + '_issues'}), 250);
            }
            if (recalculate == 'all') {
                TBG.Project.Planning.calculateAllMilestonesVisibilityDetails();
            } else {
                TBG.Project.Planning.calculateMilestoneIssueVisibilityDetails(milestone_list);
            }
            TBG.Project.Planning.calculateNewBacklogMilestoneDetails();
        };

        TBG.Project.Planning.retrieveIssue = function (issue_id, url, existing_element) {
            TBG.Main.Helpers.ajax(url, {
                params: 'issue_id=' + issue_id,
                url_method: 'get',
                loading: {indicator: (!$(existing_element)) ? 'retrieve_indicator' : 'issue_' + issue_id + '_indicator'},
                success: {
                    callback: function (json) {
                        if (json.epic) {
                            if (!$(existing_element)) {
                                $('add_epic_container').insert({before: json.component});
                                setTimeout(TBG.Project.Planning.initializeEpicDroptargets, 250);
                            } else {
                                $(existing_element).up('.milestone_issue').replace(json.component);
                            }
                        } else {
                            if (!$(existing_element)) {
                                if (json.issue_details.milestone && json.issue_details.milestone.id) {
                                    if ($('milestone_'+json.issue_details.milestone.id).hasClassName('initialized')) {
                                        TBG.Project.Planning.insertIntoMilestone(json.issue_details.milestone.id, json.component);
                                    }
                                } else {
                                    TBG.Project.Planning.insertIntoMilestone(0, json.component);
                                }
                            } else {
                                var json_milestone_id = (json.issue_details.milestone && json.issue_details.milestone.id != undefined) ? parseInt(json.issue_details.milestone.id) : 0;
                                if (parseInt($(existing_element).up('.milestone_box').dataset.milestoneId) == json_milestone_id) {
                                    $(existing_element).up('.milestone_issue').replace(json.component);
                                    TBG.Project.Planning.calculateMilestoneIssueVisibilityDetails($('milestone_' + json_milestone_id + '_issues'));
                                    TBG.Project.Planning.calculateNewBacklogMilestoneDetails();
                                } else {
                                    $(existing_element).up('.milestone_issue').remove();
                                    TBG.Project.Planning.insertIntoMilestone(json_milestone_id, json.component, 'all');
                                }
                            }
                        }
                    }
                }
            });
        };

        TBG.Core.Pollers.Callbacks.planningPoller = function () {
            var pc = $('project_planning');
            if (!TBG.Core.Pollers.Locks.planningpoller && pc) {
                TBG.Core.Pollers.Locks.planningpoller = true;
                var data_url = pc.dataset.pollUrl;
                var retrieve_url = pc.dataset.retrieveIssueUrl;
                var last_refreshed = pc.dataset.lastRefreshed;
                TBG.Main.Helpers.ajax(data_url, {
                    url_method: 'get',
                    params: 'last_refreshed=' + last_refreshed,
                    success: {
                        callback: function (json) {
                            pc.dataset.lastRefreshed = get_current_timestamp();
                            for (var i in json.ids) {
                                if (json.ids.hasOwnProperty(i)) {
                                    var issue_details = json.ids[i];
                                    var issue_element = $('issue_' + issue_details.issue_id);
                                    if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
                                        TBG.Project.Planning.retrieveIssue(issue_details.issue_id, retrieve_url, 'issue_' + issue_details.issue_id);
                                    }
                                }
                            }
                            for (var i in json.backlog_ids) {
                                if (json.backlog_ids.hasOwnProperty(i)) {
                                    var issue_details = json.backlog_ids[i];
                                    var issue_element = $('issue_' + issue_details.issue_id);
                                    if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
                                        TBG.Project.Planning.retrieveIssue(issue_details.issue_id, retrieve_url, 'issue_' + issue_details.issue_id);
                                    }
                                }
                            }
                            for (var i in json.epic_ids) {
                                if (json.epic_ids.hasOwnProperty(i)) {
                                    var issue_details = json.epic_ids[i];
                                    var issue_element = $('epic_' + issue_details.issue_id);
                                    if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
                                        TBG.Project.Planning.retrieveIssue(issue_details.issue_id, retrieve_url, 'epic_' + issue_details.issue_id);
                                    }
                                }
                            }
                            TBG.Core.Pollers.Locks.planningpoller = false;
                        }
                    }
                });
            }
        };

        TBG.Project.Planning.calculateMilestoneIssueVisibilityDetails = function (list) {
            var list_issues = jQuery(list).find('.issue_container').not('.child_issue');
            var closed_issues = jQuery(list).find('.issue_container.issue_closed').not('.child_issue');
            var visible_issues = list_issues.filter(':visible');
            var sum_points = 0;
            var sum_hours = 0;
            visible_issues.each(function (index) {
                var elm = $(this);
                if (!elm.hasClassName('child_issue')) {
                    if (elm.dataset.estimatedPoints !== undefined)
                        sum_points += parseInt(elm.dataset.estimatedPoints);
                    if (elm.dataset.estimatedHours !== undefined)
                        sum_hours += parseInt(elm.dataset.estimatedHours);
                }
            });
            var num_visible_issues = visible_issues.size();
            var milestone_id = $(list).up('.milestone_box').dataset.milestoneId;

            if (milestone_id != 0) {
                var multiplier = 100 / list_issues.size();
                var pct = Math.floor(closed_issues.size() * multiplier);
                $('milestone_' + milestone_id + '_percentage_filler').setStyle({width: pct + '%'});
            }

            if (num_visible_issues === 0 && !$(list).hasClassName('collapsed')) {
                if (list_issues.size() > 0) {
                    $('milestone_' + milestone_id + '_unassigned').hide();
                    $('milestone_' + milestone_id + '_unassigned_filtered').show();
                } else {
                    $('milestone_' + milestone_id + '_unassigned').show();
                    $('milestone_' + milestone_id + '_unassigned_filtered').hide();
                }
                $(list).addClassName('empty');
            } else {
                $('milestone_' + milestone_id + '_unassigned').hide();
                $('milestone_' + milestone_id + '_unassigned_filtered').hide();
                $(list).removeClassName('empty');
            }
            if (num_visible_issues !== list_issues.size() && milestone_id != '0') {
                $('milestone_' + milestone_id + '_issues_count').update(num_visible_issues + ' (' + list_issues.size() + ')');
            } else {
                $('milestone_' + milestone_id + '_issues_count').update(num_visible_issues);
            }
            $('milestone_' + milestone_id + '_points_count').update(sum_points);
            $('milestone_' + milestone_id + '_hours_count').update(sum_hours);
        };

        TBG.Project.Planning.calculateAllMilestonesVisibilityDetails = function () {
            jQuery('.milestone_box.initialized').find('.milestone_issues').each(function (index) {
                var was_collapsed = $(this).hasClassName('collapsed');
                $(this).removeClassName('collapsed');
                TBG.Project.Planning.calculateMilestoneIssueVisibilityDetails(this);
                if (was_collapsed && parseInt($(this).up('.milestone_box').dataset.milestoneId) !== 0) $(this).addClassName('collapsed');
            });
        };

        TBG.Project.Planning.calculateNewBacklogMilestoneDetails = function (event, ui) {
            if (event === undefined || jQuery(ui.item).hasClass('new_milestone_marker')) {
                var nbmm = (event === undefined) ? $('new_backlog_milestone_marker') : $(ui.placeholder[0]);
                var num_issues = 0;
                var sum_points = 0;
                var sum_hours = 0;
                var include_closed = $('milestone_list').hasClassName('show_closed');
                jQuery('.milestone_issue').removeClass('included');
                nbmm.up('.milestone_issues').childElements().each(function (elm) {
                    elm.addClassName('included');
                    if (!(elm.hasClassName('new_milestone_marker') && !elm.hasClassName('ui-sortable-helper')) && !elm.hasClassName('ui-element-placeholder')) {
                        if (!elm.hasClassName('new_milestone_marker')) {
                            if (include_closed || !elm.hasClassName('issue_closed'))
                                num_issues++;
                            if (!elm.hasClassName('child_issue')) {
                                if (elm.down('.issue_container').dataset.estimatedPoints !== undefined)
                                    sum_points += parseInt(elm.down('.issue_container').dataset.estimatedPoints);
                                if (elm.down('.issue_container').dataset.estimatedHours !== undefined)
                                    sum_hours += parseInt(elm.down('.issue_container').dataset.estimatedHours);
                            }
                        }
                    } else {
                        throw $break;
                    }
                });
                $('new_backlog_milestone_issues_count').update(num_issues);
                $('new_backlog_milestone_points_count').update(sum_points);
                $('new_backlog_milestone_hours_count').update(sum_hours);
            }
        };

        TBG.Project.Planning.sortMilestones = function (event, ui) {
            var list = $(event.target);
            var url = list.dataset.sortUrl;
            var items = '';
            list.childElements().each(function (milestone, index) {
                if (milestone.dataset.milestoneId !== undefined) {
                    items += '&milestone_ids['+index+']=' + milestone.dataset.milestoneId;
                }
            });
            TBG.Main.Helpers.ajax(url, {
                url_method: 'post',
                additional_params: items,
                loading: {indicator: 'planning_indicator'}
            });
        };

        TBG.Project.Planning.doSortMilestoneIssues = function (list) {
            var url = list.up('.milestone_box').dataset.issuesUrl;
            var items = '';
            list.childElements().each(function (issue) {
                if (issue.dataset.issueId !== undefined) {
                    items += '&issue_ids[]=' + issue.dataset.issueId;
                }
            });
            TBG.Main.Helpers.ajax(url, {
                url_method: 'post',
                additional_params: items,
                loading: {indicator: list.up('.milestone_box').down('.planning_indicator')}
            });
        };

        TBG.Project.Planning.sortMilestoneIssues = function (event, ui) {
            var list = $(event.target);
            var issue = $(ui.item[0]);
            if (issue.dataset.sortCancel) {
                issue.dataset.sortCancel = null;
                jQuery(this).sortable("cancel");
            } else {
                if (ui !== undefined && ui.item.hasClass('new_milestone_marker')) {
                    TBG.Project.Planning.calculateNewBacklogMilestoneDetails();
                } else {
                    TBG.Project.Planning.doSortMilestoneIssues(list);
                }
            }
        };

        TBG.Project.Planning.moveIssue = function (event, ui) {
            var issue = $(ui.item[0]);
            if (issue.dataset.sortCancel) {
                issue.dataset.sortCancel = null;
                jQuery(this).sortable("cancel");
            } else {
                if (issue.hasClassName('milestone_issue')) {
                    var list = $(event.target);
                    var url = list.up('.milestone_box').dataset.assignIssueUrl;
                    var original_list = $(ui.sender[0]);
                    TBG.Main.Helpers.ajax(url, {
                        additional_params: 'issue_id=' + issue.dataset.issueId,
                        loading: {indicator: list.up('.milestone_box').down('.planning_indicator')},
                        complete: {
                            callback: function (json) {
                                if (list.up('.milestone_box').hasClassName('initialized')) {
                                    issue.down('.issue_container').dataset.lastUpdated = get_current_timestamp();
                                    TBG.Project.Planning.doSortMilestoneIssues(list);
                                    TBG.Core.Pollers.Callbacks.planningPoller();
                                    TBG.Project.Planning.calculateMilestoneIssueVisibilityDetails(list);
                                    TBG.Project.Planning.calculateMilestoneIssueVisibilityDetails(original_list);
                                } else {
                                    issue.remove();
                                    var milestone_id = list.up('.milestone_box').dataset.milestoneId;
                                    $('milestone_' + milestone_id + '_issues_count').update(json.issues);
                                    $('milestone_' + milestone_id + '_points_count').update(json.points);
                                    $('milestone_' + milestone_id + '_hours_count').update(json.hours);
                                }
                            }
                        }
                    });
                }
            }
        };

        TBG.Project.Planning.toggleSwimlaneDetails = function (selected_item) {
            $('swimlane_details_container').childElements().each(Element.hide);
            $('swimlane_' + $(selected_item).dataset.swimlaneType + '_container').show();
            $('swimlane_input').setValue($(selected_item).dataset.swimlaneType);
        };

        TBG.Project.Planning.toggleSwimlaneExpediteDetails = function(selected_item) {
            $('swimlane_expedite_container_details').childElements().each(Element.hide);
            $('swimlane_expedite_identifier_' + $(selected_item).dataset.value + '_values').show();
        };

        TBG.Project.Planning.removeAgileBoard = function (url) {
            TBG.Main.Helpers.ajax(url, {
                url_method: 'delete',
                loading: {
                    indicator: 'dialog_indicator',
                    callback: function () {
                        ['dialog_yes', 'dialog_no'].each(function (elm) {
                            elm.addClassName('disabled');
                        });
                    }
                },
                success: {
                    callback: function (json) {
                        $('agileboard_' + json.board_id).remove();
                        TBG.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        };

        TBG.Project.Planning.saveAgileBoard = function (item) {
            var url = item.action;
            TBG.Main.Helpers.ajax(url, {
                form: item,
                loading: {
                    indicator: 'agileboard_edit_indicator',
                    disable: 'agileboard_save_button'
                },
                success: {
                    enable: 'agileboard_save_button',
                    callback: function (json) {
                        if ($('boards_list_container')) {
                            if ($('agileboard_' + json.id)) {
                                $('agileboard_' + json.id).replace(json.component);
                            } else {
                                var container = (json.private == 1) ? $('add_board_user_link') : $('add_board_project_link');
                                container.insert({before: json.component});
                            }
                            TBG.Main.Helpers.Backdrop.reset();
                        } else if ($('project_planning') && parseInt($('project_planning').dataset.boardId) == parseInt(json.id) && $('project_planning').hasClassName('whiteboard')) {
                            TBG.Main.Helpers.Backdrop.reset();
                            TBG.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                            TBG.Project.Planning.Whiteboard.retrieveWhiteboard();
                        } else if ($('project_planning') && parseInt($('project_planning').dataset.boardId) == parseInt(json.id)) {
                            var backlog = $('milestone_0');
                            TBG.Main.Helpers.Backdrop.reset();
                            if (backlog.dataset.backlogSearch != json.backlog_search) {
                                $('planning_indicator').show();
                                window.location.reload(true);
                            } else {
                                backlog.removeClassName('initialized');
                                $('milestone_0_issues').update('');
                                $('milestone_0_issues').removeClassName('ui-sortable');
                                backlog.down('.planning_indicator').show();
                                TBG.Project.Planning.initialize(TBG.Project.Planning.options);
                            }
                        }
                    }
                }
            });
        };

        TBG.Main.setFancyDropdownValue = function (item) {
            var dropdown = $(item).up('ul');
            if ($(dropdown.dataset.input)) $(dropdown.dataset.input).setValue($(item).dataset.inputValue);
            dropdown.dataset.selectedValue = $(item).dataset.inputValue;
            dropdown.childElements().each(function (elm) {
                elm.removeClassName('selected');
            });
            $(item).addClassName('selected');
            var dropdownfancylabel = $(item).up('ul').previous();
            dropdownfancylabel.removeClassName('selected');
            if (!dropdownfancylabel.hasClassName('self-updateable')) dropdownfancylabel.update($(item).dataset.displayName);
        };

        TBG.Project.Milestone.markFinished = function (form) {
            var url = form.action;
            var milestone_id = form.dataset.milestoneId;
            TBG.Main.Helpers.ajax(url, {
                form: form,
                loading: {
                    indicator: 'milestone_edit_indicator',
                    callback: function () {
                        $('mark_milestone_finished_form').select('input.button').each(Element.disable);
                    }
                },
                success: {
                    remove: 'milestone_' + milestone_id,
                    callback: function (json) {
                        TBG.Main.Helpers.Backdrop.reset();
                        if (json.component) {
                            $('milestone_list').insert(json.component);
                            setTimeout(function () {
                                TBG.Project.Planning.getMilestoneIssues($('milestone_' + json.new_milestone_id), TBG.Project.Planning.initializeDragDropSorting);
                            }, 250);
                        } else {
                            TBG.Core.Pollers.Callbacks.planningPoller();
                        }
                    }
                },
                failure: {
                    callback: function () {
                        $('mark_milestone_finished_form').select('input.button').each(Element.enable);
                    }
                }
            });
        };

        TBG.Project.Milestone.save = function (form) {
            var url = form.action;
            var issues = "";
            var include_selected_issues = $('include_selected_issues').getValue() == 1;
            var on_board = $('project_roadmap_page') == null;
            if (include_selected_issues) {
                $$('.milestone_issue.included').each(function (issue) {
                    issues += '&issues[]=' + issue.dataset.issueId;
                });
            }
            TBG.Main.Helpers.ajax(url, {
                form: form,
                additional_params: issues,
                loading: {indicator: 'milestone_edit_indicator'},
                success: {
                    reset: 'edit_milestone_form',
                    hide: 'no_milestones',
                    callback: function (json) {
                        $$('.milestone_issue.included').each(function (issue) { issue.remove(); });
                        TBG.Main.Helpers.Backdrop.reset();
                        if ($('milestone_' + json.milestone_id)) {
                            $('milestone_' + json.milestone_id).replace(json.component);
                        } else {
                            $('milestone_list').insert(json.component);
                        }
                        if (on_board) {
                            if (!include_selected_issues) {
                                setTimeout(function () {
                                    TBG.Project.Planning.getMilestoneIssues($('milestone_' + json.milestone_id), TBG.Project.Planning.initializeDragDropSorting);
                                }, 250);
                            } else {
                                TBG.Project.Planning.calculateMilestoneIssueVisibilityDetails($('milestone_0_issues'));
                                TBG.Project.Planning.initializeDragDropSorting();
                            }
                        }
                    }
                }
            });
        }

        TBG.Project.Milestone.remove = function (url, milestone_id) {
            TBG.Main.Helpers.ajax(url, {
                url_method: 'delete',
                loading: {
                    indicator: 'dialog_indicator',
                },
                success: {
                    callback: function (json) {
                        $('milestone_' + milestone_id).remove();
                        TBG.Main.Helpers.Dialog.dismiss();
                        TBG.Main.Helpers.Backdrop.reset();
                        if ($('milestone_list').childElements().size() == 0)
                            $('no_milestones').show();
                        TBG.Core.Pollers.Callbacks.planningPoller();
                    }
                }
            });
        }

        TBG.Project.Build.doAction = function (url, bid, action, update) {
            var update_elm = (update == 'all') ? 'build_table' : 'build_list_' + bid;
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'build_' + bid + '_indicator',
                    hide: 'build_' + bid + '_info'
                },
                success: {
                    update: update_elm
                },
                complete: {
                    show: 'build_' + bid + '_info'
                }
            });
        }

        TBG.Project.Build.update = function (url, bid) {
            TBG.Main.Helpers.ajax(url, {
                form: 'edit_build_' + bid,
                loading: {
                    indicator: 'build_' + bid + '_indicator',
                    hide: 'build_' + bid + '_info'
                },
                success: {
                    update: 'build_list_' + bid
                },
                complete: {
                    show: 'build_' + bid + '_info'
                }
            });
        }

        TBG.Project.Build.addToOpenIssues = function (url, bid) {
            TBG.Main.Helpers.ajax(url, {
                form: 'addtoopen_build_' + bid,
                loading: {
                    indicator: 'build_' + bid + '_indicator',
                    hide: 'build_' + bid + '_info'
                },
                success: {
                    hide: 'addtoopen_build_' + bid
                },
                complete: {
                    show: 'build_' + bid + '_info'
                }
            });
        }

        TBG.Project.Build.remove = function (url, bid, b_type, edition_id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    show: 'fullpage_backdrop_indicator',
                    indicator: 'fullpage_backdrop',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop', 'build_' + bid + '_info'],
                    callback: function () {
                        $('build_' + bid + '_indicator').addClassName('selected_red');
                    }
                },
                success: {
                    remove: ['show_build_' + bid],
                    callback: function () {
                        TBG.Main.Helpers.Dialog.dismiss();
                        if ($(b_type + '_builds_' + edition_id).childElements().size() == 0) {
                            $('no_' + b_type + '_builds_' + edition_id).show();
                        }
                    }
                },
                failure: {
                    show: 'build_' + bid + '_info',
                    hide: 'del_build_' + bid,
                    callback: function () {
                        $('build_' + bid + '_indicator').removeClassName('selected_red');
                    }
                }
            });
        }

        TBG.Project.Build.add = function (url, edition_id) {
            TBG.Main.Helpers.ajax(url, {
                form: 'add_build_form',
                loading: {indicator: 'build_add_indicator'},
                success: {
                    reset: 'add_build_form',
                    hide: 'no_builds_' + edition_id,
                    update: {element: 'builds_' + edition_id, insertion: true, from: 'html'}
                }
            });
        }

        TBG.Project.saveOther = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'project_other',
                loading: {indicator: 'settings_save_indicator'}
            });
        }

        TBG.Project.Edition.edit = function (url, edition_id)
        {
            TBG.Main.Helpers.Backdrop.show(url);
        }

        TBG.Project.Edition.remove = function (url, eid) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: 'edition_' + eid + '_delete_indicator'},
                success: {
                    remove: ['edition_' + eid + '_box', 'edition_' + eid + '_permissions'],
                    callback: function (json) {
                        if (json.itemcount == 0)
                            $('no_editions').show();
                        TBG.Main.Helpers.Dialog.dismiss();
                    }
                },
                failure: {
                    hide: 'del_edition_' + eid
                }
            });
        }

        TBG.Project.Edition.add = function (url) {
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

        TBG.Project.Edition.submitSettings = function (url, edition_id) {
            TBG.Main.Helpers.ajax(url, {
                form: 'edition_settings_form',
                loading: {indicator: 'edition_save_indicator'},
                success: {
                    update: {element: 'edition_' + edition_id + '_name', from: 'edition_name'}
                }
            });
        }

        TBG.Project.Edition.Component.add = function (url, cid) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    callback: function () {
                        $('project_component_' + cid).fade();
                    }
                },
                success: {
                    callback: function () {
                        $('edition_component_count').value++;
                        $('edition_component_' + cid).appear();
                    },
                    hide: 'edition_no_components'
                },
                failure: {
                    callback: function () {
                        $('project_component_' + cid).appear();
                    }
                }
            });
        }

        TBG.Project.Edition.Component.remove = function (url, cid) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    callback: function () {
                        $('edition_component_' + cid).fade();
                    }
                },
                success: {
                    callback: function () {
                        $('edition_component_count').value--;
                        if ($('edition_component_count').value == 0)
                            $('edition_no_components').appear();
                        $('project_component_' + cid).show();
                    }
                },
                failure: {
                    callback: function () {
                        $('edition_component_' + cid).appear();
                    }
                }
            });
        }

        TBG.Project.Component.update = function (url, cid) {
            TBG.Main.Helpers.ajax(url, {
                form: 'edit_component_' + cid + '_form',
                loading: {
                    indicator: 'component_' + cid + '_indicator'
                },
                success: {
                    update: {element: 'component_' + cid + '_name', from: 'newname'},
                    hide: 'edit_component_' + cid,
                    show: 'show_component_' + cid
                }
            });
        }

        TBG.Project.Component.add = function (url) {
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

        TBG.Project.Component.remove = function (url, cid) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: 'component_' + cid + '_delete_indicator'},
                success: {
                    remove: ['show_component_' + cid, 'edit_component_' + cid, 'component_' + cid + '_permissions'],
                    callback: function (json) {
                        if (json.itemcount == 0)
                            $('no_components').show();
                        TBG.Main.Helpers.Dialog.dismiss();
                    }
                },
                failure: {
                    hide: 'del_component_' + cid
                }
            });
        }

        TBG.Project.Component.edit = function (url, component_id)
        {
            TBG.Main.Helpers.Backdrop.show(url);
        }

        TBG.Project.submitAdvancedSettings = function (url) {
            TBG.Project._submitDetails(url, 'project_settings');
        }

        TBG.Project.submitDisplaySettings = function (url) {
            TBG.Project._submitDetails(url, 'project_other');
        }

        TBG.Project.submitInfo = function (url, pid) {
            TBG.Project._submitDetails(url, 'project_info', pid);
        }

        TBG.Project._submitDetails = function (url, form_id, pid) {
            TBG.Main.Helpers.ajax(url, {
                form: form_id,
                loading: {indicator: form_id + '_indicator'},
                success: {
                    callback: function (json) {
                        if ($('project_name_span'))
                            $('project_name_span').update($('project_name_input').getValue());
                        if ($('project_description_span')) {
                            if ($('project_description_input').getValue()) {
                                $('project_description_span').update(json.project_description);
                                $('project_no_description').hide();
                            } else {
                                $('project_description_span').update('');
                                $('project_no_description').show();
                            }
                        }
                        if ($('project_key_span'))
                            $('project_key_span').update(json.project_key);
                        if ($('sidebar_link_scrum') && $('use_scrum').getValue() == 1)
                            $('sidebar_link_scrum').show();
                        else if ($('sidebar_link_scrum'))
                            $('sidebar_link_scrum').hide();

                        ['edition', 'component'].each(function (element) {
                            if ($('enable_' + element + 's').getValue() == 1) {
                                $('add_' + element + '_button').show();
                                $('project_' + element + 's').show();
                                $('project_' + element + 's_disabled').hide();
                            } else {
                                $('add_' + element + '_button').hide();
                                $('project_' + element + 's').hide();
                                $('project_' + element + 's_disabled').show();
                            }
                        });

                        if (pid != undefined && $('project_box_' + pid) != undefined)
                            $('project_box_' + pid).update(json.content);
                    }
                }
            });
        }

        TBG.Project.findDevelopers = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'find_dev_form',
                loading: {indicator: 'find_dev_indicator'},
                success: {update: 'find_dev_results'}
            });
        }

        TBG.Project._updateUserFromJSON = function (object, field) {
            if (object.id == 0) {
                $(field + '_name').hide();
                $('no_' + field).show();
            } else {
                $(field + '_name').update(object.name);
                $('no_' + field).hide();
                $(field + '_name').show();
            }
        }

        TBG.Project.setUser = function (url, field) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: field + '_spinning'},
                success: {
                    hide: field + '_change',
                    callback: function (json) {
                        TBG.Project._updateUserFromJSON(json.field, field);
                    }
                }
            });
        }

        TBG.Project.assign = function (url, container_id) {
            var role_id = $(container_id).down('select').getValue();
            var parameters = "&role_id=" + role_id;
            TBG.Main.Helpers.ajax(url, {
                params: parameters,
                loading: {indicator: 'assign_dev_indicator'},
                success: {update: 'assignees_list'}
            });
        }

        TBG.Project.removeAssignee = function (url, type, id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'remove_assignee_' + type + '_' + id + '_indicator',
                    hide: 'assignee_' + type + '_' + id + '_link'
                },
                success: {
                    remove: 'assignee_' + type + '_' + id + '_row',
                    callback: function () {
                        if ($('project_team_' + type + 's').childElements().size() == 0) {
                            $('project_team_' + type + 's').hide();
                            $('no_project_team_' + type + 's').show();
                        }
                    }
                }
            });
        }

        TBG.Project.edit = function (url) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: 'backdrop_detail_indicator'},
                success: {update: 'backdrop_detail_content'}
            });
        }

        TBG.Project.workflow = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'workflow_form2',
                loading: {indicator: 'update_workflow_indicator'},
                success: {callback: function () {
                    TBG.Main.Helpers.Backdrop.reset();
                }}
            });
        }

        TBG.Project.workflowtable = function (url) {
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

        TBG.Project.updatePrefix = function (url, project_id) {
            TBG.Main.Helpers.ajax(url, {
                form: 'project_info',
                loading: {indicator: 'project_key_indicator'},
                success: {update: 'project_key_input'}
            });
        }

        TBG.Project.clearReleaseCenterFilters = function () {
            var prcc = $('project_release_center_container');
            ['only_archived', 'only_active', 'only_downloads'].each(function (cn) {
                prcc.removeClassName(cn);
            });
        };

        TBG.Project.checkAndToggleNoBuildsMessage = function () {
            $$('.simple_list').each(function (elem) {
                // If this list does not contain builds continue.
                if (elem.id.indexOf('active_builds_') !== 0) return;

                // We assume no build is visible.
                var one_build_visible = false;

                $(elem).childElements().each(function (elem) {
                    // If this child - build is not visible continue.
                    if (! jQuery('#' + elem.id).is(':visible')) return;

                    // Once we find visible build set flag and break this loop.
                    one_build_visible = true;
                    return false;
                });

                // Hide or show no builds message based on one build visible flag.
                if (one_build_visible) {
                    $('no_' + elem.id).hide();
                }
                else {
                    $('no_' + elem.id).show();
                }
            });
        };

        TBG.Project.clearRoadmapFilters = function () {
            var prp = $('project_roadmap_page');
            ['upcoming', 'past'].each(function (cn) {
                prp.removeClassName(cn);
            });
        };

        TBG.Project.showRoadmap = function () {
            $('milestone_details_overview').hide();
            $('project_roadmap').show();
        }

        TBG.Project.showMilestoneDetails = function (url, milestone_id, force) {
            $$('body')[0].setStyle({'overflow': 'auto'});

            var force = force || false;

            if (force && $('milestone_details_' + milestone_id)) {
                $('milestone_details_' + milestone_id).remove();
            }

            if (!$('milestone_details_' + milestone_id)) {
                window.location.hash = 'roadmap_milestone_' + milestone_id;

                TBG.Main.Helpers.ajax(url, {
                    url_method: 'get',
                    loading: {
                        indicator: 'fullpage_backdrop',
                        show: 'fullpage_backdrop_indicator',
                        hide: ['fullpage_backdrop_content', 'project_roadmap']
                    },
                    success: {
                        show: 'milestone_details_overview',
                        update: 'milestone_details_overview'
                    }
                });
            } else {
                $('project_roadmap').hide();
                $('milestone_details_overview').show();
            }
        }

        TBG.Project.toggleLeftSelection = function (item) {
            $(item).up('ul').childElements().each(function (elm) {
                elm.removeClassName('selected');
            });
            $(item).up('li').addClassName('selected');
        };

        TBG.Config.Import.importCSV = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'import_csv_form',
                loading: {
                    indicator: 'csv_import_indicator',
                    hide: 'csv_import_error'
                },
                failure: {
                    show: 'csv_import_error',
                    callback: function (json) {
                        $('csv_import_error_detail').update(json.errordetail);
                    }
                }
            });
        }

        TBG.Config.Import.getImportCsvIds = function (url) {
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

        TBG.Config.updateCheck = function (url) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'update_spinner',
                    hide: 'update_button'
                },
                success: {
                    callback: function (json) {
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

        TBG.Config.Issuetype.showOptions = function (url, id) {
            $('issuetype_' + id + '_content').toggle();
            if ($('issuetype_' + id + '_content').childElements().size() == 0) {
                TBG.Main.Helpers.ajax(url, {
                    loading: {indicator: 'issuetype_' + id + '_indicator'},
                    success: {update: 'issuetype_' + id + '_content'}
                });
            }
        }

        TBG.Config.Issuetype.update = function (url, id) {
            TBG.Main.Helpers.ajax(url, {
                form: 'edit_issuetype_' + id + '_form',
                loading: {indicator: 'edit_issuetype_' + id + '_indicator'},
                success: {
                    hide: 'edit_issuetype_' + id + '_form',
                    callback: function (json) {
                        if (json.description != undefined)
                            $('issuetype_' + id + '_description_span').update(json.description);
                        if (json.name != undefined) {
                            $('issuetype_' + id + '_name_span').update(json.name);
                            if ($('issuetype_' + id + '_info'))
                                $('issuetype_' + id + '_info').show();
                        }
                    }
                }
            });
        }

        TBG.Config.Issuetype.remove = function (url, id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    remove: 'issuetype_' + id + '_box',
                    callback: TBG.Main.Helpers.Dialog.dismiss
                }
            });
        }

        TBG.Config.Issuetype.Choices.update = function (url, id) {
            TBG.Main.Helpers.ajax(url, {
                form: 'update_' + id + '_choices_form',
                loading: {indicator: 'update_' + id + '_choices_indicator'},
                success: {hide: 'issuetype_' + id + '_content'}
            });
        }

        TBG.Config.Issuetype.add = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'add_issuetype_form',
                loading: {
                    reset: 'add_issuetype_form',
                    indicator: 'add_issuetype_indicator'
                },
                success: {
                    update: {element: 'issuetypes_list', insertion: true}
                }
            });
        }

        TBG.Config.Issuetype.toggleForScheme = function (url, issuetype_id, scheme_id, action) {
            var hide_element = 'type_toggle_' + issuetype_id + '_' + action;
            var show_element = 'type_toggle_' + issuetype_id + '_' + ((action == 'enable') ? 'disable' : 'enable');
            var cb;
            if (action == 'enable') {
                cb = function (json) {
                    $('issuetype_' + json.issuetype_id + '_box').addClassName("greenbox");
                    $('issuetype_' + json.issuetype_id + '_box').removeClassName("greybox");
                };
            } else {
                cb = function (json) {
                    $('issuetype_' + json.issuetype_id + '_box').removeClassName("greenbox");
                    $('issuetype_' + json.issuetype_id + '_box').addClassName("greybox");
                };
            }
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'issuetype_' + issuetype_id + '_indicator',
                    hide: hide_element
                },
                success: {
                    show: show_element,
                    callback: cb
                }
            });
        }

        TBG.Config.IssuetypeScheme.copy = function (url, scheme_id) {
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

        TBG.Config.IssuetypeScheme.remove = function (url, scheme_id) {
            TBG.Main.Helpers.ajax(url, {
                form: 'delete_issuetype_scheme_' + scheme_id + '_form',
                loading: {
                    indicator: 'delete_issuetype_scheme_' + scheme_id + '_indicator'
                },
                success: {
                    remove: ['delete_scheme_' + scheme_id + '_popup', 'copy_scheme_' + scheme_id + '_popup', 'issuetype_scheme_' + scheme_id],
                    update: {element: 'issuetype_schemes_list', insertion: true},
                    callback: function () {
                        TBG.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        }

        TBG.Config.Issuefields.saveOrder = function (container, type, url) {
            TBG.Main.Helpers.ajax(url, {
                additional_params: Sortable.serialize(container),
                loading: {
                    indicator: type + '_sort_indicator'
                }
            });
        };

        TBG.Config.Issuefields.Options.show = function (url, field) {
            $(field + '_content').toggle();
            if ($(field + '_content').childElements().size() == 0) {
                TBG.Main.Helpers.ajax(url, {
                    loading: {indicator: field + '_indicator'},
                    success: {
                        update: field + '_content',
                        callback: TBG.Main.Helpers.initializeColorPicker
                    }
                });
            }
        }

        TBG.Config.Issuefields.Options.add = function (url, type) {
            TBG.Main.Helpers.ajax(url, {
                form: 'add_' + type + '_form',
                loading: {indicator: 'add_' + type + '_indicator'},
                success: {
                    reset: 'add_' + type + '_form',
                    hide: 'no_' + type + '_items',
                    update: {element: type + '_list', insertion: true},
                    callback: function () {
                        if (sortable_options != undefined) {
                            Sortable.destroy(type + '_list');
                            Sortable.create(type + '_list', sortable_options);
                        }
                        TBG.Main.Helpers.initializeColorPicker();
                    }
                }
            });
        }

        TBG.Config.Issuefields.Options.update = function (url, type, id) {
            TBG.Main.Helpers.ajax(url, {
                form: 'edit_' + type + '_' + id + '_form',
                loading: {indicator: 'edit_' + type + '_' + id + '_indicator'},
                success: {
                    show: 'item_option_' + type + '_' + id + '_content',
                    hide: 'edit_item_option_' + id,
                    callback: function (json) {
                        $(type + '_' + id + '_name').update($(type + '_' + id + '_name_input').getValue());
                        if ($(type + '_' + id + '_itemdata_input') && $(type + '_' + id + '_itemdata'))
                            $(type + '_' + id + '_itemdata').style.backgroundColor = $(type + '_' + id + '_itemdata_input').getValue();
                        if ($(type + '_' + id + '_value_input') && $(type + '_' + id + '_value'))
                            $(type + '_' + id + '_value').update($(type + '_' + id + '_value_input').getValue());
                    }
                }
            });
        }

        TBG.Config.Issuefields.Options.remove = function (url, type, id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    remove: 'item_option_' + type + '_' + id,
                    callback: function (json) {
                        TBG.Main.Helpers.Dialog.dismiss();
                        if ($(type + '_list').childElements().size() == 0) {
                            $('no_' + type + '_items').show();
                        }
                    }
                }
            });
        }

        TBG.Config.Issuefields.Custom.add = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'add_custom_type_form',
                loading: {
                    indicator: 'add_custom_type_indicator',
                    reset: 'add_custom_type_form'
                },
                success: {
                    update: {element: 'custom_types_list', insertion: true}
                }
            });
        }

        TBG.Config.Issuefields.Custom.update = function (url, type) {
            TBG.Main.Helpers.ajax(url, {
                form: 'edit_custom_type_' + type + '_form',
                loading: {indicator: 'edit_custom_type_' + type + '_indicator'},
                success: {
                    hide: 'edit_custom_type_' + type + '_form',
                    callback: function (json) {
                        $('custom_type_' + type + '_description_span').update(json.description);
                        $('custom_type_' + type + '_instructions_span').update(json.instructions);
                        if (json.instructions != '') {
                            $('custom_type_' + type + '_instructions_div').show();
                            $('custom_type_' + type + '_no_instructions_div').hide();
                        } else {
                            $('custom_type_' + type + '_instructions_div').hide();
                            $('custom_type_' + type + '_no_instructions_div').show();
                        }
                        $('custom_type_' + type + '_name').update(json.name);
                    },
                    show: 'custom_type_' + type + '_info'
                }
            });
        }

        TBG.Config.Issuefields.Custom.remove = function (url, type, id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    remove: 'item_' + type + '_' + id,
                    callback: function (json) {
                        TBG.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        };

        TBG.Config.Permissions.set = function (url, field) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: field + '_indicator',
                    callback: function (json) {
                        $$('#' + field + ' .image img').each(function (element) {
                            $(element).hide();
                        });
                    }
                },
                success: {update: field + '_wrapper'}
            });
        };

        TBG.Config.Permissions.getOptions = function (url, field) {
            $(field).toggle();
            if ($(field).childElements().size() == 0) {
                TBG.Main.Helpers.ajax(url, {
                    loading: {indicator: field + '_indicator'},
                    success: {update: field}
                });
            }
        }

        TBG.Config.Roles.getPermissions = function (url, field) {
            $(field).toggle();
            if ($(field).childElements().size() == 0) {
                TBG.Main.Helpers.ajax(url, {
                    url_method: 'get',
                    loading: {indicator: field + '_indicator'},
                    success: {update: field}
                });
            }
        }

        TBG.Config.Roles.getPermissionsEdit = function (url, field) {
            $(field).toggle();
            if ($(field).childElements().size() == 0) {
                TBG.Main.Helpers.ajax(url, {
                    url_method: 'get',
                    loading: {indicator: field + '_indicator'},
                    success: {update: field}
                });
            }
        }

        TBG.Config.Roles.update = function (url, role_id) {
            TBG.Main.Helpers.ajax(url, {
                form: 'role_' + role_id + '_form',
                loading: {indicator: 'role_' + role_id + '_form_indicator'},
                success: {
                    hide: 'role_' + role_id + '_permissions_edit',
                    callback: function (json) {
                        $('role_' + role_id + '_permissions_count').update(json.permissions_count);
                        $('role_' + role_id + '_permissions_list').update('');
                        $('role_' + role_id + '_permissions_list').hide();
                        $('role_' + role_id + '_name').update(json.role_name);
                    }
                }
            });
        }

        TBG.Config.Roles.remove = function (url, role_id) {
            TBG.Main.Helpers.ajax(url, {
                url_method: 'post',
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    callback: function () {
                        var rc = $('role_' + role_id + '_container');
                        if (rc.up('ul').childElements().size() == 2) {
                            rc.up('ul').down('li.no_roles').show();
                        }
                        rc.remove();
                    }
                }
            });
        }

        TBG.Config.Roles.add = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'new_role_form',
                loading: {indicator: 'new_role_form_indicator'},
                success: {
                    update: {element: 'global_roles_list', insertion: true},
                    hide: ['global_roles_no_roles'],
                    callback: function  () {
                        $('add_new_role_input').setValue('');
                    }
                }
            });
        };

        TBG.Project.Roles.add = function (url, pid) {
            TBG.Main.Helpers.ajax(url, {
                form: 'new_project' + pid + '_role_form',
                loading: {indicator: 'new_project' + pid + '_role_form_indicator'},
                success: {
                    update: {element: 'project' + pid + '_roles_list', insertion: true},
                    hide: ['project' + pid + '_roles_no_roles', 'new_project' + pid + '_role']
                }
            });
        };

        TBG.Config.User.show = function (url, findstring) {
            TBG.Main.Helpers.ajax(url, {
                params: '&findstring=' + findstring,
                loading: {indicator: 'find_users_indicator'},
                success: {update: 'users_results'}
            });
        };

        TBG.Config.User.add = function (url, callback_function_for_import, form) {
            f = (form !== undefined) ? form : 'createuser_form';
            TBG.Main.Helpers.ajax(url, {
                form: f,
                success: {
                    hide: ['createuser_form_indicator', 'createuser_form_quick_indicator'],
                    update: 'users_results',
                    callback: function (json) {
                        $('adduser_div').hide();
                        TBG.Config.User._updateLinks(json);
                        $(f).reset();
                    }
                },
                failure: {
                    hide: ['createuser_form_indicator', 'createuser_form_quick_indicator'],
                    callback: function (json) {
                        if (json.allow_import || false) {
                            callback_function_for_import();
                        }
                    }
                }
            });
        };

        TBG.Config.User.addToScope = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'createuser_form',
                loading: {indicator: 'dialog_indicator'},
                success: {
                    update: 'users_results',
                    callback: function (json) {
                        TBG.Main.Helpers.Dialog.dismiss();
                        TBG.Config.User._updateLinks(json);
                    }
                }
            });
        };

        TBG.Config.User.getEditForm = function (url, uid) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'user_' + uid + '_edit_spinning',
                    hide: 'users_results_user_' + uid
                },
                success: {
                    // update: 'user_' + uid + '_edit_td',
                    update: 'user_' + uid + '_edit_td',
                    show: ['user_' + uid + '_edit_tr', 'users_results_user_' + uid]
                },
                failure: {
                    show: 'users_results_user_' + uid
                }
            });
        };

        TBG.Config.User.remove = function (url, user_id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    remove: ['users_results_user_' + user_id, 'user_' + user_id + '_edit_spinning', 'user_' + user_id + '_edit_tr', 'users_results_user_' + user_id + '_permissions_row'],
                    callback: TBG.Config.User._updateLinks
                }
            });
        };

        TBG.Config.User._updateLinks = function (json) {
            if (json == null) return;
            if ($('current_user_num_count'))
                $('current_user_num_count').update(json.total_count);
            (json.more_available) ? $('adduser_form_container').show() : $('adduser_form_container').hide();
            TBG.Config.Collection.updateDetailsFromJSON(json);
        };

        TBG.Config.User.update = function (url, user_id) {
            TBG.Main.Helpers.ajax(url, {
                form: 'edit_user_' + user_id + '_form',
                loading: {indicator: 'edit_user_' + user_id + '_indicator'},
                success: {
                    update: 'users_results_user_' + user_id,
                    show: 'users_results_user_' + user_id,
                    hide: 'user_' + user_id + '_edit_tr',
                    callback: function (json) {
                        $('password_' + user_id + '_leave').checked = true;
                        $('new_password_' + user_id + '_1').value = '';
                        $('new_password_' + user_id + '_2').value = '';
                        TBG.Config.Collection.updateDetailsFromJSON(json);
                    }
                }
            });
        };

        TBG.Config.User.updateScopes = function (url, user_id) {
            TBG.Main.Helpers.ajax(url, {
                form: 'edit_user_' + user_id + '_scopes_form',
                loading: {indicator: 'edit_user_' + user_id + '_scopes_form_indicator'},
                success: {
                    callback: TBG.Main.Helpers.Backdrop.reset
                }
            });
        };

        TBG.Config.User.getPermissionsBlock = function (url, user_id) {
            $('users_results_user_' + user_id + '_permissions_row').toggle();
            if ($('users_results_user_' + user_id + '_permissions').innerHTML == '') {
                TBG.Main.Helpers.ajax(url, {
                    loading: {
                        indicator: 'permissions_' + user_id + '_indicator'
                    },
                    success: {
                        update: 'users_results_user_' + user_id + '_permissions',
                        show: 'users_results_user_' + user_id + '_permissions'
                    }
                });
            }
        };

        TBG.Config.Collection.add = function (url, type, callback_function) {
            TBG.Main.Helpers.ajax(url, {
                form: 'create_' + type + '_form',
                loading: {indicator: 'create_' + type + '_indicator'},
                success: {
                    update: {element: type + 'config_list', insertion: true},
                    callback: callback_function
                }
            });
        };

        TBG.Config.Collection.remove = function (url, type, cid, callback_function) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    remove: type + 'box_' + cid,
                    callback: function (json) {
                        if (callback_function)
                            callback_function(json);
                    }
                }
            });
        };

        TBG.Config.Collection.clone = function (url, type, cid, callback_function) {
            TBG.Main.Helpers.ajax(url, {
                form: 'clone_' + type + '_' + cid + '_form',
                loading: {indicator: 'clone_' + type + '_' + cid + '_indicator'},
                success: {
                    update: {element: type + 'config_list', insertion: true},
                    hide: 'clone_' + type + '_' + cid,
                    callback: callback_function
                }
            });
        };

        TBG.Config.Collection.showMembers = function (url, type, cid) {
            $(type + '_members_' + cid + '_container').toggle();
            if ($(type + '_members_' + cid + '_list').innerHTML == '') {
                TBG.Main.Helpers.ajax(url, {
                    loading: {indicator: type + '_members_' + cid + '_indicator'},
                    success: {update: type + '_members_' + cid + '_list'},
                    failure: {hide: type + '_members_' + cid + '_container'}
                });
            }
        };

        TBG.Config.Collection.removeMember = function (url, type, cid, user_id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: type + '_members_' + cid + '_indicator',
                    hide: 'dialog_backdrop'
                },
                success: {
                    callback: function (json) {
                        $(type + '_' + cid + '_' + user_id + '_item').remove();
                        TBG.Config.Collection.updateDetailsFromJSON(json, false);
                        var ul = $(type + '_members_' + cid + '_list').down('ul');
                        if (ul != undefined && ul.childElements().size() == 0)
                            $(type + '_members_' + cid + '_no_users').show();
                    }
                }
            });
        };

        TBG.Config.Collection.addMember = function (url, type, cid, user_id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: type + '_members_' + cid + '_indicator'},
                success: {
                    callback: function (json) {
                        TBG.Config.Collection.updateDetailsFromJSON(json, false);
                        if ($(type + '_members_' + cid + '_list').down('ul').innerHTML != '') {
                            if ($(type + '_members_' + cid + '_no_users'))
                                $(type + '_members_' + cid + '_no_users').hide();
                            $(type + '_members_' + cid + '_list').down('ul').insert({bottom: json[type + 'listitem']});
                        }
                    }
                }
            });
        };

        TBG.Config.Collection.updateDetailsFromJSON = function (json, clear) {
            if (json.update_groups) {
                json.update_groups.ids.each(function (group_id) {
                    if ($('group_' + group_id + '_membercount'))
                        $('group_' + group_id + '_membercount').update(json.update_groups.membercounts[group_id]);
                    if (clear == undefined || clear == true) {
                        $('group_members_' + group_id + '_container').hide();
                        $('group_members_' + group_id + '_list').update('');
                    }
                });
            }
            if (json.update_teams) {
                json.update_teams.ids.each(function (team_id) {
                    if ($('team_' + team_id + '_membercount'))
                        $('team_' + team_id + '_membercount').update(json.update_teams.membercounts[team_id]);
                    if (clear == undefined || clear == true) {
                        $('team_members_' + team_id + '_container').hide();
                        $('team_members_' + team_id + '_list').update('');
                    }
                });
            }
            if (json.update_clients) {
                json.update_clients.ids.each(function (client_id) {
                    if ($('client_' + client_id + '_membercount'))
                        $('client_' + client_id + '_membercount').update(json.update_clients.membercounts[client_id]);
                    if (clear == undefined || clear == true) {
                        $('client_members_' + client_id + '_container').hide();
                        $('client_members_' + client_id + '_list').update('');
                    }
                });
            }
        }

        TBG.Config.Group.add = function (url) {
            TBG.Config.Collection.add(url, 'group');
        }

        TBG.Config.Group.remove = function (url, group_id) {
            TBG.Config.Collection.remove(url, 'group', group_id);
        }

        TBG.Config.Group.clone = function (url, group_id) {
            TBG.Config.Collection.clone(url, 'group', group_id);
        }

        TBG.Config.Group.showMembers = function (url, group_id) {
            TBG.Config.Collection.showMembers(url, 'group', group_id);
        }

        TBG.Config.Team.updateLinks = function (json) {
            if ($('current_team_num_count'))
                $('current_team_num_count').update(json.total_count);
            $$('.copy_team_link').each(function (element) {
                (json.more_available) ? $(element).show() : $(element).hide();
            });
            (json.more_available) ? $('add_team_div').show() : $('add_team_div').hide();
        }

        TBG.Config.Team.getPermissionsBlock = function (url, team_id) {
            if ($('team_' + team_id + '_permissions').innerHTML == '') {
                TBG.Main.Helpers.ajax(url, {
                    loading: {
                        show: 'team_' + team_id + '_permissions_container',
                        indicator: 'team_' + team_id + '_permissions_indicator'
                    },
                    success: {
                        update: 'team_' + team_id + '_permissions',
                    }
                });
            }
            else {
                $('team_' + team_id + '_permissions_container').show();
            }
        };

        TBG.Config.Team.add = function (url) {
            TBG.Config.Collection.add(url, 'team', TBG.Config.Team.updateLinks);
        }

        TBG.Config.Team.remove = function (url, team_id) {
            TBG.Config.Collection.remove(url, 'team', team_id, TBG.Config.Team.updateLinks);
        }

        TBG.Config.Team.clone = function (url, team_id) {
            TBG.Config.Collection.clone(url, 'team', team_id, TBG.Config.Team.updateLinks);
        }

        TBG.Config.Team.showMembers = function (url, team_id) {
            TBG.Config.Collection.showMembers(url, 'team', team_id);
        }

        TBG.Config.Team.removeMember = function (url, team_id, member_id) {
            TBG.Config.Collection.removeMember(url, 'team', team_id, member_id);
        }

        TBG.Config.Team.addMember = function (url, team_id, member_id) {
            TBG.Config.Collection.addMember(url, 'team', team_id, member_id);
        }

        TBG.Config.Client.add = function (url) {
            TBG.Config.Collection.add(url, 'client');
        }

        TBG.Config.Client.remove = function (url, client_id) {
            TBG.Config.Collection.remove(url, 'client', client_id);
        }

        TBG.Config.Client.showMembers = function (url, client_id) {
            TBG.Config.Collection.showMembers(url, 'client', client_id);
        }

        TBG.Config.Client.removeMember = function (url, client_id, member_id) {
            TBG.Config.Collection.removeMember(url, 'client', client_id, member_id);
        }

        TBG.Config.Client.addMember = function (url, client_id, member_id) {
            TBG.Config.Collection.addMember(url, 'client', client_id, member_id);
        }

        TBG.Config.Client.update = function (url, client_id) {
            TBG.Main.Helpers.ajax(url, {
                form: 'edit_client_' + client_id + '_form',
                loading: {indicator: 'edit_client_' + client_id + '_indicator'},
                success: {
                    hide: 'edit_client_' + client_id,
                    update: 'client_' + client_id + '_item'
                }
            });
        }

        TBG.Config.Workflows.Transition.remove = function (url, transition_id, direction) {
            $('transition_' + transition_id + '_delete_form').submit();
        }

        TBG.Config.Workflows.Scheme.copy = function (url, scheme_id) {
            TBG.Main.Helpers.ajax(url, {
                form: 'copy_workflow_scheme_' + scheme_id + '_form',
                loading: {indicator: 'copy_workflow_scheme_' + scheme_id + '_indicator'},
                success: {
                    hide: 'copy_scheme_' + scheme_id + '_popup',
                    update: {element: 'workflow_schemes_list', insertion: true}
                }
            });
        }

        TBG.Config.Workflows.Scheme.remove = function (url, scheme_id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    remove: ['delete_scheme_' + scheme_id + '_popup', 'copy_scheme_' + scheme_id + '_popup', 'workflow_scheme_' + scheme_id],
                    update: {element: 'workflow_schemes_list', insertion: true}
                }
            });
        }

        TBG.Config.Workflows._updateLinks = function (json) {
            if ($('current_workflow_num_count'))
                $('current_workflow_num_count').update(json.total_count);
            $$('.copy_workflow_link').each(function (element) {
                (json.more_available) ? $(element).show() : $(element).hide();
            });
        }

        TBG.Config.Workflows.Workflow.copy = function (url, workflow_id) {
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

        TBG.Config.Workflows.Workflow.remove = function (url, workflow_id) {
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

        TBG.Config.Workflows.Scheme.update = function (url, scheme_id) {
            TBG.Main.Helpers.ajax(url, {
                form: 'workflow_scheme_form',
                loading: {indicator: 'workflow_scheme_indicator'}
            });
        }

        TBG.Config.Workflows.Transition.Validations.add = function (url, mode, key) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: 'workflowtransition' + mode + 'validationrule_add_indicator'},
                success: {
                    hide: ['no_workflowtransition' + mode + 'validationrules', 'add_workflowtransition' + mode + 'validationrule_' + key],
                    update: {element: 'workflowtransition' + mode + 'validationrules_list', insertion: true}
                }
            });
        }

        TBG.Config.Workflows.Transition.Validations.update = function (url, rule_id) {
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

        TBG.Config.Workflows.Transition.Validations.remove = function (url, rule_id, type, mode) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    remove: ['workflowtransitionvalidationrule_' + rule_id],
                    show: ['add_workflowtransition' + type + 'validationrule_' + mode],
                    callback: function () {
                        TBG.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        }

        TBG.Config.Workflows.Transition.Actions.add = function (url, key) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: 'workflowtransitionaction_add_indicator'},
                success: {
                    hide: ['no_workflowtransitionactions', 'add_workflowtransitionaction_' + key],
                    update: {element: 'workflowtransitionactions_list', insertion: true}
                }
            });
        }

        TBG.Config.Workflows.Transition.Actions.update = function (url, action_id) {
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

        TBG.Config.Workflows.Transition.Actions.remove = function (url, action_id, type) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: 'workflowtransitionaction_' + action_id + '_delete_indicator'},
                success: {
                    hide: ['workflowtransitionaction_' + action_id + '_delete', 'workflowtransitionaction_' + action_id],
                    show: ['add_workflowtransitionaction_' + type],
                    callback: function () {
                        TBG.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        }

        /**
         * This function updates available issue reporting fields on page to match
         * those returned by thebuggenie
         */
        TBG.Issues.updateFields = function (url)
        {
            if ($('issuetype_id').getValue() != 0) {
                $('issuetype_list').hide();
            }
            if ($('project_id').getValue() != 0 && $('issuetype_id').getValue() != 0) {
                $('report_more_here').hide();
                $('report_form').show('block');

                TBG.Main.Helpers.ajax(url, {
                    loading: {indicator: 'report_issue_more_options_indicator'},
                    params: 'issuetype_id=' + $('issuetype_id').getValue(),
                    success: {
                        callback: function (json) {
                            TBG.Main.Helpers.MarkitUp($$('textarea.markuppable'));
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
                                            $(fieldname + '_additional').show('block');
                                            $(fieldname + '_div').hide();
                                            if ($(fieldname + '_id_additional')) {
                                                $(fieldname + '_id_additional').enable();
                                            }
                                            if ($(fieldname + '_value_additional')) {
                                                $(fieldname + '_value_additional').enable();
                                            }
                                            if ($(fieldname + '_id')) {
                                                $(fieldname + '_id').disable();
                                            }
                                            if ($(fieldname + '_value')) {
                                                $(fieldname + '_value').disable();
                                            }
                                            if (json.fields[fieldname].values) {
                                                $(fieldname + '_id_additional').update('');
                                                for (var opt in json.fields[fieldname].values) {
                                                    $(fieldname + '_id_additional').insert('<option value="' + opt + '">' + json.fields[fieldname].values[opt] + '</option>');
                                                }
                                                $(fieldname + '_id_additional').setValue(prev_val);
                                            }
                                        } else {
                                            if ($(fieldname + '_div')) {
                                                $(fieldname + '_div').show('block');
                                            }
                                            if ($(fieldname + '_id')) {
                                                $(fieldname + '_id').enable();
                                            }
                                            if ($(fieldname + '_value')) {
                                                $(fieldname + '_value').enable();
                                            }
                                            if ($(fieldname + '_id_additional')) {
                                                $(fieldname + '_id_additional').disable();
                                            }
                                            if ($(fieldname + '_value_additional')) {
                                                $(fieldname + '_value_additional').disable();
                                            }
                                            if ($(fieldname + '_additional')) {
                                                $(fieldname + '_additional').hide();
                                            }
                                            if (json.fields[fieldname].values) {
                                                if ($(fieldname + '_id')) {
                                                    $(fieldname + '_id').update('');
                                                    for (var opt in json.fields[fieldname].values) {
                                                        $(fieldname + '_id').insert('<option value="' + opt + '">' + json.fields[fieldname].values[opt] + '</option>');
                                                    }
                                                    $(fieldname + '_id').setValue(prev_val);
                                                }
                                            }
                                        }
                                        (json.fields[fieldname].required) ? $(fieldname + '_label').addClassName('required') : $(fieldname + '_label').removeClassName('required');
                                    } else {
                                        if ($(fieldname + '_div')) {
                                            $(fieldname + '_div').hide();
                                        }
                                        if ($(fieldname + '_id')) {
                                            $(fieldname + '_id').disable();
                                        }
                                        if ($(fieldname + '_value')) {
                                            $(fieldname + '_value').disable();
                                        }
                                        if ($(fieldname + '_additional')) {
                                            $(fieldname + '_additional').hide();
                                        }
                                        if ($(fieldname + '_id_additional')) {
                                            $(fieldname + '_id_additional').disable();
                                        }
                                        if ($(fieldname + '_value_additional')) {
                                            $(fieldname + '_value_additional').disable();
                                        }
                                    }
                                }
                            });
                            var visible_fields = false;
                            $$('.additional_information').each(function (elm) {
                                if (elm.visible()) {
                                    visible_fields = true;
                                    return;
                                }
                            })
                            if (visible_fields) {
                                $$('.additional_information')[0].up('.reportissue_additional_information_container').show('block');
                            } else {
                                $$('.additional_information')[0].up('.reportissue_additional_information_container').hide();
                            }
                            var visible_extrafields = false;
                            $('reportissue_extrafields').childElements().each(function (elm) {
                                if (elm.visible()) {
                                    visible_extrafields = true;
                                    return;
                                }
                            })
                            if (visible_extrafields) {
                                $('reportissue_extrafields_none').hide();
                            } else {
                                $('reportissue_extrafields_none').show('block');
                            }
                            $('title').focus();
                            $('report_issue_more_options_indicator').hide();
                        }
                    }
                });
            } else {
                $('report_form').hide();
                $('report_more_here').show('block');
                $('issuetype_list').show('block');
            }

        }

        /**
         * Displays the workflow transition popup dialog
         */
        TBG.Issues.showWorkflowTransition = function (transition_id) {
            var existing_container = $('workflow_transition_fullpage').down('.workflow_transition');
            if (existing_container) {
                existing_container.hide();
                $('workflow_transition_container').insert(existing_container);
            }
            var workflow_div = $('issue_transition_container_' + transition_id);
            $('workflow_transition_fullpage').insert(workflow_div);
            $('workflow_transition_fullpage').appear({duration: 0.2});
            workflow_div.appear({duration: 0.2, afterFinish: function () {
                if ($('duplicate_finder_transition_' + transition_id)) {
                    $('viewissue_find_issue_' + transition_id + '_input').observe('keypress', function (event) {
                        if (event.keyCode == Event.KEY_RETURN) {
                            TBG.Issues.findDuplicate($('duplicate_finder_transition_' + transition_id).getValue(), transition_id);
                            event.stop();
                        }
                    });
                }

            }});
        };

        TBG.Issues.submitWorkflowTransition = function (form, callback) {
            TBG.Main.Helpers.ajax(form.action, {
                form: form,
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop', 'workflow_transition_fullpage']
                },
                success: {
                    hide: 'workflow_transition_fullpage',
                    callback: callback
                },
                failure: {
                    show: 'workflow_transition_fullpage'
                }
            });
        };

        TBG.Issues.showLog = function (url) {
            if ($('viewissue_log_items').childElements().size() == 0) {
                TBG.Main.Helpers.ajax(url, {
                    url_method: 'get',
                    loading: {indicator: 'viewissue_log_loading_indicator'},
                    success: {
                        update: {element: 'viewissue_log_items'}
                    }
                });
            }
        }

        TBG.Issues.refreshRelatedIssues = function (url) {
            if ($('related_child_issues_inline')) {
                TBG.Main.Helpers.ajax(url, {
                    loading: {indicator: 'related_issues_indicator'},
                    success: {
                        hide: 'no_child_issues',
                        update: {element: 'related_child_issues_inline'},
                        callback: function () {
                            $('viewissue_related_issues_count').update($('related_child_issues_inline').childElements().size());
                        }
                    }
                });
            }
        };

        TBG.Issues.findRelated = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'viewissue_find_issue_form',
                loading: {indicator: 'find_issue_indicator'},
                success: {update: 'viewissue_relation_results'}
            });
            return false;
        };

        TBG.Issues.findDuplicate = function (url, transition_id) {
            TBG.Main.Helpers.ajax(url, {
                additional_params: 'searchfor=' + $('viewissue_find_issue_' + transition_id + '_input').getValue(),
                loading: {indicator: 'find_issue_' + transition_id + '_indicator'},
                success: {update: 'viewissue_' + transition_id + '_duplicate_results'}
            });
        };

        TBG.Issues.editTimeEntry = function (form) {
            var url = form.action;
            TBG.Main.Helpers.ajax(url, {
                form: form,
                loading: {
                    indicator: 'fullpage_backdrop_indicator',
                    hide: 'fullpage_backdrop_content'
                },
                success: {
                    callback: function (json) {
                        $('fullpage_backdrop_content').update(json.timeentries);
                        $('fullpage_backdrop_content').show();
                        if (json.timesum == 0) {
                            $('no_spent_time_' + json.issue_id).show();
                            $('spent_time_' + json.issue_id + '_name').hide();
                        } else {
                            $('no_spent_time_' + json.issue_id).hide();
                            $('spent_time_' + json.issue_id + '_name').show();
                            $('spent_time_' + json.issue_id + '_value').update(json.spenttime);
                        }
                        TBG.Issues.Field.updateEstimatedPercentbar(json);
                    }
                }
            });
        };

        TBG.Issues.deleteTimeEntry = function (url, entry_id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: 'dialog_indicator'},
                success: {
                    callback: function (json) {
                        TBG.Main.Helpers.Dialog.dismiss();
                        $('issue_spenttime_' + entry_id).remove();
                        if ($('issue_spenttime_' + entry_id + '_comment'))
                            $('issue_spenttime_' + entry_id + '_comment').remove();
                        if (json.timesum == 0) {
                            $('no_spent_time_' + json.issue_id).show();
                            $('spent_time_' + json.issue_id + '_name').hide();
                        } else {
                            $('no_spent_time_' + json.issue_id).hide();
                            $('spent_time_' + json.issue_id + '_name').show();
                            $('spent_time_' + json.issue_id + '_value').update(json.spenttime);
                        }
                        TBG.Issues.Field.updateEstimatedPercentbar(json);
                    }
                }
            });
        };

        TBG.Issues.Field.updateEstimatedPercentbar = function (data) {
            $('estimated_percentbar').update(data.percentbar);
            if ($('no_estimated_time_' + data.issue_id).visible()) {
                $('estimated_percentbar').hide();
            }
            else {
                $('estimated_percentbar').show();
            }
        };

        TBG.Issues.Add = function (url, btn) {
            var btn = btn != undefined ? $(btn) : $('reportissue_button');
            var additional_params_query = '';

            if (btn.dataset != undefined && btn.dataset.milestoneId != undefined && parseInt(btn.dataset.milestoneId) > 0) {
                additional_params_query += '/milestone_id/' + btn.dataset.milestoneId;
            }

            TBG.Main.Helpers.Backdrop.show(url +  additional_params_query);
        };

        TBG.Issues.relate = function (url) {

            TBG.Main.Helpers.ajax(url, {
                form: 'viewissue_relate_issues_form',
                loading: {indicator: 'relate_issues_indicator'},
                success: {
                    update: {element: 'related_child_issues_inline', insertion: true},
                    hide: 'no_child_issues',
                    callback: function (json) {
                        if (jQuery('.milestone_details_link.selected').eq(0).find('> a:first-child').length) {
                            jQuery('.milestone_details_link.selected').eq(0).find('> a:first-child').trigger('click');
                        }
                        else {
                            TBG.Main.Helpers.Backdrop.reset();
                        }
                        if ($('viewissue_related_issues_count')) $('viewissue_related_issues_count').update(json.count);
                        if (json.count > 0 && $('no_related_issues').visible()) $('no_related_issues').hide();
                    }
                }
            });
            return false;
        };

        TBG.Issues.removeRelated = function (url, issue_id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: 'related_issues_indicator'},
                success: {
                    remove: 'related_issue_' + issue_id,
                    callback: function () {
                        var childcount = $('related_child_issues_inline').childElements().size();
                        $('viewissue_related_issues_count').update(childcount);
                        if (childcount == 0) {
                            $('no_related_issues').show();
                        }
                    }
                }
            });
        };

        TBG.Issues.removeDuplicated = function (url, issue_id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: 'duplicate_issues_indicator'},
                success: {
                    remove: 'duplicated_issue_' + issue_id,
                    callback: function () {
                        var childcount = $('related_duplicate_issues_inline').childElements().size();
                        $('viewissue_duplicate_issues_count').update(childcount);
                        if (childcount == 0) {
                            $('no_duplicated_issues').show();
                        }
                    }
                }
            });
        };

        TBG.Issues.move = function (form, issue_id) {
            TBG.Main.Helpers.ajax(form.action, {
                form: form,
                loading: {
                    indicator: 'move_issue_indicator'
                },
                success: {
                    remove: 'issue_' + issue_id,
                    update: 'viewissue_move_issue_div'
                }
            });
        };

        TBG.Issues._addVote = function (url, direction) {
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

        TBG.Issues.voteUp = function (url) {
            TBG.Issues._addVote(url, 'up');
        };

        TBG.Issues.voteDown = function (url) {
            TBG.Issues._addVote(url, 'down');
        };

        TBG.Issues.toggleFavourite = function (url, issue_id_user_id)
        {
            var issue_id = new String(issue_id_user_id).indexOf('_') !== -1
                ? issue_id_user_id.substr(0, issue_id_user_id.indexOf('_'))
                : issue_id_user_id;
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    callback: function () {
                        if ($('popup_find_subscriber_' + issue_id) != null && $('popup_find_subscriber_' + issue_id).visible() && $('popup_find_subscriber_' + issue_id + '_spinning')) {
                            $('popup_find_subscriber_' + issue_id + '_spinning').show();
                        }
                        else {
                            TBG.Core._processCommonAjaxPostEvents({
                                show: 'issue_favourite_indicator_' + issue_id_user_id,
                                hide: ['issue_favourite_normal_' + issue_id_user_id, 'issue_favourite_faded_' + issue_id_user_id]
                            });
                        }
                    }
                },
                success: {
                    hide: 'popup_find_subscriber_' + issue_id,
                    callback: function (json) {
                        if ($('popup_find_subscriber_' + issue_id + '_spinning')) {
                            $('popup_find_subscriber_' + issue_id + '_spinning').hide();
                        }
                        else {
                            TBG.Core._processCommonAjaxPostEvents({
                                hide: 'issue_favourite_indicator_' + issue_id_user_id,
                            });
                        }
                        if ($('issue_favourite_faded_' + issue_id_user_id)) {
                            if (json.starred) {
                                $('issue_favourite_faded_' + issue_id_user_id).hide();
                                $('issue_favourite_indicator_' + issue_id_user_id).hide();
                                $('issue_favourite_normal_' + issue_id_user_id).show();
                            } else {
                                $('issue_favourite_normal_' + issue_id_user_id).hide();
                                $('issue_favourite_indicator_' + issue_id_user_id).hide();
                                $('issue_favourite_faded_' + issue_id_user_id).show();
                            }
                        } else if (json.subscriber != '') {
                            $('subscribers_list').insert(json.subscriber);
                        }
                        if (json.count != undefined && $('subscribers_field_count')) {
                            $('subscribers_field_count').update(json.count);
                        }
                    }
                }
            });
        }

        TBG.Issues.toggleBlocking = function (url, issue_id)
        {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: 'fullpage_backdrop_content'
                },
                success: {
                    callback: function (json) {
                        $('more_actions_mark_notblocking_link_' + issue_id).toggle();
                        $('more_actions_mark_blocking_link_' + issue_id).toggle();

                        if ($('blocking_div')) {
                            $('blocking_div').toggle();
                        }
                        if ($('issue_' + issue_id)) {
                            $('issue_' + issue_id).toggleClassName('blocking');
                        }
                    }
                }
            });
        }

        TBG.Issues.Link.add = function (url) {
            TBG.Main.Helpers.ajax(url, {
                form: 'attach_link_form',
                loading: {
                    indicator: 'attach_link_indicator',
                    callback: function () {
                        $('attach_link_submit').disable();
                    }
                },
                success: {
                    reset: 'attach_link_form',
                    hide: ['attach_link', 'viewissue_no_uploaded_files'],
                    update: {element: 'viewissue_uploaded_links', insertion: true},
                    callback: function (json) {
                        if ($('viewissue_uploaded_attachments_count'))
                            $('viewissue_uploaded_attachments_count').update(json.attachmentcount);
                        TBG.Main.Helpers.Backdrop.reset();
                    }
                },
                complete: {
                    callback: function () {
                        $('attach_link_submit').enable();
                    }
                }
            });
        }

        TBG.Issues.Link.remove = function (url, link_id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'viewissue_links_' + link_id + '_remove_indicator',
                    hide: link_id + '_remove_link',
                    callback: TBG.Main.Helpers.Dialog.dismiss
                },
                success: {
                    remove: ['viewissue_links_' + link_id, 'viewissue_links_' + link_id + '_remove_confirm'],
                    callback: function (json) {
                        if (json.attachmentcount == 0 && $('viewissue_no_uploaded_files')) $('viewissue_no_uploaded_files').show();
                        if ($('viewissue_uploaded_attachments_count')) $('viewissue_uploaded_attachments_count').update(json.attachmentcount);
                    }
                },
                failure: {
                    show: link_id + '_remove_link'
                }
            });
        }

        TBG.Issues.File.remove = function (url, file_id) {
            TBG.Core._detachFile(url, file_id, 'viewissue_files_', 'dialog_indicator');
        }

        TBG.Issues.Field.setPercent = function (url, mode) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: 'percent_complete_spinning'},
                success: {
                    callback: function (json) {
                        TBG.Main.updatePercentageLayout(json.percent);
                        (mode == 'set') ? TBG.Issues.markAsChanged('percent_complete') : TBG.Issues.markAsUnchanged('percent_complete');
                    },
                    hide: 'percent_complete_change'
                }
            });
        }

        TBG.Issues.Field.Updaters.dualFromJSON = function (issue_id, dualfield, field) {
            if (dualfield.id == 0) {
                $(field + '_table').hide();
                $('no_' + field).show();
            } else {
                $(field + '_content').update(dualfield.name);
                if (field == 'status')
                    $('status_' + issue_id + '_color').setStyle({backgroundColor: dualfield.color});
                else if (field == 'issuetype')
                    $('issuetype_image').src = dualfield.src;
                if ($('no_' + field))
                    $('no_' + field).hide();
                if ($(field + '_table'))
                    $(field + '_table').show();
            }
        }

        TBG.Issues.Field.Updaters.fromObject = function (issue_id, object, field) {
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
                if (object.url)
                    $(fn).href = object.url;
                $(nf).hide();
                $(fn).show();
            }
        }

        TBG.Issues.Field.Updaters.timeFromObject = function (issue_id, object, values, field) {
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
            ['points', 'hours', 'days', 'weeks', 'months'].each(function (unit) {
                if (field != 'spent_time' && $(field + '_' + issue_id + '_' + unit + '_input'))
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

        TBG.Issues.Field.Updaters.allVisible = function (visible_fields) {
            TBG.available_fields.each(function (field)
            {
                if ($(field + '_field')) {
                    if (visible_fields[field] != undefined) {
                        $(field + '_field').show();
                        if ($(field + '_additional'))
                            $(field + '_additional').show();
                    } else {
                        $(field + '_field').hide();
                        if ($(field + '_additional'))
                            $(field + '_additional').hide();
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
        TBG.Issues.Field.set = function (url, field, serialize_form) {
            var post_form = undefined;
            if (['description', 'reproduction_steps', 'title', 'shortname'].indexOf(field) != -1) {
                post_form = field + '_form';
            } else if (serialize_form != undefined) {
                post_form = serialize_form + '_form';
            }

            var loading_show = (field == 'issuetype') ? 'issuetype_indicator_fullpage' : undefined;

            TBG.Main.Helpers.ajax(url, {
                form: post_form,
                loading: {
                    indicator: loading_show != undefined ? loading_show : field + '_spinning',
                    clear: field + '_change_error',
                    hide: field + '_change_error'
                },
                success: {
                    callback: function (json) {
                        if (json.field != undefined)
                        {
                            if (field == 'status' || field == 'issuetype')
                                TBG.Issues.Field.Updaters.dualFromJSON(json.issue_id, json.field, field);
                            else if (field == 'percent_complete')
                                TBG.Main.updatePercentageLayout(json.percent);
                            else if (field == 'estimated_time') {
                                TBG.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
                                $(field + '_' + json.issue_id + '_change').hide();
                                TBG.Issues.Field.updateEstimatedPercentbar(json);
                            }
                            else if (field == 'spent_time') {
                                TBG.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
                                $(field + '_' + json.issue_id + '_change').hide();
                            }
                            else
                                TBG.Issues.Field.Updaters.fromObject(json.issue_id, json.field, field);

                            if (field == 'issuetype')
                                TBG.Issues.Field.Updaters.allVisible(json.visible_fields);
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
                        if (field == 'description' && $('description_edit')) {
                            $('description_edit').style.display = '';
                        }
                        else if (field == 'title') {
                            $('title_field').toggleClassName('editing');
                        }
                    },
                    hide: field + '_change'
                },
                failure: {
                    update: field + '_change_error',
                    show: field + '_change_error',
                    callback: function (json) {
                        new Effect.Pulsate($(field + '_change_error'));
                    }
                }
            });
        }

        TBG.Issues.Field.setTime = function (url, field, issue_id) {
            TBG.Main.Helpers.ajax(url, {
                form: field + '_' + issue_id + '_form',
                loading: {
                    indicator: field + '_' + issue_id + '_spinning',
                    clear: field + '_' + issue_id + '_change_error',
                    hide: field + '_' + issue_id + '_change_error'
                },
                success: {
                    callback: function (json) {
                        TBG.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
                        (json.changed == true) ? TBG.Issues.markAsChanged(field) : TBG.Issues.markAsUnchanged(field);
                        if ($('issue_' + issue_id)) {
                            ['points', 'hours'].each(function (unit) {
                                if (field == 'estimated_time') {
                                    TBG.Issues.Field.updateEstimatedPercentbar(json);
                                    $('issue_' + issue_id).setAttribute('data-estimated-' + unit, json.values[unit]);
                                    $('issue_' + issue_id).down('.issue_estimate.' + unit).update(json.values[unit]);
                                    (parseInt(json.values[unit]) > 0) ? $('issue_' + issue_id).down('.issue_estimate.' + unit).show() : $('issue_' + issue_id).down('.issue_estimate.' + unit).hide();
                                } else {
                                    $('issue_' + issue_id).setAttribute('data-spent-' + unit, json.values[unit]);
                                    $('issue_' + issue_id).down('.issue_spent.' + unit).update(json.values[unit]);
                                    (parseInt(json.values[unit]) > 0) ? $('issue_' + issue_id).down('.issue_spent.' + unit).show() : $('issue_' + issue_id).down('.issue_spent.' + unit).hide();
                                }
                                $('issue_' + issue_id).dataset.lastUpdated = get_current_timestamp();
                            });
                            var fields = $('issue_' + issue_id).select('.sc_' + field);
                            if (fields.size() > 0) {
                                fields.each(function (sc_element) {
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
                        if ($('milestone_list')) {
                            TBG.Project.Planning.calculateMilestoneIssueVisibilityDetails($('issue_' + issue_id).up('.milestone_issues'));
                            TBG.Project.Planning.calculateNewBacklogMilestoneDetails();
                        }
                    },
                    hide: field + '_' + issue_id + '_change'
                },
                failure: {
                    update: field + '_' + issue_id + '_change_error',
                    show: field + '_' + issue_id + '_change_error',
                    callback: function (json) {
                        new Effect.Pulsate($(field + '_' + issue_id + '_change_error'));
                    }
                }
            });
        }

        TBG.Issues.Field.revert = function (url, field)
        {
            var loading_show = (field == 'issuetype') ? 'issuetype_indicator_fullpage' : undefined;

            TBG.Issues.markAsUnchanged(field);

            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: loading_show != undefined ? loading_show : field + '_undo_spinning'
                },
                success: {
                    callback: function (json) {
                        if (json.field != undefined) {
                            if (field == 'status' || field == 'issuetype')
                                TBG.Issues.Field.Updaters.dualFromJSON(json.issue_id, json.field, field);
                            else if (field == 'estimated_time') {
                                TBG.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
                                TBG.Issues.Field.updateEstimatedPercentbar(json);
                            }
                            else if (field == 'spent_time')
                                TBG.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
                            else if (field == 'percent_complete')
                                TBG.Main.updatePercentageLayout(json.field);
                            else
                                TBG.Issues.Field.Updaters.fromObject(json.issue_id, json.field, field);

                            if (field == 'issuetype')
                                TBG.Issues.Field.Updaters.allVisible(json.visible_fields);
                            else if (field == 'description' || field == 'reproduction_steps')
                                $(field + '_form_value').update(json.field.form_value);
                            else if (field == 'pain_bug_type' || field == 'pain_likelihood' || field == 'pain_effect')
                                $('issue_user_pain').update(json.field.user_pain);

                            if (field == 'description') {
                                $('description_edit').style.display = '';
                                $('description_change').hide();
                            }
                        }

                    }
                },
                failure: {
                    callback: function () {
                        TBG.Issues.markAsChanged(field);
                    }
                }
            });
        }

        TBG.Issues.markAsChanged = function (field)
        {
            if ($('viewissue_changed') != undefined) {
                if (!$('viewissue_changed').visible()) {
                    $('viewissue_changed').show();
                    Effect.Pulsate($('issue_info_container'), {pulses: 3, duration: 2});
                }

                $(field + '_field').addClassName('issue_detail_changed');
            }

            if ($('comment_save_changes'))
                $('comment_save_changes').checked = true;
        }

        TBG.Issues.markAsUnchanged = function (field)
        {
            if ($(field + '_field') && $('issue_view')) {
                $(field + '_field').removeClassName('issue_detail_changed');
                $(field + '_field').removeClassName('issue_detail_unmerged');
                if ($('issue_view').select('.issue_detail_changed').size() == 0) {
                    $('viewissue_changed').hide();
                    $('viewissue_merge_errors').hide();
                    $('viewissue_unsaved').hide();
                    if ($('comment_save_changes'))
                        $('comment_save_changes').checked = false;
                }
            }
        }

        TBG.Issues.ACL.toggle_checkboxes = function (element, issue_id) {
            var val = element.getValue();
            var opp_val = (val == 'restricted') ? 'public' : 'restricted';
            if ($(element).checked) {
                $('acl_' + issue_id + '_' + val).show();
                $('acl_' + issue_id + '_' + opp_val).hide();
            } else {
                $('acl_' + issue_id + '_' + val).hide();
                $('acl_' + issue_id + '_' + opp_val).show();
            }
        };

        TBG.Issues.ACL.addTarget = function (url, issue_id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'popup_find_acl_' + issue_id + '_spinning'
                },
                success: {
                    update: {element: 'issue_' + issue_id + '_access_list', insertion: true},
                    hide: ['popup_find_acl_' + issue_id, 'issue_' + issue_id + '_access_list_none']
                }
            });
        };

        TBG.Issues.ACL.set = function (url, issue_id, mode) {
            TBG.Main.Helpers.ajax(url, {
                form: 'acl_' + issue_id + '_' + mode + 'form',
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    callback: TBG.Main.Helpers.Backdrop.reset
                }
            });
        };

        TBG.Issues.Affected.toggleConfirmed = function (url, affected)
        {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    callback: function () {
                        $('affected_' + affected + '_state').up('.affected_state').addClassName('loading');
                    }
                },
                success: {
                    callback: function (json) {
                        $('affected_' + affected + '_state').update(json.text);
                        $('affected_' + affected + '_state').up('.affected_state').toggleClassName('unconfirmed');
                        $('affected_' + affected + '_state').up('.affected_state').toggleClassName('confirmed');
                        $('affected_' + affected + '_state').up('.affected_state').removeClassName('loading');
                    }
                },
                complete: {
                    callback: function () {
                        $('affected_' + affected + '_state').up('.affected_state').removeClassName('loading');
                    }
                }
            });
        }

        TBG.Issues.Affected.remove = function (url, affected)
        {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    update: {element: 'viewissue_affects_count', from: 'itemcount'},
                    remove: ['affected_' + affected + '_delete', 'affected_' + affected],
                    callback: function (json) {
                        if (json.itemcount == 0)
                            $('no_affected').show();
                    }
                }
            });
        }

        TBG.Issues.Affected.setStatus = function (url, affected)
        {
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'affected_' + affected + '_status_spinning'
                },
                success: {
                    callback: function (json) {
                        $('affected_' + affected + '_status').setStyle({
                            backgroundColor: json.colour,
                        });
                    },
                    update: {element: 'affected_' + affected + '_status', from: 'name'},
                    hide: 'affected_' + affected + '_status_change'
                },
                failure: {
                    update: {element: 'affected_' + affected + '_status_error', from: 'error'},
                    show: 'affected_' + affected + '_status_error',
                    callback: function (json) {
                        new Effect.Pulsate($('affected_' + affected + '_status_error'));
                    }
                }
            });
        }

        TBG.Issues.Affected.add = function (url)
        {
            TBG.Main.Helpers.ajax(url, {
                form: 'viewissue_add_item_form',
                loading: {
                    indicator: 'add_affected_spinning'
                },
                success: {
                    callback: function (json) {
                        if ($('viewissue_affects_count'))
                            $('viewissue_affects_count').update(json.itemcount);
                        if (json.itemcount != 0 && $('no_affected'))
                            $('no_affected').hide();
                        TBG.Main.Helpers.Backdrop.reset();
                    },
                    update: {element: 'affected_list', insertion: true},
                }
            });
        }

        TBG.Issues.updateWorkflowAssignee = function (url, assignee_id, assignee_type, transition_id, teamup)
        {
            teamup = (teamup == undefined) ? 0 : 1;
            TBG.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'popup_assigned_to_name_indicator_' + transition_id,
                    hide: 'popup_no_assigned_to_' + transition_id,
                    show: 'popup_assigned_to_name_' + transition_id
                },
                success: {
                    update: 'popup_assigned_to_name_' + transition_id
                },
                complete: {
                    callback: function () {
                        $('popup_assigned_to_id_' + transition_id).setValue(assignee_id);
                        $('popup_assigned_to_type_' + transition_id).setValue(assignee_type);
                        $('popup_assigned_to_teamup_' + transition_id).setValue(teamup);
                        if (teamup) {
                            $('popup_assigned_to_teamup_info_' + transition_id).show();
                        } else {
                            $('popup_assigned_to_teamup_info_' + transition_id).hide();
                        }
                    },
                    hide: ['popup_assigned_to_teamup_info_' + transition_id, 'popup_assigned_to_change_' + transition_id]
                }
            });
        }

        TBG.Issues.updateWorkflowAssigneeTeamup = function (url, assignee_id, assignee_type, transition_id)
        {
            TBG.Issues.updateWorkflowAssignee(url, assignee_id, assignee_type, transition_id, true);
        }

        TBG.Search.deleteSavedSearch = function (url, id) {
            TBG.Main.Helpers.ajax(url, {
                loading: {indicator: 'delete_search_' + id + '_indicator'},
                success: {hide: 'saved_search_' + id + '_container'}
            });
        };

        TBG.Search.toPage = function (url, parameters, offset) {
            parameters += '&offset=' + offset;
            TBG.Main.Helpers.ajax(url, {
                params: parameters,
                loading: {indicator: 'paging_spinning'},
                success: {update: 'search_results'}
            });
        };

        TBG.Search.toggleColumn = function (column) {
            $$('.sc_' + column).each(function (element) {
                element.toggle();
            });
        };

        TBG.Search.resetColumns = function () {
            TBG.Search.ResultViews[TBG.Search.current_result_view].visible.each(function (column) {
                if (TBG.Search.ResultViews[TBG.Search.current_result_view].default_visible.indexOf(column) != -1) {
                    TBG.Search.setFilterValue($('search_column_' + column + '_toggler'), true);
                    $$('.sc_' + column).each(Element.show);
                } else {
                    TBG.Search.setFilterValue($('search_column_' + column + '_toggler'), false);
                    $$('.sc_' + column).each(Element.hide);
                }
            });
            TBG.Search.saveColumnVisibility();
        };

        TBG.Search.setColumns = function (resultview, available_columns, visible_columns, default_columns) {
            TBG.Search.current_result_view = resultview;
            TBG.Search.ResultViews[resultview] = {
                available: available_columns,
                visible: visible_columns,
                default_visible: default_columns
            };
            TBG.Search.ResultViews[resultview].available.each(function (column) {
                if (TBG.Search.ResultViews[resultview].visible.indexOf(column) != -1) {
                    TBG.Search.setFilterValue($('search_column_' + column + '_toggler'), true);
                } else {
                    TBG.Search.setFilterValue($('search_column_' + column + '_toggler'), false);
                }
            });
            $('scs_current_template').setValue(resultview);
        };

        TBG.Search.checkToggledCheckboxes = function () {
            var num_checked = 0;
            $('search_results').select('input[type=checkbox]').each(function (elm) {
                if (elm.checked)
                    num_checked++;
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

        TBG.Search.toggleCheckboxes = function () {
            var do_check = true;

            if ($(this).hasClassName('semi-checked')) {
                $(this).removeClassName('semi-checked');
                $(this).checked = true;
                do_check = true;
            } else {
                do_check = $(this).checked;
            }

            $(this).up('table').down('tbody').select('input[type=checkbox]').each(function (element) {
                element.checked = do_check;
            });

            TBG.Search.checkToggledCheckboxes();
        };

        TBG.Search.toggleCheckbox = function () {
            var num_unchecked = 0;
            var num_checked = 0;
            this.up('tbody').select('input[type=checkbox]').each(function (elm) {
                if (!elm.checked)
                    num_unchecked++;
                if (elm.checked)
                    num_checked++;
            });

            var chk_box = this.up('table').down('thead').down('input[type=checkbox]');
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

        TBG.Search.bulkContainerChanger = function (mode) {
            var sub_container_id = 'bulk_action_subcontainer_' + $('bulk_action_selector_' + mode).getValue();
            $('search_results').select('.bulk_action_subcontainer').each(function (element) {
                element.hide();
            });
            if ($(sub_container_id + '_top')) {
                $(sub_container_id + '_top').show();
                $('bulk_action_submit_top').removeClassName('disabled');
                $(sub_container_id + '_bottom').show();
                $('bulk_action_submit_bottom').removeClassName('disabled');
                var dropdown_element = $(sub_container_id + '_' + mode).down('.focusable');
                if (dropdown_element != undefined)
                    dropdown_element.focus();
            } else {
                $('bulk_action_submit_top').addClassName('disabled');
                $('bulk_action_submit_bottom').addClassName('disabled');
            }
        };

        TBG.Search.bulkChanger = function (mode) {
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

        TBG.Search.bulkPostProcess = function (json) {
            if (json.last_updated) {
                if (json.milestone_name != undefined && json.milestone_id) {
                    if ($('milestone_list') != undefined) {
                        if ($('milestone_' + json.milestone_id) == undefined) {
                            TBG.Project.Milestone.retrieve(json.milestone_url, json.milestone_id, json.issue_ids);
                        }
                    }
                    if ($('bulk_action_assign_milestone_top') != undefined && $('bulk_action_assign_milestone_top_' + json.milestone_id) == undefined) {
                        $('bulk_action_assign_milestone_top').insert('<option value="' + json.milestone_id + '" id="bulk_action_assign_milestone_top_' + json.milestone_id + '">' + json.milestone_name + '</option>');
                        $('bulk_action_assign_milestone_top').setValue(json.milestone_id);
                        $('bulk_action_assign_milestone_top_name').hide();
                    }
                    if ($('bulk_action_assign_milestone_bottom') != undefined && $('bulk_action_assign_milestone_bottom_' + json.milestone_id) == undefined) {
                        $('bulk_action_assign_milestone_bottom').insert('<option value="' + json.milestone_id + '" id="bulk_action_assign_milestone_bottom_' + json.milestone_id + '">' + json.milestone_name + '</option>');
                        $('bulk_action_assign_milestone_bottom').setValue(json.milestone_id);
                        $('bulk_action_assign_milestone_bottom_name').hide();
                    }
                }
                json.issue_ids.each(function (issue_id) {
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
                                if (status_color_item)
                                    status_color_item.setStyle({backgroundColor: json.status['color']});
                            }
                        }
                        ['resolution', 'priority', 'category', 'severity'].each(function (action) {
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
                TBG.Search.liveUpdate(true);
            }
        }

        TBG.Search.interactiveWorkflowTransition = function (url, transition_id, form) {
            TBG.Main.Helpers.ajax(url, {
                form: form,
                loading: {
                    indicator: 'transition_working_' + transition_id + '_indicator',
                    callback: function () {
                        $$('.workflow_transition_submit_button').each(function (element) {
                            $(element).addClassName('disabled');
                        });
                    }
                },
                success: {
                    callback: function (json) {
                        TBG.Core.Pollers.Callbacks.planningPoller();
                        TBG.Main.Helpers.Backdrop.reset();
                        TBG.Search.liveUpdate(true);
                    }
                },
                complete: {
                    callback: function () {
                        $$('.workflow_transition_submit_button').each(function (element) {
                            $(element).removeClassName('disabled');
                        });
                    }
                }
            });
        }

        TBG.Search.bulkWorkflowTransition = function (url, transition_id) {
            TBG.Main.Helpers.ajax(url, {
                form: 'bulk_workflow_transition_form',
                loading: {
                    indicator: 'transition_working_' + transition_id + '_indicator',
                    callback: function () {
                        $$('.workflow_transition_submit_button').each(function (element) {
                            $(element).addClassName('disabled');
                        });
                    }
                },
                success: {
                    callback: function (json) {
                        TBG.Search.bulkPostProcess(json)
                        TBG.Main.Helpers.Backdrop.reset();
                    }
                },
                complete: {
                    callback: function () {
                        $$('.workflow_transition_submit_button').each(function (element) {
                            $(element).removeClassName('disabled');
                        });
                    }
                }
            });
        };

        TBG.Search.bulkUpdate = function (url, mode) {
            if ($('bulk_action_selector_' + mode).getValue() == '')
                return;
            var issues = '';
            $('search_results').select('tbody input[type=checkbox]').each(function (element) {
                if (element.checked)
                    issues += '&issue_ids[' + element.getValue() + ']=' + element.getValue();
            });

            if ($('bulk_action_selector_' + mode).getValue() == 'perform_workflow_step') {
                TBG.Main.Helpers.Backdrop.show($('bulk_action_subcontainer_perform_workflow_step_' + mode + '_url').getValue() + issues);
            } else {
                TBG.Main.Helpers.ajax(url, {
                    form: 'bulk_action_form_' + mode,
                    additional_params: issues,
                    loading: {
                        indicator: 'fullpage_backdrop',
                        show: 'fullpage_backdrop_indicator',
                        hide: 'fullpage_backdrop_content'
                    },
                    success: {
                        callback: TBG.Search.bulkPostProcess
                    }
                });
            }
        };

        TBG.Search.moveDown = function (event) {
            var selected_elements = $('search_results').select('tr.selected');
            var old_selected_element = (selected_elements.size() == 0) ? undefined : selected_elements[0];
            var new_selected_element = (old_selected_element == undefined) ? $('search_results').select('table tbody tr')[0] : old_selected_element.next();

            TBG.Search.move(old_selected_element, new_selected_element, event, true);
        };

        TBG.Search.moveUp = function (event) {
            var selected_elements = $('search_results').select('tr.selected');
            var old_selected_element = (selected_elements.size() == 0) ? undefined : selected_elements[selected_elements.size() - 1];
            var new_selected_element = (old_selected_element == undefined) ? $('search_results').select('table tbody tr')[0] : old_selected_element.previous();

            TBG.Search.move(old_selected_element, new_selected_element, event, true);
        };

        TBG.Search.move = function (old_selected_element, new_selected_element, event, move) {
            if (old_selected_element && new_selected_element) {
                $(old_selected_element).removeClassName('selected');
            }
            if (new_selected_element) {
                var ns = $(new_selected_element);
                ns.addClassName('selected');
                var offsets = ns.cumulativeOffset();
                var dimensions = ($('bulk_action_form_top')) ? $('bulk_action_form_top').getDimensions() : ns.getDimensions();
                if (event)
                    event.preventDefault();
                if (move) {
                    var top = document.viewport.getScrollOffsets().top;
                    var v_height = document.viewport.getDimensions().height;
                    var bottom = top + v_height;
                    var is_above = top > offsets.top - dimensions.height;
                    var is_below = bottom < offsets.top + dimensions.height;
                    if (is_above || is_below) {
                        if (is_above)
                            window.scrollTo(0, offsets.top - dimensions.height);
                        if (is_below)
                            window.scrollTo(0, offsets.top + dimensions.height - v_height);
                    }
                }
            }
        }

        TBG.Search.moveTo = function (event) {
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

        TBG.Search.getFilterValues = function (element) {
            var filter = element.up('.filter');
            var results_container = filter.down('.filter_callback_results');
            var existing_container = filter.down('.filter_existing_values');
            var url = element.dataset.callbackUrl;
            var value = element.getValue();
            results_container.childElements().each(function (existing_element) {
                if (existing_element.hasClassName('selected')) {
                    existing_container.insert(existing_element.remove());
                }
            });
            if (value == '') {
                results_container.update('');
                TBG.Search.filterFilterOptionsElement(element);
            } else {
                var parameters = '&filter=' + value;
                filter.down('.filter_existing_values').select('input[type=checkbox]').each(function (checkbox) {
                    parameters += '&existing_id[' + checkbox.value + ']=1';
                });
                TBG.Main.Helpers.ajax(url, {
                    params: parameters,
                    loading: {
                        callback: function () {
                            TBG.Search.filterFilterOptionsElement(element);
                            element.addClassName('filtering');
                        }
                    },
                    success: {
                        callback: function (json) {
                            results_container.update(json.results);
                            window.setTimeout(function () {
                                results_container.select('li.filtervalue').each(function (filtervalue) {
                                    filtervalue.on('click', TBG.Search.toggleFilterValue);
                                });
                            }, 250);
                            element.removeClassName('filtering');
                        }
                    }
                });
            }
        };

        TBG.Search.initializeFilterSearchValues = function (filter) {
            var si = filter.down('input[type=search]');
            if (si != undefined)
            {
                si.dataset.previousValue = '';
                if (si.dataset.callbackUrl !== undefined) {
                    var fk = filter.dataset.filterKey;
                    si.on('keyup', function (event, element) {
                        if (TBG.ift_observers[fk])
                            clearTimeout(TBG.ift_observers[fk]);
                        if ((si.getValue().length >= 3 || si.getValue().length == 0) && si.getValue() != si.dataset.lastValue) {
                            TBG.ift_observers[fk] = setTimeout(function () {
                                TBG.Search.getFilterValues(si);
                                si.dataset.lastValue = si.getValue();
                            }, 1000);
                        }
                    });
                } else {
                    si.on('keyup', TBG.Search.filterFilterOptions);
                }
                si.on('click', function (event, element) {
                    event.stopPropagation();
                    event.preventDefault();
                });
                filter.addClassName('searchable');
            }
        };

        TBG.Search.initializeFilterField = function (filter) {
            filter.on('click', TBG.Search.toggleInteractiveFilter);
            filter.select('li.filtervalue').each(function (filtervalue) {
                filtervalue.on('click', TBG.Search.toggleFilterValue);
            });
            TBG.Search.initializeFilterSearchValues(filter);
            TBG.Search.initializeFilterNavigation(filter);
            TBG.Search.calculateFilterDetails(filter);
        };

        TBG.Search.initializeFilterNavigation = function (filter) {
            Event.observe(filter, 'keydown', function (event) {
                if (Event.KEY_DOWN == event.keyCode) {
                    TBG.Search.moveFilterDown(event, filter);
                    event.stopPropagation();
                    event.preventDefault();
                }
                else if (Event.KEY_UP == event.keyCode) {
                    TBG.Search.moveFilterUp(event, filter);
                    event.stopPropagation();
                    event.preventDefault();
                }
                else if (Event.KEY_RETURN == event.keyCode) {
                    var selected_elements = filter.select('li.highlighted');
                    var current_selected_element = (selected_elements.size() == 0) ? undefined : selected_elements[0];
                    if (current_selected_element != undefined) {
                        TBG.Search.toggleFilterValueElement(current_selected_element);
                    }
                }
                else if (Event.KEY_ESC == event.keyCode) {
                    TBG.Search.toggleInteractiveFilterElement(filter);
                }
            });
            filter.select('.filtervalue').each(function (elm) {
                if (!elm.hasClassName('separator'))
                    elm.addClassName('unfiltered');
            });
        };

        TBG.Search.filterFilterOptions = function (event, element) {
            event.stopPropagation();
            TBG.Search.filterFilterOptionsElement(element);
        };

        TBG.Search.filterFilterOptionsElement = function (element) {
            var filtervalue = element.getValue();
            if (filtervalue !== element.dataset.previousValue) {
                if (filtervalue !== '')
                    element.up().addClassName('filtered');
                else
                    element.up().removeClassName('filtered');

                element.up().select('.filtervalue').each(function (elm) {
                    if (elm.hasClassName('sticky'))
                        return;
                    if (filtervalue !== '') {
                        if (elm.innerHTML.toLowerCase().indexOf(filtervalue.toLowerCase()) !== -1 || elm.hasClassName('selected')) {
                            elm.addClassName('unfiltered');
                        } else {
                            elm.removeClassName('unfiltered');
                        }
                    } else {
                        elm.addClassName('unfiltered');
                    }
                    elm.removeClassName('highlighted');
                });
                element.dataset.previousValue = filtervalue;
            }
        };

        TBG.Search.moveFilterDown = function (event, filter) {
            var available_elements = filter.select('.filtervalue.unfiltered');
            var selected_elements = filter.select('li.highlighted');
            var old_selected_element = (selected_elements.size() == 0) ? undefined : selected_elements[0];
            var new_selected_element = (old_selected_element == undefined) ? available_elements[0] : old_selected_element.next('.filtervalue');
            if (new_selected_element === undefined && available_elements.size() > 1)
                new_selected_element = available_elements[0];

            TBG.Search.moveFilter(old_selected_element, new_selected_element, event);
        };

        TBG.Search.moveFilterUp = function (event, filter) {
            var available_elements = filter.select('.filtervalue.unfiltered');
            var selected_elements = filter.select('li.highlighted');
            var old_selected_element = (selected_elements.size() == 0) ? undefined : selected_elements[0];
            var new_selected_element = (old_selected_element == undefined) ? available_elements[0] : old_selected_element.previous('.filtervalue');
            if (new_selected_element === undefined && available_elements.size() > 1)
                new_selected_element = available_elements.last();

            TBG.Search.moveFilter(old_selected_element, new_selected_element, event);
        };

        TBG.Search.moveFilter = function (old_selected_element, new_selected_element, event) {
            if (old_selected_element && new_selected_element) {
                $(old_selected_element).removeClassName('highlighted');
            }
            if (new_selected_element) {
                var ns = $(new_selected_element);
                ns.addClassName('highlighted');
                if (event)
                    event.preventDefault();
            }
        };

        TBG.Search.addFilter = function (event, element) {
            if (!this.hasClassName('disabled')) {
                var filter = this.dataset.filter;
                $('searchbuilder_filterstrip_filtercontainer').insert($('interactive_filter_' + filter).remove());
                setTimeout(function () {
                    TBG.Search.toggleInteractiveFilterElement($('interactive_filter_' + filter));
                }, 250);
                this.addClassName('disabled');
            }
        };

        TBG.Search.removeFilter = function (element) {
            var do_update = ($('filter_' + element.dataset.filterkey + '_value_input').getValue() != '');
            $('additional_filter_' + element.dataset.filterkey + '_link').removeClassName('disabled');
            element.select('.filtervalue').each(function (elm) {

            });
            $('searchbuilder_filter_hiddencontainer').insert(element.remove());

            if (do_update)
                TBG.Search.liveUpdate();
        };

        TBG.Search.saveColumnVisibility = function () {
            var fif = $('find_issues_form');
            if (fif.dataset.isSaved === undefined) {
                var scc = $('search_columns_container');
                var parameters = fif.serialize();
                TBG.Main.Helpers.ajax(scc.dataset.url, {
                    params: parameters,
                    loading: {indicator: 'search_column_settings_indicator'},
                    success: {hide: 'search_column_settings_indicator'}
                });
            }
        };

        TBG.Search.updateColumnVisibility = function (event, element) {
            event.preventDefault();
            event.stopPropagation();
            if (element.down('input').checked) {
                TBG.Search.setFilterValue(element, false);
            } else {
                TBG.Search.setFilterValue(element, true);
            }
            TBG.Search.toggleColumn(element.dataset.value);
            TBG.Search.saveColumnVisibility();
        };

        TBG.Search.initializeFilters = function () {
            var fif = $('find_issues_form');
            fif.reset();
            $$('.filter').each(function (filter) {
                TBG.Search.initializeFilterField(filter);
            });
            ['interactive_plus_button', 'interactive_template_button', 'interactive_grouping_button', 'interactive_save_button'].each(function (element) {
                if ($(element))
                    $(element).on('click', TBG.Search.toggleInteractiveFilter);
            });
            TBG.Search.initializeFilterSearchValues($('search_column_settings_container'));
            TBG.Search.initializeFilterSearchValues($('search_grouping_container'));
            $('search_columns_container').select('li').each(function (element) {
                element.on('click', TBG.Search.updateColumnVisibility);
            });
            $('search_grouping_container').select('li').each(function (element) {
                element.on('click', TBG.Search.setGrouping);
            });
            $$('.template-picker').each(function (element) {
                element.on('click', TBG.Search.pickTemplate);
            });
            document.observe('click', function (event, element) {
                if (['INPUT'].indexOf(event.target.nodeName) != -1)
                    return;
                $$('.filter,.interactive_plus_button').each(function (element) {
                    element.removeClassName('selected');
                });
            });
            var sff = $('searchbuilder_filterstrip_filtercontainer');
            $('interactive_filters_availablefilters_container').select('li').each(function (element) {
                element.on('click', TBG.Search.addFilter);
                if (sff.down('#interactive_filter_' + element.dataset.filter)) {
                    element.addClassName('disabled');
                }
            });
            var ifts = $$('.filter_searchfield');
            TBG.ift_observers = {};
            ifts.each(function (ift) {
                ift.dataset.lastValue = '';
                ift.on('keyup', function (event, element) {
                    if (TBG.ift_observers[ift.id])
                        clearTimeout(TBG.ift_observers[ift.id]);
                    if ((ift.getValue().length >= 3 || ift.getValue().length == 0 || (ift.dataset.maxlength && ift.getValue().length > parseInt(ift.dataset.maxlength))) && ift.getValue() != ift.dataset.lastValue) {
                        TBG.ift_observers[ift.id] = setTimeout(function () {
                            TBG.Search.liveUpdate(true);
                            ift.dataset.lastValue = ift.getValue();
                            var flt = ift.up('.filter');
                            if (flt !== undefined) {
                                TBG.Search.updateFilterVisibleValue(flt, ift.getValue());
                            }
                        }, 1000);
                    }
                });

            });
        };

        TBG.Search.pickTemplate = function (event, element) {
            event.stopPropagation();
            var is_selected = this.hasClassName('selected');
            var current_elm = this;
            if (!is_selected) {
                $$('.template-picker').each(function (element) {
                    if (element == current_elm) {
                        current_elm.addClassName('selected');
                        $('filter_selected_template').setValue(current_elm.dataset.templateName);
                        if (current_elm.dataset.grouping == '1') {
                            $('search_grouping_container').removeClassName('nogrouping');
                            $('search_grouping_container').removeClassName('parameter');
                            $('search_filter_parameter_input').disable();
                        } else {
                            $('search_grouping_container').addClassName('nogrouping');
                            if (current_elm.dataset.parameter == '1') {
                                $('search_grouping_container').addClassName('parameter');
                                $('search_filter_parameter_description').update(current_elm.dataset.parameterText)
                                $('search_filter_parameter_input').enable();
                            } else {
                                $('search_grouping_container').removeClassName('parameter');
                            }
                        }
                    } else {
                        element.removeClassName('selected');
                    }
                });
            }
            $$('.filter,.interactive_plus_button').each(function (element) {
                if (element != this)
                    element.removeClassName('selected');
            });
            if (is_selected)
                this.removeClassName('selected');
            else
                this.addClassName('selected');

            TBG.Search.liveUpdate();
        };

        TBG.Search.setGrouping = function (event, element) {
            event.stopPropagation();
            TBG.Search.setFilterSelectionGroupSelections(this);
            TBG.Search.setFilterValue(element, true);

            if (element.hasClassName('groupby')) {
                if (element.dataset.groupby == '') {
                    $('filter_grouping_options').select('.grouporder').each(Element.hide);
                } else {
                    $('filter_grouping_options').select('.grouporder').each(Element.show);
                }
            }

            TBG.Search.liveUpdate();
        };

        TBG.Search.toggleInteractiveFilter = function (event, element) {
            event.stopPropagation();
            if (['INPUT'].indexOf(event.target.nodeName) != -1)
                return;
            TBG.Search.toggleInteractiveFilterElement(this);
        };

        TBG.Search.toggleInteractiveFilterElement = function (element) {
            var is_selected = element.hasClassName('selected');
            $$('.filter,.interactive_plus_button').each(function (elm) {
                if (elm != element)
                    elm.removeClassName('selected');
            });
            if (is_selected)
            {
                element.removeClassName('selected');
            }
            else
            {
                element.addClassName('selected');
                var search_inputs = (element.hasClassName('interactive_plus_button')) ? element.next().select('input[type=search]') : element.select('input[type=search]');
                if (search_inputs.size() > 0)
                    search_inputs[0].focus();
            }

            if (element.id == 'interactive_template_button' && element.hasClassName('selected')) {
                TBG.Search.initializeIssuesPerPageSlider();
            }
        };

        TBG.Search.moveIssuesPerPageSlider = function (step) {
            var steps = [25, 50, 100, 250, 500];
            var value = steps[step - 1];
            $('issues_per_page_slider_value').update(value);
            return value;
        };

        TBG.Search.isDirty = function () {
            if ($('filter_project_id_value_input').dataset.dirty == 'dirty')
                return true;
            if ($('filter_subprojects_value_input') && $('filter_subprojects_value_input').dataset.dirty == 'dirty')
                return true;

            return false;
        };

        TBG.Search.clearDirty = function () {
            $('filter_project_id_value_input').dataset.dirty = undefined;
            $('filter_subprojects_value_input').dataset.dirty = undefined;
        };

        TBG.Search.loadDynamicChoices = function () {
            var fif = $('find_issues_form');
            var url = fif.dataset.dynamicCallbackUrl;
            var parameters = '&project_id=' + $('filter_project_id_value_input').getValue();
            var filters_containers = [];
            var fsvi = $('filter_subprojects_value_input');
            if (fsvi)
                parameters += '&subprojects=' + fsvi.getValue();
            ['build', 'component', 'edition', 'milestone'].each(function (elm) {
                var filter = $('interactive_filter_' + elm);
                var results_container = filter.down('.interactive_menu_values');
                results_container.select('input[type=checkbox]').each(function (checkbox) {
                    if (checkbox.checked)
                        parameters += '&existing_ids[' + filter.dataset.filterkey + '][' + checkbox.value + ']=' + checkbox.value;
                });
                filters_containers.push({filter: filter, container: results_container});
            });
            TBG.Main.Helpers.ajax(url, {
                params: parameters,
                loading: {
                    callback: function () {
                        filters_containers.each(function (details) {
                            details['container'].addClassName('updating');
                        });
                    }
                },
                success: {
                    callback: function (json) {
                        filters_containers.each(function (details) {
                            details['container'].update(json.results[details['filter'].dataset.filterkey]);
                            window.setTimeout(function () {
                                details['container'].select('li.filtervalue').each(function (filtervalue) {
                                    filtervalue.on('click', TBG.Search.toggleFilterValue);
                                });
                                var si = details['filter'].down('input[type=search]');
                                if (si != undefined) {
                                    si.dataset.previousValue = '';
                                    TBG.Search.filterFilterOptionsElement(si);
                                }
                            }, 250);
                            details['container'].removeClassName('updating');
                        });
                    }
                }
            });
        };

        TBG.Search.sortResults = function (event) {
            if (this.dataset.sortField !== undefined) {
                var direction = (this.dataset.sortDirection == 'asc') ? 'desc' : 'asc';
                $('search_sortfields_input').setValue(this.dataset.sortField + '=' + direction);
                TBG.Search.liveUpdate(true);
            }
        };

        TBG.Search.download = function (format) {
            var fif = $('find_issues_form');
            var parameters = fif.serialize();
            window.location = fif.dataset.historyUrl + '?' + parameters + '&format=' + format;
        };

        TBG.Search.updateSavedSearchCounts = function () {
            var search_ids = '',
                searchitems = $$('.savedsearch-item'),
                project_id = $('search_sidebar').dataset.projectId;

            searchitems.each(function (searchitem) {
                search_ids += '&search_ids[]='+$(searchitem).dataset.searchId;
            });
            TBG.Main.Helpers.ajax($('main_container').dataset.url, {
                url_method: 'get',
                params: '&say=getsearchcounts&project_id='+project_id+search_ids,
                success: {
                    callback: function (json) {
                        searchitems.each(function (searchitem) {
                            var badge = $(searchitem).down('.num_results_badge');
                            if (badge !== undefined) {
                                badge.update(json[$(searchitem).dataset.searchId]);
                            }
                        });
                    }
                }
            });
        };

        TBG.Search.liveUpdate = function (force) {
            var fif = $('find_issues_form');
            var url = fif.action;
            var parameters = fif.serialize();

            var results_loaded = (fif.dataset.resultsLoaded != undefined && fif.dataset.resultsLoaded != '');

            if (force == true || results_loaded) {
                $('search_sidebar').addClassName('collapsed');
                TBG.Main.Helpers.ajax(url, {
                    params: parameters,
                    loading: {
                        indicator: 'search_results_loading_indicator',
                        callback: function () {
                            if (history.pushState) {
                                history.pushState({caller: 'liveUpdate'}, '', fif.dataset.historyUrl + '?' + parameters);
                            }
                        }
                    },
                    success: {update: 'search_results'},
                    complete: {
                        callback: function (json) {
                            if (!results_loaded) {
                                TBG.Search.updateSavedSearchCounts();
                            }
                            $('findissues_num_results_span').update(json.num_issues);
                            if (! $('findissues_search_title').visible() && ! $('findissues_search_generictitle').visible()) {
                                $('findissues_search_generictitle').show();
                            }
                            $('findissues_num_results').show();
                            $('interactive_save_button').show();
                            fif.dataset.resultsLoaded = true;
                            fif.dataset.isSaved = undefined;
                            $('search_results').select('th').each(function (header_elm) {
                                if (!header_elm.hasClassName('nosort')) {
                                    header_elm.on('click', TBG.Search.sortResults);
                                }
                            });
                            if (TBG.Search.isDirty()) {
                                TBG.Search.loadDynamicChoices();
                                TBG.Search.clearDirty();
                            }
                        }
                    }
                });
            }
        };

        TBG.Search.setIssuesPerPage = function (value) {
            var fip_value = $('filter_issues_per_page');
            fip_value.setValue(parseInt(value));
            TBG.Search.liveUpdate();
        };

        TBG.Search.initializeIssuesPerPageSlider = function () {
            var ipp_slider = $('issues_per_page_slider');
            if (ipp_slider.dataset.initialized == undefined) {
                var fip_value = $('filter_issues_per_page');
                var ipp_value = $('issues_per_page_slider_value');
                var step_start = 1;
                switch (parseInt(fip_value.getValue())) {
                    case 25:
                        step_start = 1;
                        break;
                    case 50:
                        step_start = 2;
                        break;
                    case 100:
                        step_start = 3;
                        break;
                    case 250:
                        step_start = 4;
                        break;
                    case 500:
                        step_start = 5;
                        break;
                }
                new Control.Slider('issues_per_page_handle', ipp_slider, {
                    range: $R(1, 5),
                    values: [1, 2, 3, 4, 5],
                    sliderValue: step_start,
                    onSlide: function (step) {
                        TBG.Search.moveIssuesPerPageSlider(step);
                    },
                    onChange: function (step) {
                        var value = TBG.Search.moveIssuesPerPageSlider(step);
                        TBG.Search.setIssuesPerPage(value);
                    }
                });
                ipp_slider.dataset.initialized = true;
            }
        };

        TBG.Search.setFilterValue = function (element, checked) {
            if (element.hasClassName('separator'))
                return;
            if (checked) {
                element.addClassName('selected');
                element.down('input').checked = true;
            } else {
                element.removeClassName('selected');
                element.down('input').checked = false;
            }
        };

        TBG.Search.setFilterSelectionGroupSelections = function (element) {
            var current_element = element;
            if (element.dataset.exclusive !== undefined) {
                element.up('.interactive_menu_values').childElements().each(function (filter_element) {
                    if (filter_element.hasClassName('filtervalue')) {
                        if ((element.dataset.excludeGroup !== undefined && filter_element.dataset.selectionGroup == element.dataset.excludeGroup) ||
                            element.dataset.selectionGroup == filter_element.dataset.selectionGroup) {
                            if (filter_element.dataset.value != current_element.dataset.value)
                                TBG.Search.setFilterValue(filter_element, false);
                        }
                    }
                });
            }
            else if (element.dataset.excludeGroup !== undefined) {
                element.up('.interactive_menu_values').childElements().each(function (filter_element) {
                    if (filter_element.hasClassName('filtervalue')) {
                        if (filter_element.dataset.selectionGroup != current_element.dataset.selectionGroup)
                            TBG.Search.setFilterValue(filter_element, false);
                    }
                });
            }
        };

        TBG.Search.setInteractiveDate = function (element) {
            var f_element = element.up('.filter');
            TBG.Search.calculateFilterDetails(f_element);
            element.dataset.dirty = 'dirty';
            TBG.Search.liveUpdate(true);
        };

        TBG.Search.saveSearch = function () {
            var fif = $('find_issues_form');
            var find_parameters = fif.serialize();
            var ssf = $('save_search_form');
            var p = find_parameters + '&' + ssf.serialize();

            var button = ssf.down('input[type=submit]');
            TBG.Main.Helpers.ajax(ssf.action, {
                params: p,
                loading: {
                    indicator: 'save_search_indicator',
                    callback: function () {
                        button.disable();
                    }
                },
                complete: {
                    callback: function () {
                        button.enable();
                    }
                }
            });
        };

        TBG.Search.toggleFilterValue = function (event, element) {
            event.stopPropagation();
            event.stopImmediatePropagation();
            event.preventDefault();
            TBG.Search.toggleFilterValueElement(this);
        };

        TBG.Search.toggleFilterValueElement = function (element, checked) {
            if (checked == undefined) {
                if (element.down('input').checked) {
                    TBG.Search.setFilterValue(element, false);
                } else {
                    TBG.Search.setFilterValue(element, true);
                }
            } else {
                TBG.Search.setFilterValue(element, checked);
            }
            TBG.Search.setFilterSelectionGroupSelections(element);
            var f_element = element.up('.filter');
            TBG.Search.calculateFilterDetails(f_element);
            $('filter_' + f_element.dataset.filterkey + '_value_input').dataset.dirty = 'dirty';
            TBG.Search.liveUpdate(true);
        };

        TBG.Search.calculateFilterDetails = function (filter) {
            var string = '';
            var value_string = '';
            var selected_elements = [];
            var selected_values = [];
            filter.select('input[type=checkbox]').each(function (element) {
                if (element.checked) {
                    selected_elements.push(element.dataset.text);
                    if (element.up('.filtervalue').dataset.operator == undefined) {
                        selected_values.push(element.getValue());
                    } else {
                        $('filter_' + filter.dataset.filterkey + '_operator_input').setValue(element.getValue());
                    }
                }
            });
            if (selected_elements.size() > 0) {
                string = selected_elements.join(', ');
                value_string = selected_values.join(',');
            } else {
                string = filter.dataset.allValue;
            }
            if (filter.dataset.isdate !== undefined) {
                selected_elements.push($('filter_' + filter.dataset.filterkey + '_value_input').dataset.displayValue);
                string = selected_elements.join(' ');
            }
            if (filter.dataset.istext !== undefined) {
                string = $('filter_' + filter.dataset.filterkey + '_value_input').getValue();
            }
            TBG.Search.updateFilterVisibleValue(filter, string);
            if (filter.dataset.isdate === undefined && filter.dataset.istext === undefined)
                $('filter_' + filter.dataset.filterkey + '_value_input').setValue(value_string);
        };

        TBG.Search.updateFilterVisibleValue = function (filter, value) {
            if (value.length > 23) {
                value = value.substr(0, 20) + '...';
            }
            filter.down('.value').update(value);
        };

        TBG.Search.initializeKeyboardNavigation = function () {
            Event.observe(document, 'keydown', function (event) {
                if (['INPUT', 'TEXTAREA'].indexOf(event.target.nodeName) != -1)
                    return;
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
            $('search_results').select('tr').each(function (element) {
                element.observe('click', function (event) {
                    var selected_elements = $('search_results').select('tr.selected');
                    var old_selected_element = (selected_elements.size() == 0) ? undefined : selected_elements[selected_elements.size() - 1];
                    TBG.Search.move(old_selected_element, this, null, false);
                });
            });
        };

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
            version: '1.3', // version constant
            demo: false,
            demo_text: null,
            cookie_expires: 6 * 30, // 6 months.
            cookie_name: 'openid_provider',
            cookie_path: '/',
            img_path: 'images/',
            locale: 'en', // is set in openid-<locale>.js
            sprite: 'en', // usually equals to locale, is set in
            // openid-<locale>.js
            signin_text: null, // text on submit button on the form
            all_small: false, // output large providers w/ small icons
            image_title: '%openid_provider_name', // for image title

            input_id: 'openid_identifier',
            provider_url: null,
            provider_id: null,
            providers_small: null,
            providers_large: null,
            /**
             * Class constructor
             *
             * @return {Void}
             */
            init: function () {
                var openid_btns = $('openid_btns');
                if ($('openid_choice')) {
                    $('openid_choice').setStyle({
                        display: 'block'
                    });
                }
                if ($('openid_input_area')) {
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
            getBoxHTML: function (box_id, provider, box_size, index) {
                var image_ext = box_size == 'small' ? '.ico.png' : '.png';
                return '<a title="' + this.image_title.replace('%openid_provider_name', provider["name"]) + '" href="javascript:TBG.OpenID.signin(\'' + box_id + '\');"'
                    + 'class="' + box_id + ' openid_' + box_size + '_btn button button-silver"><img src="' + TBG.basepath + 'iconsets/oxygen/openid_providers.' + box_size + '/' + box_id + image_ext + '"></a>';
            },
            /**
             * Provider image click
             *
             * @return {Void}
             */
            signin: function (box_id) {
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
            submit: function () {
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
            setOpenIdUrl: function (url) {
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
            highlight: function (box_id) {
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
            setCookie: function (value) {
                var date = new Date();
                date.setTime(date.getTime() + (this.cookie_expires * 24 * 60 * 60 * 1000));
                var expires = "; expires=" + date.toGMTString();
                document.cookie = this.cookie_name + "=" + value + expires + "; path=" + this.cookie_path;
            },
            readCookie: function () {
                var nameEQ = this.cookie_name + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
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
            useInputBox: function (provider) {
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
            setDemoMode: function (demoMode) {
                this.demo = demoMode;
            }
        };

        TBG.Tutorial.highlightArea = function (top, left, width, height, blocked, seethrough) {
            var backdrop_class = (seethrough != undefined && seethrough == true) ? 'seethrough' : '';
            var d1 = '<div class="fullpage_backdrop ' + backdrop_class + ' tutorial" style="top: 0; left: 0; width: ' + left + 'px;"></div>';
            var d2_width = TBG.Core._vp_width - left - width;
            var d2 = '<div class="fullpage_backdrop ' + backdrop_class + ' tutorial" style="top: 0; left: ' + (left + width) + 'px; width: ' + d2_width + 'px;"></div>';
            var d3 = '<div class="fullpage_backdrop ' + backdrop_class + ' tutorial" style="top: 0; left: ' + left + 'px; width: ' + width + 'px; height: ' + top + 'px"></div>';
            var vp_height = document.viewport.getHeight();
            var d4_height = vp_height - top - height;
            var d4 = '<div class="fullpage_backdrop ' + backdrop_class + ' tutorial" style="top: ' + (top + height) + 'px; left: ' + left + 'px; width: ' + width + 'px; height: ' + d4_height + 'px"></div>';
            var mc = $('main_container');
            if (blocked == true) {
                var d_overlay = '<div class="tutorial block_overlay" style="top: ' + top + 'px; left: ' + left + 'px; width: ' + width + 'px; height: ' + height + 'px;"></div>';
                mc.insert(d_overlay);
            }
            mc.insert(d1);
            mc.insert(d2);
            mc.insert(d3);
            mc.insert(d4);
            TBG.Tutorial.positionMessage(top, left, width, height);
        };
        TBG.Tutorial.highlightElement = function (element, blocked, seethrough) {
            element = $(element);
            var el = element.getLayout();
            var os = element.cumulativeOffset();
            var width = el.get('width') + el.get('padding-left') + el.get('padding-right');
            var height = el.get('height') + el.get('padding-top') + el.get('padding-bottom');
            TBG.Tutorial.highlightArea(os.top, os.left, width, height, blocked, seethrough);
        };
        TBG.Tutorial.positionMessage = function (top, left, width, height) {
            var tm = $('tutorial-message');
            ['above', 'below', 'left', 'right'].each(function (pos) {
                tm.removeClassName(pos);
            });
            if (top + left + width + height == 0) {
                tm.addClassName('full');
                tm.setStyle({top: '', left: ''});
            } else {
                tm.removeClassName('full');
                var step = parseInt(tm.dataset.tutorialStep);
                var key = tm.dataset.tutorialKey;
                var td = TBG.Tutorial.Stories[key][step];
                tm.addClassName(td.messagePosition);
                var tl = tm.getLayout();
                var twidth = tl.get('width') + tl.get('padding-left') + tl.get('padding-right');
                switch (td.messagePosition) {
                    case 'right':
                        var tl = tm.getLayout();
                        var th = tl.get('height') + tl.get('padding-top') + tl.get('padding-bottom');
                        tm.setStyle({top: (top - parseInt(th / 2)) + 'px', left: (left + width + 15) + 'px'});
                        break;
                    case 'left':
                        var tl = tm.getLayout();
                        var width = tl.get('width') + tl.get('padding-left') + tl.get('padding-right');
                        var th = tl.get('height') + tl.get('padding-top') + tl.get('padding-bottom');
                        tm.setStyle({top: (top - parseInt(th / 2)) + 'px', left: (left - width - 15) + 'px'});
                        break;
                    case 'below':
                        tm.setStyle({top: (top + height + 15) + 'px', left: ((left - parseInt(twidth / 2)) + width / 2) + 'px'});
                        break;
                    case 'above':
                        var tl = tm.getLayout();
                        var th = tl.get('height') + tl.get('padding-top') + tl.get('padding-bottom');
                        tm.setStyle({top: (top - th - 15) + 'px', left: ((left - parseInt(twidth / 2)) + width / 2) + 'px'});
                        break;
                    case 'center':
                        var tl = tm.getLayout();
                        var th = tl.get('height') + tl.get('padding-top') + tl.get('padding-bottom');
                        tm.setStyle({top: (top + (height / 2) - (th / 2)) + 'px', left: ((left - parseInt(twidth / 2)) + width / 2) + 'px'});
                        break;
                }
            }
            tm.show();
        };
        TBG.Tutorial.resetHighlight = function () {
            $$('.tutorial').each(Element.remove);
        };
        TBG.Tutorial.disable = function () {
            var tm = $('tutorial-message');
            var key = tm.dataset.tutorialKey;
            var url = tm.dataset.disableUrl;
            TBG.Main.Helpers.ajax(url, {
                params: '&key=' + key
            });
            $('tutorial-next-button').stopObserving('click');
            TBG.Tutorial.resetHighlight();
            $('tutorial-message').hide();
        };
        TBG.Tutorial.playNextStep = function () {
            TBG.Tutorial.resetHighlight();
            var tm = $('tutorial-message');
            tm.hide();
            var step = parseInt(tm.dataset.tutorialStep);
            var key = tm.dataset.tutorialKey;
            step++;
            $('tutorial-current-step').update(step);
            tm.dataset.tutorialStep = step;
            var tutorialData = TBG.Tutorial.Stories[key][step];
            if (tutorialData != undefined) {
                if (tutorialData.cb) {
                    tutorialData.cb(tutorialData);
                }
                $('tutorial-message-container').update(tutorialData.message);
                var tbn = tm.down('.tutorial-buttons').down('.button-next');
                var tb = tm.down('.tutorial-buttons').down('.button-disable');
                if (tutorialData.button != undefined) {
                    tbn.update(tutorialData.button);
                    tbn.show();
                    if (step > 1) {
                        tb.hide();
                    } else {
                        tb.show();
                    }
                } else {
                    tbn.hide();
                    tb.hide();
                }
                ['small', 'medium', 'large'].each(function (cn) {
                    tm.removeClassName(cn);
                });
                tm.addClassName(tutorialData.messageSize);
                if (tutorialData.highlight != undefined) {
                    var tdh = tutorialData.highlight;
                    var timeout = (tdh.delay) ? tdh.delay : 50;
                    window.setTimeout(function () {
                        tm.show();
                        if (tdh.element != undefined) {
                            var seethrough = (tdh.seethrough != undefined) ? tdh.seethrough : false;
                            TBG.Tutorial.highlightElement(tdh.element, tdh.blocked, seethrough);
                        } else {
                            TBG.Tutorial.highlightArea(tdh.top, tdh.left, tdh.width, tdh.height, tdh.blocked);
                        }
                    }, timeout);
                } else {
                    TBG.Tutorial.highlightArea(0, 0, 0, 0, true);
                }
            } else {
                TBG.Tutorial.disable();
            }
        };
        TBG.Tutorial.start = function (key, initial_step) {
            var tutorial = TBG.Tutorial.Stories[key];
            var ts = 0;
            var is = (initial_step != undefined) ? (initial_step - 1) : 0;
            for (var d in tutorial) {
                ts++;
            }
            var tm = $('tutorial-message');
            tm.dataset.tutorialKey = key;
            tm.dataset.tutorialStep = is;
            tm.dataset.tutorialSteps = ts;
            $('tutorial-total-steps').update(ts);
            $('tutorial-next-button').stopObserving('click');
            $('tutorial-next-button').observe('click', TBG.Tutorial.playNextStep);
            TBG.Tutorial.playNextStep();
        };

        TBG.Main.Helpers.toggler = function (elm) {
            elm.toggleClass("button-pressed");
            if (jQuery(elm).data('target')) {
                jQuery('#'+jQuery(elm).data('target')).toggle();
            } else {
                elm.next().toggle();
            }
        };

        TBG.Main.loadParentArticles = function (form) {
            TBG.Main.Helpers.ajax(form.action, {
                params: $(form).serialize(),
                loading: {
                    indicator: 'parent_selector_container_indicator',
                },
                complete: {
                    callback: function (json) {
                        $('parent_articles_list').update(json.list);
                    }
                }
            });
        };

        TBG.Main.Notifications.markAllRead = function () {
            TBG.Main.Helpers.ajax(TBG.data_url, {
                url_method: 'post',
                params: '&say=notificationsread',
                loading: {
                    callback: function () {
                        $('user_notifications').addClassName('toggling');
                    }
                },
                success: {
                    callback: function (json) {
                        var un = $('user_notifications');
                        un.select('li').each(function (li) {
                            li.removeClassName('unread');
                            li.addClassName('read');
                        });
                        TBG.Core.Pollers.Callbacks.dataPoller();
                    }
                }
            });
        };

        TBG.Main.Notifications.toggleRead = function (notification_id) {
            TBG.Main.Helpers.ajax(TBG.data_url, {
                url_method: 'post',
                params: '&say=notificationstatus&notification_id=' + notification_id,
                loading: {
                    callback: function () {
                        $('notification_' + notification_id + '_container').addClassName('toggling');
                    }
                },
                success: {
                    callback: function (json) {
                        var nc = $('notification_' + notification_id + '_container');
                        ['toggling', 'read', 'unread'].each(function (cn) {
                            nc.toggleClassName(cn);
                        });
                        TBG.Core.Pollers.Callbacks.dataPoller();
                    }
                }
            });
        };
        
        TBG.Main.Notifications.loadMore = function () {
            if (TBG.Main.Notifications.loadingLocked !== true) {
                TBG.Main.Notifications.loadingLocked = true;
                var unl = $('user_notifications_list'),
                    unl_data = unl.dataset;
                TBG.Main.Helpers.ajax(unl_data.notificationsUrl+'&offset='+unl_data.offset, {
                    url_method: 'get',
                    loading: {
                        indicator: 'user_notifications_loading_indicator'
                    },
                    success: {
                        update: { element: 'user_notifications_list', insertion: true },
                        callback: function () {
                            jQuery("#user_notifications_list_wrapper_nano").nanoScroller();
                            unl_data.offset = parseInt(unl_data.offset) + 25;
                            TBG.Main.Notifications.loadingLocked = false;
                        }
                    }
                });
            }
        }

        TBG.Main.initializeMentionable = function (textarea) {
            if ($(textarea).hasClassName('mentionable') && !$(textarea).hasClassName('mentionable-initialized')) {
                TBG.Main.Helpers.ajax(TBG.data_url, {
                    url_method: 'get',
                    params: 'say=get_mentionables&target_type=' + $(textarea).dataset.targetType + '&target_id=' + $(textarea).dataset.targetId,
                    success: {
                        callback: function (json) {
                            jQuery('#' + textarea.id).mention({
                                delimiter: '@',
                                sensitive: true,
                                emptyQuery: true,
                                queryBy: ['name', 'username'],
                                typeaheadOpts: {
                                    items: 10 // Max number of items you want to show
                                },
                                users: json.mentionables
                            });
                            $(textarea).addClassName('mentionable-initialized');
                        }
                    }
                });
            }
            ;
        };

        TBG.Main.Helpers.loadDynamicMenu = function (menu) {
            var url = $(menu).dataset.menuUrl;
            TBG.Main.Helpers.ajax(url, {
                url_method: 'get',
                success: {
                    callback: function (json) {
                        $(menu).replace(json.menu);
                    }
                }
            });
        };

        TBG.Main.Helpers.toggleFancyFilterElement = function (element) {
            var is_selected = element.hasClassName('selected');
            $$('.fancyfilter').each(function (elm) {
                if (elm != element)
                    elm.removeClassName('selected');
            });
            if (is_selected)
            {
                element.removeClassName('selected');
            }
            else
            {
                element.addClassName('selected');
            }
        };

        TBG.Main.Helpers.initializeFancyFilterField = function (filter) {
            if (!filter.hasClassName('initialized')) {
                filter.on('click', TBG.Main.Helpers.toggleFancyFilter);
                filter.select('li.filtervalue').each(function (filtervalue) {
                    filtervalue.on('click', TBG.Main.Helpers.toggleFancyFilterValue);
                });
                TBG.Main.Helpers.calculateFancyFilterDetails(filter);
            }
        };

        TBG.Main.Helpers.toggleFancyFilter = function (event, element) {
            if (event) {
                event.stopPropagation();
                if (['INPUT'].indexOf(event.target.nodeName) != -1)
                    return;
            }
            TBG.Main.Helpers.toggleFancyFilterElement(this);
        };

        TBG.Main.Helpers.setFancyFilterValue = function (element, checked) {
            if (element.hasClassName('separator'))
                return;
            if (checked) {
                element.addClassName('selected');
                element.down('input').checked = true;
            } else {
                element.removeClassName('selected');
                element.down('input').checked = false;
            }
        };

        TBG.Main.Helpers.toggleFancyFilterValue = function (event, element) {
            event.stopPropagation();
            event.stopImmediatePropagation();
            event.preventDefault();
            if (!$(this).hasClassName('disabled')) {
                TBG.Main.Helpers.toggleFancyFilterValueElement(this);
            }
        };

        TBG.Main.Helpers.setFancyFilterSelectionGroupSelections = function (element) {
            var current_element = element;
            if (element.dataset.exclusive !== undefined) {
                element.up('.interactive_menu_values').childElements().each(function (filter_element) {
                    if (filter_element.hasClassName('filtervalue')) {
                        if ((element.dataset.excludeGroup !== undefined && filter_element.dataset.selectionGroup == element.dataset.excludeGroup) ||
                            element.dataset.selectionGroup == filter_element.dataset.selectionGroup) {
                            if (filter_element.dataset.value != current_element.dataset.value)
                                TBG.Main.Helpers.setFancyFilterValue(filter_element, false);
                        }
                    }
                });
            }
            else if (element.dataset.excludeGroup !== undefined) {
                element.up('.interactive_menu_values').childElements().each(function (filter_element) {
                    if (filter_element.hasClassName('filtervalue')) {
                        if (filter_element.dataset.selectionGroup != current_element.dataset.selectionGroup)
                            TBG.Main.Helpers.setFancyFilterValue(filter_element, false);
                    }
                });
            }
            if (element.up('.fancyfilter').dataset.exclusivityGroup !== undefined) {
                var egroup = element.up('.fancyfilter').dataset.exclusivityGroup;
                $$('.interactive_menu_values').each(function (value_list) {
                    if (value_list.up('.fancyfilter').dataset.exclusivityGroup !== undefined && value_list.up('.fancyfilter').dataset.exclusivityGroup === egroup) {
                        value_list.childElements('.filtervalue').each(function (filtervalue) {
                            if ($(filtervalue).dataset.value === element.dataset.value) {
                                if ($(filtervalue) !== element) {
                                    if (element.hasClassName('selected')) {
                                        $(filtervalue).addClassName('disabled');
                                    } else {
                                        $(filtervalue).removeClassName('disabled');
                                    }
                                }
                            }
                        })
                    }
                });
            }
        };

        TBG.Main.Helpers.recalculateFancyFilters = function(filter) {
            if (filter != undefined) {
                $$('.filter').each(TBG.Main.Helpers.calculateFancyFilterDetails);
            }
            else {
                TBG.Main.Helpers.calculateFancyFilterDetails(filter);
            }
        };

        TBG.Main.Helpers.toggleFancyFilterValueElement = function (element, checked) {
            if (checked == undefined) {
                if (element.down('input').checked) {
                    TBG.Main.Helpers.setFancyFilterValue(element, false);
                } else {
                    TBG.Main.Helpers.setFancyFilterValue(element, true);
                }
            } else {
                TBG.Main.Helpers.setFancyFilterValue(element, checked);
            }
            TBG.Main.Helpers.setFancyFilterSelectionGroupSelections(element);
            var f_element = element.up('.filter');
            TBG.Main.Helpers.calculateFancyFilterDetails(f_element);
            if (element.dataset.exclusive !== undefined) TBG.Main.Helpers.toggleFancyFilterElement(f_element);
        };

        TBG.Main.Helpers.calculateFancyFilterDetails = function (filter) {
            var string = '';
            var value_string = '';
            var selected_elements = [];
            var selected_values = [];
            filter.select('input[type=checkbox]').each(function (element) {
                if (element.checked) {
                    selected_elements.push(element.dataset.text);
                    selected_values.push(element.getValue());
                }
            });
            if (selected_elements.size() > 0) {
                string = selected_elements.join(', ');
                value_string = selected_values.join(',');
            } else {
                string = filter.dataset.noSelectionValue;
            }
            TBG.Main.Helpers.updateFancyFilterVisibleValue(filter, string);
            $('filter_' + filter.dataset.filterkey + '_value_input').setValue(value_string);
        };

        TBG.Main.Helpers.updateFancyFilterVisibleValue = function (filter, value) {
            filter.down('.value').update(value);
        };

        TBG.Main.Helpers.initializeColorPicker = function () {
            jQuery('input.color').each(function (index, element) {
                var input = jQuery(element);
                input.spectrum({
                    cancelText: input.data('cancel-text'),
                    chooseText: input.data('choose-text'),
                    showInput: true,
                    preferredFormat: 'hex'
                });
            });
        };

        TBG.Main.Helpers.initializeFancyFilters = function(fancyfilter) {
            if (fancyfilter != undefined) {
                TBG.Main.Helpers.initializeFancyFilterField(fancyfilter);
            }
            else {
                $$('.fancyfilter').each(TBG.Main.Helpers.initializeFancyFilterField);
            }
        };

        TBG.Core.getPluginUpdates = function (type) {
            var params = '',
                plugins = $('installed-'+type+'s-list').childElements();
            plugins.each(function (plugin) {
                if (type == 'theme' || !plugin.hasClassName('disabled')) {
                    params += '&addons[]=' + plugin.dataset[type+'Key'];
                }
            });
            TBG.Main.Helpers.ajax($('main_container').dataset.url, {
                url_method: 'get',
                params: 'say=get_'+type+'_updates' + params,
                loading: {
                    indicator: 'installed_'+type+'s_indicator'
                },
                success: {
                    update: 'installed_'+type+'s_indicator',
                    callback: function (json) {
                        plugins.each(function (plugin) {
                            if (json[plugin.dataset[type+'Key']] !== undefined) {
                                if (plugin.dataset.version != json[plugin.dataset[type+'Key']].version) {
                                    plugin.addClassName('can-update');
                                    var link = $(type + '_'+plugin.dataset[type+'Key']+'_download_location');
                                    link.setAttribute('href', json[plugin.dataset[type+'Key']].download);
                                    jQuery('body').on('click', '.update-'+type+'-menu-item', function (e) {
                                        var pluginbox = jQuery(this).parents('li.'+type);
                                        $('update_'+type+'_help_' + pluginbox.data('id')).show();
                                        if (!TBG.Core.Pollers.pluginupdatepoller)
                                            TBG.Core.Pollers.pluginupdatepoller = new PeriodicalExecuter(TBG.Core.validatePluginUpdateUploadedPoller(type, pluginbox.data('module-key')), 5);
                                    });
                                }
                            }
                        })
                    }
                },
                failure: {
                    callback: function (response) {
                        console.log(response);
                    }
                }
            });
        };

        TBG.Core.cancelManualUpdatePoller = function () {
            TBG.Core.Pollers.Locks.pluginupdatepoller = false;
            if (TBG.Core.Pollers.pluginupdatepoller) {
                TBG.Core.Pollers.pluginupdatepoller.stop();
                TBG.Core.Pollers.pluginupdatepoller = undefined;
            }
        };

        TBG.Core.validatePluginUpdateUploadedPoller = function (type, pluginkey) {
            return function () {
                if (!TBG.Core.Pollers.Locks.pluginupdatepoller) {
                    TBG.Core.Pollers.Locks.pluginupdatepoller = true;
                    TBG.Main.Helpers.ajax($('main_container').dataset.url, {
                        url_method: 'get',
                        params: '&say=verify_'+type+'_update_file&'+type+'_key='+pluginkey,
                        success: {
                            callback: function (json) {
                                if (json.verified == '1') {
                                    jQuery('#'+type+'_'+pluginkey+'_perform_update').children('input[type=submit]').prop('disabled', false);
                                    TBG.Core.cancelManualUpdatePoller();
                                }
                                TBG.Core.Pollers.Locks.pluginupdatepoller = false;
                            }
                        }
                    });
                }
            }
        };

        TBG.Core.getAvailablePlugins = function (type, callback) {
            TBG.Main.Helpers.ajax($('main_container').dataset.url, {
                url_method: 'get',
                params: '&say=get_'+type,
                loading: {
                    indicator: 'available_'+type+'_loading_indicator'
                },
                success: {
                    update: 'available_'+type+'_container',
                    callback: function () {
                        jQuery('body').on('click', '.install-button', callback);
                    }
                }
            });
        };

        TBG.Core.installPlugin = function (button, type) {
            button = jQuery(button);
            button.addClass('installing');
            button.prop('disabled', true);
            TBG.Main.Helpers.ajax($('main_container').dataset.url, {
                url_method: 'post',
                params: '&say=install-'+type+'&'+type+'_key='+button.data('key'),
                success: {
                    callback: function (json) {
                        if (json.installed) {
                            $('online-'+type+'-' + json[type+'_key']).addClassName('installed');
                            $('installed-'+type+'s-list').insert(json[type], 'after');
                        }
                    }
                },
                failure: {
                    callback: function () {
                        button.removeClass('installing');
                        button.prop('disabled', false);
                    }
                }
            });
        };

        TBG.Modules.getModuleUpdates = function () {
            TBG.Core.getPluginUpdates('module');
        };

        TBG.Modules.getAvailableOnline = function () {
            TBG.Core.getAvailablePlugins('modules', TBG.Modules.install);
        };

        TBG.Modules.install = function (event) {
            TBG.Core.installPlugin(this, 'module');
        };

        TBG.Themes.getThemeUpdates = function () {
            TBG.Core.getPluginUpdates('theme');
        };

        TBG.Themes.getAvailableOnline = function () {
            TBG.Core.getAvailablePlugins('themes', TBG.Themes.install);
        };

        TBG.Themes.install = function (event) {
            TBG.Core.installPlugin(this, 'theme');
        };

        return TBG;
});
