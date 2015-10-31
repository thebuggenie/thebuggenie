<div class="backdrop_box large" style="text-align: left;">
    <div class="backdrop_detail_header">
        <?php echo __('Import content from CSV'); ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div style="padding: 5px;">
            <div class="header">
                <?php echo __('Importing %type', array('%type' => __($type))); ?>
            </div>
            <p class="faded_out"><?php echo __('By default a dry-run will be used so any errors in your data can be found and fixed before importing proper. To turn off the dry-run mode, uncheck the box below.'); ?></p>
        </div>
        <div class="rounded_box borderless iceblue" id="csv_import_indicator" style="padding: 5px; margin: 5px; display: none">
            <?php echo image_tag('spinning_16.gif'); ?> <?php echo __('Please wait, this may take a few minutes')?>
        </div>
        <div class="rounded_box borderless red" id="csv_import_error" style="padding: 5px; margin: 5px; display: none">
            <b><?php echo __('There was an error importing your data:'); ?></b>
            <div id="csv_import_error_detail"></div>
        </div>
        <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" method="post" id="import_csv_form" onsubmit="TBG.Config.Import.importCSV('<?php echo make_url('import_do_import_csv', array('type' => $type)); ?>');return false;">
            <textarea name="csv_data" class="csv_import_data_box"></textarea>
            <div class="rounded_box lightgrey" id="csv_import_control" style="padding: 5px; margin: 5px;">
                <div class="csv_import_dry"><input type="checkbox" name="csv_dry_run" id="csv_dry_run" checked="checked"> <label for="csv_dry_run"><?php echo __('Test the import (dry-run)'); ?></label></div>
                <div class="csv_import_go"><input type="submit" value="<?php echo __('Import'); ?>"></div>
            </div>
        </form>
    </div>
    <div class="backdrop_detail_footer">
        <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Close'); ?></a>
    </div>
</div>
