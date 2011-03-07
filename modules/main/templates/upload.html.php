<?php if (isset($error)): ?>
	frameElement.parent.thebuggenie.events.failedMessage('<?php echo $error; ?>');
<?php else: ?>
	frameElement.parent.thebuggenie.events.successMessage('<?php echo __('The file "%filename%" was uploaded successfully'); ?>');
<?php endif; ?>