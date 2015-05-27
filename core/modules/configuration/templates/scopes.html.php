<?php $tbg_response->setTitle(__('Configure scopes')); ?>
<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0 class="configuration_page">
    <tr>
        <?php include_component('leftmenu', array('selected_section' => \thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_SCOPES)); ?>
        <td valign="top" style="padding-left: 15px;">
            <div style="width: 730px;" id="config_scopes">
                <h3><?php echo __('Configure scopes'); ?></h3>
                <div class="content faded_out">
                    <p>
                        <?php echo __('The Bug Genie scopes are self-contained environments within the same The Bug Genie installation, set up to be initialized when The Bug Genie is accessed via different hostnames.'); ?>
                        <?php echo __('The default scope (which is created during the first installation) is used for all hostnames where there is no other scope defined. Read more about scopes in %ConfigureScopes.', array('%ConfigureScopes' => link_Tag(make_url('publish_article', array('article_name' => 'ConfigureScopes')), 'ConfigureScopes'))); ?>
                    </p>
                </div>
                <?php if (isset($scope_deleted)): ?>
                    <div class="greenbox" style="margin: 0 0 5px 0; font-size: 14px;">
                        <?php echo __('The scope was deleted'); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($scope_hostname_error)): ?>
                    <div class="redbox" style="margin: 0 0 5px 0; font-size: 14px;">
                        <?php echo __('The hostname must be unique and cannot be blank'); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($scope_name_error)): ?>
                    <div class="redbox" style="margin: 0 0 5px 0; font-size: 14px;">
                        <?php echo __('The scope name must be unique and cannot be blank'); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($scope_saved)): ?>
                    <div class="greenbox" style="margin: 0 0 5px 0; font-size: 14px;">
                        <?php echo __('The settings were saved successfully'); ?>
                    </div>
                <?php endif; ?>
                <h5 style="margin-top: 10px;">
                    <button class="button button-green" onclick="$('new_scope_hostname').toggle();if ($('new_scope_hostname').visible()) { $('new_scope_name_input').focus(); }" style="float: right;"><?php echo __('Create a new scope'); ?></button>
                    <?php echo __('Scopes available on this installation'); ?>
                </h5>
                <div class="yellowbox" id="new_scope_hostname" style="display: none; position: absolute; width: 720px; z-index: 100;">
                    <form action="<?php echo make_url('configure_scopes'); ?>" onsubmit="$('add_scope_indicator').show();$('add_scope_submit_button').hide();return true;" method="POST">
                        <div class="content">
                            <div class="header" style="margin-top: 0;"><?php echo __('Create a new scope'); ?></div>
                            <label for="new_scope_name_input"><?php echo __('Scope name'); ?></label>
                            <input id="new_scope_name_input" name="name" style="width: 250px;">
                            <div class="content faded_out" style="margin-bottom: 10px;">
                                <?php echo __('The scope name is used in the list below as a short descriptive name for the scope'); ?>
                            </div>
                            <label for="new_scope_hostname_input"><?php echo __('Scope hostname'); ?></label>
                            <input id="new_scope_hostname_input" name="hostname" style="width: 250px;">
                            <div class="content faded_out">
                                <?php echo __('The hostname should be provided without protocol or the trailing slash (.com, not .com/) and port specified if desired. Valid examples are: %examples', array('%examples' => '')); ?>
                                <i>bugs.mycompany.com , internal.company.org , thebuggenie.company.com , dev.company.com:8080</i>
                            </div>
                        </div>
                        <div style="float: right; padding: 2px 5px 0 5px;"><?php echo __('%create_scope or %cancel', array('%create_scope' => '', '%cancel' => javascript_link_tag(__('cancel'), array('onclick' => "$('new_scope_hostname').toggle();")))); ?></div>
                        <input type="submit" value="<?php echo __('Create scope'); ?>" style="float: right; font-weight: bold;" id="add_scope_submit_button">
                        <?php echo image_tag('spinning_16.gif', array('id' => 'add_scope_indicator', 'style' => 'float: right; display: none;')); ?>
                    </form>
                    <br style="clear: both;">
                </div>
                <div id="scopes_list" style="margin-top: 5px;">
                    <?php foreach ($scopes as $scope): ?>
                        <?php include_component('configuration/scopebox', array('scope' => $scope)); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </td>
    </tr>
</table>
