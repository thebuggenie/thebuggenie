<div class="project_header">
    <?php if ($tbg_response->getPage() == 'project_summary'): ?>
        <div class="project_header_right button-group">
            <?php \thebuggenie\core\framework\Event::createNew('core', 'project_header_buttons')->trigger(); ?>
            <?php if ($selected_project->hasDownloads() && $tbg_response->getPage() != 'project_releases'): ?>
                <?php echo link_tag(make_url('project_releases', array('project_key' => $selected_project->getKey())), image_tag('icon_download.png').__('Download'), array('class' => 'button button-orange')); ?>
            <?php endif; ?>
            <?php if ($selected_project->hasParent()): ?>
                <?php echo link_tag(make_url('project_dashboard', array('project_key' => $selected_project->getParent()->getKey())), image_tag($selected_project->getParent()->getSmallIconName(), array('style' => 'width: 16px; height: 16px;'), $selected_project->getParent()->hasSmallIcon()) . __('Up to %parent', array('%parent' => $selected_project->getParent()->getName())), array('class' => 'button button-silver')); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php \thebuggenie\core\framework\Event::createNew('core', 'project/templates/projectheader', $selected_project)->trigger(); ?>
    <div class="project_header_left">
        <?php echo image_tag($selected_project->getLargeIconName(), array('class' => 'project_header_logo'), $selected_project->hasLargeIcon()); ?>
        <div id="project_name">
            <span id="project_name_span">
                <?php echo $selected_project->getName(); ?>
            </span>
            <span id="project_sub_page"><?php echo $subpage; ?></span>
        </div>
        <?php if ($tbg_response->getPage() == 'project_dashboard' && $tbg_user->canEditProjectDetails($selected_project)): ?>
            <div class="project_header_right button-group">
                <a href="javascript:void(0);" class="button button-silver" onclick="$$('.dashboard').each(function (elm) { elm.toggleClassName('editable');});$(this).toggleClassName('button-pressed');"><?php echo __('Edit dashboard'); ?></a>
            </div>
        <?php endif; ?>
    </div>
</div>
