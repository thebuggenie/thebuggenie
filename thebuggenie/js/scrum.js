function addUserStory(url)
{
	var params = Form.serialize('add_user_story_form');
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: params,
	onLoading: function (transport) {
		$('user_story_add_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			failedMessage(json.error);
			$('user_story_add_indicator').hide();
			$('message_failed').show();
		}
		else
		{
			Form.reset('add_user_story_form');
			$('message_failed').hide();
			$('user_story_add_indicator').hide();
			$('message_user_story_added').show();
			$('scrum_unassigned_list').insert({bottom: json.content});
			new Draggable('scrum_story_' + json.story_id, { revert: true });
			new Effect.Fade('message_user_story_added', {delay: 20} );
		}
	},
	onFailure: function (transport) {
		$('user_story_add_indicator').hide();
	},
	insertion: Insertion.Bottom
	});
}

function assignStory(url, dragged, dropped)
{
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: { story_id: $(dragged.id + '_id').getValue(), sprint_id: $(dropped.id + '_id').getValue() },
	onLoading: function (transport) {
		$(dropped.id + '_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			failedMessage(json.error);
			$(dropped.id + '_indicator').hide();
			$('message_failed').show();
		}
		else
		{
			$('message_failed').hide();
			$(dropped.id + '_indicator').hide();
			dropped.insert(Element.remove(dragged), { insertion: Insertion.Bottom, queue: 'end' });
			dragged.highlight({ queue: 'end' });
			$('scrum_sprint_'+json.old_sprint_id+'_issues').update(json.old_issues);
			$('scrum_sprint_'+json.new_sprint_id+'_issues').update(json.new_issues);
			$('message_user_story_assigned').show();
			new Effect.Fade('message_user_story_assigned', {delay: 20} );
		}
	},
	onFailure: function (transport) {
		$(dropped.id + '_indicator').hide();
	}
	});
	
}