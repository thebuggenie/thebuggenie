<fieldset id="viewissue_vcs_integration_commits_container">
    <legend>
        <?php echo __('Commits (%count)', array('%count' => '<span id="viewissue_vcs_integration_commits_count">'.$links_total_count.'</span>')); ?>
    </legend>
    <div id="viewissue_vcs_integration_commits">
        <?php if (count($links) == 0 || !is_array($links)): ?>
            <div class="no_items"><?php echo __('There are no code checkins for this issue'); ?></div>
        <?php else: ?>
            <?php include_component('vcs_integration/issuecommits', array("projectId" => $selected_project->getID(), "links" => $links)); ?>
        <?php endif; ?>
    </div>
    <?php if ($links_total_count > 3): ?>
        <div class="commits_next">
            <input id="commits_offset" value="3" type="hidden">
            <input id="commits_limit" value="<?php echo $links_total_count; ?>" type="hidden">
            <?php echo image_tag('spinning_16.gif', array('id' => 'commits_indicator', 'style' => 'display: none; float: left; margin-right: 5px;')); ?>
            <?php echo javascript_link_tag(__('Show all').image_tag('action_add_small.png', array('style' => 'float: left; margin-right: 5px;')), array('onclick' => "TBG.Project.Commits.viewIssueUpdate('".make_url('vcs_viewissue_more', array('project_key' => $selected_project->getKey(), 'issue_no' => $issue->getIssueNo()))."');", 'id' => 'commits_more_link')); ?>
        </div>
    <?php endif; ?>
</fieldset>
