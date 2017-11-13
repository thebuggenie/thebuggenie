<?php

    if ($tbg_user->hasProjectPageAccess('project_planning', $project) || $tbg_user->hasProjectPageAccess('project_only_planning', $project)) {
        echo link_tag(make_url('agile_index', array('project_key' => $project->getKey())), fa_image_tag('tasks').'<span>'.__('Agile').'</span>', array('class' => 'nav-button'));
    }
