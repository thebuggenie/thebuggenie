<?php use thebuggenie\core\entities\LogItem;

if ($issue instanceof \thebuggenie\core\entities\Issue): ?>
    <h3>
        <?php echo $issue->getFormattedTitle(true); ?><br>
        <span style="font-size: 0.8em; font-weight: normal;"><?php echo __('Updated by %name', array('%name' => $updated_by->getNameWithUsername())); ?></span><br>
        <span style="font-size: 0.8em; color: #AAA; font-weight: normal;"><?php echo __('Created by %name', array('%name' => $issue->getPostedBy()->getNameWithUsername())); ?></span>
    </h3>
    <?php if (isset($comment) && $comment instanceof \thebuggenie\core\entities\Comment): ?>
        <h4><?php echo __('Comment by %name', array('%name' => $comment->getPostedBy()->getNameWithUsername())); ?></h4>
        <p><?php echo $comment->getParsedContent(); ?></p>
        <br>
    <?php endif; ?>
    <?php if (count($log_items)): ?>
        <h4><?php echo __('Changes'); ?></h4>
        <ul>
            <?php foreach ($log_items as $item): ?>
                <li>
                <?php

                    switch ($item->getChangeType())
                    {
                        case LogItem::ACTION_ISSUE_CREATED:
                        case LogItem::ACTION_COMMENT_CREATED:
                            break;
                        case LogItem::ACTION_ISSUE_CLOSE:
                            echo '<i>' . __('Issue closed') . '</i>';
                            break;
                        case LogItem::ACTION_ISSUE_REOPEN:
                            echo '<i>' . __('Issue reopened') . '</i>';
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_FREE_TEXT:
                            echo '<i>' . $item->getText() . '</i>';
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
                            echo '<i>' . __('Estimation changed: %text', array('%text' => \thebuggenie\core\entities\common\Timeable::formatTimeableLog($item->getText(), $item->getPreviousValue(), $item->getCurrentValue(), true, true))) . '</i>';
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_TIME_SPENT:
                            echo '<i>' . __('Time spent: %text', array('%text' => \thebuggenie\core\entities\common\Timeable::formatTimeableLog($item->getText(), $item->getPreviousValue(), $item->getCurrentValue(), true, true))) . '</i>';
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
                            if (!$item->getText())
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
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <br>
    <div style="color: #888;">
        <?php echo __('Show issue:') . ' ' . link_tag($module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()))); ?><br>
        <?php if (isset($comment) && $comment instanceof \thebuggenie\core\entities\Comment){echo __('Show comment:') . ' ' . link_tag($module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())).'#comment_'.$comment->getID()) . "<br>";} ?>
        <?php echo __('Show %project project dashboard:', array('%project' => $issue->getProject()->getName())) . ' ' . link_tag($module->generateURL('project_dashboard', array('project_key' => $issue->getProject()->getKey()))); ?><br>
        <br>
        <?php echo __('You were sent this notification email because you are related to, subscribed to, or commented on the issue mentioned in this email.');?><br>
        <?php echo __('Depending on your notification settings, you may or may not be notified again when this issue is updated in the future.');?><br>
        <?php echo __('To change when and how often we send these emails, update your account settings:') . ' ' . link_tag($module->generateURL('account'), $module->generateURL('account')); ?>
    </div>
<?php endif; ?>
