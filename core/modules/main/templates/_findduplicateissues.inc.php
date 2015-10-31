<?php if ($count > 0): ?>
    <div class="header_div" style="margin-bottom: 5px;"><?php echo __('The following issues matched your search'); ?>:</div>
    <input type="hidden" name="issue_action" value="duplicate">
    <select name="duplicate_issue_id" style="width: 100%">
        <?php foreach ($issues as $anIssue): ?>
            <?php if (isset($issue) && $issue instanceof \thebuggenie\core\entities\Issue && $anIssue->getID() == $issue->getID()): continue; endif; ?>
            <option value="<?php echo $anIssue->getID(); ?>">[<?php if ($anIssue->getState() == \thebuggenie\core\entities\Issue::STATE_OPEN): echo __('OPEN'); else: echo __('CLOSED'); endif; ?>] <?php echo $anIssue->getFormattedTitle(); ?></option>
        <?php endforeach; ?>
    </select>
<?php else: ?>
    <span class="faded_out"><?php echo __('No issues matched your search. Please try again with different search terms.'); ?></span>
<?php endif; ?>
