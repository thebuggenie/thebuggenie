<?php if ($matched_issues): ?>
    <div class="header_div" style="margin-bottom: 5px;"><?= __('The following issues matched your search'); ?>:</div>
    <input type="hidden" name="issue_action" value="duplicate">
    <select name="duplicate_issue_id" style="width: 100%">
        <?php foreach ($matched_issues as $matched_issue): ?>
            <option value="<?= $matched_issue->getID(); ?>">[<?= ($matched_issue->isOpen()) ? __('Open') : __('Closed'); ?>] <?= $matched_issue->getFormattedTitle(); ?></option>
        <?php endforeach; ?>
    </select>
<?php else: ?>
    <span class="faded_out"><?= __('No issues matched your search. Please try again with different search terms.'); ?></span>
<?php endif; ?>
