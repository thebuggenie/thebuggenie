<!DOCTYPE html>
<html lang="<?php echo \thebuggenie\core\framework\Settings::getHTMLLanguage(); ?>" style="cursor: progress;">
    <head>
        <meta charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>">
        <?php \thebuggenie\core\framework\Event::createNew('core', 'layout.php::header-begins')->trigger(); ?>
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
              src: url('<?php echo $webroot; ?>fonts/droid_sans_mono.eot');
              src: local('Droid Sans Mono'), local('DroidSansMono'), url('<?php echo $webroot; ?>fonts/droid_sans_mono.woff') format('woff'), url('<?php echo $webroot; ?>fonts/droid_sans_mono.ttf') format('truetype');
            }
            @font-face {
              font-family: 'Open Sans';
              font-style: normal;
              font-weight: normal;
              src: url('<?php echo $webroot; ?>fonts/open_sans.eot');
              src: local('Open Sans'), local('OpenSans'), url('<?php echo $webroot; ?>fonts/open_sans.woff') format('woff'), url('<?php echo $webroot; ?>fonts/open_sans.ttf') format('truetype');
            }
            @font-face {
              font-family: 'Open Sans';
              font-style: italic;
              font-weight: normal;
              src: url('<?php echo $webroot; ?>fonts/open_sans_italic.eot');
              src: local('Open Sans Italic'), local('OpenSans-Italic'), url('<?php echo $webroot; ?>fonts/open_sans_italic.woff') format('woff'), url('<?php echo $webroot; ?>fonts/open_sans_italic.ttf') format('truetype');
            }
            @font-face {
              font-family: 'Open Sans';
              font-style: normal;
              font-weight: bold;
              src: url('<?php echo $webroot; ?>fonts/open_sans_bold.eot');
              src: local('Open Sans Bold'), local('OpenSans-Bold'), url('<?php echo $webroot; ?>fonts/open_sans_bold.woff') format('woff'), url('<?php echo $webroot; ?>fonts/open_sans_bold.ttf') format('truetype');
            }
            @font-face {
              font-family: 'Open Sans';
              font-style: italic;
              font-weight: bold;
              src: url('<?php echo $webroot; ?>fonts/open_sans_bold_italic.eot');
              src: local('Open Sans Bold Italic'), local('OpenSans-BoldItalic'), url('<?php echo $webroot; ?>fonts/open_sans_bold_italic.woff') format('woff'), url('<?php echo $webroot; ?>fonts/open_sans_bold_italic.ttf') format('truetype');
            }
        </style>
        <link rel="shortcut icon" href="<?php if (\thebuggenie\core\framework\Settings::isUsingCustomFavicon()): echo \thebuggenie\core\framework\Settings::getFaviconURL(); else: echo image_url('favicon.png'); endif; ?>">
        <link title="<?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? __('%project_name search', array('%project_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getName())) : __('%site_name search', array('%site_name' => \thebuggenie\core\framework\Settings::getSiteHeaderName())); ?>" href="<?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? make_url('project_opensearch', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())) : make_url('opensearch'); ?>" type="application/opensearchdescription+xml" rel="search">
        <?php foreach ($tbg_response->getFeeds() as $feed_url => $feed_title): ?>
            <link rel="alternate" type="application/rss+xml" title="<?php echo str_replace('"', '\'', $feed_title); ?>" href="<?php echo $feed_url; ?>">
        <?php endforeach; ?>
        <?php include THEBUGGENIE_PATH . 'themes' . DS . \thebuggenie\core\framework\Settings::getThemeName() . DS . 'theme.php'; ?>

        <?php list ($localcss, $externalcss) = $tbg_response->getStylesheets(); ?>
        <?php foreach ($localcss as $css): ?>
            <link rel="stylesheet" href="<?php print $css; ?>">
        <?php endforeach; ?>
        <?php foreach ($externalcss as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>

        <script type="text/javascript" src="<?php echo make_url('home'); ?>js/HackTimer.silent.min.js"></script>
        <script type="text/javascript" src="<?php echo make_url('home'); ?>js/HackTimerWorker.min.js"></script>
        <script>
            var bust = function (path) {
                return path + '?bust=' + <?php echo (\thebuggenie\core\framework\Context::isDebugMode()) ? ' Math.random()' : "'" . \thebuggenie\core\framework\Settings::getVersion() . "'"; ?>;
            };

            var require = {
                baseUrl: '<?php echo make_url('home'); ?>js',
                paths: {
                    jquery: 'jquery-2.1.3.min',
                    'jquery-ui': 'jquery-ui.min',
                    'thebuggenie': bust('thebuggenie.js'),
                    'thebuggenie/tbg': bust('thebuggenie/tbg.js'),
                    'thebuggenie/tools': bust('thebuggenie/tools.js')
                },
                map: {
                    '*': { 'jquery': 'jquery-private' },
                    'jquery-private': { 'jquery': 'jquery' }
                },
                shim: {
                    'prototype': {
                        // Don't actually need to use this object as
                        // Prototype affects native objects and creates global ones too
                        // but it's the most sensible object to return
                        exports: 'Prototype'
                    },
                    'jquery.markitup': {
                        deps: ['jquery']
                    },
                    'calendarview': {
                        deps: ['prototype'],
                        exports: 'Calendar'
                    },
                    'effects': {
                        deps: ['prototype']
                    },
                    'controls': {
                        deps: ['effects']
                    },
                    'jquery.flot': {
                        deps: ['jquery']
                    },
                    'jquery.flot.selection': {
                        deps: ['jquery.flot']
                    },
                    'jquery.flot.time': {
                        deps: ['jquery.flot']
                    },
                    'jquery.flot.dashes': {
                        deps: ['jquery.flot']
                    },
                    'scriptaculous': {
                        deps: ['prototype', 'controls'],
                        exports: 'Scriptaculous'
                    },
                    'bootstrap-typeahead': {
                        deps: ['jquery']
                    },
                    'mention': {
                        deps: ['jquery', 'bootstrap-typeahead']
                    },
                    'jquery.nanoscroller': {
                        deps: ['jquery']
                    },
                    'jquery.ba-resize': {
                        deps: ['jquery']
                    },
                    deps: [<?php echo join(', ', array_map(function ($element) { return "\"{$element}\""; }, $localjs)); ?>]
                }
            };
        </script>
        <script data-main="thebuggenie" src="<?php echo make_url('home'); ?>js/require.js"></script>
        <?php foreach ($externaljs as $js): ?>
            <script type="text/javascript" src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
          <!--[if lt IE 9]>
              <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
          <![endif]-->
        <?php \thebuggenie\core\framework\Event::createNew('core', 'layout.php::header-ends')->trigger(); ?>
    </head>
    <body id="body">
        <div id="main_container" class="page-<?php echo \thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName(); ?>" data-url="<?php echo make_url('userdata'); ?>">
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
            <?php \thebuggenie\core\framework\Event::createNew('core', 'layout.php::footer-begins')->trigger(); ?>
            <?php require THEBUGGENIE_CORE_PATH . 'templates/footer.inc.php'; ?>
            <?php \thebuggenie\core\framework\Event::createNew('core', 'layout.php::footer-ends')->trigger(); ?>
        </div>
        <?php require THEBUGGENIE_CORE_PATH . 'templates/backdrops.inc.php'; ?>
        <script type="text/javascript">
            var TBG, jQuery;
            require(['domReady', 'thebuggenie/tbg', 'jquery', 'jquery.nanoscroller'], function (domReady, tbgjs, jquery, nanoscroller) {
                domReady(function () {
                    TBG = tbgjs;
                    jQuery = jquery;
                    require(['scriptaculous']);
                    var f_init = function() {TBG.initialize({ basepath: '<?php echo $webroot; ?>', data_url: '<?php echo make_url('userdata'); ?>', autocompleter_url: '<?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? make_url('project_quicksearch', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())) : make_url('quicksearch'); ?>'})};
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
            });
        </script>
    </body>
</html>
