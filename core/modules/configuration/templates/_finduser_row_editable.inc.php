<?php if ($user->isScopeConfirmed()): ?>
    <form action="<?php echo make_url('configure_users_update_user', array('user_id' => $user->getID())); ?>" method="post" onsubmit="TBG.Config.User.update('<?php echo make_url('configure_users_update_user', array('user_id' => $user->getID())); ?>', '<?php echo $user->getID(); ?>');return false;" id="edit_user_<?php echo $user->getID(); ?>_form" class="fullpage_backdrop">
        <div class="fullpage_backdrop_content backdrop_box large">
            <div class="backdrop_detail_header"><?php echo __('Edit user'); ?></div>
            <div class="backdrop_detail_content">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 150px;"><label for="username_<?php echo $user->getID(); ?>"><?php echo __('Username'); ?></label></td>
                        <td><?php if (\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): echo $user->getUsername(); else: ?><input type="text" name="username" id="username_<?php echo $user->getID(); ?>" style="width: 120px;" value="<?php echo $user->getUsername(); ?>"><?php endif; ?></td>
                        <td><label for="activated_<?php echo $user->getID(); ?>_yes"><?php echo __('Activated'); ?></label></td>
                        <td valign="middle">
                            <?php if (\thebuggenie\core\framework\Context::getScope()->isDefault()): ?>
                                <input type="radio" name="activated" id="activated_<?php echo $user->getID(); ?>_yes" value="1"<?php if ($user->isActivated()): ?> checked<?php endif; ?>>
                                <label for="activated_<?php echo $user->getID(); ?>_yes" style="font-weight: normal;"><?php echo __('Yes'); ?></label>&nbsp;
                                <input type="radio" name="activated" id="activated_<?php echo $user->getID(); ?>_no" value="0"<?php if (!$user->isActivated()): ?> checked<?php endif; ?>>
                                <label for="activated_<?php echo $user->getID(); ?>_no" style="font-weight: normal;"><?php echo __('No'); ?></label>
                            <?php else: ?>
                                <?php echo ($user->isActivated()) ? __('Yes') : __('No'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="realname_<?php echo $user->getID(); ?>"><?php echo __('Real name'); ?></label></td>
                        <td><?php if (\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): if ($user->getRealname() == null): echo '-'; else: echo $user->getRealname(); endif; else: ?><input type="text" name="realname" id="realname_<?php echo $user->getID(); ?>" style="width: 220px;" value="<?php echo $user->getRealname(); ?>"><?php endif; ?></td>
                        <td><label for="enabled_<?php echo $user->getID(); ?>_yes"><?php echo __('Enabled'); ?></label></td>
                        <td valign="middle">
                            <?php if (\thebuggenie\core\framework\Context::getScope()->isDefault()): ?>
                                <input type="radio" name="enabled" id="enabled_<?php echo $user->getID(); ?>_yes" value="1"<?php if ($user->isEnabled()): ?> checked<?php endif; ?>>
                                <label for="enabled_<?php echo $user->getID(); ?>_yes" style="font-weight: normal;"><?php echo __('Yes'); ?></label>&nbsp;
                                <input type="radio" name="enabled" id="enabled_<?php echo $user->getID(); ?>_no" value="0"<?php if (!$user->isEnabled()): ?> checked<?php endif; ?>>
                                <label for="enabled_<?php echo $user->getID(); ?>_no" style="font-weight: normal;"><?php echo __('No'); ?></label>
                            <?php else: ?>
                                <?php echo ($user->isEnabled()) ? __('Yes') : __('No'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="buddyname_<?php echo $user->getID(); ?>"><?php echo __('Nickname'); ?></label></td>
                        <td colspan="3"><?php if (\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): if ($user->getNickname() == null): echo '-'; else: echo $user->getNickname(); endif; else: ?><input type="text" name="nickname" id="nickname_<?php echo $user->getID(); ?>" style="width: 220px;" value="<?php echo $user->getNickname(); ?>"><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td><label for="email_<?php echo $user->getID(); ?>"><?php echo __('Email address'); ?></label></td>
                        <td><?php if (\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): if ($user->getEmail() == null): echo '-'; else: echo $user->getEmail(); endif; else: ?><input type="text" name="email" id="email_<?php echo $user->getID(); ?>" style="width: 220px;" value="<?php echo $user->getEmail(); ?>"><?php endif; ?></td>
                        <td><label for="user_<?php echo $user->getID(); ?>_group"><?php echo __('In group'); ?></label></td>
                        <td>
                            <select name="group" id="user_<?php echo $user->getID(); ?>_group">
                                <?php foreach (\thebuggenie\core\entities\Group::getAll() as $group): ?>
                                    <option value="<?php echo $group->getID(); ?>"<?php if ($user->getGroupID() == $group->getID()): ?> selected<?php endif; ?>><?php echo $group->getName(); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding-top: 4px;"><label for="homepage_<?php echo $user->getID(); ?>"><?php echo __('Homepage'); ?></label></td>
                        <td style="vertical-align: top;" colspan="3"><input type="text" name="homepage" id="homepage_<?php echo $user->getID(); ?>" style="width: 250px;" value="<?php echo $user->getHomepage(); ?>"></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding-top: 4px; padding-top: 15px;" colspan="4">
                            <?php if (\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
                                <?php echo __('The password setting, along with a number of other settings for this user, have been disabled due to use of an alternative authentictation mechanism'); ?>
                            <?php else: ?>
                                <input onchange="if($(this).checked) { $('edit_user_<?php echo $user->getID(); ?>_password_container').hide(); $('new_password_<?php echo $user->getID(); ?>_1').disable(); $('new_password_<?php echo $user->getID(); ?>_2').disable(); }" type="radio" name="password_action" value="leave" id="password_<?php echo $user->getID(); ?>_leave" checked><label for="password_<?php echo $user->getID(); ?>_leave"><?php echo __("Don't change the password"); ?></label>
                                <input onchange="if($(this).checked) { $('edit_user_<?php echo $user->getID(); ?>_password_container').show(); $('new_password_<?php echo $user->getID(); ?>_1').enable(); $('new_password_<?php echo $user->getID(); ?>_2').enable(); }" type="radio" name="password_action" value="change" id="password_<?php echo $user->getID(); ?>_change"><label for="password_<?php echo $user->getID(); ?>_change"><?php echo __("Input new password"); ?></label>
                                <input onchange="if($(this).checked) { $('edit_user_<?php echo $user->getID(); ?>_password_container').hide(); $('new_password_<?php echo $user->getID(); ?>_1').disable(); $('new_password_<?php echo $user->getID(); ?>_2').disable(); }" type="radio" name="password_action" value="random" id="password_<?php echo $user->getID(); ?>_random"><label for="password_<?php echo $user->getID(); ?>_random"><?php echo __("Generate random new password"); ?></label>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr id="edit_user_<?php echo $user->getID(); ?>_password_container" style="display: none;">
                        <?php if (\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
                            <td colspan="2">
                            </td>
                        <?php else: ?>
                            <td style="vertical-align: top; padding-top: 4px; line-height: 20px;">
                                <label for="new_password_<?php echo $user->getID(); ?>_1"><?php echo __('New password'); ?></label><br>
                                <label for="new_password_<?php echo $user->getID(); ?>_2"><?php echo __('Repeat password'); ?></label>
                            </td>
                            <td style="vertical-align: top; line-height: 20px;" colspan="3">
                                <input type="password" name="new_password_1" id="new_password_<?php echo $user->getID(); ?>_1" style="width: 250px;" value="" disabled><br>
                                <input type="password" name="new_password_2" id="new_password_<?php echo $user->getID(); ?>_2" style="width: 250px;" value="" disabled>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding-top: 4px;"><label><?php echo __('Member of team(s)'); ?></label></td>
                        <td colspan="3">
                            <?php foreach (\thebuggenie\core\entities\Team::getAll() as $team): ?>
                                <div class="teamlist_container">
                                    <input type="checkbox" name="teams[<?php echo $team->getID(); ?>]" id="team_<?php echo $user->getID(); ?>_<?php echo $team->getID(); ?>" value="<?php echo $team->getID(); ?>"<?php if ($user->isMemberOfTeam($team)): ?> checked<?php endif; ?>>
                                    <label for="team_<?php echo $user->getID(); ?>_<?php echo $team->getID(); ?>" style="font-weight: normal;"><?php echo $team->getName(); ?></label>&nbsp;&nbsp;
                                </div>
                            <?php endforeach; ?>
                            <?php if (count(\thebuggenie\core\entities\Team::getAll()) == 0): ?>
                                <?php echo __('No teams exist'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding-top: 4px;"><label><?php echo __('Member of client(s)'); ?></label></td>
                        <td colspan="3">
                            <?php foreach (\thebuggenie\core\entities\Client::getAll() as $client): ?>
                                <div>
                                    <input type="checkbox" name="clients[<?php echo $client->getID(); ?>]" id="client_<?php echo $user->getID(); ?>_<?php echo $client->getID(); ?>" value="<?php echo $client->getID(); ?>"<?php if ($user->isMemberOfClient($client)): ?> checked<?php endif; ?>>
                                    <label for="client_<?php echo $user->getID(); ?>_<?php echo $client->getID(); ?>" style="font-weight: normal;"><?php echo $client->getName(); ?></label>&nbsp;&nbsp;
                                </div>
                            <?php endforeach; ?>
                            <?php if (count(\thebuggenie\core\entities\Client::getAll()) == 0): ?>
                                <?php echo __('No clients exist'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: right; font-size: 13px; padding-top: 10px;">
                            <div style="padding: 10px 0 10px 0; display: none;" id="edit_user_<?php echo $user->getID(); ?>_indicator"><span style="float: left;"><?php echo image_tag('spinning_16.gif'); ?>&nbsp;<?php echo __('Please wait'); ?></span></div>
                            <input type="submit" value="<?php echo __('Update user'); ?>" style="font-size: 13px; font-weight: bold;">
                            <?php echo __('or %cancel', array('%cancel' => javascript_link_tag('<b>'.__('cancel').'</b>', array('onclick' => "$('user_".$user->getID()."_edit_tr').hide();")))); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </form>
<?php endif; ?>
