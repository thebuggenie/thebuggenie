<ul class="rounded_box white shadowed cut_top">
	<li class="searchterm"><?php echo $searchterm; ?><br><span class="informal"><?php echo __('Press "Enter" twice to find issues matching your query'); ?></span></li>
	<?php TBGEvent::createNew('core', 'quicksearch_dropdown_firstitems', $searchterm)->trigger(); ?>
	<li class="header disabled"><?php echo __('%num% issue(s) found', array('%num%' => $resultcount)); ?></li>
	<?php $cc = 0; ?>
	<?php if ($resultcount > 0): ?>
		<?php foreach ($issues as $issue): ?>
			<?php $cc++; ?>
			<?php if ($issue instanceof TBGIssue): ?>
	<li class="issue_<?php echo ($issue->isOpen()) ? 'open' : 'closed'; ?><?php if ($cc == count($issues) && $resultcount == count($issues)): ?> last<?php endif; ?>"><?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png', array('class' => 'informal')); ?><div><?php echo __('Issue %issue_no% - %title%', array('%issue_no%' => $issue->getFormattedIssueNo(true), '%title%' => (mb_strlen($issue->getTitle()) <= 32) ? $issue->getTitle() : str_pad(mb_substr($issue->getTitle(), 0, 32), 35, '...'))); ?></div><span class="informal"><?php if ($issue->isClosed()): ?>[<?php echo mb_strtoupper(__('Closed')); ?>] <?php endif; ?><?php echo __('Last updated %updated_at%', array('%updated_at%' => tbg_formatTime($issue->getLastUpdatedTime(), 6))); ?></span><span class="informal url"><?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?></span><div class="informal extra"><?php echo __('Status: %status%', array('%status%' => '<span>'.$issue->getStatus()->getName().'</span>')); ?></div><?php if ($issue->isResolutionVisible()): ?><div class="informal extra"><?php echo __('Resolution: %resolution%', array('%resolution%' => '<span>'.(($issue->getResolution() instanceof TBGResolution) ? $issue->getResolution()->getName() : '<span class="faded_out">'.__('Not determined').'</span>').'</span>')); ?></div><?php endif; ?><div class="informal extra attached"><?php echo image_tag('icon_comments.png'); ?><span class="num_attachments"><?php echo $issue->countComments(); ?></span><?php echo image_tag('icon_attached_information.png'); ?><span class="num_attachments"><?php echo $issue->countFiles(); ?></span></div></li>
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
	<?php TBGEvent::createNew('core', 'quicksearch_dropdown_founditems', $searchterm)->trigger(); ?>
</ul>