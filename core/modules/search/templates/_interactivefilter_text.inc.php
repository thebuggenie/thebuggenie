<?php
    $key = $filter->getFilterKey();
    $title = __($filter->getFilterTitle());
    $description = __("Filter on %customfield", array('%customfield' => $filter->getFilterTitle()));
?>
    <div class="filter interactive_dropdown" id="interactive_filter_<?php echo $key; ?>"
         data-filterkey="<?php echo $key; ?>" data-value="<?php echo $filter->getValue(); ?>"
         data-all-value="<?php echo __('Anything'); ?>" data-istext>
        <input type="hidden" name="fs[<?php echo $key; ?>][o]" value="<?php echo $filter->getOperator(); ?>"
               id="filter_<?php echo $key; ?>_operator_input">
        <label><?php echo $title; ?></label>

        <div class="interactive_menu">
            <h1><?php echo $description; ?></h1>
            <input type="search" name="fs[<?php echo $key; ?>][v]" class="filter_searchfield" id="filter_<?php echo $key; ?>_value_input" placeholder="<?php echo __('Enter something to search for'); ?>" value="<?php echo $filter->getValue(); ?>">
        </div>
        <span class="value"><?php if (!$filter->hasValue()) echo __('Anything'); ?></span>

        <div class="filter_remove_button"
             onclick="TBG.Search.removeFilter($(this).up());"><?php echo image_tag('icon-mono-remove.png'); ?></div>
    </div>
<?php
