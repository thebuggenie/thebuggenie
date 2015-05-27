<div id="attach_link" class="backdrop_box medium">
    <div class="backdrop_detail_header">
        <?php echo __('Attach a link to this issue'); ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <form action="<?php echo make_url('issue_attach_link', array('issue_id' => $issue->getID())); ?>" method="post" onsubmit="TBG.Issues.Link.add('<?php echo make_url('issue_attach_link', array('issue_id' => $issue->getID())); ?>');return false;" id="attach_link_form">
            <dl style="margin: 0; clear: both;">
                <dt style="width: 80px; padding-top: 3px;"><label for="attach_link_url"><?php echo __('URL'); ?>:</label></dt>
                <dd style="margin-bottom: 0px;"><input type="text" name="link_url" id="attach_link_url" style="width: 235px;"></dd>
                <dt style="width: 80px; font-size: 10px; padding-top: 4px;"><label for="attach_link_description"><?php echo __('Description'); ?>:</label></dt>
                <dd style="margin-bottom: 0px;"><input type="text" name="description" id="attach_link_description" style="width: 235px;"></dd>
            </dl>
            <br style="clear: both;">
            <div style="font-size: 12px; clear: both; padding: 15px 2px 10px 2px;"><?php echo __('Enter the link URL here, along with an optional description. Press "%attach_link" to attach it to the issue.', array('%attach_link' => __('Attach link'))); ?></div>
            <div style="text-align: center; padding: 10px; display: none;" id="attach_link_indicator"><?php echo image_tag('spinning_16.gif'); ?></div>
            <div style="text-align: center;"><input type="submit" value="<?php echo __('Attach link'); ?>" style="font-weight: bold;" id="attach_link_submit"><?php echo __('%attach_link or %cancel', array('%attach_link' => '', '%cancel' => '<b>'.javascript_link_tag(__('cancel'), array('onclick' => "TBG.Main.Helpers.Backdrop.reset();")).'</b>')); ?></div>
        </form>
    </div>
</div>
