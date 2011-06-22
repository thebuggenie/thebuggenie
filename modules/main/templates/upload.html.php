<?php if (isset($error)): ?>
	frameElement.parent.TBG.Main.failedMessage('<?php echo $error; ?>');
<?php else: ?>
	frameElement.parent.TBG.Main.successMessage('<?php echo __('The file "%filename%" was uploaded successfully'); ?>');
<?php endif; ?>