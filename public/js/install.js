
function testDBConnection()
{
	var params = Form.serialize('database_connection');
	new Ajax.Updater('connection_status', 'install.php?test_connection=true', {
		asynchronous:true,
		method: "post",
		parameters: params,
		evalScripts: true,
		onSuccess: function(success) {
			Element.hide('continue_button');
		}
	});
}

function updateURLPreview()
{
	if ($('url_host').value.empty() || $('url_subdir').value.empty())
	{
		Element.hide('continue_button');
		Element.show('continue_error');
		$('url_preview').update('<b>!! </b>You need to fill out both server and directory url.<br />If BUGS is located directly under the server, end the server url <i>without</i> a forward slash, and put a single forward slash in the directory url.');
	}
	else if($F($('bugs_settings')['url_host']).endsWith('/') == true || ($F($('bugs_settings')['url_subdir']).endsWith('/') == false || $F($('bugs_settings')['url_subdir']).startsWith('/') == false))
	{
		Element.hide('continue_button');
		Element.show('continue_error');
		$('url_preview').update('<b>!! </b>The server url <i>cannot end with a forward slash</i>, and the directory url <i>must start and end with a forward slash</i>');
	}
	else 
	{ 
		Element.show('continue_button');
		Element.hide('continue_error');
		$('url_preview').update($('url_host').value + $('url_subdir').value + 'index.php');
	}
	
	var new_url = $('url_host').value + $('url_subdir').value;
	
	if (new_url.endsWith('//'))
	{
		Element.hide('continue_button');
		Element.show('continue_error');
		$('url_preview').update('<b>!! </b>The complete url <i><b>cannot end with two forward slashes</b></i>. If BUGS is located directly under the server, end the server url <i><b>without</b></i> a forward slash, and put <i><b>a single forward slash</b></i> as the directory url.');
	}
}

function submitSettings()
{
	var params = Form.serialize('bugs_settings');
	new Ajax.Updater('installation_status', 'install.php?submit_settings=true', {
		asynchronous:true,
		method: "post",
		parameters: params,
		evalScripts: true,
		onSuccess: function(success) {
			Element.hide('continue_button');
			Element.hide('server_information');
		}
	});
}

function updateSettings()
{
	var params = Form.serialize('bugs_updated_settings');
	new Ajax.Updater('installation_status_2', 'install.php?update_settings=true', {
		asynchronous:true,
		method: "post",
		parameters: params,
		evalScripts: true,
		onSuccess: function(success) {
			Element.hide('continue_button_2');
			Element.hide('new_settings');
		}
	});
}

function finishInstallation()
{
	var params = Form.serialize('module_selection_form');
	new Ajax.Updater('finish_installation', 'install.php?finish_installation=true', {
		asynchronous:true,
		method: "post",
		parameters: params,
		evalScripts: true,
		onSuccess: function(success) {
			Element.hide('select_modules');
		}
	});
}