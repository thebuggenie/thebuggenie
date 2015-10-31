<div class="backdrop_box large transition-selector">
    <div class="backdrop_detail_header">
        <?php echo __('Transition issue'); ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="table whiteboard-columns transition-selector <?php echo ($board->usesSwimlanes()) ? ' swimlanes' : ' no-swimlanes'; ?>">
            <div class="thead" id="whiteboard-headers">
                <div class="tr">
                    <?php include_component('agile/boardcolumnheader', array('column' => $current_column)); ?>
                    <?php include_component('agile/boardcolumnheader', array('column' => $new_column)); ?>
                </div>
            </div>
            <div class="tbody">
                <div class="tr">
                    <div class="td column current_column">
                        <?php include_component('agile/whiteboardissue', array('issue' => $issue, 'column' => $current_column, 'fake' => true)); ?>
                        <?php echo image_tag('transition_selector_indicator.png', array('class' => 'transition-selector-indicator')); ?>
                    </div>
                    <div class="td column"><?php include_component('agile/whiteboardissue', array('issue' => $issue, 'column' => $new_column, 'fake' => true)); ?></div>
                </div>
            </div>
        </div>
        <h2><?php echo __('Please select which transition to apply'); ?></h2>
        <?php foreach ($statuses as $status_id): ?>
            <?php if (in_array($status_id, $same_transition_statuses)) continue; ?>
            <div class="transition">
                <a class="button button-silver transition-selector-button" href="javascript:void(0);" data-issue-id="<?php echo $issue->getID(); ?>" data-swimlane-identifier="<?php echo $swimlane_identifier; ?>" data-column-id="<?php echo $new_column->getID(); ?>" data-transition-id="<?php echo $transitions[$status_id][$statuses_occurred[$status_id]]->getID(); ?>"><?php echo $transitions[$status_id][$statuses_occurred[$status_id]]->getName(); ?></a>
                <p><?php echo $transitions[$status_id][$statuses_occurred[$status_id]]->getDescription(); ?></p>
            </div>
            <?php $statuses_occurred[$status_id]++; ?>
        <?php endforeach; ?>
    </div>
    <div class="backdrop_detail_footer">
        <a id="transition-selector-close-link" href="javascript:void(0);"><?php echo __('Cancel and close this pop-up'); ?></a>
    </div>
</div>
