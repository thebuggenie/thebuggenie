<?php

    $tbg_response->addBreadcrumb(__('Clients'), null, tbg_get_breadcrumblinks('client_list'));
    if ($client instanceof \thebuggenie\core\entities\Client)
    {
        $tbg_response->setTitle(__('Client dashboard for %client_name', array('%client_name' => $client->getName())));
        $tbg_response->setPage('client');
        $tbg_response->addBreadcrumb($client->getName(), make_url('client_dashboard', array('client_id' => $client->getID())));
    }
    else
    {
        $tbg_response->setTitle(__('Client dashboard'));
        $tbg_response->addBreadcrumb(__('Client dashboard'));
    }

?>
<div class="client_dashboard">
    <div class="main_area">
        <div class="dashboard_client_info">
            <span class="dashboard_client_header"><?php echo $client->getName(); ?></span>
            <?php if ($tbg_user->canAccessConfigurationPage(\thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_USERS)): ?>
                <div class="project_header_right button-group">
                    <button class="button button-silver dropper first last" id="team_<?php echo $client->getID(); ?>_more_actions"><?= image_tag('spinning_16.gif', ['id' => 'client_members_' . $client->getID() . '_indicator', 'style' => 'display: none']); ?>&nbsp;<?php echo __('Actions'); ?></button>
                    <ul style="margin-top: 28px; font-size: 1.1em;" class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).toggle();">
                        <li><?php echo javascript_link_tag(fa_image_tag('user-plus').__('Add member(s) to this client'), array('onclick' => '$(\'addmember_client_'.$client->getID().'\').toggle(\'block\');')); ?></li>
                        <li class="separator"></li>
                        <li class="delete"><?php echo javascript_link_tag(fa_image_tag('times').__('Delete this client'), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to delete this client?')."', '".__('If you delete this client, then all users in this client will be lose the permissions given via this client')."', {yes: {click: function() {TBG.Config.Team.remove('".make_url('configure_users_delete_client', array('client_id' => $client->getID()))."', {$client->getID()}); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});")); ?></li>
                    </ul>
                </div>
            <?php endif; ?>
            <?php include_component('main/identifiableselector', [
                'html_id'       => "addmember_client_{$client->getID()}",
                'header'        => __('Add a member to this client'),
                'callback'      => "TBG.Config.Client.addMember('".make_url('configure_users_add_client_member', ['client_id' => $client->getID(), 'user_id' => '%identifiable_value'])."', ".$client->getID().", '%identifiable_value');$('addmember_client_{$client->getID()}').hide();",
                'base_id'       => "addmember_client_{$client->getID()}",
                'include_teams' => false,
                'style'         => ['right' => '0', 'top' => '30px'],
                'allow_clear'   => false,
                'allow_close'   => true,
                'style'         => ['right' => '12px', 'top' => '35px'],
                'absolute'      => true
            ]); ?>
            <table>
                <tr>
                    <td style="padding-right: 10px">
                        <b><?php echo __('Website:'); ?></b> <?php if ($client->getWebsite() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><a href="<?php echo $client->getWebsite(); ?>" target="_blank"><?php echo $client->getWebsite(); ?></a><?php endif; ?>
                    </td>
                    <td style="padding: 0px 10px">
                        <b><?php echo __('Email address:'); ?></b> <?php if ($client->getEmail() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><a href="mailto:<?php echo $client->getEmail(); ?>" target="_blank"><?php echo $client->getEmail(); ?></a><?php endif; ?>
                    </td>
                    <td style="padding: 0px 10px">
                        <b><?php echo __('Telephone:'); ?></b> <?php if ($client->getTelephone() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><?php echo $client->getTelephone(); endif; ?>
                    </td>
                    <td style="padding: 0px 10px">
                        <b><?php echo __('Fax:'); ?></b> <?php if ($client->getFax() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><?php echo $client->getFax(); endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="team_dashboard_projects padded">
            <?php include_component('main/projectlist', ['list_mode' => 'client', 'client_id' => $client->getID(), 'admin' => false]); ?>
        </div>
    </div>
    <div class="client_dashboard_users padded">
        <div class="header">
            <?php echo __('Members of %client', array('%client' => $client->getName())); ?>&nbsp;(<span id="client_<?= $client->getID(); ?>_membercount"><?= $client->getNumberOfMembers(); ?></span>)
        </div>
        <div id="client_members_<?= $client->getID(); ?>_list">
            <?= include_component('configuration/clientuserlist', compact('client', 'users')); ?>
        </div>
    </div>
</div>
