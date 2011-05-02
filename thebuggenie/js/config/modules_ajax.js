function setEnabled(module)
{
	new Ajax.Updater('modulestrip_' + module, 'config.php?module=core&section=15&module_name=' + module + '&enabled=1', {
	asynchronous:true,
	method: "post"
	});
}

function setDisabled(module)
{
	new Ajax.Updater('modulestrip_' + module, 'config.php?module=core&section=15&module_name=' + module + '&enabled=0', {
	asynchronous:true,
	method: "post"
	});
}

function showInMenu(module)
{
	new Ajax.Updater('modulestrip_' + module, 'config.php?module=core&section=15&module_name=' + module + '&show_in_menu=1', {
	asynchronous:true,
	method: "post"
	});
}

function hideFromMenu(module)
{
	new Ajax.Updater('modulestrip_' + module, 'config.php?module=core&section=15&module_name=' + module + '&show_in_menu=0', {
	asynchronous:true,
	method: "post"
	});
}

function showInUserMenu(module)
{
	new Ajax.Updater('modulestrip_' + module, 'config.php?module=core&section=15&module_name=' + module + '&show_in_usermenu=1', {
	asynchronous:true,
	method: "post"
	});
}

function hideFromUserMenu(module)
{
	new Ajax.Updater('modulestrip_' + module, 'config.php?module=core&section=15&module_name=' + module + '&show_in_usermenu=0', {
	asynchronous:true,
	method: "post"
	});
}