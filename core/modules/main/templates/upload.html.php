<?php if (isset($error)): ?>
    frameElement.parent.TBG.Main.Helpers.Message.error('<?php echo $error; ?>');
<?php else: ?>
    frameElement.parent.TBG.Main.Helpers.Message.success('<?php echo __('The file "%filename" was uploaded successfully'); ?>');
<?php endif; ?>
