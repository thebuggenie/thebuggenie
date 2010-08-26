<div class="rounded_box white borderless shadowed backdrop_box medium" style="padding: 5px; text-align: left; font-size: 13px;">
	<div class="backdrop_detail_header"><?php echo __('Mark this issue as a duplicate'); ?></div>
	<div class="backdrop_detail_content">
		<?php echo __('Please enter some details to search for, and then the issue you want this to be a duplicate of.'); ?>
		<form id="viewissue_find_issue_form" action="<?php echo make_url('viewissue_find_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'type' => 'duplicate')); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="findDuplicateIssues('<?php echo make_url('viewissue_find_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'type' => 'duplicate')); ?>');return false;">
			<div>
				<label for="viewissue_find_issue_input"><?php echo __('Find issue(s)'); ?>&nbsp;</label>
				<input type="text" name="searchfor" id="viewissue_find_issue_input">
				<input type="submit" value="<?php echo __('Find'); ?>">
				<?php echo __('%find% or %cancel%', array('%find%' => '', '%cancel%' => '<a href="javascript:void(0);" onclick="resetFadedBackdrop();">' . __('cancel') . '</a>')); ?>
				<?php echo image_tag('spinning_20.gif', array('id' => 'find_issue_indicator', 'style' => 'display: none;')); ?><br>
			</div>
		</form>
		<div id="viewissue_duplicate_results"></div>
	</div>
	<div class="backdrop_detail_footer">
		<a href="javascript:void(0);" onclick="resetFadedBackdrop();"><?php echo __('Cancel and close this pop-up'); ?></a>
	</div>
</div>