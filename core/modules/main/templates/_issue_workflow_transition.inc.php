<div id="workflow_transition_container" style="display: none;">
    <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
        <?php foreach ($issue->getAvailableWorkflowTransitions() as $transition): ?>
            <?php if ($transition instanceof \thebuggenie\core\entities\WorkflowTransition && $transition->hasTemplate()): ?>
                <?php include_component($transition->getTemplate(), compact('issue', 'transition', 'ajax')); ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<div id="workflow_transition_fullpage" class="fullpage_backdrop" style="display: none;"></div>
