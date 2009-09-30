<?php

	$light = "";
	$blue = "";

	if ($access_level == "full")
	{
		if (BUGScontext::getRequest()->getParameter('setmodulepermaccess') && is_numeric(BUGScontext::getRequest()->getParameter('allowed')) && BUGScontext::getRequest()->getParameter('module_perm_name') == $aPerm['permission_name'])
		{
			if ($aPerm['levels'] == 3)
			{
				switch (BUGScontext::getRequest()->getParameter('allowed'))
				{
					case 0:
						BUGScontext::removePermission($aPerm['permission_name'], $aPerm['target'], $theModule->getName(), $theuid, $gid, $tid);
						if (BUGScontext::getRequest()->getParameter('insertdeny') == 1)
						{
							BUGScontext::setPermission($aPerm['permission_name'], $aPerm['target'], $theModule->getName(), $theuid, $gid, $tid, 0, 1);
						}
						break;
					case 1:
						BUGScontext::removePermission($aPerm['permission_name'], $aPerm['target'], $theModule->getName(), $theuid, $gid, $tid);
						if (BUGScontext::getUser()->hasPermission($aPerm['permission_name'], $aPerm['target'], $theModule->getName()) == true)
						{
							BUGScontext::setPermission($aPerm['permission_name'], $aPerm['target'], $theModule->getName(), $theuid, $gid, $tid, 1, 0);
						}
						break;
					case 2:
						BUGScontext::removePermission($aPerm['permission_name'], $aPerm['target'], $theModule->getName(), $theuid, $gid, $tid);
						if (BUGScontext::getUser()->hasPermission($aPerm['permission_name'], $aPerm['target'], $theModule->getName()) == true || BUGScontext::getUser()->hasPermission($aPerm['permission_name'], $aPerm['target'], $theModule->getName()) == true)
						{
							BUGScontext::setPermission($aPerm['permission_name'], $aPerm['target'], $theModule->getName(), $theuid, $gid, $tid, 1, 0);
						}
						break;
				}
			}
			else
			{
				BUGScontext::setPermission($aPerm['permission_name'], $aPerm['target'], $theModule->getName(), $theuid, $gid, $tid, BUGScontext::getRequest()->getParameter('allowed'), BUGScontext::getRequest()->getParameter('insertdeny'));
			}
		}

		if ($aPerm['levels'] == 2)
		{
			$light = (BUGScontext::getUser()->hasPermission($aPerm['permission_name'], $aPerm['target'], $theModule->getName(), $theuid, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
			if (($gid != 0) || ($tid != 0) || ($theuid != 0))
			{
				$blue = BUGScontext::getAllPermissions($aPerm['permission_name'], $theuid, $tid, $gid, $aPerm['target']);
				$light = (count($blue) == 0) ? "lightblue" : $light;
				$lightaccess = ($light == "lightblue") ? 1 : 0;
				$insertdeny = ($light == "mediumgreen") ? 1 : 0;
			}
			else
			{
				$lightaccess = ($light == "red") ? 1 : 0;
				$insertdeny = ($light == "mediumgreen") ? 1 : 0;
			}
		}
		else
		{
			$light = (BUGScontext::getUser()->hasPermission($aPerm['permission_name'], $aPerm['target'], $theModule->getName(), $theuid, $gid, $tid, $all) == true) ? "mediumgreen" : ((BUGScontext::getUser()->hasPermission($aPerm['permission_name'], $aPerm['target'], $theModule->getName(), $theuid, $gid, $tid, $all) == true) ? "yellow" : "red");
			if ($gid != 0)
			{
				$blue = BUGScontext::getAllPermissions($aPerm['permission_name'], $theuid, $tid, $gid, $aPerm['target']);
				$blue = (count($blue) == 0) ? BUGScontext::getAllPermissions($aPerm['permission_name'], $theuid, $tid, $gid, $aPerm['target']) : $blue;
				$light = (count($blue) == 0) ? "lightblue" : $light;
				$lightaccess = ($light == "lightblue") ? 2 : (($light == "yellow") ? 1 : 0);
				$insertdeny = ($light == "mediumgreen") ? 1 : 0;
			}
			else
			{
				$lightaccess = ($light == "yellow") ? 1 : (($light == "red") ? 2 : 0);
			}

			$canlink = true;
		}


		if ($canlink == true)
		{
			?>
			<td style="width: 20px; padding: 1px;" align="left" valign="middle"><a class="image" href="<?php print $thelink . "&amp;module_perm=" . $theModule->getName() . "&amp;setmodulepermaccess=true&amp;module_perm_name=" . $aPerm['permission_name'] . "&amp;allowed=" . $lightaccess . "&amp;insertdeny=" . $insertdeny; ?>"><?php echo image_tag('led_' . $light . '.png'); ?></a></td>
			<?php
		}
		else
		{
			?>
			<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('led_' . $light . '.png'); ?></td>
			<?php
		}
	}
	else
	{
		$light = (BUGScontext::getUser()->hasPermission($aPerm['permission_name'], $aPerm['target'], $theModule->getName(), $theuid, $gid, $tid, $all) == true) ? "mediumgreen" : ((BUGScontext::getUser()->hasPermission($aPerm['permission_name'], $aPerm['target'], $theModule->getName(), $theuid, $gid, $tid, $all) == true) ? "yellow" : "red");
		if ($gid != 0)
		{
			$blue = BUGScontext::getAllPermissions($aPerm['permission_name'], $theuid, $tid, $gid, 0);
			$blue = (count($blue) == 0) ? BUGScontext::getAllPermissions($aPerm['permission_name'], $theuid, $tid, $gid, 0) : $blue;
			$light = (count($blue) == 0) ? "lightblue" : $light;
		}
		?>
		<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('led_' . $light . '.png'); ?></td>
		<?php
	}
?>