<div id="vcs_integration_getcommit_backdrop_box" class="backdrop_box large">
    <div class="backdrop_detail_header">
        <?php echo __('Show commit details'); ?>
    </div>
    <div class="backdrop_detail_content">
        <?php include_component('vcs_integration/commitbox', array("projectId" => $projectId, "commit" => $commit, 'expanded' => true)); ?>
    </div>
    <div class="backdrop_detail_footer">
        <a href="javascript:void(0)" onclick="TBG.Main.Helpers.Backdrop.reset()"><?php echo __('Close'); ?></a>
    </div>
</div>
