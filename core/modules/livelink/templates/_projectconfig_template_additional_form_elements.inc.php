<?php foreach ($inputs as $input_key => $input_value): ?>
    <input type="hidden" name="<?= $input_key; ?>" value="<?= $input_value; ?>">
<?php endforeach; ?>