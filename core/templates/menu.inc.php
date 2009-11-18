<div class="medium_transparent" style="z-index: 100001; margin: 0; position: fixed; top: 0; left: 0; width: 100%; padding: 0; background-color: #E84545; font-size: 14px; color: #000; border-bottom: 1px solid #555; display: none;" id="thebuggenie_failuremessage">
	<div style="padding: 10px 0 10px 0;">
		<span style="color: #000; font-weight: bold;" id="thebuggenie_failuremessage_title"></span><br>
		<span id="thebuggenie_failuremessage_content"></span>
	</div>
</div>
<div class="medium_transparent" style="z-index: 100000; margin: 0; position: fixed; top: 0; left: 0; width: 100%; padding: 0; background-color: #45E845; font-size: 14px; color: #000; border-bottom: 1px solid #555; display: none;" id="thebuggenie_successmessage">
	<div style="padding: 10px 0 10px 0;">
		<span style="color: #000; font-weight: bold;" id="thebuggenie_successmessage_title"></span><br>
		<span id="thebuggenie_successmessage_content"></span>
	</div>
</div>
<div class="tab_menu header_menu">
	<ul>
		<?php /*?><li<?php if ($bugs_response->getPage() == 'index'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('home'), image_tag('tab_index.png', array('style' => 'float: left;')).__('Frontpage')); ?></li> */ ?>
		<?php if (!BUGScontext::getUser()->isThisGuest()): ?>
			<li<?php if ($bugs_response->getPage() == 'dashboard'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('dashboard'), image_tag('icon_dashboard_small.png', array('style' => 'float: left;')).__('My dashboard')); ?></li>
		<?php endif; ?>
		<?php if (BUGScontext::getUser()->hasPermission("b2canreportissues")): ?>
			<?php if (BUGScontext::getCurrentProject() instanceof BUGSproject): ?>
				<li<?php if ($bugs_response->getPage() == 'reportissue'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('project_reportissue', array('project_key' => BUGScontext::getCurrentProject()->getKey())), image_tag('tab_reportissue.png', array('style' => 'float: left;')).((isset($_SESSION['rni_step1_set'])) ? __('Continue reporting') : __('Report an issue'))); ?></li>
			<?php else: ?>
				<li<?php if ($bugs_response->getPage() == 'reportissue'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('reportissue'), image_tag('tab_reportissue.png', array('style' => 'float: left;')).((isset($_SESSION['rni_step1_set'])) ? __('Continue reporting') : __('Report an issue'))); ?></li>
			<?php endif; ?>
		<?php endif; ?>
		<?php if (BUGScontext::getUser()->hasPermission("b2canfindissues")): ?>
			<li<?php if ($bugs_response->getPage() == 'search'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('search'), image_tag('tab_search.png', array('style' => 'float: left;')).__('Find issues')); ?></li>
		<?php endif; ?>
		<?php foreach (BUGScontext::getModules() as $module): ?>
			<?php if ($module->hasAccess() && $module->isVisibleInMenu() && $module->isEnabled()): ?>
				<li<?php if ($bugs_response->getPage() == $module->getName()): ?> class="selected"<?php endif; ?>><?php echo link_tag($module->getRoute(), image_tag('tab_' . $module->getName() . '.png', array('style' => 'float: left;'), false, $module->getName()).$module->getMenuTitle()); ?></li>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if (BUGScontext::getUser()->hasPermission("b2viewconfig", 0, 'core', true)): ?>
			<li<?php if ($bugs_response->getPage() == 'config'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure'), image_tag('tab_config.png', array('style' => 'float: left;')).__('Configure')); ?></li>
		<?php endif; ?>
		<?php /*?><li<?php if ($bugs_response->getPage() == 'about'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('about'), image_tag('tab_about.png', array('style' => 'float: left;')).__('About')); ?></li> */ ?>
	</ul>
	<ul class="right">
		<li style="height: 24px;" class="nohover">
			<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php print BUGScontext::getTBGPath(); ?>modules/search/search.php" enctype="multipart/form-data" method="post" name="quicksearchform">
				<div style="width: auto; padding: 0; text-align: right; position: relative;">
				<label for="searchfor"><?php echo __('Quick search'); ?></label>
				<?php $quicksearch_title = __('Search for anything here'); ?>
				<input type="text" name="searchfor" id="searchfor" value="<?php echo $quicksearch_title; ?>" style="width: 180px; padding: 1px 1px 1px;" onblur="if ($('searchfor').getValue() == '') { $('searchfor').value = '<?php echo $quicksearch_title; ?>'; $('searchfor').addClassName('faded_medium'); }" onfocus="if ($('searchfor').getValue() == '<?php echo $quicksearch_title; ?>') { $('searchfor').clear(); } $('searchfor').removeClassName('faded_medium');" class="faded_medium"><div id="searchfor_autocomplete_choices" class="autocomplete"></div>
				<script type="text/javascript">

				new Ajax.Autocompleter("searchfor", "searchfor_autocomplete_choices", '<?php echo make_url('quicksearch'); ?>', {paramName: "searchfor", minChars: 2});

				</script>
				<input type="submit" value="<?php echo BUGScontext::getI18n()->__('Find'); ?>" style="padding: 0 2px 0 2px;">
				</div>
			</form>
		</li>
		<?php

			BUGScontext::trigger('core', 'header_right');

		?>
	</ul>
</div>
<?php if ($bugs_response->isProjectMenuStripVisible()): ?>
	<div id="project_menustrip"><?php include_component('project/menustrip', array('project' => BUGScontext::getCurrentProject())); ?></div>
<?php endif; ?>
<?php
/*
		if (!BUGScontext::getUser()->isThisGuest())
		{
			if ($bugs_response->getPage() == "account")
			{
				?>
				<td align="center" valign="middle" class="selected" style="width: 110px;"><table align="center"><tr><td><?php echo image_tag('tab_account.png'); ?></td><td><?php echo __('My account'); ?></td></tr></table></td>
				<?php
			}
			else
			{
				?>
				<td align="center" valign="middle" onmouseover="this.className='hover_unselected';" onmouseout="this.className='unselected';" class="unselected" style="width: 110px;"><table align="center"><tr><td><a href="javascript:void(0);" onclick="Effect.Appear('myacct', { duration: 0.5 });"><?php echo image_tag('tab_account.png'); ?></a></td><td><a href="javascript:void(0);" onclick="Effect.Appear('myacct', { duration: 0.5 });"><?php echo __('My account'); ?></a></td></tr></table>
				<div style="display: none; position: absolute; right: 1px; width: 180px; padding: 5px; background-color: #FFF; border: 1px solid #DDD;" id="myacct">
				<div style="text-align: left; padding-bottom: 2px; border-bottom: 1px solid #DDD; margin-bottom: 5px;"><b><?php echo BUGScontext::getUser()->getRealname(); ?></b></div>
				<div style="text-align: left; padding-bottom: 2px;"><b><?php echo __('Places'); ?></b></div>
				<table style="width: 100%; table-layout: fixed;" cellpadding=0 cellspacing=2>
				<?php
	
					foreach (BUGScontext::getModules() as $module)
					{
						if ($module->hasAccess())
						{
							if ($module->isEnabled() && $module->isVisibleInUsermenu())
							{
								?>
								<tr>
								<td class="imgtd" style="width: 20px;"><?php echo image_tag('tab_' . $module->getName() . '.png'); ?></td>
								<td><a href="<?php print BUGScontext::getTBGPath() . "modules/" . $module->getName() . "/" . $module->getName(); ?>.php"><?php echo __($module->getMenuTitle()); ?></a></td>
								</tr>
								<?php
							}
						}
					}
	
				?>
				</table>
				<div style="text-align: left; padding-top: 5px; padding-bottom: 2px;"><b><?php echo __('Settings'); ?></b></div>
				<table style="width: 100%; table-layout: fixed;" cellpadding=0 cellspacing=2>
					<?php BUGScontext::trigger('core', 'account_settingslist'); ?>
				</table>
				<div style="text-align: left; padding-top: 5px; padding-bottom: 2px;"><b><?php echo __('Common actions'); ?></b></div>
				<table style="width: 100%;" cellpadding=0 cellspacing=2>
				<tr>
				<td class="imgtd"><?php echo image_tag('tab_account.png'); ?></td>
				<td><?php echo link_tag(make_url('account'), __('View / edit my details')); ?></td>
				</tr>
				<tr>
				<td class="imgtd"><?php echo image_tag('icon_userstate.png'); ?></td>
				<td><a href="javascript:void(0);" onclick="Element.show('availStatus');getUserStateList();"><?php echo __('Change my status to'); ?></a></td>
				</tr>
				<tr id="availStatus" style="display: none;"><td>&nbsp;</td><td><span id="user_statelist"></span>
				<div align="right" class="small"><a href="javascript:void(0);" onclick="Element.hide('availStatus');" style="font-size: 10px;"><?php echo __('Never mind'); ?></a></div></td>
				</tr>
				<tr>
				<td class="imgtd"><?php echo image_tag('logout.png'); ?></td>
				<td style="text-align: left;"><?php echo link_tag(make_url('logout'), __('Log out')); ?></td>
				</tr>
				<?php if (isset($_COOKIE['b2_username_preswitch']) && $_COOKIE['b2_username_preswitch'] != ''): ?>
					<tr>
					<td class="imgtd"><?php echo image_tag('switchuser.png'); ?></td>
					<td style="text-align: left;"><a href="login_validate.inc.php?switch_user=true"><?php echo __('Switch back to original user'); ?></a></td>
					</tr>
				<?php endif; ?>
				</table>
				<div style="font-size: 9px; text-align: right;"><a href="javascript:void(0);" onclick="Effect.Fade('myacct', { duration: 0.5 });"><?php echo __('Close menu'); ?></a></div>
				</div></td>
				<?php
			}
		}
		else
		{
			if ($bugs_response->getPage() == "login")
			{
				?>
				<td align="center" valign="middle" class="selected" style="width: 130px;"><table align="center"><tr><td><?php echo image_tag('tab_login.png'); ?></td><td><?php echo (BUGSsettings::get('allowreg') == 1) ? __('Login / Register') : __('Login'); ?></td></tr></table></td>
				<?php
			}
			else
			{
				?>
				<td align="center" valign="middle" onmouseover="this.className='hover_unselected';" onmouseout="this.className='unselected';" class="unselected" style="width: 130px;"><table align="center"><tr><td><?php echo link_tag(make_url('login'), image_tag('tab_login.png')); ?></td><td><?php echo link_tag(make_url('login'), (BUGSsettings::get('allowreg') == 1) ? __('Login / Register') : __('Login')); ?></td></tr></table></td>
				<?php
			}
		}
		
		?>
		
		</tr>
		</table>
		
		<?php */
		
?>