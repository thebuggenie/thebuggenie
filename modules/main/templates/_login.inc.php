		<div class="tab_menu">
			<ul id="login_menu">
				<li id="tab_login"<?php if ($selected_tab == 'login'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(image_tag('icon_login.png', array('style' => 'float: left;')).__('Login'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_login', 'login_menu');")); ?></li>
				<?php TBGEvent::createNew('core', 'login_form_tab')->trigger(array('selected_tab' => $selected_tab)); ?>
				<?php if (TBGSettings::get('allowreg') == true): ?>
					<li id="tab_register"<?php if ($selected_tab == 'register'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(image_tag('icon_register.png', array('style' => 'float: left;')).__('Register new account'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_register', 'login_menu');")); ?></li>
				<?php endif; ?>
			</ul>
		</div>
		<div id="login_menu_panes">
			<div id="tab_login_pane"<?php if ($selected_tab != 'login'): ?> style="display: none;"<?php endif; ?>>
				<script language="text/javascript">
					if (document.location.href.search('<?php echo make_url('login_page'); ?>') != -1)
					{
						$('tbg3_referer').setValue('<?php echo make_url('dashboard'); ?>');
					}
				</script>
				<?php if ($article instanceof TBGWikiArticle): ?>
					<?php include_component('publish/articledisplay', array('article' => $article, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true)); ?>
				<?php endif; ?>
				<div class="logindiv regular">			
					<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('login'); ?>" method="post" id="login_form" onsubmit="TBG.Main.Login.login('<?php echo make_url('login'); ?>'); return false;">
						<?php if (!TBGContext::hasMessage('login_force_redirect') || TBGContext::getMessage('login_force_redirect') !== true): ?>
							<input type="hidden" id="tbg3_referer" name="tbg3_referer" value="<?php echo $referer; ?>" />
						<?php else: ?>
							<input type="hidden" id="return_to" name="return_to" value="<?php echo $referer; ?>" />
						<?php endif; ?>
						<div class="login_boxheader regular"><?php echo __('Log in to an existing account'); ?></div>
						<div>
							<table border="0" class="login_fieldtable">
								<tr>
									<td><label class="login_fieldheader" for="tbg3_username"><?php echo __('Username'); ?></label></td>
									<td><input type="text" id="tbg3_username" name="tbg3_username" style="width: 170px;"></td>
								</tr>
								<tr>
									<td><label class="login_fieldheader" for="tbg3_password"><?php echo __('Password'); ?></label></td>
									<td><input type="password" id="tbg3_password" name="tbg3_password" style="width: 170px;"></td>
								</tr>
							</table>
							<br>
							<input type="submit" id="login_button" class="button button-green" value="<?php echo __('Continue'); ?>">
							<span id="login_indicator" style="display: none;"><?php echo image_tag('spinning_20.gif'); ?></span>
						</div>
					</form>
				</div>
				<?php if (TBGSettings::isOpenIDavailable()): ?>
					<?php include_template('main/openidbuttons'); ?>
				<?php endif; ?>
			</div>
			<br style="clear: both;">
			<?php TBGEvent::createNew('core', 'login_form_pane')->trigger(array_merge(array('selected_tab' => $selected_tab), $options)); ?>
			<?php if (TBGSettings::get('allowreg') == true): ?>
				<?php include_template('main/loginregister', array('selected_tab' => $selected_tab)); ?>
			<?php endif; ?>
			
		</div>
		<div id="backdrop_detail_indicator" style="text-align: center; padding: 50px; display: none;">
			<?php echo image_tag('spinning_32.gif'); ?>
		</div>
<?php if (isset($error)): ?>
	<script type="text/javascript">
		TBG.Main.Helpers.Message.error('<?php echo $error; ?>');
	</script>
<?php endif; ?>
<script type="text/javascript">
	<?php if (!$tbg_request->isAjaxCall()): ?>
	document.observe('dom:loaded', function() {
	<?php endif; ?>
		$('tbg3_username').focus();
	<?php if (!$tbg_request->isAjaxCall()): ?>
	});
	<?php endif; ?>
</script>
