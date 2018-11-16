<div id="livelink_getcommit_backdrop_box" class="backdrop_box large">
    <div class="backdrop_detail_header">
        <span><?php echo __('Show commit details'); ?></span>
        <a href="javascript:void(0)" onclick="TBG.Main.Helpers.Backdrop.reset()" class="closer"><?php echo fa_image_tag('times'); ?></a>
    </div>
    <div class="backdrop_detail_content">
        <?php include_component('livelink/commitbox', array("projectId" => $projectId, "commit" => $commit, 'expanded' => true)); ?>
    </div>
</div>
