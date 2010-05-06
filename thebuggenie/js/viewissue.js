function toggleFavourite(url, issue_id)
{
	$('issue_favourite_indicator').show();
	$('issue_favourite_normal').hide();
	$('issue_favourite_faded').hide();
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
	new Ajax.Request(url, {
		method: 'post',
		requestHeaders: {Accept: 'application/json'},
		onLoading: function() {
			$('viewissue_files_'+ file_id + '_remove_link').hide();
			$('viewissue_files_'+ file_id + '_remove_indicator').show();
			$('uploaded_files_'+ file_id + '_remove_link').hide();
			$('uploaded_files_'+ file_id + '_remove_indicator').show();
		},
		onSuccess: function(transport) {
			var json = transport.responseJSON;
			if (json && json.failed == false)
			{
				$('viewissue_files_' + file_id).remove();
				$('uploaded_files_' + file_id).remove();
				$('viewissue_files_' + file_id + '_remove_confirm').remove();
				$('uploaded_files_' + file_id + '_remove_confirm').remove();
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
				$('viewissue_files_'+ file_id + '_remove_link').show();
				$('viewissue_files_'+ file_id + '_remove_indicator').hide();
				$('uploaded_files_'+ file_id + '_remove_link').show();
				$('uploaded_files_'+ file_id + '_remove_indicator').hide();
			}
		},
		onFailure: function(transport) {
			$('viewissue_files_'+ file_id + '_remove_link').show();
			$('viewissue_files_'+ file_id + '_remove_indicator').hide();
			$('uploaded_files_'+ file_id + '_remove_link').show();
			$('uploaded_files_'+ file_id + '_remove_indicator').hide();
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

function updatePercentageFromNumber(percent)
{
	cc = 0;
	$('percentage_tds').childElements().each(function(elm) {
		elm.removeClassName("percent_filled");
		elm.removeClassName("percent_unfilled");
		if (percent > 0 && percent < 100)
		{
			(cc <= percent) ? elm.addClassName("percent_filled") : elm.addClassName("percent_unfilled");
		}
		else if (percent == 0)
		{
			elm.addClassName("percent_unfilled");
		}
		else if (percent == 100)
		{
			elm.addClassName("percent_filled");
		}
		cc++;
	});
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
				updatePercentageFromNumber(json.percent);
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
		if (field == 'status') $('status_color').setStyle({ backgroundColor: dualfield.color});
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
	available_fields = new Array('description', 'user_pain', 'reproduction_steps', 'category', 'resolution', 'priority', 'reproducability', 'percent_complete', 'severity', 'editions', 'builds', 'components', 'estimated_time', 'elapsed_time', 'milestone');
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
function setField(url, field)
{
	if (field == 'description') var params = $('description_form').serialize();
	else if (field == 'reproduction_steps') var params = $('reproduction_steps_form').serialize();
	else if (field == 'title') var params = $('title_form').serialize();
	else var params = '';
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
				else if (field == 'estimated_time' || field == 'elapsed_time') updateTimeFieldFromObject(json.field, json.values, field);
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

			$('comment_' + cid + '_header').update(json.comment_title);			
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
			
			$('comment_title').clear();
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
}

/*function submitNewTitle()
{
	var params = Form.serialize('issue_edit_title');
	new Ajax.Updater('issue_title', 'include/viewissue_actions.inc.php', {
	asynchronous:true,
	method: "post",
	parameters: params,
	onSuccess: function (something) {
		Element.hide('edit_title');
		showMenu('edit_issue');
		menuUnhover('edit_issue_actions', '');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function getIssueTypes()
{
	Element.show('edit_issuetype');
	new Ajax.Updater('issuetype_table', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getissuetypes=true', {
	asynchronous:true,
	method: "post" });
}
function setIssueType(tid)
{
	new Ajax.Updater('issue_type', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>', {
	asynchronous:true,
	method: "post",
	parameters: { setissuetype: tid },
	onSuccess: function (something) {
		Element.hide('edit_issuetype');
		showMenu('edit_issue');
		menuUnhover('edit_issue_actions', '');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function submitNewDescription()
{
	tinyMCE.triggerSave();
	var params = Form.serialize('issue_edit_description');
	new Ajax.Updater('issue_description', 'include/viewissue_actions.inc.php', {
	asynchronous:true,
	method: "post",
	parameters: params,
	onSuccess: function (something) {
		Element.hide('edit_description');
		showMenu('edit_issue');
		menuUnhover('edit_issue_actions', '');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}

function submitNewDescriptionInline()
{
	tinyMCE.triggerSave();
	var params = Form.serialize('issue_edit_description_inline');
	new Ajax.Updater('issue_description', 'include/viewissue_actions.inc.php', {
	asynchronous:true,
	method: "post",
	parameters: params,
	onSuccess: function (something) {
		Element.hide('edit_description_inline');
		Element.show('issue_description');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}

function submitNewRepro()
{
	tinyMCE.triggerSave();
	var params = Form.serialize('issue_edit_repro');
	new Ajax.Updater('issue_repro', 'include/viewissue_actions.inc.php', {
	asynchronous:true,
	method: "post",
	parameters: params,
	onSuccess: function (something) {
		Element.hide('edit_repro');
		showMenu('edit_issue');
		menuUnhover('edit_issue_actions', '');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function getCategories()
{
	Element.show('edit_category');
	new Ajax.Updater('categories_table', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getcategories=true', {
	asynchronous:true,
	method: "post" });
}
function setCategory(cid)
{
	new Ajax.Updater('issue_category', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>', {
	asynchronous:true,
	method: "post",
	parameters: { setcategory: cid },
	onSuccess: function (something) {
		Element.hide('edit_category');
		showMenu('edit_issue');
		menuUnhover('edit_issue_actions', '');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function getRepros()
{
	Element.show('edit_reproducability');
	new Ajax.Updater('repros_table', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getrepros=true', {
	asynchronous:true,
	method: "post" });
}
function setReproID(rid)
{
	new Ajax.Updater('issue_reproducability', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>', {
	asynchronous:true,
	method: "post",
	parameters: { setreproid: rid },
	onSuccess: function (something) {
		Element.hide('edit_reproducability');
		showMenu('edit_issue');
		menuUnhover('edit_issue_actions', '');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function getEditions()
{
	new Ajax.Updater('editions_table', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&geteditions=true', {
	asynchronous:true,
	method: "post" });
}
function getBuilds()
{
	new Ajax.Updater('builds_table', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getbuilds=true', {
	asynchronous:true,
	method: "post" });
}
function getComponents()
{
	new Ajax.Updater('components_table', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getcomponents=true', {
	asynchronous:true,
	method: "post" });
}
function getBuildsInline()
{
	new Ajax.Updater('builds_table_inline', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getbuilds=true&inline=true', {
	asynchronous:true,
	method: "post" });
}
function getComponentsInline()
{
	new Ajax.Updater('components_table_inline', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getcomponents=true&inline=true', {
	asynchronous:true,
	method: "post" });
}
function addBuild(bid)
{
	new Ajax.Updater('affected_builds_menu', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&issue_addaffects=1', {
	asynchronous:false,
	method: "post",
	parameters: {build: bid},
	insertion: 'bottom',
	onSuccess: function (addBuildSuccess) {
		$('affects_no_builds_menu').hide();
		getBuilds();
		getBuildInline(bid);
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function addComponent(cid)
{
	new Ajax.Updater('affected_components_menu', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&issue_addaffects=1', {
	asynchronous:false,
	method: "post",
	parameters: {component: cid},
	insertion: 'bottom',
	onSuccess: function (addComponentSuccess) {
		$('affects_no_components_menu').hide();
		getComponents();
		getComponentInline(cid);
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function addEdition(eid)
{
	new Ajax.Updater('affected_editions_menu', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&issue_addaffects=1', {
	asynchronous:false,
	method: "post",
	parameters: {edition: eid},
	onSuccess: function (addEditionSuccess) {
		$('affects_no_editions_inline').hide();
		getAffectedEditionsInMenu();
		getAffectedEditionsInline();
		getEditions();
		getBuilds();
		getComponents();
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function removeAffected(aid, atype)
{
	new Ajax.Request('include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>', {
	asynchronous:false,
	method: "post",
	evalScripts: true,
	parameters: {issue_removeaffects: aid, issue_removeaffects_type: atype},
	onSuccess: function (removeAffectedSuccess) {
		$('issue_affected_' + atype + '_' + aid + '_inline').hide();
		$('issue_affected_' + atype + '_' + aid + '_menu').hide();
		if (atype == 'edition')
		{
			getEditions();
			getBuilds();
			getComponents();
		}
		else if (atype == 'build')
		{
			getBuilds();
		}
		else if (atype == 'component')
		{
			getComponents();
		}
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function setAffectedConfirmed(cfmd, aid, atype)
{
	new Ajax.Updater('affected_confirmed_' + aid + '_' + atype, 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&issue_setconfirmed=1', {
	asynchronous:true,
	method: "post",
	parameters: {a_id: aid, confirmed: cfmd, a_type: atype},
	onSuccess: function (something) {
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}

function getAffectedInMenu()
{
	new Ajax.Updater('affectslist_menu', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getaffected=1&menu=1', {
	asynchronous:true,
	method: "post",
	evalScripts: true
	});
}

function getAffectedEditionsInMenu()
{
	new Ajax.Updater('affected_editions_menu', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getaffectededitions=1', {
	asynchronous:true,
	method: "post",
	evalScripts: true
	});
}

function getAffectedEditionsInline()
{
	new Ajax.Updater('issue_editions', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&geteditionsinline=true', {
	asynchronous:true,
	method: "post",
	evalScripts: true
	});
}

function getComponentInline(cid)
{
	new Ajax.Updater('issue_affected_inline', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getaffectedcomponentinline=true&c_id=' + cid, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	insertion: 'bottom'
	});
}

function getBuildInline(bid)
{
	new Ajax.Updater('issue_affected_inline', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getaffectedbuildinline=true&b_id=' + bid, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	insertion: 'top'
	});
}

function getDuplicateOf()
{
	new Ajax.Updater('duplicate_issue_menu', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getduplicateof=true', {
	asynchronous:true,
	method: "post" });
}
function getDuplicateSearchBox()
{
	new Ajax.Updater('duplicate_issues_search', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getduplicatesearchbox=true', {
	asynchronous:true,
	method: "post" });
}
function findDuplicatedIssue()
{
	var params = Form.serialize('issue_find_duplicated_form');
	new Ajax.Updater('duplicate_issues_search', 'include/viewissue_actions.inc.php', {
	asynchronous:true,
	method: "post",
	parameters: params
	});
}
function markAsDuplicateOf(did)
{
	new Ajax.Updater('duplicate_span', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&markasduplicate=true', {
	asynchronous:true,
	method: "post",
	parameters: {d_id: did},
	onSuccess: function (something) {
		getDuplicateOf();
		getDuplicateSearchBox();
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}

function submitNewPercent()
{
	var params = Form.serialize('issue_edit_percent');
	new Ajax.Updater('issue_percent', 'include/viewissue_actions.inc.php', {
	asynchronous:true,
	method: "post",
	parameters: params,
	onSuccess: function (something) {
		Element.hide('edit_percent');
		showMenu('progress_tracking');
		menuUnhover('progress_tracking_actions', '');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function submitNewEstimatedTime()
{
	var params = Form.serialize('issue_edit_estimated');
	new Ajax.Updater('issue_estimated', 'include/viewissue_actions.inc.php', {
	asynchronous:true,
	method: "post",
	parameters: params,
	onSuccess: function (something) {
		Element.hide('edit_estimated');
		showMenu('progress_tracking');
		menuUnhover('progress_tracking_actions', '');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function submitNewElapsedTime()
{
	var params = Form.serialize('issue_edit_elapsed');
	new Ajax.Updater('issue_elapsed', 'include/viewissue_actions.inc.php', {
	asynchronous:true,
	method: "post",
	parameters: params,
	onSuccess: function (something) {
		Element.hide('edit_elapsed');
		showMenu('progress_tracking');
		menuUnhover('progress_tracking_actions', '');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function getRelatedIssuesInMenu()
{
	new Ajax.Updater('related_p_issues_menu', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getrelatedissues=1&p_issues=1', {
	asynchronous:true,
	method: "post"
	});
	new Ajax.Updater('related_c_issues_menu', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getrelatedissues=1&c_issues=1', {
	asynchronous:true,
	method: "post"
	});
	new Draggable('edit_dependant');
}
function getRelatedIssuesInline()
{
	new Ajax.Updater('related_p_issues_inline', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getrelatedissuesinline=true&p_issues=1', {
	asynchronous:true,
	method: "post"
	});
	new Ajax.Updater('related_c_issues_inline', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getrelatedissuesinline=true&c_issues=1', {
	asynchronous:true,
	method: "post"
	});
	new Draggable('edit_dependant');
}
function getRelatedIssuesSearchBox(ps, cs)
{
	if (ps == true)
	{
		new Ajax.Updater('related_p_issues_search', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getrelatedissues_searchform=1&this_depends=1', {
		asynchronous:true,
		method: "post"
		});
	}
	if (cs == true)
	{
		new Ajax.Updater('related_c_issues_search', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getrelatedissues_searchform=1&this_depends=0', {
		asynchronous:true,
		method: "post"
		});
	}
}
function findRelatedIssue(depends, form_name, div_name)
{
	var params = Form.serialize(form_name);
	new Ajax.Updater(div_name, 'include/viewissue_actions.inc.php', {
	asynchronous:true,
	method: "post",
	parameters: params
	});
}
function addRelatedIssue(depends, i_id)
{
	new Ajax.Request('include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&add_dependant_issue=true', {
	asynchronous:true,
	method: "post",
	parameters: {d_id: i_id, this_depends: depends},
	onSuccess: function (addRelatedSuccess) {
		getRelatedIssuesInMenu();
		getRelatedIssuesInline();
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function removeRelatedIssue(d_id)
{
	new Ajax.Request('include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&remove_depends=true', {
	asynchronous:true,
	method: "post",
	parameters: {p_id: d_id},
	onSuccess: function (addRelatedSuccess) {
		getRelatedIssuesInMenu();
		getRelatedIssuesInline();
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function getMilestones()
{
	getMilestonesInMenu();
	getMilestonesInView();
}
function getMilestonesInView()
{
	new Ajax.Updater('issue_assigned_milestones', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getassignedmilestones=true&nolink=true', {
	asynchronous:true,
	method: "post"
	});
}
function getMilestonesInMenu()
{
	new Ajax.Updater('issue_assigned_milestones_menu', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getassignedmilestones=true', {
	asynchronous:true,
	method: "post"
	});
	new Ajax.Updater('issue_available_milestones_menu', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getavailablemilestones=true', {
	asynchronous:true,
	method: "post"
	});
}
function addMilestone(mid)
{
	new Ajax.Request('include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&issue_addmilestone=true', {
	asynchronous:true,
	method: "post",
	parameters: {m_id: mid},
	onSuccess: function (addMilestoneSuccess) {
		getMilestones();
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function removeMilestone(mid)
{
	new Ajax.Request('include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&issue_removemilestone=true', {
	asynchronous:true,
	method: "post",
	parameters: {m_id: mid},
	onSuccess: function (removeMilestoneSuccess) {
		getMilestones();
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function getStatusList()
{
	Element.show('edit_status');
	new Ajax.Updater('issue_status_menu', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getstatuslist=true', {
	asynchronous:true,
	method: "post" });
}
function setStatus(sid)
{
	new Ajax.Updater('issue_status', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>', {
	asynchronous:true,
	method: "post",
	parameters: { setstatus: sid },
	onSuccess: function (something) {
		Element.hide('edit_status');
		showMenu('workflow_actions');
		menuUnhover('workflow_actions_actions', '');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function getResolutions()
{
	Element.show('edit_resolution');
	new Ajax.Updater('resolutions_table', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getresolutions=true', {
	asynchronous:true,
	method: "post" });
}
function setResolution(rid)
{
	new Ajax.Updater('issue_resolution', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>', {
	asynchronous:true,
	method: "post",
	parameters: { setresolution: rid },
	onSuccess: function (something) {
		Element.hide('edit_resolution');
		showMenu('workflow_actions');
		menuUnhover('workflow_actions_actions', '');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function getSeverities()
{
	Element.show('edit_severity');
	new Ajax.Updater('severities_table', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getseverities=true', {
	asynchronous:true,
	method: "post" });
}
function setSeverity(sid)
{
	new Ajax.Updater('issue_severity', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>', {
	asynchronous:true,
	method: "post",
	parameters: { setseverity: sid },
	onSuccess: function (something) {
		Element.hide('edit_severity');
		showMenu('workflow_actions');
		menuUnhover('workflow_actions_actions', '');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function getPriorities()
{
	Element.show('edit_priority');
	new Ajax.Updater('priorities_table', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getpriorities=true', {
	asynchronous:true,
	method: "post" });
}
function setPriority(pid)
{
	new Ajax.Updater('issue_priority', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>', {
	asynchronous:true,
	method: "post",
	parameters: { setpriority: pid },
	onSuccess: function (something) {
		Element.hide('edit_priority');
		showMenu('workflow_actions');
		menuUnhover('workflow_actions_actions', '');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function setBlocking(blocks)
{
	new Ajax.Updater('blocking_span', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>', {
	asynchronous:true,
	method: "post",
	parameters: { setblocking: blocks },
	onSuccess: function (something) {
		new Ajax.Updater('blocking_menu', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&updateblockingmenu=true', {
		asynchronous:true,
		method: "post" });
		showMenu('workflow_actions');
		menuUnhover('workflow_actions_actions', '');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function addTask()
{
	tinyMCE.triggerSave();
	var params = Form.serialize('issue_add_task');
	new Ajax.Request('include/viewissue_actions.inc.php', {
	asynchronous:true,
	method: "post",
	parameters: params,
	onSuccess: function (addTaskSuccess) {
		new Ajax.Updater('issue_tasks', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&gettasks=true', {
		asynchronous:true,
		method: "post",
		evalScripts: true
		});
		Element.hide('new_task');
		Form.reset('issue_add_task');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function updateTask(tid)
{
	tinyMCE.triggerSave();
	var params = Form.serialize('edit_task_' + tid + '_form');
	new Ajax.Request('include/viewissue_actions.inc.php', {
	asynchronous:true,
	method: "post",
	parameters: params,
	onSuccess: function (updateTaskSuccess) {
		new Ajax.Updater('task_' + tid + '_title', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&gettask_title=true', {
		asynchronous:true,
		method: "post",
		parameters: { t_id: tid }
		});
		new Ajax.Updater('task_' + tid + '_description', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&gettask_description=true', {
		asynchronous:true,
		method: "post",
		parameters: { t_id: tid }
		});
		getTaskLastUpdated(tid);
		Effect.Fade('edit_task_' + tid + '_details');
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function getTaskLastUpdated(tid)
{
	new Ajax.Updater('task_' + tid + '_lastupdated', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&gettask_lastupdated=true', {
	asynchronous:true,
	method: "post",
	parameters: { t_id: tid }
	});
}
function deleteTask(tid)
{
	new Ajax.Updater('task_status_list_' + tid, 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&deletetask=true', {
	asynchronous:true,
	method: "post",
	parameters: { t_id: tid },
	onSuccess: function (deleteTaskSuccess) {
		Effect.Fade('issuetask_' + tid, { duration: 0.5 });
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function promoteTask(tid)
{
	new Ajax.Updater('task_status_list_' + tid, 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&promotetask=true', {
	asynchronous:true,
	method: "post",
	parameters: { t_id: tid },
	onSuccess: function (promoteTaskSuccess) {
		Effect.Fade('issuetask_' + tid, { duration: 0.5 });
		getRelatedIssuesInline();
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function getTaskStatusList(tid)
{
	new Ajax.Updater('task_status_list_' + tid, 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getstatuslisttask=true', {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: { t_id: tid } });
}
function setTaskStatus(tid, sid)
{
	new Ajax.Updater('task_status_inline_' + tid, 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&settaskstatus=true', {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: { t_id: tid, status: sid },
	onSuccess: function (setTaskStatusSuccess) {
		Effect.Fade('task_status_' + tid, { duration: 0.5 });
		getTaskLastUpdated(tid);
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function setTaskClosed(tid, clsd)
{
	new Ajax.Updater('task_closed_' + tid, 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&task_setclosed=true', {
	asynchronous:true,
	method: "post",
	parameters: {t_id: tid, closed: clsd},
	onSuccess: function (something) {
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function submitVote()
{
	var params = Form.serialize('issue_vote_form');
	new Ajax.Updater('issue_vote_status', 'include/viewissue_actions.inc.php', {
	asynchronous:true,
	method: "post",
	parameters: params,
	onSuccess: function (submitVote) {
		new Ajax.Updater('issue_votes', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getvotes=true', {
		asynchronous:true,
		method: "post"
		});
	}
	});
}
function getAffectedStatusList(aid, atype)
{
	new Ajax.Updater('affected_status_list_' + aid + '_' + atype, 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getstatuslistaffected=true', {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: { a_id: aid, a_type: atype } });
}
function setAffectedStatus(aid, atype, sid)
{
	new Ajax.Updater('affected_status_inline_' + aid + '_' + atype, 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&setaffectedstatus=true', {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: { a_id: aid, status: sid, a_type: atype },
	onSuccess: function (something) {
		Effect.Fade('affected_status_' + aid + '_' + atype, { duration: 0.5 });
		getComments('core', 1, <?php echo $theIssue->getID(); ?>);
	}
	});
}
function getLogEntries()
{
	new Ajax.Updater('log_actions', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getlogentries=true', {
	asynchronous:true,
	method: "post"
	});
}
function removeUserIssue()
{
	new Ajax.Updater('userissue_status', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&watchlist=true&action=remove', {
	asynchronous:true,
	method: "post"
	});
}
function addUserIssue()
{
	new Ajax.Updater('userissue_status', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&watchlist=true&action=add', {
	asynchronous:true,
	method: "post"
	});
}

function deletePermission(pid)
{
	new Ajax.Request('include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&deleteaccess=1', {
	asynchronous:true,
	method: "post",
	parameters: {id: pid},
	onSuccess: function (deletePermissionSuccess) {
		getHiddenFrom();
		getAvailableTo();
	}
	});
}
function getHiddenFrom()
{
	new Ajax.Updater('issue_hiddenfrom', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&gethiddenfrom=true', {
	asynchronous:true,
	method: "post"
	});
}
function getAvailableTo()
{
	new Ajax.Updater('issue_availableto', 'include/viewissue_actions.inc.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&getavailableto=true', {
	asynchronous:true,
	method: "post"
	});
}*/
