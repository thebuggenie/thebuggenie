<div class="backdrop_box <?php echo isset($medium_backdrop) && $medium_backdrop == 1 ? 'medium' : 'large'; ?>" id="reportissue_container">
    <div class="backdrop_detail_header">
        <span><?php echo __('Report an issue'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php include_component('main/reportissue', compact('selected_project', 'issue', 'issuetypes', 'selected_issuetype', 'selected_milestone', 'selected_build', 'parent_issue', 'errors', 'permission_errors', 'board', 'locked_issuetype')); ?>
    </div>
</div>
