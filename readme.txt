== Classic SEO ==

Description:       Classic SEO is the first SEO plugin built specifically to work with ClassicPress. The plugin contains many essential SEO tools to help optimize your website.
Version:           1.0.0-beta.4
Text Domain:       cpseo
Domain Path:       /languages
Requires PHP:      7.0
Requires:          1.0.0
Tested:            4.9.99
Author:            ClassicPress Research
Author URI:        https://github.com/ClassicPress-plugins/
Plugin URI:        https://github.com/ClassicPress-plugins/classicpress-seo
License:           GPLv2
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

**SEO Plugin For ClassicPress**

Classic SEO is the first SEO plugin built specifically to work with ClassicPress. The plugin contains many essential SEO tools to help optimize your website.

**As this plugin is still in the development & testing stage, use it on a production site at your own risk.**

== Key Features ==

* **Module Based System**
* **Google Schema Markup aka Rich Snippets**
* **XML Sitemap**
* **Optimize up to 5 Keywords**
* **Google Keyword Suggestion**
* **Redirection Manager**
* **Local SEO / Knowledge Graph**
* **SEO Optimized Breadcrumbs**
* **404 Monitor**
* **Support for ACF**
* **Support for Classic Commerce**
* **Support for WooCommerce**
* **Internal Linking Suggestions**
* **Role Manager**
* **Importers for Rank Math, Yoast, All In One SEO and The SEO Framework**
* **Google Search Console Integration**

== Important note ==
**As this plugin is still in the development & testing stage, use it on a production site at your own risk.**

== Changelog ==
**v 1.0.0-beta4 / 2020-05-24**
* NEW: Add new title separator characters (#71)
* NEW: Add '%' to CTR heading in Search Analytics view within Search Console (#72)
* NEW: Use native custom post types dashboard icon in Titles & Meta Setting (#75)
* IMPROVED: Output of SEO meta for pages created with page builders (#74)
* IMPROVED: Hide cpseo meta key data in custom fields on post edit page (#76)
* UPDATED: Help page content updated (#78)
* UPDATED: Translations (#79)
* FIXED: Dash menu not updating when module activated (#77)
* FIXED: Missing cpseo prefix in some settings (#81)
* FIXED: Removed broken link from title on Dashboard page (#73)

**v 1.0.0-beta3 / 2020-04-03**
* NEW: Add new Events schema properties to help with events that have been cancelled or postponed as a result of Covid-19
* UPDATED: Code Potent Update Manager updated to latest version

**v 1.0.0-beta2 / 2020-02-14**
* NEW: Add usage tracking notice and opt out (props xxsimoxx)
* FIX: Variables translations in help pages will now translate
* FIX: Fix for duplicate "view details" when GitHub updater is active (props xxsimoxx)
* FIX: Fix for PHP error when refreshing sitemap in Search Console

**v 1.0.0-beta1 / 2020-02-08**
* NEW: Integrated Code Potent's Update Manager
* NEW: Check for WP_CLI (props xxsimoxx)
* IMPROVED: Services rich snippets (added missing properties)
* Moved to new ClassicPress-plugins GitHub repository
* Renamed dashicon image
* Remove other superfluous images
* Translation file updates

**v 0.7.0 / 2020-01-17**
* NEW: Adds importer for The SEO Framework

**v 0.6.0 / 2019-12-28**
* UPDATED: Now fully supports [Classic Commerce](https://github.com/ClassicPress-research/classic-commerce)

**v 0.5.3 / 2019-12-21**
* FIX: Fix for wrong message shown on post edit page when content length was between 500 and 600 words
* TWEAK: Minor tweaks to AIOSEO import

**v 0.5.2 / 2019-12-20**
* FIX: Fix for "Undefined variable: sitemap_settings" in class-aioseo.php

**v 0.5.1 / 2019-12-20**
* IMPROVED: All In One SEO import

**v 0.5.0 / 2019-12-18**
* NEW: Added All In One SEO import
* IMPROVED: Yoast import

**v 0.4.4 / 2019-12-06**
* FIX: Second fix for issue #24 - keywords from Rank Math not being imported

**v 0.4.3 / 2019-12-05**
* FIX: Fix for issue #24 - import from Rank Math not working as expected

**v 0.4.2 / 2019-11-29**
* NEW: Added charts on Search Console overview page
* FIX: minor bug fixes

**v 0.4.1 / 2019-11-28**
* FIX: Fix for PHP error: "Fatal error : Cannot redeclare Classic_SEO::define_constants() in /home/xxx/public_html/xxx/xxx/wp-content/plugins/classicpress-seo/classicpress-seo.php on line 223"

**v 0.4.0 / 2019-11-28**
* NEW: Added support for Google Search Console

**v 0.3.3 / 2019-11-25**
* FIX: Fix for #20: Plugin is not automatically deactivated when using PHP &lt; 7.0.

**v 0.3.2 / 2019-11-20**
* FIX: Fix for #17: PHP error &quot;call_user_func_array() expects parameter 1 to be a valid callback, function on_login not found or invalid function name&quot; 

**v 0.3.1 / 2019-11-18**
* FIX: Classic SEO icon size in admin menu

**v 0.3.0 / 2019-11-18**
* NEW: Plugin has new name. Now called Classic SEO as per [discussions on forums](https://forums.classicpress.net/t/plugin-theme-naming-conventions-when-to-use-classicpress-and-or-cp/1653/8). Note: plugin directory is still called &quot;classicpress-seo&quot; and the main PHP file is called &quot;classicpress-seo.php&quot;. These will be changed in a future version.
* NEW: Added ability to set metabox position on post/page/product edit pages
* NEW: Added support for Pinterest meta tag
* NEW: Added &quot;nosnippet&quot;, &quot;max-snippet:[number]&quot;, &quot;max-video-preview:[number]&quot; and &quot;max-image-preview:[setting]&quot; Advanced Robots Meta settings. See https://webmasters.googleblog.com/2019/09/more-controls-on-search.html
* UPDATED: Schema Types updated including removal of &quot;self-serving&quot; review snippets
* Updated the Help page
* Plugin icon updated

**v 0.2.2 / 2019-10-31**
* Fixed CSS bug which prevented tab icons from displaying correctly on post edit page in admin
* Fixed bug that disabled the sitemap &quot;Include Featured Images&quot; feature
* Improved l8n checks
* Removed a couple of debug messages from assessor.js (forgot they were still there!)
* Removed a couple of unused files

**v 0.2.1 / 2019-10-27**
* Fixed issue with breadcrumbs not displaying

**v 0.2.0 / 2019-10-26**
* Initial public release

== Frequently Asked Questions ==

**Do you guarantee that traffic to my site will increase? Do you guarantee that traffic to my site will not decrease?**
Sorry but no. No SEO plugin can guarantee anything. Classic SEO will help you to write better content and add special tags or markup that are meaningful to Google and other search engines but nobody can guarantee anything where SEO and ranking are concerned.

**Can Classic SEO be used in conjunction with other SEO plugins?**
While technically this may be possible, it is highly inadvisable. It may produce duplicate and unpredictable results. It is strongly recommended that other SEO plugins be deactivated.

**Can I transfer settings from other SEO plugins to Classic SEO?**
Yes, support for Rank Math, Yoast, All In One SEO and The SEO Framework are included.

**What are the technical requirements for using Classic SEO?**
 - ** ClassicPress 1.0.2 **
 - ** PHP 7.0 **
 - ** cURL PHP lib **

**Can I use ClassicPress SEO on a WordPress website?**
While it may technically be possible, Classic SEO is designed to work only on ClassicPress.