<div id="attach_file" style="display: none;">
	<div class="rounded_box white borderless">
		<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
		<div class="xboxcontent">
			<div class="header_div bigger">
				<?php if ($mode == 'issue'): ?>
					<?php echo __('Attach one or more file(s) to this issue'); ?>
				<?php endif; ?>
			</div>
			<div id="upload_forms">
				<form method="post" action="<?php echo $form_action ?>" enctype="multipart/form-data" id="uploader_upload_form" style="margin: 10px 0 0 5px; display: none;">
					<input type="hidden" name ="MAX_FILE_SIZE" value="<?php echo TBGSettings::getUploadsMaxSize(true); ?>">
					<input type="hidden" name="APC_UPLOAD_PROGRESS" value="" />
					<div>
						<dl>
							<dt style="width: 120px;"><label for="uploader_file"><?php echo __('Select a file'); ?></label></dt>
							<dd style="margin-bottom: 3px;"><input type="file" name="uploader_file" id="uploader_file"></dd>
							<dd><?php echo __('Files bigger than %max_filesize% can not be attached. Please check that the file you are attaching is not bigger than this.', array('%max_filesize%' => '<b>'.TBGSettings::getUploadsMaxSize().'MB</b>')); ?></dd>
							<dt style="width: 120px;"><label for="upload_file_description"><?php echo __('Describe the file'); ?></label></dt>
							<dd style="margin-bottom: 3px;"><input type="text" name="uploader_file_description" id="upload_file_description" style="width: 340px;"></dd>
							<dd class="faded_medium"><?php echo __('Enter a few words about the file, so people can understand what it is/does'); ?></dd>
							<?php if ($mode == 'issue'): ?>
								<dt style="width: 120px;"><label for="upload_file_comment"><?php echo __('Comment'); ?></label> (<?php echo __('optional'); ?>)</dt><br>
								<dd style="margin-bottom: 3px;"><textarea name="comment" cols="70" rows="3" id="upload_file_comment" style="width: 460px; height: 50px;"></textarea></dd>
								<dd class="faded_medium" style="width: 440px;"><?php echo __('If you want to add a comment with the file, enter the comment here, and it will automatically be added to the issue with the file'); ?></dd>
							<?php endif; ?>
						</dl>
						<div style="text-align: center; margin-top: 0;">
							<p style="margin-bottom: 5px;"><?php echo __('Press the %upload_and_attach% button to upload and attach the file', array('%upload_and_attach%' => '<i>'.__('Upload and attach').'</i>')); ?></p>
							<input type="submit" name="submit" value="<?php echo __('Upload and attach'); ?>" style="font-weight: bold; font-size: 13px;">
						</div>
					</div>
				</form>
			</div>
			<div id="uploader_upload_indicators">
				<div id="uploader_upload_indicator" style="display: none;">
					<?php echo image_tag('spinning_32.gif', array('style' => 'float: left;')); ?>&nbsp;<div style="float: left; font-size: 13px; padding: 1px;"><?php echo __('Uploading file, please wait'); ?>...</div>
				</div>
			</div>
			<br style="clear: both;">
			<div class="header_div"><?php echo __('Files already attached'); ?></div>
			<div id="uploaded_files_container">
				<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0>
					<tbody id="uploaded_files">
						<?php if ($mode == 'issue'): ?>
							<?php foreach ($existing_files as $file_id => $file): ?>
								<?php include_template('attachedfile', array('base_id' => 'uploaded_files', 'mode' => 'issue', 'issue' => $issue, 'file' => $file, 'file_id' => $file_id)); ?>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
			<div class="faded_medium" id="uploader_no_uploaded_files"<?php if (count($existing_files) > 0): ?> style="display: none;"<?php endif; ?>><?php echo __("You haven't uploaded any files right now (not including already attached files)"); ?></div>
			<div id="done_div">
				<?php echo __('Click %done% when you have uploaded the files you want to attach', array('%done%' => '<a href="javascript:void(0)" onclick="$(\'attach_file\').fade({ duration: 0.5 });"><b>'.__('Done').'</b></a>')); ?>
			</div>
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
	<div style="background-color: #000; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; z-index: 100000;" class="semi_transparent"> </div>
</div>
<script type="text/javascript">

	var FileUploader = Class.create({

		ID_KEY         : 'APC_UPLOAD_PROGRESS',
		pollerUrl      : '<?php echo $poller_url; ?>',
		poller         : null,
		error          : false,
		form           : null, // HTML form element
		status         : null, // element where the upload status is displayed
		idElement      : null, // element that holds the APC upload ID
		iframe         : null, // iframe we create that form will submit to

		initialize : function()
		{
			// initialize the form and observe the submit element
			this.form = $('uploader_upload_form').clone(true);
			this.form.id = this.form.id+this.generateId();
			$('upload_forms').appendChild(this.form);
			this.form.show();
			this.form.observe('submit', this._onFormSubmit.bindAsEventListener(this));

			// create a hidden iframe
			this.iframe = new Element('iframe', { name : '_upload_frame_'+this.generateId() }).hide();

			// make the form submit to the hidden iframe
			this.form.appendChild(this.iframe);
			this.form.target = this.iframe.name;

			// initialize the APC ID element so we can write a value to it later
			this.idElement = this.form.getInputs(null, this.ID_KEY)[0];

			// initialize the status container
			this.status = $('uploader_upload_indicator').clone(true);
			$('uploader_upload_indicators').appendChild(this.status);

		},

		generateId : function()
		{
			var now = new Date();
			return now.getTime();
		},

		delay : function(seconds)
		{
			var ms   = seconds * 1000;
			var then = new Date().getTime();
			var now  = then;

			while ((now - then) < ms)
				now = new Date().getTime();
		},

		_onFormSubmit : function(e)
		{
			//this.form.hide();
			var id = this.generateId();
			this.form.action = this.form.action+'&upload_id='+id;
			this.status.show();

			this.idElement.value = id;
			this.poller = new PeriodicalExecuter(this._monitorUpload.bind(this), 2);
			this._monitorUpload();
			Event.stopObserving(this.form, 'submit');
			if (!this.error)
			{
				new FileUploader();
			}
		},

		_monitorUpload : function()
		{
			var options = {
				parameters : 'upload_id=' + this.idElement.value,
				onLoading  : this._onMonitorLoading.bind(this),
				onSuccess  : this._onMonitorSuccess.bind(this),
				onFailure  : this._onMonitorFailure.bind(this)
			};

			new Ajax.Request(this.pollerUrl+'&upload_id='+this.idElement.value, options);
		},

		_onMonitorLoading : function()
		{
			this.form.hide();
		},

		_onMonitorSuccess : function(transport)
		{
			var json = transport.responseJSON;

			if (json.finished)
			{
				this.poller.stop();
				if (json.file_id)
				{
					this.status.remove();
					this.form.remove();
					$('uploader_no_uploaded_files').hide();
					$('uploaded_files').insert({bottom: json.content_uploader});
					<?php if ($mode == 'issue'): ?>
						$('viewissue_no_uploaded_files').hide();
						$('viewissue_uploaded_files').insert({bottom: json.content_inline});
					<?php endif; ?>
					this.error = false;
				}
				else if (json.error)
				{
					this.form.observe('submit', this._onFormSubmit.bindAsEventListener(this));
					this.status.hide();
					this.form.show();
					this.error = true;
					failedMessage(json.error);
				}
			}
		},

		_onMonitorFailure : function(transport)
		{
			var json = transport.responseJSON;

			this.status.hide();
			this.form.show();
			this.poller.stop();
			if (json && (json.failed || json.error))
			{
				failedMessage(json.error);
			}
			else
			{
				failedMessage(transport.responseText);
			}
		}

	});

	Event.observe(window, 'load', function() {
		new FileUploader();
	});

</script>