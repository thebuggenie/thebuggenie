<?php if ($show_box): ?>
    <div class="rounded_box iceblue borderless infobox" style="margin: 5px; padding: 5px;" id="infobox_<?= $key; ?>">
        <?= image_tag('icon_info_big.png', array('style' => 'float: left; margin: 5px 5px 5px 5px;')); ?>
        <div>
            <div class="header"><?= $title; ?></div>
            <div class="content"><?= $content; ?></div>
        </div>
        <form id="close_me_<?= $key; ?>_form" action="<?= make_url('hide_infobox', array('key' => $key)); ?>" method="post" accept-charset="<?= \thebuggenie\core\framework\Settings::getCharset(); ?>" onsubmit="TBG.Main.hideInfobox('<?= make_url('hide_infobox', array('key' => $key)); ?>', '<?= $key; ?>');return false;">
            <div class="close_me">
                <input type="checkbox" value="1" name="dont_show" id="close_me_<?= $key; ?>" class="fancycheckbox"><label for="close_me_<?= $key; ?>"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __("Don't show this again"); ?></label>
                <input type="submit" value="<?= __('Hide'); ?>">
            </div>
        </form>
        <div style="display: none;" id="infobox_<?= $key; ?>_indicator">
            <?= image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')); ?>
            <?= __('Updating, please wait ...'); ?>
        </div>
    </div>
<?php endif; ?>
