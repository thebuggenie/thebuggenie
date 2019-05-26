<div id="attach_file" class="backdrop_box large">
    <div class="backdrop_detail_header">
        <?php if ($mode == 'issue'): ?>
            <span><?= __('Attach one or more file(s) to this issue'); ?></span>
        <?php elseif ($mode == 'article'): ?>
            <span><?= __('Attach one or more file(s) to this article'); ?></span>
        <?php endif; ?>
        <a href="javascript:void(0)" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset()"><?= fa_image_tag('times'); ?></a>
    </div>
    <?php include_component('main/'.$uploader.'uploader', compact('mode', 'issue', 'article')); ?>
</div>
