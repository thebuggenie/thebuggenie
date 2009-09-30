
function saveAccountInfo()
{
	var params = Form.serialize('update_account_form');
	new Ajax.Updater('account_main', 'include/account_actions.inc.php', {
	asynchronous:true,
	method: "post",
	parameters: params
	});
}

function getAccountInfo()
{
	new Ajax.Updater('account_main', 'include/account_actions.inc.php', {
	asynchronous:true,
	method: "get"
	});
}

function getEditAccount()
{
	new Ajax.Updater('account_main', 'include/account_actions.inc.php?edit_details=true', {
	asynchronous:true,
	method: "get"
	});
}

function setAvatar(avatar)
{
	new Ajax.Updater('avatar_td', 'include/account_actions.inc.php', {
	asynchronous:true,
	method: "get",
	parameters: { set_avatar: avatar }
	});
	Effect.Fade('avatarlist', { duration: 0.5 });
}
