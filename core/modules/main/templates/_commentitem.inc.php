<?php if (isset($issue) && $issue instanceof \thebuggenie\core\entities\Issue): ?>
    <tr>
        <td class="imgtd"><?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
        <td style="padding-bottom: <?php if (isset($extra_padding) && $extra_padding == true): ?>20<?php else: ?>15<?php endif; ?>px;">
            <?php if (isset($include_time) && $include_time == true): ?><span class="time"><?php echo tbg_formatTime($comment->getPosted(), 19); ?></span>&nbsp;<?php endif; ?>
            <?php if (isset($include_project) && $include_project == true): ?><span class="faded_out smaller"><?php echo image_tag($issue->getProject()->getSmallIconName(), array('class' => 'issuelog-project-logo'), $issue->getProject()->hasSmallIcon()); ?></span><?php endif; ?>
            <?php
                $issue_title = tbg_decodeUTF8($issue->getFormattedTitle(true));
                if (isset($pad_length))
                {
                    $issue_title = tbg_truncateText($issue_title, $pad_length);
                }            
            ?>
            <?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue_title, array('class' => $issue->isClosed() ? 'issue_closed' : 'issue_open')); ?>
            <br>
            <span class="user">
                <?php if (($user = $comment->getPostedBy()) instanceof \thebuggenie\core\entities\User): ?>
                    <?php echo __('%buddy_name (%username) said'.':', array('%username' => $user->getUsername(), '%buddy_name' => $user->getBuddyname())); ?>
                <?php else: ?>
                    <?php echo __('Unknown user said').':'; ?>
                <?php endif; ?>
            </span>
            <?php
                echo '<div class="timeline_inline_details">';
                echo nl2br(tbg_truncateText(tbg_decodeUTF8($comment->getContent())));
                echo '</div>';
            ?>
        </td>
    </tr>
<?php endif; ?>
