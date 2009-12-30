function addProject(url)
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
		if (json.error)
		{
			failedMessage(json.error);
			$('project_add_indicator').hide();
		}
		else
		{
			Form.reset('add_project_form');
			$('noprojects_tr').hide();
			$('project_add_indicator').hide();
			successMessage(json.title, json.message);
			$('project_table').update($('project_table').innerHTML + json.html);
		}
	},
	onFailure: function (transport) {
		$('project_add_indicator').hide();
		var json = transport.responseJSON;
		if (json && json.error)
		{
			failedMessage(json.error);
		}
	},
	insertion: Insertion.Bottom
	});
}

function removeProject(url, pid)
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
		if (json.deleted)
		{
			$('project_delete_indicator_' + pid).remove();
			$('project_delete_confirm_' + pid).remove();
			$('project_box_' + pid).remove();
			if ($('project_table').childElements().size() == 0)
			{
				$('noprojects_tr').show();
			}
		}
		else
		{
			$('project_delete_controls_' + pid).show();
			$('project_delete_error_' + pid).show();
		}
	},
	onFailure: function (transport) {
		$('project_delete_controls_' + pid).show();
		$('project_delete_error_' + pid).show();
	}
	});
}

function addMilestone(url)
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
		if (json.error)
		{
			failedMessage(json.error);
			$('milestone_add_indicator').hide();
		}
		else
		{
			Form.reset('add_milestone_form');
			$('no_milestones').hide();
			$('milestone_add_indicator').hide();
			successMessage(json.title, json.message);
			$('milestone_list').update($('milestone_list').innerHTML + json.html);
		}
	},
	onFailure: function (transport) {
		$('milestone_add_indicator').hide();
	},
	insertion: Insertion.Bottom
	});
}

function doBuildAction(url, bid, action, update)
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
			if (json != null)
			{
				failedMessage(json.error);
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
		}
		});
}

function updateBuild(url, bid)
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
			if (json != null)
			{
				failedMessage(json.error);
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
		}
		});
}

function saveProjectOther(url)
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
			if (json.saved)
			{
				$('message_failed').hide();
				$('settings_save_indicator').hide();
				successMessage(json.title, json.message);
			}
			else
			{
				$('settings_save_indicator').hide();
				failedMessage(json.error);
			}	
		},
		onFailure: function (transport) {
			$('settings_save_indicator').hide();
			$('message_failed').show();
		}
		});
}

function addToOpenBuild(url, bid)
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
			if (json.saved)
			{
				$('build_'+bid+'_indicator').hide();
				$('build_'+bid+'_info').show();
				$('addtoopen_build_'+bid).hide();
				successMessage(json.title, json.message);
			}
			else
			{
				$('build_'+bid+'_indicator').hide();
				$('build_'+bid+'_info').show();
				$('addtoopen_build_'+bid).hide();
				failedMessage(json.title, json.message);
			}
		},
		onFailure: function (transport) {
			$('build_'+bid+'_indicator').hide();
			$('build_'+bid+'_info').show();
			$('addtoopen_build_'+bid).hide();
		}
		});
}

function deleteBuild(url, bid)
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
			if (json.deleted)
			{
				$('message_failed').hide();
				$('build_'+bid+'_indicator').hide();
				$('build_'+bid+'_info').hide();
				$('show_build_'+bid).hide();
				$('edit_build_'+bid).hide();
				$('del_build_'+bid).hide();
				$('addtoopen_build_'+bid).hide();
				$('message_build_deleted').show();
				new Effect.Fade('message_build_deleted', {delay: 5} );
			}
			else
			{
				$('build_'+bid+'_indicator').removeClassName('selected_red');
				$('build_'+bid+'_indicator').hide();
				$('build_'+bid+'_info').show();
				$('del_build_'+bid).hide();
			}
		},
		onFailure: function (transport) {
			$('build_'+bid+'_indicator').removeClassName('selected_red');
			$('build_'+bid+'_indicator').hide();
			$('build_'+bid+'_info').show();
			$('del_build_'+bid).hide();
		}
		});
}


function switchEditionTab(select_tab)
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

function addEdition(url)
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
		if (json.error)
		{
			failedMessage(json.error);
			$('edition_add_indicator').hide();
		}
		else
		{
			Form.reset('add_edition_form');
			$('edition_add_indicator').hide();
			successMessage(json.title, json.message);
			$('edition_table').update($('edition_table').innerHTML + json.html);
		}
	},
	onFailure: function (transport) {
		$('edition_add_indicator').hide();
	}
	});
}

function addBuild(url)
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
		if (json.error)
		{
			failedMessage(json.error);
			$('build_add_indicator').hide();
		}
		else
		{
			$('build_table').update($('build_table').innerHTML + json.html);
			$('build_add_indicator').hide();
			successMessage(json.title, json.message);
		}
	},
	onFailure: function (transport) {
		$('build_add_indicator').hide();
	}
	});
}

function setDefaultProject()
{
	var params = Form.serialize('default_project_form');
	new Ajax.Request('config.php?module=core&section=10', {
	asynchronous:true,
	method: "post",
	parameters: params,
	onSuccess: function (setDefaultSuccess) {
		new Ajax.Updater('message_span', 'config.php?module=core&section=10&showmessage=true&themessage=projectdefaultsaved', {
		asynchronous:true,
		evalScripts: true,
		method: "get"
		});
	}
	});
}

function addComponent(url)
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
		if (json.error)
		{
			failedMessage(json.error);
			$('project_add_indicator').hide();
		}
		else
		{
			Form.reset('add_component_form');
			$('component_add_indicator').hide();
			successMessage(json.title, json.message);
			$('no_components').hide();
			$('component_table').update($('component_table').innerHTML + json.html);
		}
	},
	onFailure: function (transport) {
		$('component_add_indicator').hide();
	},
	insertion: Insertion.Bottom
	});
}

function submitProjectSettings(url)
{
	tinyMCE.triggerSave();
	var params = Form.serialize('project_settings');
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	requestHeaders: {Accept: 'application/json'},
	parameters: params,
	onLoading: function (transport) {
		$('project_save_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.saved)
		{
			$('project_save_indicator').hide();
			$('project_name_span').update($('project_name').getValue());
			successMessage(json.title, json.message);
		}
		else
		{
			$('project_save_indicator').hide();
			failedMessage(json.error);
		}
	},
	onFailure: function (transport) {
		$('project_save_indicator').hide();
	}
	});
}

function submitEditionSettings(url)
{
	var params = Form.serialize('edition_settings');
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	parameters: params,
	requestHeaders: {Accept: 'application/json'},
	onLoading: function (transport) {
		$('edition_save_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.saved)
		{
			$('edition_save_indicator').hide();
			$('message_changes_saved').show();
			$('edition_name_span').update($('edition_name').getValue());
			new Effect.Fade('message_changes_saved', {delay: 20} );
		}
		else
		{
			$('edition_save_indicator').hide();
		}
	},
	onFailure: function (transport, response) {
		$('edition_save_indicator').hide();
	}
	});
}

function getComponents(pid, eid)
{
	new Ajax.Updater('edition_components', 'config.php?module=core&section=10&geteditioncomponents=true', {
	asynchronous:true,
	method: "get",
	parameters: {p_id: pid, e_id: eid }
	});
}

function addEditionComponent(url, cid)
{
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	requestHeaders: {Accept: 'application/json'},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.saved)
		{
			new Effect.Fade('project_component_'+cid);
			new Effect.Appear('edition_component_'+cid);
		}
		else
		{
			failedMessage(json.error);
		}
	}
	});
}

function removeEditionComponent(url, cid)
{
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	requestHeaders: {Accept: 'application/json'},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.deleted)
		{
			new Effect.Fade('edition_component_'+cid);
			new Effect.Appear('project_component_'+cid);
		}
		else
		{
			failedMessage(json.error);
		}
	}
	});
}

function updateComponent(url, cid)
{
	var params = Form.serialize('edit_component_' + cid + '_form');
	new Ajax.Request(url, {
	asynchronous:true,
	requestHeaders: {Accept: 'application/json'},
	method: "post",
	parameters: params,
	onLoading: function (transport) {
		$('component_'+cid+'_indicator').show();
		$('component_'+cid+'_icon').hide();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.saved)
		{
			$('component_'+cid+'_name').update(json.newname);
			$('component_'+cid+'_indicator').hide();
			$('component_'+cid+'_icon').show();
			$('edit_component_' + cid).hide();
			$('show_component_' + cid).show();
		}
		else
		{
			failedMessage(json.error);
			$('component_'+cid+'_indicator').hide();
			$('component_'+cid+'_icon').show();
		}
	},
	onFailure: function (transport) {
		$('component_'+cid+'_indicator').hide();
		$('component_'+cid+'_icon').show();
	}
	});
}

function updateMilestone(url, mid)
{
	var params = Form.serialize('edit_milestone_' + mid);
	new Ajax.Updater('milestone_span_' + mid, url, {
	asynchronous:true,
	method: "post",
	parameters: params,
	evalScripts: true,
	onLoading: function (transport) {
		$('milestone_'+mid+'_indicator').show();
	},
	onFailure: function (transport) {
		$('milestone_'+mid+'_indicator').hide();
	}
	});
}

function deleteMilestone(url, mid)
{
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	requestHeaders: {Accept: 'application/json'},
	onLoading: function (transport) {
		$('milestone_'+mid+'_indicator').show();
	},
	onFailure: function (transport) {
		$('milestone_'+mid+'_indicator').hide();
	},
	onSuccess: function (transport) {
		if (json.deleted)
		{
			$('milestone_'+mid+'_indicator').hide();
			$('milestone_span_' + mid).remove();
			if ($('milestone_list').childElements().size() == 0)
			{
				$('no_milestones').show();
			}
		}
		else
		{
			$('milestone_'+mid+'_indicator').hide();
		}
	}
	});
}

function deleteComponent(url, cid)
{
	new Ajax.Request('config.php?module=core&section=10&delete_component=true', {
	asynchronous:true,
	method: "post",
	parameters: {c_id: cid },
	onSuccess: function (deleteComponentSuccess) {
		new Ajax.Updater('message_span', 'config.php?module=core&section=10&showmessage=true&themessage=deletedcomponent', {
		asynchronous:true,
		evalScripts: true,
		method: "get"
		});
		$('show_component_' + cid).remove();
		$('del_component_' + cid).remove();
		if ($('component_list').childElements().size() == 0)
		{
			$('no_components').show();
		}
	}
	});
}

function deleteEdition(eid)
{
	new Ajax.Request('config.php?module=core&section=10&delete_edition=true', {
	asynchronous:true,
	method: "post",
	parameters: {e_id: eid },
	onSuccess: function (deleteEditionSuccess) {
		new Ajax.Updater('message_span', 'config.php?module=core&section=10&showmessage=true&themessage=deletededition', {
		asynchronous:true,
		evalScripts: true,
		method: "get"
		});
		Effect.Fade('edition_box_' + eid, { duration: 0.5 });
		Effect.Fade('del_edition_' + eid, { duration: 0.5 });
	}
	});
}

function findDevs(url)
{
	var params = Form.serialize('find_dev_form');
	new Ajax.Updater('find_dev_results', url, {
	asynchronous:true,
	method: "post",
	onLoading: function () { $('find_dev_indicator').show(); },
	onComplete: function () { $('find_dev_indicator').hide(); },
	parameters: params
	});
}

function updateFieldFromObject(object, field)
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

/**
 * This function is triggered every time an issue is updated via the web interface
 * It sends a request that performs the update, and gets JSON back
 * 
 * Depending on the JSON return value, it updates fields, shows/hides boxes on the 
 * page, and sets some class values
 * 
 * @param url The URL to request
 * @param field The field that is being changed
 * 
 * @return void
 */
function setUser(url, field)
{
	new Ajax.Request(url, {
		method: 'post',
		requestHeaders: {Accept: 'application/json'},
		onLoading: function(transport) {
			$(field + '_spinning').show();
			$(field + '_change_error').update('');
			$(field + '_change_error').hide();
		},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			if (Object.isUndefined(json.field) == false)
			{
				updateFieldFromObject(json.field, field);
			}
			$(field + '_spinning').hide();
			$(field + '_change').hide();
		},
		onFailure: function(transport) {
			var json = transport.responseJSON;
			$(field + '_spinning').hide();
			$(field + '_change_error').update(json.error);
			$(field + '_change_error').show();
			Effect.Pulsate($(field + '_change_error'));
		}
	});
}

function assignToProject(url, form_id)
{
	var params = Form.serialize(form_id);
	new Ajax.Updater('assignees_list', url, {
	asynchronous:true,
	method: "post",
	onLoading: function () { $('assign_dev_indicator').show(); },
	onComplete: function () { $('assign_dev_indicator').hide(); },
	parameters: params
	});
}

function removeFromProject(url, aid)
{
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	onSuccess: function (transport) {
		if ($('assignment_product_' + aid).parentNode.childElements().size() == 1)
		{
			$('assignment_product_' + aid).parentNode.remove();
		}
		$('assignment_product_' + aid).remove();
	}
	});
}

function assignToEdition(pid, uid, eid)
{
	new Ajax.Updater('assignees_list', 'config.php?module=core&section=10&edit_editions=true&add_dev=true&target_type=2', {
	asynchronous:true,
	method: "post",
	parameters: {p_id: pid, u_id: uid, target: eid }
	});
}

function removeFromEdition(pid, uid, eid)
{
	new Ajax.Updater('assignees_list', 'config.php?module=core&section=10&edit_editions=true&remove_dev=true&target_type=2', {
	asynchronous:true,
	method: "post",
	parameters: {p_id: pid, u_id: uid, target: eid }
	});
}

function assignToComponent(pid, uid, cid)
{
	new Ajax.Updater('assignees_list', 'config.php?module=core&section=10&edit_editions=true&add_dev=true&target_type=3', {
	asynchronous:true,
	method: "post",
	parameters: {p_id: pid, u_id: uid, target: cid }
	});
}

function removeFromComponent(pid, uid, cid)
{
	new Ajax.Updater('assignees_list', 'config.php?module=core&section=10&edit_editions=true&remove_dev=true&target_type=3', {
	asynchronous:true,
	method: "post",
	parameters: {p_id: pid, u_id: uid, target: cid }
	});
}
