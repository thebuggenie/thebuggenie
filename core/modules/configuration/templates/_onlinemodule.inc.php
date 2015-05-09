<li class="plugin module <?php if (\thebuggenie\core\framework\Context::isModuleLoaded($onlinemodule->key)) echo ' installed'; ?>" id="online-module-<?php echo $onlinemodule->key; ?>">
    <?php echo __('%module_name version %version by %author', array(
            '%module_name' => '<h1>'.$onlinemodule->name.'</h1>',
            '%version' => '<span class="version">'.$onlinemodule->version.'</span>',
            '%author' => '<a href="'.$onlinemodule->author->profile.'" class="author-link">'.$onlinemodule->author->name.'</a>'
        )); ?>
    <div class="rating">
        <div class="score" style="width: <?php echo $onlinemodule->rating * 16; ?>px;"></div>
    </div>
    <div class="module-actions plugin-actions">
        <button class="install-button button button-silver" data-key="<?php echo htmlentities($onlinemodule->key); ?>"><?php echo image_tag('spinning_16.gif'); ?><span><?php echo __('Add'); ?></span></button>
        <div class="status_badge module_status plugin_status enabled">
            <?php echo __('Enabled'); ?>
        </div>
    </div>
    <p class="description">
        <?php echo $onlinemodule->description; ?>
        <span class="plugin-website-link"><a href="<?php echo $onlinemodule->url; ?>"><?php echo __('Open website'); ?></a></span>
    </p>
</li>