<?php foreach ($users as $user): ?>
    <li data-value="<?php echo $user->getID(); ?>" class="filtervalue unfiltered">
        <?= fa_image_tag('check-square-o', ['class' => 'checked']) . fa_image_tag('square-o', ['class' => 'unchecked']); ?>
        <input type="checkbox" value="<?php echo $user->getID(); ?>" name="filters_<?php echo $filterkey; ?>_value_<?php echo $user->getID(); ?>" data-text="<?php echo ($user->getID() == $tbg_user->getID()) ? __('Yourself') : $user->getNameWithUsername(); ?>" id="filters_<?php echo $filterkey; ?>_value_<?php echo $user->getID(); ?>">
        <label for="filters_<?php echo $filterkey; ?>_value_<?php echo $user->getID(); ?>"><?php echo ($user->getID() == $tbg_user->getID()) ? __('Yourself') : $user->getNameWithUsername(); ?></label>
    </li>
<?php endforeach; ?>
    