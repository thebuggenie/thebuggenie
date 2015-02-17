<?php if (isset($issue) && $issue instanceof \thebuggenie\core\entities\Issue && !($issue->isDeleted()) && $issue->hasAccess()): ?>
    <tr>
        <td class="imgtd"<?php if (!isset($include_issue_title) || $include_issue_title): ?> style="padding-top: <?php echo (isset($extra_padding) && $extra_padding) ? 10 : 3; ?>px;"<?php endif; ?>>
            <?php if (!isset($include_issue_title) || $include_issue_title): ?>
                <?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png', array('style' => 'margin-top: 7px;')); ?>
            <?php endif; ?>
        </td>
        <td style="clear: both;<?php if (!isset($include_issue_title) || $include_issue_title): ?> padding-top: <?php echo (isset($extra_padding) && $extra_padding) ? 10 : 3; ?>px;<?php endif; ?>">
            <?php if ((!isset($include_issue_title) || $include_issue_title) && (isset($include_time) && $include_time == true)): ?><span class="time"><?php echo tbg_formatTime($log_action['timestamp'], 19); ?></span>&nbsp;<?php endif; ?>
            <?php if (!isset($include_issue_title) || $include_issue_title): ?>
                <?php if (isset($include_project) && $include_project == true): ?><span class="faded_out smaller"><?php echo link_tag(make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey())), '['.$issue->getProject()->getKey().']'); ?></span><?php endif; ?>
            <?php endif; ?>
            <?php 

                $issue_title = tbg_decodeUTF8($issue->getFormattedTitle(true));
                if (isset($pad_length))
                {
                    $issue_title = tbg_truncateText($issue_title, $pad_length);
                }
                
            ?>
            <?php if (!isset($include_issue_title) || $include_issue_title): ?>
                <?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue_title, array('class' => (($log_action['change_type'] == \thebuggenie\core\entities\tables\Log::LOG_ISSUE_CLOSE) ? 'issue_closed' : 'issue_open'), 'style' => 'margin-top: 7px;')); ?>
            <?php endif; ?>
            <?php if ((!isset($include_issue_title) || $include_issue_title) && (isset($include_user) && $include_user == true)): ?>
                <br>
                <span class="user">
                    <?php if (($user = \thebuggenie\core\entities\User::getB2DBTable()->selectById($log_action['user_id'])) instanceof \thebuggenie\core\entities\User): ?>
                        <?php if ($log_action['change_type'] != \thebuggenie\core\entities\tables\Log::LOG_COMMENT): ?>
                            <?php echo $user->getNameWithUsername(); ?>
                        <?php else: ?>
                            <?php echo __('%user said', array('%user' => $user->getNameWithUsername())); ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($log_action['change_type'] != \thebuggenie\core\entities\tables\Log::LOG_COMMENT): ?>
                            <span class="faded"><?php echo __('Unknown user'); ?></span>
                        <?php else: ?>
                            <?php echo __('Unknown user said'); ?>
                        <?php endif; ?>
                    <?php endif; ?>:
                </span>
            <?php elseif (!isset($include_issue_title) || $include_issue_title): ?>
                <br>
            <?php endif; ?>
            <?php

                switch ($log_action['change_type'])
                {
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_CREATED:
                        echo '<i>' . __('Issue created') . '</i>';
                        if (isset($include_details) && $include_details)
                        {
                            echo '<div class="timeline_inline_details">'.nl2br(__e($issue->getDescription())).'</div>';
                        }
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_COMMENT:
                        $comment = \thebuggenie\core\entities\Comment::getB2DBTable()->selectById((int) $log_action['text']);
                        echo '<div class="timeline_inline_details">';
                        echo nl2br(tbg_truncateText(tbg_decodeUTF8($comment->getContent())));
                        echo '</div>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_CLOSE:
                        echo '<span class="issue_closed"><i>' . __('Issue closed %text', array('%text' => $log_action['text'])) . '</i></span>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_REOPEN:
                        echo '<i>' . __('Issue reopened') . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_UPDATE:
                        echo '<i>' . $log_action['text'] . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_PAIN_BUG_TYPE:
                        echo '<i>' . __('Triaged bug type: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_PAIN_LIKELIHOOD:
                        echo '<i>' . __('Triaged likelihood: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_PAIN_EFFECT:
                        echo '<i>' . __('Triaged effect: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_PAIN_CALCULATED:
                        echo '<i>' . __('Calculated user pain: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_CATEGORY:
                        echo '<i>' . __('Category changed: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_CUSTOMFIELD_CHANGED:
                        echo '<i>' . __('Custom field changed: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_STATUS:
                        echo '<i>' . __('Status changed: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_REPRODUCABILITY:
                        echo '<i>' . __('Reproducability changed: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_PRIORITY:
                        echo '<i>' . __('Priority changed: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_SEVERITY:
                        echo '<i>' . __('Severity changed: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_RESOLUTION:
                        echo '<i>' . __('Resolution changed: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_PERCENT:
                        echo '<i>' . __('Percent completed: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_MILESTONE:
                        echo '<i>' . __('Target milestone changed: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_ISSUETYPE:
                        echo '<i>' . __('Issue type changed: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_TIME_ESTIMATED:
                        echo '<i>' . __('Estimation changed: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_TIME_SPENT:
                        echo '<i>' . __('Time spent: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_ASSIGNED:
                        echo '<i>' . __('Assignee changed: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_OWNED:
                        echo '<i>' . __('Owner changed: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    case \thebuggenie\core\entities\tables\Log::LOG_ISSUE_POSTED:
                        echo '<i>' . __('Posted by changed: %text', array('%text' => $log_action['text'])) . '</i>';
                        break;
                    default:
                        if (empty($log_action['text']))
                        {
                            echo '<i>' .__('Issue updated') . '</i>';
                        }
                        else
                        {
                            echo '<i>' .$log_action['text'] . '</i>';
                        }
                        break;
                }

            ?>
        </td>
    </tr>
<?php endif; ?>
