<div class="logindiv openid_container">
	<form action="<?php echo make_url('login'); ?>" method="post" id="openid_form" onsubmit="return TBG.OpenID.submit();">
		<input type="hidden" name="action" value="verify" />
		<div id="openid_choice">
			<div class="login_boxheader">
				<?php if (TBGSettings::get('allowreg') && TBGSettings::getOpenIDStatus() == 'all'): ?>
					<?php echo __('Log in or register with your OpenID'); ?>
				<?php else: ?>
					<?php echo __('Log in with your OpenID'); ?>
				<?php endif; ?>
			</div>
			<div id="openid_btns"></div>
		</div>
		<div id="openid_input_area">
			<input id="openid_identifier" name="openid_identifier" type="text" value="http://" />
		</div>
		<input id="openid_provider" type="hidden" value="" />
		<input id="openid_submit_button" type="submit" value="<?php echo __('Sign in'); ?>" class="button button-silver" style="display: none;">
	</form>
</div>
<script type="text/javascript">

	var providers_large = {
		openid : {
			name : 'OpenID',
			label : '<?php echo __('Enter your OpenID'); ?>',
			url : null
		},
		myopenid : {
			name : 'MyOpenID',
			label : '<?php echo __('Enter your MyOpenID username'); ?>',
			url : 'http://{username}.myopenid.com/'
		},
		yahoo : {
			name : 'Yahoo',
			url : 'http://me.yahoo.com/'
		},
		google : {
			name : 'Google',
			url : 'https://www.google.com/accounts/o8/id'
		}
	};

	var providers_small = {
		livejournal : {
			name : 'LiveJournal',
			label : '<?php echo __('Enter your Livejournal username'); ?>',
			url : 'http://{username}.livejournal.com/'
		},
		aol : {
			name : 'AOL',
			label : '<?php echo __('Enter your AOL screenname'); ?>',
			url : 'http://openid.aol.com/{username}'
		},
		flickr: {
			name: 'Flickr',
			label : '<?php echo __('Enter your Flickr username'); ?>',
			url: 'http://flickr.com/{username}/'
		},
		technorati: {
			name: 'Technorati',
			label : '<?php echo __('Your Technorati username'); ?>',
			url: 'http://technorati.com/people/technorati/{username}/'
		},
		wordpress : {
			name : 'Wordpress',
			label : '<?php echo __('Enter your Wordpress.com username'); ?>',
			url : 'http://{username}.wordpress.com/'
		},
		blogger : {
			name : 'Blogger',
			label : '<?php echo __('Your Blogger account'); ?>',
			url : 'http://{username}.blogspot.com/'
		},
		verisign : {
			name : 'Verisign',
			label : '<?php echo __('Your Verisign username'); ?>',
			url : 'http://{username}.pip.verisignlabs.com/'
		},
		vidoop: {
			name: 'Vidoop',
			label : '<?php echo __('Your Vidoop username'); ?>',
			url: 'http://{username}.myvidoop.com/'
		},
		launchpad: {
			name: 'Launchpad',
			label : '<?php echo __('Your Launchpad username'); ?>',
			url: 'https://launchpad.net/~{username}'
		},
		claimid : {
			name : 'ClaimID',
			label : '<?php echo __('Your ClaimID username'); ?>',
			url : 'http://claimid.com/{username}'
		},
		clickpass : {
			name : 'ClickPass',
			label : '<?php echo __('Enter your ClickPass username'); ?>',
			url : 'http://clickpass.com/public/{username}'
		},
		google_profile : {
			name : 'Google Profile',
			label : '<?php echo __('Enter your Google Profile username'); ?>',
			url : 'http://www.google.com/profiles/{username}'
		}
	};

	TBG.OpenID.signin_text = '<?php echo __('Sign in'); ?>';
	TBG.OpenID.image_title = '<?php echo __('Log in with %openid_provider_name%'); ?>';
	TBG.OpenID.providers_small = providers_small;
	TBG.OpenID.providers_large = providers_large;
	<?php if ($tbg_request->isAjaxCall()): ?>
		TBG.OpenID.init();
	<?php endif; ?>
</script>