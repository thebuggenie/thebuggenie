<table style="clear: both; width: 700px; margin-top: 5px;" class="padded_table" cellpadding=0 cellspacing=0>
    <tr>
        <td style="width: 200px;"><label for="b2_name"><?php echo __('The Bug Genie custom name'); ?></label></td>
        <td style="width: auto;">
            <input type="text" name="<?php echo \thebuggenie\core\framework\Settings::SETTING_TBG_NAME; ?>" id="b2_name"
                   value="<?php echo str_replace('"', '&quot;', \thebuggenie\core\framework\Settings::getSiteHeaderName()); ?>"
                   style="width: 90%;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>
            >
            <?php echo config_explanation(
                __('This is the name appearing in the headers and several other places, usually displaying "The Bug Genie"')
            ); ?>
        </td>
    </tr>
    <tr>
        <td>
            <label><?php echo __('Custom header and favicons'); ?></label>
        </td>
        <td>
            <div class="button button-blue" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', ['key' => 'site_icons']); ?>');"><span><?php echo __('Configure icons'); ?></span></div>
        </td>
    </tr>
    <tr>
        <td><label for="header_link"><?php echo __('Custom header link'); ?></label></td>
        <td>
            <input type="text" name="<?php echo \thebuggenie\core\framework\Settings::SETTING_HEADER_LINK; ?>"
                   id="header_link" value="<?php echo \thebuggenie\core\framework\Settings::getHeaderLink(); ?>"
                   style="width: 90%;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>
                >
            <?php echo config_explanation(
                __('You can alter the webpage that clicking on the header icon navigates to. If left blank it will link to the main page of this installation.')
            ); ?>
        </td>
    </tr>
    <tr>
        <td><label for="tbg_header_name_html"><?php echo __('Allow HTML in site title'); ?></label></td>
        <td>
            <input type="radio" name="<?php echo \thebuggenie\core\framework\Settings::SETTING_TBG_NAME_HTML; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?php echo \thebuggenie\core\framework\Settings::SETTING_TBG_NAME_HTML; ?>_yes" value=1<?php if (\thebuggenie\core\framework\Settings::isHeaderHtmlFormattingAllowed()): ?> checked<?php endif; ?>><label for="<?php echo \thebuggenie\core\framework\Settings::SETTING_TBG_NAME_HTML; ?>_yes"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
            <?php echo config_explanation(
                __('Enabling this setting allows a malicious admin user to potentially insert harmful code'), 'exclamation-triangle', 'fas'
            ); ?>
            <input type="radio" name="<?php echo \thebuggenie\core\framework\Settings::SETTING_TBG_NAME_HTML; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?php echo \thebuggenie\core\framework\Settings::SETTING_TBG_NAME_HTML; ?>_no" value=0<?php if (!\thebuggenie\core\framework\Settings::isHeaderHtmlFormattingAllowed()): ?> checked<?php endif; ?>><label for="<?php echo \thebuggenie\core\framework\Settings::SETTING_TBG_NAME_HTML; ?>_no"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
        </td>
    </tr>
    <tr>
        <td><label for="singleprojecttracker"><?php echo __('Single project tracker mode'); ?></label></td>
        <td>
            <input type="radio" name="<?php echo \thebuggenie\core\framework\Settings::SETTING_IS_SINGLE_PROJECT_TRACKER; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?php echo \thebuggenie\core\framework\Settings::SETTING_IS_SINGLE_PROJECT_TRACKER; ?>_yes" value=1<?php if (\thebuggenie\core\framework\Settings::isSingleProjectTracker()): ?> checked<?php endif; ?>><label for="<?php echo \thebuggenie\core\framework\Settings::SETTING_IS_SINGLE_PROJECT_TRACKER; ?>_yes"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
            <input type="radio" name="<?php echo \thebuggenie\core\framework\Settings::SETTING_IS_SINGLE_PROJECT_TRACKER; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?php echo \thebuggenie\core\framework\Settings::SETTING_IS_SINGLE_PROJECT_TRACKER; ?>_no" value=0<?php if (!\thebuggenie\core\framework\Settings::isSingleProjectTracker()): ?> checked<?php endif; ?>><label for="<?php echo \thebuggenie\core\framework\Settings::SETTING_IS_SINGLE_PROJECT_TRACKER; ?>_no"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
            <?php echo config_explanation(
                __('In single project tracker mode, The Bug Genie will display the homepage for the first project as the main page instead of the regular index page') .
                "<br>" .
                ((count(\thebuggenie\core\entities\Project::getAll()) > 1) ?
                    '<br><b class="more_than_one_project_warning">'.
                    __('More than one project exists. When in "single project" mode, accessing other projects than the first will become harder.') .
                    '</b>'
                    : ''
                )
            ); ?>
        </td>
    </tr>
    <tr>
        <td><label for="showprojectsoverview"><?php echo __('Show project list on frontpage'); ?></label></td>
        <td>
            <input type="radio" name="<?php echo \thebuggenie\core\framework\Settings::SETTING_SHOW_PROJECTS_OVERVIEW; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?php echo \thebuggenie\core\framework\Settings::SETTING_SHOW_PROJECTS_OVERVIEW; ?>_yes" value=1<?php if (\thebuggenie\core\framework\Settings::isFrontpageProjectListVisible()): ?> checked<?php endif; ?>><label for="<?php echo \thebuggenie\core\framework\Settings::SETTING_SHOW_PROJECTS_OVERVIEW; ?>_yes"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
            <input type="radio" name="<?php echo \thebuggenie\core\framework\Settings::SETTING_SHOW_PROJECTS_OVERVIEW; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?php echo \thebuggenie\core\framework\Settings::SETTING_SHOW_PROJECTS_OVERVIEW; ?>_no" value=0<?php if (!\thebuggenie\core\framework\Settings::isFrontpageProjectListVisible()): ?> checked<?php endif; ?>><label for="<?php echo \thebuggenie\core\framework\Settings::SETTING_SHOW_PROJECTS_OVERVIEW; ?>_no"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
            <?php echo config_explanation(
                __('Whether the project overview list should appear on the frontpage or not')
            ); ?>
        </td>
    </tr>
    <tr>
        <td><label for="previewcommentimages"><?php echo __('Preview images in comments'); ?></label></td>
        <td>
            <input type="radio" name="<?php echo \thebuggenie\core\framework\Settings::SETTING_PREVIEW_COMMENT_IMAGES; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?php echo \thebuggenie\core\framework\Settings::SETTING_PREVIEW_COMMENT_IMAGES; ?>_yes" value=1<?php if (\thebuggenie\core\framework\Settings::isCommentImagePreviewEnabled()): ?> checked<?php endif; ?>><label for="<?php echo \thebuggenie\core\framework\Settings::SETTING_PREVIEW_COMMENT_IMAGES; ?>_yes"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
            <input type="radio" name="<?php echo \thebuggenie\core\framework\Settings::SETTING_PREVIEW_COMMENT_IMAGES; ?>" class="fancycheckbox" <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?php echo \thebuggenie\core\framework\Settings::SETTING_PREVIEW_COMMENT_IMAGES; ?>_no" value=0<?php if (!\thebuggenie\core\framework\Settings::isCommentImagePreviewEnabled()): ?> checked<?php endif; ?>><label for="<?php echo \thebuggenie\core\framework\Settings::SETTING_PREVIEW_COMMENT_IMAGES; ?>_no"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
            <?php echo config_explanation(
                __('If you have problems with spam images, turn this off')
            ); ?>
        </td>
    </tr>
    <tr>
        <td><label for="highlight_default_lang"><?php echo __('Default code language'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_LANGUAGE; ?>" id="highlight_default_lang" style="width: 300px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                <?php foreach ($geshi_languages as $lang): ?>
                    <option value=<?php echo $lang; if (\thebuggenie\core\framework\Settings::getDefaultSyntaxHighlightingLanguage() == $lang): ?> selected<?php endif; ?>><?php echo $lang; ?></option>
                <?php endforeach; ?>
            </select>
            <?php echo config_explanation(
                __('Default language to highlight code samples with, if none is specified')
            ); ?>
        </td>
    </tr>
    <tr>
        <td><label for="highlight_default_numbering"><?php echo __('Default numbering mode'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_NUMBERING; ?>" id="highlight_default_numbering" style="width: 300px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                <option value=<?php echo \thebuggenie\core\framework\Settings::SYNTAX_HIHGLIGHTING_FANCY_NUMBERS; if (\thebuggenie\core\framework\Settings::getDefaultSyntaxHighlightingNumbering() == \thebuggenie\core\framework\Settings::SYNTAX_HIHGLIGHTING_FANCY_NUMBERS): ?> selected<?php endif; ?>><?php echo __('Fancy numbering, with highlighted lines'); ?></option>
                <option value=<?php echo \thebuggenie\core\framework\Settings::SYNTAX_HIHGLIGHTING_NORMAL_NUMBERS; if (\thebuggenie\core\framework\Settings::getDefaultSyntaxHighlightingNumbering() == \thebuggenie\core\framework\Settings::SYNTAX_HIHGLIGHTING_NORMAL_NUMBERS): ?> selected<?php endif; ?>><?php echo __('Normal numbering'); ?></option>
                <option value=<?php echo \thebuggenie\core\framework\Settings::SYNTAX_HIHGLIGHTING_NO_NUMBERS; if (\thebuggenie\core\framework\Settings::getDefaultSyntaxHighlightingNumbering() == \thebuggenie\core\framework\Settings::SYNTAX_HIHGLIGHTING_NO_NUMBERS): ?> selected<?php endif; ?>><?php echo __('No numbering'); ?></option>
            </select>
            <?php echo config_explanation(
                __('Choose how code samples should be numbered, if not otherwise specified')
            ); ?>
        </td>
    </tr>
    <tr>
        <td><label for="highlight_default_interval"><?php echo __('Default line highlight interval'); ?></label></td>
        <td>
            <input type="text" name="<?php echo \thebuggenie\core\framework\Settings::SETTING_SYNTAX_HIGHLIGHT_DEFAULT_INTERVAL; ?>" style="width: 50px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="highlight_default_interval" value="<?php echo (\thebuggenie\core\framework\Settings::get('highlight_default_interval')); ?>" />
            <?php echo config_explanation(
                __('When using fancy numbering, you can have a line highlighted at a regular interval. Set the default interval to use here, if not otherwise specified')
            ); ?>
        </td>
    </tr>
    <tr>
        <td><label for="notification_poll_interval"><?php echo __('Notification poll interval'); ?></label></td>
        <td>
            <input type="text" name="<?php echo \thebuggenie\core\framework\Settings::SETTING_NOTIFICATION_POLL_INTERVAL; ?>" style="width: 50px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="notification_poll_interval" value="<?php echo (\thebuggenie\core\framework\Settings::getNotificationPollInterval()); ?>" />
            <?php echo config_explanation(
                __('Polling is used to check for new user notifications. Set the default polling interval in seconds, or 0 to disable polling.')
            ); ?>
        </td>
    </tr>
</table>
