<?php if ($grouped_issues): ?>

    <div class="header_div"><?= __('The following issues matched your search'); ?>:</div>
    <span class="faded_out"><?= __('Either use the checkboxes and press the "%relate_these_issues"-button below or click any issues in the list, and select an action.', array('%relate_these_issues' => __('Relate these issues'))); ?></span>
    <form id="viewissue_relate_issues_form" action="<?= make_url('viewissue_relate_issues', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>" method="post" accept-charset="<?= \thebuggenie\core\framework\Settings::getCharset(); ?>" onsubmit="TBG.Issues.relate('<?= make_url('viewissue_relate_issues', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>');return false;">

        <div style="height: 400px; overflow: auto">
            <?php foreach($grouped_issues as $project => $matched_issues): ?>
                <?php if ($issue->getProject()->getName() == $project): ?>
                    <div class="header_div smaller"><?= __('Current project') ?></div>
                <?php else: ?>
                    <div class="header_div smaller"><?= $project ?></div>
                <?php endif; ?>

                <table style="width: auto; border: 0;" cellpadding="0" cellspacing="0">

                    <?php foreach($matched_issues as $matched_issue): ?>
                        <tr>
                            <td style="width: 20px;"><input type="checkbox" value="<?= $matched_issue->getID(); ?>" name="relate_issues[<?= $matched_issue->getID(); ?>]" id="relate_issue_<?= $matched_issue->getID(); ?>"></td>
                            <td class="issue_title">
                                <label for="relate_issue_<?= $matched_issue->getID(); ?>" style="font-weight: normal;">[<?= __($matched_issue->getStateAsText()); ?>] <?= $matched_issue->getFormattedTitle(); ?></label>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </table>
            <?php endforeach; ?>
        </div>

        <p><label><input type="radio" id="relate_issue_with_selected" name="relate_action" checked="checked" value="relate_children"> <?= __('Add checked issues as children'); ?></label></p>
        <p><label><input type="radio" id="relate_issue_with_selected" name="relate_action" value="relate_parent"> <?= __('Set selected issue as parent'); ?></label></p>
        <br>
        <div style="text-align: right; border-top: 1px dotted #CCC; padding-top: 5px;">
            <input type="submit" value="<?= __('Relate these issues'); ?>">
            <?= image_tag('spinning_20.gif', array('id' => 'relate_issues_indicator', 'style' => 'display: none;')); ?><br>
        </div>
    </form>

<?php else: ?>
    <span class="faded_out"><?= __('No issues matched your search. Please try again with different search terms.'); ?></span>
<?php endif; ?>
