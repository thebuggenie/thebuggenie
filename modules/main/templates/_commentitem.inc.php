<?php if (isset($issue) && $issue instanceof TBGIssue): ?>
	<tr>
		<td class="imgtd"><?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
		<td style="padding-bottom: <?php if (isset($extra_padding) && $extra_padding == true): ?>10<?php else: ?>5<?php endif; ?>px;">
			<?php if (isset($include_time) && $include_time == true): ?><span class="time"><?php echo tbg_formatTime($comment->getPosted(), 19); ?></span>&nbsp;<?php endif; ?>
			<?php if (isset($include_project) && $include_project == true): ?><span class="faded_out smaller"><?php echo link_tag(make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey())), '['.$issue->getProject()->getKey().']'); ?></span><?php endif; ?>
			<?php 
				$issue_title = $issue->getFormattedTitle(true);
				if (isset($pad_length))
				{
					$issue_title = tbg_truncateText($issue_title, $pad_length);
				}			
			?>
			<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue_title, array('class' => 'issue_open')); ?>
			<br>
			<span class="user">
				<?php if (($user = $comment->getPostedBy()) instanceof TBGUser): ?>
					<?php echo __('%username% (%buddy_name%) said', array('%username%' => $user->getUsername(), '%buddy_name%' => $user->getBuddyname())); ?>
				<?php else: ?>
					<?php echo __('Unknown user said'); ?>
				<?php endif; ?>:
			</span>
			<?php
				echo '<div class="timeline_inline_details">';
				$max_lenght = 300;
				$content = (strlen($comment->getContent()) > $max_lenght) ? substr($comment->getContent(), 0, $max_lenght) . '...' : $comment->getContent();
				echo nl2br($content);
				echo '</div>';
			?>
		</td>
	</tr>
<?php endif; ?>