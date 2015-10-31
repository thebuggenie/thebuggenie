<?php if ($show_box): ?>
    <div class="rounded_box iceblue borderless infobox" style="margin: 5px; padding: 5px;" id="infobox_<?php echo $key; ?>">
        <?php echo image_tag('icon_info_big.png', array('style' => 'float: left; margin: 5px 5px 5px 5px;')); ?>
        <div>
            <div class="header"><?php echo $title; ?></div>
            <div class="content"><?php echo $content; ?></div>
        </div>
        <form id="close_me_<?php echo $key; ?>_form" action="<?php echo make_url('hide_infobox', array('key' => $key)); ?>" method="post" accept-charset="<?php echo \thebuggenie\core\framework\Settings::getCharset(); ?>" onsubmit="TBG.Main.hideInfobox('<?php echo make_url('hide_infobox', array('key' => $key)); ?>', '<?php echo $key; ?>');return false;">
            <div class="close_me">
                <input type="checkbox" value="1" name="dont_show" id="close_me_<?php echo $key; ?>"></input>
                <label for="close_me_<?php echo $key; ?>"><?php echo __("Don't show this again"); ?></label>
                <input type="submit" value="<?php echo __('Hide'); ?>"></input>
            </div>
        </form>
        <div style="display: none;" id="infobox_<?php echo $key; ?>_indicator">
            <?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')); ?>
            <?php echo __('Updating, please wait ...'); ?>
        </div>
    </div>
<?php endif; ?>
