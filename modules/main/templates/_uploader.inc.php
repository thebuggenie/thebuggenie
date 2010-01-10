<div id="upload_forms">
	<form method="post" action="<?php echo make_url('issue_upload', array('issue_id' => $issue->getID())); ?>" enctype="multipart/form-data" id="issue_upload_form" style="margin: 10px 0 0 5px; display: none;">
		<input type="hidden" name ="MAX_FILE_SIZE" value="200000000">
		<input type="hidden" name="APC_UPLOAD_PROGRESS" value="" />
		<div>
			<dl>
				<dt style="width: 120px;"><label for="issue_file"><?php echo __('Select a file'); ?></label></dt>
				<dd style="margin-bottom: 3px;"><input type="file" name="issue_file" id="issue_file"></dd>
				<dt style="width: 120px;"><label for="upload_issue_file_description"><?php echo __('Describe the file'); ?></label></dt>
				<dd style="margin-bottom: 3px;"><input type="text" name="issue_file_description" id="upload_issue_file_description" style="width: 340px;"></dd>
				<dd class="faded_medium"><?php echo __('Enter a few words about the file, so people can understand what it is/does'); ?></dd>
				<dt style="width: 120px;"><label for="upload_issue_file_comment"><?php echo __('Comment'); ?></label> (<?php echo __('optional'); ?>)</dt><br>
				<dd style="margin-bottom: 3px;"><textarea name="comment" cols="70" rows="3" id="upload_issue_file_comment" style="width: 460px; height: 50px;"></textarea></dd>
				<dd class="faded_medium" style="width: 440px;"><?php echo __('If you want to add a comment with the file, enter the comment here, and it will automatically be added to the issue with the file'); ?></dd>
			</dl>
			<div style="text-align: center; margin-top: 0;">
				<p style="margin-bottom: 5px;"><?php echo __('Press the %upload_and_attach% button to attach the file to this issue', array('%upload_and_attach%' => '<i>'.__('Upload and attach').'</i>')); ?></p>
				<input type="submit" name="submit" value="<?php echo __('Upload and attach'); ?>" style="font-weight: bold; font-size: 13px;">
			</div>
		</div>
	</form>
</div>
<div id="issue_upload_indicators">
	<div id="issue_upload_indicator" style="display: none;">
		<?php echo image_tag('spinning_32.gif', array('style' => 'float: left;')); ?>&nbsp;<div style="float: left; font-size: 13px; padding: 1px;"><?php echo __('Uploading file, please wait'); ?>...</div>
	</div>
</div>
<br style="clear: both;">
<script type="text/javascript">

	var FileUploader = Class.create({

		ID_KEY         : 'APC_UPLOAD_PROGRESS',
		statusUrl      : '<?php echo make_url('issue_upload_status'); ?>',
		poller         : null,
		error          : false,
		form           : null, // HTML form element
		status         : null, // element where the upload status is displayed
		idElement      : null, // element that holds the APC upload ID
		iframe         : null, // iframe we create that form will submit to

		initialize : function()
		{
			// initialize the form and observe the submit element
			this.form = $('issue_upload_form').clone(true);
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
			this.status = $('issue_upload_indicator').clone(true);
			$('issue_upload_indicators').appendChild(this.status);

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

			new Ajax.Request(this.statusUrl+'&upload_id='+this.idElement.value, options);
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
					$('issue_no_uploaded_files').hide();
					$('viewissue_no_uploaded_files').hide();
					$('uploaded_files').insert({bottom: json.content});
					$('viewissue_uploaded_files').insert({bottom: json.content});
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
			if (json && json.failed)
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