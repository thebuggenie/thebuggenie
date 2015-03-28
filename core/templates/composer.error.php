<!DOCTYPE html>
<html>
    <head>
        <style>
            @import url("http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700&subset=latin,cyrillic,greek");
            @import url("http://fonts.googleapis.com/css?family=Droid+Sans+Mono&subset=latin,cyrillic,greek");

            body, td, th { padding: 0px; margin: 0px; background-color: #FFF; font-family: 'Open Sans', sans-serif; font-style: normal; font-weight: normal; text-align: left; font-size: 13px; line-height: 1.3; color: #222;}
            h1 { margin: 0; font-size: 19px; padding: 10px;
                background: -moz-linear-gradient(top, #FFFFFF 0%, #F1F1F1 100%); /* FF3.6+ */
                background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#FFFFFF), color-stop(100%,#F1F1F1)); /* Chrome,Safari4+ */
                background: -webkit-linear-gradient(top, #FFFFFF 0%,#F1F1F1 100%); /* Chrome10+,Safari5.1+ */
                background: -o-linear-gradient(top, #FFFFFF 0%,#F1F1F1 100%); /* Opera11.10+ */
                background: -ms-linear-gradient(top, #FFFFFF 0%,#F1F1F1 100%); /* IE10+ */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#FFFFFF', endColorstr='#F1F1F1',GradientType=0 ); /* IE6-9 */
                background: linear-gradient(top, #FFFFFF 0%,#F1F1F1 100%); /* W3C */
                border-bottom: 1px solid #CCC;
                border-top-left-radius: 5px;
                border-top-right-radius: 5px;
            }
            h2 { margin: 0 0 15px 0; font-size: 16px; }
            h3 { margin: 15px 0 0 0; font-size: 14px; }
            input[type="text"], input[type="password"] { float: left; margin-right: 15px; }
            label { float: left; font-weight: bold; margin-right: 5px; display: block; width: 150px; }
            label span { font-weight: normal; color: #888; }
            .rounded_box {background: transparent; margin:0px; border-radius: 5px; border: 1px solid #CCC; box-shadow: 0 0 3px rgba(0, 0, 0, 0.3); padding: 0; }
            .rounded_box h4 { margin-bottom: 0px; margin-top: 7px; font-size: 14px; }
            .error_content { padding: 10px; }
            .description { padding: 3px 3px 3px 0;}
            pre { overflow: scroll; padding: 5px; }
            .command_box { border: 1px dashed #DDD; background-color: #F5F5F5; padding: 4px; font-family: 'Droid Sans Mono', monospace; display: inline-block; margin-top: 5px; margin-bottom: 15px; }
        </style>
        <!--[if IE]>
        <style>
            body { background-color: #DFDFDF; font-family: sans-serif; font-size: 13px; }
        </style>
        <![endif]-->
    </head>
    <body>
        <div class="rounded_box" style="margin: 30px auto 0 auto; width: 700px;">
            <img style="float: left; margin: 10px;" src="header.png"><h1>External libraries not initialized</h1>
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
