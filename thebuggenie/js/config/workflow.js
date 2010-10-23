function deleteTransition(url, transition_id, direction)
{
	var trans_sib = $('transition_' + transition_id).next(1);
	var params = "&direction=" + direction;
	_updateDivWithJSONFeedback(url, null, 'delete_transition_' + transition_id + '_indicator', null, false, null, ['transition_' + transition_id, trans_sib, 'delete_transition_' + transition_id + '_confirm'], null, "post", params);
}
