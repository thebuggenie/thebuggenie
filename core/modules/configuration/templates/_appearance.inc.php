<table style="clear: both; width: 700px; margin-top: 5px;" class="padded_table" cellpadding=0 cellspacing=0>
    <tr>
        <td><label for="theme_name"><?php echo __('Selected theme'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_THEME_NAME; ?>" id="theme_name" style="width: 300px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
            <?php foreach ($themes as $aTheme): ?>
                <option value="<?php echo $aTheme; ?>"<?php if (\thebuggenie\core\framework\Settings::getThemeName() == $aTheme): ?> selected<?php endif; ?><?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>><?php echo $aTheme; ?></option>
            <?php endforeach; ?>
            </select>
            <?php echo config_explanation(
                __('Themes provide the look and feel of The Bug Genie, other than the icons. Therefore, changing the theme will change the colours, fonts and layout of your installation')
            ); ?>
        </td>
    </tr>
    <tr>
        <td><label for="theme_name"><?php echo __('Selected iconset'); ?></label></td>
        <td>
            <select name="<?php echo \thebuggenie\core\framework\Settings::SETTING_ICONSET; ?>" id="iconset" style="width: 300px;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
            <?php foreach ($icons as $anIcon): ?>
                <option value="<?php echo $anIcon; ?>"<?php if (\thebuggenie\core\framework\Settings::getIconsetName() == $anIcon): ?> selected<?php endif; ?><?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>><?php echo $anIcon; ?></option>
            <?php endforeach; ?>
            </select>
            <?php echo config_explanation(
                __('An iconset contains all the icons used in The Bug Genie. You can change the icons to be used using this option')
            ); ?>
        </td>
    </tr>
    <tr>
        <td>
            <label><?php echo __('Custom header and favicons'); ?></label>
        </td>
        <td>
            <div class="button button-blue" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'site_icons')); ?>');"><span><?php echo __('Configure icons'); ?></span></div>
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
</table>
