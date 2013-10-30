<?php

	/**
	 * Configuration for theme
	 */

	TBGContext::getResponse()->addStylesheet('oxygen.css');

?>
<style>
	.login_popup .article h1 {
		background: url('<?php echo TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . '/'; ?>logo_48.png') 0 0 no-repeat;
	}
	#tbg3_username {
		background-image: url('<?php echo TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . '/'; ?>user_mono.png');
	}
	#tbg3_password {
		background-image: url('<?php echo TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . '/'; ?>password_mono.png');
	}
	#openid-signin-button.persona-button span:after{
		background-image: url('<?php echo TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . '/'; ?>openid_providers.small/openid.ico.png');
	}
	#regular-signin-button.persona-button span:after{
		background-image: url('<?php echo TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . '/'; ?>footer_logo.png');
	}
	#forgot_password_username {
		background-image: url('<?php echo TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . '/'; ?>user_mono.png');
	}

</style>