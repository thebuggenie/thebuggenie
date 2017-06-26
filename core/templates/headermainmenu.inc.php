<?php

    use thebuggenie\core\framework;

?>
<nav class="tab_menu header_menu main_menu" id="main_menu">
    <ul>
        <?php if (!framework\Settings::isSingleProjectTracker()): ?>
            <li<?php if ($tbg_response->getPage() == 'home'): ?> class="selected"<?php endif; ?>><div class="menuitem_container"><?php echo link_tag(make_url('home'), fa_image_tag('home') . __('Frontpage')); ?></div></li>
        <?php endif; ?>
        <?php if (!$tbg_user->isThisGuest() && !framework\Settings::isSingleProjectTracker()): ?>
            <li class="with-dropdown <?php if ($tbg_response->getPage() == 'dashboard'): ?>selected<?php endif; ?>">
                <div class="menuitem_container">
                    <?php echo link_tag('javascript:void(0);', fa_image_tag('columns') . __('Dashboard')); ?>
                    <?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown'))); ?>
                </div>
                <div class="tab_menu_dropdown">
                    <?php echo link_tag(make_url('dashboard'), __('My dashboard'), ((in_array($tbg_response->getPage(), array('dashboard'))) ? array('class' => 'selected') : array())); ?>
                </div>
            </li>
        <?php endif; ?>
        <?php if (($tbg_user->hasPageAccess('teamlist') || count($tbg_user->getTeams())) && !is_null(\thebuggenie\core\entities\Team::getAll())): ?>
            <li class="with-dropdown <?php if ($tbg_response->getPage() == 'team'): ?>selected<?php endif; ?>">
                <div class="menuitem_container">
                    <?php echo link_tag('javascript:void(0)', fa_image_tag('users') . __('Teams'), array('class' => 'not_clickable')); ?>
                    <?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
                </div>
                <div id="team_menu" class="tab_menu_dropdown">
                    <?php foreach (\thebuggenie\core\entities\Team::getAll() as $team): ?>
                        <?php if (!$team->hasAccess()) continue; ?>
                        <?php echo link_tag(make_url('team_dashboard', array('team_id' => $team->getID())), fa_image_tag('users') . $team->getName()); ?>
                    <?php endforeach; ?>
                </div>
            </li>
        <?php endif; ?>
        <?php if ($tbg_user->hasPageAccess('clientlist') && count($tbg_user->getClients()) && !is_null(\thebuggenie\core\entities\Client::getAll())): ?>
            <li class="with-dropdown <?php if ($tbg_response->getPage() == 'client'): ?>selected<?php endif; ?>">
                <div class="menuitem_container">
                    <?php echo link_tag('javascript:void(0)', image_tag('tab_clients.png') . __('Clients'), array('class' => 'not_clickable')); ?>
                    <?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown')), array('onmouseover' => "")); ?>
                </div>
                <div id="client_menu" class="tab_menu_dropdown">
                    <?php foreach (\thebuggenie\core\entities\Client::getAll() as $client): ?>
                        <?php if (!$client->hasAccess()) continue; ?>
                        <?php echo link_tag(make_url('client_dashboard', array('client_id' => $client->getID())), fa_image_tag('users') . $client->getName()); ?>
                    <?php endforeach; ?>
                </div>
            </li>
        <?php endif; ?>
        <?php framework\Event::createNew('core', 'templates/headermainmenu::projectmenulinks')->trigger(); ?>
    </ul>
</nav>
