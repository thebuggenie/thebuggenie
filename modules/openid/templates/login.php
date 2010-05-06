<style>
	#openid{
		border: 1px solid #CCC;
		display: inline;
	}
	#openid, #openid INPUT{
		font-family: "Trebuchet MS";
		font-size: 12px;
	}
	#openid LEGEND{
		1.2em;
		font-weight: bold;
		color: #FF6200;
		padding-left: 5px;
		padding-right: 5px;
	}
	#openid INPUT.openid_login{
		background: #FFFFFF url('<?php echo TBGContext::getURLhost() . TBGContext::getTBGPath() . 'themes/' . TBGSettings::getThemeName() . '/'; ?>openid-icon-small.gif') no-repeat scroll;
		padding: 5px;
		padding-left: 23px;
		background-color: #fff;
		background-position: 3px 50%;
		color: #000;
		width: 220px;
		font-size: 13px;
		margin-right: 10px;
		border: 1px solid #E5E5E5;
	}
	#openid A{
	color: silver;
	}
	#openid A:hover{
		color: #5e5e5e;
	}
</style>
<?php if ($error !== null): ?>
	<div class="rounded_box red borderless" style="margin: 5px auto 5px auto; width: 700px; vertical-align: middle; padding: 5px; font-weight: bold; font-size: 13px;">
		<div class="viewissue_info_header"><?php echo __('An error occurred when trying to connect to the OpenID provider'); ?></div>
		<div class="viewissue_info_content"><?php echo $error_description; ?></div>
	</div>
<?php endif; ?>
<div>
	<fieldset id="openid">
		<legend>OpenID Login</legend>
		<form action="<?php echo make_url('openid_login'); ?>" method="post">
			<input type="hidden" name="openid_action" value="login">
			<div><input type="text" name="openid_url" class="openid_login"><input type="submit" name="login" value="login &gt;&gt;"></div>
			<div><a href="http://www.myopenid.com/" class="link" >Get an OpenID</a></div>
		</form>
	</fieldset>
</div>