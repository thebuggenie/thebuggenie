<li data-value="<?php echo $item->getID(); ?>" class="filtervalue unfiltered<?php if ($filter->hasValue($item->getID())) echo ' selected'; ?>">
    <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
    <input type="checkbox" value="<?php echo $item->getID(); ?>" name="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $item->getID(); ?>" data-text="<?php if (!\thebuggenie\core\framework\Context::isProjectContext()) echo $item->getProject()->getName().'&nbsp;&ndash;&nbsp;'; ?><?php echo $item->getName(); ?>" id="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $item->getID(); ?>" <?php if ($filter->hasValue($item->getID())) echo 'checked'; ?>>
    <label for="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $item->getID(); ?>"><?php if (!\thebuggenie\core\framework\Context::isProjectContext()) echo $item->getProject()->getName().'&nbsp;&ndash;&nbsp;'; ?><?php echo $item->getName(); ?></label>
</li>
