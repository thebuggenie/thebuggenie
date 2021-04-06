<div class="backdrop_box <?php echo isset($medium_backdrop) && $medium_backdrop == 1 ? 'medium' : 'large'; ?>" id="reportissue_container">
    <div class="backdrop_detail_header">
        <span><?php echo __('Report an issue'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php
            $compact_array_vals = array(); $defd_vars = get_defined_vars();
            foreach (array('selected_project', 'issue', 'issuetypes', 'selected_issuetype', 'selected_milestone', 'selected_build', 'parent_issue', 'errors','permission_errors', 'board', 'locked_issuetype') as $caval) {
                if (array_key_exists($caval, $defd_vars)) {
                    $compact_array_vals[] = $caval;
                }
            }
            include_component('main/reportissue', compact($compact_array_vals));
        ?>
    </div>
</div>
