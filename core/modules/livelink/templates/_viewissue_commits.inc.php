<div id="tab_viewissue_commits_pane" style="display:none;">
    <fieldset id="viewissue_vcs_integration_commits_container">
        <div id="viewissue_vcs_integration_commits">
            <?php if (count($links) == 0 || !is_array($links)): ?>
                <div class="no_items"><?php echo __('There are no code checkins for this issue'); ?></div>
            <?php else: ?>
                <?php include_component('vcs_integration/issuecommits', array("projectId" => $selected_project->getID(), "links" => $links)); ?>
            <?php endif; ?>
        </div>
    </fieldset>
</div>
