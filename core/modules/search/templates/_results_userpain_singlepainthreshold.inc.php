<table style="width: 100%;" cellpadding="0" cellspacing="0" class="search_results resizable sortable">
    <thead>
        <tr>
            <th style="text-align: center; width: 50px;"><?php echo __('User pain'); ?></th>
            <th style="width: auto; padding-left: 2px;"><?php echo __('Issue'); ?></th>
            <th style="width: 200px; padding-right: 2px;"><?php echo __('Status'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php $first = true; ?>
    <?php foreach ($search_object->getIssues() as $issue): ?>
        <tr class="<?php if ($issue->hasUnsavedChanges()): ?> changed<?php endif; ?><?php if ($issue->isBlocking()): ?> blocking<?php endif; ?><?php if ($issue->getUserPain() <= $search_object->getTemplateParameter() && $first): $first = false; ?> userpain_below_threshold<?php endif; ?>">
            <td style="padding: 3px;">
                <div class="rounded_box <?php if ($issue->getUserPain() > $search_object->getTemplateParameter()): ?>red<?php else: ?>yellow<?php endif; ?> borderless" style="margin: 0 5px 0 0;<?php if ($issue->getIssueType()->getIcon() != 'bug_report'): ?>display: none;<?php endif; ?> vertical-align: middle; padding: 1px; text-align: center;">
                    <div class="user_pain"><?php echo $issue->getUserPain(); ?></div>
                </div>
            </td>
            <td class="result_issue"<?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?> style="padding-left: 3px;"<?php endif; ?>>
                <?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), '<span class="issue_no">' . $issue->getFormattedIssueNo(true) . '</span> - <span class="issue_title">' . $issue->getTitle() . '</span>', array('class' => 'issue_link')); ?>
            </td>
            <td<?php if (!$issue->getStatus() instanceof \thebuggenie\core\entities\Datatype): ?> class="faded_out"<?php endif; ?>>
                <?php if ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype): ?>
                    <table style="table-layout: auto; width: auto;" cellpadding=0 cellspacing=0 class="status_table">
                        <tr>
                            <td style="width: 24px;"><div style="border: 1px solid #AAA; background-color: <?php echo ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 20px; height: 15px; margin-right: 2px;">&nbsp;</div></td>
                            <td style="padding-left: 5px;"><?php echo $issue->getStatus()->getName(); ?></td>
                        </tr>
                    </table>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>