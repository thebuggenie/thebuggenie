thebuggenie.events.deleteTransition = function(url, transition_id, direction)
{
	var trans_sib = $('transition_' + transition_id).next(1);
	var params = "&direction=" + direction;
thebuggenie.utils.updateDivWithJSONFeedback(url, null, 'delete_transition_' + transition_id + '_indicator', null, false, null, ['transition_' + transition_id, trans_sib, 'delete_transition_' + transition_id + '_confirm'], null, "post", params);
}

thebuggenie.events.copyWorkflowScheme = function(url, scheme_id)
{
thebuggenie.utils.postFormWithJSONFeedback(url, 'copy_workflow_scheme_' + scheme_id + '_form', 'copy_workflow_scheme_' + scheme_id + '_indicator', 'copy_scheme_' + scheme_id + '_popup', 'workflow_schemes_list', true);
}

thebuggenie.events.deleteWorkflowScheme = function(url, scheme_id)
{
thebuggenie.utils.postFormWithJSONFeedback(url, 'delete_workflow_scheme_' + scheme_id + '_form', 'delete_workflow_scheme_' + scheme_id + '_indicator', ['delete_scheme_' + scheme_id + '_popup', 'copy_scheme_' + scheme_id + '_popup', 'workflow_scheme_' + scheme_id], 'workflow_schemes_list', true);
}

thebuggenie.events.updateWorkflowLinks = function(json)
{
	if ($('current_workflow_num_count'))
	{
		$('current_workflow_num_count').update(json.total_count);
	}
	$$('.copy_workflow_link').each(function (element) {
		if (json.more_available)
		{
			$(element).show();
		}
		else
		{
			$(element).hide();
		}
	});
}

thebuggenie.events.copyWorkflow = function(url, workflow_id)
{
thebuggenie.utils.postFormWithJSONFeedback(url, 'copy_workflow_' + workflow_id + '_form', 'copy_workflow_' + workflow_id + '_indicator', 'copy_workflow_' + workflow_id + '_popup', 'workflows_list', true, null, null, updateWorkflowLinks);
}

thebuggenie.events.deleteWorkflow = function(url, workflow_id)
{
thebuggenie.utils.postFormWithJSONFeedback(url, 'delete_workflow_' + workflow_id + '_form', 'delete_workflow_' + workflow_id + '_indicator', ['delete_workflow_' + workflow_id + '_popup', 'copy_workflow_' + workflow_id + '_popup', 'workflow_' + workflow_id], 'workflows_list', true, null, null, updateWorkflowLinks);
}

thebuggenie.events.updateWorkflowScheme = function(url, scheme_id)
{
thebuggenie.utils.postFormWithJSONFeedback(url, 'workflow_scheme_form', 'workflow_scheme_indicator');
}

thebuggenie.events.addWorkflowTransitionValidationRule = function(url, mode)
{
thebuggenie.utils.postFormWithJSONFeedback(url, 'workflowtransition' + mode + 'validationrule_add_form', 'workflowtransition' + mode + 'validationrule_add_indicator', ['no_workflowtransition' + mode + 'validationrules', 'add_workflowtransition' + mode + 'validationrule_' + $('workflowtransition' + mode + 'validationrule_add_type').getValue()], 'workflowtransition' + mode + 'validationrules_list', true);
}

thebuggenie.events.updateWorkflowTransitionValidationRule = function(url, rule_id)
{
thebuggenie.utils.postFormWithJSONFeedback(url, 'workflowtransitionvalidationrule_' + rule_id + '_form', 'workflowtransitionvalidationrule_' + rule_id + '_indicator', ['workflowtransitionvalidationrule_' + rule_id + '_cancel_button', 'workflowtransitionvalidationrule_' + rule_id + '_edit'], 'workflowtransitionvalidationrule_' + rule_id + '_value', false, ['workflowtransitionvalidationrule_' + rule_id + '_edit_button', 'workflowtransitionvalidationrule_' + rule_id + '_delete_button', 'workflowtransitionvalidationrule_' + rule_id + '_description']);
}

thebuggenie.events.deleteWorkflowTransitionValidationRule = function(url, rule_id, type, mode)
{
thebuggenie.utils.updateDivWithJSONFeedback(url, null, 'workflowtransitionvalidationrule_' + rule_id + '_delete_indicator', false, false, null, ['workflowtransitionvalidationrule_' + rule_id + '_delete', 'workflowtransitionvalidationrule_' + rule_id], ['add_workflowtransition' + type + 'validationrule_' + mode], 'post');
}

thebuggenie.events.addWorkflowTransitionAction = function(url)
{
thebuggenie.utils.postFormWithJSONFeedback(url, 'workflowtransitionaction_add_form', 'workflowtransitionaction_add_indicator', ['no_workflowtransitionactions', 'add_workflowtransitionaction_' + $('workflowtransitionaction_add_type').getValue()], 'workflowtransitionactions_list', true);
}

thebuggenie.events.updateWorkflowTransitionAction = function(url, action_id)
{
thebuggenie.utils.postFormWithJSONFeedback(url, 'workflowtransitionaction_' + action_id + '_form', 'workflowtransitionaction_' + action_id + '_indicator', ['workflowtransitionaction_' + action_id + '_cancel_button', 'workflowtransitionaction_' + action_id + '_edit'], 'workflowtransitionaction_' + action_id + '_value', false, ['workflowtransitionaction_' + action_id + '_edit_button', 'workflowtransitionaction_' + action_id + '_delete_button', 'workflowtransitionaction_' + action_id + '_description']);
}

thebuggenie.events.deleteWorkflowTransitionAction = function(url, action_id, type)
{
thebuggenie.utils.updateDivWithJSONFeedback(url, null, 'workflowtransitionaction_' + action_id + '_delete_indicator', false, false, null, ['workflowtransitionaction_' + action_id + '_delete', 'workflowtransitionaction_' + action_id], ['add_workflowtransitionaction_' + type], 'post');
}
