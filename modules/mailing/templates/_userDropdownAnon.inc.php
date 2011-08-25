<?php if (TBGContext::getRouting()->getCurrentRouteName() != 'login_page'): ?>
												<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'login', 'section' => 'forgot')); ?>')"><?php echo image_tag('icon_forgot.png').__('Forgot password'); ?></a>
<?php endif; ?>