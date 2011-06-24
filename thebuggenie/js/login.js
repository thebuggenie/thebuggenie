TBG.Main.Login.checkUsername = function(url)
{
	TBG.Main.Helpers.ajax(url, {
		form: 'register1_form',
		loading: {
			indicator: 'register1_indicator',
			hide: 'register1_button'
		},
		success: {
			hide: 'register1',
			show: 'register2',
			callback: function(json) {
				$('username').value = json.message.unescapeHTML();
				$('fieldusername').value = json.message.unescapeHTML();
			}
		},
		failure: {
			reset: 'register1_form',
			show: 'register1_button'
		}
	});
}

TBG.Main.Login.register = function(url)
{
	TBG.Main.Helpers.ajax(url, {
		form: 'register2_form',
		loading: {
			indicator: 'register2_indicator',
			hide: 'register2_button',
			callback: function() {
				$$('input.required').each(function(field) {
					$(field).setStyle({backgroundColor: ''});
				});
			}
		},
		success: {
			hide: 'register2',
			update: {element: 'register_message', from: 'message'},
			show: 'register3'
		},
		failure: {
			show: 'register2_button',
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