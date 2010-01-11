<?php

	if (($access_level != "full" && $access_level != "read") || TBGContext::getRequest()->getParameter('access_level'))
	{
		tbg__msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		require_once TBGContext::getIncludePath() . 'include/config/scopes_logic.inc.php';
		?>
		<table style="width: 100%" cellpadding=0 cellspacing=0>
			<tr>
			<td style="padding-right: 10px;">
				<table class="configstrip" cellpadding=0 cellspacing=0>
					<tr>
						<td class="cleft"><b><?php echo __('Configure available scopes'); ?></b></td>
						<td class="cright">&nbsp;</td>
					</tr>
					<tr>
						<td colspan=2 class="cdesc">
						<?php echo __('Scopes are individual BUGS 2 environments, fully functional, but invisible to eachother.'); echo __('You can learn more about scopes in the %tbg__online_help%.', array('%tbg__online_help%' => tbg__helpBrowserHelper('scopes', __('The Bug Genie online help')))); ?><br><?php echo __('Click on a scope to view its details and/or change its settings.'); ?><br>
						<br>
						<a href="config.php?module=core&amp;section=14&amp;createnewscope=true"><b><?php echo __('Click here to create a new scope'); ?></b></a><br>
						</td>
					</tr>
				</table>
			</td>
			</tr>
			<tr>
				<td style="padding-right: 10px;">
					<table cellpadding=0 cellspacing=0 style="width: 100%;">
						<tr>
							<td style="width: 300px; padding: 5px;" valign="top">
								<div style="background-color: #F2F2F2; padding: 3px; border-bottom: 1px solid #DDD; margin-bottom: 5px;"><b><?php echo __('DEFAULT SCOPE'); ?></b></div>
								<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" name="defaultscopeform">
								<input type="hidden" name="module" value="core">
								<input type="hidden" name="section" value="14">
								<input type="hidden" name="setdefaultscope" value="true">
									<table cellpadding=0 cellspacing=0 width="100%">
										<tr>
										<td style="width: auto;"><select style="width: 240px;" name="defaultscope">
										<?php
	
										foreach (TBGScope::getAll() as $aScope)
										{
											?>
											<option value="<?php print $aScope->getID(); ?>"<?php if (TBGSettings::getDefaultScope()->getID() == $aScope->getID()) { print " selected"; } ?>><?php print $aScope->getName(); ?></option>
											<?php
										}
	
										?>
										</select></td>
										<td style="width: 50px; text-align: right;"><input type="submit" value="<?php echo __('Save'); ?>"></td>
										</tr>
									</table>
								</form>
								<div style="background-color: #F2F2F2; padding: 3px; border-bottom: 1px solid #DDD; margin-top: 10px; margin-bottom: 5px;"><b><?php echo __('AVAILABLE SCOPES'); ?></b></div>
								<?php
	
								if ($isAdded == true)
								{
									?>
									<div style="padding-bottom: 5px; font-weight: bold; color: #00A400;"><?php echo __('The scope was added.'); ?></div>
									<?php
								}

								foreach (TBGScope::getAll() as $aScope)
								{
									?>
									<div style="border:<?php ($theScope instanceof TBGScope && $theScope->getID() == $aScope->getID()) ? print "2px solid #9D9" : print "1px solid #DDD"; ?>; padding: 3px; width: auto;">
										<a href="config.php?module=core&amp;section=14&amp;selectedscope=<?php print $aScope->getID(); ?>"><b><?php print $aScope->getName(); ?></b></a><?php if ($aScope->isEnabled() == 0) echo __('%scope_name% (disabled)', array('%scope_name%' => '')); ?><br>
										<div style="padding: 3px; padding-left: 0px;">
											<b><?php echo __('URL:'); ?></b><br><a href="<?php echo TBGSettings::get('url_host') . TBGContext::getTBGPath(); ?>?scope=<?php echo $aScope->getShortname(); ?>"><?php echo TBGSettings::get('url_host') . TBGContext::getTBGPath();?>?scope=<?php echo $aScope->getShortname(); ?></a>
											<?php if ($aScope->getHostname() != ''): ?>
												<?php echo __('%url_1% and %url_2%', array('%url_1%' => '', '%url_2%' => '<a href="' . $aScope->getHostname() . '">' . $aScope->getHostname() . '</a>')); ?>
											<?php endif; ?>
										</div>
										<div style="padding: 3px; padding-left: 0px;"><?php print $aScope->getDescription(); ?></div>
										<div style="border-top: 1px solid #DDD; background-color: #F5F5F5; padding: 3px;">
											<table cellpadding=0 cellspacing=0 width="100%">
												<tr>
													<td style="width: 100px;"><b><?php echo __('Administered by:'); ?></b></td>
													<td>
														<table cellpadding=0 cellspacing=0 width="100%">
														<?php print tbg__userDropdown($aScope->getScopeAdmin()); ?>
														</table>
													</td>
												</tr>
											</table>
										</div>
									</div>
									<br>
									<?php
								}
	
								?>
							</td>
							<td style="width: auto; padding: 5px; vertical-align: top;">
							<div style="width: 100%; padding: 3px; background-color: #F2F2F2; border-bottom: 1px solid #DDD; margin-bottom: 5px;"><b><?php echo __('SCOPE SETTINGS'); ?></b></div>
							<?php

							if ($theScope instanceof TBGScope || $newScope)
							{
								if ($access_level == "full")
								{
									switch ($theErr)
									{
										case "scopeadmin":
											?>
											<div style="padding-bottom: 5px; font-weight: bold; color: #D55;"><?php echo __('No unique user could be found based on the details in the Scope administrator field. Please try a different name.'); ?></div>
											<?php
											break;
										case "shortname":
											?>
											<div style="padding-bottom: 5px; font-weight: bold; color: #D55;"><?php echo __('The shortname cannot be blank, as it must be possible to select by entering it in an URL.'); ?></div>
											<?php
											break;
										case "dualshortname":
											?>
											<div style="padding-bottom: 5px; font-weight: bold; color: #D55;"><?php echo __('The shortname must be unique, as it must be possible to uniquely select it by entering it in an URL.'); ?></div>
											<?php
											break;
										case "scopename":
											?>
											<div style="padding-bottom: 5px; font-weight: bold; color: #D55;"><?php echo __('The scope name cannot be blank, as it must be possible to select in the configuration section.'); ?></div>
											<?php
											break;
										default:
											?>
											<div style="padding-bottom: 5px; font-weight: bold; color: #D55;"><?php echo __($theErr); ?></div>
											<?php
									}
									if ($isUpdated == true)
									{
										?>
										<div style="padding-bottom: 5px; font-weight: bold; color: #00A400;"><?php echo __('The new settings were saved.'); ?></div>
										<?php
									}
									if ($newScope == true)
									{
										echo __('Enter the scope settings below, and press the "Create scope" button to save it.');
									}
									else
									{
										echo __('Change any setting below, and press the "Save settings" button to save.');
									}
									
									?>
									<div style="padding: 5px;">
									<?php

									if (!$newScope)
									{
										?>
										<table cellpadding=0 cellspacing=0 style="width: 100%; table-layout: fixed;">
											<tr>
												<td style="padding-top: 5px; width: 150px;" valign="top"><b><?php echo __('Scope administrator'); ?></b></td>
												<td style="padding: 2px; width: auto;">
												<table style="width: 200px;" cellpadding=0 cellspacing=0>
													<tr>
														<td>
															<table style="width: 200px;" cellpadding=0 cellspacing=0 id="scope_admin">
															<?php
															
															if ($theScope->getScopeAdmin() instanceof TBGUser)
															{
																echo tbg__userDropdown($theScope->getScopeAdmin());
															}
															else
															{
																echo '<tr><td style="color: #AAA;">' . __('No administrator set') . '</td></tr>';
															}
																
															?>
															</table>
														</td>
														<td style="width: 20px;"><a href="javascript:void(0);" class="image" onclick="Effect.Appear('edit_scopeadmin', { duration: 0.5 })"><?php echo image_tag('icon_switchassignee.png', '', __('Change'), __('Change'), 0, 12, 12); ?></a></td>
														<td style="padding: 2px;" colspan=2>&nbsp;</td>
													</tr>
												</table>
												<span id="edit_scopeadmin" style="display: none;">
												<?php tbg__AJAXuserteamselector(__('Set scope administrator'), 
																				'',
																				'config.php?module=core&section=14&selectedscope=' . $theScope->getID() . '&setscopeadmin=true', 
																				'',
																				'scope_admin', 
																				'config.php?module=core&section=14&selectedscope=' . $theScope->getID() . '&getscopeadmin=true',
																				'', 
																				'',
																				'edit_scopeadmin',
																				'', 
																				'', 
																				'', 
																				'', 
																				true
																				); ?>
												</span>
												</td>
											</tr>
											<tr>
												<td colspan=2><?php echo 	__('By changing this setting, the selected user will be moved into the new scope, and into the "Administrator" group in the new scope.') . 
																			__('This user will then no longer be able to log into the current scope, but will have admin priviliges in the selected scope.'); ?></td>
											</tr>
										</table>
										<?php
									}

									?>
									<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" name="scopeform" id="scopeform">
									<table cellpadding=0 cellspacing=0 style="width: 100%; table-layout: fixed;">
										<tr>
											<td style="padding-top: 5px; width: 150px;"><b><?php echo __('Scope name'); ?></b></td>
											<td style="padding-top: 5px;">
											<?php 
											
											if ($newScope)
											{
												?><input <?php if ($theErr == "scopename") print "style=\"background-color: #FDD;\" "; ?>type="text" name="scopename" value=""><?php
											}
											else
											{
												?><input type="text" name="scopename" value="<?php print $theScope->getName(); ?>"><?php
											}

											?>
											</td>
										</tr>
										<tr>
											<td style="padding-top: 5px;" colspan=2><?php echo __('The name of the scope, displayed in the left hand list'); ?></td>
										</tr>
										<tr>
											<td style="padding-top: 5px;"><b><?php echo __('Scope short name'); ?></b></td>
											<td style="padding-top: 5px;">
											<?php 
											
											if ($newScope)
											{
												?><input <?php if ($theErr == "shortname") print "style=\"background-color: #FDD;\" "; ?>type="text" name="shortname" value=""><?php
											}
											else
											{
												?><input type="text" name="shortname" value="<?php print $theScope->getShortname(); ?>"><?php
											}

											?>
											</td>
										</tr>
										<tr>
											<td style="padding-top: 5px;" colspan=2><?php echo __('The scope shortname, used to enter the scope via URL'); ?></td>
										</tr>
										<tr>
											<td style="padding-top: 5px;"><b><?php echo __('Scope hostname'); ?></b></td>
											<td style="padding-top: 5px;">
											<?php 
											
											if ($newScope)
											{
												?><input type="text" name="hostname" value=""><?php
											}
											else
											{
												?><input type="text" name="hostname" value="<?php print $theScope->getHostname(); ?>"><?php
											}

											?>
											</td>
										</tr>
										<tr>
											<td style="padding-top: 5px;" colspan=2><?php echo __('By filling out this setting, the scope will automatically be triggered when accessing BUGS through this hostname.'); ?><br>
											<i><?php echo __('Remember that the webserver must be set up to respond to the selected hostname for this to work.'); ?></i></td>
										</tr>
										<tr>
											<td style="padding-top: 5px;"><b><?php echo __('Enabled'); ?></b></td>
											<td style="padding-top: 5px;"><select name="enabled">
											<?php 
											
											if ($newScope)
											{
												?>
												<option value="1">Yes</option>
												<option value="0">No</option>
												<?php
											}
											else
											{
												?>
												<option value="1"<?php if ($theScope->isEnabled() == 1) { print " selected"; } ?>><?php echo __('Yes'); ?></option>
												<option value="0"<?php if ($theScope->isEnabled() == 0) { print " selected"; } ?>><?php echo __('No'); ?></option>
												<?php
											}

											?>
											</select></td>
										</tr>
										<tr>
											<td style="padding-top: 5px;" colspan=2><?php echo __('Whether or not the scope is enabled. A disabled scope is inaccessible, and it\'s users cannot log in.'); ?></td>
										</tr>
										<tr>
											<td style="padding-top: 5px;" valign="top"><b><?php echo __('Scope description'); ?></b></td>
											<td style="padding-top: 5px;">
											<?php 
											
											if ($newScope)
											{
												?><input type="text" name="description" id="description" value="" ><?php
											}
											else
											{
												?><input type="text" name="description" id="description" value="<?php echo $theScope->getDescription(); ?>" ><?php
											}

											?>
											</td>
										</tr>
										<tr>
											<td style="padding-top: 5px;" colspan=2><?php echo __('A short description of the scope, displayed in the left hand list.'); ?></td>
										</tr>
										<tr>
											<td style="padding-top: 10px; text-align: right;" colspan=2>
											<input type="hidden" name="module" value="core">
											<input type="hidden" name="section" value="14">
											<?php

											if ($newScope)
											{
												?>
												<input type="hidden" name="createscope" value="true">
												<input type="submit" value="<?php echo __('Create scope'); ?>" style="font-weight: bold;">
												<?php
											}
											else
											{
												?>
												<input type="hidden" name="savescopesettings" value="true">
												<input type="hidden" name="selectedscope" value="<?php print $theScope->getID(); ?>">
												<input type="submit" value="<?php echo __('Save settings'); ?>" style="font-weight: bold;">
												<?php
											}
											
											?>
											</td>
										</tr>
									</table>
									</form>
									</div>
									<?php 
									
									if ($theScope instanceof TBGScope && $theScope->getID() != TBGSettings::getDefaultScope()->getID() && !$newScope) 
									{
										?>
										<div style="width: 100%; padding: 3px; background-color: #F2F2F2; border-bottom: 1px solid #DDD; margin-top: 10px; margin-bottom: 5px;"><b><?php echo __('DELETE SCOPE'); ?></b></div>
										<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" name="deletescopeform">
										<?php echo __('Click the "Delete scope" button below to delete the scope and all its contents. If you just want to disable the scope, use the above selector instead.'); ?><br>
										<br><b><?php echo __('Warning:'); ?></b> <i><?php echo __('Deleting the scope is irreversible!'); ?></i>
										<input type="hidden" name="module" value="core">
										<input type="hidden" name="section" value="14">
										<input type="hidden" name="deletescope" value="true">
										<input type="hidden" name="selectedscope" value="<?php print $theScope->getID(); ?>">
										<div style="width: auto; padding: 5px; text-align: right;">
										<input type="submit" value="Delete scope" style="font-weight: bold;">
										</div>
										</form>
										<?php
									}
								}
								else
								{
									?>
									<?php echo __('Below are the settings for the selected scope.'); ?>
									<div style="padding: 5px;">
									<table cellpadding=0 cellspacing=0 width="100%">
										<tr>
											<td style="padding-top: 5px; width: 175px;"><b><?php echo __('Scope name'); ?></b></td>
											<td style="padding-top: 5px;"><?php print $theScope->getName(); ?></td>
										</tr>
										<tr>
											<td style="padding-top: 5px;"><b><?php echo __('Scope short name'); ?></b></td>
											<td style="padding-top: 5px;"><?php print $theScope->getShortname(); ?></td>
										</tr>
										<tr>
											<td style="padding-top: 5px;"><b><?php echo __('Enabled'); ?></b></td>
											<td style="padding-top: 5px;"><?php print ($theScope->isEnabled()) ? __('Yes') : __('No'); ?></td>
										</tr>
										<tr>
											<td style="padding-top: 5px;" valign="top"><b><?php echo __('Scope description'); ?></b></td>
											<td style="padding-top: 5px;"><?php print nl2br($theScope->getDescription()); ?></td>
										</tr>
										<tr>
											<td style="padding-top: 5px; padding-bottom: 5px;" colspan=2><?php echo __('The scope administrator functions as a "normal" Administrator user, within the scope. That user can delegate any permission within the scope to other users in that scope.'); ?></td>
										</tr>
										<tr>
											<td style="padding-top: 5px;" valign="top"><b><?php echo __('Scope administrator'); ?></b></td>
											<td style="padding-top: 5px;"><table cellpadding=0 cellspacing=0 width="100%"><?php print tbg__userDropdown($theScope->getScopeAdmin()); ?></table></td>
										</tr>
									</table>
									</div>
									<?php
								}
							}
							else
							{
								if ($deleteTwice == true)
								{
									?>
									<div style="padding: 5px; color: #66A466;"><b><?php echo __('The scope was deleted.'); ?></b></div>
									<?php
								}
								else
								{
									?>
									<div style="padding: 5px; color: #CCC;"><?php echo __('Please select a scope from the list to view its settings'); ?></div>
									<?php
								}
							}

							?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php
	}
?>