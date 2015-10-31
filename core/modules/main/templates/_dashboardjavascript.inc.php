<script type="text/javascript">
    var TBG;

    require(['domReady', 'thebuggenie/tbg'], function (domReady, tbgjs) {
        domReady(function () {
            TBG = tbgjs;
                $$('.dashboard_add_view_container').each(function (davc) {
                    davc.on('click', TBG.Main.Dashboard.addViewPopup);
                });
            });
        });
</script>