<div class="backdrop_box large">
    <div class="backdrop_detail_header">
        <?php echo __('Permission details for "%itemname"', array('%itemname' => $item_name)); ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php include_component('configuration/permissionsinfo', compact('key', 'mode', 'target_id', 'module', 'access_level')); ?>
    </div>
    <div class="backdrop_detail_footer">
        <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Close'); ?></a>
    </div>
</div>
