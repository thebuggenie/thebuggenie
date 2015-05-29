<?php

    $tbg_response->setTitle(__('Configure users, teams and clients'));
    $users_text = (\thebuggenie\core\framework\Context::getScope()->getMaxUsers()) ? __('Users (%num/%max)', array('%num' => '<span id="current_user_num_count">'.\thebuggenie\core\entities\User::getUsersCount().'</span>', '%max' => \thebuggenie\core\framework\Context::getScope()->getMaxUsers())) : __('Users');
    $teams_text = (\thebuggenie\core\framework\Context::getScope()->getMaxTeams()) ? __('Teams (%num/%max)', array('%num' => '<span id="current_team_num_count">'.\thebuggenie\core\entities\Team::countAll().'</span>', '%max' => \thebuggenie\core\framework\Context::getScope()->getMaxTeams())) : __('Teams');

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0 class="configuration_page">
    <tr>
        <?php include_component('leftmenu', array('selected_section' => \thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_USERS)); ?>
        <td valign="top" style="padding-left: 15px;">
            <div style="width: 730px;">
                <h3><?php echo __('Configure users, teams and clients'); ?></h3>
                <div class="tab_menu inset">
                    <ul id="usersteamsgroups_menu">
                        <li id="tab_users" class="selected"><?php echo javascript_link_tag($users_text, array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_users', 'usersteamsgroups_menu');")); ?></li>
                        <li id="tab_teams"><?php echo javascript_link_tag($teams_text, array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_teams', 'usersteamsgroups_menu');")); ?></li>
                        <li id="tab_clients"><?php echo javascript_link_tag(__('Clients'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_clients', 'usersteamsgroups_menu');")); ?></li>
                        <li id="tab_groups" class="right modest"><?php echo javascript_link_tag(__('Groups'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_groups', 'usersteamsgroups_menu');")); ?></li>
                    </ul>
                </div>
                <div id="usersteamsgroups_menu_panes">
                    <div id="tab_users_pane" style="padding-top: 0; width: 100%;">
                        <div class="lightyellowbox" style="margin-bottom: 10px; padding: 7px;" id="adduser_form_container">
                            <form action="<?php echo make_url('configure_users_add_user'); ?>" method="post" onsubmit="TBG.Config.User.add('<?php echo make_url('configure_users_add_user'); ?>', import_cb, this);return false;" id="createuser_form_quick">
                                <label for="quick_add_user_username"><?php echo __('Quick add user'); ?></label>
                                <input type="text" id="quick_add_user_username" name="username" placeholder="<?php echo __('Enter username to add'); ?>">
                                <input type="submit" value="<?php echo __('Create'); ?>" onclick="$('createuser_form_quick_indicator').show()">
                                <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; margin: 3px 5px -4px;', 'id' => 'createuser_form_quick_indicator')); ?>
                                <a href="javascript:void(0);" style="float: right; <?php if (!\thebuggenie\core\framework\Context::getScope()->hasUsersAvailable()): ?>display: none;<?php endif; ?>" onclick="$('adduser_div').toggle();"><?php echo __('More details'); ?></a>
                            </form>
                        </div>
                        <strong><?php echo __('Quick selection'); ?></strong>
                        <div class="button-group">
                            <?php foreach (range('A', 'Z') as $letter): ?>
                                <?php echo javascript_link_tag($letter, array('class' => 'button button-silver', 'style' => 'width: 12px;', 'onclick' => "TBG.Config.User.show('".make_url('configure_users_find_user')."', '{$letter}');")); ?>
                            <?php endforeach; ?>
                            <?php echo javascript_link_tag('0-9', array('style' => 'width: 23px;', 'class' => 'button button-silver', 'onclick' => "TBG.Config.User.show('".make_url('configure_users_find_user')."', '0-9');")); ?>
                        </div>
                        <div id="users_more_actions_container" style="position: relative;">
                            <button class="button button-silver last dropper" id="users_more_actions" onclick="if ($(this).hasClassName('button-pressed')){ $('findusers').focus(); }"><?php echo __('Search'); ?></button>
                            <ul id="users_more_actions_dropdown" style="width: 400px; font-size: 1.1em; z-index: 1000; margin-top: 21px;" class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$('users_more_actions').toggleClassName('button-pressed');$('users_more_actions_dropdown').toggle();">
                                <li class="finduser_container">
                                    <label for="findusers"><?php echo __('Find user(s)'); ?>:</label><br>
                                    <form action="<?php echo make_url('configure_users_find_user'); ?>" method="post" onsubmit="TBG.Config.User.show('<?php echo make_url('configure_users_find_user'); ?>', $('findusers').getValue());return false;">
                                        <input type="text" name="findusers" id="findusers" value="<?php echo $finduser; ?>" placeholder="<?php echo __('Enter something to search for'); ?>">&nbsp;<input type="submit" value="<?php echo __('Find'); ?>">
                                    </form>
                                </li>
                                <li class="separator"></li>
                                <li><?php echo javascript_link_tag(__('Show all users'), array('onclick' => "TBG.Config.User.show('".make_url('configure_users_find_user')."', 'all');")); ?></li>
                                <li><?php echo javascript_link_tag(__('Show unactivated users'), array('onclick' => "TBG.Config.User.show('".make_url('configure_users_find_user')."', 'unactivated');")); ?></li>
                                <li><?php echo javascript_link_tag(__('Show newly created users'), array('onclick' => "TBG.Config.User.show('".make_url('configure_users_find_user')."', 'newusers');")); ?></li>
                            </ul>
                        </div>
                        <div class="fullpage_backdrop" id="adduser_div"style="display: none;">
                            <script>
                                var import_cb = function () { 
                                    TBG.Main.Helpers.Dialog.show('<?php echo __('Would you like to add this user to the current scope?'); ?>',
                                                                 '<?php echo __('The username you tried to create already exists. You can give this user access to the current scope by pressing "%yes" below. If you want to create a different user, press "%no" and enter a different username.', array('%yes' => __('yes'), '%no' => __('no'))); ?>',
                                                                 {
                                                                     yes: {
                                                                         click: function() {TBG.Config.User.addToScope('<?php echo make_url('configure_users_import_user'); ?>');}
                                                                     },
                                                                     no: {click: TBG.Main.Helpers.Dialog.dismiss}
                                                                 });
                                };
                            </script>
                            <div class="fullpage_backdrop_content backdrop_box medium">
                                <div class="backdrop_detail_header"><?php echo __('Add a user'); ?></div>
                                <div class="backdrop_detail_content">
                                    <p>
                                        <?php echo __('Enter details about the new user here'); ?>
                                    </p>
                                    <form action="<?php echo make_url('configure_users_add_user'); ?>" method="post" onsubmit="TBG.Config.User.add('<?php echo make_url('configure_users_add_user'); ?>', import_cb);return false;" id="createuser_form">
                                        <dl>
                                            <dt><label for="adduser_username" class="required"><?php echo __('Username'); ?>:</label></dt>
                                            <dd>
                                                <input type="text" name="username" id="adduser_username" style="width: 200px;">
                                            </dd>
                                            <dt><label for="adduser_password"><?php echo __('Password'); ?>:</label></dt>
                                            <dd>
                                                <input type="password" name="password" id="adduser_password" style="width: 200px;"><br>
                                                <span class="faded_out"><?php echo __('Leave blank to autogenerate a password'); ?></span>
                                            </dd>
                                            <dt><label for="adduser_password_repeat"><?php echo __('Repeat password'); ?>:</label></dt>
                                            <dd>
                                                <input type="password" name="password_repeat" id="adduser_password_repeat" style="width: 200px;">
                                            </dd>
                                            <dt><label for="adduser_realname"><?php echo __('Full name'); ?>:</label></dt>
                                            <dd>
                                                <input type="text" name="realname" id="adduser_realname" style="width: 300px;">
                                            </dd>
                                            <dt><label for="adduser_buddyname"><?php echo __('Nickname'); ?>:</label></dt>
                                            <dd>
                                                <input type="text" name="buddyname" id="adduser_buddyname" style="width: 200px;">
                                            </dd>
                                            <dt><label for="adduser_email"><?php echo __('Email address'); ?>:</label></dt>
                                            <dd>
                                                <input type="text" name="email" id="adduser_email" style="width: 300px;">
                                            </dd>
                                            <?php \thebuggenie\core\framework\Event::createNew('core', 'config.createuser.email')->trigger(); ?>
                                            <dt><label for="adduser_group"><?php echo __('Add user to group'); ?>:</label></dt>
                                            <dd>
                                                <select name="group_id">
                                                    <?php foreach ($groups as $group): ?>
                                                        <option value="<?php echo $group->getID(); ?>" <?php if ($group->getID() == \thebuggenie\core\framework\Settings::getDefaultGroup()->getID()) echo ' selected'; ?>><?php echo $group->getName(); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </dd>
                                            <dt><label for="adduser_teams"><?php echo __('Add user to team(s)'); ?>:</label></dt>
                                            <dd>
                                                <?php foreach ($teams as $team): ?>
                                                    <div class="teamlist_container">
                                                        <input type="checkbox" id="adduser_teams_<?php echo $team->getID(); ?>" name="teams[<?php echo $team->getID(); ?>]" value="<?php echo $team->getID(); ?>">&nbsp;<label for="adduser_teams_<?php echo $team->getID(); ?>"><?php echo $team->getName(); ?></label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </dd>
                                        </dl>
                                        <br style="clear: both;">
                                        <div class="createuser_submit_container">
                                            <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; margin: 3px 5px -4px;', 'id' => 'createuser_form_indicator')); ?>
                                            <input type="submit" value="<?php echo (\thebuggenie\core\framework\Context::getScope()->isDefault()) ? __('Create user') : __('Create or add user'); ?>" onclick="$('createuser_form_indicator').show()"><?php echo __('%create_user or %cancel', array('%create_user' => '', '%cancel' => javascript_link_tag(__('cancel'), array('onclick' => "$('adduser_div').toggle();")))); ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <br style="clear:both;">
                        <div style="padding: 10px 0 10px 0; display: none;" id="find_users_indicator"><span style="float: left;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
                        <div id="users_results"></div>
                    </div>
                    <div id="tab_groups_pane" style="display: none;">
                        <div class="lightyellowbox" style="margin-top: 5px; padding: 7px;">
                            <form id="create_group_form" action="<?php echo make_url('configure_users_add_group'); ?>" method="post" accept-charset="<?php echo \thebuggenie\core\framework\Settings::getCharset(); ?>" onsubmit="TBG.Config.Group.add('<?php echo make_url('configure_users_add_group'); ?>');return false;">
                                <div id="add_group">
                                    <label for="group_name"><?php echo __('Create a new group'); ?></label>
                                    <input type="text" id="group_name" name="group_name" placeholder="<?php echo __('Enter group name here'); ?>">
                                    <input type="submit" value="<?php echo __('Create'); ?>">
                                </div>
                            </form>
                        </div>
                        <table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="create_group_indicator">
                            <tr>
                                <td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
                                <td style="padding: 0px; text-align: left;"><?php echo __('Adding group, please wait'); ?>...</td>
                            </tr>
                        </table>
                        <div id="groupconfig_list">
                            <?php foreach ($groups as $group): ?>
                                <?php include_component('configuration/groupbox', array('group' => $group)); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div id="tab_teams_pane" style="display: none;">
                        <div class="lightyellowbox" style="margin-top: 5px; padding: 7px;<?php if (!\thebuggenie\core\framework\Context::getScope()->hasTeamsAvailable()): ?> display: none;<?php endif; ?>" id="add_team_div">
                            <form id="create_team_form" action="<?php echo make_url('configure_users_add_team'); ?>" method="post" accept-charset="<?php echo \thebuggenie\core\framework\Settings::getCharset(); ?>" onsubmit="TBG.Config.Team.add('<?php echo make_url('configure_users_add_team'); ?>');return false;">
                                <label for="team_name"><?php echo __('Create a new team'); ?></label>
                                <input type="text" id="team_name" name="team_name" placeholder="<?php echo __('Enter team name here'); ?>">
                                <input type="submit" value="<?php echo __('Create'); ?>">
                                <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; margin: 3px 5px -4px;', 'id' => 'create_team_indicator')); ?>
                            </form>
                        </div>
                        <div id="teamconfig_list">
                            <?php foreach ($teams as $team): ?>
                                <?php include_component('configuration/teambox', array('team' => $team)); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div id="tab_clients_pane" style="display: none;">
                        <div class="lightyellowbox" style="margin-top: 5px; padding: 7px;">
                            <form id="create_client_form" action="<?php echo make_url('configure_users_add_client'); ?>" method="post" accept-charset="<?php echo \thebuggenie\core\framework\Settings::getCharset(); ?>" onsubmit="TBG.Config.Client.add('<?php echo make_url('configure_users_add_client'); ?>');return false;">
                                <div id="add_client">
                                    <label for="client_name"><?php echo __('Create a new client'); ?></label>
                                    <input type="text" id="client_name" name="client_name" placeholder="<?php echo __('Enter client name here'); ?>">
                                    <input type="submit" value="<?php echo __('Create'); ?>">
                                    <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; margin: 3px 5px -4px;', 'id' => 'create_client_indicator')); ?>
                                </div>
                            </form>
                            <?php echo __('You can set other details, such as an email address or telephone number, after creating the client.'); ?>
                        </div>
                        <div id="clientconfig_list">
                            <?php foreach ($clients as $client): ?>
                                <div id="client_<?php echo $client->getID(); ?>_item"><?php include_component('configuration/clientbox', array('client' => $client)); ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>
    <script type="text/javascript">
        require(['domReady', 'thebuggenie/tbg', 'jquery'], function (domReady, tbgjs, jQuery) {
            domReady(function () {
                jQuery("body").on("click", "#findusers", function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                });
                <?php if ($finduser): ?>
                    tbgjs.Config.User.show('<?php echo make_url('configure_users_find_user'); ?>', '<?php echo $finduser; ?>');
                <?php endif; ?>
            });
        });
    </script>
