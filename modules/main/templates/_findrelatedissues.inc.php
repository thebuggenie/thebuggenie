<?php if ($count > 0): ?>
	<div class="viewissue_info_header"><?php echo __('The following issues matched your search'); ?>:</div>
	<div class="viewissue_info_content">
		<span class="faded_medium"><?php echo __('Either use the checkboxes and press the "%relate_these_issues%"-button below or click any issues in the list, and select an action.', array('%relate_these_issues%' => __('Relate these issues'))); ?></span>
		<form id="viewissue_relate_issues_form" action="<?php echo make_url('viewissue_relate_issues', array('project_key' => $theIssue->getProject()->getKey(), 'issue_id' => $theIssue->getID())); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="relateIssues('<?php echo make_url('viewissue_relate_issues', array('project_key' => $theIssue->getProject()->getKey(), 'issue_id' => $theIssue->getID())); ?>');return false;">
			<table style="width: auto; border: 0;" cellpadding="0" cellspacing="0">
				<?php foreach ($issues as $issue): ?>
					<tr>
						<td style="width: 20px;"><input type="checkbox" value="<?php echo $issue->getID(); ?>" name="relate_issues[<?php echo $issue->getID(); ?>]"></td>
						<td class="issue_title">
							<?php echo $issue->getFormattedTitle(); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<div style="text-align: right; border-top: 1px dotted #CCC; padding-top: 5px;">
				<select id="relate_issue_with_selected" name="relate_action">
					<option value="relate_children" selected><?php echo __('Mark selected issues as child issues of this issue'); ?></option>
					<option value="relate_parents"><?php echo __('Mark selected issues as parent issues of this issue'); ?></option>
				</select>
				<input type="submit" value="<?php echo __('Relate these issues'); ?>">
				<?php echo image_tag('spinning_20.gif', array('id' => 'relate_issues_indicator', 'style' => 'display: none;')); ?><br>
			</div>
		</form>
	</div>
<?php else: ?>
	<span class="faded_medium"><?php echo __('No issues matched your search. Please try again with different search terms.'); ?></span>
<?php endif; ?>