<li id="viewissue_links_<?php echo $link_id; ?>" class="attached_item">
    <a href="<?php echo $link['url']; ?>" class="downloadlink" title="<?php echo $link['url']; ?>" target="_blank">
        <?php echo image_tag('icon_link.png'); ?>
        <?php echo ($link['description'] != '') ? $link['description'] : $link['url']; ?>
    </a>
    <?php if ($issue->canRemoveAttachments()): ?>
        <div class="removelink">
            <?php echo javascript_link_tag(image_tag('action_delete.png'), array('id' => $link_id . '_remove_link', 'onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to remove this link?')."', '".__('This action cannot be reversed. Are you sure you want to do this?')."', {yes: {click: function() {TBG.Issues.Link.remove('".make_url('issue_remove_link', array('issue_id' => $issue->getID(), 'link_id' => $link_id))."', ".$link_id."); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});")); ?>
            <?php echo image_tag('spinning_16.gif', array('id' => 'viewissue_links_' . $link_id . '_remove_indicator', 'style' => 'display: none;')); ?>
        </div>
    <?php endif; ?>
</li>
