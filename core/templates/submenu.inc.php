<nav class="submenu_strip<?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?> project_context<?php endif; ?>" id="global_submenu">
    <ul id="submenu" class="project_stuff">
        <?php
            $breadcrumbs = $tbg_response->getBreadcrumbs();
            foreach ($breadcrumbs as $index => $breadcrumb)
            {
                echo '<li class="breadcrumb">';
                $class = (array_key_exists('class', $breadcrumb) && $breadcrumb['class']) ? $breadcrumb['class'] : '';
                if ($breadcrumb['url'])
                {
                    echo link_tag($breadcrumb['url'], $breadcrumb['title'], array('class' => $class));
                }
                else
                {
                    echo '<span class="', $class, '">', $breadcrumb['title'], '</span>';
                }
                if (array_key_exists('subitems', $breadcrumb) && is_array($breadcrumb['subitems']) && count($breadcrumb['subitems']) > 0)
                {
                    echo javascript_link_tag(image_tag('tabmenu_dropdown_popout.png', array('class' => 'dropdown_activator clickable')), array('title' => __('Click to expand'), 'class' => 'submenu_activator dropper'));
                    $next_title = ($index + 1) < count($breadcrumbs) ? $breadcrumbs[$index + 1]['title'] : null;
                    echo '<ul class="simple_list rounded_box white shadowed popup_box more_actions_dropdown">';
                        foreach ($breadcrumb['subitems'] as $subindex => $subitem)
                        {
                            if (array_key_exists('url', $subitem) || (array_key_exists('title', $subitem) && $subitem['title'] == $next_title))
                            {
                                $class = strpos($subitem['title'], $next_title) === 0 ? 'selected' : '';
                                $url = array_key_exists('url', $subitem) ? $subitem['url'] : '#';
                                echo '<li class="', $class, '"><a href="', $url, '">', $subitem['title'], '</a></li>';
                            }
                            elseif (array_key_exists('separator', $subitem) && $subitem['separator'])
                            {
                                echo '<li class="separator"></li>';
                            }
                        }
                    echo '</ul>';
                }
                else if ($index + 1 < count($breadcrumbs))
                {
                    echo image_tag('tabmenu_dropdown_popout.png', array('class' => 'dropdown_activator'));
                }
                echo '</li>';
            }
        ?>
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
