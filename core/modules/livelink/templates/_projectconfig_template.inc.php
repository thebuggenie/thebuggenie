<?php

/**
 * @var \thebuggenie\core\modules\livelink\Livelink $module
 */

?>
<?php if (!$project->getId()): ?>
<div class="livelink-banner">
    <h2 class="livelink-header">
        <?= fa_image_tag('leaf tbg-livelink') . __('TBG Live Link'); ?>
    </h2>
    <p class="livelink-intro">
        <?= __('Import and/or link an existing project from %github, %gitlab, %bitbucket and more with %tbg_live_link.', ['%github' => fa_image_tag('github') . ' GitHub', '%gitlab' => fa_image_tag('gitlab') . ' GitLab', '%bitbucket' => fa_image_tag('bitbucket') . ' BitBucket', '%tbg_live_link' => link_tag('https://thebuggenie.com/features/livelink', fa_image_tag('leaf tbg-livelink') . 'TBG Live Link', ['target' => '_blank'])]); ?>
    </p>
    <?php if ($module->hasConnectors()): ?>
        <ul class="livelink-import-list">
            <?php foreach ($module->getConnectorModules() as $connector_key => $connector_module): ?>
                <li>
                    <span class="description"><?= fa_image_tag($connector_module->getConnector()->getLogo()) . $connector_module->getConnector()->getProjectTemplateDescription(); ?></span>
                    <button class="button button-silver" onclick="TBG.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'livelink-import_project', 'connector' => $connector_key]); ?>');"><?php echo __('Import / link'); ?></button>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="livelink-intro">
            <?= __('%tbg_live_link requires integration plugins, available via a paid subscription. If you already have a subscription, download the integration plugins from %configure_modules or visit %thebuggenie_com to get started.', ['%tbg_live_link' => link_tag('https://thebuggenie.com/features/livelink', fa_image_tag('leaf tbg-livelink') . 'TBG Live Link', ['target' => '_blank']), '%thebuggenie_com' => link_tag('https://thebuggenie.com/register/self-hosted', fa_image_tag('globe') . ' thebuggenie.com'), '%configure_modules' => link_tag(make_url('configure_modules'), __('Configuration center') . '&nbsp;&raquo;&nbsp;' . __('Modules'))]); ?>
        </p>
    <?php endif; ?>
</div>
<fieldset class="livelink-separator">
    <legend><?= __('%import_with_livelink or create a project manually', array('%import_with_livelink' => '')); ?></legend>
</fieldset>
<?php endif; ?>