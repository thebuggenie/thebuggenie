function importCSV(url)
{
	var params = Form.serialize('import_csv_form');
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		parameters: params,
		onLoading: function (transport) {
			$('csv_import_indicator').show();
			$('csv_import_error').hide();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				thebuggenie.events.failedMessage(json.error);
				$('csv_import_indicator').hide();
			}
			else
			{
				$('csv_import_indicator').hide();
				thebuggenie.events.successMessage(json.title, json.message);
			}
		},
		onFailure: function (transport) {
			$('csv_import_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
				$('csv_import_error_detail').update(json.errordetail);
				$('csv_import_error').show();
			}
		}
	});
}