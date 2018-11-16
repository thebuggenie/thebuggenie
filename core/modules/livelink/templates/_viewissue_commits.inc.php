<div id="tab_viewissue_commits_pane" style="display:none;">
    <fieldset id="viewissue_livelink_commits_container">
        <div id="viewissue_livelink_commits">
            <?php if (count($links) == 0 || !is_array($links)): ?>
                <div class="no_items"><?php echo __('There are no code checkins for this issue'); ?></div>
            <?php else: ?>
                <?php include_component('livelink/issuecommits', array("projectId" => $selected_project->getID(), "links" => $links)); ?>
            <?php endif; ?>
        </div>
    </fieldset>
</div>
