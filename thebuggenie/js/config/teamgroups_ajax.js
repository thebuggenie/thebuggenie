function getEditGroup(gid)
{
	new Ajax.Updater('show_group_' + gid, 'config.php?module=core&section=1&get_editgroup=true', {
	asynchronous:true,
	method: "get",
	evalScripts: true,
	parameters: {group: gid }
	});
}

function getShowGroup(gid)
{
	new Ajax.Updater('show_group_' + gid, 'config.php?module=core&section=1&get_showgroup=true', {
	asynchronous:true,
	method: "get",
	evalScripts: true,
	parameters: {group: gid}
	});
}

function updateGroup(gid)
{
	var params = Form.serialize('edit_group_' + gid + '_form');
	new Ajax.Updater('show_group_' + gid, 'config.php?module=core&section=1', {
	asynchronous:true,
	method: "post",
	parameters: params
	});
}

function getEditTeam(tid)
{
	new Ajax.Updater('show_team_' + tid, 'config.php?module=core&section=1&get_editteam=true', {
	asynchronous:true,
	method: "get",
	evalScripts: true,
	parameters: {team: tid }
	});
}

function addGroup()
{
	var params = Form.serialize('add_group_form');
	new Ajax.Updater('group_list', 'config.php?module=core&section=1', {
	asynchronous:true,
	method: "post",
	parameters: params,
	insertion: 'bottom',
	onSuccess: 	function (request)
				{
					Form.reset('add_group_form');
				}
	});
}

function addTeam()
{
	var params = Form.serialize('add_team_form');
	new Ajax.Updater('team_list', 'config.php?module=core&section=1', {
	asynchronous:true,
	method: "post",
	parameters: params,
	insertion: 'bottom',
	onSuccess: 	function (request)
				{
					Form.reset('add_team_form');
				}
	});
}

function getShowTeam(tid)
{
	new Ajax.Updater('show_team_' + tid, 'config.php?module=core&section=1&get_showteam=true', {
	asynchronous:true,
	method: "get",
	evalScripts: true,
	parameters: {team: tid}
	});
}

function updateTeam(tid)
{
	var params = Form.serialize('edit_team_' + tid + '_form');
	new Ajax.Updater('show_team_' + tid, 'config.php?module=core&section=1', {
	asynchronous:true,
	method: "post",
	parameters: params
	});
}
