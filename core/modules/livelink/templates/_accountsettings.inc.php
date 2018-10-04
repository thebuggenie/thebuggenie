<?php

/**
 * @var \thebuggenie\core\entities\User $tbg_user
 * @var \thebuggenie\core\modules\livelink\Livelink $module
 */

?>
<h3><?php echo __('Configured services'); ?></h3>
<p><?php echo __('Link your external services (such as GitHub, GitLab, etc) from this page to enable linking projects via %tbg_live_link', ['%tbg_live_link' => link_tag('https://thebuggenie.com/features/livelink', fa_image_tag('leaf tbg-livelink') . 'TBG Live Link')]); ?></p>
<ul id="livelink-connector-accounts" class="livelink_connector_accounts">
    <?php foreach ($module->getConnectorModules() as $connector_key => $connector_provider): ?>
        <li id="livelink-<?= $connector_key; ?>-configuration">
            <span class="description"><?= fa_image_tag($connector_provider->getConnector()->getLogo()) . $connector_provider->getConnector()->getName(); ?></span>
            <button class="button button-silver" onclick="TBG.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'livelink-configure_connector', 'connector' => $connector_key]); ?>');"><?php echo __('Configure'); ?></button>
        </li>
    <?php endforeach; ?>
</ul>
