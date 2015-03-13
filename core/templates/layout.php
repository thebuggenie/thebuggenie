<!DOCTYPE html>
<html lang="<?php echo \thebuggenie\core\framework\Settings::getHTMLLanguage(); ?>" style="cursor: progress;">
    <head>
        <meta charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>">
        <?php \thebuggenie\core\framework\Event::createNew('core', 'header_begins')->trigger(); ?>
        <meta name="description" content="The bug genie, friendly issue tracking">
        <meta name="keywords" content="thebuggenie friendly issue tracking">
        <meta name="author" content="thebuggenie.com">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <title><?php echo ($tbg_response->hasTitle()) ? strip_tags(\thebuggenie\core\framework\Settings::getSiteHeaderName() . ' ~ ' . $tbg_response->getTitle()) : strip_tags(\thebuggenie\core\framework\Settings::getSiteHeaderName()); ?></title>
        <style>
            @font-face {
              font-family: 'Droid Sans Mono';
              font-style: normal;
              font-weight: normal;
              src: url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/droid_sans_mono.eot');
              src: local('Droid Sans Mono'), local('DroidSansMono'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/droid_sans_mono.woff') format('woff'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/droid_sans_mono.ttf') format('truetype');
            }
            @font-face {
              font-family: 'Open Sans';
              font-style: normal;
              font-weight: normal;
              src: url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans.eot');
              src: local('Open Sans'), local('OpenSans'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans.woff') format('woff'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans.ttf') format('truetype');
            }
            @font-face {
              font-family: 'Open Sans';
              font-style: italic;
              font-weight: normal;
              src: url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_italic.eot');
              src: local('Open Sans Italic'), local('OpenSans-Italic'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_italic.woff') format('woff'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_italic.ttf') format('truetype');
            }
            @font-face {
              font-family: 'Open Sans';
              font-style: normal;
              font-weight: bold;
              src: url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_bold.eot');
              src: local('Open Sans Bold'), local('OpenSans-Bold'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_bold.woff') format('woff'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_bold.ttf') format('truetype');
            }
            @font-face {
              font-family: 'Open Sans';
              font-style: italic;
              font-weight: bold;
              src: url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_bold_italic.eot');
              src: local('Open Sans Bold Italic'), local('OpenSans-BoldItalic'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_bold_italic.woff') format('woff'), url('<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>fonts/open_sans_bold_italic.ttf') format('truetype');
            }
        </style>
        <link rel="shortcut icon" href="<?php if (\thebuggenie\core\framework\Settings::isUsingCustomFavicon()): echo \thebuggenie\core\framework\Settings::getFaviconURL(); else: echo image_url('favicon.png'); endif; ?>">
        <link title="<?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? __('%project_name search', array('%project_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getName())) : __('%site_name search', array('%site_name' => \thebuggenie\core\framework\Settings::getSiteHeaderName())); ?>" href="<?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? make_url('project_opensearch', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())) : make_url('opensearch'); ?>" type="application/opensearchdescription+xml" rel="search">
        <?php foreach ($tbg_response->getFeeds() as $feed_url => $feed_title): ?>
            <link rel="alternate" type="application/rss+xml" title="<?php echo str_replace('"', '\'', $feed_title); ?>" href="<?php echo $feed_url; ?>">
        <?php endforeach; ?>
        <?php include THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'themes' . DS . \thebuggenie\core\framework\Settings::getThemeName() . DS . 'theme.php'; ?>
        <?php if (count(\thebuggenie\core\framework\Context::getModules())): ?>
            <?php foreach (\thebuggenie\core\framework\Context::getModules() as $module): ?>
                <?php if (file_exists(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'themes' . DS . \thebuggenie\core\framework\Settings::getThemeName() . DS . "{$module->getName()}.css")): ?>
                    <?php $tbg_response->addStylesheet("{$module->getName()}.css"); ?>
                <?php endif; ?>
                <?php if (file_exists(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS . 'js' . DS . "{$module->getName()}.js")): ?>
                    <?php $tbg_response->addJavascript("{$module->getName()}.js"); ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php list ($cssstring, $sepcss) = tbg_get_stylesheets(); ?>
        <?php foreach (explode(',', $cssstring) as $css): ?>
            <link rel="stylesheet" href="<?php print make_url('home').$css; ?>">
        <?php endforeach; ?>
        <?php foreach ($sepcss as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>

        <?php list ($jsstring, $sepjs) = tbg_get_javascripts(); ?>
        <?php foreach (explode(',', $jsstring) as $js): ?>
            <script type="text/javascript" src="<?php print make_url('home').$js; ?>"></script>
        <?php endforeach; ?>
        <?php foreach ($sepjs as $js): ?>
            <script type="text/javascript" src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
          <!--[if lt IE 9]>
              <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
          <![endif]-->
        <?php \thebuggenie\core\framework\Event::createNew('core', 'header_ends')->trigger(); ?>
    </head>
    <body id="body">
        <div id="main_container">
            <?php if (!in_array(\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName(), array('login_page', 'elevated_login_page', 'reset_password'))): ?>
                <?php \thebuggenie\core\framework\Logging::log('Rendering header'); ?>
                <?php require THEBUGGENIE_CORE_PATH . 'templates/headertop.inc.php'; ?>
                <?php \thebuggenie\core\framework\Logging::log('done (rendering header)'); ?>
            <?php endif; ?>
            <div id="content_container">
                <?php \thebuggenie\core\framework\Logging::log('Rendering content'); ?>
                <?php echo $content; ?>
                <?php \thebuggenie\core\framework\Logging::log('done (rendering content)'); ?>
            </div>
            <?php \thebuggenie\core\framework\Event::createNew('core', 'footer_begin')->trigger(); ?>
            <?php require THEBUGGENIE_CORE_PATH . 'templates/footer.inc.php'; ?>
            <?php \thebuggenie\core\framework\Event::createNew('core', 'footer_end')->trigger(); ?>
        </div>
        <?php require THEBUGGENIE_CORE_PATH . 'templates/backdrops.inc.php'; ?>
        <script type="text/javascript">
            document.observe('dom:loaded', function() {
                var f_init = function() {TBG.initialize({ basepath: '<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>', data_url: '<?php echo make_url('userdata'); ?>', autocompleter_url: '<?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? make_url('project_quicksearch', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())) : make_url('quicksearch'); ?>'})};
                <?php if (\thebuggenie\core\framework\Context::isDebugMode()): ?>
                    TBG.debug = true;
                    TBG.debugUrl = '<?php echo make_url('debugger', array('debug_id' => '___debugid___')); ?>';
                    <?php
                        $load_time = \thebuggenie\core\framework\Context::getLoadTime();
                        $load_time = ($load_time >= 1) ? round($load_time, 2) . 's' : round($load_time * 1000, 1) . 'ms';
                    ?>
                    TBG.Core.AjaxCalls.push({location: 'Page loaded', time: new Date(), debug_id: '<?php echo \thebuggenie\core\framework\Context::getDebugID(); ?>', loadtime: '<?php echo $load_time; ?>'});
                    TBG.loadDebugInfo('<?php echo \thebuggenie\core\framework\Context::getDebugID(); ?>', f_init);
                <?php else: ?>
                    f_init();
                <?php endif; ?>
            });
        </script>
    </body>
</html>
