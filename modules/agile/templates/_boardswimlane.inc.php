<?php

    use thebuggenie\modules\agile\entities\AgileBoard;

?>
<tbody class="<?php if (!count($issues)) echo 'collapsed'; ?>" data-swimlane-identifier="<?php echo $swimlane->getIdentifier(); ?>">
    <?php if ($swimlane->getBoard()->usesSwimlanes() && $swimlane->hasIdentifiables()): ?>
        <tr>
            <td colspan="<?php echo count($swimlane->getBoard()->getColumns()); ?>" class="swimlane-header">
                <div class="header">
                    <?php echo image_tag('icon-mono-expand.png', array('class' => 'expander', 'onclick' => "$(this).up('tbody').toggleClassName('collapsed');")); ?>
                    <?php echo image_tag('icon-mono-collapse.png', array('class' => 'collapser', 'onclick' => "$(this).up('tbody').toggleClassName('collapsed');")); ?>
                    <?php if ($swimlane->getBoard()->getSwimlaneType() == AgileBoard::SWIMLANES_ISSUES): ?>
                        <span class="issue_header <?php if ($swimlane->getIdentifierIssue()->isClosed()) echo 'closed'; ?>">
                            <?php echo link_tag(make_url('viewissue', array('issue_no' => $swimlane->getIdentifierIssue()->getFormattedIssueNo(), 'project_key' => $swimlane->getIdentifierIssue()->getProject()->getKey())), $swimlane->getIdentifierIssue()->getFormattedIssueNo(true, false), array('title' => $swimlane->getIdentifierIssue()->getFormattedTitle(), 'target' => '_new', 'class' => 'issue_header')); ?>
                            <?php echo $swimlane->getIdentifierIssue()->getTitle(); ?>
                        </span>
                    <?php else: ?>
                        <?php echo $swimlane->getName(); ?>
                    <?php endif; ?>
                    (<?php echo count($issues); ?>)
                </div>
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <?php foreach ($swimlane->getBoard()->getColumns() as $column): ?>
            <td class="column" id="swimlane_<?php echo $swimlane->getIdentifier(); ?>_column_<?php echo $column->getID(); ?>" data-column-id="<?php echo $column->getID(); ?>" data-swimlane-identifier="<?php echo $swimlane->getIdentifier(); ?>" data-status-ids="<?php echo join(',', $column->getStatusIds()); ?>">
                <?php foreach ($issues as $issue): ?>
                    <?php if ($column->hasIssue($issue)) include_component('agile/whiteboardissue', compact('issue', 'column', 'swimlane')); ?>
                <?php endforeach; ?>
                <br style="clear: both;">
            </td>
        <?php endforeach; ?>
    </tr>
</tbody>