<div id="attach_file" class="backdrop_box large">
    <div class="backdrop_detail_header">
        <?php if ($mode == 'issue'): ?>
            <?php echo __('Attach one or more file(s) to this issue'); ?>
        <?php elseif ($mode == 'article'): ?>
            <?php echo __('Attach one or more file(s) to this article'); ?>
        <?php endif; ?>
    </div>
    <?php include_component('main/'.$uploader.'uploader', compact('mode', 'issue', 'article')); ?>
</div>
