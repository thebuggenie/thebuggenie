<?php foreach ($teams as $team): ?>
    <li data-value="<?php echo $team->getID(); ?>" class="filtervalue unfiltered">
        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
        <input type="checkbox" value="<?php echo $team->getID(); ?>" name="filters_<?php echo $filterkey; ?>_value_<?php echo $team->getID(); ?>" data-text="<?php echo $team->getName(); ?>" id="filters_<?php echo $filterkey; ?>_value_<?php echo $team->getID(); ?>">
        <label for="filters_<?php echo $filterkey; ?>_value_<?php echo $team->getID(); ?>"><?php echo $team->getName(); ?></label>
    </li>
<?php endforeach; ?>
    