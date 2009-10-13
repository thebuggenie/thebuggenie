<?php

	$themes = BUGScontext::getThemes();
	$languages = BUGSi18n::getLanguages();
	
?>
<table style="clear: both; width: 700px; margin-top: 5px;" class="padded_table" cellpadding=0 cellspacing=0>
	<tr>
		<td style="width: 200px;"><label for="user_themes"><?php echo __('Individual themes'); ?></label></td>
		<td style="width: auto;">
			<select name="user_themes" id="user_themes" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (BUGSsettings::isUserThemesEnabled()): ?> selected<?php endif; ?>><?php echo __('Yes, users can choose their preferred theme'); ?></option>
				<option value=0<?php if (!BUGSsettings::isUserThemesEnabled()): ?> selected<?php endif; ?>><?php echo __('No, this theme will always be used'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('Choose whether users can select a different theme than the default theme for their own account.'); ?></td>
	</tr>
	<tr>
		<td><label for="requirelogin"><?php echo __('Anonymous access'); ?></label></td>
		<td>
			<select name="requirelogin" id="requirelogin" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (BUGSsettings::isLoginRequired()): ?> selected<?php endif; ?>><?php echo __('You need a valid user account to access any content'); ?></option>
				<option value=0<?php if (!BUGSsettings::isLoginRequired()): ?> selected<?php endif; ?>><?php echo __('Use the guest user account'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="defaultisguest"><?php echo __('Guest user is authenticated'); ?></label></td>
		<td>
			<select name="defaultisguest" id="defaultisguest" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (BUGSsettings::isDefaultUserGuest()): ?> selected<?php endif; ?>><?php echo __('No, the default user is a guest account'); ?></option>
				<option value=0<?php if (!BUGSsettings::isDefaultUserGuest()): ?> selected<?php endif; ?>><?php echo __('Yes, the default user is a normal account'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('Select if the default user is a guest user or a normal user'); ?></td>
	</tr>
	<tr>
		<td><label for="allowreg"><?php echo __('New user accounts'); ?></label></td>
		<td>
			<select name="allowreg" id="allowreg" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (BUGSsettings::isRegistrationEnabled()): ?> selected<?php endif; ?>><?php echo __('Users can register new accounts'); ?></option>
				<option value=0<?php if (!BUGSsettings::isRegistrationEnabled()): ?> selected<?php endif; ?>><?php echo __('All new user accounts will be created by an admin'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="limit_registration"><?php echo __('Registration domain whitelist'); ?></label></td>
		<td><input type="text" name="limit_registration" id="limit_registration"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?> value="<?php echo BUGSsettings::getRegistrationDomainWhitelist(); ?>" style="width: 300px;"></td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('Comma-separated list of allowed domains (ex: %example%). Leave empty to allow all domains.', array('%example%' => 'thebuggenie.com, zegeniestudios.net')); ?></td>
	</tr>
	<tr>
		<td><label for="defaultgroup"><?php echo __('Default user group'); ?></label></td>
		<td>
			<select name="defaultgroup" id="defaultgroup" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
			<?php foreach (BUGSgroup::getAll() as $aGroup): ?>
				<option value="<?php print $aGroup->getID(); ?>"<?php if (($default_group = BUGSsettings::getDefaultGroup()) instanceof BUGSgroup && $default_group->getID() == $aGroup->getID()): ?> selected<?php endif; ?>><?php print $aGroup->getName(); ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('New users will automatically be added to this group'); ?></td>
	</tr>
	<tr>
		<td><label for="returnfromlogin"><?php echo __('Redirect after login'); ?></label></td>
		<td>
			<select name="returnfromlogin" id="returnfromlogin" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<?php $return_routes = array('home' => __('Frontpage'), 'account' => __('Account details')); ?>
				<?php BUGScontext::trigger('core', 'setting_returnfromlogin', &$return_routes); ?>
				<?php foreach ($return_routes as $route => $description): ?> 
					<option value="<?php echo $route; ?>"<?php if (BUGSsettings::getLoginReturnRoute() == $route): ?> selected<?php endif; ?>><?php echo $description; ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="returnfromlogout"><?php echo __('Redirect after logout'); ?></label></td>
		<td>
			<select name="returnfromlogout" id="returnfromlogout" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<?php $return_routes = array('home' => __('Frontpage'), 'account' => __('Account details')); ?>
				<?php BUGScontext::trigger('core', 'setting_returnfromlogout', &$return_routes); ?>
				<?php foreach ($return_routes as $route => $description): ?> 
					<option value="<?php echo $route; ?>"<?php if (BUGSsettings::getLogoutReturnRoute() == $route): ?> selected<?php endif; ?>><?php echo $description; ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="onlinestate"><?php echo __('User state when online'); ?></label></td>
		<td>
			<select name="onlinestate" id="onlinestate" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
			<?php foreach (BUGSuserstate::getAll() as $aState): ?>
				<option value="<?php print $aState->getID(); ?>"<?php if (($onlinestate = BUGSsettings::getOnlineState()) instanceof BUGSdatatype && $onlinestate->getID() == $aState->getID()): ?> selected<?php endif; ?>><?php print $aState->getName(); ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="awaystate"><?php echo __('User state when inactive'); ?></label></td>
		<td>
			<select name="awaystate" id="awaystate" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
			<?php foreach (BUGSuserstate::getAll() as $aState): ?>
				<option value="<?php print $aState->getID(); ?>"<?php if (($awaystate = BUGSsettings::getAwayState()) instanceof BUGSdatatype && $awaystate->getID() == $aState->getID()): ?> selected<?php endif; ?>><?php print $aState->getName(); ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="offlinestate"><?php echo __('User state when offline'); ?></label></td>
		<td>
			<select name="offlinestate" id="offlinestate" style="width: 300px;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
			<?php foreach (BUGSuserstate::getAll() as $aState): ?>
				<option value="<?php print $aState->getID(); ?>"<?php if (($offlinestate = BUGSsettings::getOfflineState()) instanceof BUGSdatatype && $offlinestate->getID() == $aState->getID()): ?> selected<?php endif; ?>><?php print $aState->getName(); ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
</table>