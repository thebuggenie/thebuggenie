<div id="attach_link" class="backdrop_box medium">
    <div class="backdrop_detail_header">
        <span><?= __('Attach a link to this issue'); ?></span>
        <a href="javascript:void(0)" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset()"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <form action="<?= make_url('issue_attach_link', array('issue_id' => $issue->getID())); ?>" method="post" onsubmit="TBG.Issues.Link.add('<?= make_url('issue_attach_link', array('issue_id' => $issue->getID())); ?>');return false;" id="attach_link_form">
            <dl>
                <dt style="width: 80px; padding-top: 3px;"><label for="attach_link_url"><?= __('URL'); ?>:</label></dt>
                <dd style="margin-bottom: 0px;"><input type="text" name="link_url" id="attach_link_url" style="width: 235px;"></dd>
                <dt style="width: 80px; font-size: 10px; padding-top: 4px;"><label for="attach_link_description"><?= __('Description'); ?>:</label></dt>
                <dd style="margin-bottom: 0px;"><input type="text" name="description" id="attach_link_description" style="width: 235px;"></dd>
            </dl>
            <div style="font-size: 12px; clear: both; padding: 15px 2px 10px 2px;"><?= __('Enter the link URL here, along with an optional description. Press "%attach_link" to attach it to the issue.', array('%attach_link' => __('Attach link'))); ?></div>
            <div class="backdrop_details_submit">
                <span class="explanation"></span>
                <div class="submit_container">
                    <div style="text-align: center;"><button type="submit" id="attach_link_submit"><?= image_tag('spinning_16.gif', ['id' => 'attach_link_indicator', 'style' => 'display: none;']) . __('Attach link'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
