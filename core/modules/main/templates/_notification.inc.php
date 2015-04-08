<li class="<?php echo ($notification->isRead()) ? 'read' : 'unread'; ?>" id="notification_<?php echo $notification->getID(); ?>_container">
    <a href="javascript:void(0);" onclick="TBG.Main.Notifications.toggleRead(<?php echo $notification->getID(); ?>);" class="notification_status_toggler">
        <?php echo image_tag('icon_notification_read.png', array('class' => 'icon_read')); ?>
        <?php echo image_tag('icon_notification_unread.png', array('class' => 'icon_unread')); ?>
    </a>
    <?php

        switch ($notification->getNotificationType())
        {
            case \thebuggenie\core\entities\Notification::TYPE_ISSUE_CREATED:
                ?>
                <h1>
                    <time><?php echo tbg_formatTime($notification->getCreatedAt(), 20); ?></time>
                    <?php echo __('%user_name created a new issue under %project_name', array('%user_name' => get_component_html('main/userdropdown', array('user' => $notification->getTarget()->getPostedBy())), '%project_name' => link_tag(make_url('project_dashboard', array('project_key' => $notification->getTarget()->getProject()->getKey())), $notification->getTarget()->getProject()->getName()))); ?>
                </h1>
                <div class="notification_content"><?php echo link_tag(make_url('viewissue', array('project_key' => $notification->getTarget()->getProject()->getKey(), 'issue_no' => $notification->getTarget()->getFormattedIssueNo())), $notification->getTarget()->getFormattedIssueNo(true, true)); ?> - <?php echo $notification->getTarget()->getTitle(); ?></div>
                <?php
                break;
            case \thebuggenie\core\entities\Notification::TYPE_ISSUE_UPDATED:
                ?>
                <h1>
                    <time><?php echo tbg_formatTime($notification->getCreatedAt(), 20); ?></time>
                    <?php echo __('%issue_no was updated by %user_name', array('%user_name' => get_component_html('main/userdropdown', array('user' => $notification->getTriggeredByUser())), '%issue_no' => link_tag(make_url('viewissue', array('project_key' => $notification->getTarget()->getProject()->getKey(), 'issue_no' => $notification->getTarget()->getFormattedIssueNo())), $notification->getTarget()->getFormattedIssueNo(true, true)))); ?>
                </h1>
                <?php
                break;
            case \thebuggenie\core\entities\Notification::TYPE_ISSUE_COMMENTED:
                ?>
                <h1>
                    <time><?php echo tbg_formatTime($notification->getCreatedAt(), 20); ?></time>
                    <?php echo __('%user_name posted a %comment on %issue_no', array('%user_name' => get_component_html('main/userdropdown', array('user' => $notification->getTriggeredByUser())), '%comment' => link_tag(make_url('viewissue', array('project_key' => $notification->getTarget()->getTarget()->getProject()->getKey(), 'issue_no' => $notification->getTarget()->getTarget()->getFormattedIssueNo())).'#comment_'.$notification->getTarget()->getID(), __('%username_posted_a comment %on_issue', array('%username_posted_a' => '', '%on_issue' => ''))), '%issue_no' => link_tag(make_url('viewissue', array('project_key' => $notification->getTarget()->getTarget()->getProject()->getKey(), 'issue_no' => $notification->getTarget()->getTarget()->getFormattedIssueNo())), $notification->getTarget()->getTarget()->getFormattedIssueNo(true, true)))); ?>
                </h1>
                <?php
                break;
            case \thebuggenie\core\entities\Notification::TYPE_COMMENT_MENTIONED:
                if ($notification->getTarget()->getTargetType() == \thebuggenie\core\entities\Comment::TYPE_ISSUE): ?>
                    <h1>
                        <time><?php echo tbg_formatTime($notification->getCreatedAt(), 20); ?></time>
                        <?php echo __('%user_name mentioned you in a %comment on %issue_no', array('%user_name' => get_component_html('main/userdropdown', array('user' => $notification->getTriggeredByUser())), '%comment' => link_tag(make_url('viewissue', array('project_key' => $notification->getTarget()->getTarget()->getProject()->getKey(), 'issue_no' => $notification->getTarget()->getTarget()->getFormattedIssueNo())).'#comment_'.$notification->getTarget()->getID(), __('%username_posted_a comment %on_issue', array('%username_posted_a' => '', '%on_issue' => ''))), '%issue_no' => link_tag(make_url('viewissue', array('project_key' => $notification->getTarget()->getTarget()->getProject()->getKey(), 'issue_no' => $notification->getTarget()->getTarget()->getFormattedIssueNo())), $notification->getTarget()->getTarget()->getFormattedIssueNo(true, true)))); ?>
                    </h1>
                <?php else: ?>
                    <h1>
                        <time><?php echo tbg_formatTime($notification->getCreatedAt(), 20); ?></time>
                        <?php echo __('%user_name mentioned you in a %comment on %article_name', array('%user_name' => get_component_html('main/userdropdown', array('user' => $notification->getTriggeredByUser())), '%comment' => link_tag(make_url('publish_article', array('article_name' => $notification->getTarget()->getTarget()->getName())).'#comment_'.$notification->getTarget()->getID(), __('%username_posted_a comment %on_issue', array('%username_posted_a' => '', '%on_issue' => ''))), '%article_name' => link_tag(make_url('publish_article', array('article_name' => $notification->getTarget()->getTarget()->getName())), $notification->getTarget()->getTarget()->getName()))); ?>
                    </h1>
                <?php endif; ?>
                <?php
                break;
            case \thebuggenie\core\entities\Notification::TYPE_ARTICLE_COMMENTED:
                ?>
                <h1>
                    <time><?php echo tbg_formatTime($notification->getCreatedAt(), 20); ?></time>
                    <?php echo __('%user_name posted a %comment on %article_name', array('%user_name' => get_component_html('main/userdropdown', array('user' => $notification->getTriggeredByUser())), '%comment' => link_tag(make_url('publish_article', array('article_name' => $notification->getTarget()->getTarget()->getName())).'#comment_'.$notification->getTarget()->getID(), __('%username_posted_a comment %on_issue', array('%username_posted_a' => '', '%on_issue' => ''))), '%article_name' => link_tag(make_url('publish_article', array('article_name' => $notification->getTarget()->getTarget()->getName())), $notification->getTarget()->getTarget()->getName()))); ?>
                </h1>
                <?php
                break;
            case \thebuggenie\core\entities\Notification::TYPE_ARTICLE_UPDATED:
                ?>
                <h1>
                    <time><?php echo tbg_formatTime($notification->getCreatedAt(), 20); ?></time>
                    <?php echo __('%user_name updated %article_name', array('%user_name' => get_component_html('main/userdropdown', array('user' => $notification->getTriggeredByUser())), '%article_name' => link_tag(make_url('publish_article', array('article_name' => $notification->getTarget()->getTarget()->getName())), $notification->getTarget()->getTarget()->getName()))); ?>
                </h1>
                <?php
                break;
            default:
                \thebuggenie\core\framework\Event::createNew('core', '_notification_view', $notification)->trigger();
        }

    ?>
</li>
