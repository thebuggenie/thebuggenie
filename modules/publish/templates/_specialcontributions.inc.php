<div class="article">


    <?php if ($username === null): ?>
    <div class="header" >
        <?= __('Special:Contributions to %namespace namespace',
               array('%namespace' => ($projectnamespace ? $projectnamespace : __('global')))); ?>
    </div>
    <p>
        <?php if (empty($contributions)): ?>
            <?= __('There are no contributions to this namespace or you are not allowed to access any of the articles in the namespace.'); ?>
        <?php else: ?>
            <?= __('Below is a listing of all contributions made to %namespace namespace. Contributions are listed only for articles you are allowed to access.',
                   array('%namespace' => ($projectnamespace ? $projectnamespace : 'global'))); ?>
        <?php endif ?>
    </p>


    <?php elseif ($username === ""): ?>
    <div class="header" >
        <?= __('Special:Contributions to %namespace namespace created via initial fixtures',
               array('%namespace' => ($projectnamespace ? $projectnamespace : __('global')))); ?>
    </div>
    <p>
        <?php if (empty($contributions)): ?>
        <?= __('There are no contributions to this namespace or you are not allowed to access any of the articles in the namespace.'); ?>
            <?php else: ?>
        <?= __('Below is a listing of all contributions made via initial fixtures to %namespace namespace. Contributions are listed only for articles you are allowed to access.',
               array('%namespace' => ($projectnamespace ? $projectnamespace : 'global'))); ?>
        <?php endif ?>
    </p>


    <?php elseif ($invalid_user): ?>
    <div class="header">
        <?= __('Special:Contributions - No such user %username', array('%username' => $username)); ?>
    </div>
    <p>
        <?= __('User with specified username does not exist.'); ?>
    </p>


    <?php else: ?>
    <div class="header">
        <?= __('Special:Contributions to %namespace namespace by %user_full_name',
               array('%namespace' => ($projectnamespace ? $projectnamespace : __('global')),
                     '%user_full_name' => $user_full_name)); ?>
    </div>
    <p>
        <?php if (empty($contributions)): ?>
            <?= __('This user has made no contributions to the wiki or you are not allowed to access any of the articles the user has contributed to.'); ?>
        <?php else: ?>
            <?= __('Below is a listing of all contributions made by this user to %namespace namespace. Contributions are listed only for articles you are allowed to access.',
                   array('%namespace' => ($projectnamespace ? $projectnamespace : 'global'))); ?>
        <?php endif ?>
    </p>
    <?php endif ?>

    <?php if (!empty($contributions)): ?>
        <div class="full_width_table outter">
            <table class="full_width_table inner">
                <thead>
                    <tr>
                        <th><?= __('Date'); ?></th>
                        <th><?= __('Details'); ?></th>
                        <th><?= __('Article'); ?></th>
                        <?php if ($username === null): ?>
                            <th><?= __('Author'); ?></th>
                        <?php endif ?>
                        <th><?= __('Reason'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contributions as $revision): ?>
                        <tr>
                            <td><?= link_tag($revision->getRevisionURL(), tbg_formatTime($revision->getDate(), 1)); ?></td>
                            <td>(
                                <?= ($revision->getDiffURL() ? link_tag($revision->getDiffURL(), 'diff') : 'diff'); ?>
                                |
                                <?= link_tag($revision->getHistoryURL(), 'hist'); ?>
                                )</td>
                            <td><?= link_tag($revision->getArticleURL(), $revision->getArticleName()); ?></td>
                            <?php if ($username === null): ?>
                                <?php if ($revision->getAuthor() !== null): ?>
                                    <td><?= link_tag($revision->getAuthorContributionsURL($revision->getAuthor()), $revision->getAuthorNameWithUsername()); ?></td>
                                <?php else: ?>
                                    <td><em><?= __('no author'); ?></em></td>
                                <?php endif?>
                            <?php endif ?>
                            <td><?= $revision->getReason(); ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <div>
            View (
            <?= ($page == 1 ? __('newest') : link_tag($navigation_urls['newest'], __('newest'))); ?> |
            <?= ($page == 1 ? __('newer')  : link_tag($navigation_urls['newer'], __('newer'))); ?>   |

            <?= ($page == $total_pages ? __('older')  : link_tag($navigation_urls['older'], __('older'))); ?> |
            <?= ($page == $total_pages ? __('oldest') : link_tag($navigation_urls['oldest'], __('oldest'))); ?>
            )
            Per page (
            <?= ($page_size == 20 ? '20' : link_tag($page_size_urls[20], '20')); ?> |
            <?= ($page_size == 50 ? '50' : link_tag($page_size_urls[50], '50')); ?> |
            <?= ($page_size == 100 ? '100' : link_tag($page_size_urls[100], '100')); ?> |
            <?= ($page_size == 500 ? '500' : link_tag($page_size_urls[500], '500')); ?>
            )
        </div>
    <?php endif ?>
</div>
