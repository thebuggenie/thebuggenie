<li class="folder">
    <span class="foldername"><a href="javascript:void(0);"><?= fa_image_tag('minus-square', ['class' => 'collapser'], 'far') . fa_image_tag('plus-square', ['class' => 'expander'], 'far') . fa_image_tag('folder') . $foldername; ?></a></span>
    <ul>
        <?php foreach ($directory as $foldername => $directory): ?>
            <?php include_component('livelink/directory', ['basepath' => $basepath . '/' . $foldername, 'foldername' => $foldername, 'directory' => $directory, 'structure' => $structure]); ?>
        <?php endforeach; ?>
        <?php include_component('livelink/files', ['basepath' => $basepath, 'structure' => $structure]); ?>
    </ul>
</li>
