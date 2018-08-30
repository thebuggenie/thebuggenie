<div class="backdrop_box medium" id="viewissue_move_issue_div">
    <div class="backdrop_detail_header">
        <span><?= __('Move issue to a different project'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <form action="<?= make_url('move_issue', array('issue_id' => $issue->getID())); ?>" method="post" <?php if (isset($multi) && $multi): ?>onsubmit="TBG.Issues.move($(this), <?= $issue->getID(); ?>);return false;"<?php endif; ?>>
        <div id="backdrop_detail_content" class="backdrop_detail_content">
            <input type="hidden" name="multi" value="<?= (int) (isset($multi) && $multi); ?>">
            <div class="rounded_box borderless yellow" style="margin: 5px 0 20px 0;">
                <p><?= __('Please be aware that moving this issue to a different project will reset details such as status, category, etc., and may also make some fields invisible, depending on the issue type configuration for that project. The issue will also be renumbered.'); ?></p>
            </div>
            <label for="move_issue_project"><?= __('Move issue to'); ?></label><br>
            <select name="project_id">
                <?php foreach (\thebuggenie\core\entities\Project::getAll() as $project): ?>
                    <?php if (!$project->hasAccess() || $project->isDeleted() || $project->isArchived() || !$tbg_user->canReportIssues($project) || $project->getID() == $issue->getProject()->getID()) continue; ?>
                    <option value="<?= $project->getID(); ?>"<?php if ($project->getID() == $issue->getProject()->getID()): ?> selected<?php endif; ?>><?= $project->getName(); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="backdrop_details_submit">
            <span class="explanation"></span>
            <div class="submit_container">
                <button type="submit" onclick="$('move_issue_indicator').show();"><?= image_tag('spinning_16.gif', array('id' => 'move_issue_indicator', 'style' => 'display: none;')) . __('Move issue'); ?></button>
            </div>
        </div>
    </form>
</div>
