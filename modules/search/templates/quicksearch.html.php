<ul class="rounded_box white shadowed cut_top">
	<li class="searchterm"><?php echo $searchterm; ?><br><span class="informal"><?php echo __('Press "Enter" twice to search'); ?></span></li>
	<?php $cc = 0; ?>
	<?php if (count($issues) > 0): ?>
		<?php $cc++; ?>
		<?php foreach ($issues as $issue): ?>
			<?php if ($issue instanceof TBGIssue): ?>
				<li<?php if ($cc == count($issues)): ?> class="last"<?php endif; ?>>
					<?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png', array('class' => 'informal')); ?>
					<?php echo __('Issue %issue_no% - %title%', array('%issue_no%' => $issue->getFormattedIssueNo(true), '%title%' => str_pad(substr($issue->getTitle(), 0, 32), 35, '...'))); ?><br>
					<span class="informal"><?php echo __('Last updated %updated_at%', array('%updated_at%' => tbg_formatTime($issue->getLastUpdatedTime(), 6))); ?></span>
					<span class="informal url"><?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?></span>
				</li>
			<?php else: ?>
				<?php TBGEvent::createNew('search', 'quicksearch_item', $issue)->trigger(); ?>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>