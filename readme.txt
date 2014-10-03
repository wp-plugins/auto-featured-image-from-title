=== Auto Featured Image from Title ===
Contributors: brochris
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=chris@designsbychris.com
Author URI: http://designsbychris.com
Plugin URI: http://designsbychris.com/auto-featured-image-from-title
Tags: featured image, featured images, generate thumbnail, generate thumbnails, text picture, text pictures, automatic featured image, auto featured image, automatically generate featured image, automatically set featured image
Requires at least: 3.5
Tested up to: 4.0
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically generates an image from the post title of a new post and sets it as the featured image.

== Description ==

This plugin automatically generates an image from the post title of a new post and sets it as the featured image. The image will then be included in your theme wherever the featured image for the post is called for.

== Installation ==

1. Upload to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings (optional, but encouraged)

== Frequently Asked Questions ==

= Why doesn't the generated featured image appear at the top of the post? =

That's up to your Wordpress theme. Some themes do this by default, some don't. If yours doesn't, edit your Wordpress theme and insert the following code where you'd like the featured image to appear.

`<?php
if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
  the_post_thumbnail();
}
?>`

= How can I customize the font, colors, and image to match my site? =

You can customize the generated image somewhat via the options page which can be found in under Settings > Auto Featured Image.

= When does the plugin create an image for my new blog post? =

When you click "Save Draft" or "Update" or "Publish."

= What do I do if I change the title of the post and want the featured image to have the next text? =

Click "Remove featured image." Then when you click "Save Draft" or "Update" or "Publish" it will generate the new image.

= What if I don't want to change the post title, but I want to generate a new image with a different background, colors, or font? =

Go to your media library, and delete the generated image that you no longer want to use. Change the settings you would like to change on the Settings > Auto Featured Image page. Then click "Save Draft" or "Update" or "Publish" on the post, it will generate a new image based on your settings.

== Screenshots ==

1. Admin Settings
2. An example of an automatically generated image
3. An example of an automatically generated image
4. An example of an automatically generated image
5. The image is automatically set as the featured image

== Changelog ==

= 1.0 =
* Initial release

== Upgrade Notice ==

No need to upgrade; it's brand new!