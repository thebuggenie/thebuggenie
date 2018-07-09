<div class="backdrop_box medium" id="viewissue_add_relation_div">
    <div class="backdrop_detail_header"><?php echo __('Find related issues'); ?></div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php echo __('Please enter some details to search for, and then select the matching issues to relate them'); ?>
        <form id="viewissue_find_related_issues_form" action="<?php echo make_url('viewissue_find_related_issues', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>" method="post" accept-charset="<?php echo \thebuggenie\core\framework\Settings::getCharset(); ?>" onsubmit="TBG.Issues.findRelated('<?php echo make_url('viewissue_find_related_issues', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>');return false;">
            <div>
                <label for="viewissue_find_related_issues_input"><?php echo __('Find issue(s)'); ?>&nbsp;</label>
                <input type="text" name="searchfor" id="viewissue_find_related_issues_input">
                <input type="submit" value="<?php echo __('Find'); ?>" style="margin-top: -3px;">
                <?php echo __('%find or %cancel', array('%find' => '', '%cancel' => '<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();">' . __('cancel') . '</a>')); ?>
                <?php echo image_tag('spinning_20.gif', array('id' => 'viewissue_find_related_issues_indicator', 'style' => 'display: none;')); ?><br>
            </div>
        </form>
        <div id="viewissue_relation_results"></div>
    </div>
    <div class="backdrop_detail_footer">
        <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Cancel and close this pop-up'); ?></a>
    </div>
</div>
