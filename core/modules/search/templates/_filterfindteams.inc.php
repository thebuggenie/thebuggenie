<?php foreach ($teams as $team): ?>
    <li data-value="<?php echo $team->getID(); ?>" class="filtervalue unfiltered">
        <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
        <input type="checkbox" value="<?php echo $team->getID(); ?>" name="filters_<?php echo $filterkey; ?>_value_<?php echo $team->getID(); ?>" data-text="<?php echo $team->getName(); ?>" id="filters_<?php echo $filterkey; ?>_value_<?php echo $team->getID(); ?>">
        <label for="filters_<?php echo $filterkey; ?>_value_<?php echo $team->getID(); ?>"><?php echo $team->getName(); ?></label>
    </li>
<?php endforeach; ?>
    