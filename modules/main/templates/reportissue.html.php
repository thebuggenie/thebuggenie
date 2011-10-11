<?php 

	$tbg_response->addBreadcrumb(__('Report an issue'), make_url('project_reportissue', array('project_key' => TBGContext::getCurrentProject()->getKey())), tbg_get_breadcrumblinks('project_summary', TBGContext::getCurrentProject()));
	$tbg_response->setTitle(__('Report an issue'));
	
	if (TBGContext::getCurrentProject()->isLocked() == true)
	{
	   ?>
      <div class="rounded_box red borderless" id="notfound_error">
      		<div class="viewissue_info_header"><?php echo __("Reporting disabled"); ?></div>
      		<div class="viewissue_info_content">
      			<?php if (isset($message) && $message): ?>
      				<?php echo $message; ?>
      			<?php else: ?>
      				<?php echo __("The administrator has disabled reporting issues for this project"); ?>
      			<?php endif; ?>
      		</div>
      	</div>
      </div>
    <?php
    return;
  }
?>
<div style="text-align: center; margin-bottom: 10px;">
	<div class="report_issue_header">
		<?php echo __("What's the issue?"); ?>
	</div>
	<div id="reportissue_content">
		<?php include_component('main/reportissue', compact('issue', 'errors', 'permission_errors', 'selected_project', 'issuetypes')); ?>
	</div>
</div>
