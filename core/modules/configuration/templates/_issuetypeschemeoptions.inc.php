<?php if (!$scheme->isCore()): ?>
    <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuetypes_update_choices_for_scheme', array('id' => $issuetype->getID(), 'scheme_id' => $scheme->getID())); ?>" onsubmit="TBG.Config.Issuetype.Choices.update('<?php echo make_url('configure_issuetypes_update_choices_for_scheme', array('id' => $issuetype->getID(), 'scheme_id' => $scheme->getID())); ?>', <?php echo $issuetype->getID(); ?>);return false;" id="update_<?php echo $issuetype->getID(); ?>_choices_form">
<?php endif; ?>
    <div class="rounded_box white borderless" style="margin: 5px; font-size: 12px;">
        <div class="header_div" style="margin-top: 0;">
            <?php echo __('Available issue fields'); ?>
        </div>
        <table style="width: 100%;" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th style="padding: 2px;"><?php echo __('Field'); ?></th>
                    <th style="padding: 2px; text-align: center;" class="highlighted_column"><?php echo __('Visible'); ?></th>
                    <th style="padding: 2px; text-align: center;"><?php echo __('Reportable'); ?></th>
                    <th style="padding: 2px; text-align: center;"><?php echo __('Additional'); ?></th>
                    <th style="padding: 2px; text-align: center;"><?php echo __('Required'); ?></th>
                </tr>
            </thead>
            <tbody id="<?php echo $issuetype->getID(); ?>_list">
                <?php foreach ($builtinfields as $item): ?>
                    <?php include_component('issuetypeschemeoption', array('issuetype' => $issuetype, 'scheme' => $scheme, 'key' => $item, 'item' => $item, 'visiblefields' => $visiblefields)); ?>
                <?php endforeach; ?>
                <?php if (count($customtypes) > 0): ?>
                    <?php foreach ($customtypes as $key => $item): ?>
                        <?php include_component('issuetypeschemeoption', array('issuetype' => $issuetype, 'scheme' => $scheme, 'key' => $key, 'item' => $item, 'visiblefields' => $visiblefields)); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if (!$scheme->isCore()): ?>
            <div style="margin: 10px 0 0 0; height: 25px;">
                <div style="float: left; font-size: 13px; padding-top: 2px; font-weight: bold;"><?php echo __('Click "%save" to save your changes', array('%save' => __('Save'))); ?></div>
                <input type="submit" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
                <span id="update_<?php echo $issuetype->getID(); ?>_choices_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
            </div>
        <?php endif; ?>
    </div>
<?php if (!$scheme->isCore()): ?>
    </form>
<?php endif; ?>
