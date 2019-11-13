# Classic SEO Plugin (Experimental)

Classic SEO is the first SEO plugin built specifically to work with ClassicPress. The plugin contains many essential SEO tools to help optimize your website.

This plugin was created largely as a result of the following ClassicPress petitions:

- https://petitions.classicpress.net/posts/175/allow-editing-of-page-title-and-meta-description-seo
- https://petitions.classicpress.net/posts/176/auto-xml-sitemap-generation-seo


**Minimum Requirements:**

- ClassicPress 1.0.2
- PHP 7.0

**As this plugin is still in the development & testing stage, please do not use it on a production site.**

## Credits
This is a fork of Rank Math.

## Key functional differences between Classic SEO (v0.3.0-dev) and Rank Math

**Included** | **Not included**
-------------|-----------------
404 Monitor|Setup Wizard
Support for ACF|Support for AMP
Link Counter|Support for bbPress
Local SEO / Knowledge Graph|Support for BuddyPress
Redirections|Search Console
Rich Snippets|Webmaster tools
XML Sitemap|SEO Analysis
WooCommerce|Ability to edit .htaccess
Role Manager|Ability to edit robots.txt
Social meta: Support for Facebook, Twitter, Google Places, LinkedIn, Instagram, YouTube, Pinterest|Social meta: Removed support for Yelp, FourSquare, Flickr, Reddit, SoundCloud, Tumblr and Myspace
Support for [Classic Commerce](https://github.com/ClassicPress-research/classic-commerce) to be added|

## Support
If you find a bug, please create an issue on the [issues page](https://github.com/ClassicPress-research/classicpress-seo/issues)

## Changelog

### v 0.3.0
- NEW: Plugin has new name. Now called Classic SEO as per [discussions on forums](https://forums.classicpress.net/t/plugin-theme-naming-conventions-when-to-use-classicpress-and-or-cp/1653/8)
- NEW: Added ability to set metabox position on post/page/product edit pages
- NEW: Added support for Pinterest meta tag

### v 0.2.2
- Fixed CSS bug which prevented tab icons from displaying correctly on post edit page in admin
- Fixed bug that disabled the sitemap "Include Featured Images" feature
- Improved l8n checks
- Removed a couple of debug messages from assessor.js (forgot they were still there!)
- Removed a couple of unused files

### v 0.2.1
- Fixed issue with breadcrumbs not displaying

### v 0.2.0
- Initial public release
