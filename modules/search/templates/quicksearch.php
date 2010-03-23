<ul>
	<li><?php echo $searchterm; ?><br><span class="informal"><?php echo __('Press "Enter" twice to search'); ?></span></li>
	<?php if (count($results) > 0): ?>
		<?php foreach ($results as $issue): ?>
			<?php if ($issue instanceof TBGIssue): ?>
				<li><?php echo __('Issue %issue_no% - %title%', array('%issue_no%' => $issue->getFormattedIssueNo(true), '%title%' => substr($issue->getTitle(), 0, 25))); ?><br><span class="informal"><?php echo __('Last updated %updated_at%', array('%updated_at%' => tbg_formatTime($issue->getLastUpdatedTime(), 6))); ?></span></li>
			<?php else: ?>
				<?php TBGEvent::createNew('search', 'quicksearch_item', $issue)->trigger(); ?>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>