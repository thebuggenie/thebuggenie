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
									$(key + '_id_additional').update('');
								}
								else if ($(key + '_div') && $(key + '_div').visible())
								{
									prev_val = $(key + '_id').getValue();
									$(key + '_id').update('');
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
				/*if (Object.isUndefined(json.description) == false)
				{
					$('description_div').show();
					(json.description.required) ? $('description_label').addClassName('required') : $('description_label').removeClassName('required');
				}
				else
				{
					$('description_div').hide();
				}

				if (Object.isUndefined(json.reproduction_steps) == false)
				{
					$('reproduction_steps_div').show();
					(json.reproduction_steps.required) ? $('reproduction_steps_label').addClassName('required') : $('reproduction_steps_label').removeClassName('required');
				}
				else
				{
					$('reproduction_steps_div').hide();
				}
				
				if (Object.isUndefined(json.edition) == false && Object.isUndefined(json.edition.editions) == false)
				{
					var prev_val = $('edition_id').getValue();
					$('edition_id').update('');
					for (var opt in json.edition.editions) {
						$('edition_id').insert('<option value="'+opt+'">'+json.edition.editions[opt]+'</option>');
					}
					$('edition_id').setValue(prev_val);
					$('edition_div').show();
					(json.edition.required) ? $('edition_label').addClassName('required') : $('edition_label').removeClassName('required');
				}
				else
				{
					$('edition_div').hide();
				}
				
				if (Object.isUndefined(json.build) == false && Object.isUndefined(json.build.builds) == false)
				{
					var prev_val = $('build_id').getValue();
					$('build_id').update('');
					for (var opt in json.build.builds) {
						$('build_id').insert('<option value="'+opt+'">'+json.build.builds[opt]+'</option>');
					}
					$('build_id').setValue(prev_val);
					$('build_div').show();
					(json.build.required) ? $('build_label').addClassName('required') : $('build_label').removeClassName('required');
				}
				else
				{
					$('build_div').hide();
				}
				
				if (Object.isUndefined(json.component) == false && Object.isUndefined(json.component.components) == false)
				{
					var prev_val = $('component_id').getValue();
					$('component_id').update('');
					for (var opt in json.component.components) {
						$('component_id').insert('<option value="'+opt+'">'+json.component.components[opt]+'</option>');
					}
					$('component_id').setValue(prev_val);
					$('component_div').show();
					(json.component.required) ? $('component_label').addClassName('required') : $('component_label').removeClassName('required');
				}
				else
				{
					$('component_div').hide();
				}
				
				if (Object.isUndefined(json.category) == false)
				{
					if (json.category.additional == false)
					{
						$('category_div').show();
						$('category_additional').hide();
						$('category_id_additional').disable();
						$('category_id').enable();
						(json.category.required) ? $('category_label').addClassName('required') : $('category_label').removeClassName('required');
					}
					else
					{
						$('category_additional').show();
						$('category_div').hide();
						$('category_id').disable();
						$('category_id_additional').enable();
					}						
				}
				else
				{
					$('category_additional').hide();
					$('category_div').hide();
				}

				if (Object.isUndefined(json.resolution) == false)
				{
					var div_id = (json.resolution.additional == true) ? 'resolution_additional' : 'resolution_div';
					if (json.resolution.additional == false)
					{
						$('resolution_div').show();
						$('resolution_additional').hide();
						$('resolution_id_additional').disable();
						$('resolution_id').enable();
						(json.resolution.required) ? $('resolution_label').addClassName('required') : $('resolution_label').removeClassName('required');
					}
					else
					{
						$('resolution_additional').show();
						$('resolution_div').hide();
						$('resolution_id').disable();
						$('resolution_id_additional').enable();
					}						
				}
				else
				{
					$('resolution_additional').hide();
					$('resolution_div').hide();
				}
				
				if (Object.isUndefined(json.reproducability) == false)
				{
					var div_id = (json.reproducability.additional == true) ? 'reproducability_additional' : 'reproducability_div';
					if (json.reproducability.additional == false)
					{
						$('reproducability_div').show();
						$('reproducability_additional').hide();
						$('reproducability_id_additional').disable();
						$('reproducability_id').enable();
						(json.reproducability.required) ? $('reproducability_label').addClassName('required') : $('reproducability_label').removeClassName('required');
					}
					else
					{
						$('reproducability_additional').show();
						$('reproducability_div').hide();
						$('reproducability_id').disable();
						$('reproducability_id_additional').enable();
					}						
				}
				else
				{
					$('reproducability_additional').hide();
					$('reproducability_div').hide();
				}
				
				if (Object.isUndefined(json.severity) == false)
				{
					var div_id = (json.severity.additional == true) ? 'severity_additional' : 'severity_div';
					if (json.severity.additional == false)
					{
						$('severity_div').show();
						$('severity_additional').hide();
						$('severity_id_additional').disable();
						$('severity_id').enable();
						(json.severity.required) ? $('severity_label').addClassName('required') : $('severity_label').removeClassName('required');
					}
					else
					{
						$('severity_div').hide();
						$('severity_additional').show();
						$('severity_id').disable();
						$('severity_id_additional').enable();
					}						
				}
				else
				{
					$('severity_additional').hide();
					$('severity_div').hide();
				}

				if (Object.isUndefined(json.priority) == false)
				{
					var div_id = (json.priority.additional == true) ? 'priority_additional' : 'priority_div';
					if (json.priority.additional == false)
					{
						$('priority_div').show();
						$('priority_id_additional').disable();
						$('priority_additional').hide();
						$('priority_id').enable();
						(json.priority.required) ? $('priority_label').addClassName('required') : $('priority_label').removeClassName('required');
					}
					else
					{
						$('priority_additional').show();
						$('priority_div').hide();
						$('priority_id').disable();
						$('priority_id_additional').enable();
					}						
				}
				else
				{
					$('priority_additional').hide();
					$('priority_div').hide();
				}

				if (Object.isUndefined(json.status) == false)
				{
					var div_id = (json.status.additional == true) ? 'status_additional' : 'status_div';
					if (json.status.additional == false)
					{
						$('status_div').show();
						$('status_id_additional').disable();
						$('status_id').enable();
						(json.status.required) ? $('status_label').addClassName('required') : $('status_label').removeClassName('required');
					}
					else
					{
						$('status_additional').show();
						$('status_id').disable();
						$('status_id_additional').enable();
					}						
				}
				else
				{
					$('status_additional').hide();
					$('status_div').hide();
				}*/
				
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
