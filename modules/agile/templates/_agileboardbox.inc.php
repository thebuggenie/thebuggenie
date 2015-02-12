<?php use thebuggenie\modules\agile\entities\AgileBoard; ?>
<li id="agileboard_<?php echo $board->getID(); ?>" class="agileboard">
    <div class="actionlinks">
        <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'agileboard', 'project_id' => $board->getProject()->getID(), 'board_id' => $board->getID())); ?>');" title="<?php echo __('Edit this board'); ?>"><?php echo image_tag('icon-mono-settings.png'); ?></a>
        <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Delete this board?'); ?>', '<?php echo __('Do you really want to delete this board?').'<br>'.__('Deleting this will make it unavailable. No issues or saved searches will be affected by this action.'); ?>', {yes: {click: function() {TBG.Project.Planning.removeAgileBoard('<?php echo make_url('agile_board', array('board_id' => $board->getID(), 'project_key' => $board->getProject()->getKey())); ?>');}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});" title="<?php echo __('Delete this board'); ?>"><?php echo image_tag('action_delete.png'); ?></a>
    </div>
    <a href="<?php echo make_url('agile_whiteboard', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>" class="board">
        <?php switch ($board->getType())
                {
                    case AgileBoard::TYPE_GENERIC:
                        echo image_tag('board_generic.png');
                        break;
                    case AgileBoard::TYPE_SCRUM:
                        echo image_tag('board_scrum.png');
                        break;
                    case AgileBoard::TYPE_KANBAN:
                        echo image_tag('board_kanban.png');
                        break;
                }
                ?>
        <div class="board_details">
            <div class="board_name"><?php echo $board->getName(); ?></div>
            <div class="board_description"><?php echo $board->getDescription(); ?></div>
        </div>
    </a>
</li>
