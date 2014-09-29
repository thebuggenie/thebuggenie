<div class="toc">
    <div class="header">
        <?php echo __('Table of contents'); ?>
        <div class="button-group">
            <button class="first" title="<?php echo __('Minimize'); ?>" onclick="$(this).toggleClassName('button-pressed');$('publish_toc').toggle();">M</button>
            <button class="button button-silver last" title="<?php echo __('Stick to top'); ?>" onclick="$(this).toggleClassName('button-pressed');$(this).up('.toc').toggleClassName('fixed');">S</button>
        </div>
    </div>
    <div class="content" id="publish_toc">
        <?php foreach ($toc as $entry): ?>
            <div class="publish_toc_<?php echo $entry['level']; ?>"><a href="#<?php echo $entry['id']; ?>"><?php echo $entry['content']; ?></a></div>
        <?php endforeach; ?>
    </div>
</div>
