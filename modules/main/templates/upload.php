<?php if (isset($error)): ?>
	frameElement.parent.failedMessage('<?php echo $error; ?>');
<?php else: ?>
	frameElement.parent.successMessage('<?php echo __('The file "%filename%" was uploaded successfully'); ?>');
<?php endif; ?>