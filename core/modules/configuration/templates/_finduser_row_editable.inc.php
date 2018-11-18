<?php if ($user->isScopeConfirmed()): ?>
    <form action="<?= make_url('configure_users_update_user', array('user_id' => $user->getID())); ?>" method="post" onsubmit="TBG.Config.User.update('<?= make_url('configure_users_update_user', array('user_id' => $user->getID())); ?>', '<?= $user->getID(); ?>');return false;" id="edit_user_<?= $user->getID(); ?>_form" class="fullpage_backdrop">
        <div class="fullpage_backdrop_content backdrop_box large">
            <div class="backdrop_detail_header">
                <span><?= __('Edit user'); ?></span>
                <?= javascript_link_tag(fa_image_tag('times'), ['class' => 'closer', 'onclick' => "$('user_".$user->getID()."_edit_tr').hide();"]); ?>
            </div>
            <div class="backdrop_detail_content">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 150px;"><label for="username_<?= $user->getID(); ?>"><?= __('Username'); ?></label></td>
                        <td><?php if (\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): echo $user->getUsername(); else: ?><input type="text" name="username" id="username_<?= $user->getID(); ?>" style="width: 120px;" value="<?= $user->getUsername(); ?>"><?php endif; ?></td>
                        <td><label for="activated_<?= $user->getID(); ?>_yes"><?= __('Activated'); ?></label></td>
                        <td valign="middle">
                            <?php if (\thebuggenie\core\framework\Context::getScope()->isDefault()): ?>
                                <input type="radio" name="activated" id="activated_<?= $user->getID(); ?>_yes" value="1"<?php if ($user->isActivated()): ?> checked<?php endif; ?>>
                                <label for="activated_<?= $user->getID(); ?>_yes" style="font-weight: normal;"><?= __('Yes'); ?></label>&nbsp;
                                <input type="radio" name="activated" id="activated_<?= $user->getID(); ?>_no" value="0"<?php if (!$user->isActivated()): ?> checked<?php endif; ?>>
                                <label for="activated_<?= $user->getID(); ?>_no" style="font-weight: normal;"><?= __('No'); ?></label>
                            <?php else: ?>
                                <?= ($user->isActivated()) ? __('Yes') : __('No'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="realname_<?= $user->getID(); ?>"><?= __('Real name'); ?></label></td>
                        <td><?php if (\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): if ($user->getRealname() == null): echo '-'; else: echo $user->getRealname(); endif; else: ?><input type="text" name="realname" id="realname_<?= $user->getID(); ?>" style="width: 220px;" value="<?= $user->getRealname(); ?>"><?php endif; ?></td>
                        <td><label for="enabled_<?= $user->getID(); ?>_yes"><?= __('Enabled'); ?></label></td>
                        <td valign="middle">
                            <?php if (\thebuggenie\core\framework\Context::getScope()->isDefault()): ?>
                                <input type="radio" name="enabled" id="enabled_<?= $user->getID(); ?>_yes" value="1"<?php if ($user->isEnabled()): ?> checked<?php endif; ?>>
                                <label for="enabled_<?= $user->getID(); ?>_yes" style="font-weight: normal;"><?= __('Yes'); ?></label>&nbsp;
                                <input type="radio" name="enabled" id="enabled_<?= $user->getID(); ?>_no" value="0"<?php if (!$user->isEnabled()): ?> checked<?php endif; ?>>
                                <label for="enabled_<?= $user->getID(); ?>_no" style="font-weight: normal;"><?= __('No'); ?></label>
                            <?php else: ?>
                                <?= ($user->isEnabled()) ? __('Yes') : __('No'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="buddyname_<?= $user->getID(); ?>"><?= __('Nickname'); ?></label></td>
                        <td colspan="3"><?php if (\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): if ($user->getNickname() == null): echo '-'; else: echo $user->getNickname(); endif; else: ?><input type="text" name="nickname" id="nickname_<?= $user->getID(); ?>" style="width: 220px;" value="<?= $user->getNickname(); ?>"><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td><label for="email_<?= $user->getID(); ?>"><?= __('Email address'); ?></label></td>
                        <td><?php if (\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): if ($user->getEmail() == null): echo '-'; else: echo $user->getEmail(); endif; else: ?><input type="text" name="email" id="email_<?= $user->getID(); ?>" style="width: 220px;" value="<?= $user->getEmail(); ?>"><?php endif; ?></td>
                        <td><label for="user_<?= $user->getID(); ?>_group"><?= __('In group'); ?></label></td>
                        <td>
                            <select name="group" id="user_<?= $user->getID(); ?>_group">
                                <?php foreach (\thebuggenie\core\entities\Group::getAll() as $group): ?>
                                    <option value="<?= $group->getID(); ?>"<?php if ($user->getGroupID() == $group->getID()): ?> selected<?php endif; ?>><?= $group->getName(); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding-top: 4px;"><label for="homepage_<?= $user->getID(); ?>"><?= __('Homepage'); ?></label></td>
                        <td style="vertical-align: top;" colspan="3"><input type="text" name="homepage" id="homepage_<?= $user->getID(); ?>" style="width: 250px;" value="<?= $user->getHomepage(); ?>"></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding-top: 4px; padding-top: 15px;" colspan="4">
                            <?php if (\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
                                <?= __('The password setting, along with a number of other settings for this user, have been disabled due to use of an alternative authentictation mechanism'); ?>
                            <?php else: ?>
                                <input onchange="if($(this).checked) { $('edit_user_<?= $user->getID(); ?>_password_container').hide(); $('new_password_<?= $user->getID(); ?>_1').disable(); $('new_password_<?= $user->getID(); ?>_2').disable(); }" type="radio" name="password_action" value="leave" id="password_<?= $user->getID(); ?>_leave" checked><label for="password_<?= $user->getID(); ?>_leave"><?= __("Don't change the password"); ?></label>
                                <input onchange="if($(this).checked) { $('edit_user_<?= $user->getID(); ?>_password_container').show(); $('new_password_<?= $user->getID(); ?>_1').enable(); $('new_password_<?= $user->getID(); ?>_2').enable(); }" type="radio" name="password_action" value="change" id="password_<?= $user->getID(); ?>_change"><label for="password_<?= $user->getID(); ?>_change"><?= __("Input new password"); ?></label>
                                <input onchange="if($(this).checked) { $('edit_user_<?= $user->getID(); ?>_password_container').hide(); $('new_password_<?= $user->getID(); ?>_1').disable(); $('new_password_<?= $user->getID(); ?>_2').disable(); }" type="radio" name="password_action" value="random" id="password_<?= $user->getID(); ?>_random"><label for="password_<?= $user->getID(); ?>_random"><?= __("Generate random new password"); ?></label>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr id="edit_user_<?= $user->getID(); ?>_password_container" style="display: none;">
                        <?php if (\thebuggenie\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
                            <td colspan="2">
                            </td>
                        <?php else: ?>
                            <td style="vertical-align: top; padding-top: 4px; line-height: 20px;">
                                <label for="new_password_<?= $user->getID(); ?>_1"><?= __('New password'); ?></label><br>
                                <label for="new_password_<?= $user->getID(); ?>_2"><?= __('Repeat password'); ?></label>
                            </td>
                            <td style="vertical-align: top; line-height: 20px;" colspan="3">
                                <input type="password" name="new_password_1" id="new_password_<?= $user->getID(); ?>_1" style="width: 250px;" value="" disabled><br>
                                <input type="password" name="new_password_2" id="new_password_<?= $user->getID(); ?>_2" style="width: 250px;" value="" disabled>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding-top: 4px;"><label><?= __('Member of team(s)'); ?></label></td>
                        <td colspan="3">
                            <?php foreach (\thebuggenie\core\entities\Team::getAll() as $team): ?>
                                <div class="teamlist_container">
                                    <input type="checkbox" class="fancycheckbox" name="teams[<?= $team->getID(); ?>]" id="team_<?= $user->getID(); ?>_<?= $team->getID(); ?>" value="<?= $team->getID(); ?>"<?php if ($user->isMemberOfTeam($team)): ?> checked<?php endif; ?>>
                                    <label for="team_<?= $user->getID(); ?>_<?= $team->getID(); ?>" style="font-weight: normal;"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . $team->getName(); ?></label>&nbsp;&nbsp;
                                </div>
                            <?php endforeach; ?>
                            <?php if (count(\thebuggenie\core\entities\Team::getAll()) == 0): ?>
                                <?= __('No teams exist'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding-top: 4px;"><label><?= __('Member of client(s)'); ?></label></td>
                        <td colspan="3">
                            <?php foreach (\thebuggenie\core\entities\Client::getAll() as $client): ?>
                                <div>
                                    <input type="checkbox" class="fancycheckbox" name="clients[<?= $client->getID(); ?>]" id="client_<?= $user->getID(); ?>_<?= $client->getID(); ?>" value="<?= $client->getID(); ?>"<?php if ($user->isMemberOfClient($client)): ?> checked<?php endif; ?>>
                                    <label for="client_<?= $user->getID(); ?>_<?= $client->getID(); ?>" style="font-weight: normal;"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . $client->getName(); ?></label>&nbsp;&nbsp;
                                </div>
                            <?php endforeach; ?>
                            <?php if (count(\thebuggenie\core\entities\Client::getAll()) == 0): ?>
                                <?= __('No clients exist'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="backdrop_details_submit">
                <span class="explanation"></span>
                <div class="submit_container">
                    <button type="submit" class="button button-silver"><?= image_tag('spinning_16.gif', ['id' => 'edit_user_' . $user->getID() . '_indicator', 'style' => 'display: none;']) . __('Update user'); ?></button>
                </div>
            </div>
        </div>
    </form>
<?php endif; ?>
