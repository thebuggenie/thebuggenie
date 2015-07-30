<?php foreach ($search_object->getIssues() as $issue): ?>
    <?php list ($showtablestart, $showheader, $prevgroup_id, $groupby_description) = \thebuggenie\core\modules\search\controllers\Main::resultGrouping($issue, $search_object->getGroupby(), $cc, $prevgroup_id); ?>
    <?php if ($showtablestart && $cc > 1): ?>
        <?php echo '</tbody></table>'; ?>
    <?php endif; ?>
    <?php if ($showheader): ?>
        <h3 style="margin-top: 20px;"><?php echo $groupby_description; ?></h3>
    <?php endif; ?>
    <?php if ($showtablestart): ?>
        <table style="width: 100%;" cellpadding="0" cellspacing="0" class="resizable sortable">
            <thead>
                <tr>
                    <th style="text-align: center; width: 60px;"><?php echo __('Progress'); ?></th>
                    <th style="width: auto; padding-left: 2px;"><?php echo __('Title'); ?></th>
                    <th style="width: auto;"><?php echo __('Description'); ?></th>
                    <th style="width: auto; padding-left: 2px;"><?php echo __('Status'); ?></th>
                    <th style="width: 170px;"><?php echo __('More info'); ?></th>
                </tr>
            </thead>
            <tbody>
    <?php endif; ?>
                <tr class="<?php if ($issue->hasUnsavedChanges()): ?> changed<?php endif; ?><?php if ($issue->isBlocking()): ?> blocking<?php endif; ?>">
                    <td style="text-align: center; background-color: <?php

                        switch (true)
                        {
                            case ($issue->getPercentCompleted() == 0):
                                echo '#BF0303; color: #FFF';
                                break;
                            case ($issue->getPercentCompleted() <= 20):
                                echo '#80B5FF';
                                break;
                            case ($issue->getPercentCompleted() <= 40):
                                echo '#FFF6C8';
                                break;
                            case ($issue->getPercentCompleted() <= 60):
                                echo '#F3C300';
                                break;
                            case ($issue->getPercentCompleted() < 100):
                                echo '#D9E8C3';
                                break;
                            case ($issue->getPercentCompleted() == 100):
                                echo '#37A42B';
                                break;
                        }

                    ?>; font-weight: bold;"><?php echo $issue->getPercentCompleted(); ?>%</td>
                    <td style="padding: 3px; font-weight: bold;"><?php echo (mb_strlen($issue->getTitle()) > 60) ? mb_substr($issue->getTitle(), 0 , 57) . '...' : $issue->getTitle(); ?></td>
                    <td style="padding: 3px; color: #888;">
                        <?php if ($issue->hasDescription()): ?>
                            <?php echo (mb_strlen($issue->getDescription()) > 120) ? mb_substr($issue->getDescription(), 0 , 117) . '...' : $issue->getDescription(); ?>
                        <?php else: ?>
                            <span class="faded_out"><?php echo __('No description provided'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 3px;">
                        <?php if ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype): ?>
                            <div class="sc_status_color status_badge" style="background-color: <?php echo ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>;"><span class="sc_status_name"><?php echo $issue->getStatus()->getName(); ?></span></div>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="result_issue"><?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedIssueNo(true, true), array('class' => 'issue_link')); ?></td>
                </tr>
    <?php if ($cc == $search_object->getTotalNumberOfIssues()): ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php $cc++; ?>
<?php endforeach; ?>
