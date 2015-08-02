<div class="issue_paginator fixed visible" id="issues_paginator">
    <?php if ($currentpage > 1): ?>
        <?php if ($currentpage > 2): ?>
        <button class="button button-silver" title="<?php echo __('First page'); ?>" onclick="TBG.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', 0);">&larrb;</button>
        <?php endif; ?>
        <button class="button button-silver" title="<?php echo __('Previous page'); ?>" onclick="TBG.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo ($currentpage - 2) * $ipp; ?>);">&laquo;</button>
    <?php endif; ?>
    <?php for ($cc = 1; $cc <= $pagecount; $cc++): ?>
        <?php if ($cc == $currentpage): ?>
            <button class="button button-silver disabled"><?php echo $cc; ?></button>
        <?php else: ?>
            <button class="button button-silver" onclick="TBG.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo $ipp * ($cc - 1); ?>);"><?php echo $cc; ?></button>
        <?php endif; ?>
    <?php endfor; ?>
    <?php if ($currentpage < $pagecount): ?>
        <button class="button button-silver" title="<?php echo __('Next page'); ?>" onclick="TBG.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo $currentpage * $ipp; ?>);">&raquo;</button>
        <?php if ($currentpage < $pagecount - 1): ?>
            <button class="button button-silver" title="<?php echo __('Last page'); ?>" onclick="TBG.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo ($pagecount - 1) * $ipp; ?>);">&rarrb;</button>
        <?php endif; ?>
    <?php endif; ?>
    <?php echo image_tag('spinning_20.gif', array('id' => 'paging_spinning', 'style' => 'display: none; margin: 0 0 -6px 5px;')); ?>
</div>
<div class="issue_paginator" id="issues_paginator_static">
    <?php if ($currentpage > 1): ?>
        <?php if ($currentpage > 2): ?>
        <button class="button button-silver" title="<?php echo __('First page'); ?>" onclick="TBG.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', 0);">&larrb;</button>
        <?php endif; ?>
        <button class="button button-silver" title="<?php echo __('Previous page'); ?>" onclick="TBG.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo ($currentpage - 2) * $ipp; ?>);">&laquo;</button>
    <?php endif; ?>
    <?php for ($cc = 1; $cc <= $pagecount; $cc++): ?>
        <?php if ($cc == $currentpage): ?>
            <button class="button button-silver disabled"><?php echo $cc; ?></button>
        <?php else: ?>
            <button class="button button-silver" onclick="TBG.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo $ipp * ($cc - 1); ?>);"><?php echo $cc; ?></button>
        <?php endif; ?>
    <?php endfor; ?>
    <?php if ($currentpage < $pagecount): ?>
        <button class="button button-silver" title="<?php echo __('Next page'); ?>" onclick="TBG.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo $currentpage * $ipp; ?>);">&raquo;</button>
        <?php if ($currentpage < $pagecount - 1): ?>
            <button class="button button-silver" title="<?php echo __('Last page'); ?>" onclick="TBG.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo ($pagecount - 1) * $ipp; ?>);">&rarrb;</button>
        <?php endif; ?>
    <?php endif; ?>
    <?php echo image_tag('spinning_20.gif', array('id' => 'paging_spinning', 'style' => 'display: none; margin: 0 0 -6px 5px;')); ?>
</div>
