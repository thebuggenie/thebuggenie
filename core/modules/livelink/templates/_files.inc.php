<?php if (isset($structure['filepaths'][$basepath])): ?>
    <?php foreach ($structure['filepaths'][$basepath] as $file): ?>
        <li class="action_<?= $file->getAction(); ?>">
            <?php if ($file->getAction() == \thebuggenie\core\entities\CommitFile::ACTION_DELETED): ?>
                <a href="#file_<?= $file->getID(); ?>"><?= fa_image_tag('minus-square', ['class' => 'action-icon'], 'fas') . fa_image_tag($file->getFontAwesomeIcon(), [], $file->getFontAwesomeIconStyle()) . '<span class="filename">' . $file->getFilename() . '</span>'; ?></a>
            <?php elseif ($file->getAction() == \thebuggenie\core\entities\CommitFile::ACTION_RENAMED): ?>
                <a href="#file_<?= $file->getID(); ?>"><?= fa_image_tag('edit-square', ['class' => 'action-icon'], 'fas') . fa_image_tag($file->getFontAwesomeIcon(), [], $file->getFontAwesomeIconStyle()) . '<span class="filename">' . $file->getFilename() . '</span>'; ?></a>
            <?php elseif ($file->getAction() == \thebuggenie\core\entities\CommitFile::ACTION_ADDED): ?>
                <a href="#file_<?= $file->getID(); ?>"><?= fa_image_tag('plus-square', ['class' => 'action-icon'], 'fas') . fa_image_tag($file->getFontAwesomeIcon(), [], $file->getFontAwesomeIconStyle()) . '<span class="filename">' . $file->getFilename() . '</span>'; ?></a>
            <?php else: ?>
                <a href="#file_<?= $file->getID(); ?>"><?= fa_image_tag('dot-circle', ['class' => 'action-icon'], 'far') . fa_image_tag($file->getFontAwesomeIcon(), [], $file->getFontAwesomeIconStyle()) . '<span class="filename">' . $file->getFilename() . '</span>'; ?></a>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
<?php endif; ?>
