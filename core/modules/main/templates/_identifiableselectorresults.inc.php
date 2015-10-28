<?php if (isset($clients)): ?>
    <li class="nohover"><label><?php echo __('Clients found'); ?></label></li>
    <?php if (count($clients) > 0): ?>
        <?php foreach ($clients as $client): ?>
            <li><a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_value'), '%identifiable_value', urlencode('%identifiable_type'), '%identifiable_type'), array($client->getID(), $client->getID(), 'client', "'client'"), (isset($client_callback)) ? $client_callback : $callback); ?>"><?php echo $client->getName(); ?></a></li>
        <?php endforeach; ?>
    <?php else: ?>
        <li class="disabled"><?php echo __("Couldn't find any clients"); ?></li>
    <?php endif; ?>
<?php else: ?>
    <?php if (!isset($header) || $header == true): ?><li class="nohover"><label><?php echo __('Users found'); ?></label></li><?php endif; ?>
    <?php if (count($users) > 0): ?>
        <?php foreach ($users as $user): ?>
            <li><a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_value'), '%identifiable_value', urlencode('%identifiable_type'), '%identifiable_type'), array($user->getID(), $user->getID(), 'user', "'user'"), $callback); ?>"><?php echo $user->getNameWithUsername(); ?></a></li>
            <?php if (isset($teamup_callback)): ?>
                <li><a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_value'), '%identifiable_value', urlencode('%identifiable_type'), '%identifiable_type'), array($user->getID(), $user->getID(), 'user', "'user'"), $teamup_callback); ?>"><?php echo __('Team up with %username', array('%username' => $user->getNameWithUsername())); ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <li class="disabled"><?php echo __("Couldn't find any users"); ?></li>
    <?php endif; ?>
    <?php if ($include_teams): ?>
        <li class="nohover"><label><?php echo __('Teams found'); ?></label></li>
        <?php if (isset($teams) && count($teams) > 0): ?>
            <?php foreach ($teams as $team): ?>
                <li><a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_value'), '%identifiable_value', urlencode('%identifiable_type'), '%identifiable_type'), array($team->getID(), $team->getID(), 'team', "'team'"), (isset($team_callback)) ? $team_callback : $callback); ?>"><?php echo $team->getName(); ?></a></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="disabled"><?php echo __("Couldn't find any teams"); ?></li>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
