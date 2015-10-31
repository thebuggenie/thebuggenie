<div class="backdrop_box medium" id="viewissue_move_issue_div">
    <div class="backdrop_detail_header"><?php echo __('Move issue to a different project'); ?></div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <form action="<?php echo make_url('move_issue', array('issue_id' => $issue->getID())); ?>" method="post" <?php if (isset($multi) && $multi): ?>onsubmit="TBG.Issues.move($(this), <?php echo $issue->getID(); ?>);return false;"<?php endif; ?>>
            <input type="hidden" name="multi" value="<?php echo (int) (isset($multi) && $multi); ?>">
            <div class="rounded_box borderless yellow" style="margin: 5px 0 20px 0;">
                <p><?php echo __('Please be aware that moving this issue to a different project will reset details such as status, category, etc., and may also make some fields invisible, depending on the issue type configuration for that project. The issue will also be renumbered.'); ?></p>
            </div>
            <label for="move_issue_project"><?php echo __('Move issue to'); ?></label><br>
            <select name="project_id">
                <?php foreach (\thebuggenie\core\entities\Project::getAll() as $project): ?>
                    <?php if (!$project->hasAccess() || $project->isDeleted() || $project->isArchived() || !$tbg_user->canReportIssues($project) || $project->getID() == $issue->getProject()->getID()) continue; ?>
                    <option value="<?php echo $project->getID(); ?>"<?php if ($project->getID() == $issue->getProject()->getID()): ?> selected<?php endif; ?>><?php echo $project->getName(); ?></option>
                <?php endforeach; ?>
            </select>
            <div style="text-align: right; padding-top: 5px;">
                <input type="submit" value="<?php echo __('Move issue'); ?>" onclick="$('move_issue_indicator').show();">
                <?php echo image_tag('spinning_16.gif', array('id' => 'move_issue_indicator', 'style' => 'display: none; margin-right: 5px;')); ?>
                <?php echo __('%move_issue or %cancel', array('%move_issue' => '', '%cancel' => '')); ?>
                <a href="javascript:void(0)" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('cancel'); ?></a>
            </div>
        </form>
    </div>
    <div class="backdrop_detail_footer">
    </div>
</div>
