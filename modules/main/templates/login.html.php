<?php

	TBGContext::loadLibrary('ui');
?>
<div class="login_page_div">
<?php include_component('main/login', compact('section', 'error')); ?>
</div>
<script type="text/javascript">
	<?php if (TBGContext::hasMessage('login_message')): ?>
		TBG.Main.Helpers.Message.success('<?php echo TBGContext::getMessageAndClear('login_message'); ?>');
	<?php elseif (TBGContext::hasMessage('login_message_err')): ?>
		TBG.Main.Helpers.Message.error('<?php echo TBGContext::getMessageAndClear('login_message_err'); ?>');
	<?php endif; ?>
	document.observe('dom:loaded', function() {
		$('tbg3_username').focus();
	});
</script>