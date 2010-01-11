	<div style="padding: 5px;<?php print ($theuid != 0) ? "padding-left: 0px;" : ""; ?>">
	A blue icon means the selected <?php (TBGContext::getRequest()->getParameter('group')) ? print "group" : ((TBGContext::getRequest()->getParameter('user')) ? print "user" : print "team"); ?> has the access specified by the "Everyone" group. A green icon means it has full access to the specified section. For sections where you can have three levels of access, read-access is marked with a yellow icon. A red icon means it does not have access to that section. <?php if ($access_level == "full") { ?>Click the icon to change permissions for that section.<?php } ?>
	<table style="width: 100%; padding-top: 5px;" cellpadding=0 cellspacing=0>
		<tr>
			<td style="width: 50%; padding: 5px; padding-left: 0px;" align="left" valign="top">
				<?php if ($theuid != 0): ?>
					<div style="border-bottom: 1px solid #DDD;"><b>User-specific permissions</b> (click link to edit)</div>
					<table style="width: 100%; margin-top: 3px; table-layout: fixed;" cellpadding=0 cellspacing=0>
						<tr<?php if (TBGContext::getRequest()->getParameter('user_perm') == 1) { print " class=\"p_sel\""; } ?>>
							<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_general.png'); ?></td>
							<td style="width: auto; padding: 1px;" align="left" valign="middle"><a href="<?php print $thelink; ?>&amp;user_perm=1" style="font-weight: bold;">User-specific permissions</a></td>
						</tr>
					</table>
					<br>
				<?php endif; ?>
				<div style="border-bottom: 1px solid #DDD;"><b>General access permissions</b> (click link to edit)</div>
				<table style="width: 100%; margin-top: 3px; table-layout: fixed;" cellpadding=0 cellspacing=0>
					<tr<?php if (TBGContext::getRequest()->getParameter('general_perm') == 1) { print " class=\"p_sel\""; } ?>>
						<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_general.png'); ?></td>
						<td style="width: auto; padding: 1px;" align="left" valign="middle"><a href="<?php print $thelink; ?>&amp;general_perm=1" style="font-weight: bold;">General permissions</a></td>
					</tr>
				</table>
				<div style="margin-top: 10px; border-bottom: 1px solid #DDD;"><b>Projects</b> (click a project to select it)</div>
				<table style="width: 100%; margin-top: 3px; table-layout: fixed;" cellpadding=0 cellspacing=0>
					<?php
					
					foreach (TBGProject::getAll() as $aProject)
					{
						$aProject = new TBGProject($aProject['id']);
						?>
						<tr<?php if (TBGContext::getRequest()->getParameter('p_id') == $aProject->getID()) { print " class=\"p_sel\""; } ?>>
							<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('icon_project.png'); ?></td>
							<td style="width: auto; padding: 1px; font-weight: bold;" align="left" valign="middle"><a href="<?php print $thelink . "&amp;p_id=" . $aProject->getID(); ?>"><?php print $aProject->getName(); ?></a></td>
							<?php
							if ($access_level == "full")
							{
								if (TBGContext::getRequest()->getParameter('setprojectaccess') && TBGContext::getRequest()->getParameter('p_id') == $aProject->getID() && is_numeric(TBGContext::getRequest()->getParameter('allowed')))
								{
									TBGContext::setPermission("b2projectaccess", $aProject->getID(), "core", $theuid, $gid, $tid, TBGContext::getRequest()->getParameter('allowed'), TBGContext::getRequest()->getParameter('insertdeny'));
								}
								$light = (TBGContext::getUser()->hasPermission("b2projectaccess", $aProject->getID(), "core", $theuid, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
								if (($gid != 0) || ($tid != 0) || ($theuid != 0))
								{
									$blue = TBGContext::getAllPermissions("b2projectaccess", $theuid, $tid, $gid, $aProject->getID());
									$light = (count($blue) == 0) ? "lightblue" : $light;
									$lightaccess = ($light == "lightblue") ? 1 : 0;
									$insertdeny = ($light == "mediumgreen") ? 1 : 0;
								}
								else
								{
									$lightaccess = ($light == "red") ? 1 : 0;
									$insertdeny = ($light == "mediumgreen") ? 1 : 0;
								}
								//echo '<br>light for uid' . $theuid . ', gid' . $gid . ', tid' . $tid . 'and all' . $all . ' : ' . $aProject->getID() . $light . '<br>';
								?>
								<td style="width: 20px; padding: 1px;" align="left" valign="middle"><a class="image" href="<?php print $thelink . "&amp;setprojectaccess=true&amp;allowed=" . $lightaccess . "&amp;p_id=" . $aProject->getID() . "&amp;insertdeny=" . $insertdeny; ?>"><?php echo image_tag('led_' . $light . '.png'); ?></a></td>
								<?php
							}
							else
							{
								$light = (TBGContext::getUser()->hasPermission("b2projectaccess", $aProject->getID(), "core", 0, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
								if (($gid != 0) || ($tid != 0) || ($theuid != 0))
								{
									$blue = TBGContext::getAllPermissions("b2projectaccess", $theuid, $tid, $gid, $aProject->getID());
									$light = (count($blue) == 0) ? "lightblue" : $light;
								}
								?>
								<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('led_' . $light . '.png'); ?></td>
								<?php
							}
							?>
						</tr>
						<?php
					}
					if (count(TBGProject::getAll()) == 0)
					{
						?>
						<tr><td style="color: #C5C5C5;">(There are no projects)</td></tr>
						<?php
					}
					else
					{
						?>
						<tr>
							<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('icon_newissue.png'); ?></td>
								<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle">Report issues</td>
								<?php
								if ($access_level == "full")
								{
									if (TBGContext::getRequest()->getParameter('setreportissueaccess') && is_numeric(TBGContext::getRequest()->getParameter('allowed')))
									{
										TBGContext::setPermission('b2canreportissues', 0, "core", $theuid, $gid, $tid, TBGContext::getRequest()->getParameter('allowed'), TBGContext::getRequest()->getParameter('insertdeny'));
									}
									$light = (TBGContext::getUser()->hasPermission('b2canreportissues', 0, "core", $theuid, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
									if (($gid != 0) || ($tid != 0) || ($theuid != 0))
									{
										$blue = TBGContext::getAllPermissions('b2canreportissues', $theuid, $tid, $gid);
										$light = (count($blue) == 0) ? "lightblue" : $light;
										$lightaccess = ($light == "lightblue") ? 1 : 0;
										$insertdeny = ($light == "mediumgreen") ? 1 : 0;
									}
									else
									{
										$lightaccess = ($light == "red") ? 1 : 0;
										$insertdeny = ($light == "mediumgreen") ? 1 : 0;
									}
									?>
									<td style="width: 20px; padding: 1px;" align="left" valign="middle"><a class="image" href="<?php print $thelink . "&amp;setreportissueaccess=true&amp;allowed=" . $lightaccess . "&amp;insertdeny=" . $insertdeny; ?>"><?php echo image_tag('led_' . $light . '.png'); ?></a></td>
									<?php
								}
								else
								{
									$light = (TBGContext::getUser()->hasPermission('b2canreportissues', 0, "core", $theuid, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
									if (($gid != 0) || ($tid != 0) || ($theuid != 0))
									{
										$blue = TBGContext::getAllPermissions('b2canreportissues', $theuid, $tid, $gid);
										$light = (count($blue) == 0) ? "lightblue" : $light;
									}
									?>
									<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('led_' . $light . '.png'); ?></td>
									<?php
								}
								?>
						</tr>
						<tr<?php if (is_numeric(TBGContext::getRequest()->getParameter('issuespermrest'))) { print " class=\"p_sel\""; } ?>>
							<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('icon_issuespermrest.png'); ?></td>
							<td style="width: auto; padding: 1px; font-weight: normal;" colspan=2 align="left" valign="middle"><a href="<?php print $thelink . "&amp;issuespermrest=1"; ?>">Issue-specific access privileges</a></td>
						</tr>
						<?php
					}
					?>
				</table>
				<div style="padding-top: 10px; border-bottom: 1px solid #DDD;"><b>Modules</b></div>
				<table style="width: 100%; margin-top: 3px; table-layout: fixed;" cellpadding=0 cellspacing=0>
				<?php

					//$bugs_modules = bugs_getModules("",array("id", "module_longname", "module_name"),"");
					foreach (TBGContext::getModules() as $aModule)
					{
						// if (bugs_getModulePermissions($aModule['module_name']) == true)
						//$aModule = new TBGModule(1);
						if ($aModule->hasAccess() == true)
						{
							//$module_name = $aModule->getName();
							//$module_permissions = bugs_getModulePermissionConfig($aModule->getName())
							?>
							<tr>
								<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_modules.png'); ?></td>
								<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle">
								<?php

									if (count($aModule->getAvailablePermissions()) == 0)
									{
										print $aModule->getLongname();
									}
									else
									{
										echo '<a href="' . $thelink . '&amp;module_perm=' . $aModule->getName() . '">' . $aModule->getLongname() . '</a>';
									}
									
								?></td>
								<?php
									if ($access_level == "full")
									{
										if (TBGContext::getRequest()->getParameter('setmoduleaccess') && TBGContext::getRequest()->getParameter('module_name') == $aModule->getName() && is_numeric(TBGContext::getRequest()->getParameter('allowed')))
										{
											TBGContext::getModule(TBGContext::getRequest()->getParameter('module_name'))->setPermission($theuid, $gid, $tid, TBGContext::getRequest()->getParameter('allowed'), TBGContext::getRequest()->getParameter('insertdeny'));
										}
										// $light = (bugs_getModulePermissions($aModule['module_name'], $theuid, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
										$light = ($aModule->hasAccess($theuid, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
										if (($gid != 0) || ($tid != 0) || ($theuid != 0))
										{
											$blue = $aModule->isAccessPermissionCached($theuid, $gid, $tid); // bugs_getAllModulePermissions($aModule->getName(), $theuid, $tid, $gid);
											$light = (!$blue) ? "lightblue" : $light;
											$lightaccess = ($light == "lightblue") ? 1 : 0;
											$insertdeny = ($light == "mediumgreen") ? 1 : 0;
										}
										else
										{
											$lightaccess = ($light == "red") ? 1 : 0;
											$insertdeny = ($light == "mediumgreen") ? 1 : 0;
										}
										?>
										<td style="width: 20px; padding: 1px;" align="left" valign="middle"><a class="image" href="<?php print $thelink . "&amp;setmoduleaccess=true&amp;allowed=" . $lightaccess . "&amp;module_name=" . $aModule->getName() . "&amp;insertdeny=" . $insertdeny; ?>"><?php echo image_tag('led_' . $light . '.png'); ?></a></td>
										<?php
									}
									else
									{
										// $light = (bugs_getModulePermissions($aModule['module_name'], $theuid, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
										$light = ($aModule->hasAccess($theuid, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
										if (($gid != 0) || ($tid != 0) || ($theuid != 0))
										{
											$blue = TBGModule::getAllModulePermissions($aModule->getName(), $theuid, $tid, $gid);
											$light = (count($blue) == 0) ? "lightblue" : $light;
										}
										?>
										<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('led_' . $light . '.png'); ?></td>
										<?php
									}
								?>
							</tr>
							<?php
						}
					}
					if (count(TBGContext::getModules()) == 0)
					{
						?>
						<tr><td>&nbsp;</td><td colspan=2 style="color: #C5C5C5;">(There are no modules)</td></tr>
						<?php
					}

				?>
				</table>
				<div style="padding-top: 10px; border-bottom: 1px solid #DDD;"><b>Sections</b></div>
				<table style="width: 100%; margin-top: 3px; table-layout: fixed;" cellpadding=0 cellspacing=0>
				<?php

					$pages = array();
					$pages[] = array("index", "Frontpage");
					$pages[] = array("about", "About");
					$pages[] = array("account", "My Account");
					$pages[] = array("login", "Login / Register");

					foreach ($pages as $aPage)
					{

						?>
						<tr>
							<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('icon_page.png'); ?></td>
							<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php print $aPage[1]; ?></td>
							<?php
							$pagename = $aPage[0];
							if ($access_level == "full")
							{
								if (TBGContext::getRequest()->getParameter('setpageaccess') && TBGContext::getRequest()->getParameter('page') == $pagename && is_numeric(TBGContext::getRequest()->getParameter('allowed')))
								{
									if (TBGContext::getRequest()->getParameter('allowed') == 1 && TBGContext::getRequest()->getParameter('insertdeny') == 1)
									{
										TBGContext::removePermission("b2no${pagename}access", 0, "core", $theuid, $gid, $tid);
									}
									else
									{
										if (TBGContext::getUser()->hasPermission("b2no${pagename}access", 0, "core", TBGContext::getUser()->getUID(), $gid, $tid) == false)
										{
											#print "fu";
											TBGContext::setPermission("b2no${pagename}access", 0, "core", $theuid, $gid, $tid, TBGContext::getRequest()->getParameter('allowed'), TBGContext::getRequest()->getParameter('insertdeny'));
										}
									}
								}
								$light = (TBGContext::getUser()->hasPermission("b2no${pagename}access", 0, "core", $theuid, $gid, $tid, $all) == false) ? "mediumgreen" : "red";
								#print $light;
								if (($gid != 0) || ($tid != 0) || ($theuid != 0))
								{
									$blue = TBGContext::getAllPermissions("b2no${pagename}access", $theuid, $tid, $gid, 0);
									$light = (count($blue) == 0) ? "lightblue" : $light;
									$lightaccess = ($light == "lightblue") ? 0 : 1;
									$insertdeny = ($light == "mediumgreen") ? 0 : 1;
								}
								else
								{
									$lightaccess = ($light == "red") ? 0 : 1;
									$insertdeny = ($light == "red") ? 1 : 0;
								}

								$canlink = false;
								if ($lightaccess == 1)
								{
									if (TBGContext::getUser()->hasPermission("b2no${pagename}access", 0, "core", TBGContext::getUser()->getUID(), $gid, $tid) == false)
									{
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
									<td style="width: 20px; padding: 1px;" align="left" valign="middle"><a class="image" href="<?php print $thelink . "&amp;setpageaccess=true&amp;allowed=" . $lightaccess . "&amp;page=${pagename}&amp;insertdeny=" . $insertdeny; ?>"><?php echo image_tag('led_' . $light . '.png'); ?></a></td>
									<?php
								}
								else
								{
									?>
									<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('led_' . $light . '.png'); ?></td>
									<?php
								}
								?>
								<?php
							}
							else
							{
								$light = (TBGContext::getUser()->hasPermission("b2no${pagename}access", 0, "core", 0, $gid, $tid, $all) == false) ? "mediumgreen" : "red";
								if (($gid != 0) || ($tid != 0) || ($theuid != 0))
								{
									$blue = TBGContext::getAllPermissions("b2no${pagename}access", $theuid, $tid, $gid, 0);
									$light = (count($blue) == 0) ? "lightblue" : $light;
								}
								?>
								<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('led_' . $light . '.png'); ?></td>
								<?php
							}
							?>
						</tr>
						<?php
					}
				?>
				</table>
				<div style="padding-top: 10px; border-bottom: 1px solid #DDD;"><b>Configuration</b></div>
				<table style="width: 100%; margin-top: 3px; table-layout: fixed;" cellpadding=0 cellspacing=0>
					<tr<?php if (TBGContext::getRequest()->getParameter('config') == 1) { print " class=\"p_sel\""; } ?>>
						<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('tab_config.png'); ?></td>
						<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><a href="<?php print $thelink . "&amp;config=1"; ?>">Access to the configuration page</a></td>
						<?php
						if ($access_level == "full")
						{
							if (TBGContext::getRequest()->getParameter('setconfigaccess') && is_numeric(TBGContext::getRequest()->getParameter('allowed')) && TBGContext::getRequest()->getParameter('config_no') == 0)
							{
								//if (TBGContext::getRequest()->getParameter('allowed') == 0 || TBGContext::getUser()->hasPermission("b2viewconfig", 0, "core", $theuid, $gid, $tid) || TBGContext::getUser()->hasPermission("b2saveconfig", 0, "core", $theuid, $gid, $tid))
								//{
								//	echo 'fds';
									TBGContext::setPermission("b2viewconfig", 0, "core", $theuid, $gid, $tid, TBGContext::getRequest()->getParameter('allowed'), TBGContext::getRequest()->getParameter('insertdeny'));
								//}
							}
							$light = (TBGContext::getUser()->hasPermission("b2viewconfig", 0, "core", $theuid, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
							if (($gid != 0) || ($tid != 0) || ($theuid != 0))
							{
								$blue = TBGContext::getAllPermissions("b2viewconfig", $theuid, $tid, $gid, 0);
								$light = (count($blue) == 0) ? "lightblue" : $light;
								$lightaccess = ($light == "lightblue") ? 1 : 0;
								$insertdeny = ($light == "mediumgreen") ? 1 : 0;
							}
							else
							{
								$lightaccess = ($light == "red") ? 1 : 0;
								$insertdeny = ($light == "mediumgreen") ? 1 : 0;
							}

							$canlink = false;
							if ($lightaccess == 0 || TBGContext::getUser()->hasPermission("b2viewconfig", 0, "core", TBGContext::getUser()->getUID()) == true)
							{
								$canlink = true;
							}

							if ($canlink == true)
							{
								?>
								<td style="width: 20px; padding: 1px;" align="left" valign="middle"><a class="image" href="<?php print $thelink . "&amp;setconfigaccess=true&amp;config_no=0&amp;allowed=" . $lightaccess . "&amp;insertdeny=" . $insertdeny; ?>"><?php echo image_tag('led_' . $light . '.png'); ?></a></td>
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
							$light = (TBGContext::getUser()->hasPermission("b2viewconfig", 0, "core", 0, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
							if (($gid != 0) || ($tid != 0) || ($theuid != 0))
							{
								$blue = TBGContext::getAllPermissions("b2viewconfig", $theuid, $tid, $gid, 0);
								$light = (count($blue) == 0) ? "lightblue" : $light;
							}
							?>
							<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('led_' . $light . '.png'); ?></td>
							<?php
						}
						?>
					</tr>
				</table>
			</td>
			<td style="width: 50%; padding: 5px;" align="left" valign="top">
				<?php
				if (TBGContext::getRequest()->getParameter('general_perm') == 1)
				{
					$all_permissions = TBGContext::getAvailablePermissions('general');
					?>
					<div style="border-bottom: 1px solid #DDD;"><b>General permissions</b></div>
					<table style="width: 100%; margin-top: 3px; table-layout: fixed;" cellpadding=0 cellspacing=0>
					<?php

						$perm_link = $thelink . "&amp;general_perm=1";

						foreach ($all_permissions as $aPerm)
						{
							?>
							<tr>
								<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_moduleperm.png'); ?></td>
								<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php print $aPerm['description'] ?></td>
								<?php
									require "permissions_general.inc.php";
								?>
							</tr>
							<?php
						}
						if (count($all_permissions) == 0)
						{
							?>
							<tr><td colspan=2 style="color: #C5C5C5;">(There are no general permissions)</td></tr>
							<?php
						}

					?>
					</table>
					<?php
				}
				elseif (TBGContext::getRequest()->getParameter('user_perm') == 1)
				{
					$all_permissions = TBGContext::getAvailablePermissions('user');
					?>
					<div style="border-bottom: 1px solid #DDD;"><b>User-specific permissions</b></div>
					<table style="width: 100%; margin-top: 3px; table-layout: fixed;" cellpadding=0 cellspacing=0>
					<?php

						$perm_link = $thelink . "&amp;user_perm=1";

						foreach ($all_permissions as $aPerm)
						{
							?>
							<tr>
								<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_moduleperm.png'); ?></td>
								<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php print $aPerm['description'] ?></td>
								<?php
									require "permissions_general.inc.php";
								?>
							</tr>
							<?php
						}
						if (count($all_permissions) == 0)
						{
							?>
							<tr><td colspan=2 style="color: #C5C5C5;">(There are no user specific permissions)</td></tr>
							<?php
						}

					?>
					</table>
					<?php
				}
				elseif (is_numeric(TBGContext::getRequest()->getParameter('p_id')))
				{
					$theProject = new TBGProject(TBGContext::getRequest()->getParameter('p_id'));
					$all_permissions = TBGContext::getAvailablePermissions('projects', $theProject->getID());
					//echo $theProject->getID();

					?>
					<div style="border-bottom: 1px solid #DDD;"><b>Project-specific permission settings</b></div>
					<table style="width: 100%; margin-top: 3px; table-layout: fixed;" cellpadding=0 cellspacing=0>
					<?php

						$perm_link = $thelink . "&amp;p_id=" . TBGContext::getRequest()->getParameter('p_id');
						$target_type = 1;

						foreach ($all_permissions as &$aPerm)
						{
							if ($aPerm['levels'] == 4)
							{
								$aPerm['target'] = '1:' . $aPerm['target'];
								$aPerm['levels'] = 2;
							}
							?>
							<tr>
								<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_moduleperm.png'); ?></td>
								<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php print $aPerm['description']; ?></td>
								<?php
									require "permissions_general.inc.php";
								?>
							</tr>
							<?php
						}

					?>
					</table>
					<div style="margin-top: 10px; border-bottom: 1px solid #DDD;"><b>Milestones</b></div>
					<table style="width: 100%; margin-top: 3px; table-layout: fixed;" cellpadding=0 cellspacing=0>
					<?php

						foreach ($theProject->getMilestones() as $aMilestone)
						{
							$aMilestone = new TBGMilestone($aMilestone['id']);
							?>
							<tr>
								<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('icon_milestones.png'); ?></td>
								<td style="width: auto; padding: 1px; font-weight: bold;" align="left" valign="middle"><?php print $aMilestone->getName(); ?></td>
								<?php
								if ($access_level == "full")
								{
									if (TBGContext::getRequest()->getParameter('setmilestoneaccess') && TBGContext::getRequest()->getParameter('milestoneid') == $aMilestone->getID() && is_numeric(TBGContext::getRequest()->getParameter('allowed')))
									{
										TBGContext::setPermission("b2milestoneaccess", $aMilestone->getID(), "core", $theuid, $gid, $tid, TBGContext::getRequest()->getParameter('allowed'), TBGContext::getRequest()->getParameter('insertdeny'));
									}
									$light = (TBGContext::getUser()->hasPermission("b2milestoneaccess", $aMilestone->getID(), "core", $theuid, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
									if (($gid != 0) || ($tid != 0) || ($theuid != 0))
									{
										$blue = TBGContext::getAllPermissions("b2milestoneaccess", $theuid, $tid, $gid, $aMilestone->getID());
										$light = (count($blue) == 0) ? "lightblue" : $light;
										$lightaccess = ($light == "lightblue") ? 1 : 0;
										$insertdeny = ($light == "mediumgreen") ? 1 : 0;
									}
									else
									{
										$lightaccess = ($light == "red") ? 1 : 0;
										$insertdeny = ($light == "mediumgreen") ? 1 : 0;
									}
									?>
									<td style="width: 20px; padding: 1px;" align="left" valign="middle"><a class="image" href="<?php print $thelink . "&amp;p_id=" . TBGContext::getRequest()->getParameter('p_id') . "&amp;setmilestoneaccess=true&amp;allowed=" . $lightaccess . "&amp;milestoneid=" . $aMilestone->getID() . "&amp;insertdeny=" . $insertdeny; ?>"><?php echo image_tag('led_' . $light . '.png'); ?></a></td>
									<?php
								}
								else
								{
									$light = (TBGContext::getUser()->hasPermission("b2milestoneaccess", $aMilestone->getID(), "core", 0, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
									if (($gid != 0) || ($tid != 0) || ($theuid != 0))
									{
										$blue = TBGContext::getAllPermissions("b2milestoneaccess", $theuid, $tid, $gid, $aMilestone->getID());
										$light = (count($blue) == 0) ? "lightblue" : $light;
									}
									?>
									<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('led_' . $light . '.png'); ?></td>
									<?php
								}
								?>
							</tr>
							<?php
						}
						if (count($theProject->getMilestones()) == 0)
						{
							?>
							<tr><td colspan=2 style="color: #C5C5C5;">(This project has no milestones defined)</td></tr>
							<?php
						}
					?>
					</table>
					<div style="margin-top: 10px; border-bottom: 1px solid #DDD;"><b>Editions and builds</b></div>
					<table style="width: 100%; margin-top: 3px; table-layout: fixed;" cellpadding=0 cellspacing=0>
					<?php

					foreach ($theProject->getEditions() as $anEdition)
					{
						?>
						<tr>
							<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('icon_edition.png'); ?></td>
							<td style="width: auto; padding: 1px; font-weight: bold;" align="left" valign="middle"><?php print $anEdition->getName(); ?></td>
							<?php
							if ($access_level == "full")
							{
								if (TBGContext::getRequest()->getParameter('seteditionaccess') && TBGContext::getRequest()->getParameter('e_id') == $anEdition->getID() && is_numeric(TBGContext::getRequest()->getParameter('allowed')))
								{
									TBGContext::setPermission("b2editionaccess", $anEdition->getID(), "core", $theuid, $gid, $tid, TBGContext::getRequest()->getParameter('allowed'), TBGContext::getRequest()->getParameter('insertdeny'));
								}
								$light = (TBGContext::getUser()->hasPermission("b2editionaccess", $anEdition->getID(), "core", $theuid, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
								if (($gid != 0) || ($tid != 0) || ($theuid != 0))
								{
									$blue = TBGContext::getAllPermissions("b2editionaccess", $theuid, $tid, $gid, $anEdition->getID());
									$light = (count($blue) == 0) ? "lightblue" : $light;
									$lightaccess = ($light == "lightblue") ? 1 : 0;
									$insertdeny = ($light == "mediumgreen") ? 1 : 0;
								}
								else
								{
									$lightaccess = ($light == "red") ? 1 : 0;
									$insertdeny = ($light == "mediumgreen") ? 1 : 0;
								}
								?>
								<td style="width: 20px; padding: 1px;" align="left" valign="middle"><a class="image" href="<?php print $thelink . "&amp;p_id=" . TBGContext::getRequest()->getParameter('p_id') . "&amp;seteditionaccess=true&amp;allowed=" . $lightaccess . "&amp;e_id=" . $anEdition->getID() . "&amp;insertdeny=" . $insertdeny; ?>"><?php echo image_tag('led_' . $light . '.png'); ?></a></td>
								<?php
							}
							else
							{
								$light = (TBGContext::getUser()->hasPermission("b2editionaccess", $anEdition->getID(), "core", 0, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
								if (($gid != 0) || ($tid != 0) || ($theuid != 0))
								{
									$blue = TBGContext::getAllPermissions("b2editionaccess", $theuid, $tid, $gid, $anEdition->getID());
									$light = (count($blue) == 0) ? "lightblue" : $light;
								}
								?>
								<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('led_' . $light . '.png'); ?></td>
								<?php
							}
							?>
						</tr>
						<?php
						//$builds = bugs_getBuilds($anEdition->getID());

						foreach ($anEdition->getBuilds() as $aBuild)
						{
							?>
							<tr>
								<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('icon_build.png'); ?></td>
								<td style="width: auto; padding: 1px;" align="left" valign="middle"><?php print $aBuild; ?></td>
								<?php
								if ($access_level == "full")
								{
									if (TBGContext::getRequest()->getParameter('setbuildaccess') && TBGContext::getRequest()->getParameter('b_id') == $aBuild->getID() && is_numeric(TBGContext::getRequest()->getParameter('allowed')))
									{
										TBGContext::setPermission("b2buildaccess", $aBuild->getID(), "core", $theuid, $gid, $tid, TBGContext::getRequest()->getParameter('allowed'), TBGContext::getRequest()->getParameter('insertdeny'));
									}
									$light = (TBGContext::getUser()->hasPermission("b2buildaccess", $aBuild->getID(), "core", $theuid, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
									if (($gid != 0) || ($tid != 0) || ($theuid != 0))
									{
										$blue = TBGContext::getAllPermissions("b2buildaccess", $theuid, $tid, $gid, $aBuild->getID());
										$light = (count($blue) == 0) ? "lightblue" : $light;
										$lightaccess = ($light == "lightblue") ? 1 : 0;
										$insertdeny = ($light == "mediumgreen") ? 1 : 0;
									}
									else
									{
										$lightaccess = ($light == "red") ? 1 : 0;
										$insertdeny = ($light == "mediumgreen") ? 1 : 0;
									}
									?>
									<td style="width: 20px; padding: 1px;" align="left" valign="middle"><a class="image" href="<?php print $thelink . "&amp;p_id=" . TBGContext::getRequest()->getParameter('p_id') . "&amp;setbuildaccess=true&amp;allowed=" . $lightaccess . "&amp;b_id=" . $aBuild->getID() . "&amp;insertdeny=" . $insertdeny; ?>"><?php echo image_tag('led_' . $light . '.png'); ?></a></td>
									<?php
								}
								else
								{
									$light = (TBGContext::getUser()->hasPermission("b2buildaccess", $aBuild->getID(), "core", 0, $gid, $tid, $all) == true) ? "mediumgreen" : "red";
									if (($gid != 0) || ($tid != 0) || ($theuid != 0))
									{
										$blue = TBGContext::getAllPermissions("b2buildaccess", $theuid, $tid, $gid, $aBuild->getID());
										$light = (count($blue) == 0) ? "lightblue" : $light;
									}
									?>
									<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('led_' . $light . '.png'); ?></td>
									<?php
								}
								?>
							</tr>
							<?php
						}
						if (count($anEdition->getBuilds()) == 0)
						{
							?>
							<tr><td>&nbsp;</td><td colspan=2 style="color: #C5C5C5;">(This edition has no builds)</td></tr>
							<?php
						}
					}
					if (count($theProject->getEditions()) == 0)
					{
						?>
						<tr><td style="color: #C5C5C5;">(This project has no editions)</td></tr>
						<?php
					}
					?>
					</table>
					<?php
				}
				elseif (is_numeric(TBGContext::getRequest()->getParameter('issuespermrest')))
				{
					?>
					<div style="border-bottom: 1px solid #DDD;"><b>Issue-specific access privileges</b></div>
					<table style="width: 100%; margin-top: 3px; table-layout: fixed;" cellpadding=0 cellspacing=0>
					<?php

						$restrictions = TBGContext::getAllPermissions("b2notviewissue", $theuid, $tid, $gid);
						$restrictions2 = TBGContext::getAllPermissions("b2viewissue", $theuid, $tid, $gid);
						foreach ($restrictions2 as $aRest2)
						{
							$restrictions[] = $aRest2;
						}

						if (count($restrictions) >= 1)
						{
							foreach ($restrictions as $aRest)
							{
								//$prefix = bugs_getIssuePrefix($aRest['target_id']);
								$restIssue = TBGFactory::TBGIssueLab($aRest['target_id']);
								if ($aRest['p_type'] == "b2notviewissue")
								{
									?>
									<tr>
										<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('icon_issuesrest.png'); ?></td>
										<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle">Can not access issue <?php print $restIssue->getFormattedIssueNo(); ?></td>
										<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('led_red.png'); ?></td>
									</tr>
									<?php
								}
								else
								{
									?>
									<tr>
										<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('icon_issuesperm.png'); ?></td>
										<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle">Can access issue <?php print $restIssue->getFormattedIssueNo(); ?></td>
										<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('led_mediumgreen.png'); ?></td>
									</tr>
									<?php
								}
							}
						}
						else
						{
							?>
							<tr><td style="color: #C5C5C5;">(No specific privileges for this group/team/user)</td></tr>
							<?php
						}

					?>
					</table>
					<?php
				}
				elseif (TBGContext::getRequest()->getParameter('module_perm') && !is_numeric(TBGContext::getRequest()->getParameter('module_perm')))
				{
					$theModule = TBGContext::getModule(TBGContext::getRequest()->getParameter('module_perm'));
					?>
					<div style="border-bottom: 1px solid #DDD;"><b>Module specific permissions</b></div>
					<table style="width: 100%; margin-top: 3px; table-layout: fixed;" cellpadding=0 cellspacing=0>
					<?php

						foreach ($theModule->getAvailablePermissions() as $aPerm)
						{
							?>
							<tr>
								<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_moduleperm.png'); ?></td>
								<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php print $aPerm['description'] ?></td>
								<?php
									require TBGContext::getIncludePath() . 'include/permissions_module_config.inc.php';
								?>
							</tr>
							<?php
						}

					?>
					</table>
					<?php
				}
				elseif (TBGContext::getRequest()->getParameter('config') == 1)
				{
					?>
					<div style="border-bottom: 1px solid #DDD;"><b>Configuration page access</b></div>
					<table style="width: 100%; margin-top: 3px; table-layout: fixed;" cellpadding=0 cellspacing=0>
					<tr>
						<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_general.png'); ?></td>
						<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php echo __('General settings'); ?></td>
						<?php
							$configno = 12;
							require "permissions_config_access.inc.php";
						?>
					</tr>
					<tr>
						<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_server.png'); ?></td>
						<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php echo __('Server settings'); ?></td>
						<?php
							$configno = 11;
							require "permissions_config_access.inc.php";
						?>
					</tr>
					<tr>
						<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_scopes.png'); ?></td>
						<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php echo __('Scopes'); ?></td>
						<?php
							$configno = 14;
							require "permissions_config_access.inc.php";
						?>
					</tr>
					<tr>
						<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_files.png'); ?></td>
						<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php echo __('File upload settings'); ?></td>
						<?php
							$configno = 3;
							require "permissions_config_access.inc.php";
						?>
					</tr>
					
					<tr>
						<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_import.png'); ?></td>
						<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php echo __('Import data from version 1.9'); ?></td>
						<?php
							$configno = 16;
							require "permissions_config_access.inc.php";
						?>
					</tr>
					<tr>
						<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_projects.png'); ?></td>
						<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php echo __('Manage projects'); ?></td>
						<?php
							$configno = 10;
							require "permissions_config_access.inc.php";
						?>
					</tr>
					<tr>
						<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_builds.png'); ?></td>
						<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php echo __('Manage milestones'); ?></td>
						<?php
							$configno = 9;
							require "permissions_config_access.inc.php";
						?>
					</tr>
					<tr>
						<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_issuetypes.png'); ?></td>
						<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php echo __('Manage data types'); ?></td>
						<?php
							$configno = 4;
							require "permissions_config_access.inc.php";
						?>
					</tr>
					<tr>
						<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_users.png'); ?></td>
						<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php echo __('Manage users'); ?></td>
						<?php
							$configno = 2;
							require "permissions_config_access.inc.php";
						?>
					</tr>
					<tr>
						<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_teamgroups.png'); ?></td>
						<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php echo __('Manage teams &amp; groups'); ?></td>
						<?php
							$configno = 1;
							require "permissions_config_access.inc.php";
						?>
					</tr>
					<tr>
						<td style="width: 20px; padding: 1px;" align="left" valign="middle"><?php echo image_tag('cfg_icon_modules.png'); ?></td>
						<td style="width: auto; padding: 1px; font-weight: normal;" align="left" valign="middle"><?php echo __('Manage modules'); ?></td>
						<?php
							$configno = 15;
							require "permissions_config_access.inc.php";
						?>
					</tr>
					</table>
					<?php
				}
				else
				{
					?>
					<div style="font-weight: bold; border-bottom: 1px solid #DDD; color: #BBB;">Select an item in the list to the left</div>
					<?php
				}
				?>
			</td>
	</table>
	</div>