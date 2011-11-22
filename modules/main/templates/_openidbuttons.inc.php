<div class="logindiv openid_container">
	<form action="<?php echo make_url('login'); ?>" method="post" id="openid_form" onclick="return openid.submit();">
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
		<input type="submit" value="<?php echo __('Sign in'); ?>" class="button button-silver">
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
		/* aol : {
			name : 'AOL',
			label : '<?php echo __('Enter your AOL screenname'); ?>',
			url : 'http://openid.aol.com/{username}'
		}, */
	};

	var providers_small = {
		livejournal : {
			name : 'LiveJournal',
			label : '<?php echo __('Enter your Livejournal username'); ?>',
			url : 'http://{username}.livejournal.com/'
		},
		/* flickr: {
			name: 'Flickr',
			label: 'Enter your Flickr username.',
			url: 'http://flickr.com/{username}/'
		}, */
		/* technorati: {
			name: 'Technorati',
			label: 'Enter your Technorati username.',
			url: 'http://technorati.com/people/technorati/{username}/'
		}, */
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
		/* vidoop: {
			name: 'Vidoop',
			label: 'Your Vidoop username',
			url: 'http://{username}.myvidoop.com/'
		}, */
		/* launchpad: {
			name: 'Launchpad',
			label: 'Your Launchpad username',
			url: 'https://launchpad.net/~{username}'
		}, */
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

	openid.locale = 'en';
	openid.sprite = 'en'; // reused in german& japan localization
	openid.signin_text = '<?php echo __('Sign in'); ?>';
	openid.image_title = '<?php echo __('Log in with %openid_provider_name%'); ?>';
	openid.no_sprite = true;
	openid.providers_small = providers_small;
	openid.providers_large = providers_large;
	openid.init('openid_identifier');
</script>