<?php
    $article_name = $article->getName();
?>
<div class="header <?php echo $mode; ?>">
    <?php if ($mode != 'edit'): ?>
        <div class="title_left_images">
            <?php if ($tbg_user->isGuest()): ?>
                <?php echo image_tag('star_faded.png', array('id' => 'article_favourite_faded_'.$article->getId())); ?>
                <div class="tooltip from-above leftie">
                    <?php echo __('Please log in to subscribe to updates for this article'); ?>
                </div>
            <?php else: ?>
                <div class="tooltip from-above leftie">
                    <?php echo __('Click the star to toggle whether you want to be notified whenever this article updates or changes'); ?><br>
                </div>
                <?php echo image_tag('spinning_20.gif', array('id' => 'article_favourite_indicator_'.$article->getId(), 'style' => 'display: none;')); ?>
                <?php echo image_tag('star_faded.png', array('id' => 'article_favourite_faded_'.$article->getId(), 'style' => 'cursor: pointer;'.(($tbg_user->isArticleStarred($article->getID())) ? 'display: none;' : ''), 'onclick' => "TBG.Main.toggleFavouriteArticle('".make_url('toggle_favourite_article', array('article_id' => $article->getID(), 'user_id' => $tbg_user->getID()))."', ".$article->getID().");")); ?>
                <?php echo image_tag('star.png', array('id' => 'article_favourite_normal_'.$article->getId(), 'style' => 'cursor: pointer;'.((!$tbg_user->isArticleStarred($article->getID())) ? 'display: none;' : ''), 'onclick' => "TBG.Main.toggleFavouriteArticle('".make_url('toggle_favourite_article', array('article_id' => $article->getID(), 'user_id' => $tbg_user->getID()))."', ".$article->getID().");")); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if ($article->getID() || $mode == 'edit'): ?>
        <?php if ($show_actions): ?>
            <div class="button-group">
                <?php if ($article->getID() && $mode != 'view'): ?>
                    <?php echo link_tag(make_url('publish_article', array('article_name' => $article->getName())), __('Show'), array('class' => 'button button-silver')); ?>
                <?php endif; ?>
                <?php if ((isset($article) && $article->canEdit()) || (!isset($article) && ((\thebuggenie\core\framework\Context::isProjectContext() && !\thebuggenie\core\framework\Context::getCurrentProject()->isArchived()) || (!\thebuggenie\core\framework\Context::isProjectContext() && \thebuggenie\core\framework\Context::getModule('publish')->canUserEditArticle($article_name))))): ?>
                    <?php if ($mode == 'edit'): ?>
                        <?php echo javascript_link_tag(($article->getID()) ? __('Edit') : __('Create new article'), array('class' => 'button button-silver button-pressed')); ?>
                    <?php else: ?>
                        <?php echo link_tag(make_url('publish_article_edit', array('article_name' => $article_name)), ($article->getID()) ? __('Edit') : __('Create new article'), array('class' => 'button button-silver')); ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (!isset($embedded) || !$embedded): ?>
                    <a class="button button-silver more_actions_button dropper last" id="more_actions_article_<?php echo $article->getID(); ?>_button"><?php echo __('More actions'); ?></a>
                    <ul class="simple_list rounded_box white shadowed more_actions_dropdown popup_box" onclick="$('more_actions_article_<?php echo $article->getID(); ?>_button').toggleClassName('button-pressed');TBG.Main.Profile.clearPopupsAndButtons();">
                        <?php if ($mode == 'edit'): ?>
                            <li><a href="javascript:void(0);" onclick="$('main_container').toggleClassName('distraction-free');"><?php echo __('Toggle distraction-free writing'); ?></a></li>
                            <li class="separator"></li>
                            <li class="parent_article_selector_menu_entry"><a href="javascript:void(0);" onclick="$('parent_selector_container').toggle();TBG.Main.loadParentArticles();"><?php echo __('Select parent article'); ?></a></li>
                        <?php endif; ?>
                        <?php if ($article->getID()): ?>
                            <li<?php if ($mode == 'history'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('publish_article_history', array('article_name' => $article_name)), __('History')); ?></li>
                            <?php if (in_array($mode, array('show', 'edit')) && \thebuggenie\core\framework\Settings::isUploadsEnabled() && $article->canEdit()): ?>
                                <li><a href="javascript:void(0);" onclick="TBG.Main.showUploader('<?php echo make_url('get_partial_for_backdrop', array('key' => 'uploader', 'mode' => 'article', 'article_name' => $article->getName())); ?>');"><?php echo __('Attach a file'); ?></a></li>
                            <?php endif; ?>
                            <?php if (isset($article) && $article->canEdit()): ?>
                                <li<?php if ($mode == 'permissions'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('publish_article_permissions', array('article_name' => $article_name)), __('Permissions')); ?></li>
                                <li class="separator"></li>
                                <li><?php echo link_tag(make_url('publish_article_new', array('parent_article_name' => $article_name)), __('Create new article here')); ?></li>
                                <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
                                    <li><?php echo link_tag(make_url('publish_article_new', array('parent_article_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getName().':')), __('Create new article')); ?></li>
                                <?php else: ?>
                                    <li><?php echo link_tag(make_url('publish_article_new'), __('Create new article')); ?></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <li class="separator"></li>
                            <?php if ($article->canDelete()): ?>
                                <li><?php echo javascript_link_tag(__('Delete this article'), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__('Please confirm')."', '".__('Do you really want to delete this article?')."', {yes: {click: function () { TBG.Main.deleteArticle('".make_url('publish_article_delete', array('article_name' => $article->getName()))."') }}, no: {click: TBG.Main.Helpers.Dialog.dismiss}})")); ?></li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($mode == 'edit'): ?>
        <div id="article_edit_header_information">
            <div id="article_parent_container">
                <input type="hidden" name="parent_article_name" id="parent_article_name" value="<?php echo ($article->getParentArticleName()) ? $article->getParentArticleName() : htmlentities($tbg_request['parent_article_name'], ENT_COMPAT, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>" style="width: 400px;">
                <span class="parent_article_name <?php if (!$article->getParentArticle() instanceof \thebuggenie\modules\publish\entities\Article) echo ' faded_out'; ?>">
                    <span id="parent_article_name_span">
                        <?php if ($article->getParentArticle() instanceof \thebuggenie\modules\publish\entities\Article): ?>
                            <?php echo ($article->getParentArticle()->getManualName()) ? $article->getParentArticle()->getManualName() : $article->getParentArticle()->getName(); ?>
                        <?php else: ?>
                            <?php echo __('No parent article'); ?>
                        <?php endif; ?>
                    </span>
                &nbsp;&raquo;</span>
            </div>
            <input type="text" name="new_article_name" id="new_article_name" value="<?php echo $article->getName(); ?>">
            <input type="text" name="manual_name" id="manual_name" value="<?php echo $article->getManualName(); ?>">
        </div>
    <?php else: ?>
    <?php
        if ($article->getArticleType() == \thebuggenie\modules\publish\entities\Article::TYPE_MANUAL)
        {
            echo $article->getManualName();
        }
        else
        {
            $namespaces = explode(':', $article_name);
            if (count($namespaces) > 1 && $namespaces[0] == 'Category')
            {
                array_shift($namespaces);
                echo '<span class="faded_out blue">Category:</span>';
            }
            if (\thebuggenie\core\framework\Context::isProjectContext() && count($namespaces) > 1 && mb_strtolower($namespaces[0]) == \thebuggenie\core\framework\Context::getCurrentProject()->getKey())
            {
                array_shift($namespaces);
                echo '<span>', \thebuggenie\core\framework\Context::getCurrentProject()->getName(), ':</span>';
            }
            echo get_spaced_name(implode(':', $namespaces));
        }
    ?>
    <?php endif; ?>
    <?php

        if ($article->getID() && $mode)
        {
            switch ($mode)
            {
                /* case 'edit':
                    ?><span class="faded_out"><?php echo __('%article_name ~ Edit', array('%article_name' => '')); ?></span><?php
                    break; */
                case 'history':
                    ?><span class="faded_out"><?php echo __('%article_name ~ History', array('%article_name' => '')); ?></span><?php
                    break;
                case 'permissions':
                    ?><span class="faded_out"><?php echo __('%article_name ~ Permissions', array('%article_name' => '')); ?></span><?php
                    break;
                case 'attachments':
                    ?><span class="faded_out"><?php echo __('%article_name ~ Attachments', array('%article_name' => '')); ?></span><?php
                    break;
            }
        }

    ?>
</div>
