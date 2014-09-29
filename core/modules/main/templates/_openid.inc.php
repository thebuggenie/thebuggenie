<div class="backdrop_box large" id="login_popup">
    <div class="backdrop_detail_header"><?php echo __('Add external login'); ?></div>
    <div id="backdrop_detail_content" class="backdrop_detail_content rounded_top login_content">
        <?php include_component('main/openidbuttons', array('mode' => 'add_signin')); ?>
    </div>
    <div class="backdrop_detail_footer">
        <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Close'); ?></a>
    </div>
</div>
