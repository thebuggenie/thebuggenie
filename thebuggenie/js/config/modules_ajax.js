thebuggenie.events.setEnabled = function(module)
{
	new Ajax.Updater('modulestrip_' + module, 'config.php?module=core&section=15&module_name=' + module + '&enabled=1', {
	asynchronous:true,
	method: "post"
	});
}

thebuggenie.events.setDisabled = function(module)
{
	new Ajax.Updater('modulestrip_' + module, 'config.php?module=core&section=15&module_name=' + module + '&enabled=0', {
	asynchronous:true,
	method: "post"
	});
}

thebuggenie.events.showInMenu = function(module)
{
	new Ajax.Updater('modulestrip_' + module, 'config.php?module=core&section=15&module_name=' + module + '&show_in_menu=1', {
	asynchronous:true,
	method: "post"
	});
}

thebuggenie.events.hideFromMenu = function(module)
{
	new Ajax.Updater('modulestrip_' + module, 'config.php?module=core&section=15&module_name=' + module + '&show_in_menu=0', {
	asynchronous:true,
	method: "post"
	});
}

thebuggenie.events.showInUserMenu = function(module)
{
	new Ajax.Updater('modulestrip_' + module, 'config.php?module=core&section=15&module_name=' + module + '&show_in_usermenu=1', {
	asynchronous:true,
	method: "post"
	});
}

thebuggenie.events.hideFromUserMenu = function(module)
{
	new Ajax.Updater('modulestrip_' + module, 'config.php?module=core&section=15&module_name=' + module + '&show_in_usermenu=0', {
	asynchronous:true,
	method: "post"
	});
}