<div style="padding: 5px; border-bottom: 1px solid #DDD; background-color: #F8F8F8;"><b><?php echo __('"Report an issue", step 2'); ?></b></div>
<div style="padding: 5px;">
<?php echo __('Every edition is made up of several components, which alltogether makes one edition. Please select which component is affected by your issue.'); ?><br>
<br>
<?php echo __('You must also select a severity and a category for your issue, and an issue type. The severity defines how serious the issue is for you, while the category helps developers decide how to best resolve your issue, and the issue type makes sure the right developer gets to your issue.'); ?><br>
<br>
<div style="width: auto; border-bottom: 1px dotted #DDD;"><i><?php echo __('Don\'t know what to choose?'); ?></i></div>
<?php echo bugs_helpBrowserHelper('reportissue_issuetype', __('What is an issue type?')); ?><br>
<?php echo bugs_helpBrowserHelper('reportissue_component', __('What is a component?')); ?><br>
<?php echo bugs_helpBrowserHelper('reportissue_category', __('What is a category?')); ?><br>
<?php echo bugs_helpBrowserHelper('reportissue_severity', __('What is "severity"?')); ?><br>
<br>
<br>
<?php echo __('When you are happy with the selections, please confirm it by pressing the "Confirm" button to the far right, which will take you to step 3.'); ?>
</div>