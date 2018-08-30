<div class="backdrop_box medium" id="viewissue_add_item_div">
    <div class="backdrop_detail_header">
        <span><?= __('Add affected item'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <form id="viewissue_add_item_form" action="<?= make_url('add_affected', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>" method="post" accept-charset="<?= \thebuggenie\core\framework\Settings::getCharset(); ?>" onsubmit="TBG.Issues.Affected.add('<?= make_url('add_affected', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>');return false;">
        <div id="backdrop_detail_content" class="backdrop_detail_content">
            <?= __('Please select the type and item you wish to add as affected by this issue.'); ?>
            <br />
            <div class="header_div"><?= __('Item type'); ?></div>
            <?php if ($issue->getProject()->isEditionsEnabled() && $issue->canEditAffectedEditions() && isset($editions) && count($editions)): ?>
                <input type="radio" name="item_type" id="item_type_edition" value="edition" onclick="$('no_type').hide(); $('which_item_edition').show(); $('which_item_component').hide(); $('which_item_build').hide(); $('item_submit').show();" /><label for="item_type_edition"> <?= __('Edition'); ?></label><br />
            <?php endif; ?>
            <?php if ($issue->getProject()->isComponentsEnabled() && $issue->canEditAffectedComponents() && isset($components) && count($components)): ?>
                <input type="radio" name="item_type" id="item_type_component" value="component" onclick="$('no_type').hide(); $('which_item_edition').hide(); $('which_item_component').show(); $('which_item_build').hide(); $('item_submit').show();" /><label for="item_type_component"> <?= __('Component'); ?></label><br />
            <?php endif; ?>
            <?php if ($issue->getProject()->isBuildsEnabled() && $issue->canEditAffectedBuilds() && isset($builds) && count($builds)): ?>
                <input type="radio" name="item_type" id="item_type_build" value="build" onclick="$('no_type').hide(); $('which_item_edition').hide(); $('which_item_component').hide(); $('which_item_build').show(); $('item_submit').show();" /><label for="item_type_build"> <?= __('Release'); ?></label><br />
            <?php endif; ?>
            <?php if ($issue->getProject()->isBuildsEnabled() || $issue->getProject()->isComponentsEnabled() || $issue->getProject()->isEditionsEnabled()): ?>
                <div class="header_div"><?= __('Affected item'); ?></div>
                <div class="faded_out" id="no_type" style="padding-top: 10px;"><?=('Please select an item type'); ?></div>
                <select name="which_item_edition" id="which_item_edition" style="width: 100%; margin-top: 10px; display: none;">
                <?php if ($issue->getProject()->isEditionsEnabled() && $issue->canEditAffectedEditions()): ?>
                    <?php foreach ($editions as $edition): ?>
                    <option value="<?= $edition->getID(); ?>"><?= $edition->getName(); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
                </select>
                <select name="which_item_component" id="which_item_component" style="width: 100%; margin-top: 10px; display: none;">
                <?php if ($issue->getProject()->isComponentsEnabled() && $issue->canEditAffectedComponents()): ?>
                    <?php foreach ($components as $component): ?>
                    <option value="<?= $component->getID(); ?>"><?= $component->getName(); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
                </select>
                <select name="which_item_build" id="which_item_build" style="width: 100%; margin-top: 10px; display: none;">
                <?php if ($issue->getProject()->isBuildsEnabled() && $issue->canEditAffectedBuilds()): ?>
                    <?php foreach ($builds as $build): ?>
                    <option value="<?= $build->getID(); ?>"><?= $build->getName(); ?> (<?php print $build->getVersionMajor(); ?>.<?php print $build->getVersionMinor(); ?>.<?php print $build->getVersionRevision(); ?>)</option>
                    <?php endforeach; ?>
                <?php endif; ?>
                </select>
            <?php else: ?>
                <div class="faded_out" style="padding-top: 10px;"><?=('No item types are enabled'); ?></div>
            <?php endif; ?>
        </div>
        <?php if ($issue->getProject()->isBuildsEnabled() || $issue->getProject()->isComponentsEnabled() || $issue->getProject()->isEditionsEnabled()): ?>
            <div class="backdrop_details_submit">
                <span class="explanation"></span>
                <div class="submit_container">
                    <button type="submit" style="display: none;" id="item_submit"><?= image_tag('spinning_16.gif', ['style' => 'display: none;', 'id' => 'add_affected_spinning']) . __('Add this item'); ?></button>
                </div>
            </div>
        <?php endif; ?>
    </form>
</div>
