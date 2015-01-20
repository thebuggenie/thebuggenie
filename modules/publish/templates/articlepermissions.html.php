<?php

    include_component('publish/wikibreadcrumbs', array('article_name' => $article_name));
    \thebuggenie\core\framework\Context::loadLibrary('publish/publish');
    $tbg_response->setTitle(__('%article_name permissions', array('%article_name' => $article_name)));

?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
    <tr>
        <td class="side_bar">
            <?php include_component('leftmenu', array('article' => $article)); ?>
        </td>
        <td class="main_area article">
            <a name="top"></a>
            <div class="article" style="width: auto; padding: 5px; position: relative;">
                <?php include_component('publish/header', array('article' => $article, 'article_name' => $article_name, 'show_actions' => true, 'mode' => 'permissions')); ?>
                <?php if ($article instanceof \thebuggenie\modules\publish\entities\Article): ?>
                    <?php if (\thebuggenie\core\framework\Context::getModule('publish')->canUserEditArticle($article_name)): ?>
                        <ul class="simple_list">
                        <?php foreach ($namespaces as $namespace): ?>
                            <li class="rounded_box <?php if (!(is_numeric($namespace) && $namespace == 0) && $namespace == $article->getName()): ?>verylightyellow<?php else: ?>invisible borderless<?php endif; ?>" style="padding: 10px;">
                                <div class="namespace_header">
                                    <?php if (is_numeric($namespace) && $namespace == 0): ?>
                                        <?php echo __('Specify permissions for entire wiki'); ?>
                                    <?php elseif ($namespace == $article->getName()): ?>
                                        <?php echo __('Specify permissions for the article %article_name', array('%article_name' => '<span class="namespace">'.$namespace.'</span>')); ?>
                                    <?php elseif ($namespace == "Category"): ?>
                                        <?php echo __('Specify permissions to edit categories'); ?>
                                    <?php else: ?>
                                        <?php echo __('Specify permissions for the %namespace namespace', array('%namespace' => '<span class="namespace">'.$namespace.'</span>')); ?>
                                    <?php endif; ?>
                                </div>
                                <?php if (is_numeric($namespace) && $namespace == 0): ?>
                                    <?php echo __('Select this option to specify permissions for the entire wiki.'); ?>
                                <?php elseif ($namespace == $article->getName()): ?>
                                    <?php echo __('Select this option to specify permissions for this article.'); ?>
                                <?php elseif ($namespace == "Category"): ?>
                                    <?php echo __('Select this option to specify permissions for who can create and edit categories.'); ?>
                                <?php else: ?>
                                    <?php echo __('Specify permissions for the %namespace namespace. These permissions will apply for all articles in the mentioned namespace for which article-specific permissions, or child-namespace permissions have not been granted.', array('%namespace' => '<i>'.$namespace.'</i>')); ?>
                                <?php endif; ?>
                                <div style="text-align: right; padding: 10px;">
                                    <button onclick="$('publish_<?php echo $namespace; ?>_readarticle_permissions').toggle();"><?php echo __('Edit read permissions'); ?></button>
                                    <button onclick="$('publish_<?php echo $namespace; ?>_editarticle_permissions').toggle();"><?php echo __('Edit write permissions'); ?></button>
                                    <button onclick="$('publish_<?php echo $namespace; ?>_deletearticle_permissions').toggle();"><?php echo __('Edit delete permissions'); ?></button>
                                </div>
                                <div id="publish_<?php echo $namespace; ?>_readarticle_permissions" style="padding: 10px; width: 700px; display: none;">
                                    <?php include_component('configuration/permissionsinfo', array('key' => \thebuggenie\modules\publish\Publish::PERMISSION_READ_ARTICLE, 'mode' => 'module_permissions', 'target_id' => $namespace, 'module' => 'publish', 'access_level' => \thebuggenie\core\framework\Settings::ACCESS_FULL)); ?>
                                </div>
                                <div id="publish_<?php echo $namespace; ?>_editarticle_permissions" style="padding: 10px; width: 700px; display: none;">
                                    <?php include_component('configuration/permissionsinfo', array('key' => \thebuggenie\modules\publish\Publish::PERMISSION_EDIT_ARTICLE, 'mode' => 'module_permissions', 'target_id' => $namespace, 'module' => 'publish', 'access_level' => \thebuggenie\core\framework\Settings::ACCESS_FULL)); ?>
                                </div>
                                <div id="publish_<?php echo $namespace; ?>_deletearticle_permissions" style="padding: 10px; width: 700px; display: none;">
                                    <?php include_component('configuration/permissionsinfo', array('key' => \thebuggenie\modules\publish\Publish::PERMISSION_DELETE_ARTICLE, 'mode' => 'module_permissions', 'target_id' => $namespace, 'module' => 'publish', 'access_level' => \thebuggenie\core\framework\Settings::ACCESS_FULL)); ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="redbox" style="margin: 0 5px 5px 5px; font-size: 14px;">
                            <?php echo __('You do not have access to edit permissions for this article'); ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <?php include_component('publish/placeholder', array('article_name' => $article_name, 'nocreate' => true)); ?>
                <?php endif; ?>
            </div>
        </td>
    </tr>
</table>
