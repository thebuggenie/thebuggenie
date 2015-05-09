<li class="plugin theme" id="online-theme-<?php echo $onlinetheme->key; ?>">
    <?php echo __('%theme_name version %version by %author', array(
            '%theme_name' => '<h1>'.$onlinetheme->name.'</h1>',
            '%version' => '<span class="version">'.$onlinetheme->version.'</span>',
            '%author' => '<a href="'.$onlinetheme->author->profile.'" class="author-link">'.$onlinetheme->author->name.'</a>'
        )); ?>
    <div class="rating">
        <div class="score" style="width: <?php echo $onlinetheme->rating * 16; ?>px;"></div>
    </div>
    <div class="theme-actions plugin-actions">
        <button class="install-button button button-silver" data-key="<?php echo htmlentities($onlinetheme->key); ?>"><?php echo image_tag('spinning_16.gif'); ?><span><?php echo __('Add'); ?></span></button>
        <div class="status_badge theme_status plugin_status enabled">
            <?php echo __('Enabled'); ?>
        </div>
    </div>
    <p class="description">
        <?php echo $onlinetheme->description; ?>
        <span class="plugin-website-link"><a href="<?php echo $onlinetheme->url; ?>"><?php echo __('Open website'); ?></a></span>
    </p>
</li>