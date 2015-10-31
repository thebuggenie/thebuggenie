<?php
    $tbg_response->setTitle(__('Configure data types'));
    $tbg_response->addStylesheet(make_url('asset_css_unthemed', array('css' => 'spectrum.css')));
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0 class="configuration_page">
    <tr>
        <?php include_component('leftmenu', array('selected_section' => 4)); ?>
        <td valign="top" style="padding-left: 15px;">
            <div style="width: 730px;" id="config_issuefields">
                <h3><?php echo __('Configure issue fields'); ?></h3>
                <div class="content faded_out">
                    <p><?php echo __('Edit built-in and custom issue fields and values here. Remember that the issue fields visibility (in the issue view or during reporting) is decided by the %issuetype_scheme in use by the project.', array('%issuetype_scheme' => link_tag(make_url('configure_issuetypes_schemes'), __('Issuetype scheme')))); ?></p>
                </div>
                <div class="lightyellowbox" style="margin: 15px 0 10px 0; position: relative;">
                    <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_add_customtype'); ?>" onsubmit="TBG.Config.Issuefields.Custom.add('<?php echo make_url('configure_issuefields_add_customtype'); ?>');return false;" id="add_custom_type_form">
                        <div style="position: absolute; right: 15px; top: 15px;">
                            <input type="submit" value="<?php echo __('Add issue field'); ?>" style="font-weight: normal; font-size: 14px;" id="add_custom_type_button">
                            <?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'add_custom_type_indicator')); ?>
                        </div>
                        <label for="new_custom_field_name" style="width: 150px; display: inline-block;"><?php echo __('Add new issue field'); ?></label>
                        <input type="text" name="name" id="new_custom_field_name" style="width: 250px;">
                        <br style="clear: both;">
                        <label for="new_custom_field_name" style="width: 150px; display: inline-block;"><?php echo __('Field type'); ?></label>
                        <select id="new_custom_field_type" name="field_type" style="width: 400px;">
                            <?php foreach (\thebuggenie\core\entities\CustomDatatype::getFieldTypes() as $type => $description): ?>
                                <option value="<?php echo $type; ?>"><?php echo $description; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <br style="clear: both;">
                    </form>
                </div>
                <h5><?php echo __('Existing issue fields'); ?></h5>
                <?php foreach ($builtin_types as $type_key => $type): ?>
                    <div class="greybox" style="margin: 5px 0 0 0;">
                        <button class="button button-silver" onclick="TBG.Config.Issuefields.Options.show('<?php echo make_url('configure_issuefields_getoptions', array('type' => $type_key)); ?>', '<?php echo $type_key; ?>');"" style="float: right; margin-left: 5px;"><?php echo __('Edit'); ?></button>
                        <?php echo image_tag('spinning_32.gif', array('style' => 'float: right; margin-left: 5px; display: none;', 'id' => $type_key . '_indicator')); ?>
                        <div class="header"><a href="javascript:void(0);" onclick="TBG.Config.Issuefields.Options.show('<?php echo make_url('configure_issuefields_getoptions', array('type' => $type_key)); ?>', '<?php echo $type_key; ?>');"><?php echo $type['description']; ?></a>&nbsp;<span class="faded_out dark" style="font-weight: normal; font-size: 12px;"><?php echo $type['key']; ?></span></div>
                        <div class="content" id="<?php echo $type_key; ?>_content" style="display: none;"> </div>
                    </div>
                <?php endforeach; ?>
                <div id="custom_types_list">
                    <?php foreach ($custom_types as $type_key => $type): ?>
                        <?php include_component('issuefields_customtype', array('type_key' => $type_key, 'type' => $type)); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </td>
    </tr>
</table>
<script>
</script>
