<div class="rounded_box iceblue borderless" style="margin: 10px 0px 10px 10px; vertical-align: middle; font-size: 14px;">
	<?php echo image_tag('summary_badge.png', array('style' => 'float: left; margin-right: 5px;'), false, 'messages'); ?>
	<?php if ($messages['unread'] > 0): ?>
		<strong><?php echo __('%number_of% new messages', array('%number_of%' => $messages['unread'])); ?></strong>
	<?php else: ?>
		<?php echo __('No new messages'); ?>
	<?php endif; ?>
	<br />
	<span style="font-size: 12px;"><?php echo __('in your %inbox%', array('%inbox%' => javascript_link_tag(__('inbox'), array('onclick' => "failedMessage('Not available', 'Messaging functionality is not implemented yet');")))); ?></span>
	<br />
	<div style="clear: both; margin-top: 15px; font-size: 13px;">
		<?php echo image_tag('write_message.png', array('style' => 'float: left; margin-right: 5px;'), false, 'messages'); ?>
		<?php echo javascript_link_tag(__('Write a message'), array('onclick' => "failedMessage('Not available', 'Messaging functionality is not implemented yet');")); ?>
	</div>
</div>