<fieldset id="viewissue_vcs_integration_commits_container">
    <legend>
        <?php echo __('Commits (%count)', array('%count' => '<span id="viewissue_vcs_integration_commits_count">'.count($links).'</span>')); ?>
    </legend>
    <div id="viewissue_vcs_integration_commits">
        <?php if (count($links) == 0 || !is_array($links)): ?>
            <div class="no_items"><?php echo __('There are no code checkins for this issue'); ?></div>
        <?php else: ?>
            <?php foreach ($links as $link) include_component('vcs_integration/issuecommitbox', array("projectId" => $projectId, "commit" => $link->getCommit())); ?>
        <?php endif; ?>
    </div>
</fieldset>