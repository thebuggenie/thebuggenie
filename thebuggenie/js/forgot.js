function resetForgotPassword(url)
{
	var params = Form.serialize('forgot_password_form');
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		parameters: params,
		onLoading: function (transport) {
			$('forgot_password_button').hide();
			$('forgot_password_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				$('forgot_password_indicator').hide();
				$('forgot_password_button').show();
				failedMessage(json.error);
			}
			else
			{
				$('forgot_password_indicator').hide();
				$('forgot_password_button').show();
				successMessage(json.message);
			}
		},
		onFailure: function (transport) {
			$('forgot_password_indicator').hide();
			$('forgot_password_button').show();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				Form.reset('forgot_password_form');
				failedMessage(json.error);
			}
		}
	});
}