<div class="backdrop_box large">
    <div class="backdrop_detail_header">
        <span><?php echo __('Configure dashboard'); ?></span>
        <?php echo javascript_link_tag(fa_image_tag('plus'), array('onclick' => "TBG.Main.Dashboard.View.add();", 'class' => 'add_link')); ?>
        <?php echo link_tag($previous_route, fa_image_tag('times'), ['class' => 'closer']); ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <ul id="views_list" style="float: left; margin: 0; padding: 0; list-style: none;" class="sortable">
        <?php foreach ($dashboardViews as $view): ?>
            <li id="view_<?php echo $view->getDetail(); ?>" class="rounded_box mediumgrey">
                <span class="dashboard_view_data" id="<?php echo $view->getDetail(); ?>_<?php echo $view->getType(); ?>"><?php echo ($view->getType()) ? __($views[$view->getType()][$view->getDetail()]) : __('...Select a view...'); ?></span>
                <?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown', 'style' => 'float: right; margin-left: 5px;')), array('onclick' => "this.up('li').toggleClassName('verylightyellow');this.up('li').toggleClassName('mediumgrey');")); ?>
                <?php echo javascript_link_tag(image_tag('action_remove_small.png', array('class' => 'menu_dropdown', 'style' => 'float: right; margin-left: 5px;')), array('onclick' => "this.up('li').remove();Sortable.create('views_list');")); ?>
                <div class="available_views_list">
                    <?php foreach ($views as $id_type => $view_type): ?>
                        <?php foreach ($view_type as $id_view => $a_view): ?>
                            <div id="<?php echo $id_view; ?>_<?php echo $id_type; ?>" onclick="TBG.Main.Dashboard.View.swap(this);"><?php echo __($a_view); ?></div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
        
        <ul id="view_template" style="display: none;">
            <li id="view_default" class="rounded_box verylightyellow">
                <span class="template_view dashboard_view_data" id="0_0"><?php echo __('...Select a view...'); ?></span>
                <?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown', 'style' => 'float: right; margin-left: 5px;')), array('onclick' => "this.up('li').toggleClassName('verylightyellow');this.up('li').toggleClassName('mediumgrey');")); ?>
                <?php echo javascript_link_tag(image_tag('action_remove_small.png', array('class' => 'menu_dropdown', 'style' => 'float: right; margin-left: 5px;')), array('onclick' => "this.up('li').remove();Sortable.create('views_list');")); ?>
                <div class="available_views_list">
                    <?php foreach ($views as $id_type => $view_type): ?>
                        <?php foreach ($view_type as $id_view => $a_view): ?>
                            <div id="<?php echo $id_view; ?>_<?php echo $id_type; ?>" onclick="TBG.Main.Dashboard.View.swap(this);"><?php echo __($a_view); ?></div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </li>
        </ul>
        <br style="clear: both;">
    </div>
    <div id="save_dashboard" class="backdrop_details_submit">
        <span class="explanation"><?php echo __("When you're happy, save your changes"); ?></span>
        <div class="submit_container">
            <button onclick="TBG.Main.Dashboard.save('<?php echo make_url('dashboard_save', array('target_type' => $target_type, 'tid' => $tid)); ?>');" class="button button-silver"><?php echo image_tag('spinning_16.gif', array('id' => 'save_dashboard_indicator', 'style' => 'display: none;')) . __('Save dashboard'); ?></button>
        </div>
    </div>
</div>
<script>Sortable.create('views_list', {constraint: ''});</script>
