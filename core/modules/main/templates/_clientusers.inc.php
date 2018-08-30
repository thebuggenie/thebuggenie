<div class="backdrop_box medium" id="client_users">
    <div class="backdrop_detail_header">
        <span><?= __('Users for %clientname', array('%clientname' => $client->getName())); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?= __('Click a user to view more details, such as view his/her user card.'); ?>
        <div class="client_users_div">
            <div class="client_users_div_inner">
                <?php if (count($clientusers) == 0): ?>
                <span class="faded_out"><?= __('There are no users assigned to this client'); ?></span>
                <?php else: ?>
                    <ul class="client_users">
                    <?php foreach ($clientusers as $user): ?>
                        <li><?= include_component('main/userdropdown', array('user' => $user)); ?></li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
