<div style="margin-top: 5px;" class="tab_menu inset">
    <ul id="publish_settings_menu">
        <li class="selected" id="publish_tab_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('publish_tab_settings', 'publish_settings_menu');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_general.png', array('style' => 'float: left;')).__('General wiki settings'); ?></a></li>
        <li id="publish_tab_import"><a onclick="TBG.Main.Helpers.tabSwitcher('publish_tab_import', 'publish_settings_menu');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_import.png', array('style' => 'float: left;')).__('Import wiki articles'); ?></a></li>
    </ul>
</div>
<div id="publish_settings_menu_panes">
    <div id="publish_tab_settings_pane" style="margin: 10px 0 0 0;">
        <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_module', array('config_module' => $module->getName())); ?>" enctype="multipart/form-data" method="post">
            <table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0>
                <tr>
                    <td style="width: 200px; padding: 5px;"><label for="publish_menu_title"><?php echo __('Menu title'); ?></label></td>
                    <td>
                        <select name="menu_title" id="publish_menu_title" style="width: 250px;"<?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL) ? ' disabled' : ''; ?>>
                            <option value=5 <?php echo ($module->getSetting('menu_title') == 5) ? ' selected' : ''; ?>><?php echo __('Project archive / Archive'); ?></option>
                            <option value=3 <?php echo ($module->getSetting('menu_title') == 3) ? ' selected' : ''; ?>><?php echo __('Project documentation / Documentation'); ?></option>
                            <option value=4 <?php echo ($module->getSetting('menu_title') == 4) ? ' selected' : ''; ?>><?php echo __('Project documents / Documents'); ?></option>
                            <option value=2 <?php echo ($module->getSetting('menu_title') == 2) ? ' selected' : ''; ?>><?php echo __('Project help / Help'); ?></option>
                            <option value=1 <?php echo ($module->getSetting('menu_title') == 1 || $module->getSetting('menu_title') == 0) ? ' selected' : ''; ?>><?php echo __('Project wiki / Wiki'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?php echo __('Specify here if you want to show a different menu title than "Wiki" in the header menu'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="allow_camelcase_links_yes"><?php echo __('Allow "CamelCased" links'); ?></label></td>
                    <td>
                        <input type="radio" name="allow_camelcase_links" value="1" id="allow_camelcase_links_yes"<?php if ($module->getSetting('allow_camelcase_links') == 1): ?> checked<?php endif; ?>>&nbsp;<label for="allow_camelcase_links_yes"><?php echo __('Yes'); ?></label>&nbsp;
                        <input type="radio" name="allow_camelcase_links" value="0" id="allow_camelcase_links_no"<?php if ($module->getSetting('allow_camelcase_links') == 0): ?> checked<?php endif; ?>>&nbsp;<label for="allow_camelcase_links_no"><?php echo __('No'); ?></label>
                    </td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?php echo __('Traditionally, %CamelCasing has been used to specify links between documents in Wikis. If you want to keep this turned on, specify so here. Make sure you read the %wikiformatting wiki article if you are unsure how to use this feature.', array('%CamelCasing' => link_tag('http://wikipedia.org/wiki/CamelCase', __('CamelCasing'), array('target' => '_blank')), '%wikiformatting' => link_tag(make_url('publish_article', array('article_name' => 'WikiFormatting')), 'WikiFormatting', array('target' => '_blank')))); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="hide_wiki_links_no"><?php echo __('Show "Wiki" links'); ?></label></td>
                    <td>
                        <input type="radio" name="hide_wiki_links" value="0" id="hide_wiki_links_no"<?php if ($module->getSetting('hide_wiki_links') != 1): ?> checked<?php endif; ?>>&nbsp;<label for="hide_wiki_links_no"><?php echo __('Yes'); ?></label>&nbsp;
                        <input type="radio" name="hide_wiki_links" value="1" id="hide_wiki_links_yes"<?php if ($module->getSetting('hide_wiki_links') == 1): ?> checked<?php endif; ?>>&nbsp;<label for="hide_wiki_links_yes"><?php echo __('No'); ?></label>
                    </td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?php echo __('Setting this to "%no" will hide all "Wiki" tabs and links', array('%no' => __('No'))); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="require_change_reason_yes"><?php echo __('Require change reason'); ?></label></td>
                    <td>
                        <input type="radio" name="require_change_reason" value="1" id="require_change_reason_yes"<?php if ($module->getSetting('require_change_reason') == 1): ?> checked<?php endif; ?>>&nbsp;<label for="require_change_reason_yes"><?php echo __('Yes'); ?></label>&nbsp;
                        <input type="radio" name="require_change_reason" value="0" id="require_change_reason_no"<?php if ($module->getSetting('require_change_reason') != 1): ?> checked<?php endif; ?>>&nbsp;<label for="require_change_reason_no"><?php echo __('No'); ?></label>
                    </td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?php echo __('Setting this to "%no" will not require users to enter a reason when saving Wiki changes', array('%no' => __('No'))); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="wiki_free_edit"><?php echo __('Wiki permissions'); ?></label></td>
                    <td>
                        <select name="free_edit" id="wiki_free_edit">
                            <option value="2" id="free_edit_everyone"<?php if ($module->getSetting('free_edit') == 2): ?> selected<?php endif; ?>><?php echo __('Open for everyone with access to add / remove content'); ?></label><br>
                            <option value="1" id="free_edit_registered"<?php if ($module->getSetting('free_edit') == 1): ?> selected<?php endif; ?>><?php echo __('Only registered users can add / remove content'); ?></label>
                            <option value="0" id="free_edit_permissions"<?php if ($module->getSetting('free_edit') == 0): ?> selected<?php endif; ?>><?php echo __('Set wiki permissions manually'); ?></label>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?php echo __('Specify how you want to control access to wiki editing functionality'); ?></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 5px; text-align: right;">&nbsp;</td>
                </tr>
            </table>
        <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
            <div class="bluebox" style="margin: 0 0 5px 0;">
                <?php echo __('Click "%save" to save wiki notification settings', array('%save' => __('Save'))); ?>
                <input type="submit" id="submit_settings_button" style="margin: -3px -3px 0 0; float: right; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
            </div>
        <?php endif; ?>
        </form>
    </div>
    <div id="publish_tab_import_pane" style="margin: 10px 0 0 0; display: none;">
        <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_module', array('config_module' => $module->getName())); ?>" enctype="multipart/form-data" method="post">
            <input type="hidden" name="import_articles" value="1">
            <div class="greybox" style="margin-bottom: 5px;">
                <label for="select_article_categories"><?php echo __('Show articles in namespace'); ?>: </label>
                <select id="select_article_categories" onchange="$('import_articles_list').childElements().each(function (elm) { if (elm.hasClassName('article_category_' + $('select_article_categories').getValue())) { elm.show(); var chkval = true; } else { elm.hide(); } elm.select('input[type=checkbox]').each(function (chkbx) { chkbx.enabled = chkval; }); })">
                    <option value="" selected><?php echo __('Without namespace'); ?></option>
                    <?php foreach ($categories as $category_key => $category_name): ?>
                        <option value="<?php echo $category_key; ?>"><?php echo $category_name; ?></option>
                    <?php endforeach; ?>
                </select>
                <br style="clear: both;">
                <input type="checkbox" id="import_articles_select_all" onchange="$('import_articles_list').childElements().each(function (elm) { elm.select('input[type=checkbox]').each(function (chkbx) { chkbx.checked = elm.hasClassName('article_category_' + $('select_article_categories').getValue()) && $('import_articles_select_all').checked; }); })">&nbsp;<?php echo __('Toggle selection on visible articles'); ?>
            </div>
            <p class="faded_out" style="margin-bottom: 5px;">
                <?php echo __('Please select which articles to import, from the list of available articles below. When you are finished, click the %import_articles button at the bottom', array('%import_articles' => __('Import articles'))); ?>
            </p>
            <ul class="simple_list" id="import_articles_list">
            <?php foreach ($articles as $article_name => $details): ?>
                <li class="article_category_<?php echo $details['category']; ?>" style="<?php if ($details['category'] != '') echo 'display: none;'; ?>">
                    <input type="checkbox" value="1" name="import_article[<?php echo $article_name; ?>]" id="import_article_<?php echo mb_strtolower($article_name); ?>"<?php if (!$details['exists']) echo ' selected'; ?>>&nbsp;
                    <label for="import_article_<?php echo mb_strtolower($article_name); ?>"><?php echo urldecode($article_name); ?></label>
                    <?php if ($details['exists']): ?>
                        &nbsp;<?php echo link_tag(make_url('publish_article', array('article_name' => $article_name)), __('Open existing article in new window'), array('style' => 'font-size: 0.8em;', 'target' => "_{$article_name}")); ?>
                        <div class="faded_out"><?php echo __('Importing this article will overwrite an existing article in the database'); ?></div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
            <br style="clear: both;">
            <div class="bluebox" style="margin: 0 0 5px 0;">
                <?php echo __('Click "%import_articles" to import the selected articles', array('%import_articles' => __('Import articles'))); ?>
                <input type="submit" id="submit_import_button" style="margin: -3px -3px 0 0; float: right; font-size: 14px; font-weight: bold;" value="<?php echo __('Import articles'); ?>">
            </div>
        <?php endif; ?>
        </form>
    </div>
</div>
