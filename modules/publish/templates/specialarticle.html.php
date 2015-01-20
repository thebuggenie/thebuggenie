<?php

    include_component('publish/wikibreadcrumbs', array('article_name' => $article_name));
    \thebuggenie\core\framework\Context::loadLibrary('publish/publish');
    $tbg_response->setTitle($article_name);

?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
    <tr>
        <td class="side_bar">
            <?php include_component('leftmenu', compact('article', 'special')); ?>
        </td>
        <td class="main_area article">
            <a name="top"></a>
            <?php if ($component): ?>
                <?php include_component("publish/special{$component}", compact('projectnamespace')); ?>
            <?php else: ?>
                <div class="redbox" style="margin: 15px;">
                    <?php echo __('This special page does not exist'); ?>
                </div>
            <?php endif; ?>
        </td>
    </tr>
</table>
