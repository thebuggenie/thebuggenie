<?php

	TBGContext::loadLibrary('ui');
?>
<div class="login_page_div">
<?php
	echo $content;
?>
</div>
<?php if (TBGContext::hasMessage('login_message')): ?>
<script type="text/javascript">
	TBG.Main.Helpers.Message.success('<?php echo TBGContext::getMessageAndClear('login_message'); ?>');
</script>
<?php elseif (TBGContext::hasMessage('login_message_err')): ?>
<script type="text/javascript">
	TBG.Main.Helpers.Message.error('<?php echo TBGContext::getMessageAndClear('login_message_err'); ?>');
</script>
<?php endif; ?>