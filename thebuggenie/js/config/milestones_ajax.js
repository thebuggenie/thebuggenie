function addMilestone(pid)
{
	var params = Form.serialize('add_milestone_form');
	new Ajax.Updater('milestones_span', 'config.php?module=core&p_id=' + pid + '&section=9', {
	asynchronous:true,
	method: "post",
	parameters: params,
	onSuccess: function (addMilestoneSuccess) {
		Form.reset('add_milestone_form');
		new Ajax.Updater('message_span', 'config.php?module=core&section=9&showmessage=true&themessage=addedmilestone', {
		asynchronous:true,
		evalScripts: true,
		method: "get"
		});
		Element.hide('nomilestones');
	},
	insertion: Insertion.Bottom
	});
}

function updateMilestone(pid, mid)
{
	var params = Form.serialize('edit_milestone_' + mid);
	new Ajax.Updater('milestone_span_' + mid, 'config.php?module=core&section=9&p_id=' + pid, {
	asynchronous:true,
	method: "post",
	parameters: params
	});
	Element.hide('edit_milestone_' + mid);
	Effect.Appear('show_milestone_' + mid, { duration: 0.5 });
}

function setMilestoneVisibility(pid, mid, visibility)
{
	new Ajax.Updater('milestone_span_' + mid, 'config.php?module=core&section=9&p_id=' + pid + '&setvisibility=true', {
	asynchronous:true,
	method: "post",
	parameters: { m_id: mid, visible: visibility }
	});
}

function deleteMilestone(pid, mid)
{
	new Ajax.Updater('milestones_span', 'config.php?module=core&section=9&p_id=' + pid + '&delete_milestone=true', {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: {m_id: mid },
	onSuccess: function (deleteMilestoneSuccess) {
		Effect.Fade('show_milestone_' + mid, { duration: 0.5 });
		Effect.Fade('delete_milestone_' + mid, { duration: 0.5 });
	},
	insertion: Insertion.Bottom
	});
}
