<?php

    /**
     * Configuration for theme
     */

    $tbg_response->addStylesheet(make_url('asset_css', array('theme_name' => 'oxygen', 'css' => 'theme.css')));
    \thebuggenie\core\framework\Settings::setIconsetName('oxygen');
?>
<style>
    #tbg3_username, #fieldusername { background-image: url('<?php echo $webroot . 'iconsets/' . \thebuggenie\core\framework\Settings::getIconsetName() . '/'; ?>user_mono.png'); }
    #fieldusername.invalid { background-image: url('<?php echo $webroot . 'iconsets/' . \thebuggenie\core\framework\Settings::getIconsetName() . '/'; ?>icon_error.png'); background-color: rgba(255, 220, 220, 0.5); }
    #fieldusername.valid { background-image: url('<?php echo $webroot . 'iconsets/' . \thebuggenie\core\framework\Settings::getIconsetName() . '/'; ?>icon_ok.png'); background-color: rgba(220, 255, 220, 0.5); }
    .login_popup input[type=password] { background-image: url('<?php echo $webroot . 'iconsets/' . \thebuggenie\core\framework\Settings::getIconsetName() . '/'; ?>password_mono.png'); }
    #openid-signin-button.persona-button span:after{ background-image: url('<?php echo $webroot . 'iconsets/' . \thebuggenie\core\framework\Settings::getIconsetName() . '/'; ?>openid_providers.small/openid.ico.png'); }
    #regular-signin-button.persona-button span:after{ background-image: url('<?php echo $webroot . 'iconsets/' . \thebuggenie\core\framework\Settings::getIconsetName() . '/'; ?>footer_logo.png'); }
    #forgot_password_username { background-image: url('<?php echo $webroot . 'iconsets/' . \thebuggenie\core\framework\Settings::getIconsetName() . '/'; ?>user_mono.png'); }
    #planning_filter_title_input { background-image: url('<?php echo $webroot . 'iconsets/' . \thebuggenie\core\framework\Settings::getIconsetName() . '/'; ?>icon-mono-search.png'); }
    .login_popup .article h1 { background: url('<?php echo $webroot . 'iconsets/' . \thebuggenie\core\framework\Settings::getIconsetName() . '/'; ?>logo_48.png') 0 0 no-repeat; }

    table.results_normal th.sort_asc { background-image: url('<?php echo $webroot; ?>iconsets/oxygen/sort_down.png') !important; padding-left: 25px !important; }
    table.results_normal th.sort_desc { background-image: url('<?php echo $webroot; ?>iconsets/oxygen/sort_up.png') !important; padding-left: 25px !important; }

    .module .rating { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/star_faded_small.png'); }
    .module .rating .score { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/star_small.png'); }

    .markItUp .markItUpButton1 a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/h1.png'); }
    .markItUp .markItUpButton2 a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/h2.png'); }
    .markItUp .markItUpButton3 a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/h3.png'); }
    .markItUp .markItUpButton4 a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/h4.png'); }
    .markItUp .markItUpButton5 a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/h5.png'); }
    .markItUp .markItUpButton6 a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/bold.png'); }
    .markItUp .markItUpButton7 a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/italic.png'); }
    .markItUp .markItUpButton8 a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/stroke.png'); }
    .markItUp .markItUpButton9 a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/list-bullet.png'); }
    .markItUp .markItUpButton10 a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/list-numeric.png'); }
    .markItUp .markItUpButton11 a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/picture.png'); }
    .markItUp .markItUpButton12 a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/link.png'); }
    .markItUp .markItUpButton13 a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/url.png'); }
    .markItUp .markItUpButton14 a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/quotes.png'); }
    .markItUp .markItUpButton15 a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/code.png'); }
    .markItUp .preview a { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/preview.png'); }
    .markItUpResizeHandle { background-image:url('<?php echo $webroot; ?>iconsets/oxygen/markitup/handle.png'); }
    .markItUpHeader ul .markItUpDropMenu { background-image: url('<?php echo $webroot; ?>iconsets/oxygen/markitup/menu.png'); }
    .markItUpHeader ul ul .markItUpDropMenu { background-image: url('<?php echo $webroot; ?>iconsets/oxygen/markitup/submenu.png'); }

</style>