function toggleFavourite(url, issue_id)
{
	$('issue_favourite_indicator').show();
	$('issue_favourite_normal').hide();
	$('issue_favourite_faded').hide();
	new Ajax.Request(url, {
		method: 'post',
		parameters: {issue_id: issue_id},
		requestHeaders: {Accept: 'application/json'},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			if (Object.isUndefined(json.starred) == false)
			{
				if (json.starred)
				{
					$('issue_favourite_faded').hide();
					$('issue_favourite_indicator').hide();
					$('issue_favourite_normal').show();
				}
				else
				{
					$('issue_favourite_normal').hide();
					$('issue_favourite_indicator').hide();
					$('issue_favourite_faded').show();
				}
			}
			else
			{
				$('issue_favourite_normal').hide();
				$('issue_favourite_indicator').hide();
				$('issue_favourite_faded').show();
			}
			$('issue_favourite_indicator').hide();
		},
		onFailure: function(transport) {
			$('issue_favourite_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
			else
			{
				failedMessage(transport.responseText);
			}
		}
	});
}

function attachLink(url)
{
	var params = $('attach_link_form').serialize();
	$('attach_link_indicator').show();
	$('attach_link_submit').hide();
	new Ajax.Request(url, {
		method: 'post',
		parameters: params,
		requestHeaders: {Accept: 'application/json'},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			if (json && !json.failed)
			{
				$('attach_link_form').reset();
				$('attach_link').hide();
				$('viewissue_no_uploaded_files').hide();
				$('viewissue_uploaded_links').insert({bottom: json.content});
				$('viewissue_uploaded_attachments_count').update(json.attachmentcount);
			}
			else if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
			else
			{
				failedMessage(transport.responseText);
			}
			$('attach_link_indicator').hide();
			$('attach_link_submit').show();
		},
		onFailure: function(transport) {
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
			else
			{
				failedMessage(transport.responseText);
			}
			$('attach_link_indicator').hide();
			$('attach_link_submit').show();
		}
	});
}

function removeLinkFromIssue(url, link_id)
{
	new Ajax.Request(url, {
		method: 'post',
		requestHeaders: {Accept: 'application/json'},
		onLoading: function() {
			$('viewissue_links_'+ link_id + '_remove_link').hide();
			$('viewissue_links_'+ link_id + '_remove_indicator').show();
		},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			if (json && json.failed == false)
			{
				$('viewissue_links_' + link_id).remove();
				$('viewissue_links_' + link_id + '_remove_confirm').remove();
				successMessage(json.message);
				if (json.attachmentcount == 0)
				{
					$('viewissue_no_uploaded_files').show();
				}
				$('viewissue_uploaded_attachments_count').update(json.attachmentcount);
			}
			else
			{
				if (json && (json.failed || json.error))
				{
					failedMessage(json.error);
				}
				else
				{
					failedMessage(transport.responseText);
				}
				$('viewissue_links_'+ link_id + '_remove_link').show();
				$('viewissue_links_'+ link_id + '_remove_indicator').hide();
			}
		},
		onFailure: function(transport) {
			$('viewissue_links_'+ link_id + '_remove_link').show();
			$('viewissue_links_'+ link_id + '_remove_indicator').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
			else
			{
				failedMessage(transport.responseText);
			}
		}
	});
}

function detachFileFromIssue(url, file_id)
{
	_detachFile(url, file_id, 'viewissue_files_');
}

function updatePercent(url, mode)
{
	new Ajax.Request(url, {
		method: 'post',
		requestHeaders: {Accept: 'application/json'},
		onLoading: function(transport) {
			$('percent_spinning').show();
		},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			if (Object.isUndefined(json.percent) == false)
			{
				updatePercentageFromNumber('percentage_tds', json.percent);
				(mode == 'set') ? setIssueChanged('percent') : setIssueUnchanged('percent');
			}
			else
			{
			}
			$('percent_spinning').hide();
		},
		onFailure: function(transport) {
			$('percent_spinning').hide();
			var json = transport.responseJSON;
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
			else
			{
				failedMessage(transport.responseText);
			}
		}
	});
}

function updateDualFieldFromJSON(dualfield, field)
{
	if (dualfield.id == 0)
	{
		$(field + '_table').hide();
		$('no_' + field).show();
	}
	else
	{
		$(field + '_content').update(dualfield.name);
		if (field == 'status') $('status_color').setStyle({backgroundColor: dualfield.color});
		else if (field == 'issuetype') $('issuetype_image').src = dualfield.src;
		$('no_' + field).hide();
		$(field + '_table').show();
	}
}

function updateFieldFromObject(object, field)
{
	if ((Object.isUndefined(object.id) == false && object.id == 0) || (object.value && object.value == ''))
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

function updateTimeFieldFromObject(object, values, field)
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
	$(field + '_months').setValue(values.months);
	$(field + '_weeks').setValue(values.weeks);
	$(field + '_days').setValue(values.days);
	$(field + '_hours').setValue(values.hours);
	$(field + '_points').setValue(values.points);
}

function updateVisibleFields(visible_fields)
{
	available_fields = new Array('description', 'user_pain', 'reproduction_steps', 'category', 'resolution', 'priority', 'reproducability', 'percent_complete', 'severity', 'edition', 'build', 'component', 'estimated_time', 'spent_time', 'milestone');
	available_fields.each(function (key, index) 
	{
		if ($(key + '_field'))
		{
			if (Object.isUndefined(visible_fields[key]) == false) 
			{
				$(key + '_field').show();
				if ($(key + '_additional'))
				{
					$(key + '_additional').show();
				}
			}
			else 
			{
				$(key + '_field').hide();
				if ($(key + '_additional'))
				{
					$(key + '_additional').hide();
				}
			}
		}
	});
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
function setField(url, field, serialize)
{
	if (field == 'description') var params = $('description_form').serialize();
	else if (field == 'reproduction_steps') var params = $('reproduction_steps_form').serialize();
	else if (field == 'title') var params = $('title_form').serialize();
	else var params = '';
	
	if (serialize !== undefined) var params = $(serialize + '_form').serialize();
	if (field == 'issuetype') $('issuetype_indicator_fullpage').show();
	new Ajax.Request(url, {
		method: 'post',
		parameters: params,
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
				if (field == 'status' || field == 'issuetype') updateDualFieldFromJSON(json.field, field);
				else updateFieldFromObject(json.field, field);
				if (field == 'issuetype')
				{
					updateVisibleFields(json.visible_fields);
				}
				else if (field == 'pain_bug_type' || field == 'pain_likelihood' || field == 'pain_effect')
				{
					$('issue_user_pain').update(json.user_pain);
					if (json.user_pain_diff_text != '')
					{
						$('issue_user_pain_calculated').update(json.user_pain_diff_text);
						$('issue_user_pain_calculated').show();
					}
					else
					{
						$('issue_user_pain_calculated').hide();
					}
				}
			}
			else if (json.failed)
			{
				failedMessage(json.error);
			}
			(json.changed == true) ? setIssueChanged(field) : setIssueUnchanged(field);
			$(field + '_spinning').hide();
			$(field + '_change').hide();
			if (field == 'issuetype') $('issuetype_indicator_fullpage').hide();
		},
		onFailure: function(transport) {
			var json = transport.responseJSON;
			$(field + '_spinning').hide();
			$(field + '_change_error').update(json.error);
			$(field + '_change_error').show();
			if (field == 'issuetype') $('issuetype_indicator_fullpage').hide();
			Effect.Pulsate($(field + '_change_error'));
		}
	});
}

function setTimeField(url, field)
{
	params = $(field + '_form').serialize(); 
	new Ajax.Request(url, {
		method: 'post',
		parameters: params,
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
				updateTimeFieldFromObject(json.field, json.values, field);
			}
			else
			{
			}
			(json.changed == true) ? setIssueChanged(field) : setIssueUnchanged(field);
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

function revertField(url, field)
{
	if (field == 'issuetype') $('issuetype_indicator_fullpage').show();
	new Ajax.Request(url, {
		method: 'post',
		requestHeaders: {Accept: 'application/json'},
		onLoading: function(transport) {
			$(field + '_undo_spinning').show();
		},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			if (Object.isUndefined(json.field) == false)
			{
				if (field == 'status' || field == 'issuetype') updateDualFieldFromJSON(json.field, field);
				else if (field == 'estimated_time' || field == 'spent_time') updateTimeFieldFromObject(json.field, json.values, field);
				else updateFieldFromObject(json.field, field);
				if (field == 'issuetype') 
				{
					updateVisibleFields(json.visible_fields);
				}
				else if (field == 'description' || field == 'reproduction_steps')
				{
					$(field + '_form_value').update(json.form_value);
				}
				else if (field == 'pain_bug_type' || field == 'pain_likelihood' || field == 'pain_effect')
				{
					$('issue_user_pain').update(json.field.user_pain);
				}
				setIssueUnchanged(field);
			}
			else if (json && json.error)
			{
				failedMessage(json.error);
			}
			$(field + '_undo_spinning').hide();
			if (field == 'issuetype') $('issuetype_indicator_fullpage').hide();
		},
		onFailure: function(transport) {
			$(field + '_undo_spinning').hide();
			var json = transport.responseJSON;
			if (json && json.error)
			{
				failedMessage(json.error);
			}
			if (field == 'issuetype') $('issuetype_indicator_fullpage').hide();
		}
	});
}

function setIssueChanged(field)
{
	if (!$('viewissue_changed').visible())
	{
		$('viewissue_changed').show();
		Effect.Pulsate($('viewissue_changed'));
	}
	$(field + '_header').addClassName('issue_detail_changed');
	$(field + '_content').addClassName('issue_detail_changed');
	if ($('comment_save_changes'))
	{
		$('comment_save_changes').checked = true;
	}
}

function setIssueUnchanged(field)
{
	$(field + '_header').removeClassName('issue_detail_changed');
	$(field + '_header').removeClassName('issue_detail_unmerged');
	$(field + '_content').removeClassName('issue_detail_changed');
	$(field + '_content').removeClassName('issue_detail_unmerged');
	if ($('issue_view').select('.issue_detail_changed').size() == 0)
	{
		$('viewissue_changed').hide();
		$('viewissue_merge_errors').hide();
		$('viewissue_unsaved').hide();
		if ($('comment_save_changes'))
		{
			$('comment_save_changes').checked = false;
		}
	}
}

function deleteComment(url, cid)
{
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	requestHeaders: {Accept: 'application/json'},
	onLoading: function (transport) {
		$('comment_delete_controls_' + cid).hide();
		$('comment_delete_indicator_' + cid).show();
	},
	onSuccess: function(transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			$('comment_delete_controls_' + cid).show();
			$('comment_delete_indicator_' + cid).hide();
			failedMessage(json.error);
		}
		else
		{
			$('comment_delete_indicator_' + cid).remove();
			$('comment_delete_confirm_' + cid).remove();
			$('comment_' + cid).remove();
			if ($('comments_box').childElements().size() == 0)
			{
				$('comments_none').show();
			}
			successMessage(json.title);
		}
	},
	onFailure: function (transport) {
		$('comment_delete_indicator_' + cid).hide();
		$('comment_delete_controls_' + cid).show();
		var json = transport.responseJSON;
		if (json && (json.failed || json.error))
		{
			failedMessage(json.error);
		}
	}
	});
}

function updateComment(url, cid)
{
	params = $('comment_edit_form_' + cid).serialize();
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	parameters: params,
	requestHeaders: {Accept: 'application/json'},
	onLoading: function (transport) {
		$('comment_edit_controls_' + cid).hide();
		$('comment_edit_indicator_' + cid).show();
	},
	onSuccess: function(transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			$('comment_edit_controls_' + cid).show();
			$('comment_edit_indicator_' + cid).hide();
			failedMessage(json.error);
		}
		else
		{
			$('comment_edit_indicator_' + cid).hide();
			$('comment_edit_' + cid).hide();

			//$('comment_' + cid + '_header').update(json.comment_title);
			/* $('comment_' + cid + '_date').update(json.comment_date);	see the actions file */
			$('comment_' + cid + '_body').update(json.comment_body);	

			$('comment_view_' + cid).show();
			$('comment_edit_controls_' + cid).show();
			
			successMessage(json.title);
		}
	},
	onFailure: function (transport) {
		$('comment_edit_controls_' + cid).show();
		$('comment_edit_indicator_' + cid).hide();
		var json = transport.responseJSON;
		if (json && json.error)
		{
			failedMessage(json.error);
		}
	}
	});
}

function addComment(url, commentcount_span)
{
	if ($('comment_save_changes').checked)
	{
		return true;
	}
	params = $('comment_form').serialize();
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	parameters: params,
	requestHeaders: {Accept: 'application/json'},
	onLoading: function (transport) {
		$('comment_add_controls').hide();
		$('comment_add_indicator').show();
	},
	onSuccess: function(transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			$('comment_add_controls').show();
			$('comment_add_indicator').hide();
			failedMessage(json.error);
		}
		else
		{
			$('comment_add_indicator').hide();
			$('comment_add').hide();
			$('comment_add_button').show();

			$('comments_box').insert({bottom: json.comment_data});

			if ($('comments_box').childElements().size() != 0)
			{
				$('comments_none').hide();
			}
			
			$('comment_add_controls').show();
			
			//$('comment_title').clear();
			$('comment_bodybox').clear()
			$('comment_visibility').setValue(1);
			$(commentcount_span).update(json.commentcount);
			
			successMessage(json.title);
		}
	},
	onFailure: function (transport) {
		$('comment_add_controls').show();
		$('comment_add_indicator').hide();
		var json = transport.responseJSON;
		if (json && json.error)
		{
			failedMessage(json.error);
		}
	}
	});
	return false;
}

function toggleConfirmed(url, affected)
{
	new Ajax.Request(url, {
		method: 'post',
		requestHeaders: {Accept: 'application/json'},
		onLoading: function(transport) {
			$('affected_' + affected + '_confirmed_icon').hide();
			$('affected_' + affected + '_confirmed_spinner').show();
		},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			successMessage(json.message);
			
			$('affected_' + affected + '_confirmed_icon').writeAttribute('alt', json.alt);
			$('affected_' + affected + '_confirmed_icon').writeAttribute('src', json.src);
			
			$('affected_' + affected + '_confirmed_icon').show();
			$('affected_' + affected + '_confirmed_spinner').hide();
		},
		onFailure: function(transport) {
			var json = transport.responseJSON;

			failedMessage(json.error);
			$('affected_' + affected + '_confirmed_spinner').hide();
			$('affected_' + affected + '_confirmed_icon').show();
		}
	});
}

function deleteAffected(url, affected)
{
	new Ajax.Request(url, {
		method: 'post',
		requestHeaders: {Accept: 'application/json'},
		onLoading: function(transport) {
			$('affected_' + affected + '_delete_spinner').show();
		},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			successMessage(json.message);
			
			$('viewissue_affects_count').update(json.itemcount);
			$('affected_' + affected + '_delete').remove();
			$('affected_' + affected).remove();
			
			if (json.itemcount == 0)
			{
				$('no_affected').show();
			}
		},
		onFailure: function(transport) {
			var json = transport.responseJSON;

			failedMessage(json.error);
			$('affected_' + affected + '_delete_spinner').hide();
		}
	});
}

function statusAffected(url, affected)
{
	new Ajax.Request(url, {
		method: 'post',
		requestHeaders: {Accept: 'application/json'},
		onLoading: function(transport) {
			$('affected_' + affected + '_status_spinning').show();
		},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			successMessage(json.message);
			
			$('affected_' + affected + '_status_colour').setStyle({
				backgroundColor: json.colour,
				fontSize: '1px',
				width: '20px',
				height: '15px',
				marginRight: '2px'
			});
			$('affected_' + affected + '_status_name').update(json.name);
			$('affected_' + affected + '_status_spinning').hide();
			$('affected_' + affected + '_status_change').hide();
		},
		onFailure: function(transport) {
			var json = transport.responseJSON;

			$('affected_' + affected + '_status_spinning').hide();
			$('affected_' + affected + '_status_error').update(json.error);
			$('affected_' + affected + '_status_error').show();
			Effect.Pulsate($('affected_' + affected + '_status_error'));
		}
	});
}

function addAffected(url)
{
	params = $('viewissue_add_item_form').serialize();
	new Ajax.Request(url, {
		asynchronous:true,
		method: "post",
		parameters: params,
		requestHeaders: {Accept: 'application/json'},
		onLoading: function(transport) {
			$('add_affected_spinning').show();
		},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			successMessage(json.message);

			$('viewissue_affects_count').update(json.itemcount);
			
			if (json.itemcount != 0)
			{
				$('no_affected').hide();
			}
			
			$('affected_list').insert({bottom: json.content});
			
			$('add_affected_spinning').hide();
			resetFadedBackdrop();
		},
		onFailure: function(transport) {
			var json = transport.responseJSON;

			$('add_affected_spinning').hide();
			failedMessage(json.error);
		}
	});
}

function updateWorkflowAssignee(url, assignee_id, assignee_type)
{
	$('popup_no_assigned_to').hide();
	$('popup_assigned_to_name').show();
	_updateDivWithJSONFeedback(url, 'popup_assigned_to_name', 'popup_assigned_to_name_indicator');
	$('popup_assigned_to_id').setValue(assignee_id);
	$('popup_assigned_to_type').setValue(assignee_type);
	$('popup_assigned_to_teamup').setValue(0);
	$('popup_assigned_to_teamup_info').hide();
	$('popup_assigned_to_change').hide();
}

function updateWorkflowAssigneeTeamup(url, assignee_id, assignee_type)
{
	$('popup_no_assigned_to').hide();
	$('popup_assigned_to_name').show();
	_updateDivWithJSONFeedback(url, 'popup_assigned_to_name', 'popup_assigned_to_name_indicator');
	$('popup_assigned_to_id').setValue(assignee_id);
	$('popup_assigned_to_type').setValue(assignee_type);
	$('popup_assigned_to_teamup').setValue(1);
	$('popup_assigned_to_teamup_info').show();
	$('popup_assigned_to_change').hide();
}