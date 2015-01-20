<?php

    include_component('publish/wikibreadcrumbs', array('article_name' => $article_name));
    \thebuggenie\core\framework\Context::loadLibrary('publish/publish');
    $tbg_response->setTitle(__('%article_name attachments', array('%article_name' => $article_name)));

?>
<?php if (\thebuggenie\core\framework\Settings::isUploadsEnabled() && $article instanceof \thebuggenie\modules\publish\entities\Article && $article->canEdit()): ?>
    <?php include_component('main/uploader', array('article' => $article, 'mode' => 'article')); ?>
<?php endif; ?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
    <tr>
        <td class="side_bar">
            <?php include_component('leftmenu', array('article' => $article)); ?>
        </td>
        <td class="main_area article">
            <a name="top"></a>
            <div class="article" style="width: auto; padding: 5px; position: relative;">
                <?php include_component('publish/header', array('article' => $article, 'article_name' => $article_name, 'show_actions' => true, 'mode' => 'attachments')); ?>
                <?php if ($article instanceof \thebuggenie\modules\publish\entities\Article): ?>
                    <?php if (\thebuggenie\core\framework\Settings::isUploadsEnabled() && $article->canEdit()): ?>
                        <table border="0" cellpadding="0" cellspacing="0" style="margin: 5px; float: left;" id="article_attach_file_button"><tr><td class="nice_button" style="font-size: 13px; margin-left: 0;"><input type="button" onclick="$('attach_file').show();" value="<?php echo __('Attach a file'); ?>"></td></tr></table>
                    <?php else: ?>
                        <table border="0" cellpadding="0" cellspacing="0" style="margin: 5px; float: left;" id="article_attach_file_button"><tr><td class="nice_button disabled" style="font-size: 13px; margin-left: 0;"><input type="button" onclick="TBG.Main.Helpers.Message.error('<?php echo __('File uploads are not enabled'); ?>');" value="<?php echo __('Attach a file'); ?>"></td></tr></table>
                    <?php endif; ?>
                    <br style="clear: both;">
                    <?php include_component('publish/attachments', array('article' => $article)); ?>
                <?php else: ?>
                    <?php include_component('publish/placeholder', array('article_name' => $article_name, 'nocreate' => true)); ?>
                <?php endif; ?>
            </div>
        </td>
    </tr>
</table>
