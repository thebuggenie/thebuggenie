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
