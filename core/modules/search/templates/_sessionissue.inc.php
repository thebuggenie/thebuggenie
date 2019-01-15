<?php /** @var \thebuggenie\core\entities\Issue $issue */ ?>
<li>
    <a href="<?= make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?>" title="<?= $issue->getFormattedTitle(true); ?>">
        <?= $issue->getFormattedTitle(true, false); ?>
        <span class="additional_information">
            <span class="status_badge" style="background-color: <?php echo $issue->getStatus()->getColor(); ?>;color: <?php echo $issue->getStatus()->getTextColor(); ?>;"><span><?php echo __($issue->getStatus()->getName()); ?></span></span>
            <?php if ($issue->getPriority() instanceof \thebuggenie\core\entities\Priority): ?>
                <span class="priority priority_<?= $issue->getPriority()->getValue(); ?>"><?= fa_image_tag($issue->getPriority()->getFontAwesomeIcon(), [], $issue->getPriority()->getFontAwesomeIconStyle()) . $issue->getPriority()->getName(); ?></span>
            <?php endif; ?>
        </span>
    </a>
</li>
