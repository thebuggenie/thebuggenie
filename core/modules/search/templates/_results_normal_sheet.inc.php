<?php

    $headers = array(__("Project"), __("Issue type"), __("Issue number"), __("Issue title"), __("Description"), __("Reproduction steps"), __("Posted by"), __("Assigned to"), __("Status"), __('Category'), __('Priority'), __('Reproducability'), __('Severity'), __("Resolution"), __('Targetted for'), __("Posted at"), __("Last updated"), __("Percentage complete"), __("Time estimated"), __("Time spent"), __("User pain"), __("Votes"));
    foreach ($custom_columns as $column) {
        $headers[] = __($column->getName());
    }

    foreach ($headers as $index => $header) {
        $sheet->setCellValueByColumnAndRow($index, 1, $header);
    }

    if ($search_object->getNumberOfIssues()) {
        $cc = 2;
        foreach ($search_object->getIssues() as $issue) {
            $temp = $issue->getAssignee();
            if ($temp instanceof thebuggenie\core\entities\User && !($temp->isDeleted())) {
                $assignee = $temp->getBuddyname();
            } elseif ($temp instanceof thebuggenie\core\entities\Team) {
                $assignee = $temp->getName();
            } else {
                $assignee = '-';
            }

            $temp = $issue->getPostedBy();
            if ($temp instanceof thebuggenie\core\entities\User && !($temp->isDeleted())) {
                $posted_by = $temp->getBuddyname();
            } else {
                $posted_by = '-';
            }

            $temp = $issue->getStatus();
            if ($temp instanceof thebuggenie\core\entities\Status) {
                $status = $temp->getName();
            } else {
                $status = '-';
            }

            $temp = $issue->getPriority();
            if ($temp instanceof thebuggenie\core\entities\Priority) {
                $priority = $temp->getName();
            } else {
                $priority = '-';
            }

            $temp = $issue->getResolution();
            if ($temp instanceof thebuggenie\core\entities\Resolution) {
                $resolution = $temp->getName();
            } else {
                $resolution = '-';
            }

            $temp = $issue->getCategory();
            if ($temp instanceof thebuggenie\core\entities\Category) {
                $category = $temp->getName();
            } else {
                $category = '-';
            }

            $temp = $issue->getReproducability();
            if ($temp instanceof thebuggenie\core\entities\Reproducability) {
                $reproducability = $temp->getName();
            } else {
                $reproducability = '-';
            }

            $temp = $issue->getSeverity();
            if ($temp instanceof thebuggenie\core\entities\Severity) {
                $severity = $temp->getName();
            } else {
                $severity = '-';
            }

            $temp = $issue->getMilestone();
            if ($temp instanceof thebuggenie\core\entities\Milestone) {
                $milestone = $temp->getName();
            } else {
                $milestone = '-';
            }

            unset($temp);

            $sheet->setCellValueByColumnAndRow(0, $cc, $issue->getProject()->getName());
            $sheet->setCellValueByColumnAndRow(1, $cc, $issue->getIssueType()->getName());
            $sheet->setCellValueByColumnAndRow(2, $cc, $issue->getFormattedIssueNo());
            $sheet->setCellValueByColumnAndRow(3, $cc, $issue->getRawTitle());
            $sheet->setCellValueByColumnAndRow(4, $cc, $issue->getDescription());
            $sheet->setCellValueByColumnAndRow(5, $cc, $issue->getReproductionSteps());
            $sheet->setCellValueByColumnAndRow(6, $cc, $posted_by);
            $sheet->setCellValueByColumnAndRow(7, $cc, $assignee);
            $sheet->setCellValueByColumnAndRow(8, $cc, $status);
            $sheet->setCellValueByColumnAndRow(9, $cc, $category);
            $sheet->setCellValueByColumnAndRow(10, $cc, $priority);
            $sheet->setCellValueByColumnAndRow(11, $cc, $reproducability);
            $sheet->setCellValueByColumnAndRow(12, $cc, $severity);
            $sheet->setCellValueByColumnAndRow(13, $cc, $resolution);
            $sheet->setCellValueByColumnAndRow(14, $cc, $milestone);
            $sheet->setCellValueByColumnAndRow(15, $cc, tbg_formatTime($issue->getPosted(), 21));
            $sheet->setCellValueByColumnAndRow(16, $cc, tbg_formatTime($issue->getLastUpdatedTime(), 21));
            $sheet->setCellValueByColumnAndRow(17, $cc, $issue->getPercentCompleted() . '%');
            $sheet->setCellValueByColumnAndRow(18, $cc, (!$issue->hasEstimatedTime()) ? '-' : \thebuggenie\core\entities\Issue::getFormattedTime($issue->getEstimatedTime(true, true)));
            $sheet->setCellValueByColumnAndRow(19, $cc, (!$issue->hasSpentTime()) ? '-' : \thebuggenie\core\entities\Issue::getFormattedTime($issue->getSpentTime(true, true)));
            $sheet->setCellValueByColumnAndRow(20, $cc, $issue->getUserpain());
            $sheet->setCellValueByColumnAndRow(21, $cc, $issue->getVotes());
            $start_column = 22;
            foreach ($custom_columns as $column) {
                $value = $issue->getCustomField($column->getKey());
                switch ($column->getType()) {
                    case \thebuggenie\core\entities\CustomDatatype::DATE_PICKER:
                        $value = strtotime($value) !== false ? tbg_formatTime($value, 20) : '';
                        break;
                    case \thebuggenie\core\entities\CustomDatatype::DROPDOWN_CHOICE_TEXT:
                    case \thebuggenie\core\entities\CustomDatatype::RADIO_CHOICE:
                        $value = ($value instanceof \thebuggenie\core\entities\CustomDatatypeOption) ? $value->getValue() : '';
                        break;
                    case \thebuggenie\core\entities\CustomDatatype::CLIENT_CHOICE:
                    case \thebuggenie\core\entities\CustomDatatype::COMPONENTS_CHOICE:
                    case \thebuggenie\core\entities\CustomDatatype::EDITIONS_CHOICE:
                    case \thebuggenie\core\entities\CustomDatatype::MILESTONE_CHOICE:
                    case \thebuggenie\core\entities\CustomDatatype::RELEASES_CHOICE:
                    case \thebuggenie\core\entities\CustomDatatype::STATUS_CHOICE:
                    case \thebuggenie\core\entities\CustomDatatype::TEAM_CHOICE:
                    case \thebuggenie\core\entities\CustomDatatype::USER_CHOICE:
                        $value = ($value instanceof \thebuggenie\core\entities\common\Identifiable) ? $value->getName() : '';
                        break;
                    case \thebuggenie\core\entities\CustomDatatype::DATETIME_PICKER:
                        $value = strtotime($value) !== false ? tbg_formatTime($value, 25) : '';
                        break;
                    case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXT:
                    case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_MAIN:
                    case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_SMALL:
                    default:
                        break;
                }
                $sheet->setCellValueByColumnAndRow($start_column, $cc, $value);
                $start_column++;
            }

            $cc++;
        }
    }

    ob_end_clean();

    switch ($format) {
        case 'xlsx':
            $objWriter = new \PHPExcel_Writer_Excel2007($phpexcel);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="issues.xlsx"');
            break;
        case 'ods':
        default:
            header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
            header('Content-Disposition: attachment;filename="issues.ods"');
            $objWriter = new \PHPExcel_Writer_OpenDocument($phpexcel);
    }

    header('Cache-Control: max-age=0');

    $objWriter->save('php://output');
    exit();
