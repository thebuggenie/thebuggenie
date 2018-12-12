<?php foreach ($clients as $client): ?>
    <li data-value="<?php echo $client->getID(); ?>" class="filtervalue unfiltered">
        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
        <input type="checkbox" value="<?php echo $client->getID(); ?>" name="filters_<?php echo $filterkey; ?>_value_<?php echo $client->getID(); ?>" data-text="<?php echo $client->getName(); ?>" id="filters_<?php echo $filterkey; ?>_value_<?php echo $client->getID(); ?>">
        <label for="filters_<?php echo $filterkey; ?>_value_<?php echo $client->getID(); ?>"><?php echo $client->getName(); ?></label>
    </li>
<?php endforeach; ?>
