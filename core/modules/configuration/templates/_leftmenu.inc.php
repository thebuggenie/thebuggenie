<td valign="top" class="project_information_sidebar">
    <div class="sidebar_links">
    <?php foreach ($config_sections as $config_info): ?>
        <?php foreach ($config_info as $section => $info): ?>
            <?php $is_selected = (bool) (($selected_section == \thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_MODULES && isset($selected_subsection) && array_key_exists('module', $info) && $selected_subsection == $info['module']) || ($selected_section != \thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_MODULES && !isset($selected_subsection) && !array_key_exists('module', $info) && $selected_section == $section)); ?>
            <?php if (is_array($info['route'])): ?>
                <?php $url = make_url($info['route'][0], $info['route'][1]); ?>
            <?php else: ?>
                <?php $url = make_url($info['route']); ?>
            <?php endif;?>
            <?php if ($is_selected) $tbg_response->addBreadcrumb($info['description'], $url); ?>
            <a href="<?php echo $url; ?>"<?php if ($is_selected): ?> class="selected"<?php endif; ?>>
                <?php
                    if (isset($info['module']) && $info['module'] != 'core'):
                        echo image_tag('cfg_icon_'.$info['icon'].'.png', array(), false, $info['module']);
                      else:
                        echo image_tag('cfg_icon_'.$info['icon'].'.png', array());
                    endif;
                    echo $info['description'];
                ?>
            </a>
        <?php endforeach;?>
    <?php endforeach;?>
    </div>
</td>
