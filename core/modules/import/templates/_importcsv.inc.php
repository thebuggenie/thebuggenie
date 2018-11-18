<div class="backdrop_box large">
    <div class="backdrop_detail_header">
        <span><?= __('Import content from CSV'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <form accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" method="post" id="import_csv_form" onsubmit="TBG.Config.Import.importCSV('<?= make_url('import_do_import_csv', array('type' => $type)); ?>');return false;">
        <div id="backdrop_detail_content" class="backdrop_detail_content">
            <div class="header">
                <?= __('Importing %type', array('%type' => __($type))); ?>
            </div>
            <p class="faded_out"><?= __('By default a dry-run will be used so any errors in your data can be found and fixed before importing proper. To turn off the dry-run mode, uncheck the box below.'); ?></p>
            <div class="rounded_box borderless iceblue" id="csv_import_indicator" style="padding: 5px; margin: 5px; display: none">
                <?= image_tag('spinning_16.gif'); ?> <?= __('Please wait, this may take a few minutes')?>
            </div>
            <div class="rounded_box borderless red" id="csv_import_error" style="padding: 5px; margin: 5px; display: none">
                <b><?= __('There was an error importing your data:'); ?></b>
                <div id="csv_import_error_detail"></div>
            </div>
            <textarea name="csv_data" class="csv_import_data_box"></textarea>
        </div>
        <div class="backdrop_details_submit" id="csv_import_control">
            <span class="explanation">
                <input type="checkbox" class="fancycheckbox" name="csv_dry_run" id="csv_dry_run" checked="checked"><label for="csv_dry_run"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Test the import (dry-run)'); ?></label>
            </span>
            <div class="submit_container"><input type="submit" value="<?= __('Import'); ?>"></div>
        </div>
    </form>
</div>
