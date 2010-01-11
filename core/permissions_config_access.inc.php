<?php

	$light = "";
	$blue = "";

	if ($access_level == "full")
	{
		if (TBGContext::getRequest()->getParameter('setconfigaccess') && is_numeric(TBGContext::getRequest()->getParameter('allowed')) && TBGContext::getRequest()->getParameter('config_no') == $configno)
		{
			switch (TBGContext::getRequest()->getParameter('allowed'))
			{
				case 0:
					TBGContext::removePermission("b2readconfig", $configno, "core", $theuid, $gid, $tid);
					TBGContext::removePermission("b2saveconfig", $configno, "core", $theuid, $gid, $tid);
					if (TBGContext::getRequest()->getParameter('insertdeny') == 1)
					{
						TBGContext::setPermission("b2readconfig", $configno, "core", $theuid, $gid, $tid, 0, 1);
						TBGContext::setPermission("b2saveconfig", $configno, "core", $theuid, $gid, $tid, 0, 1);
					}
					break;
				case 1:
					TBGContext::removePermission("b2readconfig", $configno, "core", $theuid, $gid, $tid);
					if (TBGContext::getUser()->hasPermission("b2saveconfig", $configno, "core") == true)
					{
						TBGContext::setPermission("b2saveconfig", $configno, "core", $theuid, $gid, $tid, 1, 0);
					}
					break;
				case 2:
					TBGContext::removePermission("b2saveconfig", $configno, "core", $theuid, $gid, $tid);
					if (TBGContext::getUser()->hasPermission("b2readconfig", $configno, "core") == true || TBGContext::getUser()->hasPermission("b2saveconfig", $configno, "core") == true)
					{
						TBGContext::setPermission("b2readconfig", $configno, "core", $theuid, $gid, $tid, 1, 0);
					}
					break;
			}
		}
		$light = (TBGContext::getUser()->hasPermission("b2saveconfig", $configno, "core", $theuid, $gid, $tid, $all) == true) ? "mediumgreen" : ((TBGContext::getUser()->hasPermission("b2readconfig", $configno, "core", $theuid, $gid, $tid, $all) == true) ? "yellow" : "red");
		if ($gid != 0)
		{
			$blue = TBGContext::getAllPermissions("b2readconfig", $theuid, $tid, $gid, $configno);
			$blue = (count($blue) == 0) ? TBGContext::getAllPermissions("b2saveconfig", $theuid, $tid, $gid, $configno) : $blue;
			$light = (count($blue) == 0) ? "lightblue" : $light;
			$lightaccess = ($light == "lightblue") ? 2 : (($light == "yellow") ? 1 : 0);
			$insertdeny = ($light == "mediumgreen") ? 1 : 0;
		}
		else
		{
			$lightaccess = ($light == "yellow") ? 1 : (($light == "red") ? 2 : 0);
		}

		$canlink = false;
		if ($lightaccess == 2)
		{
			if (TBGContext::getUser()->hasPermission("b2readconfig", $configno, "core") == true || TBGContext::getUser()->hasPermission("b2saveconfig", $configno, "core") == true)
			{
				$canlink = true;
			}
		}
		elseif ($lightaccess == 1)
		{
			if (TBGContext::getUser()->hasPermission("b2saveconfig", $configno, "core") == true)
			{
				$canlink = true;
			}
			else
			{
				$lightaccess = 0;
				$insertdeny = 1;
				$canlink = true;
			}
		}
		else
		{
			$canlink = true;
		}

		if ($canlink == true)
		{
			?>
			<td style="width: 20px; padding: 1px;" align="left" valign="middle"><a class="image" href="<?php print $thelink . "&amp;config=1&amp;setconfigaccess=true&amp;config_no=$configno&amp;allowed=" . $lightaccess . "&amp;insertdeny=" . $insertdeny; ?>"><?php echo image_tag('led_' . $light . '.png'); ?></a></td>
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
		$light = (TBGContext::getUser()->hasPermission("b2saveconfig", $configno, "core", $theuid, $gid, $tid, $all) == true) ? "mediumgreen" : ((TBGContext::getUser()->hasPermission("b2readconfig", $configno, "core", $theuid, $gid, $tid, $all) == true) ? "yellow" : "red");
		if ($gid != 0)
		{
			$blue = TBGContext::getAllPermissions("b2readconfig", $theuid, $tid, $gid, 0);
			$blue = (count($blue) == 0) ? TBGContext::getAllPermissions("b2saveconfig", $theuid, $tid, $gid, 0) : $blue;
			$light = (count($blue) == 0) ? "lightblue" : $light;
		}
		?>
		<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('led_' . $light . '.png'); ?></td>
		<?php
	}
?>