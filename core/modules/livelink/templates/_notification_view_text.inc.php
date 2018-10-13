<?php

    switch ($notification->getNotificationType())
    {
        case \thebuggenie\modules\vcs_integration\Vcs_integration::NOTIFICATION_COMMIT_MENTIONED:
            ?>
                <?php echo tbg_formatTime($notification->getCreatedAt(), 20); ?>
                <?php echo __('%user_name mentioned you in commit %rev', array(
                    '%user_name' => get_component_html('main/userdropdown_inline.text', array('user' => $notification->getTriggeredByUser())),
                    '%rev' => $notification->getTarget()->getRevisionString()
                    )); ?>
            <?php echo $notification->getTarget()->getLog(); ?>
            <?php
            break;
    }

?>
