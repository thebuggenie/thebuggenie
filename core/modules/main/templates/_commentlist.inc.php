<?php foreach (\thebuggenie\core\entities\Comment::getComments($target_id, $target_type, $tbg_user->getCommentSortOrder()) as $comment): ?>
    <?php

    $options = compact('comment', 'comment_count_div', 'mentionable_target_type');
    if (isset($issue))
        $options['issue'] = $issue;

    include_component('main/comment', $options);

    ?>
<?php endforeach; ?>

