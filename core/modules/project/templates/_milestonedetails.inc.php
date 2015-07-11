<?php if ($milestone instanceof \thebuggenie\core\entities\Milestone): ?>
    <div class="milestone_details" id="milestone_details_<?php echo $milestone->getID(); ?>">
        <h3>
            <span id="milestone_name" class="milestone_name"><?php echo $milestone->getName(); ?></span>&nbsp;
            <?php echo javascript_link_tag(image_tag('icon_edit.png'), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'milestone', 'project_id' => $milestone->getProject()->getId(), 'milestone_id' => $milestone->getID()))."');", 'class' => 'button button-icon button-silver')); ?>
            <br>
        </h3>
        <span class="milestone_date">
            <?php if ($milestone->getStartingDate() && $milestone->isScheduled()): ?>
                (<?php echo tbg_formatTime($milestone->getStartingDate(), 22, true, true); ?> - <?php echo tbg_formatTime($milestone->getScheduledDate(), 22, true, true); ?>)
            <?php elseif ($milestone->getStartingDate() && !$milestone->isScheduled()): ?>
                (<?php echo __('Starting %start_date', array('%start_date' => tbg_formatTime($milestone->getStartingDate(), 22, true, true))); ?>)
            <?php elseif (!$milestone->getStartingDate() && $milestone->isScheduled()): ?>
                (<?php echo __('Ends %end_date', array('%end_date' => tbg_formatTime($milestone->getScheduledDate(), 22, true, true))); ?>)
            <?php endif; ?>
        </span>
        <div id="selected_burndown_image" class="graph_view" style="margin: 15px; width: 750px; height: 360px;"></div>
        <?php //echo image_tag(make_url('project_scrum_sprint_burndown_image', array('project_key' => $selected_project->getKey(), 'sprint_id' => $milestone->getID())), array('style' => 'margin: 15px 0 15px 0;', 'id' => 'selected_burndown_image'), true); ?>
        <table id="milestone_details_issue_list" cellpadding="0" cellspacing="0" border="0">
            <thead>
                <tr>
                    <th>&nbsp;</td>
                    <th><?php echo __('Points'); ?></td>
                    <th><?php echo __('Hours'); ?></td>
                    <th>&nbsp;</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($milestone->getIssues() as $issue): ?>
                    <?php if ($issue->isChildIssue()) continue; ?>
                    <?php include_component('project/milestonedetailsissue', array('issue' => $issue, 'milestone' => $milestone)); ?>
                <?php endforeach; ?>
                <tr>
                    <td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 0; font-weight: bold; font-size: 12px;"><?php echo __('Total estimated effort'); ?></td>
                    <td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 0; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_estimated_points"><?php echo $milestone->getPointsEstimated(); ?></td>
                    <td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 0; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_estimated_hours"><?php echo $milestone->getHoursEstimated(); ?></td>
                    <td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 0; font-weight: bold; font-size: 12px;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 0; font-weight: bold; font-size: 12px;"><?php echo __('Current effort'); ?></td>
                    <td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 0; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_spent_points"><?php echo $milestone->getPointsSpent(); ?></td>
                    <td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 0; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_spent_hours"><?php echo $milestone->getHoursSpent(); ?></td>
                    <td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 0; font-weight: bold; font-size: 12px;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-weight: bold; font-size: 12px;"><?php echo __('Total remaining effort'); ?></td>
                    <td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_remaining_points"><?php echo $milestone->getPointsEstimated() - $milestone->getPointsSpent(); ?></td>
                    <td style="width: 50px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-size: 13px; font-weight: bold; text-align: center; padding: 5px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_remaining_hours"><?php echo $milestone->getHoursEstimated() - $milestone->getHoursSpent(); ?></td>
                    <td style="padding: 5px; border-top: 1px dotted #AAA; border-bottom: 1px dotted #AAA; font-weight: bold; font-size: 12px;">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <script type="text/javascript">
            require(['domReady', 'jquery', 'jquery.flot', 'jquery.flot.time', 'jquery.flot.dashes'], function (domReady, jQuery) {
                domReady(function () {
                    jQuery(function () {

                        var d_e_points = [];
                        var d_e_hours = [];
                        var d_s_points = [];
                        var d_s_hours = [];
                        var d_b_points = [];
                        var d_b_hours = [];

                        <?php if ($milestone->hasStartingDate() && $milestone->hasScheduledDate()): ?>
                            var eh_keys = [];
                            var eh_values = [];
                            var ep_keys = [];
                            var ep_values = [];
                            var burndown_data = <?php echo json_encode($burndown_data); ?>;
                            var currenttime = <?php echo NOW * 1000; ?>;


                            for(var d in burndown_data.burndown.points) {
                                if(burndown_data.burndown.points.hasOwnProperty(d)) {
                                    d_b_points.push([d * 1000, burndown_data.burndown.points[d]]);
                                }
                            }

                            for(var d in burndown_data.burndown.hours) {
                                if(burndown_data.burndown.hours.hasOwnProperty(d)) {
                                    d_b_hours.push([d * 1000, burndown_data.burndown.hours[d]]);
                                }
                            }

                            for(var d in burndown_data.estimations.hours) {
                                if(burndown_data.estimations.hours.hasOwnProperty(d)) {
                                    eh_keys.push(d);
                                    eh_values.push(burndown_data.estimations.hours[d]);
                                }
                            }
                            for(var d in burndown_data.estimations.points) {
                                if(burndown_data.estimations.points.hasOwnProperty(d)) {
                                    ep_keys.push(d);
                                    ep_values.push(burndown_data.estimations.points[d]);
                                }
                            }
                            var d_e_velocity_hours = [[eh_keys.min() * 1000, eh_values.max()], [eh_keys.max() * 1000, 0]];
                            var d_e_velocity_points = [[ep_keys.min() * 1000, ep_values.max()], [ep_keys.max() * 1000, 0]];
                                var x_config = TBG.Chart.config.x_config;
                                x_config.mode = 'time';
                                var grid_config = TBG.Chart.config.grid_config;
                                grid_config.markings = [{xaxis: {from: currenttime, to: currenttime}, color: '#955', lineWidth: 1}];
                                jQuery.plot(jQuery("#selected_burndown_image"), [
                                    {
                                        data: d_e_velocity_hours,
                                        dashes: {show: true, lineWidth: 1},
                                        points: {show: false},
                                        color: '#66F',
                                        label: '<?php echo __('Estimated velocity (hours)'); ?>'
                                    },
                                    {
                                        data: d_e_velocity_points,
                                        dashes: {show: true, lineWidth: 1},
                                        points: {show: false},
                                        color: '#333',
                                        label: '<?php echo __('Estimated velocity (points)'); ?>'
                                    },
                                    {
                                        data: d_b_hours,
                                        lines: {show: true},
                                        points: {show: true},
                                        color: '#923A6F',
                                        label: '<?php echo __('Hours burndown'); ?>'
                                    },
                                    {
                                        data: d_b_points,
                                        lines: {show: true},
                                        points: {show: true},
                                        color: '#F83A39',
                                        label: '<?php echo __('Points burndown'); ?>'
                                    }
                                ], {
                                yaxis: TBG.Chart.config.y_config,
                                xaxis: x_config,
                                grid: grid_config
                                });
                        <?php else: ?>
                            <?php foreach (range(0, 8) as $cc): ?>
                                <?php $eh_val = ($cc != 3) ? 0 : array_sum($burndown_data['estimations']['hours']); ?>
                                d_e_hours.push([<?php echo $cc; ?>,<?php echo $eh_val; ?>]);
                                <?php $sh_val = ($cc != 3) ? 0 : array_sum($burndown_data['spent_times']['hours']); ?>
                                d_s_hours.push([<?php echo $cc; ?>,<?php echo $sh_val; ?>]);
                                <?php $ep_val = ($cc != 5) ? 0 : array_sum($burndown_data['estimations']['points']); ?>
                                d_e_points.push([<?php echo $cc; ?>,<?php echo $ep_val; ?>]);
                                <?php $sp_val = ($cc != 5) ? 0 : array_sum($burndown_data['spent_times']['points']); ?>
                                d_s_points.push([<?php echo $cc; ?>,<?php echo $sp_val; ?>]);
                            <?php endforeach; ?>
                            var x_config = TBG.Chart.config.x_config;
                            x_config.ticks = [[0, ' '], [1, ' '], [2, ' '], [3, '<?php echo __('Hours'); ?>'], [4, ' '], [5, '<?php echo __('Points'); ?>'], [6, ' '], [7, ' '], [8, ' ']]
                            jQuery.plot(jQuery("#selected_burndown_image"), [
                                {
                                    data: d_e_hours,
                                    color: '#92BA6F',
                                    label: '<?php echo __('Estimated hours'); ?>'
                                },
                                {
                                    data: d_e_points,
                                    color: '#F8C939',
                                    label: '<?php echo __('Estimated points'); ?>'
                                },
                                {
                                    data: d_s_hours,
                                    color: '#923A6F',
                                    label: '<?php echo __('Spent hours'); ?>'
                                },
                                {
                                    data: d_s_points,
                                    color: '#F83A39',
                                    label: '<?php echo __('Spent points'); ?>'
                                }
                            ], {
                                series: {
                                    stack: true,
                                    lines: { show: false },
                                    bars: { show: true, barWidth: 0.6, align: "center" }
                                },
                                yaxis: TBG.Chart.config.y_config,
                                xaxis: x_config,
                                grid: TBG.Chart.config.grid_config
                            });
                        <?php endif; ?>
                    });
                });
            });
        </script>
    </div>
<?php else: ?>
    <?php echo __('No milestone selected'); ?>
<?php endif; ?>
