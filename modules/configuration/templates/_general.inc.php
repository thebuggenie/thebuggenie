<table style="clear: both; width: 700px; margin-top: 5px;" class="padded_table" cellpadding=0 cellspacing=0>
	<tr>
		<td style="width: 200px;"><label for="b2_name"><?php echo __('The Bug Genie custom name'); ?></label></td>
		<td style="width: auto;"><input type="text" name="b2_name" id="b2_name" value="<?php echo str_replace('"', '&quot;', TBGSettings::getTBGname()); ?>" style="width: 100%;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>></td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('This is the name appearing in the headers and several other places, usually displaying "The Bug Genie"'); ?> <i>(<?php echo __('HTML allowed'); ?>)</i></td>
	</tr>
	<tr>
		<td><label for="b2_tagline"><?php echo __('Tagline / slogan'); ?></label></td>
		<td><input type="text" name="b2_tagline" id="b2_tagline" value="<?php echo str_replace('"', '&quot;', TBGSettings::getTBGtagline()); ?>" style="width: 100%;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>></td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('This will appear beneath the name in the header on all pages'); ?> <i>(<?php echo __('HTML allowed'); ?>)</i></td>
	</tr>
	<tr>
		<td><label for="singleprojecttracker"><?php echo __('Single project tracker mode'); ?></label></td>
		<td>
			<select name="singleprojecttracker" id="singleprojecttracker" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (TBGSettings::isSingleProjectTracker()): ?> selected<?php endif; ?>><?php echo __('Yes, behave as tracker for a single project'); ?></option>
				<option value=0<?php if (!TBGSettings::isSingleProjectTracker()): ?> selected<?php endif; ?>><?php echo __('No, use regular index page'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2">
			<?php echo __('In single project tracker mode, The Bug Genie will display the homepage for the first project as the main page instead of the regular index page'); ?><br>
			<?php if (count(TBGProject::getAll()) > 1): ?>
				<br>
				<b class="more_than_one_project_warning"><?php echo __('More than one project exists. When in "single project" mode, accessing other projects than the first will become harder.'); ?></b>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td><label for="showprojectsoverview"><?php echo __('Show projects overview'); ?></label></td>
		<td>
			<select name="showprojectsoverview" id="showprojectsoverview" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (TBGSettings::isProjectOverviewEnabled()): ?> selected<?php endif; ?>><?php echo __('Yes, show on the frontpage'); ?></option>
				<option value=0<?php if (!TBGSettings::isProjectOverviewEnabled()): ?> selected<?php endif; ?>><?php echo __('No, don\'t show'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('Whether the project overview list should appear on the frontpage or not'); ?></td>
	</tr>
	<tr>
		<td><label for="cleancomments"><?php echo __('Comment trail'); ?></label></td>
		<td>
			<select name="cleancomments" id="cleancomments" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (TBGSettings::isCommentTrailClean()): ?> selected<?php endif; ?>><?php echo __("Don't post system comments when an issue is updated"); ?></option>
				<option value=0<?php if (!TBGSettings::isCommentTrailClean()): ?> selected<?php endif; ?>><?php echo __('Always post comments when an issue is updated'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('To keep the comment trail clean in issues, you can select not to post system comments when an issue is updated.'); ?><br>(<?php echo __('The issue log will always be updated regardless of this setting.'); ?>)</td>
	</tr>
	<tr>
		<td><label for="previewcommentimages"><?php echo __('Preview images in comments'); ?></label></td>
		<td>
			<select name="previewcommentimages" id="previewcommentimages" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=0<?php if (!TBGSettings::isCommentImagePreviewEnabled()): ?> selected<?php endif; ?>><?php echo __("Don't show image previews of attached images in comments"); ?></option>
				<option value=1<?php if (TBGSettings::isCommentImagePreviewEnabled()): ?> selected<?php endif; ?>><?php echo __('Show image previews of attached images in comments'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('If you have problems with spam images, turn this off'); ?></td>
	</tr>
	<?php
		// Generate list of languages
		$files = scandir(THEBUGGENIE_PATH.'core/geshi/geshi/');
	?>
	<tr>
		<td><label for="highlight_default_lang"><?php echo __('Default code language'); ?></label></td>
		<td>
			<select name="highlight_default_lang" id="highlight_default_lang" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<?php
					foreach ($files as $lang)
					{
						if (strstr($lang, '.php') === false)
						{
							continue;
						}
						else
						{
							$file = str_replace('.php', '', $lang);
							?>
							<option value=<?php echo $file; if (TBGSettings::get('highlight_default_lang') == $file): ?> selected<?php endif; ?>><?php echo $file; ?></option>
							<?php
						}
					}
				?>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('Default language to highlight code samples with, if none is specified'); ?></td>
	</tr>
	<tr>
		<td><label for="highlight_default_numbering"><?php echo __('Default numbering mode'); ?></label></td>
		<td>
			<select name="highlight_default_numbering" id="highlight_default_numbering" style="width: 300px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
				<option value=1<?php if (TBGSettings::get('highlight_default_numbering') == '1'): ?> selected<?php endif; ?>><?php echo __('Fancy numbering, with highlighted lines'); ?></option>
				<option value=2<?php if (TBGSettings::get('highlight_default_numbering') == '2'): ?> selected<?php endif; ?>><?php echo __('Normal numbering'); ?></option>
				<option value=3<?php if (TBGSettings::get('highlight_default_numbering') == '3'): ?> selected<?php endif; ?>><?php echo __('No numbering'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('Choose how code samples should be numbered, if not otherwise specified'); ?></td>
	</tr>
	<tr>
		<td><label for="highlight_default_interval"><?php echo __('Default line highlight interval'); ?></label></td>
		<td>
			<input type="text" style="width: 50px;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?> name="highlight_default_interval" id="highlight_default_interval" value="<?php echo (TBGSettings::get('highlight_default_interval')); ?>" />
		</td>
	</tr>
	<tr>
		<td class="config_explanation" colspan="2"><?php echo __('When using fancy numbering, you can have a line highlighted at a regular interval. Set the default interval to use here, if not otherwise specified'); ?></td>
	</tr>
</table>
