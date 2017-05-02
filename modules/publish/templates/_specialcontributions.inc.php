<div class="article">
    <?php if ($username === null && empty($contributions)): ?>
        <div class="header" >
            <?= __('Special:Contributions to %namespace namespace',
                          array('%namespace' => ($projectnamespace ? $projectnamespace : __('global')))); ?>
        </div>
        <p>
            <?= __('There are no contributions to this namespace or you are not allowed to access any of the articles in the namespace.'); ?>
        </p>
    <?php elseif ($username === null && !empty($contributions)):  ?>
        <div class="header" >
            <?= __('Special:Contributions to %namespace namespace',
                          array('%namespace' => ($projectnamespace ? $projectnamespace : __('global')))); ?>
        </div>
        <p>
            <?= __('Below is a listing of all contributions made to %namespace namespace. Contributions are listed only for articles you are allowed to access.',
                          array('%namespace' => ($projectnamespace ? $projectnamespace : 'global'))); ?>
        </p>
    <?php elseif ($username !== null && $user === null): ?>
        <div class="header">
            <?= __('Special:Contributions - No such user %username', array('%username' => $username)); ?>
        </div>
        <p>
            <?= __('User with specified username does not exist.'); ?>
        </p>
    <?php elseif ($username !== null && $user !== null && empty($contributions)): ?>
        <div class="header">
            <?= __('Special:Contributions to %namespace namespace by %username',
                          array('%namespace' => ($projectnamespace ? $projectnamespace : __('global')),
                                '%username' => $user)); ?>
        </div>
        <p>
            <?= __('This user has made no contributions to the wiki or you are not allowed to access any of the articles the user has contributed to.'); ?>
        </p>
    <?php elseif ($username !== null && $user !== null): ?>
        <div class="header"><?= __('Special:Contributions to %namespace namespace by %username',
                                          array('%namespace' => ($projectnamespace ? $projectnamespace : __('global')),
                                                '%username' => $user)); ?>

        </div>
        <p>
            <?= __('Below is a listing of all contributions made by this user to %namespace namespace. Contributions are listed only for articles you are allowed to access.',
                          array('%namespace' => ($projectnamespace ? $projectnamespace : 'global'))); ?>
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
                    <?php for ($i = $page_start_at; $i <= $page_end_at; $i++): ?>
                        <tr>
                            <td><?= link_tag($contributions[$i]['revision_url'], tbg_formatTime($contributions[$i]['date'], 1)); ?></td>
                            <td>(
                                <?= ($contributions[$i]['diff_url']) ? link_tag($contributions[$i]['diff_url'], 'diff') : 'diff'; ?>
                                |
                                <?= link_tag($contributions[$i]['history_url'], 'hist'); ?>
                                )</td>
                            <td><?= link_tag($contributions[$i]['article_url'], $contributions[$i]['article_name']); ?></td>
                            <?php if ($username === null): ?>
                                <?php if ($contributions[$i]['author'] !== null): ?>
                                    <td><?= link_tag($contributions[$i]['author_contributions_url'], $contributions[$i]['author']); ?></td>
                                <?php else: ?>
                                    <td><em><?= __('no author'); ?></em></td>
                                <?php endif?>
                            <?php endif ?>
                            <td><?= $contributions[$i]['reason']; ?></td>
                        </tr>
                    <?php endfor ?>
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
