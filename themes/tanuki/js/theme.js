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
