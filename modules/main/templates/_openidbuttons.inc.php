<div class="logindiv openid_container">
	<form action="<?php echo make_url('login'); ?>" method="post" id="openid_form" onclick="return openid.submit();">
		<input type="hidden" name="action" value="verify" />
		<div id="openid_choice">
			<div class="login_boxheader"><?php echo __('Log in with your OpenID'); ?></div>
			<div id="openid_btns"></div>
		</div>
		<div id="openid_input_area">
			<input id="openid_identifier" name="openid_identifier" type="text" value="http://" />
		</div>
		<input type="submit" value="<?php echo __('Sign in'); ?>" class="button button-silver">
	</form>
</div>
<script type="text/javascript">

	/*
		Simple OpenID Plugin
		http://code.google.com/p/openid-selector/

		This code is licensed under the New BSD License.
	*/

	var providers;

	var openid = {
		version : '1.3', // version constant
		demo : false,
		demo_text : null,
		cookie_expires : 6 * 30, // 6 months.
		cookie_name : 'openid_provider',
		cookie_path : '/',

		img_path : 'images/',
		locale : null, // is set in openid-<locale>.js
		sprite : null, // usually equals to locale, is set in
		// openid-<locale>.js
		signin_text : null, // text on submit button on the form
		all_small : false, // output large providers w/ small icons
		no_sprite : false, // don't use sprite image
		image_title : '{provider}', // for image title

		input_id : null,
		provider_url : null,
		provider_id : null,

		/**
		 * Class constructor
		 *
		 * @return {Void}
		 */
		init : function(input_id) {
			providers = {};
			Object.extend(providers, providers_large);
			Object.extend(providers, providers_small);
			var openid_btns = $('openid_btns');
			this.input_id = input_id;
			$('openid_choice').setStyle({
				display: 'block'
			});
			$('openid_input_area').innerHTML = "";
			var i = 0;
			// add box for each provider
			for (id in providers_large) {
				box = this.getBoxHTML(id, providers_large[id], (this.all_small ? 'small' : 'large'), i++);
				openid_btns.insert(box);
			}
			if (providers_small) {
				openid_btns.insert('<br/>');
				for (id in providers_small) {
					box = this.getBoxHTML(id, providers_small[id], 'small', i++);
					openid_btns.insert(box);
				}
			}
	//		$('openid_form').submit = this.submit;
			var box_id = this.readCookie();
			if (box_id) {
				this.signin(box_id, true);
			}
		},

		/**
		 * @return {String}
		 */
		getBoxHTML : function(box_id, provider, box_size, index) {
			if (this.no_sprite) {
				var image_ext = box_size == 'small' ? '.ico.png' : '.png';
				return '<a title="' + this.image_title.replace('{provider}', provider["name"]) + '" href="javascript:openid.signin(\'' + box_id + '\');"'
						+ 'class="' + box_id + ' openid_' + box_size + '_btn button button-silver"><img src="../../iconsets/oxygen/openid_providers.' + box_size + '/' + box_id + image_ext + '"></a>';
			}
			var x = box_size == 'small' ? -index * 24 : -index * 100;
			var y = box_size == 'small' ? -60 : 0;
			return '<a title="' + this.image_title.replace('{provider}', provider["name"]) + '" href="javascript:openid.signin(\'' + box_id + '\');"'
					+ ' style="background: #FFF url(' + this.img_path + '../../iconsets/oxygen/openid-providers-' + this.sprite + '.png); background-position: ' + x + 'px ' + y + 'px" '
					+ 'class="' + box_id + ' openid_' + box_size + '_btn button button-silver"></a>';
		},

		/**
		 * Provider image click
		 *
		 * @return {Void}
		 */
		signin : function(box_id, onload) {
			var provider = providers[box_id];
			if (!provider) {
				return;
			}
			this.highlight(box_id);
			this.setCookie(box_id);
			this.provider_id = box_id;
			this.provider_url = provider['url'];
			// prompt user for input?
			if (provider['label']) {
				this.useInputBox(provider);
			} else {
				$('openid_input_area').innerHTML = '';
				if (!onload) {
					$('openid_form').submit();
				}
			}
		},

		/**
		 * Sign-in button click
		 *
		 * @return {Boolean}
		 */
		submit : function() {
			var url = openid.provider_url;
			var username_field = $('openid_username');
			var username = username_field ? $('openid_username').getValue() : '';
			console.log(username);
			console.log(url);
			if (url) {
				url = url.replace('{username}', username);
				openid.setOpenIdUrl(url);
			}
			if (url.indexOf("javascript:") == 0) {
				url = url.substr("javascript:".length);
				eval(url);
				return false;
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
				style = 'background: #FFF url(../../iconsets/oxygen/openid-inputicon.gif) no-repeat scroll 0 50%; padding-left:18px;';
			}
			html = '<input id="' + id + '" type="text" style="' + style + '" name="' + id + '" value="' + value + '" />';
			if (label) {
				html += '<label for="' + id + '">' + label + '</label>';
			}
					/*+ '<input id="openid_submit" type="submit" value="' + this.signin_text + '"/>'; */
			input_area.innerHTML = html;

	//		$('openid_submit').onclick = this.submit;
			$(id).focus();
		},

		setDemoMode : function(demoMode) {
			this.demo = demoMode;
		}
	};

	var providers_large = {
		openid : {
			name : 'OpenID',
			label : 'Enter your OpenID.',
			url : null
		},
		myopenid : {
			name : 'MyOpenID',
			label : 'Enter your MyOpenID username.',
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
			label : 'Enter your AOL screenname.',
			url : 'http://openid.aol.com/{username}'
		}, */
	};

	var providers_small = {
		livejournal : {
			name : 'LiveJournal',
			label : 'Enter your Livejournal username.',
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
			label : 'Enter your Wordpress.com username.',
			url : 'http://{username}.wordpress.com/'
		},
		blogger : {
			name : 'Blogger',
			label : 'Your Blogger account',
			url : 'http://{username}.blogspot.com/'
		},
		verisign : {
			name : 'Verisign',
			label : 'Your Verisign username',
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
			label : 'Your ClaimID username',
			url : 'http://claimid.com/{username}'
		},
		clickpass : {
			name : 'ClickPass',
			label : 'Enter your ClickPass username',
			url : 'http://clickpass.com/public/{username}'
		},
		google_profile : {
			name : 'Google Profile',
			label : 'Enter your Google Profile username',
			url : 'http://www.google.com/profiles/{username}'
		}
	};

	openid.locale = 'en';
	openid.sprite = 'en'; // reused in german& japan localization
	openid.demo_text = 'In client demo mode. Normally would have submitted OpenID:';
	openid.signin_text = 'Sign-In';
	openid.image_title = 'log in with {provider}';
	openid.no_sprite = true;
	openid.init('openid_identifier');
</script>