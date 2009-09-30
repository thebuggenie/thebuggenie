<script type="text/javascript">

function updateFilter(fcc)
{
	var params = Form.serialize('update_filter_' + fcc);
	new Ajax.Updater('filter_table', 'search.php?update_filters=true', {
	asynchronous:true,
	evalScripts: true,
	method: "post",
	parameters: params,
	onComplete: function (getbutton) {
		getSearchButton();
		Effect.Pulsate('filter_row_' + fcc, { pulses: 2, duration: 1 });
		}
	});
}

function removeFilter(fcc)
{
	Effect.Fade('filter_row_' + fcc, { duration: 0.5 });
	new Ajax.Updater('filter_table', 'search.php?update_filters=true&custom_search=true&remove_filter_cc=' + fcc, {
	asynchronous:true,
	evalScripts: true,
	method: "post",
	onSuccess: function (getbutton) {
		getSearchButton();
		}
	});
}

function addFilter()
{
	var params = Form.serialize('add_filter');
	new Ajax.Updater('filter_table', 'search.php?update_filters=true', {
	asynchronous:true,
	evalScripts: true,
	method: "post",
	parameters: params,
	onSuccess: function (getbutton) {
		getSearchButton();
		}
	});
}

function getSearchButton()
{
	new Ajax.Updater('search_button', 'search.php?get_search_button=true&custom_search=true', {
	asynchronous:true,
	evalScripts: true,
	method: "get"
	});
}

function doSearch()
{
	var params = Form.serialize('submit_search');
	new Ajax.Updater('search_results', 'search.php?perform_search=true', {
	asynchronous:true,
	evalScripts: true,
	parameters: params,
	method: "post",
	onSuccess: function (getbutton) {
		$('filter_table').hide();
		}
	});
}

</script>