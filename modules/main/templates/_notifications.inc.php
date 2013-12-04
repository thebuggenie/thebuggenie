<ul>
	<?php if ($num_unread + $num_read == 0): ?>
		<li class="faded_out"><?php echo __('You have no notifications'); ?></li>
	<?php else: ?>
		<?php foreach ($notifications as $notification): ?>
		<li class="<?php echo ($notification->isRead()) ? 'read' : 'unread'; ?>">
			<?php 
			
				switch ($notification->getNotificationType())
				{
					case TBGNotification::TYPE_ISSUE_CREATED:
						?>
						<h1><?php echo __('%user_name created a new issue under %project_name', array('%user_name' => get_component_html('main/userdropdown', array('user' => $notification->getTarget()->getPostedBy())), '%project_name' => link_tag(make_url('project_dashboard', array('project_key' => $notification->getTarget()->getProject()->getKey())), $notification->getTarget()->getProject()->getName()))); ?></h1>
						<div class="notification_content"><?php echo link_tag(make_url('viewissue', array('project_key' => $notification->getTarget()->getProject()->getKey(), 'issue_no' => $notification->getTarget()->getFormattedIssueNo())), $notification->getTarget()->getFormattedIssueNo(true, true)); ?> - <?php echo $notification->getTarget()->getTitle(); ?></div>
						<?php
						break;
					case TBGNotification::TYPE_ISSUE_UPDATED:
						?>
						<h1><?php echo __('%issue_no was updated by %user_name', array('%user_name' => get_component_html('main/userdropdown', array('user' => $notification->getTriggeredByUser())), '%issue_no' => link_tag(make_url('viewissue', array('project_key' => $notification->getTarget()->getProject()->getKey(), 'issue_no' => $notification->getTarget()->getFormattedIssueNo())), $notification->getTarget()->getFormattedIssueNo(true, true)))); ?></h1>
						<?php
						break;
					case TBGNotification::TYPE_ISSUE_COMMENTED:
						?>
						<h1><?php echo __('%user_name posted a %comment on %issue_no', array('%user_name' => get_component_html('main/userdropdown', array('user' => $notification->getTriggeredByUser())), '%comment' => link_tag(make_url('viewissue', array('project_key' => $notification->getTarget()->getTarget()->getProject()->getKey(), 'issue_no' => $notification->getTarget()->getTarget()->getFormattedIssueNo())).'#comment_'.$notification->getTarget()->getID(), __('%username_posted_a comment %on_issue', array('%username_posted_a' => '', '%on_issue' => ''))), '%issue_no' => link_tag(make_url('viewissue', array('project_key' => $notification->getTarget()->getTarget()->getProject()->getKey(), 'issue_no' => $notification->getTarget()->getTarget()->getFormattedIssueNo())), $notification->getTarget()->getTarget()->getFormattedIssueNo(true, true)))); ?></h1>
						<?php
						break;
					case TBGNotification::TYPE_ARTICLE_COMMENTED:
						break;
					case TBGNotification::TYPE_ARTICLE_UPDATED:
						break;
				}
			
			?>
			<time><?php echo tbg_formatTime($notification->getCreatedAt(), 20); ?></time>
		</li>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>
