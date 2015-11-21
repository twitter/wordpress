=== Plugin Name ===
Contributors: Twitter, niallkennedy
Tags: twitter, embedded tweet, twitter moment, twitter video, twitter grid, vine, periscope, twitter cards, tweet button, follow button, twitter analytics, twitter ads
Requires at least: 3.9
Tested up to: 4.4
Stable tag: 1.4.0
License: MIT
License URI: http://opensource.org/licenses/MIT

Official Twitter, Vine, and Periscope plugin for WordPress. Embed content and grow your audience. Requires PHP 5.4 or greater.

== Description ==

The Twitter plugin for WordPress makes it easy to embed single Tweets, multiple Tweets, a Moment, or a Vine on your website. Improve the reach of your content with the Tweet button and populate rich link previews on Twitter with automatically-generated Twitter Card markup. Help your audience follow your latest updates with the Twitter follow button and Periscope On Air button.

All features are deeply integrated with WordPress APIs to make building your webpages and administrative features as easy as possible with the extensibility you expect from WordPress. The plugin is multisite-aware, supports post meta customizations through the WordPress REST API, and shortcode customizations through shortcode UI.

Requires PHP version 5.4 or greater.

= Embed Twitter content =

Embed a [single Tweet](https://dev.twitter.com/web/embedded-tweets), [single Tweet with video template](https://dev.twitter.com/web/embedded-video), [Moment](https://dev.twitter.com/web/embedded-moments "Twitter Moment"), or [Twitter collection](https://dev.twitter.com/web/embedded-timelines/collection) grid template by pasting a URL into your article content. Customize advanced options using a shortcode.

Choose a light or dark theme, customize link and border colors, and configure other widget template options through your site's WordPress administrative interface.

The plugin automatically customizes embed HTML to match the locale or your site, optimally enqueues Twitter's widgets JavaScript for fast loading and extensibility, and handles advanced cases such as articles loaded asynchronously via the WordPress API.

= Embed a Vine =

Embed a Vine by pasting a URL into your article content. Customize advanced options using a shortcode.

The plugin optimally enqueues Vine's embed JavaScript to handle unpausing and unmuting videos as they become visible on the page.

= Add a Tweet button to public posts =

Add a Tweet button to public posts to encourage your visitors to share your content on Twitter. The Tweet button automatically constructs share text, URLs, and shares your site's Twitter account in the Tweet. Visitors may see recommended accounts to follow after posting your content, including your site's specified accounts.

Customize the pre-populated share text and hashtags shown in a Tweet composer for each post from your site's post editor.

= Enable link previews and Twitter bylines with Twitter Cards =

The plugin automatically generates [Twitter Card](https://dev.twitter.com/cards/overview) markup to populate link previews on Twitter and attribute articles to a site and author Twitter account. Increase engagement with your content and related Twitter accounts.

Twitter Cards with site attribution provides access to [Twitter Analytics](https://analytics.twitter.com/) for detailed information about your site's Twitter audience including top sharers and engagement data.

Provide a custom link preview title and description for each post from your site's post editor.

= Twitter follow button =

Add a [Twitter follow button](https://dev.twitter.com/web/follow-button) with a WordPress widget, shortcode, or by pasting a Twitter profile link into a post.

= Periscope On Air button =

Display a [Periscope On Air button](https://www.periscope.tv/embed) by pasting a Periscope profile URL into article content. Customize advanced options using a shortcode.

= Associate WordPress accounts with Twitter and Periscope identities =

Add a Twitter or Periscope username to a WordPress profile page for easy reference to your authors' external accounts. The plugin includes author attribution for posts and can dynamically include Twitter follow or Periscope On Air buttons through a shortcode when account information exists.

= Add an advertising pixel with a shortcode =

Add a Twitter audience pixel or [track advertising conversions](https://support.twitter.com/articles/20170807-conversion-tracking-for-websites "Twitter advertising conversion tracking") by adding an advertising pixel through a simple shortcode.

> <strong>Docs and active development</strong><br>
>Contribute to the plugin, submit pull requests, or run test suites through the [Twitter plugin for WordPress GitHub repository](https://github.com/twitter/wordpress).
> View [Twitter for WordPress documentation](https://dev.twitter.com/web/wordpress) to learn more about customization through WordPress filters.

== Upgrade Notice ==
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

The Twitter plugin for WordPress includes a settings page with options to customize the background color, link color, and border color used in Twitter embedded Tweets and embedded timelines.

= How do I include an embedded timeline in my page? =

Paste a Twitter collection URL into your post content to see a media-rich grid display on your website.

Log in to Twitter.com and visit the [Twitter widgets settings page](https://twitter.com/settings/widgets) to create and manage user, list, and search [embedded timeline](https://dev.twitter.com/web/embedded-timelines) widgets for your account. Widget settings are saved to a widget identifier for the logged in account; you may want to create a widget from your site's account, not your personal account, for continuity and general organization. Copy-and-paste the HTML generated by the Twitter widgets configuration tool into a new [WordPress text widget](http://codex.wordpress.org/WordPress_Widgets#Using_Text_Widgets).

= My custom link color and border color do not appear in embedded Tweets or timelines =

Your site may have a [Content Security Policy](https://developer.mozilla.org/docs/Web/Security/CSP/Introducing_Content_Security_Policy) blocking Twitter's JavaScript from inserting your custom styling into the widget.

You may have configured an embedded timeline widget with a non-default link color. Your widget configuration overrides your page / theme configuration.

= Does the Twitter plugin add additional tracking of my site's visitors? =

The Twitter plugin for WordPress makes it easier to explicitly include Twitter features and functionality on your WordPress site. No additional tracking is added as a result of our plugin code's execution on your server(s).

Twitter widgets and buttons load Twitter's widgets.js library through the WordPress JavaScript queue. Read more about [how Twitter for Websites widgets respect user privacy](https://dev.twitter.com/web/overview/privacy).

Twitter advertising trackers are only included on the page when invoked by the site using the `twitter_tracking` shortcode. Read more about [Twitter's policies for conversion tracking and tailored audiences products](https://support.twitter.com/articles/20171365-policies-for-conversion-tracking-and-tailored-audiences).

== Screenshots ==

1. Settings screen. Customize Tweet and Timeline color schemes including background, text colors, and borders. Attribute site content to a Twitter account. Automatically include Tweet buttons alongside your post content.
2. Post editor meta box. Define custom Tweet text, hashtags, and Twitter Card data.
3. Embed single Tweets, Tweets with video templates, multiple Tweets in a grid format, Twitter Moments, and Vines.

== Installation ==

1. Add the Twitter plugin to your WordPress installation
1. Activate the plugin through the 'Plugins' menu in WordPress
