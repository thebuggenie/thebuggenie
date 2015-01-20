<?php $apc_enabled = \thebuggenie\core\framework\Request::CanGetUploadStatus(); ?>
<div id="backdrop_detail_content" class="backdrop_detail_content">
    <div id="upload_forms">
        <form method="post" action="<?php echo $form_action; ?>" enctype="multipart/form-data" id="uploader_upload_form" style="margin: 10px 0 0 5px;<?php if ($apc_enabled): ?> display: none;<?php endif; ?>">
            <input type="hidden" name ="MAX_FILE_SIZE" value="<?php echo \thebuggenie\core\framework\Settings::getUploadsEffectiveMaxSize(true); ?>">
            <input type="hidden" name="APC_UPLOAD_PROGRESS" value="" />
            <div>
                <dl>
                    <dt style="width: 120px;"><label for="uploader_file"><?php echo __('Select a file'); ?></label></dt>
                    <dd style="margin-bottom: 3px;"><input type="file" name="uploader_file" id="uploader_file"></dd>
                    <?php $max_filesize = \thebuggenie\core\framework\Settings::getUploadsEffectiveMaxSize();
                    if($max_filesize != 0):?>
                    <dt style="width: 100%;"><?php echo __('Files bigger than %max_filesize can not be attached. Please check that the file you are attaching is not bigger than this.', array('%max_filesize' => '<b>'.$max_filesize.'MB</b>')); ?></dt>
                    <?php endif; ?>
                    <dt style="width: 120px;"><label for="upload_file_description"><?php echo __('Describe the file'); ?></label></dt>
                    <dd style="margin-bottom: 3px;"><input type="text" name="uploader_file_description" id="upload_file_description" style="width: 440px;"
                    placeholder="<?php echo __('Describe the file, so people understand what it is/does'); ?>"></dd>
                </dl>
                <?php if ($mode == 'issue'): ?>
                    <br style="clear:both" />
                    <label for="upload_file_comment"><?php echo __('Comment'); ?></label> (<?php echo __('optional'); ?>)<br>
                    <textarea name="comment" cols="70" rows="10" id="upload_file_comment" class="markuppable" style="width: 560px; height: 150px;"
                    placeholder="<?php echo __('Comments entered here will be added to the issue with the file.'); ?>"></textarea></dd>
                <?php endif; ?>
            </div>
            <div style="text-align: center; clear: both;" id="upload_and_attach">
                <p style="margin-bottom: 5px;"><?php echo __('Press the %upload_and_attach button to upload and attach the file', array('%upload_and_attach' => '<i>'.__('Upload and attach').'</i>')); ?></p>
                <input type="submit" name="submit" value="<?php echo __('Upload and attach'); ?>" style="font-weight: bold; font-size: 13px;">
            </div>
        </form>
    </div>
    <div id="uploader_upload_indicators">
        <div id="uploader_upload_indicator" style="display: none;">
            <?php echo image_tag('spinning_32.gif', array('style' => 'float: left;')); ?>&nbsp;<div style="float: left; font-size: 13px; padding: 1px;"><?php echo __('Uploading file, please wait'); ?>...</div>
        </div>
    </div>
    <div class="header_div" style="clear: both;"><?php echo __('Files already attached'); ?></div>
    <div id="uploaded_files_container">
        <table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0>
            <tbody id="uploaded_files">
                <?php if ($mode == 'issue'): ?>
                    <?php foreach ($existing_files as $file_id => $file): ?>
                        <?php include_component('main/attachedfile', array('base_id' => 'uploaded_files', 'mode' => 'issue', 'issue' => $issue, 'file' => $file, 'file_id' => $file_id)); ?>
                    <?php endforeach; ?>
                <?php elseif ($mode == 'article'): ?>
                    <?php foreach ($existing_files as $file_id => $file): ?>
                        <?php include_component('main/attachedfile', array('base_id' => 'uploaded_files', 'mode' => 'article', 'article' => $article, 'file' => $file, 'file_id' => $file_id)); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="faded_out" id="uploader_no_uploaded_files"<?php if (count($existing_files) > 0): ?> style="display: none;"<?php endif; ?>><?php echo __("You haven't uploaded any files right now (not including already attached files)"); ?></div>
    <div id="done_div">
        <?php echo __('Click %done when you have uploaded the files you want to attach', array('%done' => '<a href="javascript:void(0)" onclick="$(\'attach_file\').hide();"><b>'.__('Done').'</b></a>')); ?>
    </div>
    <?php if ($apc_enabled): ?>
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
                    this.form.hide();
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
                        evalScripts: true,
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
                                $('viewissue_uploaded_attachments_count').update(json.attachmentcount);
                            <?php elseif ($mode == 'article'): ?>
                                $('article_<?php echo mb_strtolower($article->getName()); ?>_no_files').hide();
                                $('article_<?php echo mb_strtolower($article->getName()); ?>_files').insert({bottom: json.content_inline});
                            <?php endif; ?>
                            this.error = false;
                            TBG.Main.Helpers.Message.success('File attached successfully');
                        }
                        else if (json.error)
                        {
                            this.form.observe('submit', this._onFormSubmit.bindAsEventListener(this));
                            this.status.hide();
                            this.form.hide();
                            this.error = true;
                            TBG.Main.Helpers.Message.error(json.error);
                        }
                    }
                },

                _onMonitorFailure : function(transport)
                {
                    var json = transport.responseJSON;

                    this.status.hide();
                    this.form.hide();
                    this.poller.stop();
                    if (json && (json.failed || json.error))
                    {
                        TBG.Main.Helpers.Message.error(json.error);
                    }
                    else
                    {
                        TBG.Main.Helpers.Message.error(transport.responseText);
                    }
                }

            });

            Event.observe(window, 'load', function() {
                new FileUploader();
            });

        </script>
    <?php endif; ?>
</div>
<div class="backdrop_detail_footer">
    <a href="javascript:void(0)" onclick="$('attach_file').hide();"><?php echo __('Close'); ?></a>
</div>
