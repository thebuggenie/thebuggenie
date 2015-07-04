<?php

    $can_remove = false;
    if ($mode == 'issue' && $issue->canRemoveAttachments())
        $can_remove = true;
    if ($mode == 'article' && $article->canEdit())
        $can_remove = true;

?>
<?php if ($file instanceof \thebuggenie\core\entities\File): ?>
    <li id="<?php echo $base_id . '_' . $file_id; ?>" class="attached_item <?php if ($file->isImage()) echo 'file_image'; ?>">
        <?php if ($file->isImage()): ?>
            <a href="<?php echo make_url('showfile', array('id' => $file_id)); ?>" target="_new" class="imagepreview" title="<?php echo ($file->hasDescription()) ? $file->getDescription() : $file->getOriginalFilename(); ?>"><?php echo image_tag(make_url('showfile', array('id' => $file_id)), array(), true); ?></a>
            <div class="embedlink removelink">
                <?php echo javascript_link_tag(image_tag('action_embed.png'), array('onclick' => "TBG.Main.Helpers.Dialog.showModal('".__('Embedding this file in descriptions or comments')."', '".__('Use this tag to include this image: [[Image:%filename|thumb|Image description]]', array('%filename' => $file->getOriginalFilename()))."');")); ?>
            </div>
        <?php else: ?>
            <a href="<?php echo make_url('downloadfile', array('id' => $file_id)); ?>" class="downloadlink">
                <?php echo image_tag('icon_download.png'); ?>
                <?php echo ($file->hasDescription()) ? $file->getDescription() : $file->getOriginalFilename(); ?>
            </a>
        <?php endif; ?>
        <?php if ($can_remove): ?>
            <div class="removelink">
                <?php if ($mode == 'issue'): ?>
                    <?php echo javascript_link_tag(image_tag('action_delete.png'), array('id' => $base_id . '_' . $file_id . '_remove_link', 'onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to detach this file?')."', '".__('If you detach this file, it will be deleted. This action cannot be undone. Are you sure you want to remove this file?')."', {yes: {click: function() {TBG.Issues.File.remove('".make_url('issue_detach_file', array('issue_id' => $issue->getID(), 'file_id' => $file_id))."', ".$file_id."); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});")); ?>
                <?php elseif ($mode == 'article'): ?>
                    <?php echo javascript_link_tag(image_tag('action_delete.png'), array('id' => $base_id . '_' . $file_id . '_remove_link', 'onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to detach this file?')."', '".__('If you detach this file, it will be deleted. This action cannot be undone. Are you sure you want to remove this file?')."', {yes: {click: function() {TBG.Main.detachFileFromArticle('".make_url('article_detach_file', array('article_name' => $article->getName(), 'file_id' => $file_id))."', ".$file_id.", ".$article->getID()."); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});")); ?>
                <?php endif; ?>
                <?php echo image_tag('spinning_16.gif', array('id' => $base_id . '_' . $file_id . '_remove_indicator', 'style' => 'display: none;')); ?>
            </div>
        <?php endif; ?>
        <div class="upload_details">
            <?php echo __('%filename uploaded %date by %username', array('%filename' => '<span class="filename">'.$file->getOriginalFilename().'</span>', '%date' => tbg_formatTime($file->getUploadedAt(), 23), '%username' => (($file->getUploadedBy() instanceof \thebuggenie\core\entities\User) ? '<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show(\'' . make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $file->getUploadedBy()->getID())) . '\');" class="faded_out">' . $file->getUploadedBy()->getNameWithUsername() . '</a>' : __('unknown user')))); ?>
        </div>
    </li>
<?php else: ?>
    <li class="faded_out"><?php echo __('Invalid file'); ?></li>
<?php endif; ?>
