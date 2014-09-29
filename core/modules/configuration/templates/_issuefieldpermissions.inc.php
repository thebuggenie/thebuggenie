<div class="backdrop_box large">
    <div class="backdrop_detail_header">
        <?php echo __('Permission details for "%itemname"', array('%itemname' => $item_name)); ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php echo __('Specify who can set this value for issues.'); ?>
        <?php include_component('configuration/permissionsinfo', array('key' => $item_key, 'mode' => 'datatype', 'target_id' => $item_id, 'module' => 'core', 'access_level' => $access_level)); ?>
    </div>
    <div class="backdrop_detail_footer">
        <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Close'); ?></a>
    </div>
</div>
