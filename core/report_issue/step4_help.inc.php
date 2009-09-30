<div style="padding: 5px; border-bottom: 1px solid #DDD; background-color: #F8F8F8;"><b><?php echo __('"Report an issue", step 4'); ?></b></div>
<div style="padding: 5px;">
<?php echo __('Finally, if you have any links related to the issue, or if you have any files you need to upload to this issue report, add them here.'); ?><br>
<br>
<div style="width: auto; border-bottom: 1px dotted #DDD;"><i><?php echo __('Don\'t know what to do?'); ?></i></div>
<?php echo bugs_helpBrowserHelper('reportissue_addlink', __('How do I add a link to my report?')); ?><br>
<?php echo bugs_helpBrowserHelper('reportissue_addfile', __('How do I upload a file to my report?')); ?><br>
<?php echo bugs_helpBrowserHelper('reportissue_addscreenshot', __('How do I add a screenshot to my report?')); ?><br>
<br>
<br>
<?php echo __('Whenever you have added all the links and/or files you planned to, press the "Confirm" button to get an overview of the issue report before you post it.'); ?><br><br>
<div style="border: 1px dotted #DDD; padding: 5px;"><i><?php echo __('If you don\'t want to add any links or files, just press the "Confirm" button right away.'); ?></i></div>
</div>