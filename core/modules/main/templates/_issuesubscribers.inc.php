<div class="backdrop_box medium" id="viewissue_add_item_div">
    <div class="backdrop_detail_header">
        <?php echo javascript_link_tag(__('Add a user'), array('onclick' => "$('popup_find_subscriber_{$issue->getID()}').toggle('block');", 'style' => 'float: right;', 'class' => 'button button-silver')); ?>
        <?php echo __('Manage issue subscribers'); ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php include_component('identifiableselector', array(    'html_id'             => "popup_find_subscriber_{$issue->getID()}",
                                                                'header'             => __('Subscribe someone to this issue'),
                                                                'callback'             => "TBG.Issues.toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $issue->getID(), 'user_id' => '%identifiable_value'))."', '".$issue->getID()."_%identifiable_value');",
                                                                'base_id'            => "popup_find_subscriber_{$issue->getID()}",
                                                                'include_teams'        => false,
                                                                'allow_clear'        => false,
                                                                'style'             => array('right' => '8px'),
                                                                'absolute'            => true)); ?>
        <p>
            <?php echo __('The list below shows all users manually subscribed to notifications about this issue. To toggle whether they receive notifications, click the star next to their name.'); ?>
        </p>
        <ul id="subscribers_list" class="simple_list" style="margin-top: 15px;">
            <?php foreach ($users as $user): ?>
                <?php include_component('main/issuesubscriber', compact('user', 'issue')); ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="backdrop_detail_footer">
        <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Done'); ?></a>
    </div>
</div>
