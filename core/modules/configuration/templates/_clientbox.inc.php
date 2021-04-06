<div class="greybox" style="padding: 0; margin-top: 5px;" id="clientbox_<?= $client->getID(); ?>">
    <div style="padding: 5px; position: relative;">
        <?= image_tag('client_large.png', array('style' => 'float: left; margin-right: 5px;')); ?>
        <div style="position: absolute; right: 12px; top: 12px;">
            <button class="button button-silver dropper"><?= __('Actions'); ?></button>
            <ul style="position: absolute; font-size: 1.1em; width: 200px; top: 23px; margin-top: 0; right: 0; text-align: right; z-index: 1000;" class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');">
                <li>
                    <?= javascript_link_tag(__('Add member(s) to this client'), array('onclick' => '$(\'addmember_client_'.$client->getID().'\').removeClassName(\'popup_box\');$(\'addmember_client_'.$client->getID().'\').toggle(\'block\');')); ?>
                </li>
                <li>
                    <?= javascript_link_tag(__('List users in this client'), array('onclick' => 'TBG.Config.Client.showMembers(\''.make_url('configure_users_get_client_members', array('client_id' => $client->getID())).'\', '.$client->getID().');')); ?>
                </li>
                <li>
                    <?= javascript_link_tag(__('Edit this client'), array('onclick' => '$(\'edit_client_'.$client->getID().'\').toggle();')); ?>
                </li>
                <li>
                    <?= javascript_link_tag(__('Delete this client'), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to delete this client?')."', '".__('If you delete this client, any projects this client is assigned to will be set to having no client')."', {yes: {click: function() {TBG.Config.Client.remove('".make_url('configure_users_delete_client', array('client_id' => $client->getID()))."', {$client->getID()}); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});")); ?>
                </li>
            </ul>
        </div>
        <p class="clientbox_header"><?= $client->getName(); ?></p>
        <p class="clientbox_membercount"><?= __('ID: %id', array('%id' => $client->getID())); ?> - <?= __('%number_of member(s)', array('%number_of' => '<span id="client_'.$client->getID().'_membercount">'.$client->getNumberOfMembers().'</span>')); ?></p>
        <div class="fullpage_backdrop" style="margin: 5px; display: none;" id="edit_client_<?= $client->getID(); ?>">
            <div class="fullpage_backdrop_content backdrop_box medium">
                <div class="backdrop_detail_header">
                    <span><?= __('Edit client settings'); ?></span>
                    <?= javascript_link_tag(fa_image_tag('times'), ['class' => 'closer', 'onclick' => '$(\'edit_client_'.$client->getID().'\').toggle();']); ?>
                </div>
                <form id="edit_client_<?= $client->getID(); ?>_form" action="<?= make_url('configure_users_edit_client', array('client_id' => $client->getID())); ?>" method="post" accept-charset="<?= \thebuggenie\core\framework\Settings::getCharset(); ?>" onsubmit="TBG.Config.Client.update('<?= make_url('configure_users_edit_client', array('client_id' => $client->getID())); ?>', '<?= $client->getID(); ?>');return false;">
                    <div class="backdrop_detail_content">
                        <input type="hidden" name="client_id" value="<?= $client->getID(); ?>">
                        <div id="edit_client">
                            <table style="clear: both; width: 100%;" class="padded_table" cellpadding=0 cellspacing=0>
                                <tr>
                                    <td style="width: 120px;">
                                        <label for="client_<?= $client->getID(); ?>_new_code"><?= __('Client code'); ?></label>
                                    </td>
                                    <td>
                                        <input style="width: 96%;" type="text" id="edit_code" name="client_code" value="<?= $client->getCode(); ?>" maxlength="255">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 120px;">
                                        <label for="client_<?= $client->getID(); ?>_new_name"><?= __('Client name'); ?></label>
                                    </td>
                                    <td>
                                        <input style="width: 96%;" type="text" id="edit_name" name="client_name" value="<?= $client->getName(); ?>" maxlength="255">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="edit_client_<?= $client->getID(); ?>_new_contact"><?= __('Contact'); ?></label>
                                    </td>
                                    <td>
                                        <input style="width: 96%;" type="text" id="client_contact" name="client_contact" value="<?= $client->getContact(); ?>" maxlength="255">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="edit_client_<?= $client->getID(); ?>_new_title"><?= __('Title'); ?></label>
                                    </td>
                                    <td>
                                        <input style="width: 96%;" type="text" id="client_title" name="client_title" value="<?= $client->getTitle(); ?>" maxlength="255">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="edit_client_<?= $client->getID(); ?>_new_email"><?= __('Email'); ?></label>
                                    </td>
                                    <td>
                                        <input style="width: 96%;" type="text" id="client_email" name="client_email" value="<?= $client->getEmail(); ?>" maxlength="255">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="edit_client_<?= $client->getID(); ?>_new_telephone"><?= __('Phone'); ?></label>
                                    </td>
                                    <td>
                                        <input style="width: 96%;" type="text" id="client_telephone" name="client_telephone" value="<?= $client->getTelephone(); ?>" maxlength="255">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="edit_client_<?= $client->getID(); ?>_new_fax"><?= __('Fax'); ?></label>
                                    </td>
                                    <td>
                                        <input style="width: 96%;" type="text" id="client_fax" name="client_fax" value="<?= $client->getFax(); ?>" maxlength="255">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="edit_client_<?= $client->getID(); ?>_new_address"><?= __('Address'); ?></label>
                                    </td>
                                    <td>
                                        <input style="width: 96%;" type="text" id="client_address" name="client_address" value="<?= $client->getAddress(); ?>" maxlength="255">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="edit_client_<?= $client->getID(); ?>_new_website"><?= __('Website'); ?></label>
                                    </td>
                                    <td>
                                        <input style="width: 96%;" type="text" id="client_website" name="client_website" value="<?= $client->getWebsite(); ?>" maxlength="2083">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="edit_client_<?= $client->getID(); ?>_new_notes"><?= __('Notes'); ?></label>
                                    </td>
                                    <td>
                                        <textarea style="width: 97%; height: 5em;" rows="5" id="client_notes" name="client_notes" maxlength="4096"><?= $client->getNotes(); ?></textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="backdrop_details_submit">
                        <span class="explanation"></span>
                        <div class="submit_container">
                            <button type="submit" id="edit_client_<?= $client->getID(); ?>_save_button"><?= image_tag('spinning_16.gif', ['id' => 'edit_client_' . $client->getID() . '_indicator', 'style' => 'display: none;']) . __('Save'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php include_component('main/identifiableselector', array(
                                                                 'html_id'       => "addmember_client_{$client->getID()}",
                                                                 'header'        => __('Add a member to this client'),
                                                                 'callback'      => "TBG.Config.Client.addMember('".make_url('configure_users_add_client_member', array('client_id' => $client->getID(), 'user_id' => '%identifiable_value'))."', ".$client->getID().", '%identifiable_value');$('addmember_client_{$client->getID()}').hide();$('addmember_client_{$client->getID()}').addClassName('popup_box');",
                                                                 'base_id'       => "addmember_client_{$client->getID()}",
                                                                 'include_teams' => false,
                                                                 'include_users' => true,
                                                                 'allow_clear'   => false,
                                                                 'allow_close'   => true,
                                                                 'style'         => array('display' => 'none'),
                                                                 'absolute'      => false
        )); ?>
        
    </div>
    <div class="rounded_box lightgrey" style="margin-bottom: 5px; display: none;" id="client_members_<?= $client->getID(); ?>_container">
        <div class="dropdown_header"><?= __('Users in this client'); ?></div>
        <table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 96%;" id="client_members_<?= $client->getID(); ?>_indicator">
            <tr>
                <td style="width: 20px; padding: 2px;"><?= image_tag('spinning_20.gif'); ?></td>
                <td style="padding: 0px; text-align: left;"><?= __('Retrieving members, please wait'); ?>...</td>
            </tr>
        </table>
        <div id="client_members_<?= $client->getID(); ?>_list"></div>
    </div>
</div>
