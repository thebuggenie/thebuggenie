<?php
    if (!is_array($commits))
    {
        return; // silently quit
    }
    
    foreach ($commits as $commit)
    {
        include_component('livelink/commitbox', array('project' => $selected_project, 'commit' => $commit, 'branch' => $branch));
    }