<div style="padding: 5px; border-bottom: 1px solid #DDD; background-color: #F8F8F8;"><b><?php echo __('Welcome to the "Report an issue" wizard!'); ?></b></div>
<div style="padding: 5px;">
<?php echo __('This is the first step. Please select which project you are reporting an issue for. You also need to select an edition and a build.'); ?><br>
<br>
<div style="width: auto; border-bottom: 1px dotted #DDD;"><i><?php echo __('Don\'t know what to choose?'); ?></i></div>
<?php echo bugs_helpBrowserHelper('reportissue_project', __('What project do I select?')); ?><br>
<?php echo bugs_helpBrowserHelper('reportissue_edition', __('What edition do I select?')); ?><br>
<?php echo bugs_helpBrowserHelper('reportissue_build', __('What build do I select?')); ?><br>
<br>
<br>
<?php echo __('If you are happy with the selections, please confirm it by pressing the "Confirm" button to the far right, which will take you to step 2.'); ?>
</div>