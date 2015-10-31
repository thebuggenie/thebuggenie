<div class="greybox" style="padding: 0; margin-top: 5px;" id="clientbox_<?php echo $client->getID(); ?>">
    <div style="padding: 5px; position: relative;">
        <?php echo image_tag('client_large.png', array('style' => 'float: left; margin-right: 5px;')); ?>
        <div style="position: absolute; right: 12px; top: 12px;">
            <button class="button button-silver dropper"><?php echo __('Actions'); ?></button>
            <ul style="position: absolute; font-size: 1.1em; width: 200px; top: 23px; margin-top: 0; right: 0; text-align: right; z-index: 1000;" class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="jQuery(this).prev().toggleClass('button-pressed');jQuery(this).toggle();">
                <li>
                    <?php echo javascript_link_tag(__('Add member(s) to this client'), array('onclick' => '$(\'addmember_client_'.$client->getID().'\').toggle(\'block\');')); ?>
                </li>
                <li>
                    <?php echo javascript_link_tag(__('List users in this client'), array('onclick' => 'TBG.Config.Client.showMembers(\''.make_url('configure_users_get_client_members', array('client_id' => $client->getID())).'\', '.$client->getID().');')); ?>
                </li>
                <li>
                    <?php echo javascript_link_tag(__('Edit this client'), array('onclick' => '$(\'edit_client_'.$client->getID().'\').toggle();')); ?>
                </li>
                <li>
                    <?php echo javascript_link_tag(__('Delete this client'), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to delete this client?')."', '".__('If you delete this client, any projects this client is assigned to will be set to having no client')."', {yes: {click: function() {TBG.Config.Client.remove('".make_url('configure_users_delete_client', array('client_id' => $client->getID()))."', {$client->getID()}); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});")); ?>
                </li>
            </ul>
        </div>
        <p class="clientbox_header"><?php echo $client->getName(); ?></p>
        <p class="clientbox_membercount"><?php echo __('ID: %id', array('%id' => $client->getID())); ?> - <?php echo __('%number_of member(s)', array('%number_of' => '<span id="client_'.$client->getID().'_membercount">'.$client->getNumberOfMembers().'</span>')); ?></p>
        <div class="fullpage_backdrop" style="margin: 5px; display: none;" id="edit_client_<?php echo $client->getID(); ?>">
            <div class="fullpage_backdrop_content backdrop_box medium">
                <div class="backdrop_detail_header"><?php echo __('Edit client settings'); ?></div>
                <div class="backdrop_detail_content">
                    <form id="edit_client_<?php echo $client->getID(); ?>_form" action="<?php echo make_url('configure_users_edit_client', array('client_id' => $client->getID())); ?>" method="post" accept-charset="<?php echo \thebuggenie\core\framework\Settings::getCharset(); ?>" onsubmit="TBG.Config.Client.update('<?php echo make_url('configure_users_edit_client', array('client_id' => $client->getID())); ?>', '<?php echo $client->getID(); ?>');return false;">
                    <input type="hidden" name="client_id" value="<?php echo $client->getID(); ?>">
                        <div id="edit_client">
                            <table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
                                <tr>
                                    <td style="width: 120px;">
                                        <label for="client_<?php echo $client->getID(); ?>_new_name"><?php echo __('Client name'); ?></label>
                                    </td>
                                    <td>
                                        <input style="width: 300px;" type="text" id="edit_name" name="client_name" value="<?php echo $client->getName(); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="edit_client_<?php echo $client->getID(); ?>_new_email"><?php echo __('Email Address'); ?></label>
                                    </td>
                                    <td>
                                        <input style="width: 300px;" type="text" id="client_email" name="client_email" value="<?php echo $client->getEmail(); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="edit_client_<?php echo $client->getID(); ?>_new_website"><?php echo __('Website'); ?></label>
                                    </td>
                                    <td>
                                        <input style="width: 300px;" type="text" id="client_website" name="client_website" value="<?php echo $client->getWebsite(); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="edit_client_<?php echo $client->getID(); ?>_new_telephone"><?php echo __('Telephone number'); ?></label>
                                    </td>
                                    <td>
                                        <input style="width: 300px;" type="text" id="client_telephone" name="client_telephone" value="<?php echo $client->getTelephone(); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="edit_client_<?php echo $client->getID(); ?>_new_fax"><?php echo __('Fax number'); ?></label>
                                    </td>
                                    <td>
                                        <input style="width: 300px;" type="text" id="client_fax" name="client_fax" value="<?php echo $client->getFax(); ?>">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    <div style="text-align: right;">
                        <input type="submit" id="edit_client_<?php echo $client->getID(); ?>_save_button" style="padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>"> <?php echo javascript_link_tag(__('or cancel'), array('onclick' => '$(\'edit_client_'.$client->getID().'\').toggle();')); ?>
                    </div>
                    </form>
                    <table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="edit_client_<?php echo $client->getID(); ?>_indicator">
                        <tr>
                            <td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
                            <td style="padding: 0px; text-align: left;"><?php echo __('Saving, please wait'); ?>...</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <?php include_component('main/identifiableselector', array(    'html_id'        => "addmember_client_{$client->getID()}",
                                                                'header'             => __('Add a member to this client'),
                                                                'callback'             => "TBG.Config.Client.addMember('".make_url('configure_users_add_client_member', array('client_id' => $client->getID(), 'user_id' => '%identifiable_value'))."', ".$client->getID().", '%identifiable_value');$('addmember_client_{$client->getID()}').hide();",
                                                                'base_id'            => "addmember_client_{$client->getID()}",
                                                                'include_teams'        => false,
                                                                'style'                => array('right' => '0', 'top' => '30px'),
                                                                'allow_clear'        => false,
                                                                'allow_close'        => true,
                                                                'style'                => array('right' => '12px', 'top' => '35px'),
                                                                'absolute'            => true)); ?>
    </div>
    <div class="rounded_box lightgrey" style="margin-bottom: 5px; display: none;" id="client_members_<?php echo $client->getID(); ?>_container">
        <div class="dropdown_header"><?php echo __('Users in this client'); ?></div>
        <table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="client_members_<?php echo $client->getID(); ?>_indicator">
            <tr>
                <td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
                <td style="padding: 0px; text-align: left;"><?php echo __('Retrieving members, please wait'); ?>...</td>
            </tr>
        </table>
        <div id="client_members_<?php echo $client->getID(); ?>_list"></div>
    </div>
</div>
