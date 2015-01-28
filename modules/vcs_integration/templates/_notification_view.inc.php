<?php

    switch ($notification->getNotificationType())
    {
        case \thebuggenie\modules\vcs_integration\Vcs_integration::NOTIFICATION_COMMIT_MENTIONED:
            ?>
            <h1>
                <time><?php echo tbg_formatTime($notification->getCreatedAt(), 20); ?></time>
                <?php echo __('%user_name mentioned you in commit %rev', array(
                    '%user_name' => get_component_html('main/userdropdown', array('user' => $notification->getTriggeredByUser())),
                    '%rev' => javascript_link_tag($notification->getTarget()->getRevisionString(), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'vcs_integration_getcommit', 'commit_id' => $notification->getTarget()->getID()))."');"))
                    )); ?>
            </h1>
            <div class="notification_content"><?php echo $notification->getTarget()->getLog(); ?></div>
            <?php
            break;
    }

?>
