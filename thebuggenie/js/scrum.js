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
		}
		else
		{
			Form.reset('add_user_story_form');
			$('user_story_add_indicator').hide();
			$('scrum_sprint_0_list').insert({bottom: json.content});
			$('scrum_sprint_0_unassigned').hide();
			new Draggable('scrum_story_' + json.story_id, {revert: true});
		}
	},
	onFailure: function (transport) {
		$('user_story_add_indicator').hide();
	}
	});
}

function addSprint(url, assign_url)
{
	var params = Form.serialize('add_sprint_form');
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	evalScripts: true,
	parameters: params,
	onLoading: function (transport) {
		$('sprint_add_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			failedMessage(json.error);
			$('sprint_add_indicator').hide();
		}
		else
		{
			Form.reset('add_sprint_form');
			//$('message_failed').hide();
			$('no_sprints').hide();
			$('sprint_add_indicator').hide();
			//$('message_sprint_added').show();
			$('scrum_sprints').insert({bottom: json.content});
			Droppables.add('scrum_sprint_' + json.sprint_id, {hoverclass: 'highlighted', onDrop: function (dragged, dropped, event) {assignStory(assign_url, dragged, dropped)}});
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
	parameters: {story_id: $(dragged.id + '_id').getValue(), sprint_id: $(dropped.id + '_id').getValue()},
	onLoading: function (transport) {
		$(dropped.id + '_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			failedMessage(json.error);
			$(dropped.id + '_indicator').hide();
		}
		else
		{
			$(dropped.id + '_indicator').hide();
			$(dropped.id + '_list').insert(Element.remove(dragged), {insertion: Insertion.Bottom, queue: 'end'});
			$('scrum_sprint_' + json.old_sprint_id + '_issues').update(json.old_issues);
			$('scrum_sprint_' + json.new_sprint_id + '_issues').update(json.new_issues);
			$('scrum_sprint_' + json.old_sprint_id + '_estimated_points').update(json.old_estimated_points);
			$('scrum_sprint_' + json.new_sprint_id + '_estimated_points').update(json.new_estimated_points);
			$('scrum_sprint_' + json.old_sprint_id + '_estimated_hours').update(json.old_estimated_hours);
			$('scrum_sprint_' + json.new_sprint_id + '_estimated_hours').update(json.new_estimated_hours);
			($('scrum_sprint_' + json.old_sprint_id + '_list').childElements().size() == 0) ? $('scrum_sprint_' + json.old_sprint_id + '_unassigned').show() : $('scrum_sprint_' + json.old_sprint_id + '_unassigned').hide();
			($('scrum_sprint_' + json.new_sprint_id + '_list').childElements().size() == 0) ? $('scrum_sprint_' + json.new_sprint_id + '_unassigned').show() : $('scrum_sprint_' + json.new_sprint_id + '_unassigned').hide();
			//$('message_user_story_assigned').show();
			//new Effect.Fade('message_user_story_assigned', {delay: 20} );
		}
	},
	onFailure: function (transport) {
		$(dropped.id + '_indicator').hide();
	}
	});
}

function setStoryColor(url, story_id, color)
{
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	parameters: {color: color},
	onLoading: function (transport) {
		$('color_selector_' + story_id + '_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			failedMessage(json.error);
			$('color_selector_' + story_id + '_indicator').hide();
			$('color_selector_' + story_id).hide();
		}
		else
		{
			$('color_selector_' + story_id + '_indicator').hide();
			$('color_selector_' + story_id).hide();
			$('story_color_' + story_id).style.backgroundColor = color;
		}
	},
	onFailure: function (transport) {
		$('color_selector_' + story_id + '_indicator').hide();
		$('color_selector_' + story_id).hide();
	}
	});
}

function setStoryEstimates(url, story_id)
{
	var params = {};
	if ($('scrum_story_' + story_id + '_points_input') && $('scrum_story_' + story_id + '_hours_input'))
	{
		params = { estimated_points: $('scrum_story_' + story_id + '_points_input').getValue(), estimated_hours: $('scrum_story_' + story_id + '_hours_input').getValue() };
	}
	else if ($('scrum_story_' + story_id + '_hours_input'))
	{
		params = { estimated_hours: $('scrum_story_' + story_id + '_hours_input').getValue() };
	}
	else if ($('scrum_story_' + story_id + '_points_input'))
	{
		params = { estimated_points: $('scrum_story_' + story_id + '_points_input').getValue() };
	}
	new Ajax.Request(url, {
	asynchronous:true,
	method: "post",
	parameters: params,
	onLoading: function (transport) {
		$('point_selector_' + story_id + '_indicator').show();
	},
	onSuccess: function (transport) {
		var json = transport.responseJSON;
		if (json.failed)
		{
			failedMessage(json.error);
			$('point_selector_' + story_id + '_indicator').hide();
			$('scrum_story_' + story_id + '_estimation').hide();
		}
		else
		{
			$('point_selector_' + story_id + '_indicator').hide();
			$('scrum_story_' + story_id + '_estimation').hide();
			if ($('scrum_story_' + story_id + '_points'))
			{
				$('scrum_story_' + story_id + '_points').update(json.points);
			}
			if ($('scrum_story_' + story_id + '_hours'))
			{
				$('scrum_story_' + story_id + '_hours').update(json.hours);
				if ($('selected_burndown_image'))
				{
					reloadImage('selected_burndown_image');
				}
			}
			$('scrum_sprint_' + json.sprint_id + '_estimated_points').update(json.new_estimated_points);
			$('scrum_sprint_' + json.sprint_id + '_remaining_points').update(json.new_remaining_points);
			$('scrum_sprint_' + json.sprint_id + '_estimated_hours').update(json.new_estimated_hours);
			$('scrum_sprint_' + json.sprint_id + '_remaining_hours').update(json.new_remaining_hours);
		}
	},
	onFailure: function (transport) {
		$('point_selector_' + story_id + '_indicator').hide();
		$('scrum_story_' + story_id + '_estimation').hide();
	}
	});
}