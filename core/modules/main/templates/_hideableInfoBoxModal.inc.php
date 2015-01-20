<?php if ($show_box): ?>
    <div class="fullpage_backdrop infobox_modal" id="infobox_<?php echo $key; ?>">
        <div class="backdrop_box large">
            <div class="backdrop_detail_header"><?php echo $title; ?></div>
            <div class="backdrop_detail_content">
                <?php include_component($template, $options); ?>
                <form id="close_me_<?php echo $key; ?>_form" action="<?php echo make_url('hide_infobox', array('key' => $key)); ?>" method="post" accept-charset="<?php echo \thebuggenie\core\framework\Settings::getCharset(); ?>" onsubmit="TBG.Main.hideInfobox('<?php echo make_url('hide_infobox', array('key' => $key)); ?>', '<?php echo $key; ?>');return false;">
                    <div class="close_me">
                        <input type="checkbox" value="1" name="dont_show" id="close_me_<?php echo $key; ?>"></input>
                        <label for="close_me_<?php echo $key; ?>"><?php echo __("Don't show this again"); ?></label>
                        <input type="submit" value="<?php echo $button_label; ?>"></input>
                    </div>
                </form>
                <div style="display: none;" id="infobox_<?php echo $key; ?>_indicator">
                    <?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')); ?>
                    <?php echo __('Updating, please wait ...'); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
