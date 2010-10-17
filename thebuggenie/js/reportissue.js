function updateFields(url, projectmenustripurl)
{
	if ($('issuetype_id').getValue() != 0)
	{
		$('issuetype_list').hide();
		$('issuetype_dropdown').show();
	}
	if (projectmenustripurl != '' && $('project_id').getValue() != 0)
	{
		updateProjectMenuStrip(projectmenustripurl, $('project_id').getValue());
	}
	if ($('project_id').getValue() != 0 && $('issuetype_id').getValue() != 0)
	{
		$('report_more_here').hide();
		$('report_form').show();
		$('report_issue_more_options_indicator').show();
		
		new Ajax.Request(url, {
			method: 'post',
			parameters: { project_id: $('project_id').getValue(), issuetype_id: $('issuetype_id').getValue() },
			requestHeaders: {Accept: 'application/json'},
			onSuccess: function(transport) {
				var json = transport.responseJSON;
				available_fields = json.available_fields;
				fields = json.fields;
				available_fields.each(function (key, index)
				{
					if ($(key + '_div'))
					{
						if (fields[key])
						{
							if (fields[key].values)
							{
								var prev_val;
								if ($(key + '_additional') && $(key + '_additional').visible())
								{
									prev_val = $(key + '_id_additional').getValue();
								}
								else if ($(key + '_div') && $(key + '_div').visible())
								{
									prev_val = $(key + '_id').getValue();
								}
							}
							if (fields[key].additional && $(key + '_additional'))
							{
								$(key + '_additional').show();
								$(key + '_div').hide();
								if ($(key + '_id_additional')) $(key + '_id_additional').enable();
								if ($(key + '_id')) $(key + '_id').disable();
								
								if (fields[key].values)
								{
									$(key + '_id_additional').update('');
									for (var opt in fields[key].values)
									{
										$(key + '_id_additional').insert('<option value="'+opt+'">'+fields[key].values[opt]+'</option>');
									}
									$(key + '_id_additional').setValue(prev_val);
								}
							}
							else
							{
								$(key + '_div').show();
								if ($(key + '_id')) $(key + '_id').enable();
								if ($(key + '_additional')) $(key + '_additional').hide();
								if ($(key + '_id_additional')) $(key + '_id_additional').disable();
								if (fields[key].values)
								{
									$(key + '_id').update('');
									for (var opt in fields[key].values)
									{
										$(key + '_id').insert('<option value="'+opt+'">'+fields[key].values[opt]+'</option>');
									}
									$(key + '_id').setValue(prev_val);
								}
							}
							(fields[key].required) ? $(key + '_label').addClassName('required') : $(key + '_label').removeClassName('required');
						}
						else
						{
							$(key + '_div').hide();
							if ($(key + '_id')) $(key + '_id').disable();
							if ($(key + '_additional')) $(key + '_additional').hide();
							if ($(key + '_id_additional')) $(key + '_id_additional').disable();
						}
					}
				});				
				$('report_issue_more_options_indicator').hide();
			},
			onFailure: function(transport) {
				$('report_issue_more_options_indicator').hide();
			}
		});
	}
	else
	{
		$('report_form').hide();
		$('report_more_here').show();
	}
	
}
