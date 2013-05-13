<div class="almost_not_transparent shadowed popup_message failure" onclick="TBG.Main.Helpers.Message.clear();" style="display: none;" id="thebuggenie_failuremessage">
	<div style="padding: 10px 0 10px 0;">
		<div class="dismiss_me"><?php echo __('Click this message to dismiss it'); ?></div>
		<span style="color: #000; font-weight: bold;" id="thebuggenie_failuremessage_title"></span><br>
		<span id="thebuggenie_failuremessage_content"></span>
	</div>
</div>
<div class="almost_not_transparent shadowed popup_message success" onclick="TBG.Main.Helpers.Message.clear();" style="display: none;" id="thebuggenie_successmessage">
	<div style="padding: 10px 0 10px 0;">
		<div class="dismiss_me"><?php echo __('Click this message to dismiss it'); ?></div>
		<span style="color: #000; font-weight: bold;" id="thebuggenie_successmessage_title"></span><br>
		<span id="thebuggenie_successmessage_content"></span>
	</div>
</div>
<div id="fullpage_backdrop" class="fullpage_backdrop" style="display: none;">
	<div style="position: absolute; top: 45%; left: 40%; z-index: 100001; color: #FFF; font-size: 15px; font-weight: bold;" id="fullpage_backdrop_indicator">
		<?php echo image_tag('spinning_32.gif'); ?><br>
		<?php echo __('Please wait ...'); ?>
	</div>
	<div id="fullpage_backdrop_content" class="fullpage_backdrop_content"> </div>
</div>
<?php if (TBGContext::getRouting()->getCurrentRouteName() != 'login_page' && $tbg_user->isGuest()): ?>
	<div id="login_backdrop" class="fullpage_backdrop" style="display: none;">
		<div id="login_content" class="fullpage_backdrop_content">
			<?php include_component('main/loginpopup', array('content' => get_component_html('main/login'), 'mandatory' => false)); ?>
		</div>
	</div>
<?php endif; ?>
<?php if (TBGSettings::isPersonaAvailable() && ($tbg_user->isGuest() || $tbg_request->hasCookie('tbg3_persona_session'))): ?>
	<script src="https://login.persona.org/include.js"></script>
	<script type="text/javascript">
		document.observe('dom:loaded', function() {
			var currentUser = <?php echo (!$tbg_user->isGuest()) ? "'{$tbg_user->getEmail()}'" : 'null'; ?>;

			navigator.id.watch({
			  loggedInUser: currentUser,
			  onlogin: function(assertion) {
				// A user has logged in! Here you need to:
				// 1. Send the assertion to your backend for verification and to create a session.
				// 2. Update your UI.
				TBG.Main.Helpers.ajax('<?php echo make_url('login'); ?>', {
					url_method: 'post',
					additional_params: '&persona=true&assertion='+assertion+'&referrer_route=<?php echo TBGContext::getRouting()->getCurrentRouteName(); ?>',
					loading: {
						indicator: 'fullpage_backdrop',
						clear: 'fullpage_backdrop_content',
						hide: 'login_backdrop',
						show: 'fullpage_backdrop_indicator'
					},
					success: {
						callback: function(json) {
							window.location.reload();
						}
					},
					failure: {
						callback: function(json) {
							navigator.id.logout();
						}
					}
				});
			  },
			  onlogout: function() {
				// A user has logged out! Here you need to:
				// Tear down the user's session by redirecting the user or making a call to your backend.
				// Also, make sure loggedInUser will get set to null on the next page load.
				// (That's a literal JavaScript null. Not false, 0, or undefined. null.)
				TBG.Main.Helpers.ajax('<?php echo make_url('logout'); ?>', {
					url_method: 'post',
					loading: {
						indicator: 'fullpage_backdrop',
						clear: 'fullpage_backdrop_content',
						show: 'fullpage_backdrop_indicator'
					},
					success: {
						callback: function(json) {
							window.location = json.url;
						}
					}
				});
			  }
			});
			if ($('persona-signin-button')) $('persona-signin-button').observe('click', function() { navigator.id.request(); } );
		});
	</script>
<?php endif; ?>
<div id="dialog_backdrop" style="display: none; background-color: transparent; width: 100%; height: 100%; position: fixed; top: 0; left: 0; margin: 0; padding: 0; text-align: center; z-index: 100000;">
	<div id="dialog_backdrop_content" class="fullpage_backdrop_content">
		<div class="rounded_box shadowed_box white cut_top cut_bottom bigger">
			<div style="width: 900px; text-align: left; margin: 0 auto; font-size: 13px;">
				<?php echo image_tag('dialog_question.png', array('style' => 'float: left;')); ?>
				<h3 id="dialog_title"></h3>
				<p id="dialog_content"></p>
			</div>
			<div style="text-align: center; padding: 10px;">
				<?php echo image_tag('spinning_20.gif', array('style' => 'float: right; display: none;', 'id' => 'dialog_indicator')); ?>
				<a href="javascript:void(0)" id="dialog_yes" class="button button-green"><?php echo __('Yes'); ?></a>
				<a href="javascript:void(0)" id="dialog_no" class="button button-red"><?php echo __('No'); ?></a>
			</div>
		</div>
	</div>
	<div style="background-color: #000; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; z-index: 999;" class="semi_transparent"> </div>
</div>