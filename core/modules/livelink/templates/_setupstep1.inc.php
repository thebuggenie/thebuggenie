<p><?= __('To continue, enter a github repository name, github url or git clone url below.'); ?></p>
<input type="hidden" name="setup-step" value="1">
<div class="address-container" id="livelink_address_container">
    <img class="verified" src="<?= image_url('icon_ok.png'); ?>">
    <input type="text" id="livelink_repository_url_input" name="repository_url" placeholder="<?= __('Enter a github url, project name or clone url here'); ?>">
</div>
<input type="submit" id="livelink_form_button" class="button" value="<?= __('Next'); ?>">
<a href="#" id="livelink_change_button" class="button button-silver change-button"><?= __('Change'); ?></a>
<span id="livelink_form_indicator" style="display: none;" class="indicator"><?= image_tag('spinning_20.gif'); ?></span>
