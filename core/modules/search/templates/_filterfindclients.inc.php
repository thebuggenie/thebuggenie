<?php foreach ($clients as $client): ?>
    <li data-value="<?php echo $client->getID(); ?>" class="filtervalue unfiltered">
        <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
        <input type="checkbox" value="<?php echo $client->getID(); ?>" name="filters_<?php echo $filterkey; ?>_value_<?php echo $client->getID(); ?>" data-text="<?php echo $client->getName(); ?>" id="filters_<?php echo $filterkey; ?>_value_<?php echo $client->getID(); ?>">
        <label for="filters_<?php echo $filterkey; ?>_value_<?php echo $client->getID(); ?>"><?php echo $client->getName(); ?></label>
    </li>
<?php endforeach; ?>
