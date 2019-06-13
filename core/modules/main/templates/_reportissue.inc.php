<?php

    /**
     * @var \thebuggenie\core\entities\Issuetype[] $issuetypes
     */

?>
<?php if (!empty($errors) || !(empty($permission_errors))): ?>
    <div class="rounded_box report_issue_desc red borderless" style="margin-bottom: 5px;">
        <strong><?= __('One or more errors occured when trying to file your issue'); ?>:</strong>
        <ul>
            <?php foreach ($errors as $key => $error): ?>
                <?php if (is_array($error)): ?>
                    <?php foreach ($error as $suberror): ?>
                        <li><?= $suberror; ?></li>
                    <?php endforeach; ?>
                <?php elseif (is_bool($error)): ?>
                    <li>
                        <?php if ($key == 'title' || in_array($key, \thebuggenie\core\entities\Datatype::getAvailableFields(true)) || in_array($key, array('pain_bug_type', 'pain_likelihood', 'pain_effect'))): ?>
                            <?php

                                switch ($key)
                                {
                                    case 'title':
                                        echo __('You have to specify a title');
                                        break;
                                    case 'description':
                                        echo __('You have to enter a description in the "%description" field', array('%description' => __('Description')));
                                        break;
                                    case 'shortname':
                                        echo __('You have to enter a label in the "%issue_label" field', array('%issue_label' => __('Issue label')));
                                        break;
                                    case 'reproduction_steps':
                                        echo __('You have to enter something in the "%steps_to_reproduce" field', array('%steps_to_reproduce' => __('Steps to reproduce')));
                                        break;
                                    case 'edition':
                                        echo __("Please specify a valid edition");
                                        break;
                                    case 'build':
                                        echo __("Please specify a valid version / release");
                                        break;
                                    case 'component':
                                        echo __("Please specify a valid component");
                                        break;
                                    case 'category':
                                        echo __("Please specify a valid category");
                                        break;
                                    case 'status':
                                        echo __("Please specify a valid status");
                                        break;
                                    case 'priority':
                                        echo __("Please specify a valid priority");
                                        break;
                                    case 'reproducability':
                                        echo __("Please specify a valid reproducability");
                                        break;
                                    case 'severity':
                                        echo __("Please specify a valid severity");
                                        break;
                                    case 'resolution':
                                        echo __("Please specify a valid resolution");
                                        break;
                                    case 'milestone':
                                        echo __("Please specify a valid milestone");
                                        break;
                                    case 'estimated_time':
                                        echo __("Please enter a valid estimate");
                                        break;
                                    case 'spent_time':
                                        echo __("Please enter time already spent working on this issue");
                                        break;
                                    case 'percent_complete':
                                        echo __("Please enter how many percent complete the issue already is");
                                        break;
                                    case 'pain_bug_type':
                                        echo __("Please enter a valid triaged bug type");
                                        break;
                                    case 'pain_likelihood':
                                        echo __("Please enter a valid triaged likelihood");
                                        break;
                                    case 'pain_effect':
                                        echo __("Please enter a valid triaged effect");
                                        break;
                                    default:
                                        echo __("Please triage the reported issue, so the user pain score can be properly calculated");
                                        break;
                                }

                            ?>
                        <?php elseif (\thebuggenie\core\entities\CustomDatatype::doesKeyExist($key)): ?>
                            <?= __('Required field "%field_name" is missing or invalid', array('%field_name' => \thebuggenie\core\entities\CustomDatatype::getByKey($key)->getDescription())); ?>
                        <?php else:

                            $event = new \thebuggenie\core\framework\Event('core', 'reportissue.validationerror', $key);
                            $event->setReturnValue($key);
                            $event->triggerUntilProcessed();
                            echo __('A validation error occured: %error', array('%error' => $event->getReturnValue()));

                        ?>
                        <?php endif; ?>
                    </li>
                <?php else: ?>
                    <li><?= $error; ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php foreach ($permission_errors as $key => $p_error): ?>
                <?php if (is_array($p_error)): ?>
                    <?php foreach ($p_error as $p_suberror): ?>
                        <li><?= $p_suberror; ?></li>
                    <?php endforeach; ?>
                <?php elseif (is_bool($p_error)): ?>
                    <li>
                        <?php if (in_array($key, \thebuggenie\core\entities\Datatype::getAvailableFields(true))): ?>
                            <?php

                                switch ($key)
                                {
                                    case 'description':
                                        echo __("You don't have access to enter a description");
                                        break;
                                    case 'shortname':
                                        echo __("You don't have access to enter an issue label");
                                        break;
                                    case 'reproduction_steps':
                                        echo __("You don't have access to enter steps to reproduce");
                                        break;
                                    case 'edition':
                                        echo __("You don't have access to add edition information");
                                        break;
                                    case 'build':
                                        echo __("You don't have access to enter release information");
                                        break;
                                    case 'component':
                                        echo __("You don't have access to enter component information");
                                        break;
                                    case 'category':
                                        echo __("You don't have access to specify a category");
                                        break;
                                    case 'status':
                                        echo __("You don't have access to specify a status");
                                        break;
                                    case 'priority':
                                        echo __("You don't have access to specify a priority");
                                        break;
                                    case 'reproducability':
                                        echo __("You don't have access to specify reproducability");
                                        break;
                                    case 'severity':
                                        echo __("You don't have access to specify a severity");
                                        break;
                                    case 'resolution':
                                        echo __("You don't have access to specify a resolution");
                                        break;
                                    case 'estimated_time':
                                        echo __("You don't have access to estimate the issue");
                                        break;
                                    case 'spent_time':
                                        echo __("You don't have access to specify time already spent working on the issue");
                                        break;
                                    case 'percent_complete':
                                        echo __("You don't have access to specify how many percent complete the issue is");
                                        break;
                                }

                            ?>
                        <?php else: ?>
                            <?= __('You don\'t have access to enter "%field_name"', array('%field_name' => \thebuggenie\core\entities\CustomDatatype::getByKey($key)->getDescription())); ?>
                        <?php endif; ?>
                    </li>
                <?php else: ?>
                    <li><?= $p_error; ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <?= __('Please try to fix the error described above, and then click the %file_issue button again', array('%file_issue' => '<b>'.__('File issue').'</b>')); ?>.
    </div>
<?php elseif ($issue instanceof \thebuggenie\core\entities\Issue): ?>
    <div class="rounded_box report_issue_desc <?php if (!$tbg_request->isAjaxCall()): ?>green<?php endif; ?> borderless" style="margin-bottom: 10px;" id="report_issue_reported_issue_details">
        <div style="font-size: 1.1em;">
            <strong><?= __('The following issue was reported: %issue_title', array('%issue_title' => '')); ?></strong>
            <?= link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedIssueNo(true) . ' - ' . $issue->getTitle()); ?><br>
        </div>
        <span class="faded_out"><?= __('Click the link to visit the reported issue'); ?></span>
    </div>
    <a class="button button-silver" id="report_issue_report_another_button" onclick="[$(this), $('report_issue_form'), $('report_more_here'), $('report_form'), $('issuetype_list'), $('report_issue_reported_issue_details')].each(function (el) { Element.toggle(el, 'block'); });$('reportissue_container').removeClassName('medium');$('reportissue_container').addClassName('large');"><?= __('Report another issue'); ?></a>
<?php endif; ?>
<?php if ($tbg_request->isAjaxCall()): ?>
    <form action="<?= make_url('project_reportissue', array('project_key' => $selected_project->getKey())); ?>" method="post" accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" onsubmit="TBG.Main.submitIssue('<?= make_url('project_reportissue', array('project_key' => $selected_project->getKey(), 'return_format' => 'planning')); ?>');return false;" id="report_issue_form" style="<?php if (isset($issue) && $issue instanceof \thebuggenie\core\entities\Issue) echo 'display: none;'; ?>">
<?php else: ?>
    <form action="<?= make_url('project_reportissue', array('project_key' => $selected_project->getKey())); ?>" method="post" accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>">
<?php endif; ?>
    <input type="hidden" name="project_id" id="project_id" value="<?= $selected_project->getID(); ?>">
    <?php if (isset($selected_milestone) || isset($selected_build) || isset($parent_issue)): ?>
        <div class="rounded_box lightyellow borderless">
            <ul class="simple_list" style="padding: 5px; margin: 5px; font-size: 1.2em;">
                <?php if (isset($selected_milestone)): ?>
                    <li>
                        <?= __('You are adding an issue to %milestone_name', array('%milestone_name' => '<b>'.$selected_milestone->getName().'</b>')); ?>
                        <input type="hidden" name="milestone_id" id="reportissue_selected_milestone_id" value="<?= $selected_milestone->getID(); ?>">
                        <input type="hidden" name="milestone_fixed" value="1">
                    </li>
                <?php endif; ?>
                <?php if (isset($parent_issue)): ?>
                    <li>
                        <?= __('Issues you create will be child issues of %related_issue_title', array('%related_issue_title' => '<b>'.$parent_issue->getFormattedTitle().'</b>')); ?>
                        <input type="hidden" name="parent_issue_id" id="reportissue_parent_issue_id" value="<?= $parent_issue->getID(); ?>">
                        <?php if ($issue instanceof \thebuggenie\core\entities\Issue): ?>
                        <script>
                            require(['domReady', 'thebuggenie/tbg'], function (domReady, TBG) {
                                domReady(function () {
                                    TBG.Issues.refreshRelatedIssues('<?= make_url('viewissue_related_issues', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $parent_issue->getID())); ?>');
                                });
                            });
                        </script>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
                <?php if (isset($selected_build)): ?>
                    <li>
                        <?= __('You are adding an issue to release %release_name', array('%release_name' => '<b>'.$selected_build->getName().'</b>')); ?>
                        <input type="hidden" name="build_id" id="reportissue_selected_build_id" value="<?= $selected_build->getID(); ?>">
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if (count($issuetypes) > 0): ?>
        <div class="issuetype_list" id="issuetype_list"<?php if ($selected_issuetype instanceof \thebuggenie\core\entities\Issuetype): ?> style="display: none;"<?php endif; ?>>
            <?php if ($introarticle instanceof \thebuggenie\modules\publish\entities\Article): ?>
                <?php include_component('publish/articledisplay', array('article' => $introarticle, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true)); ?>
            <?php endif; ?>
            <?php foreach ($issuetypes as $issuetype): ?>
                <?php if (!$selected_project->getIssuetypeScheme()->isIssuetypeReportable($issuetype)) continue; ?>
                <?php if (isset($board) && $issuetype->getID() == $board->getEpicIssuetypeID()) continue; ?>
                <a class="button button-silver button-report-issue" data-key="<?= $issuetype->getKey(); ?>" data-id="<?= $issuetype->getID(); ?>" href="javascript:void(0);">
                    <?= fa_image_tag($issuetype->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $issuetype->getType()]); ?>
                    <span><?= __('Choose %issuetype_name', array('%issuetype_name' => '<br>'.$issuetype->getName())); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
        <script type="text/javascript">
            require(['domReady', 'thebuggenie/tbg', 'jquery'], function (domReady, tbgjs, $) {
                domReady(function () {
                    var issueDescriptions = {
                    <?php foreach ($issuetypes as $issuetype): ?>
                        <?php if (!$selected_project->getIssuetypeScheme()->isIssuetypeReportable($issuetype) && !$tbg_request->isAjaxCall()) continue; ?>
                        "<?= $issuetype->getKey(); ?>" : "<?= addslashes(html_entity_decode($issuetype->getDescription(),ENT_QUOTES)); ?>",
                    <?php endforeach; ?>
                    };

                    var cachedHelp = $("#issuetype_description_help").text();

                    $(".issuetype_list a").each(function() {
                        var issueType = $(this);
                        var issueKey = issueType.attr("data-key");

                        issueType
                        .click(function() {
                            $('#issuetype_id').val(issueType.attr("data-id") * 1);
                            $('#reportissue_container').addClass('huge');
                            $('#reportissue_container').removeClass('large');
                            TBG.Issues.updateFields('<?= make_url('getreportissuefields', array('project_key' => $selected_project->getKey())); ?>');
                        })
                        .mouseover(function() {
                            $('#issuetype_description_help').text(issueDescriptions[issueKey]);
                        })
                        .mouseout(function() {
                            $('#issuetype_description_help').text(cachedHelp);
                        });
                    });
                });
            });
        </script>
    <?php endif; ?>
    <div style="clear: both;"></div>
    <?php if (count($issuetypes) > 0): ?>
        <?php if (!isset($description)) : ?>
            <?php $description = ''; ?>
        <?php endif; ?>
        <?php if (!isset($reproduction_steps)) : ?>
            <?php $reproduction_steps = ''; ?>
        <?php endif; ?>
        <div id="report_more_here"<?php if ($selected_issuetype instanceof \thebuggenie\core\entities\Issuetype && $selected_project instanceof \thebuggenie\core\entities\Project): ?> style="display: none;"<?php endif; ?>>
            <span id="issuetype_description_help"><?= __("Hold your mouse over an issuetype to see what it's used for"); ?></span>
        </div>
        <div class="report_form" id="report_form"<?php if (!$selected_project instanceof \thebuggenie\core\entities\Project || !$selected_issuetype instanceof \thebuggenie\core\entities\Issuetype): ?> style="display: none;"<?php endif; ?>>
            <?php if ($reporthelparticle instanceof \thebuggenie\modules\publish\entities\Article): ?>
                <?php include_component('publish/articledisplay', array('article' => $reporthelparticle, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true)); ?>
            <?php endif; ?>
            <table cellpadding="0" cellspacing="0"<?php if (array_key_exists('title', $errors)): ?> class="reportissue_error"<?php endif; ?>>
                <tr>
                    <td style="width: 180px;"><label for="title" class="required"><span>* </span><?= __('Short summary'); ?></label></td>
                    <td style="text-align: left;"><input type="text" name="title" id="title" class="title" value="<?php if (isset($title) && trim($title) != '') echo htmlspecialchars($title); ?>" placeholder="<?= __('Enter a short, but descriptive summary of the issue here'); ?>"></td>
                </tr>
            </table>
            <table cellpadding="0" cellspacing="0"<?php if (array_key_exists('shortname', $errors)): ?> class="reportissue_error"<?php endif; ?> id="shortname_div" style="display: none;">
                <tr>
                    <td style="width: 180px;"><label for="shortname" id="shortname_label"><span>* </span><?= __('Issue label'); ?></label></td>
                    <td style="text-align: left;"><input type="text" name="shortname" id="shortname" class="shortname" value="<?php if (isset($shortname) && trim($shortname) != '') echo htmlspecialchars($shortname); ?>" placeholder="<?= __('Enter a very short label for the issue here'); ?>"></td>
                </tr>
            </table>
            <table cellpadding="0" cellspacing="0" style="margin-top: 10px;">
                <tr>
                    <td style="width: 180px;"><label for="issuetype_id"><?= (isset($locked_issuetype) && $locked_issuetype) ? __('Issue type') : __('Select issue type'); ?></label>
                    <td style="text-align: left;">
                        <?php if (isset($locked_issuetype) && $locked_issuetype): ?>
                            <input type="hidden" name="issuetype_id" id="issuetype_id" value="<?= $selected_issuetype->getID(); ?>">
                        <?php endif; ?>
                        <select name="issuetype_id" id="issuetype_id<?php if (isset($locked_issuetype) && $locked_issuetype) echo '_fake'; ?>" style="min-width: 300px; height: 25px;" onchange="TBG.Issues.updateFields('<?= make_url('getreportissuefields', array('project_key' => $selected_project->getKey())); ?>');" <?php if (isset($locked_issuetype) && $locked_issuetype) echo 'disabled'; ?>>
                            <option value="0"><?= __('Please select an issue type from this list'); ?>...</option>
                            <?php foreach ($issuetypes as $issuetype): ?>
                                <?php if (!$selected_project->getIssuetypeScheme()->isIssuetypeReportable($issuetype) && !$tbg_request->isAjaxCall()) continue; ?>
                                <option value="<?= $issuetype->getID(); ?>"<?php if ($selected_issuetype instanceof \thebuggenie\core\entities\Issuetype && $selected_issuetype->getID() == $issuetype->getID()): ?> selected<?php endif; ?>><?= $issuetype->getName(); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <div id="report_issue_more_options_indicator" style="display: none;">
                <?= image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')); ?>
                <div style="padding-top: 2px;"><?= __('Checking fields, please wait'); ?>...</div>
            </div>
            <table cellpadding="0" cellspacing="0" id="description_div" style="display: none; margin-top: 15px;"<?php if (array_key_exists('description', $errors)): ?> class="reportissue_error"<?php endif; ?>>
                <tr>
                    <td style="width: 180px;"><label for="description" id="description_label"><span>* </span><?= __('Description'); ?></label></td>
                    <td class="report_issue_help faded_out dark"><?= __('Describe the issue in as much detail as possible. More is better.'); ?></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 5px;" class="editor_container">
                        <?php include_component('main/textarea', array('area_name' => 'description', 'target_type' => 'project', 'target_id' => $selected_project->getID(), 'height' => '300px', 'width' => '990px', 'syntax' => $tbg_user->getPreferredIssuesSyntax(true), 'value' => ((isset($selected_description)) ? $selected_description : null))); ?>
                    </td>
                </tr>
            </table>
            <table cellpadding="0" cellspacing="0" id="reproduction_steps_div" style="display: none; margin-top: 15px;"<?php if (array_key_exists('reproduction_steps', $errors)): ?> class="reportissue_error"<?php endif; ?>>
                <tr>
                    <td style="width: 180px;"><label for="reproduction_steps" id="reproduction_steps_label"><span>* </span><?= __('Reproduction steps'); ?></label></td>
                    <td class="report_issue_help faded_out dark"><?= __('Enter the steps necessary to reproduce the issue, as detailed as possible.'); ?></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 5px;" class="editor_container">
                        <?php include_component('textarea', array('area_name' => 'reproduction_steps', 'target_type' => 'project', 'target_id' => $selected_project->getID(), 'height' => '300px', 'width' => '990px', 'syntax' => $tbg_user->getPreferredIssuesSyntax(true), 'value' => ((isset($selected_reproduction_steps)) ? $selected_reproduction_steps : null))); ?>
                    </td>
                </tr>
            </table>
            <?php if ($canupload): ?>
                <?php include_component('main/dynamicuploader', array('mode' => 'issue')); ?>
            <?php endif; ?>
            <div class="reportissue_additional_information_container" style="display: none;">
                <table cellpadding="0" cellspacing="0" id="edition_div" style="display: none;" class="additional_information<?php if (array_key_exists('edition', $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <td style="width: 180px;"><label for="edition_id" id="edition_label"><span>* </span><?= __('Edition'); ?></label></td>
                        <td class="report_issue_help faded_out dark"><?= __("Select which edition of the product you're using"); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-top: 5px;">
                            <select name="edition_id" id="edition_id" style="width: 100%;">
                                <option value=""<?php if (!$selected_edition instanceof \thebuggenie\core\entities\Edition) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                <?php if ($selected_edition instanceof \thebuggenie\core\entities\Edition): ?>
                                    <option value="<?= $selected_edition->getID(); ?>"><?= $selected_edition->getName(); ?></option>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" id="build_div" style="display: none;" class="additional_information<?php if (array_key_exists('build', $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <td style="width: 180px;"><label for="build_id" id="build_label"><span>* </span><?= __('Release'); ?></label></td>
                        <td class="report_issue_help faded_out dark"><?= __("Select which release you're using"); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-top: 5px;">
                            <select name="build_id" id="build_id" style="width: 100%;">
                                <option value=""<?php if (!$selected_build instanceof \thebuggenie\core\entities\Build) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                <?php if ($selected_build instanceof \thebuggenie\core\entities\Build): ?>
                                    <option value="<?= $selected_build->getID(); ?>"><?= $selected_build->getName(); ?> (<?= $selected_build->getVersion(); ?>)</option>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" id="component_div" style="display: none;" class="additional_information<?php if (array_key_exists('component', $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <td style="width: 180px;"><label for="component_id" id="component_label"><span>* </span><?= __('Component'); ?></label></td>
                        <td class="report_issue_help faded_out dark"><?= __("Choose the component affected by this issue"); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-top: 5px;">
                            <select name="component_id" id="component_id" style="width: 100%;">
                                <option value=""<?php if (!$selected_component instanceof \thebuggenie\core\entities\Component) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                <?php if ($selected_component instanceof \thebuggenie\core\entities\Component): ?>
                                    <option value="<?= $selected_component->getID(); ?>"><?= $selected_component->getName(); ?></option>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" id="estimated_time_div" style="display: none;" class="additional_information<?php if (array_key_exists('estimated_time', $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <td style="width: 180px;"><label for="estimated_time_id" id="estimated_time_label"><span>* </span><?= __('Estimate'); ?></label></td>
                        <td style="text-align: left;"><input type="text" name="estimated_time" id="estimated_time_id" style="width: 220px;" value="<?php if ($selected_estimated_time !== null) echo $selected_estimated_time; ?>" placeholder="<?= __('Enter an estimate here'); ?>"></td>
                    </tr>
                    <tr>
                        <td style="padding-top: 5px;" class="report_issue_help faded_out dark" colspan="2"><?= __('Type in your estimate here. Use keywords such as "points", "minutes", "hours", "days", "weeks" and "months" to describe your estimate'); ?></td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" id="spent_time_div" style="display: none;" class="additional_information<?php if (array_key_exists('spent_time', $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <td style="width: 180px;"><label for="spent_time_id" id="spent_time_label"><span>* </span><?= __('Time spent'); ?></label></td>
                        <td style="text-align: left;"><input type="text" name="spent_time" id="spent_time_id" style="width: 220px;" value="<?php if ($selected_spent_time !== null) echo $selected_spent_time; ?>" placeholder="<?= __('Enter time spent here'); ?>"></td>
                    </tr>
                    <tr>
                        <td style="padding-top: 5px;" class="report_issue_help faded_out dark" colspan="2"><?= __('Enter time spent on this issue here. Use keywords such as "points", "minutes", "hours", "days", "weeks" and "months" to describe your effort'); ?></td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" id="percent_complete_div" style="display: none;" class="additional_information<?php if (array_key_exists('percent_complete', $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <td style="width: 180px;"><label for="percent_complete_id" id="percent_complete_label"><span>* </span><?= __('Pct. completed'); ?></label></td>
                        <td style="text-align: left; font-size: 16px;"><input type="text" name="percent_complete" id="percent_complete_id" style="width: 50px;"<?php if ($selected_percent_complete !== null): ?> value="<?= $selected_percent_complete; ?>"<?php endif; ?>> %</td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" id="status_div" style="display: none;" class="additional_information<?php if (array_key_exists('status', $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <td style="width: 180px;"><label for="status_id" id="status_label"><span>* </span><?= __('Status'); ?></label></td>
                        <td class="report_issue_help faded_out dark"><?= __("Choose a status for this issue"); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-top: 5px;">
                            <select name="status_id" id="status_id" style="width: 100%;">
                                <option value=""<?php if (!$selected_status instanceof \thebuggenie\core\entities\Status) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?= $status->getID(); ?>"<?php if ($selected_status instanceof \thebuggenie\core\entities\Status && $selected_status->getID() == $status->getID()): ?> selected<?php endif; ?>><?= $status->getName(); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" id="category_div" style="display: none;" class="additional_information<?php if (array_key_exists('category', $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <td style="width: 180px;"><label for="category_id" id="category_label"><span>* </span><?= __('Category'); ?></label></td>
                        <td class="report_issue_help faded_out dark"><?= __("Choose a category for this issue"); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-top: 5px;">
                            <select name="category_id" id="category_id" style="width: 100%;">
                                <option value=""<?php if (!$selected_category instanceof \thebuggenie\core\entities\Category) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                <?php foreach ($categories as $category): ?>
                                    <?php if (!$category->hasAccess()) continue; ?>
                                    <option value="<?= $category->getID(); ?>"<?php if ($selected_category instanceof \thebuggenie\core\entities\Category && $selected_category->getID() == $category->getID()): ?> selected<?php endif; ?>><?= $category->getName(); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" id="resolution_div" style="display: none;" class="additional_information<?php if (array_key_exists('resolution', $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <td style="width: 180px;"><label for="resolution_id" id="resolution_label"><span>* </span><?= __('Resolution'); ?></label></td>
                        <td class="report_issue_help faded_out dark"><?= __("Choose a resolution for this issue"); ?></td>
                    <tr>
                        <td colspan="2" style="padding-top: 5px;">
                            <select name="resolution_id" id="resolution_id" style="width: 100%;">
                                <option value=""<?php if (!$selected_resolution instanceof \thebuggenie\core\entities\Resolution) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                <?php foreach ($resolutions as $resolution): ?>
                                    <option value="<?= $resolution->getID(); ?>"<?php if ($selected_resolution instanceof \thebuggenie\core\entities\Resolution && $selected_resolution->getID() == $resolution->getID()): ?> selected<?php endif; ?>><?= $resolution->getName(); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" id="reproducability_div" style="display: none;" class="additional_information<?php if (array_key_exists('reproducability', $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <td style="width: 180px;"><label for="reproducability_id" id="reproducability_label"><span>* </span><?= __('Reproducability'); ?></label></td>
                        <td class="report_issue_help faded_out dark"><?= __("Choose a how often you can reproduce this issue"); ?></td>
                    <tr>
                        <td colspan="2" style="padding-top: 5px;">
                            <select name="reproducability_id" id="reproducability_id" style="width: 100%;">
                                <option value=""<?php if (!$selected_reproducability instanceof \thebuggenie\core\entities\Reproducability) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                <?php foreach ($reproducabilities as $reproducability): ?>
                                    <option value="<?= $reproducability->getID(); ?>"<?php if ($selected_reproducability instanceof \thebuggenie\core\entities\Reproducability && $selected_reproducability->getID() == $reproducability->getID()): ?> selected<?php endif; ?>><?= $reproducability->getName(); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" id="priority_div" style="display: none;" class="additional_information<?php if (array_key_exists('priority', $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <td style="width: 180px;"><label for="priority_id" id="priority_label"><span>* </span><?= __('Priority'); ?></label></td>
                        <td class="report_issue_help faded_out dark"><?= __("Choose the priority of this issue"); ?></td>
                    <tr>
                        <td colspan="2" style="padding-top: 5px;">
                            <select name="priority_id" id="priority_id" style="width: 100%;">
                                <option value=""<?php if (!$selected_priority instanceof \thebuggenie\core\entities\Priority) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                <?php foreach ($priorities as $priority): ?>
                                    <option value="<?= $priority->getID(); ?>"<?php if ($selected_priority instanceof \thebuggenie\core\entities\Priority && $selected_priority->getID() == $priority->getID()): ?> selected<?php endif; ?>><?= $priority->getName(); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php if (!isset($selected_milestone)): ?>
                    <table cellpadding="0" cellspacing="0" id="milestone_div" style="display: none;" class="additional_information<?php if (array_key_exists('milestone', $errors)): ?> reportissue_error<?php endif; ?>">
                        <tr>
                            <td style="width: 180px;"><label for="milestone_id" id="milestone_label"><span>* </span><?= __('Milestone'); ?></label></td>
                            <td class="report_issue_help faded_out dark"><?= __("Choose the milestone of this issue"); ?></td>
                        <tr>
                            <td colspan="2" style="padding-top: 5px;">
                                <select name="milestone_id" id="milestone_id" style="width: 100%;">
                                    <option value=""<?php if (!$selected_milestone instanceof \thebuggenie\core\entities\Milestone) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                    <?php foreach ($milestones as $milestone): ?>
                                        <?php if ($milestone->isClosed()) continue; ?>
                                        <option value="<?= $milestone->getID(); ?>"<?php if ($selected_milestone instanceof \thebuggenie\core\entities\Milestone && $selected_milestone->getID() == $milestone->getID()): ?> selected<?php endif; ?>><?= $milestone->getName(); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                <?php endif; ?>
                <table cellpadding="0" cellspacing="0" id="pain_bug_type_div" style="display: none;" class="additional_information<?php if (array_key_exists('pain_bug_type', $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <td style="width: 180px;"><label for="pain_bug_type_id" id="pain_bug_type_label"><span>* </span><?= __('Triaging: Bug type'); ?></label></td>
                        <td class="report_issue_help faded_out dark"><?= __("What type of bug is this?"); ?></td>
                    <tr>
                        <td colspan="2" style="padding-top: 5px;">
                            <select name="pain_bug_type_id" id="pain_bug_type_id" style="width: 100%;">
                                <option value=""<?php if (!$selected_pain_bug_type) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                <?php foreach (\thebuggenie\core\entities\Issue::getPainTypesOrLabel('pain_bug_type') as $choice_id => $choice): ?>
                                    <option value="<?= $choice_id; ?>"<?php if ($selected_pain_bug_type == $choice_id): ?> selected<?php endif; ?>><?= $choice; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" id="pain_likelihood_div" style="display: none;" class="additional_information<?php if (array_key_exists('pain_likelihood', $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <td style="width: 180px;"><label for="pain_likelihood_id" id="pain_likelihood_label"><span>* </span><?= __('Triaging: Likelihood'); ?></label></td>
                        <td class="report_issue_help faded_out dark"><?= __("How likely are users to experience the bug?"); ?></td>
                    <tr>
                        <td colspan="2" style="padding-top: 5px;">
                            <select name="pain_likelihood_id" id="pain_likelihood_id" style="width: 100%;">
                                <option value=""<?php if (!$selected_pain_likelihood) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                <?php foreach (\thebuggenie\core\entities\Issue::getPainTypesOrLabel('pain_likelihood') as $choice_id => $choice): ?>
                                    <option value="<?= $choice_id; ?>"<?php if ($selected_pain_likelihood == $choice_id): ?> selected<?php endif; ?>><?= $choice; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" id="pain_effect_div" style="display: none;" class="additional_information<?php if (array_key_exists('pain_effect', $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <td style="width: 180px;"><label for="pain_effect_id" id="pain_effect_label"><span>* </span><?= __('Triaging: Effect'); ?></label></td>
                        <td class="report_issue_help faded_out dark"><?= __("Of the people who experience the bug, how badly does it affect their experience?"); ?></td>
                    <tr>
                        <td colspan="2" style="padding-top: 5px;">
                            <select name="pain_effect_id" id="pain_effect_id" style="width: 100%;">
                                <option value=""<?php if (!$selected_pain_effect) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                <?php foreach (\thebuggenie\core\entities\Issue::getPainTypesOrLabel('pain_effect') as $choice_id => $choice): ?>
                                    <option value="<?= $choice_id; ?>"<?php if ($selected_pain_effect == $choice_id): ?> selected<?php endif; ?>><?= $choice; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" id="severity_div" style="display: none;" class="additional_information<?php if (array_key_exists('severity', $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <td style="width: 180px;"><label for="severity_id" id="severity_label"><span>* </span><?= __('Severity'); ?></label></td>
                        <td class="report_issue_help faded_out dark"><?= __("Choose a severity for this issue"); ?></td>
                    <tr>
                        <td colspan="2" style="padding-top: 5px;">
                            <select name="severity_id" id="severity_id" style="width: 100%;">
                                <option value=""<?php if (!$selected_severity instanceof \thebuggenie\core\entities\Severity) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                <?php foreach ($severities as $severity): ?>
                                    <option value="<?= $severity->getID(); ?>"<?php if ($selected_severity instanceof \thebuggenie\core\entities\Severity && $selected_severity->getID() == $severity->getID()): ?> selected<?php endif; ?>><?= $severity->getName(); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php foreach (\thebuggenie\core\entities\CustomDatatype::getAll() as $field => $customdatatype): ?>
                    <table cellpadding="0" cellspacing="0" id="<?= $customdatatype->getKey(); ?>_div" style="display: none;" class="additional_information<?php if (array_key_exists($customdatatype->getKey(), $errors)): ?> reportissue_error<?php endif; ?>">
                        <tr>
                            <?php if ($customdatatype->getType() == \thebuggenie\core\entities\CustomDatatype::DATE_PICKER || $customdatatype->getType() == \thebuggenie\core\entities\CustomDatatype::DATETIME_PICKER): ?>
                                <td style="width: 180px;"><label for="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_label"><span>* </span><?= __($customdatatype->getDescription()); ?></label></td>
                                <td style="width: 326px;position: relative;" class="report_issue_help faded_out dark">
                                    <a href="javascript:void(0);" class="dropper dropdown_link"><?= image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                                    <ul class="popup_box more_actions_dropdown" id="<?= $customdatatype->getKey(); ?>_change">
                                        <li class="header"><?= __($customdatatype->getDescription()); ?></li>
                                        <li>
                                            <a href="javascript:void(0);" onclick="$('<?= $customdatatype->getKey(); ?>_name').hide();$('<?= $customdatatype->getKey(); ?>_value').value = '';$('no_<?= $customdatatype->getKey(); ?>').show();"><?= __('Clear this field'); ?></a>
                                        </li>
                                        <li class="separator"></li>
                                        <li id="customfield_<?= $customdatatype->getKey(); ?>_calendar_container" style="padding: 0;"></li>
                                        <?php if ($customdatatype->getType() == \thebuggenie\core\entities\CustomDatatype::DATETIME_PICKER): ?>
                                            <li class="nohover">
                                                <label><?= __('Time'); ?></label>
                                                <input type="text" id="customfield_<?= $customdatatype->getKey(); ?>_hour" value="00" style="width: 20px; font-size: 0.9em; text-align: center;">&nbsp;:&nbsp;
                                                <input type="text" id="customfield_<?= $customdatatype->getKey(); ?>_minute" value="00" style="width: 20px; font-size: 0.9em; text-align: center;">
                                            </li>
                                        <?php endif; ?>
                                        <script type="text/javascript">
                                            require(['domReady', 'thebuggenie/tbg', 'calendarview'], function (domReady, tbgjs, Calendar) {
                                                domReady(function () {
                                                    Calendar.setup({
                                                        dateField: '<?= $customdatatype->getKey(); ?>_name',
                                                        parentElement: 'customfield_<?= $customdatatype->getKey(); ?>_calendar_container',
                                                        valueCallback: function(element, date) {
                                                            <?php if ($customdatatype->getType() == \thebuggenie\core\entities\CustomDatatype::DATETIME_PICKER) { ?>
                                                                var value = date.setHours(parseInt($('customfield_<?= $customdatatype->getKey(); ?>_hour').value));
                                                                var date  = new Date(value);
                                                                var value = Math.floor(date.setMinutes(parseInt($('customfield_<?= $customdatatype->getKey(); ?>_minute').value)) / 1000);
                                                                $('<?= $customdatatype->getKey(); ?>_name').dataset.dateStr = $('<?= $customdatatype->getKey(); ?>_name').innerText;
                                                                $('<?= $customdatatype->getKey(); ?>_name').update(
                                                                    $('<?= $customdatatype->getKey(); ?>_name').dataset.dateStr + ' '
                                                                    + parseInt($('customfield_<?= $customdatatype->getKey(); ?>_hour').value) + ':'
                                                                    + parseInt($('customfield_<?= $customdatatype->getKey(); ?>_minute').value)
                                                                );
                                                            <?php } else { ?>
                                                                var value = Math.floor(date.getTime() / 1000);
                                                            <?php } ?>
                                                            $('<?= $customdatatype->getKey(); ?>_name').show();
                                                            $('<?= $customdatatype->getKey(); ?>_value').value = value;
                                                            $('no_<?= $customdatatype->getKey(); ?>').hide();
                                                        }
                                                    });
                                                    <?php if ($customdatatype->getType() == \thebuggenie\core\entities\CustomDatatype::DATETIME_PICKER): ?>
                                                        Event.observe($('customfield_<?= $customdatatype->getKey(); ?>_hour'), 'change', function (event) {
                                                            var value = parseInt($('<?= $customdatatype->getKey(); ?>_value').value);
                                                            var hours = parseInt(this.value);
                                                            if (value <= 0 || hours < 0 || hours > 24) return;
                                                            var date = new Date(value * 1000);
                                                            $('<?= $customdatatype->getKey(); ?>_value').value = date.setHours(parseInt(this.value)) / 1000;
                                                            $('<?= $customdatatype->getKey(); ?>_name').update(
                                                                $('<?= $customdatatype->getKey(); ?>_name').dataset.dateStr + ' '
                                                                + parseInt($('customfield_<?= $customdatatype->getKey(); ?>_hour').value) + ':'
                                                                + parseInt($('customfield_<?= $customdatatype->getKey(); ?>_minute').value)
                                                            );
                                                        });
                                                        Event.observe($('customfield_<?= $customdatatype->getKey(); ?>_minute'), 'change', function (event) {
                                                            var value = parseInt($('<?= $customdatatype->getKey(); ?>_value').value);
                                                            var minutes = parseInt(this.value);
                                                            if (value <= 0 || minutes < 0 || minutes > 60) return;
                                                            var date = new Date(value * 1000);
                                                            $('<?= $customdatatype->getKey(); ?>_value').value = date.setMinutes(parseInt(this.value)) / 1000;
                                                            $('<?= $customdatatype->getKey(); ?>_name').update(
                                                                $('<?= $customdatatype->getKey(); ?>_name').dataset.dateStr + ' '
                                                                + parseInt($('customfield_<?= $customdatatype->getKey(); ?>_hour').value) + ':'
                                                                + parseInt($('customfield_<?= $customdatatype->getKey(); ?>_minute').value)
                                                            );
                                                        });
                                                    <?php endif; ?>
                                                });
                                            });
                                        </script>
                                    </ul>
                                    <span id="<?= $customdatatype->getKey(); ?>_name" style="display: none;"><?= __('Not set'); ?></span><span class="faded_out" id="no_<?= $customdatatype->getKey(); ?>"><?= __('Not set'); ?></span>
                                    <input type="hidden" name="<?= $customdatatype->getKey(); ?>_value" id="<?= $customdatatype->getKey(); ?>_value" />
                                </td>
                            <?php else: ?>
                                <td style="width: 180px;"><label for="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_label"><span>* </span><?= __($customdatatype->getDescription()); ?></label></td>
                                <td class="report_issue_help faded_out dark"><?= __($customdatatype->getInstructions()); ?></td>
                            <?php endif; ?>
                        <tr>
                            <td colspan="2" style="padding-top: 5px;" class="editor_container">
                                <?php
                                    switch ($customdatatype->getType())
                                    {
                                        case \thebuggenie\core\entities\CustomDatatype::DROPDOWN_CHOICE_TEXT: ?>
                                            <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                                <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof \thebuggenie\core\entities\CustomDatatypeOption) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                                <?php foreach ($customdatatype->getOptions() as $option): ?>
                                                <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof \thebuggenie\core\entities\CustomDatatypeOption && $selected_customdatatype[$customdatatype->getKey()]->getID() == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::EDITIONS_CHOICE: ?>
                                            <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                                <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof \thebuggenie\core\entities\Edition) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                                <?php if ($selected_project instanceof \thebuggenie\core\entities\Project): ?>
                                                    <?php foreach ($selected_project->getEditions() as $option): ?>
                                                    <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::STATUS_CHOICE: ?>
                                            <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                                <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof \thebuggenie\core\entities\Status) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                                <?php foreach (\thebuggenie\core\entities\Status::getAll() as $option): ?>
                                                    <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::TEAM_CHOICE: ?>
                                            <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                                <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof \thebuggenie\core\entities\Team) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                                <?php foreach (\thebuggenie\core\entities\Team::getAll() as $option): ?>
                                                    <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::CLIENT_CHOICE: ?>
                                            <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                                <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof \thebuggenie\core\entities\Client) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                                <?php foreach (\thebuggenie\core\entities\Client::getAll() as $option): ?>
                                                    <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::COMPONENTS_CHOICE: ?>
                                            <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                                <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof \thebuggenie\core\entities\Component) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                                <?php if ($selected_project instanceof \thebuggenie\core\entities\Project): ?>
                                                    <?php foreach ($selected_project->getComponents() as $option): ?>
                                                        <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::RELEASES_CHOICE: ?>
                                            <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                                <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof \thebuggenie\core\entities\Build) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                                <?php if ($selected_project instanceof \thebuggenie\core\entities\Project): ?>
                                                    <?php foreach ($selected_project->getBuilds() as $option): ?>
                                                        <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::RADIO_CHOICE: ?>
                                            <input type="radio" name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_0" value="" <?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof \thebuggenie\core\entities\CustomDatatypeOption): ?> selected<?php endif; ?> /> <label for="<?= $customdatatype->getKey(); ?>_0"><?= __('Not specified'); ?></label><br>
                                            <?php foreach ($customdatatype->getOptions() as $option): ?>
                                                <input type="radio" name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_<?= $option->getID(); ?>" value="<?= $option->getID(); ?>" <?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof \thebuggenie\core\entities\CustomDatatypeOption && $selected_customdatatype[$customdatatype->getKey()]->getID() == $option->getID()): ?> selected<?php endif; ?> /> <label for="<?= $customdatatype->getKey(); ?>_<?= $option->getID(); ?>"><?= $option->getName(); ?></label><br>
                                            <?php endforeach; ?>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXT:
                                            ?>
                                            <input type="text" name="<?= $customdatatype->getKey(); ?>_value" value="<?= $selected_customdatatype[$customdatatype->getKey()]; ?>" id="<?= $customdatatype->getKey(); ?>_value" /><br>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_SMALL:
                                        case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_MAIN:
                                            ?>
                                            <?php include_component('main/textarea', array('area_name' => $customdatatype->getKey().'_value', 'target_type' => 'project', 'target_id' => $selected_project->getID(), 'area_id' => $customdatatype->getKey().'_value', 'height' => '75px', 'width' => '100%', 'hide_hint' => true, 'syntax' => $tbg_user->getPreferredIssuesSyntax(true), 'value' => $selected_customdatatype[$customdatatype->getKey()])); ?>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::DATE_PICKER:
                                        case \thebuggenie\core\entities\CustomDatatype::DATETIME_PICKER:
                                            ?>

                                            <?php
                                            break;
                                    }
                                ?>
                            </td>
                        </tr>
                    </table>
                <?php endforeach; ?>
                <br style="clear: both;">
            </div>
            <?php if ($selected_issuetype != null && $selected_project != null): ?>
                <script type="text/javascript">
                    require(['domReady', 'thebuggenie/tbg'], function (domReady, TBG) {
                        domReady(function () {
                            TBG.Issues.updateFields('<?= make_url('getreportissuefields', array('project_key' => $selected_project->getKey())); ?>');
                        });
                    });
                </script>
            <?php endif; ?>
            <?php \thebuggenie\core\framework\Event::createNew('core', 'reportissue.prefile')->trigger(); ?>
            <?php if ($selected_project instanceof \thebuggenie\core\entities\Project && $selected_project->permissionCheck('canlockandeditlockedissues')): ?>
                <div class="report-issue-custom-access-check">
                    <?= fa_image_tag('user-lock'); ?>
                    <input type="checkbox" name="custom_issue_access" id="report-issue-custom-access-checkbox" class="fancycheckbox" onchange="TBG.Issues.ACL.toggle_custom_access(this);" value="1"><label for="report-issue-custom-access-checkbox"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Custom access policy'); ?></label>
                    <div class="report-issue-custom-access-container" style="display:none;">
                        <input type="radio" name="issue_access" id="issue_access_public" onchange="TBG.Issues.ACL.toggle_checkboxes(this, '', 'public');" value="public"<?php if ($selected_project->getIssuesLockType() === \thebuggenie\core\entities\Project::ISSUES_LOCK_TYPE_PUBLIC) echo ' checked'; ?>><label for="issue_access_public"><?= __('Available to anyone with access to project'); ?></label><br>
                        <input type="radio" name="issue_access" id="issue_access_public_category" onchange="TBG.Issues.ACL.toggle_checkboxes(this, '', 'public_category');" value="public_category"<?php if ($selected_project->getIssuesLockType() === \thebuggenie\core\entities\Project::ISSUES_LOCK_TYPE_PUBLIC_CATEGORY) echo ' checked'; ?>><label for="issue_access_public_category"><?= __('Available to anyone with access to project, category and those listed below'); ?></label><br>
                        <input type="radio" name="issue_access" id="issue_access_restricted" onchange="TBG.Issues.ACL.toggle_checkboxes(this, '', 'restricted');" value="restricted"<?php if ($selected_project->getIssuesLockType() === \thebuggenie\core\entities\Project::ISSUES_LOCK_TYPE_RESTRICTED) echo ' checked'; ?>><label for="issue_access_restricted"><?= __('Available only to you and those listed below'); ?></label><br>
                        <script>
                            require(['domReady', 'jquery'], function (domReady, jQuery) {
                                domReady(function () { jQuery('input[name=issue_access]').trigger('change'); });
                            });
                        </script>
                        <?php image_tag('spinning_16.gif', array('id' => 'acl_indicator_', 'style' => '')); ?>
                        <div id="acl-users-teams-selector" style="display: none;">
                            <h4 style="margin-top: 10px;">
                                <?= javascript_link_tag(__('Add a user or team'), array('onclick' => "var tempPopup = $('popup_find_acl_');  if (tempPopup.style.opacity == 0) { tempPopup.toggle('block'); tempPopup.style.opacity = 1.0; tempPopup.style.transform = 'none'; tempPopup.style.display = 'block';} else { tempPopup.toggle('block'); }", 'style' => 'float: right;', 'class' => 'button button-silver')); ?>
                                <?= __('Users or teams who can see this issue'); ?>
                            </h4>
                            <?php include_component('main/identifiableselector', array(    'html_id'             => "popup_find_acl_",
                                                                                      'header'             => __('Give someone access to this issue'),
                                                                                      'callback'             => "TBG.Issues.ACL.addTarget('" . make_url('getacl_formentry', array('identifiable_type' => 'user', 'identifiable_value' => '%identifiable_value')) . "', '');",
                                                                                      'team_callback'     => "TBG.Issues.ACL.addTarget('" . make_url('getacl_formentry', array('identifiable_type' => 'team', 'identifiable_value' => '%identifiable_value')) . "', '');",
                                                                                      'base_id'            => "popup_find_acl_",
                                                                                      'include_teams'        => true,
                                                                                      'allow_clear'        => false,
                                                                                      'absolute'            => true,
                                                                                      'use_form'            => false)); ?>
                        </div>
                        <div id="acl__public" style="display: none;">
                            <ul class="issue_access_list simple_list" id="issue__public_category_access_list" style="display: none;">
                                <li id="issue__public_category_access_list_none" class="faded_out" style="<?php if (count($al_items)): ?>display: none; <?php endif; ?>padding: 5px;"><?= __('Noone else can see this issue'); ?></li>
                                <?php foreach ($al_items as $item): ?>
                                    <?php include_component('main/issueaclformentry', array('target' => $item['target'])); ?>
                                <?php endforeach; ?>
                            </ul>
                            <div style="text-align: right;">
                                <input id="issue_access_public_category_input" type="hidden" name="public_category" disabled>
                            </div>
                        </div>
                        <div id="acl__restricted" style="display: none;">
                            <ul class="issue_access_list simple_list" id="issue__restricted_access_list">
                                <li id="issue__restricted_access_list_none" class="faded_out" style="<?php if (count($al_items)): ?>display: none; <?php endif; ?>padding: 5px;"><?= __('Noone else can see this issue'); ?></li>
                                <?php foreach ($al_items as $item): ?>
                                    <?php include_component('main/issueaclformentry', array('target' => $item['target'])); ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="rounded_box report_issue_submit_container report_issue_desc green borderless">
                <div><?= __('When you are satisfied, click the %file_issue button to file your issue', array('%file_issue' => '<strong>'.__('File issue').'</strong>')); ?></div>
                <input type="submit" class="button button-silver" value="<?= __('File issue'); ?>" id="report_issue_submit_button">
                <?= image_tag('spinning_20_bg_green.gif', array('id' => 'report_issue_indicator', 'style' => 'display: none;')); ?>
            </div>
            <div class="rounded_box report_issue_desc borderless lightgrey" id="report_issue_add_extra" style="vertical-align: middle; padding: 5px;">
                <strong><?= __('Add more information to your issue'); ?></strong><br>
                <p><?= __('Specify additional information by clicking the links below before submitting your issue'); ?></p>
                <p id="reportissue_extrafields_none"><?= __('No additional actions available'); ?></p>
                <ul id="reportissue_extrafields">
                    <li id="status_additional" style="display: none;">
                        <?= image_tag('icon_status.png'); ?>
                        <div id="status_link"<?php if ($selected_status instanceof \thebuggenie\core\entities\Status): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('status_link').hide();$('status_additional_div').show();"><?= __('Set initial status'); ?></a></div>
                        <div id="status_additional_div"<?php if ($selected_status === null): ?> style="display: none;"<?php endif; ?>>
                            <select name="status_id" id="status_id_additional">
                                <option value=""><?= __('Not specified'); ?></option>
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?= $status->getID(); ?>"<?php if ($selected_status instanceof \thebuggenie\core\entities\Datatype && $selected_status->getID() == $status->getID()): ?> selected<?php endif; ?>><?= $status->getName(); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <a href="javascript:void(0);" class="img" onclick="$('status_link').show();$('status_additional_div').hide();$('status_id_additional').setValue(0);"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                        </div>
                    </li>
                    <li id="edition_additional" style="display: none;">
                        <?= image_tag('icon_edition.png'); ?>
                        <div id="edition_link"<?php if ($selected_edition instanceof \thebuggenie\core\entities\Edition): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('edition_link').hide();$('edition_additional_div').show();"><?= __('Specify edition'); ?></a></div>
                        <div id="edition_additional_div"<?php if ($selected_edition === null): ?> style="display: none;"<?php endif; ?>>
                            <select name="edition_id" id="edition_id_additional">
                                <option value=""<?php if (!$selected_edition instanceof \thebuggenie\core\entities\Edition) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                <?php if ($selected_edition instanceof \thebuggenie\core\entities\Edition): ?>
                                    <option value="<?= $selected_edition->getID(); ?>"><?= $selected_edition->getName(); ?> (<?= $selected_edition->getVersion(); ?>)</option>
                                <?php endif; ?>
                            </select>
                            <a href="javascript:void(0);" class="img" onclick="$('edition_link').show();$('edition_additional_div').hide();$('edition_id_additional').setValue(0);"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                        </div>
                    </li>
                    <li id="build_additional" style="display: none;">
                        <?= image_tag('icon_build.png'); ?>
                        <div id="build_link"<?php if ($selected_build instanceof \thebuggenie\core\entities\Build): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('build_link').hide();$('build_additional_div').show();"><?= __('Specify release'); ?></a></div>
                        <div id="build_additional_div"<?php if ($selected_build === null): ?> style="display: none;"<?php endif; ?>>
                            <select name="build_id" id="build_id_additional">
                                <option value=""<?php if (!$selected_build instanceof \thebuggenie\core\entities\Build) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                <?php if ($selected_build instanceof \thebuggenie\core\entities\Build): ?>
                                    <option value="<?= $selected_build->getID(); ?>"><?= $selected_build->getName(); ?> (<?= $selected_build->getVersion(); ?>)</option>
                                <?php endif; ?>
                            </select>
                            <a href="javascript:void(0);" class="img" onclick="$('build_link').show();$('build_additional_div').hide();$('build_id_additional').setValue(0);"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                        </div>
                    </li>
                    <li id="component_additional" style="display: none;">
                        <?= image_tag('icon_component.png'); ?>
                        <div id="component_link"<?php if ($selected_component instanceof \thebuggenie\core\entities\Component): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('component_link').hide();$('component_additional_div').show();"><?= __('Specify component'); ?></a></div>
                        <div id="component_additional_div"<?php if ($selected_component === null): ?> style="display: none;"<?php endif; ?>>
                            <select name="component_id" id="component_id_additional">
                                <option value=""<?php if (!$selected_component instanceof \thebuggenie\core\entities\Component) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                <?php if ($selected_component instanceof \thebuggenie\core\entities\Component): ?>
                                    <option value="<?= $selected_component->getID(); ?>"><?= $selected_component->getName(); ?> (<?= $selected_component->getVersion(); ?>)</option>
                                <?php endif; ?>
                            </select>
                            <a href="javascript:void(0);" class="img" onclick="$('component_link').show();$('component_additional_div').hide();$('component_id_additional').setValue(0);"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                        </div>
                    </li>
                    <?php if (!isset($selected_milestone)): ?>
                        <li id="milestone_additional" style="display: none;">
                            <?= image_tag('icon_milestone.png'); ?>
                            <div id="milestone_link"<?php if ($selected_milestone instanceof \thebuggenie\core\entities\Milestone): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('milestone_link').hide();$('milestone_additional_div').show();"><?= __('Specify milestone'); ?></a></div>
                            <div id="milestone_additional_div"<?php if ($selected_milestone === null): ?> style="display: none;"<?php endif; ?>>
                                <select name="milestone_id" id="milestone_id_additional">
                                    <option value=""<?php if (!$selected_milestone instanceof \thebuggenie\core\entities\Milestone) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                    <?php if ($selected_milestone instanceof \thebuggenie\core\entities\Milestone): ?>
                                        <option value="<?= $selected_milestone->getID(); ?>"><?= $selected_milestone->getName(); ?></option>
                                    <?php endif; ?>
                                </select>
                                <a href="javascript:void(0);" class="img" onclick="$('milestone_link').show();$('milestone_additional_div').hide();$('milestone_id_additional').setValue(0);"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                            </div>
                        </li>
                    <?php endif; ?>
                    <li id="category_additional" style="display: none;">
                        <?= image_tag('icon_category.png'); ?>
                        <div id="category_link"<?php if ($selected_category instanceof \thebuggenie\core\entities\Category): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('category_link').hide();$('category_additional_div').show();"><?= __('Specify category'); ?></a></div>
                        <div id="category_additional_div"<?php if ($selected_category === null): ?> style="display: none;"<?php endif; ?>>
                            <select name="category_id" id="category_id_additional">
                                <option value=""><?= __('Not specified'); ?></option>
                                <?php foreach ($categories as $category): ?>
                                    <?php if (!$category->hasAccess()) continue; ?>
                                    <option value="<?= $category->getID(); ?>"<?php if ($selected_category instanceof \thebuggenie\core\entities\Datatype && $selected_category->getID() == $category->getID()): ?> selected<?php endif; ?>><?= $category->getName(); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <a href="javascript:void(0);" class="img" onclick="$('category_link').show();$('category_additional_div').hide();$('category_id_additional').setValue(0);"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                        </div>
                    </li>
                    <li id="estimated_time_additional" style="display: none;">
                        <?= image_tag('icon_time.png'); ?>
                        <div id="estimated_time_link"<?php if ($selected_estimated_time != ''): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('estimated_time_link').hide();$('estimated_time_additional_div').show();"><?= __('Estimate time'); ?></a></div>
                        <div id="estimated_time_additional_div"<?php if ($selected_estimated_time === null): ?> style="display: none;"<?php endif; ?>>
                            <input name="estimated_time" id="estimated_time_id_additional" style="width: 100px;">
                            <a href="javascript:void(0);" class="img" onclick="$('estimated_time_link').show();$('estimated_time_additional_div').hide();$('estimated_time_id_additional').setValue('');"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                        </div>
                    </li>
                    <li id="spent_time_additional" style="display: none;">
                        <?= image_tag('icon_time.png'); ?>
                        <div id="spent_time_link"<?php if ($selected_spent_time != ''): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('spent_time_link').hide();$('spent_time_additional_div').show();"><?= __('Time spent on fix'); ?></a></div>
                        <div id="spent_time_additional_div"<?php if ($selected_spent_time === null): ?> style="display: none;"<?php endif; ?>>
                            <input name="spent_time" id="spent_time_id_additional" style="width: 100px;">
                            <a href="javascript:void(0);" class="img" onclick="$('spent_time_link').show();$('spent_time_additional_div').hide();$('spent_time_id_additional').setValue('');"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                        </div>
                    </li>
                    <li id="percent_complete_additional" style="display: none;">
                        <?= image_tag('icon_percent.png'); ?>
                        <div id="percent_complete_link"<?php if ($selected_percent_complete != ''): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('percent_complete_link').hide();$('percent_complete_additional_div').show();"><?= __('Set percent completed'); ?></a></div>
                        <div id="percent_complete_additional_div"<?php if ($selected_percent_complete === null): ?> style="display: none;"<?php endif; ?>>
                            <input name="percent_complete" id="percent_complete_id_additional" style="width: 30px;"<?php if ($selected_percent_complete !== null): ?> value="<?= $selected_percent_complete; ?>"<?php endif; ?>>
                            <a href="javascript:void(0);" class="img" onclick="$('percent_complete_link').show();$('percent_complete_additional_div').hide();$('percent_complete_id_additional').setValue('');"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                        </div>
                    </li>
                    <li id="priority_additional" style="display: none;">
                        <?= image_tag('icon_priority.png'); ?>
                        <div id="priority_link"<?php if ($selected_priority instanceof \thebuggenie\core\entities\Priority): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('priority_link').hide();$('priority_additional_div').show();"><?= __('Set priority'); ?></a></div>
                        <div id="priority_additional_div"<?php if ($selected_priority === null): ?> style="display: none;"<?php endif; ?>>
                            <select name="priority_id" id="priority_id_additional">
                                <option value=""><?= __('Not specified'); ?></option>
                                <?php foreach ($priorities as $priority): ?>
                                    <option value="<?= $priority->getID(); ?>"><?= $priority->getName(); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <a href="javascript:void(0);" class="img" onclick="$('priority_link').show();$('priority_additional_div').hide();$('priority').setValue(0);"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                        </div>
                    </li>
                    <li id="reproducability_additional" style="display: none;">
                        <?= image_tag('icon_reproducability.png'); ?>
                        <div id="reproducability_link"<?php if ($selected_reproducability instanceof \thebuggenie\core\entities\Reproducability): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('reproducability_link').hide();$('reproducability_additional_div').show();"><?= __('Set reproducability'); ?></a></div>
                        <div id="reproducability_additional_div"<?php if ($selected_reproducability === null): ?> style="display: none;"<?php endif; ?>>
                            <select name="reproducability_id" id="reproducability_id_additional">
                                <option value=""><?= __('Not specified'); ?></option>
                                <?php foreach ($reproducabilities as $reproducability): ?>
                                    <option value="<?= $reproducability->getID(); ?>"<?php if ($selected_reproducability instanceof \thebuggenie\core\entities\Datatype && $selected_reproducability->getID() == $reproducability->getID()): ?> selected<?php endif; ?>><?= $reproducability->getName(); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <a href="javascript:void(0);" class="img" onclick="$('reproducability_link').show();$('reproducability_additional_div').hide();$('reproducability_id_additional').setValue(0);"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                        </div>
                    </li>
                    <li id="resolution_additional" style="display: none;">
                        <?= image_tag('icon_resolution.png'); ?>
                        <div id="resolution_link"<?php if ($selected_resolution instanceof \thebuggenie\core\entities\Resolution): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('resolution_link').hide();$('resolution_additional_div').show();"><?= __('Set resolution'); ?></a></div>
                        <div id="resolution_additional_div"<?php if ($selected_resolution === null): ?> style="display: none;"<?php endif; ?>>
                            <select name="resolution_id" id="resolution_id_additional">
                                <option value=""><?= __('Not specified'); ?></option>
                                <?php foreach ($resolutions as $resolution): ?>
                                    <option value="<?= $resolution->getID(); ?>"><?= $resolution->getName(); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <a href="javascript:void(0);" class="img" onclick="$('resolution_link').show();$('resolution_additional_div').hide();$('resolution_id_additional').setValue(0);"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                        </div>
                    </li>
                    <li id="severity_additional" style="display: none;">
                        <?= image_tag('icon_severity.png'); ?>
                        <div id="severity_link"<?php if ($selected_severity instanceof \thebuggenie\core\entities\Severity): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('severity_link').hide();$('severity_additional_div').show();"><?= __('Set severity'); ?></a></div>
                        <div id="severity_additional_div"<?php if ($selected_severity === null): ?> style="display: none;"<?php endif; ?>>
                            <select name="severity_id" id="severity_id_additional">
                                <option value=""><?= __('Not specified'); ?></option>
                                <?php foreach ($severities as $severity): ?>
                                    <option value="<?= $severity->getID(); ?>"><?= $severity->getName(); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <a href="javascript:void(0);" class="img" onclick="$('severity_link').show();$('severity_additional_div').hide();$('severity_id_additional').setValue(0);"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                        </div>
                    </li>
                    <?php foreach (\thebuggenie\core\entities\CustomDatatype::getAll() as $customdatatype): ?>
                        <li id="<?= $customdatatype->getKey(); ?>_additional" style="display: none;">
                            <?= image_tag('icon_customdatatype.png'); ?>
                            <div id="<?= $customdatatype->getKey(); ?>_link"<?php if ($selected_customdatatype[$customdatatype->getKey()] !== null): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('<?= $customdatatype->getKey(); ?>_link').hide();$('<?= $customdatatype->getKey(); ?>_additional_div').show();"><?= __($customdatatype->getDescription()); ?></a></div>
                            <div id="<?= $customdatatype->getKey(); ?>_additional_div"<?php if ($selected_customdatatype[$customdatatype->getKey()] === null): ?> style="display: none;"<?php endif; ?> class="editor_container">
                                <?php
                                    switch ($customdatatype->getType())
                                    {
                                        case \thebuggenie\core\entities\CustomDatatype::DROPDOWN_CHOICE_TEXT:
                                            ?>
                                            <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional">
                                                <?php foreach ($customdatatype->getOptions() as $option): ?>
                                                <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof \thebuggenie\core\entities\CustomDatatypeOption && $selected_customdatatype[$customdatatype->getKey()]->getID() == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::EDITIONS_CHOICE:
                                            ?>
                                            <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional">
                                                <?php if ($selected_project instanceof \thebuggenie\core\entities\Project): ?>
                                                    <?php foreach ($selected_project->getEditions() as $option): ?>
                                                    <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::STATUS_CHOICE:
                                            ?>
                                            <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional">
                                                <?php foreach (\thebuggenie\core\entities\Status::getAll() as $option): ?>
                                                <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::TEAM_CHOICE:
                                            ?>
                                            <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional">
                                                <?php foreach (\thebuggenie\core\entities\Team::getAll() as $option): ?>
                                                <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::CLIENT_CHOICE:
                                            ?>
                                            <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional">
                                                <?php foreach (\thebuggenie\core\entities\Client::getAll() as $option): ?>
                                                <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::COMPONENTS_CHOICE:
                                            ?>
                                            <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional">
                                                <?php if ($selected_project instanceof \thebuggenie\core\entities\Project): ?>
                                                    <?php foreach ($selected_project->getComponents() as $option): ?>
                                                    <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::RELEASES_CHOICE:
                                            ?>
                                            <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional">
                                                <?php if ($selected_project instanceof \thebuggenie\core\entities\Project): ?>
                                                    <?php foreach ($selected_project->getBuilds() as $option): ?>
                                                    <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::RADIO_CHOICE:
                                            ?>
                                            <label for="<?= $customdatatype->getKey(); ?>_id_additional"><?= $customdatatype->getDescription(); ?></label>
                                            <br>
                                            <?php foreach ($customdatatype->getOptions() as $option): ?>
                                                <input type="radio" name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional" value="<?= $option->getID(); ?>" <?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof \thebuggenie\core\entities\CustomDatatypeOption && $selected_customdatatype[$customdatatype->getKey()]->getID() == $option->getID()): ?> selected<?php endif; ?> /> <?= $option->getName(); ?><br>
                                            <?php
                                            endforeach;
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXT:
                                            ?>
                                            <input type="text" name="<?= $customdatatype->getKey(); ?>_value" class="field_additional" value="<?= $selected_customdatatype[$customdatatype->getKey()]; ?>" id="<?= $customdatatype->getKey(); ?>_value_additional" />
                                            <?php
                                            break;
                                        case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_SMALL:
                                        case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_MAIN:
                                            ?>
                                            <label for="<?= $customdatatype->getKey(); ?>_value_additional"><?= $customdatatype->getDescription(); ?></label>
                                            <br>
                                            <?php include_component('main/textarea', array('area_name' => $customdatatype->getKey().'_value', 'target_type' => 'project', 'target_id' => $selected_project->getID(), 'area_id' => $customdatatype->getKey().'_value_additional', 'height' => '125px', 'hide_hint' => true, 'width' => '100%', 'value' => $selected_customdatatype[$customdatatype->getKey()])); ?>
                                            <?php
                                            break;
                                    }
                                    if (!$customdatatype->hasCustomOptions())
                                    {
                                        ?>
                                        <a href="javascript:void(0);" class="img" onclick="$('<?= $customdatatype->getKey(); ?>_link').show();$('<?= $customdatatype->getKey(); ?>_additional_div').hide();$('<?= $customdatatype->getKey(); ?>_value_additional').setValue('');"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <a href="javascript:void(0);" class="img" onclick="$('<?= $customdatatype->getKey(); ?>_link').show();$('<?= $customdatatype->getKey(); ?>_additional_div').hide();$('<?= $customdatatype->getKey(); ?>_id_additional').setValue(0);"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                                        <?php
                                    }
                                    ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    <?php \thebuggenie\core\framework\Event::createNew('core', 'reportissue.listfields')->trigger(); ?>
                </ul>
                <div style="clear: both;"> </div>
            </div>
        </div>
    <?php endif; ?>
</form>
