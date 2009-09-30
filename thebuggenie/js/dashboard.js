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
