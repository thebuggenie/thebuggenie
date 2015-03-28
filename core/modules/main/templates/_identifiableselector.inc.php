<ul class="rounded_box white<?php if (isset($absolute) && $absolute): ?> shadowed<?php endif; ?><?php if (isset($classes)): echo ' '.$classes; endif; ?> popup_box more_actions_dropdown identifiable_selector" id="<?php echo $html_id; ?>" style="<?php if (isset($absolute) && $absolute): ?>position: absolute;<?php else: ?>margin: 5px 0 5px 0; clear: both;<?php endif; ?> <?php if (!isset($hidden) || $hidden): ?>display: none;<?php endif; ?> width: 324px; z-index: 10001; <?php if (isset($style)): foreach ($style as $key => $val): echo ' ' . $key . ': ' . $val . ';'; endforeach; endif; ?>">
    <li class="header"><?php echo $header; ?></li>
    <?php if ($allow_clear): ?>
        <li>
            <a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_value'), '%identifiable_value'), array(0, 0), $callback); ?>"><?php echo $clear_link_text; ?></a>
        </li>
        <li class="separator"></li>
    <?php endif; ?>
    <li class="dropdown_content nohover form_container">
        <form id="<?php echo $base_id; ?>_form" accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" method="post" action="" onsubmit="TBG.Main.findIdentifiable('<?php echo make_url('main_find_identifiable'); ?>', '<?php echo $base_id; ?>');return false;">
            <?php if ($include_teams && $include_users): ?>
                <label for="<?php echo $base_id; ?>_input"><?php echo __('Find a user or team'); ?>:</label><br>
            <?php elseif ($include_teams): ?>
                <label for="<?php echo $base_id; ?>_input"><?php echo __('Find a team'); ?>:</label><br>
            <?php elseif ($include_clients): ?>
                <label for="<?php echo $base_id; ?>_input"><?php echo __('Find a client'); ?>:</label><br>
            <?php else: ?>
                <label for="<?php echo $base_id; ?>_input"><?php echo __('Find a user'); ?>:</label><br>
            <?php endif; ?>
            <?php $text_title = __('Enter a name here'); ?>
            <?php if (isset($teamup_callback)): ?>
                <input type="hidden" name="teamup_callback" value="<?php echo $teamup_callback; ?>">
            <?php endif; ?>
            <input type="hidden" name="callback" value="<?php echo $callback; ?>">
            <?php if (isset($team_callback)): ?>
                <input type="hidden" name="team_callback" value="<?php echo $team_callback; ?>">
            <?php endif; ?>
            <input type="hidden" name="include_teams" value="<?php echo (int) $include_teams; ?>">
            <input type="hidden" name="include_clients" value="<?php echo (int) $include_clients; ?>">
            <input type="text" name="find_identifiable_by" id="<?php echo $base_id; ?>_input" value="<?php echo $text_title; ?>" style="width: 240px; padding: 1px 1px 1px;" onblur="if (this.getValue() == '') { this.value = '<?php echo $text_title; ?>'; this.addClassName('faded_out'); }" onfocus="if (this.getValue() == '<?php echo $text_title; ?>') { this.clear(); } this.removeClassName('faded_out');" class="faded_out">
            <input type="submit" style="width: 60px;" value="<?php echo __('Find'); ?>"></input>
        </form>
    </li>
    <li class="dropdown_content nohover" id="<?php echo $base_id; ?>_results_container" style="display: none;">
        <ul id="<?php echo $base_id; ?>_results"></ul>
    </li>
    <?php if ($include_users): ?>
        <li class="separator"></li>
        <li class="nohover"><label><?php echo __('Select yourself or a friend below'); ?></label></li>
        <li><a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_value'), '%identifiable_value', urlencode('%identifiable_type'), '%identifiable_type'), array($tbg_user->getID(), $tbg_user->getID(), 'user', "'user'"), $callback); ?>"><?php echo __('Select yourself'); ?> (<?php echo $tbg_user->getUsername(); ?>)</a></li>
        <?php if (count($tbg_user->getFriends()) == 0): ?>
            <li class="disabled"><?php echo __("Your friends will appear here"); ?></li>
        <?php else: ?>
            <?php include_component('main/identifiableselectorresults', array('header' => false, 'users' => $tbg_user->getFriends(), 'callback' => $callback, 'team_callback' => ((isset($team_callback)) ? $team_callback : null))); ?>
        <?php endif; ?>
    <?php endif; ?>
    <?php if (isset($team_callback) && count($tbg_user->getTeams()) > 0): ?>
        <li class="separator"></li>
        <li class="nohover">
            <?php if ($include_users): ?>
                <label><?php echo __('%select_yourself_or_a_friend or select one of your teams', array('%select_yourself_or_a_friend' => '')); ?></label>
            <?php else: ?>
                <label><?php echo __('Select one of your teams'); ?></label>
            <?php endif; ?>
        </li>
        <?php foreach ($tbg_user->getTeams() as $team): ?>
            <li><a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_value'), '%identifiable_value', urlencode('%identifiable_type'), '%identifiable_type'), array($team->getID(), $team->getID(), 'team', "'team'"), $team_callback); ?>"><?php echo $team->getName(); ?></a></li>
        <?php endforeach; ?>
    <?php elseif (isset($client_callback) && count($tbg_user->getClients()) > 0): ?>
        <li class="separator"></li>
        <li><label><?php echo __('Select one of your clients'); ?>:</label></li>
        </li>
        <?php foreach ($tbg_user->getClients() as $client): ?>
            <li><a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_value'), '%identifiable_value', urlencode('%identifiable_type'), '%identifiable_type'), array($client->getID(), $client->getID(), 'client', "'client'"), $client_callback); ?>"><?php echo __('Select %clientname', array('%clientname' => $client->getName())); ?> (<?php echo $client->getName(); ?>)</a></li>
        <?php endforeach; ?>
    <?php endif; ?>
    <li id="<?php echo $base_id; ?>_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
    <li id="<?php echo $base_id; ?>_change_error" class="error_message" style="display: none;"></li>
    <?php /*if (isset($allow_close) && $allow_close == true): ?>
        <li style="text-align: right;">
            <a href="javascript:void(0);" onclick="$('<?php echo $html_id; ?>').toggle();"><?php echo __('Close popup'); ?></a>
        </li>
    <?php endif;*/ ?>
</ul>
