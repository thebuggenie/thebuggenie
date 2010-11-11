<div class="rounded_box white borderless shadowed backdrop_box small" style="padding: 5px; text-align: left; font-size: 13px;">
	<div class="backdrop_detail_header"><?php echo $transition->getDescription(); ?></div>
	<form action="<?php echo make_url('transition_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'transition_id' => $transition->getID())); ?>" method="post" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>">
		<div class="backdrop_detail_content">
			<ul class="simple_list">
				<?php if (in_array('status', $transition->getProperties())): ?>
					<li>
						<input type="hidden" name="set_status" id="close_issue_set_status" value="1">
						<label for="close_issue_set_status"><?php echo __('Status'); ?></label>
						<select name="status_id">
							<?php foreach ($statuses as $status): ?>
								<option value="<?php echo $status->getID(); ?>"<?php if ($issue->getStatus() instanceof TBGStatus && $issue->getStatus()->getID() == $status->getID()): ?> selected<?php endif; ?>><?php echo $status->getName(); ?></option>
							<?php endforeach; ?>
						</select>
					</li>
				<?php endif; ?>
				<?php if (in_array('resolution', $transition->getProperties())): ?>
					<li id="close_issue_resolution_div"<?php if (!$issue->isResolutionVisible()): ?> style="display: none;"<?php endif; ?>>
						<input type="hidden" name="set_resolution" id="close_issue_set_resolution" value="1">
						<label for="close_issue_set_resolution"><?php echo __('Resolution'); ?></label>
						<select name="resolution_id">
							<?php foreach ($fields_list['resolution']['choices'] as $resolution): ?>
							<option value="<?php echo $resolution->getID(); ?>"<?php if ($issue->getResolution() instanceof TBGResolution && $issue->getResolution()->getID() == $resolution->getID()): ?> selected<?php endif; ?>><?php echo $resolution->getName(); ?></option>
							<?php endforeach; ?>
						</select>
					</li>
					<?php if (!$issue->isResolutionVisible()): ?>
						<li id="close_issue_resolution_link" class="faded_out">
							<?php echo __("Resolution isn't visible for this issuetype / product combination"); ?>
							<a href="javascript:void(0);" onclick="$('close_issue_resolution_link').hide();$('close_issue_resolution_div').show();"><?php echo __('Set anyway'); ?></a>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<li style="margin-top: 10px;">
					<label for="close_comment"><?php echo __('Write a comment if you want it to be added'); ?></label>
					<textarea name="close_comment" id="close_comment" style="width: 372px; height: 50px;"></textarea>
				</li>
			</ul>
			<div style="text-align: right; margin-right: 5px;">
				<input type="submit" value="<?php echo $transition->getName(); ?>">
			</div>
		</div>
		<div class="backdrop_detail_footer">
			<?php echo '<a href="javascript:void(0);" onclick="resetFadedBackdrop();">' . __('Cancel and close this pop-up') . '</a>'; ?>
		</div>
	</form>
</div>