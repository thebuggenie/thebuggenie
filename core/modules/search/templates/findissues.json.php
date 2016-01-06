<?php

    $issues = [];
    if ($search_object->getNumberOfIssues()) {
        foreach ($search_object->getIssues() as $issue) {
            $issues[] = $issue->toJSON();
        }
    }

    echo json_encode($issues);
