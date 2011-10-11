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
<div id="fullpage_backdrop" style="display: none; background-color: transparent; z-index: 100000; width: 100%; height: 100%; position: fixed; top: 0; left: 0; margin: 0; padding: 0; text-align: center;">
	<div style="position: absolute; top: 45%; left: 40%; z-index: 100001; color: #FFF; font-size: 15px; font-weight: bold;" id="fullpage_backdrop_indicator">
		<?php echo image_tag('spinning_32.gif'); ?><br>
		<?php echo __('Please wait ...'); ?>
	</div>
	<div id="fullpage_backdrop_content" class="fullpage_backdrop_content"> </div>
	<div style="background-color: #000; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; z-index: 100000;" class="semi_transparent" <?php if (TBGContext::getRouting()->getCurrentRouteAction() != 'login'): ?>onclick="TBG.Main.Helpers.Backdrop.reset();"<?php endif; ?>> </div>
</div>
<div id="dialog_backdrop" style="display: none; background-color: transparent; width: 100%; height: 100%; position: fixed; top: 0; left: 0; margin: 0; padding: 0; text-align: center; z-index: 100000;">
	<div id="dialog_backdrop_content" class="fullpage_backdrop_content">
		<div class="rounded_box shadowed_box white cut_top cut_bottom bigger">
			<div style="width: 900px; text-align: left; margin: 0 auto; font-size: 13px;">
				<?php echo image_tag('dialog_question.png', array('style' => 'float: left;')); ?>
				<h3 id="dialog_title"></h3>
				<p id="dialog_content"></p>
			</div>
			<div style="text-align: center; padding: 10px;">
				<a href="javascript:void(0)" id="dialog_yes" class="button button-green"><?php echo __('Yes'); ?></a>
				<a href="javascript:void(0)" id="dialog_no" class="button button-red"><?php echo __('No'); ?></a>
			</div>
		</div>
	</div>
	<div style="background-color: #000; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; z-index: 999;" class="semi_transparent"> </div>
</div>