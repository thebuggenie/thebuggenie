<div class="backdrop_box login_popup" id="login_popup">
    <div id="backdrop_detail_content" class="backdrop_detail_content rounded_top login_content">
        <?php echo $content; ?>
    </div>
    <div class="backdrop_detail_footer">
    <?php if ($mandatory != true): ?>
        <a href="javascript:void(0);" onclick="$('login_backdrop').hide();"><?php echo __('Close'); ?></a>
    <?php endif; ?>
    </div>
</div>
<?php if (isset($options['error'])): ?>
    <script type="text/javascript">
        TBG.Main.Helpers.Message.error('<?php echo $options['error']; ?>');
    </script>
<?php endif; ?>