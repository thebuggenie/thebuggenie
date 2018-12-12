<p><?= __('To continue, enter a github repository name, github url or git clone url below.'); ?></p>
<input type="hidden" name="setup-step" value="1">
<div class="address-container">
    <input type="text" name="repository_url" placeholder="<?= __('Enter a github url, project name or clone url here'); ?>">
</div>
<input type="submit" id="livelink_form_button" class="button" value="<?= __('Next'); ?>">
<span id="livelink_form_indicator" style="display: none;"><?= image_tag('spinning_20.gif'); ?></span>
