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
            <?php $os_unicode_ranges = array('latin' => 'U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215, U+E0FF, U+EFFD, U+F000', 'latin_ext' => 'U+0100-024F, U+1E00-1EFF, U+20A0-20AB, U+20AD-20CF, U+2C60-2C7F, U+A720-A7FF', 'cyrillic' => 'U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116'); ?>
            <?php echo font_face_woffs('Open Sans', 'open_sans_300_italic', 300, 'italic', 'Open Sans Light Italic', 'OpenSansLight-Italic', $os_unicode_ranges); ?>
            <?php echo font_face_woffs('Open Sans', 'open_sans_400_italic', 400, 'italic', 'Open Sans Italic', 'OpenSans-Italic', $os_unicode_ranges); ?>
            <?php echo font_face_woffs('Open Sans', 'open_sans_600_italic', 'bold', 'italic', 'Open Sans Semibold Italic', 'OpenSans-SemiboldItalic', $os_unicode_ranges); // semibold will act as bold ?>
            <?php echo font_face_woffs('Open Sans', 'open_sans_300', 300, 'normal', 'Open Sans Light', 'OpenSansLight', $os_unicode_ranges); ?>
            <?php echo font_face_woffs('Open Sans', 'open_sans_400', 400, 'normal', 'Open Sans', 'OpenSans', $os_unicode_ranges); ?>
            <?php echo font_face_woffs('Open Sans', 'open_sans_600', 'bold', 'normal', 'Open Sans Semibold', 'OpenSans-Semibold', $os_unicode_ranges); // semibold will act as bold ?>
        </style>
        <link rel="shortcut icon" href="<?php echo (\thebuggenie\core\framework\Context::isProjectContext() && \thebuggenie\core\framework\Context::getCurrentProject()->hasSmallIcon()) ? \thebuggenie\core\framework\Context::getCurrentProject()->getSmallIconName() : (\thebuggenie\core\framework\Settings::isUsingCustomFavicon() ? \thebuggenie\core\framework\Settings::getFaviconURL() : image_url('favicon.png')); ?>">
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

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

        <script type="text/javascript" src="<?php echo make_url('home'); ?>js/HackTimer.min.js"></script>
        <script type="text/javascript" src="<?php echo make_url('home'); ?>js/HackTimer.silent.min.js"></script>
        <script type="text/javascript" src="<?php echo make_url('home'); ?>js/HackTimerWorker.min.js"></script>
        <script>
            var bust = function (path) {
                return path + '?bust=' + <?php echo (\thebuggenie\core\framework\Context::isDebugMode()) ? ' Math.random()' : "'" . \thebuggenie\core\framework\Settings::getVersion() . "'"; ?>;
            };

            var require = {
                waitSeconds: 0,
                baseUrl: '<?php echo make_url('home'); ?>js',
                paths: {
                    jquery: 'jquery-2.1.3.min',
                    'jquery-ui': 'jquery-ui.min',
                    'thebuggenie': bust('thebuggenie.js'),
                    'thebuggenie/tbg': bust('thebuggenie/tbg.js'),
                    'thebuggenie/tools': bust('thebuggenie/tools.js'),
                    'TweenMax': bust('greensock/TweenMax.js'),
                    'TweenLite': bust('greensock/TweenLite.js'),
                    'GSDraggable': bust('greensock/utils/Draggable.js'),
                    'jquery.nanoscroller': bust('jquery.nanoscroller.js')
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
                        deps: ['jquery', 'jquery.flot']
                    },
                    'jquery.flot.time': {
                        deps: ['jquery', 'jquery.flot']
                    },
                    'jquery.flot.dashes': {
                        deps: ['jquery', 'jquery.flot']
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
                    'jquery.ui.touch-punch': {
                        deps: ['jquery-ui']
                    },
                    'jquery.animate-enhanced.min': {
                        deps: ['jquery']
                    },
                     'jquery-ui': {
                         deps: ['jquery.animate-enhanced.min']
                     },
                     'dragdrop': {
                         deps: ['effects']
                     },
                    deps: [<?php echo join(', ', array_map(function ($element) { return "\"{$element}\""; }, $localjs)); ?>]
                }
            };
        </script>
        <script data-main="thebuggenie" src="<?php echo make_url('home'); ?>js/require.js"></script>
        <script src="<?php echo make_url('home'); ?>js/promise-7.0.4.min.js"></script>
        <?php foreach ($externaljs as $js): ?>
            <script type="text/javascript" src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
          <!--[if lt IE 9]>
              <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
          <![endif]-->
        <?php \thebuggenie\core\framework\Event::createNew('core', 'layout.php::header-ends')->trigger(); ?>
    </head>
    <body id="body">
        <div id="main_container" class="page-<?php echo \thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName(); ?> cf" data-url="<?php echo make_url('userdata'); ?>">
            <?php if (!in_array(\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName(), array('login_page', 'elevated_login_page', 'reset_password'))): ?>
                <?php \thebuggenie\core\framework\Logging::log('Rendering header'); ?>
                <?php require THEBUGGENIE_CORE_PATH . 'templates/headertop.inc.php'; ?>
                <?php \thebuggenie\core\framework\Logging::log('done (rendering header)'); ?>
            <?php endif; ?>
            <div id="content_container" class="cf">
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
