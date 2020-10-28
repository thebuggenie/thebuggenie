<div id="workflow_transition_container" style="display: none;">
    <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
        <?php foreach ($issue->getAvailableWorkflowTransitions() as $transition): ?>
            <?php if ($transition instanceof \thebuggenie\core\entities\WorkflowTransition && $transition->hasTemplate()): ?>
                <?php
                    $compact_array_vals = array(); $defd_vars = get_defined_vars();
                    foreach (array('issue', 'transition', 'ajax') as $caval) {
                        if (array_key_exists($caval, $defd_vars)) {
                            $compact_array_vals[] = $caval;
                        }
                    }
                    include_component($transition->getTemplate(), compact($compact_array_vals));
                ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<div id="workflow_transition_fullpage" class="fullpage_backdrop" style="display: none;"></div>
