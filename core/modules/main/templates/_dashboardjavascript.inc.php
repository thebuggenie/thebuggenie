<script type="text/javascript">
    TBG.Main.Dashboard.url = "<?php echo make_url("dashboard_view", array('view_id' => '')); ?>/{view_id}";

    document.observe('dom:loaded', function() {
        $$('.dashboard_add_view_container').each(function (davc) {
            davc.on('click', TBG.Main.Dashboard.addViewPopup);
        });
        $$('.dashboard .remover').each(function (remover) {
            remover.on('click', TBG.Main.Dashboard.removeView);
        });
    });
</script>