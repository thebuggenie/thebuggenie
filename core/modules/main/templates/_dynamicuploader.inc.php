<?php if (isset($target)): ?>
<form action="<?php echo make_url('update_attachments', array('target' => $mode, 'target_id' => $target->getID())); ?>" method="post" onsubmit="TBG.Main.updateAttachments(this);return false;">
    <div class="backdrop_detail_content">
<?php endif; ?>
        <div class="upload_container" id="upload_drop_zone">
            <span class="double"><?php echo __('Drop files on this area or %click_here to add files', array('%click_here' => '<span class="upload_click_here">'.__('click here').'</span>')); ?></span>
            <span class="single" style="display: none;"><?php echo __('%click_here to add files', array('%click_here' => '<span class="upload_click_here">'.__('click here').'</span>')); ?></span>
        </div>
        <div class="upload_file_listing">
            <ul id="file_upload_list" data-filename-label="<?php echo htmlentities(__('File'), ENT_COMPAT, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>" data-description-label="<?php echo htmlentities(__('Description'), ENT_COMPAT, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>" data-description-placeholder="<?php echo htmlentities(__('Enter a short file description here'), ENT_COMPAT, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>" data-preview-src="<?php echo image_url('icon_file_generic.png'); ?>">
                <?php if (isset($existing_files)): ?>
                    <?php foreach ($existing_files as $file): ?>
                        <li>
                            <?php if ($file->isImage()): ?>
                                <span class="imagepreview" title="<?php echo $file->getOriginalFilename(); ?>"><?php echo image_tag(make_url('showfile', array('id' => $file->getID())), array(), true); ?></span>
                            <?php else: ?>
                                <span class="imagepreview" title="<?php echo $file->getOriginalFilename(); ?>"><?php echo image_tag('icon_file_generic.png'); ?></span>
                            <?php endif; ?>                    
                            <label><?php echo __('File'); ?></label><span class="filename"><?php echo $file->getOriginalFilename(); ?></span> <span class="filesize"><?php echo $file->getReadableFilesize(); ?></span><br>
                            <label><?php echo __('Description'); ?></label><input type="text" class="file_description" name="file_description[<?php echo $file->getId(); ?>]" value="<?php echo $file->getDescription(); ?>" placeholder="<?php echo __('Enter a short file description here'); ?>">
                            <input type="hidden" name="files[<?php echo $file->getId(); ?>]" value="<?php echo $file->getId(); ?>">
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?></ul>
        </div>
        <script>
            require(['domReady', 'thebuggenie/tbg'], function (domReady, TBG) {
                domReady(function () {
                    var upload_container = $('upload_drop_zone');
                    if ('ondrop' in document.createElement('span')) {
                        upload_container.addEventListener('dragover', TBG.Main.dragOverFiles, false);
                        upload_container.addEventListener('dragleave', TBG.Main.dragOverFiles, false);
                        upload_container.addEventListener('drop', TBG.Main.dropFiles, false);
                        upload_container.addEventListener('click', function () {$('file_upload_dummy').click();}, false);
                    } else {
                        upload_container.down('.double').hide();
                        upload_container.down('.single').show();
                    }
                });
            });
        </script>
<?php if (isset($target)): ?>
    </div>
    <div class="backdrop_detail_footer">
        <a href="javascript:void(0)" onclick="TBG.Main.Helpers.Backdrop.reset()"><?php echo __('Cancel'); ?></a>
        <?php echo __('%cancel or %save_attachments', array('%cancel' => '', '%save_attachments' => '')); ?>
        <?php echo image_tag('spinning_16.gif', array('id' => 'attachments_indicator', 'style' => 'display: none;')); ?>
        <input type="submit" class="button button-silver" value="<?php echo __('Save attachments'); ?>" id="dynamic_uploader_submit">
    </div>
</form>
<?php endif; ?>

