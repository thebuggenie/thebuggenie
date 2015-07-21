<?php

    include_component('publish/wikibreadcrumbs', array('article_name' => $article_name));
    \thebuggenie\core\framework\Context::loadLibrary('publish/publish');
    $tbg_response->setTitle(__('%article_name history', array('%article_name' => $article_name)));

?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
    <tr>
        <td class="side_bar">
            <?php include_component('leftmenu', array('article' => $article)); ?>
        </td>
        <td class="main_area article">
            <a name="top"></a>
            <div class="article" style="width: auto; padding: 5px; position: relative;">
                <?php include_component('publish/header', array('article' => $article, 'article_name' => $article_name, 'show_actions' => true, 'mode' => 'history')); ?>
                <?php if ($article instanceof \thebuggenie\modules\publish\entities\Article): ?>
                    <?php if ($history_action == 'list'): ?>
                        <form action="<?php echo make_url('publish_article_diff', array('article_name' => $article->getName())); ?>" method="post">
                            <table cellpadding="0" cellspacing="0" id="article_history">
                                <thead>
                                    <tr>
                                        <th style="width: 25px; text-align: center;">#</th>
                                        <th style="width: 150px;"><?php echo __('Updated'); ?></th>
                                        <th style="width: 200px;"><?php echo __('Author'); ?></th>
                                        <th><?php echo __('Comment'); ?></th>
                                        <?php if ($revision_count > 1): ?>
                                            <th style="width: 60px;" colspan="2"><?php echo __('Compare'); ?></th>
                                            <?php if (\thebuggenie\core\framework\Context::getModule('publish')->canUserEditArticle($article_name)): ?>
                                                <th style="width: 150px;"><?php echo __('Actions'); ?></th>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($history as $revision => $history_item): ?>
                                        <tr>
                                            <td style="text-align: center;"><b><?php echo ($revision < $revision_count) ? link_tag(make_url('publish_article_revision', array('article_name' => $article->getName(), 'revision' => $revision)), $revision) : $revision; ?></b></td>
                                            <td style="text-align: center;"><?php echo tbg_formatTime($history_item['updated'], 20); ?></td>
                                            <td><i><?php echo ($history_item['author'] instanceof \thebuggenie\core\entities\User) ? '<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show(\'' . make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $history_item['author']->getID())) . '\');">' . $history_item['author']->getName() . '</a>' : __('Initial import'); ?></i></td>
                                            <td><?php echo $history_item['change_reason']; ?></td>
                                            <?php if ($revision_count > 1): ?>
                                                <td style="width: 30px; text-align: center;">
                                                    <?php if ($revision > 1): ?>
                                                        <input type="radio" value="<?php echo $revision; ?>" <?php if ($revision == $revision_count): ?>checked <?php endif; ?> name="to_revision" id="from_revision_<?php echo $revision; ?>">
                                                    <?php endif; ?>
                                                </td>
                                                <td style="width: 30px; text-align: center;">
                                                    <?php if ($revision < $revision_count): ?>
                                                        <input type="radio" value="<?php echo $revision; ?>" <?php if ($revision == $revision_count - 1): ?>checked <?php endif; ?> name="from_revision" id="to_revision_<?php echo $revision; ?>">
                                                    <?php endif; ?>
                                                </td>
                                                <?php if ($revision < $revision_count && $article->canEdit()): ?>
                                                    <td style="position: relative;">
                                                            <?php echo javascript_link_tag(__('Restore this version'), array('onclick' => "$('restore_article_revision_{$revision}').toggle();")); ?>
                                                            <div class="rounded_box white shadowed" style="width: 400px; position: absolute; right: 15px; display: none; z-index: 100;" id="restore_article_revision_<?php echo $revision; ?>">
                                                                <div class="header_div"><?php echo __('Are you sure you want to restore this revision?'); ?></div>
                                                                <div class="content" style="padding: 5px;">
                                                                    <?php echo __('If you confirm, all changes after this revision will be lost, and the article reverted back to the state it was in revision %revision_number', array('%revision_number' => '<b>'.$revision.'</b>')); ?>
                                                                    <div style="text-align: right; padding: 5px;">
                                                                        <input type="hidden" name="restore">
                                                                        <?php echo __('%yes or %cancel', array('%yes' => link_tag(make_url('publish_article_restore', array('article_name' => $article->getName(), 'revision' => $revision)), __('Yes')), '%cancel' => javascript_link_tag(__('cancel'), array('onclick' => "$('restore_article_revision_{$revision}').toggle();", 'style' => 'font-weight: bold;')))); ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    </td>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <?php if ($revision_count > 1): ?>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">&nbsp;</td>
                                            <td colspan="2" style="text-align: center;"><input type="submit" value="<?php echo __('Compare'); ?>"></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </tfoot>
                                <?php endif; ?>
                            </table>
                        </form>
                    <?php elseif ($history_action == 'diff'): ?>
                        <p style="padding: 0 5px 10px 10px; font-size: 13px;">
                            <?php echo '<b>'.__('Showing the difference between revisions: %from_revision &rArr; %to_revision', array('&rArr;' => '<b>&rarr;</b>', '%from_revision' => '</b><i>'.__('%revision_number, by %author [%date]', array('%revision_number' => link_tag(make_url('publish_article_revision', array('article_name' => $article->getName(), 'revision' => $from_revision)), $from_revision, array('style' => 'font-weight: bold;')), '%author' => $from_revision_author, '%date' => tbg_formatTime($from_revision_date, 20))).'</i>', '%to_revision' => '<i>'.__('%revision_number, by %author [%date]', array('%revision_number' => (($to_revision < $revision_count) ? link_tag(make_url('publish_article_revision', array('article_name' => $article->getName(), 'revision' => $to_revision)), $to_revision, array('style' => 'font-weight: bold;')) : $to_revision)."/{$revision_count}</b>", '%author' => $to_revision_author, '%date' => tbg_formatTime($to_revision_date, 20))).'</i>'), true); ?><br />
                            <?php echo link_tag(make_url('publish_article_history', array('article_name' => $article->getName())), '&lt;&lt; '.__('Back to history')); ?>
                        </p>
                        <?php $cc = 1; ?>
                        <table cellpadding="0" cellspacing="0" id="article_diff">
                            <?php $odd = true; ?>
                            <?php foreach ($diff as $line): ?>
                                <tr<?php if ($odd): ?> class="odd"<?php endif; ?>>
                                    <td style="width: 40px; text-align: right; font-weight: bold; padding-right: 5px;"><?php echo $cc; ?>.</td>
                                    <td style="padding: 0;"><?php echo $line; ?></td>
                                </tr>
                                <?php $cc++; ?>
                                <?php $odd = !$odd; ?>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                <?php else: ?>
                    <?php include_component('publish/placeholder', array('article_name' => $article_name, 'nocreate' => true)); ?>
                <?php endif; ?>
            </div>
        </td>
    </tr>
</table>
