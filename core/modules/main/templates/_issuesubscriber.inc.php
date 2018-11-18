<li>
    <?php echo image_tag('spinning_16.gif', array('id' => 'issue_favourite_indicator_'.$issue->getId().'_'.$user->getID(), 'style' => 'display: none; float: left; margin-right: 5px;')); ?>
    <?php echo fa_image_tag('star', array('id' => 'issue_favourite_faded_'.$issue->getId().'_'.$user->getID(), 'class' => 'unsubscribed', 'style' => 'display: none', 'onclick' => "TBG.Issues.toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $issue->getID(), 'user_id' => $user->getID()))."', '".$issue->getID().'_'.$user->getID()."');")); ?>
    <?php echo fa_image_tag('star', array('id' => 'issue_favourite_normal_'.$issue->getId().'_'.$user->getID(), 'class' => 'subscribed', 'onclick' => "TBG.Issues.toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $issue->getID(), 'user_id' => $user->getID()))."', '".$issue->getID().'_'.$user->getID()."');")); ?>
    <?php include_component('main/userdropdown', compact('user')); ?>
</li>