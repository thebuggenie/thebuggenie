<?php /** @var \thebuggenie\core\entities\CommitFileDiff $diff */ ?>
<div class="diff-header"><?= fa_image_tag('crosshairs') . $diff->getDiffHeader(); ?></div>
<?php if ($too_long): ?>
    <div class="warning-box too-long"><?= fa_image_tag('exclamation-circle') . __('This diff is too long to be shown here'); ?></div>
<?php else: ?>
    <table class="diff-preview" cellspacing="0" cellpadding="0">
        <?php foreach ($lines as $line): ?>
            <tr class="line <?= $line['change']; ?>">
                <td class="line-number"><pre><?= ($line['change'] != 'add') ? $removelinecounter : '&nbsp;'; ?></pre></td>
                <td class="line-number"><pre><?= ($line['change'] != 'remove') ? $addlinecounter : '&nbsp;'; ?></pre></td>
                <td class="text"><pre><?= htmlspecialchars($line['text'], ENT_QUOTES, 'UTF-8'); ?></pre></td>
            </tr>
            <?php if ($line['change'] != 'remove') $addlinecounter += 1; ?>
            <?php if ($line['change'] != 'add') $removelinecounter += 1; ?>
        <?php endforeach; ?>
    </table>
<?php endif; ?>