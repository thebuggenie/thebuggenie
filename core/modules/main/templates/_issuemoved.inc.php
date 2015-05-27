<div class="backdrop_detail_header"><?php echo __('The issue was moved'); ?></div>
<div id="backdrop_detail_content" class="backdrop_detail_content">
    <?php echo __('The selected issue has been moved to the project %project_name. Issues that are moved get new issue numbers - the issue can now be found here: %issue_link', array('%project_name' => link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), $project->getName()), '%issue_link' => '<br>'.link_tag(make_url('viewissue', array('project_key' => $project->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedTitle()))); ?>
    <div style="text-align: right;">
        <a href="javascript:void(0)" class="button button-silver" onclick="if (jQuery('.milestone_details_link.selected').eq(0).find('> a:first-child').length) {jQuery('.milestone_details_link.selected').eq(0).find('> a:first-child').trigger('click');} else {TBG.Main.Helpers.Backdrop.reset();}"><?php echo __('Got it'); ?></a>
    </div>
</div>
