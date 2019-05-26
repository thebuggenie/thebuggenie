<div class="backdrop_box large">
    <div class="backdrop_detail_header">
        <span><?php echo __('Permission details for "%itemname"', array('%itemname' => $item_name)); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content permissions_popup">
        <?php include_component('configuration/permissionsinfo', compact('key', 'mode', 'target_id', 'module', 'access_level')); ?>
    </div>
</div>
