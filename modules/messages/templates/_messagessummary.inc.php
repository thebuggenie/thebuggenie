<div class="rounded_box iceblue_borderless" style="margin: 10px 0px 10px 10px;">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px 10px 5px 10px; font-size: 14px;">
		<?php echo image_tag('summary_badge.png', array('style' => 'float: left; margin-right: 5px;'), false, 'messages'); ?>
		<?php if ($messages['unread'] > 0): ?>
			<strong><?php echo __('%number_of% new messages', array('%number_of%' => $messages['unread'])); ?></strong>
		<?php else: ?>
			<?php echo __('No new messages'); ?>
		<?php endif; ?>
		<br />
		<span style="font-size: 12px;"><?php echo __('in your %inbox%', array('%inbox%' => '<a href="#">' . __('inbox') . '</a>')); ?></span>
		<br />
		<div style="clear: both; margin-top: 15px; font-size: 13px;">
			<?php echo image_tag('write_message.png', array('style' => 'float: left; margin-right: 5px;'), false, 'messages'); ?>
			<a href="#">Write a message</a>
		</div>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>