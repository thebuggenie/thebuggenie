<!DOCTYPE html>
<html>
    <head>
        <style>
            @import url('https://fonts.googleapis.com/css?family=Fira+Mono:400,500,700|Source+Sans+Pro:400,400i,600,600i&subset=cyrillic,cyrillic-ext,latin-ext');

            body, td, th { padding: 0px; margin: 0px; background-color: #FFF; font-family: 'Source Sans Pro', 'Open Sans', sans-serif; font-style: normal; font-weight: normal; text-align: left; font-size: 13px; line-height: 1.3; color: #222;}
            h1 {
                margin: 0 10px;
                font-size: 21px;
                padding: 10px 0;
                background: transparent;
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                border-top-left-radius: 2px;
                border-top-right-radius: 2px;
                font-weight: normal;
                text-transform: uppercase;
                vertical-align: middle;
            }
            h1 img {
                display: inline-block;
                margin-right: 10px;
                vertical-align: middle;
                height: 24px;
            }
            h1 span {
                vertical-align: middle;
            }
            b, strong { font-weight: 600; }
            h2 { margin: 0 0 15px 0; font-size: 16px; }
            h3 { margin: 15px 0 0 0; font-size: 14px; }
            input[type="text"], input[type="password"] { float: left; margin-right: 15px; }
            label { float: left; font-weight: 600; margin-right: 5px; display: block; width: 150px; }
            label span { font-weight: normal; color: #888; }
            .rounded_box {
                background: transparent;
                margin: 0;
                border-radius: 2px;
                border: 1px solid rgba(0, 0, 0, 0.1);
                box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
                padding: 0;
            }
            .rounded_box h4 { margin-bottom: 0px; margin-top: 7px; font-size: 14px; }
            .error_content { padding: 10px; }
            .description { padding: 3px 3px 3px 0;}
            pre { overflow: scroll; padding: 5px; }
            .command_box { border: 1px dashed #DDD; background-color: #F5F5F5; padding: 4px; font-family: 'Fira Mono', monospace; display: inline-block; margin-top: 5px; margin-bottom: 15px; }
        </style>
        <!--[if IE]>
        <style>
            body { background-color: #DFDFDF; font-family: sans-serif; font-size: 13px; }
        </style>
        <![endif]-->
    </head>
    <body>
        <div class="rounded_box" style="margin: 30px auto 0 auto; width: 700px;">
            <h1><img src="/images/logo_48.png"><span>External libraries not initialized</span></h1>
            <div class="error_content">
                <h2>Error loading external libraries</h2>
                <p>
                    The Bug Genie uses the <a href="http://getcomposer.org">composer</a> dependency management tool to control external libraries.<br>
                    Before you can use or install The Bug Genie, you must use composer to initialize the vendor libraries.<br>
                    <br>
                    If you have already downloaded and installed composer, you can do this by running the following command from the directory containing The Bug Genie:<br>
                    <div class="command_box">php /path/to/composer.phar install</div><br>
                    When the command completes. refresh this page and continue.<br>
                    <br>
                    You can read more about composer &ndash; including how to install it &ndash; on <a href="http://getcomposer.org">http://getcomposer.org</a>
                </p>
            </div>
        </div>
    </body>
</html>
