<?php if ($project_count > 0): ?>
    <ul class="project_list simple_list">
        <?php foreach ($projects as $project): ?>
            <li><?php include_component('project/project', compact('project')); ?></li>
        <?php endforeach; ?>
    </ul>
    <?php if ($pagination->getTotalPages() > 1): ?>
        <?php include_component('main/pagination', compact('pagination')); ?>
    <?php endif; ?>
<?php else: ?>
    <div class="onboarding large">
        <?= image_tag('onboard_noprojects.png'); ?>
        <div class="helper-text">
            <?php if ($list_mode == 'all'): ?>
                <?php if ($show_project_config_link): ?>
                    <?= __('There are no projects. Get started by clicking the "%create_project" button', ['%create_project' => __('Create project')]); ?>
                <?php elseif ($project_state == 'archived'): ?>
                    <?= __("There are no archived projects."); ?>
                <?php else: ?>
                    <?= __("You don't have access to any projects yet."); ?>
                <?php endif; ?>
            <?php elseif ($list_mode == 'team'): ?>
                <?php if ($show_project_config_link): ?>
                    <?= __('There are no projects linked to this team. Get started by clicking the "%create_project" button', ['%create_project' => __('Create project')]); ?>
                <?php elseif ($project_state == 'archived'): ?>
                    <?= __("There are no archived projects for this team."); ?>
                <?php else: ?>
                    <?= __("There are no projects linked to this team."); ?>
                <?php endif; ?>
            <?php elseif ($list_mode == 'client'): ?>
                <?php if ($show_project_config_link): ?>
                    <?= __('There are no projects linked to this client. Get started by clicking the "%create_project" button', ['%create_project' => __('Create project')]); ?>
                <?php elseif ($project_state == 'archived'): ?>
                    <?= __("There are no archived projects for this team."); ?>
                <?php else: ?>
                    <?= __("There are no projects linked to this client."); ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
