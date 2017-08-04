var TBG, jQuery;
require(['domReady', 'thebuggenie/tbg', 'jquery', 'jquery.nanoscroller'], function (domReady, tbgjs, jquery, nanoscroller) {
    domReady(function () {
        TBG = tbgjs;
        jQuery = jquery;

        // Event.stopObserving(window, 'resize', TBG.Core._mobileMenuMover);
        TBG.Core._mobileMenuMover = function () {};
        if ($('mobile_menu').childElements().size() == 1) {
            // var um = $('user_menu');
            // if (um) {
            //     $('mobile_menu').insert(um.remove());
            // }
            $('mobile_menu').insert($('main_menu').remove());
            if ($('comment_add_button') != undefined) {
                $('comment_add_button').addClassName('immobile');
            }
            if ($('workflow_actions') && $('title_header')) {
                $('title_header').insert($('workflow_actions').remove());
            }
            if ($('posted_at_field') && $('title_header')) {
                $('title_header').insert($('posted_at_field').remove());
            }
            if ($('project_information_menu')) {
                $('project_information_menu').removeClassName('tab_menu_dropdown');
            }
            if ($('project_settings_main_link') || $('project_release_center_main_link')) {
                var html = '<a class="dropper button" id="edit-project-header-button" href="javascript:void(0);"><i class="fa fa-cog"></i><i class="fa fa-caret-down"></i></a>' +
                    '<ul class="popup_box more_actions_dropdown" id="project_header_dropper_menu"></ul>';

                $('reportissue_button').insert({before: html});
                if ($('project_release_center_main_link')) {
                    $('project_header_dropper_menu').insert('<li><a href="'+$('project_release_center_main_link').href+'">'+$('project_release_center_main_link').innerHTML+'</a></li>');
                }
                if ($('project_settings_main_link')) {
                    $('project_header_dropper_menu').insert('<li><a href="'+$('project_settings_main_link').href+'">'+$('project_settings_main_link').innerHTML+'</a></li>');
                }
                if ($('edit-project-dashboard-button')) {
                    $('project_header_dropper_menu').insert('<li class="separator">&nbsp;</li>');
                    $('project_header_dropper_menu').insert('<li><a href="javascript:void(0);" onclick="$$(\'.dashboard\').each(function (elm) { elm.toggleClassName(\'editable\');});$(this).toggleClassName(\'button-pressed\');">'+$('edit-project-dashboard-button').innerHTML+'</a></li>');
                }
            }
        }

        jQuery("body").on("click", "#mobile_menu .menu_dropdown", function (e) {
            var is_active = jQuery(this).parents('li').hasClass('active');
            jQuery(this).parents('ul').find('li').removeClass('active');
            if (is_active) {
                jQuery(this).parents('li').removeClass('active');
            } else {
                jQuery(this).parents('li').addClass('active');
            }
        });
        // console.log('Stopped autoresizing');
        // TBG.Core._mobileMenuMover();
    });
});
