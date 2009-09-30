<script type="text/javascript">

function getMonth(month, year, mode)
{
	new Ajax.Updater('calendar_' + mode + '_container', 'calendar_actions.inc.php?get_month=true&month=' + month + '&year=' + year + '&mode=' + mode, {
	asynchronous:true,
	method: "get"
	});
}

function getWeek(day, month, year)
{
	new Ajax.Updater('calendar_full_container', 'calendar_actions.inc.php?get_week=true&day=' + day + '&month=' + month + '&year=' + year, {
	asynchronous:true,
	method: "get"
	});
}

function getOverview(day, month, year)
{
	new Ajax.Updater('calendar_full_container', 'calendar_actions.inc.php?get_day=true&day=' + day + '&month=' + month + '&year=' + year, {
	asynchronous:true,
	method: "get"
	});
}

</script>