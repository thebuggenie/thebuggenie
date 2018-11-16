<div id="tab_livelink_pane"<?php if ($selected_tab != 'livelink'): ?> style="display: none;"<?php endif; ?>>
    <?php include_component('livelink/projectconfig_template', ['project' => $project]); ?>
</div>
<script>
    require(['domReady', 'thebuggenie/tbg', 'jquery'], function (domReady, tbgjs, $) {
        domReady(function () {
            var submitSetupStep = function(e) {
                var form_id        = 'livelink_form',
                    $form          = $('#' + form_id),
                    $indicator     = $('#' + form_id + '_indicator'),
                    $submit_button = $('#' + form_id + '_button'),
                    url            = $form.attr("action");

                $indicator.show();
                e.preventDefault();

                var submitStep = function () {
                    return new Promise(function (resolve, reject) {
                        $.ajax({
                            type: 'POST',
                            data: $form.serialize(),
                            url: url,
                            success: resolve,
                            error: function (details) {
                                $indicator.hide();
                                $submit_button.attr('disabled', false);
                                reject(details);
                            }
                        });
                    });
                };

                submitStep()
                    .then(function (result) {
                        $indicator.hide();
                        $form.addClass('disabled');
                        $('#livelink_address_container').addClass('verified');
                        $('#livelink_repository_url_input').attr('disabled', true);
                    }, function (details) {
                        tbgjs.Main.Helpers.Message.error(details.responseJSON.error);
                    });
            };

            $('#livelink_form').submit(submitSetupStep);

            $('#livelink_change_button').click(function (e) {
                e.preventDefault();

                $('#livelink_form').removeClass('disabled');
                $('#livelink_address_container').removeClass('verified');
                $('#livelink_repository_url_input').attr('disabled', false);
            })
        });
    });
</script>