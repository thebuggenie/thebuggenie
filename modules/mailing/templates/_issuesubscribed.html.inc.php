<?php if ($issue instanceof \thebuggenie\core\entities\Issue): ?>
    <h3>
        <?php echo $issue->getFormattedTitle(true); ?><br>
        <span style="font-size: 0.8em; font-weight: normal;"><?php echo __('Created by %name', array('%name' => $issue->getPostedBy()->getNameWithUsername())); ?></span>
    </h3>
    <?php echo __('You have been subscribed to this issue and will be notified if and when it changes in the future.'); ?><br>
    <?php echo __('To unsubscribe from this issue, open the issue in your web browser and click the "star" icon in the top left corner, next to the issue title.'); ?>
    <br>
    <br>
    <div style="color: #888;">
        <?php echo __('Show issue:') . ' ' . link_tag($module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()))); ?><br>
        <?php echo __('Show %project project dashboard:', array('%project' => $issue->getProject()->getName())) . ' ' . link_tag($module->generateURL('project_dashboard', array('project_key' => $issue->getProject()->getKey()))); ?><br>
        <br>
        <?php echo __('You were sent this notification email because you are related to the issue mentioned in this email.'); ?><br>
        <?php echo __('To change when and how often we send these emails, update your account settings:') . ' ' . link_tag($module->generateURL('account'), $module->generateURL('account')); ?>
    </div>
<?php endif; ?>