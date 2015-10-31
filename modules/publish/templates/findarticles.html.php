<?php

    include_component('publish/wikibreadcrumbs');
    \thebuggenie\core\framework\Context::loadLibrary('publish/publish');
    $tbg_response->setTitle($article_name);

?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
    <tr>
        <td class="side_bar">
            <?php include_component('leftmenu', array('article' => $article)); ?>
        </td>
        <td class="main_area article">
            <a name="top"></a>
            <?php if (isset($error) && $error): ?>
                <div class="redbox" style="margin: 0 0 5px 0; font-size: 14px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($message) && $message): ?>
                <div class="greenbox" style="margin: 0 0 5px 5px; font-size: 14px;">
                    <b><?php echo $message; ?></b>
                </div>
            <?php endif; ?>
            <h2><?php echo __('Find articles'); ?></h2>
            <form action="<?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? make_url('publish_find_project_articles', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())) : make_url('publish_find_articles'); ?>" method="get" accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>">
                <label for="find_article_name" style="font-size: 1.1em; float: left;"><?php echo __('Find articles by name'); ?></label><input type="text" name="articlename" id="find_article_name" value="<?php echo $articlename; ?>" style="width: 400px; float: left; margin-left: 5px; padding: 2px; font-size: 1.3em;">
                <input type="submit" value="<?php echo __('Find'); ?>" class="wiki-find-articles-button" style="float: left; margin-left: 5px; font-size: 1.2em; padding: 3px 5px">
            </form>
            <br style="clear: both;">
            <?php if (isset($resultcount)): ?>
                <?php if ($resultcount): ?>
                    <div class="header_div" style="font-size: 1.3em;"><?php echo __('Found %num article(s)', array('%num' => $resultcount)); ?></div>
                    <ul class="simple_list wiki-find-articles-list">
                        <?php foreach ($articles as $article): ?>
                        <li style="margin-bottom: 0;">
                            <?php echo link_tag(make_url('publish_article', array('article_name' => $article->getName())), $article->getName(), array('style' => 'font-size: 1.1em;')); ?><br>
                            <div class="faded_out"><?php echo __('Last updated %updated_at', array('%updated_at' => tbg_formatTime($article->getLastUpdatedDate(), 6))); ?></div>
                            <div class="article_preview">
                                <?php echo tbg_truncateText($article->getContent()); ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="faded_out" style="font-size: 1.3em;"><?php echo __('No articles found'); ?></div>
                <?php endif; ?>
            <?php else: ?>
                <div class="faded_out" style="font-size: 1.3em;"><?php echo __('Enter something to search for in the input box above'); ?></div>
            <?php endif; ?>
        </td>
    </tr>
</table>
