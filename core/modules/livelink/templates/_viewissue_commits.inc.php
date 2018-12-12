<?php

/**
 * @var \thebuggenie\core\entities\Commit[] $commits
 */

?>
<div id="tab_viewissue_commits_pane" style="display:none;">
    <fieldset id="viewissue_livelink_commits_container">
        <div id="viewissue_livelink_commits" class="commits-list">
            <?php if (count($commits) == 0 || !is_array($commits)): ?>
                <div class="no_items"><?php echo __('There are no code checkins for this issue'); ?></div>
            <?php else: ?>
                <?php include_component('livelink/issuecommits', array("project" => $selected_project, "commits" => $commits)); ?>
            <?php endif; ?>
        </div>
    </fieldset>
</div>
