<div class="backdrop_box <?php echo isset($medium_backdrop) && $medium_backdrop == 1 ? 'medium' : 'huge'; ?>" id="reportissue_container">
    <div class="backdrop_detail_header"><?php echo __('Report an issue'); ?></div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php include_component('main/reportissue', compact('selected_project', 'issue', 'issuetypes', 'selected_issuetype', 'selected_milestone', 'selected_build', 'parent_issue', 'errors', 'permission_errors', 'board', 'locked_issuetype')); ?>
    </div>
    <div class="backdrop_detail_footer">
        <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Close this popup'); ?></a>
    </div>
</div>
