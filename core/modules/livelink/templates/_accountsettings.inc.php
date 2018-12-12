<?php

/**
 * @var \thebuggenie\core\entities\User $tbg_user
 * @var \thebuggenie\core\modules\livelink\Livelink $module
 */

?>
<h3><?= __('Configured services'); ?></h3>
<p><?= __('Link your external services (such as GitHub, GitLab, etc) from this page to enable linking projects via %tbg_live_link', ['%tbg_live_link' => link_tag('https://thebuggenie.com/features/livelink', fa_image_tag('leaf') . 'TBG Live Link')]); ?></p>
<?php if (!$module->hasConnectors()): ?>
    <p class="livelink-intro">
        <?= __('%tbg_live_link requires integration plugins. Download the integration plugins from %configure_modules or visit %thebuggenie_com to get started.', ['%tbg_live_link' => link_tag('https://thebuggenie.com/features/livelink', fa_image_tag('leaf') . 'TBG Live Link', ['target' => '_blank']), '%thebuggenie_com' => link_tag('https://thebuggenie.com/register/self-hosted', fa_image_tag('globe') . ' thebuggenie.com'), '%configure_modules' => link_tag(make_url('configure_modules'), __('Configuration center') . '&nbsp;&raquo;&nbsp;' . __('Modules'))]); ?>
    </p>
<?php else: ?>
    <ul id="livelink-connector-accounts" class="livelink_connector_accounts">
        <?php foreach ($module->getConnectorModules() as $connector_key => $connector_provider): ?>
            <li id="livelink-<?= $connector_key; ?>-configuration" class="<?= ($connector_provider->getConnector()->isConfigured()) ? 'connected' : ''; ?>">
                <span class="description"><?= fa_image_tag($connector_provider->getConnector()->getLogo(), ['class' => 'connector_logo'], $connector_provider->getConnector()->getLogoStyle()) . $connector_provider->getConnector()->getName(); ?><span class="not-connected"><?= fa_image_tag('square') . __('Not connected'); ?></span><span class="connected-ok"><?= fa_image_tag('check-square', [], 'far') . __('Connected'); ?></span></span>
                <button class="button button-silver button-connect-livelink-connector" onclick="TBG.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'livelink-configure_connector', 'connector' => $connector_key]); ?>');"><?= __('Connect'); ?></button>
                <button class="button button-silver button-disconnect-livelink-connector" data-connector="<?= $connector_key; ?>"><?= image_tag('spinning_16.gif', ['class' => "indicator"]) . __('Disconnect'); ?></button>
            </li>
        <?php endforeach; ?>
    </ul>
    <script>
        require(['domReady', 'thebuggenie/tbg', 'jquery'], function (domReady, tbgjs, $) {
            domReady(function () {

                var $livelink_connector_accounts = $('#livelink-connector-accounts');

                var disconnectConnector = function(e) {
                    var url       = '<?= make_url('disconnect_livelink_connector'); ?>',
                        $button   = $(this),
                        connector = $button.data('connector');

                    e.preventDefault();

                    $button.addClass('submitting');
                    $button.attr('disabled', true);

                    var submitStep = function () {
                        return new Promise(function (resolve, reject) {
                            $.ajax({
                                type: 'POST',
                                dataType: 'text',
                                data: 'connector=' + connector,
                                url: url,
                                success: resolve,
                                error: function (details) {
                                    $button.removeClass('submitting');
                                    $button.attr('disabled', false);
                                    reject(details);
                                }
                            });
                        });
                    };

                    submitStep()
                        .then(function (result) {
                            $('#livelink-' + connector + '-configuration').removeClass('connected');
                            $button.removeClass('submitting');
                            $button.attr('disabled', false);
                        }, function (details) {
                            tbgjs.Main.Helpers.Message.error(details.responseJSON.error);
                        });
                };

                $livelink_connector_accounts.off('click');
                $livelink_connector_accounts.on('click', '.button-disconnect-livelink-connector', disconnectConnector);
            });
        });
    </script>
<?php endif; ?>
