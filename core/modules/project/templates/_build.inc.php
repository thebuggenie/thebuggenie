<div class="backdrop_box large">
    <div class="backdrop_detail_header">
        <?php echo ($build->getId()) ? __('Edit release details') : __('Add new release'); ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_projects_build', array('project_id' => $project->getID())); ?>" method="post" id="build_form" onsubmit="$('add_release_indicator').show();return true;" enctype="multipart/form-data">
            <table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
                <tr>
                    <td style="width: 200px;"><label for="build_name"><?php echo __('Release name:'); ?></label></td>
                    <td style="width: 580px;"><input type="text" name="build_name" id="build_name" style="width: 570px;" value="<?php echo $build->getName(); ?>"></td>
                </tr>
                <tr>
                    <td><label for="ver_mj"><?php echo __('Version: %version_number', array('%version_number' => '')); ?></label></td>
                    <td><input type="text" name="ver_mj" id="ver_mj" style="width: 20px; font-size: 0.9em; text-align: center;" value="<?php echo $build->getVersionMajor(); ?>">&nbsp;.&nbsp;<input type="text" name="ver_mn" id="ver_mn" style="width: 20px; font-size: 0.9em; text-align: center;" value="<?php echo $build->getVersionMinor(); ?>">&nbsp;.&nbsp;<input type="text" name="ver_rev" id="ver_rev" style="width: 20px; font-size: 0.9em; text-align: center;" value="<?php echo $build->getVersionRevision(); ?>"></td>
                </tr>
                <tr>
                    <td><label for="is_released_yes"><?php echo __('Released'); ?></label></td>
                    <td>
                        <input type="radio" name="isreleased" id="is_released_yes" value="1"<?php if ($build->isReleased()) echo ' checked'; ?>><label for="is_released_yes" style="font-weight: normal;"><?php echo __('Yes'); ?></label>
                        <input type="radio" name="isreleased" id="is_released_no" value="0"<?php if (!$build->isReleased()) echo ' checked'; ?>><label for="is_released_no" style="font-weight: normal;"><?php echo __('No'); ?></label>
                    </td>
                </tr>
                <tr>
                    <td><label for="locked_no"><?php echo __('Status'); ?></label></td>
                    <td>
                        <input type="radio" name="locked" id="locked_no" value="0"<?php if (!$build->isLocked()) echo ' checked'; ?>><label for="locked_no" style="font-weight: normal;"><?php echo __('Active'); ?></label>
                        <input type="radio" name="locked" id="locked_yes" value="1"<?php if ($build->isLocked()) echo ' checked'; ?>><label for="locked_yes" style="font-weight: normal;"><?php echo __('Archived'); ?></label>
                    </td>
                </tr>
                <tr>
                    <td><label for="has_release_date"><?php echo __('Release date'); ?></label></td>
                    <td style="padding: 2px;">
                        <select name="has_release_date" id="has_release_date" style="width: 70px;" onchange="var val = $(this).getValue(); ['day', 'month', 'year', 'hour', 'minute'].each(function(item) { (val == 1) ? $('release_'+item).enable() : $('release_'+item).disable(); });">
                            <option value=1<?php if ($build->hasReleaseDate()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
                            <option value=0<?php if (!$build->hasReleaseDate()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
                        </select>
                        <script type="text/javascript">
                            require(['domReady', 'jquery'], function (domReady, jQuery) {
                                domReady(function () {
                                    jQuery('#has_release_date').on('change', function (ev) {
                                        if (this.value == 0) return false;

                                        if (jQuery('#release_month').val() == 1
                                            && jQuery('#release_day').val() == 1
                                            && jQuery('#release_year').val() == 1990) {
                                            var d = new Date();

                                            jQuery('#release_month').val(d.getMonth() + 1);
                                            jQuery('#release_day').val(d.getDate());
                                            jQuery('#release_year').val(d.getFullYear());
                                        }
                                    });
                                });
                            });
                        </script>
                        <select style="width: 85px;" name="release_month" id="release_month"<?php if (!$build->hasReleaseDate()): ?> disabled<?php endif; ?>>
                        <?php for($cc = 1;$cc <= 12;$cc++): ?>
                            <option value=<?php print $cc; ?><?php print (($build->getReleaseDateMonth() == $cc) ? " selected" : "") ?>><?php echo strftime('%B', mktime(0, 0, 0, $cc, 1)); ?></option>
                        <?php endfor; ?>
                        </select>
                        <select style="width: 40px;" name="release_day" id="release_day"<?php if (!$build->hasReleaseDate()): ?> disabled<?php endif; ?>>
                        <?php for($cc = 1;$cc <= 31;$cc++): ?>
                            <option value=<?php print $cc; ?><?php echo (($build->getReleaseDateDay() == $cc) ? " selected" : "") ?>><?php echo $cc; ?></option>
                        <?php endfor; ?>
                        </select>
                        <select style="width: 55px;" name="release_year" id="release_year"<?php if (!$build->hasReleaseDate()): ?> disabled<?php endif; ?>>
                        <?php for($cc = 1990;$cc <= (date("Y") + 10);$cc++): ?>
                            <option value=<?php print $cc; ?><?php echo (($build->getReleaseDateYear() == $cc) ? " selected" : "") ?>><?php echo $cc; ?></option>
                        <?php endfor; ?>
                        </select>
                        <b><?php echo __('%release_date_input - time: %time_input', array('%release_date_input' => '', '%time_input' => '')); ?></b>
                        <input type="text" id="release_hour" name="release_hour" style="width: 20px; font-size: 0.9em; text-align: center;" value="<?php echo $build->getReleaseDateHour(); ?>"<?php if (!$build->hasReleaseDate()): ?> disabled<?php endif; ?>>&nbsp;:&nbsp;
                        <input type="text" id="release_minute" name="release_minute" style="width: 20px; font-size: 0.9em; text-align: center;" value="<?php echo $build->getReleaseDateMinute(); ?>"<?php if (!$build->hasReleaseDate()): ?> disabled<?php endif; ?>>
                    </td>
                </tr>
                <tr>
                    <td><label for="build_milestone_dropdown"><?php echo __('Milestone release'); ?></label></td>
                    <td>
                        <select name="milestone" id="build_milestone_dropdown">
                            <option value="0"<?php if (!$build->getMilestone() instanceof \thebuggenie\core\entities\Milestone) echo ' selected'; ?>><?php echo __('This release is not related to a milestone'); ?></option>
                            <?php foreach ($project->getAvailableMilestones() as $milestone): ?>
                                <option value="<?php echo $milestone->getID(); ?>"<?php if ($build->getMilestone() instanceof \thebuggenie\core\entities\Milestone && $build->getMilestone()->getID() == $milestone->getID()) echo ' selected'; ?>><?php echo __('This is a release of milestone %milestone_name', array('%milestone_name' => $milestone->getName())); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <?php if ($build->getProject()->isEditionsEnabled() || $build->getEdition() instanceof \thebuggenie\core\entities\Edition): ?>
                    <tr>
                        <td><label for="build_edition_dropdown"><?php echo __('Edition release'); ?></label></td>
                        <td>
                            <select name="edition" id="build_edition_dropdown">
                                <option value="0"<?php if (!$build->getEdition() instanceof \thebuggenie\core\entities\Edition) echo ' selected'; ?>><?php echo __('This release is not related to a edition'); ?></option>
                                <?php foreach ($project->getEditions() as $edition): ?>
                                    <option value="<?php echo $edition->getID(); ?>"<?php if ($build->getEdition() instanceof \thebuggenie\core\entities\Edition && $build->getEdition()->getID() == $edition->getID()) echo ' selected'; ?>><?php echo __('This is a release of edition %edition_name', array('%edition_name' => $edition->getName())); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
            <div class="rounded_box lightgrey borderless" style="margin: 5px 0;">
                <table style="clear: both; width: 780px;" cellpadding=0 cellspacing=0>
                    <td style="width: 200px;"><label><?php echo __('Release download'); ?></label></td>
                    <td>
                        <ul class="simple_list" style="margin-top: 0;" id="edit_build_download_options">
                            <li><input type="radio" id="download_none" name="download" value="0"<?php if (!$build->hasDownload()) echo ' checked'; ?>><label for="download_none"><?php echo ($build->hasDownload()) ? __('Clear download information') : __('Leave as is %no_download', array('%no_download' => '<span class="faded_out">('.__('no download').')</span>')).'</span>'; ?></label></li>
                            <?php if ($build->hasFile()): ?>
                                <li><input type="radio" id="download_leave_file" name="download" value="leave_file" checked><label for="download_leave_file"><?php echo __('Use existing file %filename', array('%filename' => '<span class="faded_out" style="font-weight: normal;">('.$build->getFile()->getOriginalFilename().')</span>')); ?></label></li>
                            <?php endif; ?>
                            <?php if (\thebuggenie\core\framework\Settings::isUploadsEnabled()): ?>
                                <li><input type="radio" id="download_upload" name="download" value="upload_file"><label for="download_upload"><?php echo __('Upload file for download'); ?>:</label>&nbsp;<input type="file" name="upload_file"></li>
                            <?php else: ?>
                                <li class="faded_out"><input type="radio" disabled><label><?php echo __('Upload file for download'); ?></label>&nbsp;<?php echo __('File uploads are not enabled'); ?></li>
                            <?php endif; ?>
                            <li><input type="radio" id="download_url" name="download" value="url"<?php if ($build->hasFileURL()) echo ' checked'; ?>><label for="download_url"><?php echo __('Specify download URL'); ?>:</label>&nbsp;<input type="text" style="width: 300px;" name="file_url"></li>
                        </ul>
                    </td>
                </table>
            </div>
            <table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
                <tr>
                    <td colspan="2" style="padding: 10px 0 10px 10px; text-align: right;">
                        <div style="float: left; font-size: 13px; padding-top: 2px; font-style: italic;" class="config_explanation">
                            <?php if ($build->getId()): ?>
                                <?php echo __('When you are done, click "%update_release" to update the details for this release', array('%update_release' => __('Update release'))); ?>
                            <?php else: ?>
                                <?php echo __('When you are done, click "%add_release" to publish this release', array('%add_release' => __('Add release'))); ?>
                            <?php endif; ?>
                        </div>
                        <?php if ($build->getID()): ?>
                            <input type="hidden" name="build_id" value="<?php echo $build->getID(); ?>">
                        <?php endif; ?>
                        <?php if (!$build->getProject()->isEditionsEnabled() && !$build->getEdition() instanceof \thebuggenie\core\entities\Edition): ?>
                            <input type="hidden" name="edition" value="0">
                        <?php endif; ?>
                            <input class="button button-green" style="float: right;" type="submit" value="<?php echo ($build->getId()) ? __('Update release') : __('Add release'); ?>">
                        <span id="add_release_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div class="backdrop_detail_footer">
        <?php echo javascript_link_tag(__('Close popup'), array('onclick' => 'TBG.Main.Helpers.Backdrop.reset();')); ?>
    </div>
</div>
