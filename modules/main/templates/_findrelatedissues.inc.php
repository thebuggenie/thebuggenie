<?php if ($count > 0): ?>
	<div class="header_div"><?php echo __('The following issues matched your search'); ?>:</div>
	<span class="faded_out"><?php echo __('Either use the checkboxes and press the "%relate_these_issues%"-button below or click any issues in the list, and select an action.', array('%relate_these_issues%' => __('Relate these issues'))); ?></span>
	<form id="viewissue_relate_issues_form" action="<?php echo make_url('viewissue_relate_issues', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="TBG.Issues.relate('<?php echo make_url('viewissue_relate_issues', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>');return false;">
		<div style="height: 400px; overflow: auto">
		<table style="width: auto; border: 0;" cellpadding="0" cellspacing="0">
			<?php foreach ($issues as $aissue): ?>
				<?php if ($aissue->getID() == $issue->getID()): continue; endif; ?>
				<tr>
					<td style="width: 20px;"><input type="checkbox" value="<?php echo $aissue->getID(); ?>" name="relate_issues[<?php echo $aissue->getID(); ?>]"></td>
					<td class="issue_title">
						[<?php if ($aissue->getState() == TBGIssue::STATE_OPEN): echo __('OPEN'); else: echo __('CLOSED'); endif; ?>] <?php echo $aissue->getFormattedTitle(); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		</div>
		<div style="text-align: right; border-top: 1px dotted #CCC; padding-top: 5px;">
			<select id="relate_issue_with_selected" name="relate_action">
				<option value="relate_children" selected><?php echo __('Mark selected issues as child issues of this issue'); ?></option>
				<option value="relate_parents"><?php echo __('Mark selected issues as parent issues of this issue'); ?></option>
			</select>
			<input type="submit" value="<?php echo __('Relate these issues'); ?>">
			<?php echo image_tag('spinning_20.gif', array('id' => 'relate_issues_indicator', 'style' => 'display: none;')); ?><br>
		</div>
	</form>
	
<?php else: ?>
	<span class="faded_out"><?php echo __('No issues matched your search. Please try again with different search terms.'); ?></span>
<?php endif; ?>