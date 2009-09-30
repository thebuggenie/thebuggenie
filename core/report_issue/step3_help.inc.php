<div style="padding: 5px; border-bottom: 1px solid #DDD; background-color: #F8F8F8;"><b><?php echo __('"Report an issue", step 3'); ?></b></div>
<div style="padding: 5px;">
<?php echo __('In this step it is time to describe your issue. The summary should be short but descriptive, and the description should describe your issue in as much detail as possible.'); ?><br>
<br>
<?php echo __('If possible, you should provide a way to reproduce the issue.'); ?><br>
<br>
<div style="width: auto; border-bottom: 1px dotted #DDD;"><i><?php echo __('Don\'t know what to write?'); ?></i></div>
<?php echo bugs_helpBrowserHelper('reportissue_summary', __('What should an issue summary contain?')); ?><br>
<?php echo bugs_helpBrowserHelper('reportissue_description', __('What should a description contain?')); ?><br>
<?php echo bugs_helpBrowserHelper('reportissue_reproduction', __('What should "reproduction steps" contain?')); ?><br>
<br>
<br>
<?php echo __('Whenever you are happy with what you have written, please confirm it by pressing the "Confirm" button to the far right, which will take you to the last step.'); ?><br><?php echo __('(Which is optional, by the way)'); ?>
</div>