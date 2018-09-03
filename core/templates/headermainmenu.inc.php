<?php

    use thebuggenie\core\framework;

?>
<nav class="header_menu" id="main_menu">
    <ul>
        <?php if (!framework\Settings::isSingleProjectTracker()): ?>
            <li<?php if ($tbg_response->getPage() == 'home'): ?> class="selected"<?php endif; ?>>
                <?= link_tag(make_url('home'), fa_image_tag('home') . '<span>'.__('Projects').'</span>'); ?>
            </li>
        <?php endif; ?>
        <?php if (!$tbg_user->isThisGuest() && !framework\Settings::isSingleProjectTracker()): ?>
            <li class="<?php if ($tbg_response->getPage() == 'dashboard'): ?>selected<?php endif; ?>">
                <?= link_tag(make_url('dashboard'), fa_image_tag('columns') . '<span>'.__('Dashboard').'</span>'); ?>
            </li>
        <?php endif; ?>
        <?php if (($tbg_user->hasPageAccess('teamlist') || count($tbg_user->getTeams())) && !is_null(\thebuggenie\core\entities\Team::getAll())): ?>
            <li class="with-dropdown <?php if ($tbg_response->getPage() == 'team'): ?>selected<?php endif; ?>">
                <?= link_tag('javascript:void(0)', fa_image_tag('users') . '<span>'.__('Teams').'</span>' . fa_image_tag('caret-down', ['class' => 'dropdown-indicator']), ['class' => 'dropper']); ?>
                <ul id="team_menu" class="tab_menu_dropdown popup_box">
                    <?php foreach (\thebuggenie\core\entities\Team::getAll() as $team): ?>
                        <?php if (!$team->hasAccess()) continue; ?>
                        <li><?= link_tag(make_url('team_dashboard', array('team_id' => $team->getID())), fa_image_tag('users') . $team->getName()); ?></li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endif; ?>
        <?php if ($tbg_user->hasPageAccess('clientlist') && count($tbg_user->getClients()) && !is_null(\thebuggenie\core\entities\Client::getAll())): ?>
            <li class="with-dropdown <?php if ($tbg_response->getPage() == 'client'): ?>selected<?php endif; ?>">
                <?= link_tag('javascript:void(0)', fa_image_tag('users') . '<span>'.__('Clients').'</span>' . fa_image_tag('caret-down', ['class' => 'dropdown-indicator']), ['class' => 'dropper']); ?>
                <ul id="client_menu" class="tab_menu_dropdown popup_box">
                    <?php foreach (\thebuggenie\core\entities\Client::getAll() as $client): ?>
                        <?php if (!$client->hasAccess()) continue; ?>
                        <li><?= link_tag(make_url('client_dashboard', array('client_id' => $client->getID())), fa_image_tag('users') . $client->getName()); ?></li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endif; ?>
        <?php framework\Event::createNew('core', 'templates/headermainmenu::projectmenulinks')->trigger(); ?>
    </ul>
</nav>
