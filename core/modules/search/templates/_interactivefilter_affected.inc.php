<div class="filter interactive_dropdown" id="interactive_filter_<?php echo $filter->getFilterKey(); ?>" data-filterkey="<?php echo $filter->getFilterKey(); ?>" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('Any'); ?>">
    <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][o]" value="<?php echo $filter->getOperator(); ?>">
    <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][v]" value="" id="filter_<?php echo $filter->getFilterKey(); ?>_value_input">
    <label><?php

            switch ($filter->getFilterKey())
            {
                case 'build':
                    echo __('Affects release(s)');
                    break;
                case 'component':
                    echo __('Affects component(s)');
                    break;
                case 'edition':
                    echo __('Affects edition(s)');
                    break;
                case 'milestone':
                    echo __('Targetted milestone(s)');
                    break;
                default:
                    echo __($filter->getFilterTitle());
                    break;
            }

    ?></label>
    <span class="value"><?php if (!$filter->hasValue()) echo __('Any'); ?></span>
    <div class="interactive_menu wider">
        <h1><?php

                switch ($filter->getFilterKey())
                {
                    case 'build':
                        echo __('Filter on affected release(s)');
                        break;
                    case 'component':
                        echo __('Filter on affected component(s)');
                        break;
                    case 'edition':
                        echo __('Filter on affected edition(s)');
                        break;
                    case 'milestone':
                        echo __('Filter on targetted milestone(s)');
                        break;
                    default:
                        echo __("Filter on %customfield", array('%customfield' => $filter->getFilterTitle()));
                        break;
                }

        ?></h1>
        <input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
        <div class="interactive_values_container">
            <ul class="interactive_menu_values">
                <?php include_component('search/interactivefilterdynamicchoicelist', array('filter' => $filter, 'items' => $filter->getAvailableValues())); ?>
            </ul>
        </div>
    </div>
    <div class="filter_remove_button" onclick="TBG.Search.removeFilter($(this).up());"><?php echo image_tag('icon-mono-remove.png'); ?></div>
</div>
