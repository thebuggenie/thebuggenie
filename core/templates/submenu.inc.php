<nav class="submenu_strip<?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?> project_context<?php endif; ?>" id="global_submenu">
    <ul id="submenu" class="project_stuff">
        <?php $breadcrumbs = $tbg_response->getBreadcrumbs(); ?>
        <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
            <?php $has_sub_menu = (array_key_exists('subitems', $breadcrumb) && is_array($breadcrumb['subitems'])); ?>
            <li class="breadcrumb">
                <?php if ($has_sub_menu): ?>
                    <?php echo javascript_link_tag(image_tag('tabmenu_dropdown_popout.png', array('class' => 'dropdown_activator clickable')), array('title' => __('Click to expand'), 'class' => 'submenu_activator dropper')); ?>
                <?php elseif ($index): ?>
                    <?php echo image_tag('tabmenu_dropdown_popout.png', array('class' => 'dropdown_activator')); ?>
                <?php endif; ?>
                <?php if (array_key_exists('subitems', $breadcrumb) && is_array($breadcrumb['subitems']) && count($breadcrumb['subitems'])): ?>
                    <ul class="simple_list rounded_box white shadowed popup_box more_actions_dropdown">
                        <?php foreach ($breadcrumb['subitems'] as $subindex => $subitem): ?>
                            <?php if (array_key_exists('url', $subitem) || $subitem['title'] == $breadcrumb['title']): ?>
                                <li class="<?php if (strpos($subitem['title'], $breadcrumb['title']) === 0) echo 'selected'; ?>"><a href="<?php echo (array_key_exists('url', $subitem)) ? $subitem['url'] : '#'; ?>"><?php echo $subitem['title']; ?></a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php $class = (array_key_exists('class', $breadcrumb) && $breadcrumb['class']) ? $breadcrumb['class'] : ''; ?>
                <?php if ($breadcrumb['url']): ?>
                    <?php echo link_tag($breadcrumb['url'], $breadcrumb['title'], array('class' => $class)); ?>
                <?php else: ?>
                    <span <?php if ($class): ?> class="<?php echo $class; ?>"<?php endif; ?>><?php echo $breadcrumb['title']; ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php if ($tbg_user->canSearchForIssues()): ?>
        <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? make_url('search', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())) : make_url('search'); ?>" method="get" name="quicksearchform" id="quicksearchform">
            <div style="width: auto; padding: 0; position: relative;" id="quicksearch_container">
                <input type="hidden" name="fs[text][o]" value="=">
                <?php echo image_tag('spinning_16.gif', array('id' => 'quicksearch_indicator', 'style' => 'display: none;')); ?>
                <input type="search" name="fs[text][v]" accesskey="f" id="searchfor" placeholder="<?php echo __('Search for anything here'); ?>"><div id="searchfor_autocomplete_choices" class="autocomplete rounded_box"></div>
                <input type="submit" class="button-blue" id="quicksearch_submit" value="<?php echo \thebuggenie\core\framework\Context::getI18n()->__('Find'); ?>">
            </div>
        </form>
    <?php endif; ?>
</nav>
