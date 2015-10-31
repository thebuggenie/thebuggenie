<div id="tab_vcs_pane"<?php if ($selected_tab != 'vcs'): ?> style="display: none;"<?php endif; ?>>
<h3><?php echo __('Editing VCS connectivity settings');?></h3>
    <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
        <div class="rounded_box red" style="margin-top: 10px;">
            <?php echo __('You do not have the relevant permissions to access VCS Integration settings'); ?>
        </div>
    <?php else: ?>
        <div class="rounded_box lightgrey"><?php echo __("Remember to set up the hook after saving these settings - see the %documentation. You will need this project's ID number: %id", array('%id' => '<b>'.$project->getID().'</b>', '%documentation' => link_tag(make_url('publish_article', array('article_name' => 'VCSIntegration')), __('documentation'), array('target' => '_blank')))); ?></div>
        <br>
        <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_vcs_settings', array('project_id' => $project->getID())); ?>" method="post" onsubmit="TBG.Main.Helpers.formSubmit('<?php echo make_url('configure_vcs_settings', array('project_id' => $project->getID())); ?>', 'vcs'); return false;" id="vcs">
            <input type="hidden" name="project_id" value="<?php echo $project->getID(); ?>">
            <table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
                <tr>
                    <td style="width: 200px;"><label for="vcs_mode"><?php echo __('Enable VCS Integration?'); ?></label></td>
                    <td style="width: 580px;">
                        <select name="vcs_mode" id="vcs_mode" style="width: 100%">
                            <option value="0"<?php if (\thebuggenie\core\framework\Settings::get('vcs_mode_'.$project->getID(), 'vcs_integration') == 0): ?> selected="selected"<?php endif;?>><?php echo __('Disable for this project');?></option>
                            <option value="1"<?php if (\thebuggenie\core\framework\Settings::get('vcs_mode_'.$project->getID(), 'vcs_integration') == 1): ?> selected="selected"<?php endif;?>><?php echo __('Enable for commits applying to existing issues only');?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width: 200px;"><label for="vcs_workflow"><?php echo __('Enable workflow?'); ?></label></td>
                    <td style="width: 580px;">
                        <select name="vcs_workflow" id="vcs_workflow" style="width: 100%">
                            <option value="0"<?php if (\thebuggenie\core\framework\Settings::get('vcs_workflow_'.$project->getID(), 'vcs_integration') == 0): ?> selected="selected"<?php endif;?>><?php echo __('Disable for this project');?></option>
                            <option value="1"<?php if (\thebuggenie\core\framework\Settings::get('vcs_workflow_'.$project->getID(), 'vcs_integration') == 1): ?> selected="selected"<?php endif;?>><?php echo __('Enable for this project');?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?php echo __('This will allow specially-crafted commit messages to cause a workflow transition, in the same way as incoming emails. See the documentation for details.'); ?></td>
                </tr>
                <tr>
                    <td style="width: 200px;"><label for="access_method"><?php echo __('Access method'); ?></label></td>
                    <td style="width: 580px;">
                        <select name="access_method" id="access_method" style="width: 100%" onchange="if ($('access_method').getValue() == '1') { $('http_passkey').show(); } else { $('http_passkey').hide(); }">
                            <option value="0"<?php if (\thebuggenie\core\framework\Settings::get('access_method_'.$project->getID(), 'vcs_integration') != 1): ?> selected="selected"<?php endif;?>><?php echo __('Direct Access (via a call to tbg_cli)'); ?></option>
                            <option value="1"<?php if (\thebuggenie\core\framework\Settings::get('access_method_'.$project->getID(), 'vcs_integration') == 1): ?> selected="selected"<?php endif;?>><?php echo __('HTTP Access (via a call to a URL)'); ?> - <?php echo __('Required for Github, Gitorious and Bitbucket users'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr id="http_passkey"<?php if (\thebuggenie\core\framework\Settings::get('access_method_'.$project->getID(), 'vcs_integration') != 1): ?> style="display: none;"<?php endif; ?>>
                    <td style="width: 200px;"><label for="access_passkey"><?php echo __('HTTP Passkey'); ?></label></td>
                    <td style="width: 580px; position: relative;">
                        <input type="text" name="access_passkey" style="width: 100%" id="access_passkey" value="<?php echo \thebuggenie\core\framework\Settings::get('access_passkey_'.$project->getID(), 'vcs_integration'); ?>" style="width: 100;">
                    </td>
                </tr>
                <tr>
                    <td style="width: 200px;"><label for="browser_url"><?php echo __('URL to repository browser'); ?></label></td>
                    <td style="width: 580px; position: relative;">
                        <input type="text" name="browser_url" style="width: 100%" id="browser_url" value="<?php echo \thebuggenie\core\framework\Settings::get('browser_url_'.$project->getID(), 'vcs_integration'); ?>" style="width: 100;">
                    </td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?php echo __('If the repository name is part of the URL (e.g. http://www.example.com/viewvc/myrepo), please include it as part of this field.'); ?></td>
                </tr>
                <tr>
                    <td style="width: 200px;"><label for="browser_type"><?php echo __('Repository browser type'); ?></label></td>
                    <td style="width: 580px; position: relative;">
                        <select name="browser_type" id="browser_type" style="width: 100%" onchange="if ($('browser_type').getValue() == 'websvn' || $('browser_type').getValue() == 'websvn_mv') { $('repository_box').show(); } else { $('repository_box').hide(); } if ($('browser_type').getValue() == 'other') { $('vcs_custom_urls').show(); } else { $('vcs_custom_urls').hide(); }">
                            <option value='other' selected="selected"><?php echo __('Set URLs manually'); ?></option>
                            <optgroup label="<?php echo __('Multi-system'); ?>">
                                <option value='viewvc'>CVS/SVN - ViewVC</option>
                            </optgroup>
                            <optgroup label="Subversion">
                                <option value='websvn'>WebSVN</option>
                                <option value='websvn_mv'>WebSVN <?php echo __('using MultiViews'); ?></option>
                            </optgroup>
                            <optgroup label="Mercurial">
                                <option value='hgweb'>hgweb</option>
                                <option value='rhodecode'>RhodeCode</option>
                            </optgroup>
                            <optgroup label="Git">
                                <option value='gitweb'>gitweb</option>
                                <option value='cgit' >cgit</option>
                                <option value='gitorious'>Gitorious (<?php echo __('locally hosted'); ?>)</option>
                                <option value='github'>Github</option>
                                <option value='gitlab'>Gitlab</option>
                                <option value='bitbucket'>Bitbucket</option>
                                <option value='rhodecode'>RhodeCode</option>
                            </optgroup>
                            <optgroup label="Bazaar">
                                <option value='loggerhead'>Loggerhead</option>
                            </optgroup>
                        </select>
                        <script type="text/javascript">
                            require(['domReady', 'jquery', 'prototype'], function (domReady, jquery, prototype) {
                                domReady(function () {
                                    var browser_type_value = "<?php echo \thebuggenie\core\framework\Settings::get('browser_type_'.$project->getID(), 'vcs_integration'); ?>";

                                    if (browser_type_value != '') {
                                        $('browser_type').value = browser_type_value;
                                        jquery('#browser_type').trigger('change');
                                    }
                                });
                            });
                        </script>
                    </td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?php echo __('If you are setting up for the first time, it is recommended you set a browser type, which will automatically set the URLs for you.'); ?></td>
                </tr>
                <tr id="repository_box" style="display: none">
                    <td style="width: 200px;"><label for="repository"><?php echo __('Repository name'); ?></label></td>
                    <td style="width: 580px; position: relative;">
                        <input type="text" name="repository" style="width: 100%" id="repository" style="width: 100;">
                    </td>
                </tr>
            </table>
            <div id="vcs_custom_urls">
                <h4><?php echo __('Custom browser URLs'); ?></h4>
                <div class="header"><?php echo __('In the Commit details page field, the following parameters will be replaced with a real value when link is generated:'); ?></div>
                <ul>
                    <li>%revno - <?php echo __('Revision number/hash of either the current or previous revision (the one to use is automatically chosen as appropriate)'); ?></li>
                    <li>%branch - <?php echo __('Branch (if provided by the hook). If no branch is provided, this will be left unchanged'); ?></li>
                </ul>
                <div class="header"><?php echo __('In the other fields, these parameters will be replaced with real values when links are generated:'); ?></div>
                <ul>
                    <li>%branch - <?php echo __('Branch (if provided by the hook). If no branch is provided, this will be left unchanged'); ?></li>
                    <li>%revno - <?php echo __('Revision number/hash'); ?></li>
                    <li>%oldrev - <?php echo __('Revision number/hash of previous commit'); ?></li>
                    <li>%file - <?php echo __('Filename and path, from root of repository'); ?></li>
                </ul>
                <table style="clear: both; width: 780px; margin-top: 10px" class="padded_table" cellpadding=0 cellspacing=0>
                    <tr>
                        <td style="width: 200px;"><label for="commit_url"><?php echo __('Commit details page'); ?></label></td>
                        <td style="width: 580px; position: relative;">
                            <input type="text" name="commit_url" style="width: 100%"s id="commit_url" value="<?php echo \thebuggenie\core\framework\Settings::get('commit_url_'.$project->getID(), 'vcs_integration'); ?>" style="width: 100;">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 200px;"><label for="log_url"><?php echo __('File log page'); ?></label></td>
                        <td style="width: 580px; position: relative;">
                            <input type="text" name="log_url" style="width: 100%" id="log_url" value="<?php echo \thebuggenie\core\framework\Settings::get('log_url_'.$project->getID(), 'vcs_integration'); ?>" style="width: 100;">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 200px;"><label for="blob_url"><?php echo __('File blob/view page'); ?></label></td>
                        <td style="width: 580px; position: relative;">
                            <input type="text" name="blob_url" style="width: 100%" id="blob_url" value="<?php echo \thebuggenie\core\framework\Settings::get('blob_url_'.$project->getID(), 'vcs_integration'); ?>" style="width: 100;">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 200px;"><label for="diff_url"><?php echo __('Diff page'); ?></label></td>
                        <td style="width: 580px; position: relative;">
                            <input type="text" name="diff_url" style="width: 100%" id="diff_url" value="<?php echo \thebuggenie\core\framework\Settings::get('diff_url_'.$project->getID(), 'vcs_integration'); ?>" style="width: 100;">
                        </td>
                    </tr>
                </table>
            </div>
            <table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
                <tr>
                    <td colspan="2" style="padding: 10px 0 10px 10px; text-align: right;">
                        <div style="float: left; font-size: 13px; padding-top: 2px; font-style: italic;" class="config_explanation"><?php echo __('When you are done, click "%save" to save your changes on all tabs', array('%save' => __('Save'))); ?></div>
                        <div id="vcs_button" style="float: right; font-size: 14px; font-weight: bold;">
                            <input type="submit" class="button button-green" value="<?php echo __('Save'); ?>">
                        </div>
                        <span id="vcs_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
                    </td>
                </tr>
            </table>
        </form>
    <?php endif; ?>
</div>
