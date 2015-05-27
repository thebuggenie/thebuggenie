<?php $b_id = $build->getID(); ?>
<li id="show_build_<?php print $b_id; ?>" class="release_item <?php if ($build->isActive()) echo 'active'; ?> <?php if ($build->hasDownload()) echo 'download'; ?>">
    <?php if ($tbg_user->canManageProjectReleases($build->getProject())): ?>
        <div class="button-group">
            <button class="button button-silver" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'project_build', 'project_id' => $build->getProject()->getId(), 'build_id' => $build->getId())); ?>');"><?php echo __('Edit'); ?></button>
            <button class="button button-silver" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Delete this release?'); ?>', '<?php echo __('Do you really want to delete this release?').'<br>'.__('Deleting this release will make it unavailable for download, and remove it from any associated issue reports or feature requests.').'<br><b>'.__('This action cannot be reverted').'</b>'; ?>', {yes: {click: function() {TBG.Project.Build.remove('<?php echo make_url('configure_build_delete', array('build_id' => $b_id, 'project_id' => $build->getProject()->getId())); ?>', <?php print $b_id; ?>, '<?php echo ($build->isLocked()) ? "archived" : "active"; ?>', <?php echo $build->getEditionID(); ?>);}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo __('Delete'); ?></button>
        </div>
    <?php endif; ?>
    <?php echo image_tag('icon_build_medium.png', array('class' => 'release_icon')); ?>
    <span id="build_<?php echo $b_id; ?>name" class="release_name"><?php print $build->getName(); ?></span>
    <span class="release_version" id="build_<?php echo $b_id; ?>_version"><?php print $build->getVersion(); ?></span>
    <div class="release_date <?php if ($build->isReleased()) echo 'released'; ?>" id="build_<?php echo $b_id; ?>_release_date">
        <?php if ($build->isReleased()): ?>
            <?php $release_date_text = $build->hasReleaseDate() ? __('Released %release_date', array('%release_date' => tbg_formatTime($build->getReleaseDate(), 7, true, true))) : __('Released'); ?>
        <?php else: ?>
            <?php $release_date_text = __('Not released yet'); ?>
        <?php endif; ?>
        <?php if ($build->hasDownload()): ?>
            <?php $release_date_text = __('%release_date, download: %download_filename', array('%release_date' => $release_date_text, '%download_filename' => ($build->hasFile()) ? link_tag(make_url('downloadfile', array('id' => $build->getFile()->getID())), $build->getFile()->getOriginalFilename()) : link_tag($build->getFileURL()))); ?>
        <?php else: ?>
            <?php $release_date_text = __('%release_date, no download available', array('%release_date' => $release_date_text)); ?>
        <?php endif; ?>
        <?php if (! $build->isReleased()): ?>
            <div id="build_<?php echo $b_id; ?>_not_released"><?php echo $release_date_text; ?></div>
        <?php else: ?>
            <?php echo $release_date_text; ?>
        <?php endif; ?>
    </div>
</li>
