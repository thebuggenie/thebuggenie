function toggleFavourite(url, issue_id)
{
	$('issue_favourite_indicator_' + issue_id).show();
	$('issue_favourite_normal_' + issue_id).hide();
	$('issue_favourite_faded_' + issue_id).hide();
	new Ajax.Request(url, {
		method: 'post',
		parameters: { issue_id: issue_id },
		requestHeaders: {Accept: 'application/json'},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			if (Object.isUndefined(json.starred) == false)
			{
				if (json.starred)
				{
					$('issue_favourite_faded_' + issue_id).hide();
					$('issue_favourite_indicator_' + issue_id).hide();
					$('issue_favourite_normal_' + issue_id).show();
				}
				else
				{
					$('issue_favourite_normal_' + issue_id).hide();
					$('issue_favourite_indicator_' + issue_id).hide();
					$('issue_favourite_faded_' + issue_id).show();
				}
			}
			else
			{
				$('issue_favourite_normal_' + issue_id).hide();
				$('issue_favourite_indicator_' + issue_id).hide();
				$('issue_favourite_faded_' + issue_id).show();
			}
			$('issue_favourite_indicator_' + issue_id).hide();
		},
		onFailure: function(transport) {
			$('issue_favourite_indicator_' + issue_id).hide();
		}
	});
}

function swapDashboardView(el1, el2)
{
	var source = el1.innerHTML;
	var target = el2.innerHTML;
	
	el1.innerHTML = target;
	el2.innerHTML = source;
	
	source = el1.id;
	target = el2.id;
	
	el1.id = target;
	el2.id = source;
	
	if (el2.className == 'template_view')
	{ 
		el2.className = '';
		el1.ancestors()[0].remove();
	}
	
	el1.ancestors()[3].hide();
}

function addDashboardView()
{
	var element_view = $('view_default').clone(true);
	element_view.id = 'view_' + new Date().getTime();
	$('views_list').insert(element_view);
	element_view = null;
	
	Sortable.create('views_list', {constraint: ''});
}

function saveDashboard(url)
{
	var countViews = document.evaluate( 'count(//ul[@id="views_list"]/li)', document, null, XPathResult.NUMBER_TYPE, null ).numberValue;
	myViews = new Array();
	var params = 'id=';
	for(i = 0 ; i < countViews ; i++)
	{
		params = params + document.getElementById('views_list').childNodes[i].childNodes[1].getAttribute('id') + ';';
	}

	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		parameters: params,
		onLoading: function (transport) {
			$('save_dashboard_indicator').show();
			$('save_dashboard').hide();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				failedMessage(json.error);
			}
			else
			{
				successMessage(json.message);
			}
			$('save_dashboard_indicator').hide();
			$('save_dashboard').show();
		},
		onFailure: function (transport) {
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
			$('save_dashboard_indicator').hide();
			$('save_dashboard').show();
		}
	});	
}