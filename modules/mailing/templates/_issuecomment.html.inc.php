<?php if ($issue instanceof \thebuggenie\core\entities\Issue && $comment instanceof \thebuggenie\core\entities\Comment): ?>
    <h3>
        <?php echo $issue->getFormattedTitle(true); ?><br>
        <span style="font-size: 0.8em; font-weight: normal;"><?php echo __('Created by %name', array('%name' => $issue->getPostedBy()->getNameWithUsername())); ?></span>
    </h3>
    <br>
    <h4><?php echo __('Comment by %name', array('%name' => $comment->getPostedBy()->getNameWithUsername()));?></h4>
    <p><?php echo $comment->getParsedContent(); ?></p>
    <br>
    <div style="color: #888;">
        <?php echo __('Show issue:') . ' ' . link_tag($module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()))); ?><br>
        <?php echo __('Show comment:') . ' ' . link_tag($module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())).'#comment_'.$comment->getID()); ?><br>
        <?php echo __('Show %project project dashboard:', array('%project' => $issue->getProject()->getName())) . ' ' . link_tag($module->generateURL('project_dashboard', array('project_key' => $issue->getProject()->getKey()))); ?><br>
        <br>
        <?php echo __('You were sent this notification email because you are related to the issue mentioned in this email.'); ?><br>
        <?php echo __('To change when and how often we send these emails, update your account settings:') . ' ' . link_tag($module->generateURL('account'), $module->generateURL('account')); ?>
    </div>
<?php endif; ?>
