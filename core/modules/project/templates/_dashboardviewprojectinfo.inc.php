<?php
    $projectHasDescription = $view->getProject()->hasDescription();
?>
<div id="project_description"<?php if(!$projectHasDescription) echo ' class="none"'; ?>>
    <?php
        if ($projectHasDescription)
            echo tbg_parse_text($view->getProject()->getDescription());
        else
            echo __('This project has no description');
    ?>
</div>
<?php if ($view->getProject()->hasOwner()): ?>
    <div class="project_role">
        <div class="label"><?php echo __('Owned by: %name', array('%name' => '')); ?></div>
        <div class="value">
        <?php if ($view->getProject()->getOwner() instanceof \thebuggenie\core\entities\Team): ?>
            <?php include_component('main/teamdropdown', array('team' => $view->getProject()->getOwner())); ?>
        <?php else: ?>
            <?php include_component('main/userdropdown', array('user' => $view->getProject()->getOwner())); ?>
        <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php if ($view->getProject()->hasLeader()): ?>
    <div class="project_role">
        <div class="label"><?php echo __('Lead by: %name', array('%name' => '')); ?></div>
        <div class="value">
        <?php if ($view->getProject()->getLeader() instanceof \thebuggenie\core\entities\Team): ?>
            <?php include_component('main/teamdropdown', array('team' => $view->getProject()->getLeader())); ?>
        <?php else: ?>
            <?php include_component('main/userdropdown', array('user' => $view->getProject()->getLeader())); ?>
        <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php if ($view->getProject()->hasQaResponsible()): ?>
    <div class="project_role">
        <div class="label"><?php echo __('QA responsible: %name', array('%name' => '')); ?></div>
        <div class="value">
        <?php if ($view->getProject()->getQaResponsible() instanceof \thebuggenie\core\entities\Team): ?>
            <?php include_component('main/teamdropdown', array('team' => $view->getProject()->getQaResponsible())); ?>
        <?php else: ?>
            <?php include_component('main/userdropdown', array('user' => $view->getProject()->getQaResponsible())); ?>
        <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php if ($view->getProject()->hasHomepage()): ?>
    <a class="button button-silver dash" href="<?php echo $view->getProject()->getHomepage(); ?>" target="_blank"><?php echo __('Visit homepage'); ?></a>
<?php endif; ?>
<?php if ($view->getProject()->hasDocumentationURL()): ?>
    <a class="button button-silver dash" href="<?php echo $view->getProject()->getDocumentationURL(); ?>" target="_blank"><?php echo __('Open documentation'); ?></a>
<?php endif; ?>
<br style="clear: both;">
