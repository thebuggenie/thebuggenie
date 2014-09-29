    <li data-view-type="<?php echo $type; ?>" data-view-subtype="<?php echo $subtype; ?>" class="dashboard_view">
        <h3><?php echo $detail['title']; ?></h3>
        <div class="icon_container">
            <?php if ($icon_type == 'info') echo image_tag('dashboard_view_info.png'); ?>
            <?php if ($icon_type == 'statistics') echo image_tag('dashboard_view_statistics.png'); ?>
            <?php if ($icon_type == 'searches') echo image_tag('dashboard_view_search.png'); ?>
        </div>
        <div class="description">
            <?php echo $detail['description']; ?>
        </div>
        <div class="add_button_container">
            <?php echo image_tag('spinning_16.gif', array('class' => 'view_indicator', 'style' => 'display: none;')); ?>
            <button onclick="TBG.Main.Dashboard.addView(this);" class="button button-silver"><?php echo __('Add view'); ?></button>
        </div>
    </li>
