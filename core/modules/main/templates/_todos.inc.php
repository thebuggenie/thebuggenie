<div id="todos_box">
    <div class="faded_out todos_none" id="todos_none" <?php if ($issue->countTodos() != 0): ?>style="display: none;"<?php endif; ?>><?php echo __('There are no todos'); ?></div>
    <ul class="simple_list" id="todos_list">
        <?php foreach ($issue->getTodos()['issue'] as $todo_key => $todo): ?>
            <?php include_component('main/todo', array_merge(compact('todo', 'issue'), array('done' => false, 'todo_key' => 'todos_' . ($todo_key + 1)))); ?>
        <?php endforeach; ?>
    </ul>
    <?php foreach ($issue->getTodos()['comments'] as $comment_id => $comment_todos): ?>
        <?php echo include_component('main/userdropdown', array('user' => $issue->getComments()[$comment_id]->getPostedBy(), 'size' => 'small')); ?>
        <ul class="simple_list" id="comment_<?php echo $comment_id; ?>_todos_list">
            <?php foreach ($comment_todos as $todo_key => $todo): ?>
                <?php include_component('main/todo', array_merge(compact('todo', 'issue'), array('done' => false, 'todo_key' => 'comment_' . $comment_id . '_todos_' . ($todo_key + 1)))); ?>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>
</div>
<div id="done_todos_box">
    <div class="header"><?php echo __('Done'); ?></div>
    <div class="faded_out done_todos_none" id="done_todos_none" <?php if ($issue->countDoneTodos() != 0): ?>style="display: none;"<?php endif; ?>><?php echo __('There are no done todos'); ?></div>
    <?php foreach ($issue->getDoneTodos()['issue'] as $todo_key => $todo): ?>
        <?php include_component('main/todo', array_merge(compact('todo', 'issue'), array('done' => true, 'todo_key' => $todo_key . '_done'))); ?>
    <?php endforeach; ?>
    <?php foreach ($issue->getDoneTodos()['comments'] as $comment_id => $comment_todos): ?>
        <?php echo include_component('main/userdropdown', array('user' => $issue->getComments()[$comment_id]->getPostedBy(), 'size' => 'small')); ?>
        <?php foreach ($comment_todos as $todo_key => $todo): ?>
            <?php include_component('main/todo', array_merge(compact('todo', 'issue'), array('done' => true, 'todo_key' => $todo_key . '_comment_done'))); ?>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>
<script type="text/javascript">
    require(['domReady', 'prototype', 'dragdrop'], function (domReady) {
        domReady(function () {
            $('viewissue_todo_count').update(<?php echo $issue->countTodos(); ?>);
            Sortable.destroy('todos_list');
            Sortable.create('todos_list', {constraint: '', onUpdate: function(container) { TBG.Issues.saveTodosOrder(container, '<?php echo make_url('todo_saveorder', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'comment_id' => 0)); ?>'); }});
            <?php foreach ($issue->getTodos()['comments'] as $comment_id => $comment_todos): ?>
                Sortable.destroy('comment_<?php echo $comment_id; ?>_todos_list');
                Sortable.create('comment_<?php echo $comment_id; ?>_todos_list', {constraint: '', onUpdate: function(container) { TBG.Issues.saveTodosOrder(container, '<?php echo make_url('todo_saveorder', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'comment_id' => $comment_id)); ?>'); }});
            <?php endforeach; ?>
        });
    });
</script>