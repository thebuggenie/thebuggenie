<div class="backdrop_box medium" id="viewissue_add_item_div">
	<div class="backdrop_detail_header"><?php echo __('Issue access policy'); ?></div>
	<div id="backdrop_detail_content">
		<form action="<?php echo make_url('move_issue', array('issue_id' => $issue->getID())); ?>" method="post">
			<label for="issue_access_public"><?php echo __('Issue access policy'); ?></label><br>
			<input type="radio" name="issue_access" id="issue_access_public" value="public" checked><?php echo __('Available to anyone with access'); ?><br>
			<input type="radio" name="issue_access" id="issue_access_restricted" value="public" checked><?php echo __('Available only to those listed below'); ?><br>
			<div id="issue_<?php echo $issue->getID(); ?>_access_list">
				<?php foreach ($issue->getAccessList() as $item): ?>
					<input type="hidden" name="access_list[]"
				<?php endforeach; ?>
			</div>
		</form>
	</div>
	<div class="backdrop_detail_footer">
		<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Cancel'); ?></a>
	</div>
</div>