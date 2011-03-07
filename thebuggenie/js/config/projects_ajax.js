thebuggenie.events.updateProjectLinks = function(json)
{
	if ($('current_project_num_count'))
	{
		$('current_project_num_count').update(json.total_count);
	}
	if (json.more_available)
	{
		$('add_project_div').show();
	}
	else
	{
		$('add_project_div').hide();
	}
}

thebuggenie.events.addProject = function(url)
{
	var params = Form.serialize('add_project_form');
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		parameters: params,
		onLoading: function (transport) {
			$('project_add_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				thebuggenie.events.failedMessage(json.error);
				$('project_add_indicator').hide();
			}
			else
			{
				Form.reset('add_project_form');
				$('noprojects_tr').hide();
				$('project_add_indicator').hide();
				thebuggenie.events.successMessage(json.title, json.message);
				$('project_table').insert({bottom: json.content});
				updateProjectLinks(json);
			}
		},
		onFailure: function (transport) {
			$('project_add_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.removeProject = function(url, pid)
{
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	requestHeaders: {Accept: 'application/json'},
	onLoading: function (transport) {
		$('project_delete_controls_' + pid).hide();
		$('project_delete_indicator_' + pid).show();
	},
	onSuccess: function(transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			$('project_delete_controls_' + pid).show();
			$('project_delete_indicator_' + pid).hide();
			thebuggenie.events.failedMessage(json.error);
		}
		else
		{
			$('project_delete_indicator_' + pid).remove();
			$('project_delete_confirm_' + pid).remove();
			$('project_box_' + pid).remove();
			if ($('project_table').childElements().size() == 0)
			{
				$('noprojects_tr').show();
			}
			updateProjectLinks(json);
			thebuggenie.events.successMessage(json.title);
		}
	},
	onFailure: function (transport) {
		$('project_delete_controls_' + pid).show();
		$('project_delete_error_' + pid).show();
		var json = transport.responseJSON;
		if (json && (json.failed || json.error))
		{
			thebuggenie.events.failedMessage(json.error);
		}
	}
	});
}

thebuggenie.events.addMilestone = function(url)
{
	var params = Form.serialize('add_milestone_form');
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		parameters: params,
		onLoading: function (transport) {
			$('milestone_add_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				thebuggenie.events.failedMessage(json.error);
				$('milestone_add_indicator').hide();
			}
			else
			{
				Form.reset('add_milestone_form');
				$('no_milestones').hide();
				$('milestone_add_indicator').hide();
				thebuggenie.events.successMessage(json.title, json.message);
				$('milestone_list').insert({bottom: json.content});
			}
		},
		onFailure: function (transport) {
			$('milestone_add_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.doBuildAction = function(url, bid, action, update)
{
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		onLoading: function (transport) {
			$('build_'+bid+'_indicator').show();
			$('build_'+bid+'_info').hide();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
				$('build_'+bid+'_indicator').hide();
				$('build_'+bid+'_info').show();
			}
			else
			{
				$('build_'+bid+'_indicator').hide();
				$('build_'+bid+'_info').show();
				if (update == 'all')
				{
					$('build_table').update(transport.responseText);
				}
				else
				{
					$('build_list_' + bid).update(transport.responseText);
				}
			}
		},
		onFailure: function (transport) {
			$('build_'+bid+'_indicator').hide();
			$('build_'+bid+'_info').show();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.updateBuild = function(url, bid)
{
	var params = Form.serialize('edit_build_'+bid);
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		parameters: params,
		onLoading: function (transport) {
			$('build_'+bid+'_indicator').show();
			$('build_'+bid+'_info').hide();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
				$('build_'+bid+'_indicator').hide();
				$('build_'+bid+'_info').show();
			}
			else
			{
				$('build_'+bid+'_indicator').hide();
				$('build_'+bid+'_info').show();
				$('build_list_' + bid).update(transport.responseText);
			}
		},
		onFailure: function (transport) {
			$('build_'+bid+'_indicator').hide();
			$('build_'+bid+'_info').show();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.saveProjectOther = function(url)
{
	var params = Form.serialize('project_other');
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		parameters: params,
		requestHeaders: {Accept: 'application/json'},
		onLoading: function (transport) {
			$('settings_save_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				$('settings_save_indicator').hide();
				thebuggenie.events.failedMessage(json.error);
			}
			else
			{
				$('settings_save_indicator').hide();
				thebuggenie.events.successMessage(json.title, json.message);
			}	
		},
		onFailure: function (transport) {
			$('settings_save_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.addToOpenBuild = function(url, bid)
{
	var params = Form.serialize('addtoopen_build_'+bid);
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		parameters: params,
		requestHeaders: {Accept: 'application/json'},
		onLoading: function (transport) {
			$('build_'+bid+'_indicator').show();
			$('build_'+bid+'_info').hide();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				$('build_'+bid+'_indicator').hide();
				$('build_'+bid+'_info').show();
				$('addtoopen_build_'+bid).hide();
				thebuggenie.events.failedMessage(json.title, json.message);
			}
			else
			{
				$('build_'+bid+'_indicator').hide();
				$('build_'+bid+'_info').show();
				$('addtoopen_build_'+bid).hide();
				thebuggenie.events.successMessage(json.title, json.message);
			}
		},
		onFailure: function (transport) {
			$('build_'+bid+'_indicator').hide();
			$('build_'+bid+'_info').show();
			$('addtoopen_build_'+bid).hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.deleteBuild = function(url, bid)
{
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		requestHeaders: {Accept: 'application/json'},
		onLoading: function (transport) {
			$('build_'+bid+'_indicator').addClassName('selected_red');
			$('build_'+bid+'_indicator').show();
			$('build_'+bid+'_info').hide();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (!json.deleted)
			{
				$('build_'+bid+'_indicator').removeClassName('selected_red');
				$('build_'+bid+'_indicator').hide();
				$('build_'+bid+'_info').show();
				$('del_build_'+bid).hide();
				thebuggenie.events.failedMessage(json.error);
			}
			else
			{
				if ($('build_list_'+bid))
				{
					$('build_list_'+bid).remove();
				}
				else
				{
					$('buildbox_'+bid).remove();
				}
				if ($('build_table').childElements().size() == 0)
				{
					$('no_builds').show();
				}
				thebuggenie.events.successMessage(json.message);
			}
		},
		onFailure: function (transport) {
			$('build_'+bid+'_indicator').removeClassName('selected_red');
			$('build_'+bid+'_indicator').hide();
			$('build_'+bid+'_info').show();
			$('del_build_'+bid).hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.deleteComponent = function(url, cid)
{
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		requestHeaders: {Accept: 'application/json'},
		onLoading: function (transport) {
			$('component_'+cid+'_delete_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (!json.deleted)
			{
				$('component_'+cid+'_delete_indicator').hide();
				$('del_component_'+cid).hide();
				thebuggenie.events.failedMessage(json.error);
			}
			else
			{
				$('show_component_'+cid).remove();
				$('edit_component_'+cid).remove();
				$('component_'+cid+'_permissions').remove();
				if (json.itemcount == 0)
				{
					$('no_components').show();
				}
				thebuggenie.events.successMessage(json.message);
			}
		},
		onFailure: function (transport) {
			$('component_'+cid+'_delete_indicator').hide();
			$('del_component_'+cid).hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.deleteEdition = function(url, eid)
{
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		requestHeaders: {Accept: 'application/json'},
		onLoading: function (transport) {
			$('edition_'+eid+'_delete_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (!json.deleted)
			{
				$('edition_'+eid+'_delete_indicator').hide();
				$('del_edition_'+eid).hide();
				thebuggenie.events.failedMessage(json.error);
			}
			else
			{
				$('edition_box_'+eid).remove();
				$('edition_'+eid+'_permissions').remove();
				if (json.itemcount == 0)
				{
					$('no_editions').show();
				}
				thebuggenie.events.successMessage(json.message);
			}
		},
		onFailure: function (transport) {
			$('edition_'+eid+'_delete_indicator').hide();
			$('del_edition_'+eid).hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.switchEditionTab = function(select_tab)
{
	$('edition_settings').hide();
	$('tab_edition_settings').removeClassName('selected');
	$('edition_components').hide();
	$('tab_edition_components').removeClassName('selected');
	$('edition_builds').hide();
	$('tab_edition_builds').removeClassName('selected');
	$('edition_'+select_tab).show();
	$('tab_edition_'+select_tab).addClassName('selected');
}

thebuggenie.events.addEdition = function(url)
{
	var params = Form.serialize('add_edition_form');
		new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		parameters: params,
		onLoading: function (transport) {
			$('edition_add_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				thebuggenie.events.failedMessage(json.error);
				$('edition_add_indicator').hide();
			}
			else
			{
				Form.reset('add_edition_form');
				$('edition_add_indicator').hide();
				$('no_editions').hide();
				thebuggenie.events.successMessage(json.title, json.message);
				$('edition_table').update($('edition_table').innerHTML + json.html);
			}
		},
		onFailure: function (transport) {
			$('edition_add_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.addBuild = function(url)
{
	var params = Form.serialize('add_build_form');
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		parameters: params,
		onLoading: function (transport) {
			$('build_add_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				thebuggenie.events.failedMessage(json.error);
				$('build_add_indicator').hide();
			}
			else
			{
				$('build_table').update($('build_table').innerHTML + json.html);
				$('no_builds').hide();
				$('build_add_indicator').hide();
				thebuggenie.events.successMessage(json.title, json.message);
			}
		},
		onFailure: function (transport) {
			$('build_add_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.addComponent = function(url)
{
	var params = Form.serialize('add_component_form');
		new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		evalScripts: true,
		parameters: params,
		onLoading: function (transport) {
			$('component_add_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				thebuggenie.events.failedMessage(json.error);
				$('component_add_indicator').hide();
			}
			else
			{
				Form.reset('add_component_form');
				$('component_add_indicator').hide();
				thebuggenie.events.successMessage(json.title, json.message);
				$('no_components').hide();
				$('component_table').update($('component_table').innerHTML + json.html);
			}
		},
		onFailure: function (transport) {
			$('component_add_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.submitProjectSettings = function(url)
{
thebuggenie.utils.submitProjectDetails(url, 'project_settings');
}

thebuggenie.events.submitProjectInfo = function(url, pid)
{
thebuggenie.utils.submitProjectDetails(url, 'project_info', pid);
}

thebuggenie.utils.submitProjectDetails = function(url, form, pid)
{
	var params = Form.serialize(form);
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		requestHeaders: {Accept: 'application/json'},
		parameters: params,
		onLoading: function (transport) {
			$(form + '_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				$(form + '_indicator').hide();
				thebuggenie.events.failedMessage(json.error);
			}
			else
			{
				$(form + '_indicator').hide();
				if ($('project_name_span'))
				{
					$('project_name_span').update($('project_name_input').getValue());
				}
				if ($('project_description_span'))
				{
					if ($('project_description_input').getValue())
					{
						$('project_description_span').update(json.project_description);
						$('project_no_description').hide();
					}
					else
					{
						$('project_description_span').update('');
						$('project_no_description').show();
					}
				}
				if ($('project_key_span'))
				{
					$('project_key_span').update(json.project_key);
				}
				if ($('sidebar_link_scrum') && $('use_scrum').getValue() == 1)
				{
					$('sidebar_link_scrum').show();
				}
				else if ($('sidebar_link_scrum'))
				{
					$('sidebar_link_scrum').hide();
				}
				if ($('enable_editions').getValue() == 1)
				{
					$('add_edition_form').show();
					$('project_editions').show();
					$('project_editions_disabled').hide();
				}
				else
				{
					$('add_edition_form').hide();
					$('project_editions').hide();
					$('project_editions_disabled').show();
				}
				if ($('enable_components').getValue() == 1)
				{
					$('add_component_form').show();
					$('project_components').show();
					$('project_components_disabled').hide();
				}
				else
				{
					$('add_component_form').hide();
					$('project_components').hide();
					$('project_components_disabled').show();
				}
				if ($('enable_builds').getValue() == 1)
				{
					$('add_build_form').show();
					$('project_builds').show();
					$('project_builds_disabled').hide();
				}
				else
				{
					$('add_build_form').hide();
					$('project_builds').hide();
					$('project_builds_disabled').show();
				}
				
				if (pid != undefined)
				{
					if ($('project_box_' + pid) != undefined)
					{
						$('project_box_' + pid).update(json.content);
					}
				}
				
				thebuggenie.events.successMessage(json.title, json.message);
			}
		},
		onFailure: function (transport) {
			$(form + '_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.submitEditionSettings = function(url)
{
thebuggenie.utils.postFormWithJSONFeedback(url, 'edition_settings_form', 'edition_save_indicator');
}

thebuggenie.events.addEditionComponent = function(url, cid)
{
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		requestHeaders: {Accept: 'application/json'},
		onLoading: function (transport) {
			$('project_component_'+cid).fade();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				thebuggenie.events.failedMessage(json.error);
			}
			else
			{
				$('edition_component_count').value++;
				$('edition_component_'+cid).appear();
				$('edition_no_components').hide();
			}
		},
		onFailure: function (transport, response) {
			$('project_component_'+cid).appear();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.removeEditionComponent = function(url, cid)
{
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	requestHeaders: {Accept: 'application/json'},
		onLoading: function (transport) {
			$('edition_component_'+cid).fade();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				thebuggenie.events.failedMessage(json.error);
			}
			else
			{
				$('edition_component_count').value--;
				if ($('edition_component_count').value == 0)
				{
					$('edition_no_components').appear();
				}
				$('project_component_'+cid).show();
			}
		},
		onFailure: function (transport, response) {
			$('edition_component_'+cid).appear();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.updateComponent = function(url, cid)
{
	var params = Form.serialize('edit_component_' + cid + '_form');
		new Ajax.Request(url, {
		asynchronous:true,
		requestHeaders: {Accept: 'application/json'},
		method: "post",
		parameters: params,
		onLoading: function (transport) {
			$('component_'+cid+'_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				thebuggenie.events.failedMessage(json.error);
				$('component_'+cid+'_indicator').hide();
			}
			else
			{
				$('component_'+cid+'_name').update(json.newname);
				$('component_'+cid+'_indicator').hide();
				$('edit_component_' + cid).hide();
				$('show_component_' + cid).show();
			}
		},
		onFailure: function (transport) {
			$('component_'+cid+'_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.updateMilestone = function(url, mid)
{
thebuggenie.utils.postFormWithJSONFeedback(url, 'edit_milestone_' + mid, 'milestone_' + mid + '_indicator', 'edit_milestone_' + mid, 'milestone_span_' + mid, false, 'show_milestone_' + mid);
}

thebuggenie.events.deleteMilestone = function(url, mid)
{
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		requestHeaders: {Accept: 'application/json'},
		onLoading: function (transport) {
			$('milestone_'+mid+'_indicator').show();
		},
		onSuccess: function (transport) {
			var json = transport.responseJSON;
			if (json.failed)
			{
				$('milestone_'+mid+'_indicator').hide();
				thebuggenie.events.failedMessage(json.error);
			}
			else
			{
				$('milestone_'+mid+'_indicator').hide();
				$('milestone_span_' + mid).remove();
				if ($('milestone_list').childElements().size() == 0)
				{
					$('no_milestones').show();
				}
			}
		},
		onFailure: function (transport) {
			$('milestone_'+mid+'_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.findDevs = function(url)
{
	var params = Form.serialize('find_dev_form');
	new Ajax.Updater('find_dev_results', url, {
		asynchronous:true,
		method: "post",
		parameters: params,
		onLoading: function (transport) {
			$('find_dev_indicator').show();
		},
		onComplete: function (transport) {
			$('find_dev_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		},
		onFailure: function (transport) {
			$('find_dev_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.updateFieldFromObject = function(object, field)
{
	if (object.id == 0)
	{
		$(field + '_name').hide();
		$('no_' + field).show();
	}
	else
	{
		$(field + '_name').update(object.name);
		$('no_' + field).hide();
		$(field + '_name').show();
	}
}

thebuggenie.events.setUser = function(url, field)
{
	new Ajax.Request(url, {
		method: 'post',
		requestHeaders: {Accept: 'application/json'},
		onLoading: function(transport) {
			$(field + '_spinning').show();
		},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			$(field + '_spinning').hide();
			$(field + '_change').hide();
			if (json.failed)
			{
				thebuggenie.events.failedMessage(json.error);
			}
			else
			{
				updateFieldFromObject(json.field, field);
			}
		},
		onFailure: function(transport) {
			$(field + '_spinning').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.assignToProject = function(url, form_id)
{
	var params = Form.serialize(form_id);
	new Ajax.Updater('assignees_list', url, {
		asynchronous:true,
		method: "post",
		parameters: params,
		onLoading: function (transport) {
			$('assign_dev_indicator').show();
		},
		onComplete: function (transport) {
			$('assign_dev_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		},
		onFailure: function (transport) {
			$('assign_dev_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				thebuggenie.events.failedMessage(json.error);
			}
		}
	});
}

thebuggenie.events.removeAssignee = function(url, type, id)
{
thebuggenie.utils.updateDivWithJSONFeedback(url, null, 'remove_assignee_'+type+'_'+id+'_indicator', null, null, 'assignee_'+type+'_'+id+'_link', ['assignee_'+type+'_'+id+'_row']);
}

thebuggenie.events.editEdition = function(url, edition_id)
{
thebuggenie.utils.updateDivWithJSONFeedback(url, 'backdrop_detail_content', 'backdrop_detail_indicator', false);
}

thebuggenie.events.backToEditProject = function(url, project_id)
{
thebuggenie.utils.updateDivWithJSONFeedback(url, 'backdrop_detail_content', 'backdrop_detail_indicator', false);
}

thebuggenie.events.updateProjectPrefix = function(url, project_id)
{
thebuggenie.utils.postFormWithJSONFeedback(url, 'project_info', 'project_key_indicator', null, '', null, null, 'project_key_input');
}