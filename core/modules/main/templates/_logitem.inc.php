<?php

    use thebuggenie\core\entities\LogItem,
        thebuggenie\core\entities\Commit,
        thebuggenie\core\entities\Issue;

    /** @var LogItem $item */

?>
<?php if ($item->getTargetType() == LogItem::TYPE_COMMIT && $item->getCommit() instanceof Commit): ?>
    <tr>
        <td class="imgtd"<?php if (!isset($include_issue_title) || $include_issue_title): ?> style="padding-top: <?php echo (isset($extra_padding) && $extra_padding) ? 10 : 3; ?>px;"<?php endif; ?>>
            <?php if (!isset($include_issue_title) || $include_issue_title): ?>
                <?php echo fa_image_tag('code-branch'); ?>
            <?php endif; ?>
        </td>
        <td style="clear: both;<?php if (!isset($include_issue_title) || $include_issue_title): ?> padding-bottom: <?php echo (isset($extra_padding) && $extra_padding) ? 15 : 10; ?>px;<?php endif; ?>">
            <?php if ((!isset($include_issue_title) || $include_issue_title) && (isset($include_time) && $include_time == true)): ?><span class="time"><?php echo tbg_formatTime($item->getTime(), 19); ?></span>&nbsp;<?php endif; ?>
            <?php if (!isset($include_issue_title) || $include_issue_title): ?>
                <?php echo link_tag(make_url('livelink_project_commit', ['commit_hash' => $item->getCommit()->getRevision(), 'project_key' => $item->getProject()->getKey()]), $title, ['style' => 'margin-top: 7px;'], $item->getCommit()->getTitle()); ?>
            <?php endif; ?>
            <?php if ((!isset($include_issue_title) || $include_issue_title) && (isset($include_user) && $include_user == true)): ?>
                <br>
                <span class="user">
                    <?php if ($item->getUser() instanceof \thebuggenie\core\entities\User): ?>
                        <?php if ($item->getChangeType() != LogItem::ACTION_COMMENT_CREATED): ?>
                            <?php echo $item->getUser()->getNameWithUsername().':'; ?>
                        <?php else: ?>
                            <?php echo __('%user said', array('%user' => $item->getUser()->getNameWithUsername())).':'; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($item->getChangeType() != LogItem::ACTION_COMMENT_CREATED): ?>
                            <span class="faded"><?php echo __('Unknown user').':'; ?></span>
                        <?php else: ?>
                            <?php echo __('Unknown user said').':'; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </span>
            <?php elseif (!isset($include_issue_title) || $include_issue_title): ?>
                <br>
            <?php endif; ?>
        </td>
    </tr>
<?php elseif ($item->getTargetType() == LogItem::TYPE_ISSUE && $item->getIssue() instanceof Issue && !($item->getIssue()->isDeleted()) && $item->getIssue()->hasAccess()): ?>
    <tr>
        <td class="imgtd"<?php if (!isset($include_issue_title) || $include_issue_title): ?> style="padding-top: <?php echo (isset($extra_padding) && $extra_padding) ? 10 : 3; ?>px;"<?php endif; ?>>
            <?php if (!isset($include_issue_title) || $include_issue_title): ?>
                <?php echo fa_image_tag($item->getIssue()->getIssueType()->getFontAwesomeIcon()); ?>
            <?php endif; ?>
        </td>
        <td style="clear: both;<?php if (!isset($include_issue_title) || $include_issue_title): ?> padding-bottom: <?php echo (isset($extra_padding) && $extra_padding) ? 15 : 10; ?>px;<?php endif; ?>">
            <?php if ((!isset($include_issue_title) || $include_issue_title) && (isset($include_time) && $include_time == true)): ?><span class="time"><?php echo tbg_formatTime($item->getTime(), 19); ?></span>&nbsp;<?php endif; ?>
            <?php if (!isset($include_issue_title) || $include_issue_title): ?>
                <?php if (isset($include_project) && $include_project == true): ?><span class="faded_out smaller"><?php echo image_tag($item->getIssue()->getProject()->getSmallIconName(), array('class' => 'issuelog-project-logo'), $item->getIssue()->getProject()->hasSmallIcon()); ?></span><?php endif; ?>
            <?php endif; ?>
            <?php 

                $title = tbg_decodeUTF8($item->getIssue()->getFormattedTitle(true));
                if (isset($pad_length))
                {
                    $title = tbg_truncateText($title, $pad_length);
                }
                
            ?>
            <?php if (!isset($include_issue_title) || $include_issue_title): ?>
                <?php echo link_tag(make_url('viewissue', array('project_key' => $item->getIssue()->getProject()->getKey(), 'issue_no' => $item->getIssue()->getFormattedIssueNo())), $title, array('class' => (($item->getChangeType() == LogItem::ACTION_ISSUE_CLOSE) ? 'issue_closed' : 'issue_open'), 'style' => 'margin-top: 7px;')); ?>
            <?php endif; ?>
            <?php if ((!isset($include_issue_title) || $include_issue_title) && (isset($include_user) && $include_user == true)): ?>
                <br>
                <span class="user">
                    <?php if ($item->getUser() instanceof \thebuggenie\core\entities\User): ?>
                        <?php if ($item->getChangeType() != LogItem::ACTION_COMMENT_CREATED): ?>
                            <?php echo $item->getUser()->getNameWithUsername().':'; ?>
                        <?php else: ?>
                            <?php echo __('%user said', array('%user' => $item->getUser()->getNameWithUsername())).':'; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($item->getChangeType() != LogItem::ACTION_COMMENT_CREATED): ?>
                            <span class="faded"><?php echo __('Unknown user').':'; ?></span>
                        <?php else: ?>
                            <?php echo __('Unknown user said').':'; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </span>
            <?php elseif (!isset($include_issue_title) || $include_issue_title): ?>
                <br>
            <?php endif; ?>
            <div style="line-height: 1.4; word-break: break-all; word-wrap: break-word; -ms-word-break: break-all; <?php if (!isset($include_issue_title) || $include_issue_title == false): ?>margin-top: -7px; margin-bottom: 10px;<?php endif; ?>">
            <?php

                switch ($item->getChangeType())
                {
                    case LogItem::ACTION_ISSUE_CREATED:
                        echo '<i>' . __('Issue created') . '</i>';
                        if (isset($include_details) && $include_details)
                        {
                            echo '<div class="timeline_inline_details">'.nl2br(__e(tbg_truncateText($item->getIssue()->getDescription(), 100))).'</div>';
                        }
                        break;
                    case LogItem::ACTION_COMMENT_CREATED:
                        $comment = \thebuggenie\core\entities\Comment::getB2DBTable()->selectById((int) $item->getText());
                        echo '<div class="timeline_inline_details">';
                        echo nl2br(tbg_truncateText(tbg_decodeUTF8(tbg_truncateText($comment->getContent(), 100))));
                        echo '</div>';
                        break;
                    case LogItem::ACTION_ISSUE_CLOSE:
                        echo '<span class="issue_closed"><i>' . __('Issue closed %text', array('%text' => $item->getText())) . '</i></span>';
                        break;
                    case LogItem::ACTION_ISSUE_REOPEN:
                        echo '<i>' . __('Issue reopened') . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_FREE_TEXT:
                        echo '<i>' . tbg_truncateText($item->getText(), 100) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_PAIN_BUG_TYPE:
                        echo '<i>' . __('Triaged bug type: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_PAIN_LIKELIHOOD:
                        echo '<i>' . __('Triaged likelihood: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_PAIN_EFFECT:
                        echo '<i>' . __('Triaged effect: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_PAIN_SCORE:
                        echo '<i>' . __('Calculated user pain: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_CATEGORY:
                        echo '<i>' . __('Category changed: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_CUSTOMFIELD:
                        echo '<i>' . __('Custom field changed: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_STATUS:
                        echo '<i>' . __('Status changed: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_REPRODUCABILITY:
                        echo '<i>' . __('Reproducability changed: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_PRIORITY:
                        echo '<i>' . __('Priority changed: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_SEVERITY:
                        echo '<i>' . __('Severity changed: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_RESOLUTION:
                        echo '<i>' . __('Resolution changed: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_PERCENT_COMPLETE:
                        echo '<i>' . __('Percent completed: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_MILESTONE:
                        echo '<i>' . __('Target milestone changed: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_ISSUETYPE:
                        echo '<i>' . __('Issue type changed: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_ESTIMATED_TIME:
                        if ($item->getPreviousValue() === NULL && $item->getCurrentValue() === NULL)
                        {
                            echo '<i>' . __('Estimation changed: %text', array('%text' => $item->getText())) . '</i>';
                        }
                        else
                        {
                            echo '<i>' . __('Estimation changed: %text', array('%text' => \thebuggenie\core\entities\common\Timeable::formatTimeableLog($item->getText(), $item->getPreviousValue(), $item->getCurrentValue(), true, true))) . '</i>';
                        }
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_TIME_SPENT:
                        if ($item->getPreviousValue() === NULL && $item->getCurrentValue() === NULL)
                        {
                            echo '<i>' . __('Time spent: %text', array('%text' => $item->getText())) . '</i>';
                        }
                        else
                        {
                            echo '<i>' . __('Time spent: %text', array('%text' => \thebuggenie\core\entities\common\Timeable::formatTimeableLog($item->getText(), $item->getPreviousValue(), $item->getCurrentValue(), true, true))) . '</i>';
                        }
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_ASSIGNEE:
                        echo '<i>' . __('Assignee changed: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_OWNER:
                        echo '<i>' . __('Owner changed: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    case LogItem::ACTION_ISSUE_UPDATE_POSTED_BY:
                        echo '<i>' . __('Posted by changed: %text', array('%text' => $item->getText())) . '</i>';
                        break;
                    default:
                        if (empty($item->getText()))
                        {
                            echo '<i>' .__('Issue updated') . '</i>';
                        }
                        else
                        {
                            echo '<i>' .$item->getText() . '</i>';
                        }
                        break;
                }

            ?>
            </div>
        </td>
    </tr>
<?php else: ?>
    <?php var_dump($item); ?>
<?php endif; ?>
