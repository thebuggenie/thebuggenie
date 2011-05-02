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
				failedMessage(json.error);
				$('csv_import_indicator').hide();
			}
			else
			{
				$('csv_import_indicator').hide();
				successMessage(json.title, json.message);
			}
		},
		onFailure: function (transport) {
			$('csv_import_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
				$('csv_import_error_detail').update(json.errordetail);
				$('csv_import_error').show();
			}
		}
	});
}

function updatecheck(url)
{
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		onLoading: function (transport) {
			$('update_button').hide();
			$('update_spinner').show();
		},
		onSuccess: function (transport) {
			$('update_button').show();
			$('update_spinner').hide();
			var json = transport.responseJSON;
			if (json && (json.failed))
			{
				failedMessage(json.title, json.message);
			}
			else
			{
				if (json.uptodate)
				{
					successMessage(json.title, json.message);
				}
				else
				{
					failedMessage(json.title, json.message);
				}
			}
		},
		onFailure: function (transport) {
			$('update_button').show();
			$('update_spinner').hide();
			var json = transport.responseJSON;
			if (json && (json.failed))
			{
				failedMessage(json.title, json.message);
			}
		}
	});
}