<?php if ($count > 0): ?>
	<div class="header_div" style="margin-bottom: 5px;"><?php echo __('The following issues matched your search'); ?>:</div>
	<form action="<?php echo make_url('markasduplicate', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>" method="post" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>">
		<input type="hidden" name="issue_action" value="duplicate">
		<select name="duplicate_issue" style="width: 100%">
			<?php foreach ($issues as $aissue): ?>
				<option value="<?php echo $aissue->getID(); ?>"><?php echo $aissue->getFormattedTitle(); ?></option>
			<?php endforeach; ?>
		</select>
		<br><br>
		<?php echo __('Do you want to change some of these values as well?'); ?>
		<ul class="duplicate_issues">
			<li>
				<input type="checkbox" name="set_close" id="markasduplicate_issue_set_closed" value="1" checked><label for="markasduplicate_issue_set_closed"><?php echo __('Close issue'); ?></label>
			</li>
			<li>
				<input type="checkbox" name="set_status" id="markasduplicate_issue_set_status" value="1"><label for="markasduplicate_issue_set_status"><?php echo __('Status'); ?></label>
				<select name="status_id">
					<option value="0"> </option>
					<?php foreach ($statuses as $status): ?>
						<option value="<?php echo $status->getID(); ?>"><?php echo $status->getName(); ?></option>
					<?php endforeach; ?>
				</select>
			</li>
			<li id="markasduplicate_issue_resolution_div"<?php if (!$issue->isResolutionVisible()): ?> style="display: none;"<?php endif; ?>>
				<input type="checkbox" name="set_resolution" id="markasduplicate_issue_set_resolution" value="1"><label for="markasduplicate_issue_set_resolution"><?php echo __('Resolution'); ?></label>
				<select name="resolution_id">
					<option value="0"> </option>
					<?php foreach ($fields_list['resolution']['choices'] as $resolution): ?>
						<option value="<?php echo $resolution->getID(); ?>"><?php echo $resolution->getName(); ?></option>
					<?php endforeach; ?>
				</select>
			</li>
			<?php if (!$issue->isResolutionVisible()): ?>
				<li id="markasduplicate_issue_resolution_link" class="faded_out">
					<?php echo __("Resolution isn't visible for this issuetype / product combination"); ?>
					<a href="javascript:void(0);" onclick="$('markasduplicate_issue_resolution_link').hide();$('markasduplicate_issue_resolution_div').show();"><?php echo __('Set anyway'); ?></a>
				</li>
			<?php endif; ?>
			<li>
				<label for="markasduplicate_comment"><?php echo __('Write a comment if you want it to be added'); ?></label>
				<?php include_template('main/textarea', array('area_name' => 'markasduplicate_comment', 'area_id' => 'markasduplicate_comment', 'height' => '75px', 'width' => '570px', 'value' => '')); ?>
			</li>
		</ul>
		<div style="text-align: right; margin-right: 5px;">
			<input type="submit" value="<?php echo __('Mark as duplicate'); ?>" />
		</div>
	</form>
<?php else: ?>
	<span class="faded_out"><?php echo __('No issues matched your search. Please try again with different search terms.'); ?></span>
<?php endif; ?>