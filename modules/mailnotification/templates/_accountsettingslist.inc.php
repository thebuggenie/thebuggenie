<li>
	<?php echo image_tag('cfg_icon_mailnotification.png', array('style' => 'float: left; margin-right: 5px;'), false, 'mailnotification'); ?>
	<a href="<?php print TBGContext::getTBGPath() . "account.php?settings=mailnotification"; ?>"><?php echo TBGContext::getI18n()->__('Notification settings'); ?></a>
</li>