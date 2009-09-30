<div class="menu_top"></div>

<table class="menu_strip" cellpadding=0 cellspacing=0 width="100%" style="table-layout: auto;">
<tr>
<td class="menu_container">
<table cellpadding=0 cellspacing=0>
<tr>
<td align="left" valign="middle" class="unselected"><?php print $striptitle; ?></td>
</tr>
</table>

<?php

	if (BUGScontext::getUser()->hasPermission("b2no".$page."access", 0, 'core'))
	{
		bugs_moveTo(BUGSsettings::get('returnfromlogout'));
		exit;
	}

?>