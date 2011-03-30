<ul class="rounded_box white shadowed cut_top">
	<li class="searchterm"><?php echo $searchterm; ?><br><span class="informal"><?php echo __('Press "Enter" twice to search'); ?></span></li>
	<li class="header disabled"><?php echo __('%num% issue(s) found', array('%num%' => $resultcount)); ?></li>
	<?php $cc = 0; ?>
	<?php if ($resultcount > 0): ?>
		<?php foreach ($issues as $issue): ?>
			<?php $cc++; ?>
			<?php if ($issue instanceof TBGIssue): ?>
				<li class="issue_<?php echo ($issue->isOpen()) ? 'open' : 'closed'; ?><?php if ($cc == count($issues) && $resultcount == count($issues)): ?> last<?php endif; ?>">
					<?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png', array('class' => 'informal')); ?>
					<div><?php echo __('Issue %issue_no% - %title%', array('%issue_no%' => $issue->getFormattedIssueNo(true), '%title%' => str_pad(substr($issue->getTitle(), 0, 32), 35, '...'))); ?></div>
					<span class="informal"><?php if ($issue->isClosed()): ?>[<?php echo strtoupper(__('Closed')); ?>] <?php endif; ?><?php echo __('Last updated %updated_at%', array('%updated_at%' => tbg_formatTime($issue->getLastUpdatedTime(), 6))); ?></span>
					<span class="informal url"><?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?></span>
				</li>
			<?php else: ?>
				<?php TBGEvent::createNew('search', 'quicksearch_item', $issue)->trigger(); ?>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if ($resultcount - $cc > 0): ?>
			<li class="find_more_issues last">
				<span class="informal"><?php echo __('See %num% more issues ...', array('%num%' => $resultcount - $cc)); ?></span>
				<div class="hidden url"><?php echo (TBGContext::isProjectContext()) ? make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('search'); ?>?filters[text][value]=<?php echo $searchterm; ?>&filters[text][operator]=<?php echo urlencode('='); ?></div>
			</li>
		<?php endif; ?>
	<?php else: ?>
		<li class="disabled no_issues_found">
			<?php echo __('No issues found matching your query'); ?>
		</li>
	<?php endif; ?>
</ul>