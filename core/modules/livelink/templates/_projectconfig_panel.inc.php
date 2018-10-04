<div id="tab_livelink_pane"<?php if ($selected_tab != 'livelink'): ?> style="display: none;"<?php endif; ?>>
    <h3><?= __('Connect with Github');?></h3>
    <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
        <div class="rounded_box red" style="margin-top: 10px;">
            <?= __('You do not have the relevant permissions to access VCS Integration settings'); ?>
        </div>
    <?php else: ?>
        <div class="address-settings">
            <form action="<?= make_url('configure_livelink_settings', array('project_id' => $project->getID())); ?>" accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_livelink_settings', array('project_id' => $project->getID())); ?>" method="post" onsubmit="return false;" id="livelink_form">
                <?php include_component('livelink/setupstep1'); ?>
            </form>
        </div>
    <?php endif; ?>
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