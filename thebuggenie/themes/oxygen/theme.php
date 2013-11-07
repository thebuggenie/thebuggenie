<?php

	/**
	 * Configuration for theme
	 */

	TBGContext::getResponse()->addStylesheet('oxygen.css');

?>
<style>
	#tbg3_username { background-image: url('<?php echo TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . '/'; ?>user_mono.png'); }
	#tbg3_password { background-image: url('<?php echo TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . '/'; ?>password_mono.png'); }
	#openid-signin-button.persona-button span:after{ background-image: url('<?php echo TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . '/'; ?>openid_providers.small/openid.ico.png'); }
	#regular-signin-button.persona-button span:after{ background-image: url('<?php echo TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . '/'; ?>footer_logo.png'); }
	#forgot_password_username { background-image: url('<?php echo TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . '/'; ?>user_mono.png'); }
	.login_popup .article h1 { background: url('<?php echo TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . '/'; ?>logo_48.png') 0 0 no-repeat; }

	table.sortable tr th.sortcol.sortasc { background-image: url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/sort_up.png') !important; padding-left: 25px !important; }
	table.sortable tr th.sortcol.sortdesc { background-image: url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/sort_down.png') !important; padding-left: 25px !important; }
	table.sortable tr th.sortcol { background-image: none; padding-left: 5px !important; }

	.markItUp .markItUpButton1 a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/h1.png'); }
	.markItUp .markItUpButton2 a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/h2.png'); }
	.markItUp .markItUpButton3 a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/h3.png'); }
	.markItUp .markItUpButton4 a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/h4.png'); }
	.markItUp .markItUpButton5 a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/h5.png'); }
	.markItUp .markItUpButton6 a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/bold.png'); }
	.markItUp .markItUpButton7 a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/italic.png'); }
	.markItUp .markItUpButton8 a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/stroke.png'); }
	.markItUp .markItUpButton9 a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/list-bullet.png'); }
	.markItUp .markItUpButton10 a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/list-numeric.png'); }
	.markItUp .markItUpButton11 a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/picture.png'); }
	.markItUp .markItUpButton12 a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/link.png'); }
	.markItUp .markItUpButton13 a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/url.png'); }
	.markItUp .markItUpButton14 a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/quotes.png'); }
	.markItUp .markItUpButton15 a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/code.png'); }
	.markItUp .preview a { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/preview.png'); }
	.markItUpResizeHandle { background-image:url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/handle.png'); }
	.markItUpHeader ul .markItUpDropMenu { background-image: url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/menu.png'); }
	.markItUpHeader ul ul .markItUpDropMenu { background-image: url('<?php echo TBGContext::getTBGPath(); ?>iconsets/oxygen/markitup/submenu.png'); }

</style>