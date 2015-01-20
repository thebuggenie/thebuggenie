<li>
    <?php if ($target instanceof \thebuggenie\core\entities\User): ?>
        <input type="hidden" name="access_list_users[<?php echo $target->getID(); ?>]" value="<?php echo $target->getID(); ?>">
        <?php echo javascript_link_tag(image_tag('icon_delete.png', array('style' => 'float: right;')), array('onclick' => "if ($(this).up('ul').childElements().size() == 2) { $(this).up('ul').select('li.faded_out').first().show(); }$(this).up('li').remove();", 'title' => __('Remove access for this user'))); ?>
        <?php echo include_component('main/userdropdown', array('user' => $target)); ?>
    <?php elseif ($target instanceof \thebuggenie\core\entities\Team): ?>
        <?php echo javascript_link_tag(image_tag('icon_delete.png', array('style' => 'float: right;')), array('onclick' => "if ($(this).up('ul').childElements().size() == 2) { $(this).up('ul').select('li.faded_out').first().show(); }$(this).up('li').remove();", 'title' => __('Remove access for this team'))); ?>
        <input type="hidden" name="access_list_teams[<?php echo $target->getID(); ?>]" value="<?php echo $target->getID(); ?>">
        <?php echo include_component('main/teamdropdown', array('team' => $target)); ?>
    <?php endif; ?>
</li>
