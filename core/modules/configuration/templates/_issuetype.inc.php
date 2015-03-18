<?php if (!isset($scheme)): ?>
<div class="greybox" style="margin: 5px 0 0 0;" id="issuetype_<?php echo $type->getID(); ?>_box">
<?php else: ?>
<div class="<?php if ($scheme->isSchemeAssociatedWithIssuetype($type)): ?>greenbox<?php else: ?>greybox<?php endif; ?>" style="margin: 5px 0 0 0;" id="issuetype_<?php echo $type->getID(); ?>_box">
<?php endif; ?>
    <?php echo image_tag('spinning_32.gif', array('style' => 'float: right; margin-left: 5px; display: none;', 'id' => 'issuetype_' . $type->getID() . '_indicator')); ?>
    <div class="header" style="font-size: 1.1em;">
        <span id="issuetype_<?php echo $type->getID(); ?>_name_span"><?php echo $type->getName(); ?></span>&nbsp;
        <?php if (isset($scheme)): ?>
            <?php if (!$scheme->isCore()): ?>
                <a href="#" onclick="TBG.Config.Issuetype.toggleForScheme('<?php echo make_url('configure_issuetypes_enable_issuetype_for_scheme', array('id' => $type->getID(), 'scheme_id' => $scheme->getID())); ?>', <?php echo $type->getID(); ?>, <?php echo $scheme->getID(); ?>, 'enable');return false;"<?php if ($scheme->isSchemeAssociatedWithIssuetype($type)): ?> style="display: none;"<?php endif; ?> class="issuetype_scheme_associate_link" id="type_toggle_<?php echo $type->getID(); ?>_enable"><?php echo __('Enable issue type for this scheme'); ?></a>
                <a href="#" onclick="TBG.Config.Issuetype.toggleForScheme('<?php echo make_url('configure_issuetypes_disable_issuetype_for_scheme', array('id' => $type->getID(), 'scheme_id' => $scheme->getID())); ?>', <?php echo $type->getID(); ?>, <?php echo $scheme->getID(); ?>, 'disable');return false;"<?php if (!$scheme->isSchemeAssociatedWithIssuetype($type)): ?> style="display: none;"<?php endif; ?> class="issuetype_scheme_associate_link" id="type_toggle_<?php echo $type->getID(); ?>_disable"><?php echo __('Disable issue type for this scheme'); ?></a>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (!isset($scheme) || !$scheme->isCore()): ?>
            <?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; float: right; display: none;', 'id' => 'delete_issuetype_'.$type->getID().'_indicator')); ?>
            <div class="button-group" style="margin-top: -3px; float: right;">
                <a class="button button-silver button-icon" title="<?php echo __('Show / edit issue type settings'); ?>" href="javascript:void(0);" onclick="$('edit_issuetype_<?php echo $type->getID(); ?>_form').toggle();$('issuetype_<?php echo $type->getID(); ?>_info').toggle();" class="image"><?php echo image_tag('icon_edit.png'); ?></a>
                <?php if (isset($scheme)): ?>
                    <a class="button button-icon button-silver" title="<?php echo __('Show / edit available choices'); ?>" href="javascript:void(0);" onclick="TBG.Config.Issuetype.showOptions('<?php echo make_url('configure_issuetypes_getoptions_for_scheme', array('id' => $type->getID(), 'scheme_id' => $scheme->getID())); ?>', <?php echo $type->getID(); ?>);" class="image"><?php echo image_tag('action_dropdown_small.png'); ?></a>
                <?php endif; ?>
                <button class="button button-silver button-icon" title="<?php echo __('Remove issuetype'); ?>" onclick="<?php if (!$type->isAssociatedWithAnySchemes()): ?>TBG.Main.Helpers.Dialog.show('<?php echo __('Delete this issue type?'); ?>', '<?php echo __('Do you really want to delete this issue type? Issues with this issue type will be unavailable.').'<br><b>'.__('This action cannot be reverted').'</b>'; ?>', {yes: {click: function() {TBG.Config.Issuetype.remove('<?php echo make_url('configure_issuetypes_delete', array('id' => $type->getID())); ?>', <?php echo $type->getID(); ?>);}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});<?php else: ?>TBG.Main.Helpers.Message.error('<?php echo __('Cannot remove this issue type'); ?>', '<?php echo __('Issue types associated with an issue type scheme cannot be removed'); ?>');<?php endif; ?>"><?php echo image_tag('icon_delete.png'); ?></button>
            </div>
        <?php endif; ?>
    </div>
    <?php if (!isset($scheme)): ?>
        <div id="issuetype_<?php echo $type->getID(); ?>_info" class="issuetype_description">
            <b><?php echo __('Description'); ?>:</b>&nbsp;<span id="issuetype_<?php echo $type->getID(); ?>_description_span"><?php echo $type->getDescription(); ?></span><br>
        </div>
    <?php endif; ?>
    <?php if (!isset($scheme)): ?>
        <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuetypes_update_issuetype', array('id' => $type->getID())); ?>" onsubmit="TBG.Config.Issuetype.update('<?php echo make_url('configure_issuetypes_update_issuetype', array('id' => $type->getID())); ?>', <?php echo $type->getID(); ?>);return false;" id="edit_issuetype_<?php echo $type->getID(); ?>_form" style="display: none;">
    <?php elseif (!$scheme->isCore()): ?>
        <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuetypes_update_issuetype_for_scheme', array('id' => $type->getID(), 'scheme_id' => $scheme->getID())); ?>" onsubmit="TBG.Config.Issuetype.update('<?php echo make_url('configure_issuetypes_update_issuetype_for_scheme', array('id' => $type->getID(), 'scheme_id' => $scheme->getID())); ?>', <?php echo $type->getID(); ?>);return false;" id="edit_issuetype_<?php echo $type->getID(); ?>_form" style="display: none;">
    <?php endif; ?>
            <div class="rounded_box white borderless" style="clear: both; margin: 5px; font-size: 12px;">
            <table cellpadding="0" cellspacing="0">
                <?php if (!isset($scheme)): ?>
                    <tr>
                        <td style="vertical-align: top; padding-top: 5px;"><label for="issuetype_<?php echo $type->getID(); ?>_name"><?php echo __('Name'); ?></label></td>
                        <td><input type="text" name="name" id="issuetype_<?php echo $type->getID(); ?>_name" value="<?php echo $type->getName(); ?>" style="width: 300px;"><br></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding-top: 5px;"><label for="issuetype_<?php echo $type->getID(); ?>_description"><?php echo __('Description'); ?></label></td>
                        <td>
                            <input type="text" name="description" id="issuetype_<?php echo $type->getID(); ?>_description" value="<?php echo $type->getDescription(); ?>" style="width: 600px;">
                            <div class="faded_out" style="margin-bottom: 10px; padding: 2px; font-size: 12px;"><?php echo __('Users see this description when choosing an issue type to report'); ?>.</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding-top: 5px;"><label for="issuetype_<?php echo $type->getID(); ?>_icon"><?php echo __('Issue type'); ?></label></td>
                        <td>
                            <select name="icon" id="issuetype_<?php echo $type->getID(); ?>_icon">
                                <?php foreach ($icons as $icon => $description): ?>
                                    <option value="<?php echo $icon; ?>"<?php if ($type->getIcon() == $icon): ?> selected<?php endif; ?>><?php echo $description; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="faded_out" style="margin-bottom: 10px; padding: 2px; font-size: 12px;"><?php echo __('Select what kind of issue type this is'); ?></div>
                        </td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <?php if (!$scheme->isCore()): ?>
                            <td style="vertical-align: top; padding-top: 5px;"><label for="issuetype_<?php echo $type->getID(); ?>_reportable"><?php echo __('Reportable'); ?></label></td>
                        <?php endif; ?>
                        <td style="padding-left: 2px;">
                            <?php if ($scheme->isCore()): ?>
                                <?php echo ($scheme->isIssuetypeReportable($type)) ? __('Users can report new issues with this issue type') : __('Users cannot report new issues with this issue type, but may choose it when editing an issue'); ?>
                            <?php else: ?>
                                <select name="reportable" id="issuetype_<?php echo $type->getID(); ?>_reportable" style="width:100%">
                                    <option value="1"<?php if ($scheme->isIssuetypeReportable($type)): ?> selected<?php endif; ?>><?php echo __('Users can report new issues with this issue type'); ?></option>
                                    <option value="0"<?php if (!$scheme->isIssuetypeReportable($type)): ?> selected<?php endif; ?>><?php echo __('Users cannot report new issues with this issue type, but may choose it when editing an issue'); ?></option>
                                </select>
                                <div class="faded_out" style="margin-bottom: 10px; padding: 2px; font-size: 12px;"><?php echo __('Whether this issue type is enabled for reporting or not'); ?>.</div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <?php if (!$scheme->isCore()): ?>
                            <td style="vertical-align: top; padding-top: 5px;"><label for="issuetype_<?php echo $type->getID(); ?>_redirect"><?php echo __('Redirect'); ?></label></td>
                        <?php endif; ?>
                        <td style="padding-left: 2px;">
                            <?php if ($scheme->isCore()): ?>
                                <?php echo ($scheme->isIssuetypeRedirectedAfterReporting($type)) ? __('The user is redirected to the reported issue after it has been reported') : __('A blank "%report_issue" page with a link to the reported issue at the top will be shown after the issue is reported', array('%report_issue' => __('Report issue'))); ?>
                            <?php else: ?>
                                <select name="redirect_after_reporting" id="issuetype_<?php echo $type->getID(); ?>_redirect" style="width:100%">
                                    <option value="1"<?php if ($scheme->isIssuetypeRedirectedAfterReporting($type)): ?> selected<?php endif; ?>><?php echo __('The user is redirected to the reported issue after it has been reported'); ?></option>
                                    <option value="0"<?php if (!$scheme->isIssuetypeRedirectedAfterReporting($type)): ?> selected<?php endif; ?>><?php echo __('A blank "%report_issue" page with a link to the reported issue at the top will be shown after the issue is reported', array('%report_issue' => __('Report issue'))); ?></option>
                                </select>
                                <div class="faded_out" style="margin-bottom: 10px; padding: 2px; font-size: 12px;"><?php echo __('Whether to forward the user to the reported issue after it has been reported'); ?>.</div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
            <?php if (!isset($scheme) || !$scheme->isCore()): ?>
                <input type="submit" value="<?php echo __('Update details'); ?>" style="font-weight: bold; font-size: 13px;">
                <?php echo __('%update_details or %cancel', array('%update_details' => '', '%cancel' => '<a href="javascript:void(0);" onclick="$(\'edit_issuetype_' . $type->getID() . '_form\').toggle();$(\'issuetype_' . $type->getID() . '_info\').toggle();"><b>' . __('cancel') . '</b></a>')); ?>
            <?php endif; ?>
        </div>
    <?php if (!isset($scheme) || !$scheme->isCore()): ?>
        </form>
        <?php echo image_tag('spinning_20.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'edit_issuetype_' . $type->getID() . '_indicator')); ?>
    <?php endif; ?>
    <div class="content" id="issuetype_<?php echo $type->getID(); ?>_content" style="display: none;"> </div>
</div>
