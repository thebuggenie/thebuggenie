<?php
if ($commits == false)
{
  ?>
  <p class="faded_out"><?php echo __('No commits have been found for this project'); ?></p>
  <?php
}
else
{
  ?>
  <div class="project_commits_box">
    <div id="commits">
      <?php include_component('livelink/projectcommits', array('selected_project' => $selected_project, 'commits' => $commits, 'branch' => $branch)); ?>
    </div>

    <div class="commits_next">
      <input id="commits_offset" value="40" type="hidden">
      <?php echo image_tag('spinning_16.gif', array('id' => 'commits_indicator', 'style' => 'display: none; float: left; margin-right: 5px;')); ?>
      <?php //echo javascript_link_tag(__('Show more').image_tag('action_add_small.png', array('style' => 'float: left; margin-right: 5px;')), array('onclick' => "TBG.Project.Commits.update('".make_url('vcs_commitspage_more', array('project_key' => $selected_project->getKey()))."'".(is_null($branchname) ? ", undefined" : ", '".$branchname."'" ).(is_null($gitlab_repos_nss) ? ", undefined" : ", '".$gitlab_repos_nss."'" ).");", 'id' => 'commits_more_link')); ?>
    </div>
  </div>
  <?php
}
?>
