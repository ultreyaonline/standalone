<script>
    window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
    ga('create', '{!! config('google_analytics.googleid') !!}', '{!! $_SERVER['SERVER_NAME'] !!}');

    // Enable, and configure, the plugins you want to use. NOTE: if "options" are requested, you NEED to define them for each plugin. See the docs
    // https://github.com/googleanalytics/autotrack
    // https://philipwalton.com/articles/the-google-analytics-setup-i-use-on-every-site-i-build/#using-autotrack-plugins
//    ga('require', 'cleanUrlTracker', options); // Ensures consistency in the URL paths that get reported to Google Analytics; avoiding the problem where separate rows in your pages reports actually point to the same page.
    ga('require', 'eventTracker'); // Enables declarative event tracking, via HTML attributes in the markup.
//    ga('require', 'impressionTracker', options); // Allows you to track when elements are visible within the viewport.
    ga('require', 'maxScrollTracker'); // Automatically tracks how far down the page a user scrolls.
//    ga('require', 'mediaQueryTracker', options); // Enables tracking media query matching and media query changes.
    ga('require', 'outboundFormTracker'); // Automatically tracks form submits to external domains.
    ga('require', 'outboundLinkTracker'); // Automatically tracks link clicks to external domains.
    ga('require', 'pageVisibilityTracker'); // Automatically tracks how long pages are in the visible state (as opposed to in a background tab)
    ga('require', 'socialWidgetTracker'); // Automatically tracks user interactions with the official Facebook and Twitter widgets.
    ga('require', 'urlChangeTracker'); // Automatically tracks URL changes for single page applications.

    ga('set', 'transport', 'beacon');
    ga('set', 'anonymizeIp', 'true');
    ga('send', 'pageview');
</script>
<script async src='https://www.google-analytics.com/analytics.js' title="GA"></script>
<script async src='/js/autotrack.js'></script>
