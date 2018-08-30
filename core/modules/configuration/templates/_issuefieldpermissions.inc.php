<div class="backdrop_box large">
    <div class="backdrop_detail_header">
        <span><?php echo __('Permission details for "%itemname"', array('%itemname' => $item_name)); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php echo __('Specify who can set this value for issues.'); ?>
        <?php include_component('configuration/permissionsinfo', array('key' => $item_key, 'mode' => 'datatype', 'target_id' => $item_id, 'module' => 'core', 'access_level' => $access_level)); ?>
    </div>
</div>
