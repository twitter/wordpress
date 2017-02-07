=== Plugin Name ===
Contributors: Twitter, niallkennedy
Tags: twitter, embedded tweet, embedded timeline, twitter profile, twitter list, twitter moment, twitter video, twitter grid, vine, periscope, twitter cards, tweet button, follow button, twitter analytics, twitter ads
Requires at least: 4.1
Tested up to: 4.7
Stable tag: 2.0.1
License: MIT
License URI: https://opensource.org/licenses/MIT

Official Twitter, Periscope, and Vine plugin for WordPress. Embed content and grow your audience. Requires PHP 5.4 or greater.

== Description ==

Embed Twitter content, improve sharing on Twitter, convert your web audience into Twitter or Periscope subscribers, and easily track visits to your website from Twitter advertising.

Requires PHP version 5.4 or greater.

= Embed Twitter content =
Embed Twitter content by pasting a URL, customizing a shortcode, or in a widget area.

* [single Tweet](https://dev.twitter.com/web/embedded-tweets "single Tweet embed")
* [single Tweet with video template](https://dev.twitter.com/web/embedded-video "single Tweet with video embed")
* [profile timeline](https://dev.twitter.com/web/embedded-timelines/user "Twitter embedded profile timeline")
* [list timeline](https://dev.twitter.com/web/embedded-timelines/list "Twitter embedded list timeline")
* [search timeline](https://dev.twitter.com/web/embedded-timelines/search "Twitter embedded search timeline")
* [collection](https://dev.twitter.com/web/embedded-timelines/collection "Twitter embedded collection")
* [Moment](https://dev.twitter.com/web/embedded-moments "Twitter embedded Moment")
* [Vine](https://dev.twitter.com/web/vine "Vine embed")

Customize embed display to match your theme. Choose a light or dark background, customize link and border colors, and customize timeline template components through your site's WordPress administrative interface.

The plugin automatically customizes an embed's template text to match the locale of your site, optimally loads Twitter's JavaScript to improve site speed and extensibility, and handles advanced use cases such as articles loaded asynchronously via the WordPress API.

= Grow your Twitter audience =

Automatically generate link previews for your site's URLs shared on Twitter using [Twitter Cards markup](https://dev.twitter.com/cards/overview) . Easily identify your site and author Twitter accounts through your site and user administrative interfaces.

Add a [Tweet button](https://dev.twitter.com/web/tweet-button) to public posts to encourage your visitors to share your content on Twitter. Visitors may see recommended accounts to follow after sharing your content including your site and its authors.

Add a [Follow button](https://dev.twitter.com/web/follow-button) to convert your site visitors into Twitter subscribers.

Add a [Periscope On Air button](https://www.periscope.tv/embed#on-air-button) to convert your site visitors into Periscope subscribers.

= Improve Twitter advertising campaigns =

Easily add a Twitter website tag to your website to track the effectiveness and [conversion rates](https://business.twitter.com/en/help/campaign-measurement-and-analytics/conversion-tracking-for-websites.html) of Twitter advertising campaigns or [build tailored audiences](https://business.twitter.com/en/targeting/tailored-audiences.html) to target your Twitter advertisements for your website audience.

> <strong>Docs and active development</strong><br>
> Contribute to the plugin, submit pull requests, or run test suites through the [Twitter plugin for WordPress GitHub repository](https://github.com/twitter/wordpress).
> View [Twitter for WordPress documentation](https://dev.twitter.com/web/wordpress) to learn more about customization through WordPress filters.

== Upgrade Notice ==
= 2.0.0 =
Adds profile, list, search, and collection timelines. Upgrade advertising pixel.

= 1.5.0 =
Update admin menu functionality for compatibility with WordPress 4.5+.

= 1.4.0 =
Remove Tweet button options for share count. Display a Follow button by pasting a Twitter profile URL.

= 1.3.0 =
Adds embedded Tweets grid template, Vine embeds, and Periscope On Air buttons.

= 1.2.0 =
Support Twitter Moment embeds.

= 1.1.0 =
Shortcode improvements for ajax-loaded posts. Remove photo, gallery, and product Twitter Cards no longer supported by Twitter.

= 1.0.1 =
Display admin notice if current PHP version does not meet minimum requirements. Do not display Tweet button in auto-generated excerpt.

== Changelog ==
= 2.0.1 =
* Enqueue Twitter widgets JavaScript and advertising JavaScript early in the page build process if a widget is active on the page
* Tweet button: update expected length of a wrapped t.co URL with HTTP scheme
* Support expanded post metadata descriptors in WordPress 4.6+. Includes REST API support for custom Tweet button and Twitter Cards values
* Fix post metadata deletion for custom Tweet button and Twitter Card text

= 2.0.0 =
* Embed a [profile timeline](https://dev.twitter.com/web/embedded-timelines/user "Twitter embedded profile timeline"), [list timeline](https://dev.twitter.com/web/embedded-timelines/list "Twitter embedded list timeline"), or [collection](https://dev.twitter.com/web/embedded-timelines/collection "Twitter embedded collection") by pasting a URL, customizing a shortcode, or a widget
* Embed a [search timeline](https://dev.twitter.com/web/embedded-timelines/search "Twitter embedded search timeline") by shortcode or widget using a widget ID configured on Twitter.com
* Upgrade Twitter advertising tracker to [universal website tag](https://blog.twitter.com/2016/website-conversion-tracking-and-remarketing-made-easier-and-more-flexible "Twitter blog: announcing universal website tag")
* Twitter Cards include image alternative text when available
* Follow button and Periscope On Air button use post author or site username if no username specified
* Improved compatibility with WordPress.com / Jetpack formatting of `tweet` shortcode
* Prefer wp_resource_hints API for DNS prefetch in WordPress 4.6+
* Update single Tweet with video to remove status customization no longer supported by Twitter
* Fix bug when a large featured image is highlighted in a Twitter Card
* Describe site username option for WordPress REST API in WordPress 4.7
* Bump minimum WordPress version to 4.1

= 1.5.0 =
* Place Twitter administrative menu as a general menu item, not the deprecated utility menu. WordPress 4.5 compatibility feature
* Use publish.twitter.com oEmbed API endpoint for single Tweet oEmbed

= 1.4.0 =
* Remove Tweet button options for share count and align display, matching Twitter's changes
* Convert Twitter profile URLs into a Follow button
* Enable language packs loaded from WordPress.org

= 1.3.0 =
* Display multiple Tweets in a [media-rich responsive grid template](https://dev.twitter.com/web/embedded-timelines/collection#template-grid) by pasting a Twitter collection URL 
* Add a [Periscope On Air button](https://www.periscope.tv/embed) through a widget, shortcode, or as an embed handler for a Periscope profile URL
* Add a Vine through a URL or shortcode
* Shortcode UI integration now uses the `register_shortcode_ui` action introduced in Shortcake 0.5.0

= 1.2.0 =
* Embed a Twitter Moment by simply pasting a URL
* Always load Twitter ads conversion tracking JavaScript over HTTPS

= 1.1.0 =
* Shortcodes now include inline asynchronous JavaScript loaders for improved compatibility with ajax-loaded content
* Twitter announced photo, gallery, and product Twitter Cards are [no longer supported](https://twittercommunity.com/t/deprecating-the-photo-gallery-and-product-cards/38961 "Twitter announcement: deprecation of photo, gallery, product cards"). Removed from plugin
* Add [Shortcake plugin](https://wordpress.org/plugins/shortcode-ui/ "Shortcake WordPress plugin") compatibility for form-based shortcode construction and previews
* Improved Twitter Card image compatibility

= 1.0.1 =
* Display admin notice if plugin is installed on a site not meeting minimum PHP requirement
* Disable Tweet button the_content wrapper when called during excerpt generation
* Tweet button: add support for via shortcode attribute
* Fix: save Follow button widget with no overrides

= 1.0.0 =
* Embedded Tweet
* Embedded Tweet with video template
* Tweet button
* Twitter Cards
* Follow button
* Advertising tracker for Twitter custom audiences and ad conversion

== Frequently Asked Questions ==

= How can I change an embedded Tweet's background and link colors to match my site's theme? =

The Twitter plugin for WordPress includes a settings page with options to set a light or dark theme and choose a link or border color used in embedded Tweets and timelines.

= My custom link color and border color do not appear in embedded Tweets or timelines =

Your site may have a [Content Security Policy](https://developer.mozilla.org/docs/Web/Security/CSP/Introducing_Content_Security_Policy) blocking Twitter's JavaScript from inserting your custom styling into the widget.

You may have configured an embedded search timeline widget with a non-default link color. Your stored widget configuration overrides your page or theme’s configuration.

= Does the Twitter plugin add additional tracking of my site's visitors? =

The Twitter plugin for WordPress makes it easier to explicitly include Twitter features and functionality on your WordPress site. No additional tracking is added as a result of our plugin code's execution on your server(s).

Twitter widgets and buttons load Twitter's widgets.js library through the WordPress JavaScript queue. Read more about [how Twitter for Websites widgets respect user privacy](https://dev.twitter.com/web/overview/privacy).

Twitter advertising trackers are only included on the page when invoked by the site using the `twitter_tracking` shortcode or placing the Twitter advertising shortcode in a widget area. Read more about [Twitter's policies for conversion tracking and tailored audiences products](https://support.twitter.com/articles/20171365).

== Screenshots ==

1. Settings screen. Customize Tweet and Timeline color schemes including background, text colors, and borders. Attribute site content to a Twitter account. Automatically include Tweet buttons alongside your post content.
2. Post editor meta box. Define custom Tweet text, hashtags, and Twitter Card data.
3. Embed single Tweets, timelines, Twitter Moments, and Vines.
4. Embed a Twitter profile, list, collection, Moment, or search result in a theme widget area. Add a Twitter follow button or Periscope on Air button to a theme widget area to increase followers. Easily add advertising conversion tracking through a widget.
