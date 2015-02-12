<div class="backdrop_box large transition-selector">
    <div class="backdrop_detail_header">
        <?php echo __('Transition issue'); ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <table class="whiteboard-columns transition-selector <?php echo ($board->usesSwimlanes()) ? ' swimlanes' : ' no-swimlanes'; ?>">
            <thead id="whiteboard-headers">
                <tr>
                    <?php include_component('agile/boardcolumnheader', array('column' => $current_column)); ?>
                    <?php include_component('agile/boardcolumnheader', array('column' => $new_column)); ?>
                </tr>
            </thead>
            <tbody>
                <td class="column current_column">
                    <?php include_component('agile/whiteboardissue', array('issue' => $issue, 'column' => $current_column, 'fake' => true)); ?>
                    <?php echo image_tag('transition_selector_indicator.png', array('class' => 'transition-selector-indicator')); ?>
                </td>
                <td class="column"><?php include_component('agile/whiteboardissue', array('issue' => $issue, 'column' => $new_column, 'fake' => true)); ?></td>
            </tbody>
        </table>
        <h2><?php echo __('Please select which transition to apply'); ?></h2>
        <?php foreach ($statuses as $status_id): ?>
            <div class="transition">
                <a class="button button-silver" href="javascript:void(0);" onclick="TBG.Project.Planning.Whiteboard.moveIssueColumn(jQuery('#whiteboard_issue_<?php echo $issue->getID(); ?>'), jQuery('#swimlane_<?php echo $swimlane_identifier; ?>_column_<?php echo $new_column->getID(); ?>'), <?php echo $transitions[$status_id]->getID(); ?>);"><?php echo $transitions[$status_id]->getName(); ?></a>
                <p><?php echo $transitions[$status_id]->getDescription(); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
