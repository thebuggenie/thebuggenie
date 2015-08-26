<?php /* <div style="text-align: center;"><?php echo image_tag(make_url('project_statistics_last_15', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), array('style' => 'margin-top: 10px;'), true); ?></div> */ ?>
<div id="dashboard_<?php echo $view->getID(); ?>_graph" class="graph_view" style="margin: 5px; width: 100%; height: 250px;"></div>
<script type="text/javascript">
    require(['jquery', 'jquery.flot', 'jquery.ba-resize'],
        function (jQuery) {
            var d_open = [];
            <?php for ($i = 0; $i < count($issues['open']); $i++): ?>
            d_open.push([<?php echo $i; ?>, <?php echo $issues['open'][$i]; ?>]);
            <?php endfor; ?>
            var d_closed = [];
            <?php for ($i = 0; $i < count($issues['closed']); $i++): ?>
            d_closed.push([<?php echo $i; ?>, <?php echo $issues['closed'][$i]; ?>]);
            <?php endfor; ?>
            function initPlot() {
                jQuery.plot(jQuery("#dashboard_<?php echo $view->getID(); ?>_graph"), [
                    {
                        data: d_closed,
                        lines: { show: true, fill: true },
                        points: { show: true },
                        color: '#92BA6F',
                        label: '<?php echo __('Issues closed'); ?>'
                    },
                    {
                        data: d_open,
                        lines: { show: true, fill: true },
                        points: { show: true },
                        color: '#F8C939',
                        label: '<?php echo __('Issues opened'); ?>'
                    }
                ], {
                    xaxis: {
                        color: '#AAA',
                        tickDecimals: 0,
                        ticks: 15
                    },
                    yaxis: {
                        color: '#AAA',
                        min: 0,
                        tickDecimals: 0
                    },
                    grid: {
                        color: '#CCC',
                        borderWidth: 1,
                        backgroundColor: { colors: ["#FFF", "#EEE"] },
                        hoverable: true,
                        autoHighlight: true
                    }
                });
            }
            jQuery("#dashboard_<?php echo $view->getID(); ?>_graph").resize(function () {
                initPlot();
            });
            initPlot();
    });
</script>
