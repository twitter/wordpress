# Twitter plugin for WordPress

The Twitter plugin for WordPress optimizes your website for a Twitter audience through easy to use sharing buttons, embedded Tweets, embedded timelines, auto-generated markup indexed by Twitter, and Follow buttons to help grow your Twitter audience. All features are deeply integrated with WordPress APIs to make building your webpages and administrative features as easy as possible with the extensibility you expect from WordPress.

The Twitter plugin for WordPress requires PHP 5.4 or greater to take advantage of [traits](http://php.net/manual/language.oop5.traits.php), [late static bindings](http://php.net/manual/language.oop5.late-static-bindings.php) for extensibility, [namespaces](http://php.net/manual/language.namespaces.rationale.php), and Twitter libraries. Embedded Tweets require a server capable of communicating with Twitter's servers over TLS/SSL; the [cURL library](http://php.net/manual/book.curl.php) typically works best.

Current version: `2.0.2`

[![Build Status](https://travis-ci.org/twitter/wordpress.svg)](https://travis-ci.org/twitter/wordpress)

## Features

Individual features may be toggled off through the `twitter_features` filter.

Features built on top of oEmbed APIs omit JavaScript from the oEmbed response, asynchronously loading JavaScript through the WordPress JavaScript queue. An asynchronous JavaScript function queue is available for Twitter's widgets JavaScript and universal website tracking JavaScript to register dependencies such as an analytics tracker.

### Embedded Tweet

Add an [embedded Tweet](https://dev.twitter.com/web/embedded-tweets) to your site by URL or by using the `tweet` [shortcode macro](http://codex.wordpress.org/Shortcode). Shortcode syntax is compatible with Jetpack, allowing flexible migration for WordPress.com or Jetpack sites.

Add a Twitter embedded video widget to your site by using the `twitter_video` shortcode macro.

Customize an embedded Tweet color scheme to match your site's color palette. Choose a theme type, link color, and border color in the WordPress administrative interface.

### Twitter profile

Add an [embedded profile timeline](https://dev.twitter.com/web/embedded-timelines/user) to your site by URL, using the `[twitter_profile]` shortcode macro, or adding a widget to your theme's widget area.

### Twitter list

Add an [embedded list timeline](https://dev.twitter.com/web/embedded-timelines/list) to your site by URL, using the `[twitter_list]` shortcode macro, or adding a widget to your theme's widget area.

### Twitter search

Add an [embedded search timeline](https://dev.twitter.com/web/embedded-timelines/search) to your site using the `[twitter_list]` shortcode macro or adding a widget to your theme's widget area.

### Collection

Display a [Twitter collection](https://dev.twitter.com/web/embedded-timelines/collection) by URL, using the `twitter_collection` shortcode macro, or adding widget to a theme's widget area.

Add a Twitter collection with a grid template display to an article by URL or by using the `twitter_grid` shortcode macro.

### Embedded Moment

Add a [Twitter Moment](https://twitter.com/i/moments) to an article by URL or by using the `twitter_moment` shortcode macro.

### Embedded Vine

Add a [Vine simple embed](https://dev.twitter.com/web/vine) to an article by URL or by using the `vine` shortcode macro.

### Twitter Cards

The Twitter plugin for WordPress will automatically generate Twitter [Twitter Cards](https://dev.twitter.com/cards/overview) markup to highlight your post content when shared on Twitter. Add your site and author Twitter usernames through the WordPress administrative interface to attribute content to Twitter authors and drive followers.

Add your site's Twitter username through the WordPress administrative interface to enable free [Twitter Analytics](https://analytics.twitter.com/) for your site. Twitter Analytics shows the popularity of your site's URLs shared on Twitter, the Twitter accounts growing your content's reach, and how your content spreads across Twitter.

Customize each post's Twitter Card title and description directly from the WordPress editor to fine-tune your content marketing for the Twitter audience.

### Tweet Button

Add a [Tweet Button](https://dev.twitter.com/web/tweet-button) to any page using the `twitter_share` shortcode macro to encourage sharing of your content on Twitter. The Twitter plugin for WordPress automatically pulls in site and author Twitter accounts to credit in the Tweet and grow followers.

Customize Tweet text and add hashtags Tweet Button shares for your post through a custom editor in the WordPress post editing interface.

### Follow Button

Add a Follow Button to your site by configuring a Follow button [WordPress widget](http://codex.wordpress.org/WordPress_Widgets) for use in an eligible widget area. Add a Follow button anywhere in your post content or theme using the `twitter_follow` shortcode macro.

### Periscope On Air Button

Add a [Periscope On Air Button](https://www.periscope.tv/embed) to any page using a Periscope profile URL or the `periscope_on_air` shortcode macro to help your website audience discover your Periscope account. The button will automatically display your account's on air status when you broadcast.

### Advertising Tracking

WordPress-powered sites with Twitter advertising accounts can use the `twitter_tracking` shortcode with a campaign identifier to track advertising conversions or build a custom audience for later targeting in a Twitter advertisement. Easily track activity on your WordPress site with JavaScript and other resources optimally loaded through WordPress.

## Code structure

The plugin is split into PHP object builders for constructing buttons, widgets, and cards and validating parameters. WordPress-specific code exists under `src/Twitter/WordPress/`. Coding standards therefore vary between PSR style (e.g. space indent) and WordPress style (e.g. tab indent) based on directory and its target runtime environment.

## Code of Conduct
This, and all github.com/twitter projects, are under the [Twitter Open Source Code of Conduct](https://github.com/twitter/code-of-conduct/blob/master/code-of-conduct.md). Additionally, see the [Typelevel Code of Conduct](http://typelevel.org/conduct) for specific examples of harassing behavior that are not tolerated.

## Contact

End user support questions should be submitted to the WordPress plugin repository forum:
<https://wordpress.org/support/plugin/twitter>

Bugs or feature development contributions should be created through the GitHub repository:
<https://github.com/twitter/wordpress/issues>

## Authors
* Niall Kennedy <https://twitter.com/niall>

## Maintainers
* Evan Sobkowicz <https://twitter.com/evansobkowicz>

A full list of [contributors](https://github.com/twitter/wordpress/graphs/contributors) can be found on GitHub.

## License
Copyright 2015- Twitter, Inc.

Licensed under the MIT License: http://opensource.org/licenses/MIT
