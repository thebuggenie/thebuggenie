function loginRegister1(url)
{
	var params = Form.serialize('register1_form');
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		parameters: params,
		onLoading: function (transport) {
			$('register1_button').hide();
			$('register1_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				Form.reset('register1_form');
				$('register1_indicator').hide();
				$('register1_button').show();			
				failedMessage(json.error);
			}
			else
			{
				$('register1_indicator').hide();
				$('register1').hide();
				$('username').value = json.message.unescapeHTML();
				$('fieldusername').value = json.message.unescapeHTML();
				$('register2').show();
			}
		},
		onFailure: function (transport) {
			$('register1_indicator').hide();
			$('register1_button').show();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
		}
	});
}

function loginRegister2(url)
{
	var params = Form.serialize('register2_form');
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		parameters: params,
		onLoading: function (transport) {
			$$('input.required').each(function(field) {
				$(field).setStyle({ backgroundColor: '' });
			});
			$('register2_button').hide();
			$('register2_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				$('register2_indicator').hide();
				$('register2_button').show();
				json.fields.each(function(field) {
					$(field).setStyle({ backgroundColor: '#FBB' });
				});	
				failedMessage(json.error);
			}
			else
			{
				$('register2_indicator').hide();
				$('register2').hide();
				$('register_message').update(json.message);
				$('register_message').innerHTML;
				$('register3').show();
			}
		},
		onFailure: function (transport) {
			$('register2_indicator').hide();
			$('register2_button').show();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
		}
	});
}

function loginUser(url)
{
	var params = Form.serialize('login_form');
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		parameters: params,
		onLoading: function (transport) {
			$('login_button').hide();
			$('login_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				Form.reset('login_form');
				$('tbg3_referer').value = json.referer;
				$('login_indicator').hide();
				$('login_button').show();
				failedMessage(json.error);
			}
			else
			{
				document.location = json.forward;
			}
		},
		onFailure: function (transport) {
			$('login_indicator').hide();
			$('login_button').show();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				Form.reset('login_form');
				$('tbg3_referer').value = json.referer;
				failedMessage(json.error);
			}
		}
	});
}