function deleteTransition(url, transition_id, direction)
{
	var trans_sib = $('transition_' + transition_id).next(1);
	var params = "&direction=" + direction;
	_updateDivWithJSONFeedback(url, null, 'delete_transition_' + transition_id + '_indicator', null, false, null, ['transition_' + transition_id, trans_sib, 'delete_transition_' + transition_id + '_confirm'], null, "post", params);
}

function copyWorkflowScheme(url, scheme_id)
{
	_postFormWithJSONFeedback(url, 'copy_workflow_scheme_' + scheme_id + '_form', 'copy_workflow_scheme_' + scheme_id + '_indicator', 'copy_scheme_' + scheme_id + '_popup', 'workflow_schemes_list', true);
}

function deleteWorkflowScheme(url, scheme_id)
{
	_postFormWithJSONFeedback(url, 'delete_workflow_scheme_' + scheme_id + '_form', 'delete_workflow_scheme_' + scheme_id + '_indicator', ['delete_scheme_' + scheme_id + '_popup', 'copy_scheme_' + scheme_id + '_popup', 'workflow_scheme_' + scheme_id], 'workflow_schemes_list', true);
}

function copyWorkflow(url, workflow_id)
{
	_postFormWithJSONFeedback(url, 'copy_workflow_' + workflow_id + '_form', 'copy_workflow_' + workflow_id + '_indicator', 'copy_workflow_' + workflow_id + '_popup', 'workflows_list', true);
}

function deleteWorkflow(url, workflow_id)
{
	_postFormWithJSONFeedback(url, 'delete_workflow_' + workflow_id + '_form', 'delete_workflow_' + workflow_id + '_indicator', ['delete_workflow_' + workflow_id + '_popup', 'copy_workflow_' + workflow_id + '_popup', 'workflow_' + workflow_id], 'workflows_list', true);
}

function updateWorkflowScheme(url, scheme_id)
{
	_postFormWithJSONFeedback(url, 'workflow_scheme_form', 'workflow_scheme_indicator');
}