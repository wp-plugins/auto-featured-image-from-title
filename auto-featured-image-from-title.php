<?php
/*
Plugin Name: Auto Featured Image from Title
Version: 1.0
Description: Automatically generates an image from the post title and sets it as the featured image
Author: Chris Huff
Author URI: http://designsbychris.com
Plugin URI: http://designsbychris.com/auto-featured-image-from-title
License: GPLv2 or later
*/

function auto_featured_image_from_title ($post_id) {

	global $post;

	// Check to see if the post already has a featured image
	if ( '' != get_the_post_thumbnail($post->ID) )
	return;

	// If this is just a revision, don't update the featured image
	if ( wp_is_post_revision($post->ID) )
	return;

	// Make sure a title has been given to the post
	$post_title = html_entity_decode(get_the_title($post->ID),ENT_QUOTES,'UTF-8');
	if (( $post_title == '' ) || ( $post_title == 'Auto Draft' ))
	return;

	// Set options if they don't exist yet
	add_option('auto_image_width',640);
	add_option('auto_image_height',360);
	add_option('auto_image_bg_image',"sunset.jpg");
	add_option('auto_image_bg_color',"#b5b5b5");
	add_option('auto_image_fontface',"chunkfive.ttf");
	add_option('auto_image_fontsize',"72");
	add_option('auto_image_text_color',"#fff76d");
	add_option('auto_image_shadow',"yes");
	add_option('auto_image_shadow_color',"#000000");

	// Get options from database
	$auto_image_width = get_option('auto_image_width');
	$auto_image_height = get_option('auto_image_height');
	$auto_image_bg_image = get_option('auto_image_bg_image');
	$auto_image_bg_color = get_option('auto_image_bg_color');
	$auto_image_fontface = get_option('auto_image_fontface');
	$auto_image_fontsize = get_option('auto_image_fontsize');
	$auto_image_text_color = get_option('auto_image_text_color');
	$auto_image_shadow = get_option('auto_image_shadow');
	$auto_image_shadow_color = get_option('auto_image_shadow_color');

	// Separate hexidecimal colors into red, green, blue strings
	function afift_hex2rgbcolors($c){
		$c = str_replace("#", "", $c);
		if(strlen($c) == 3){
			$r = hexdec( $c[0] . $c[1] );
			$g = hexdec( $c[1] . $c[1] );
			$b = hexdec( $c[2] . $c[1] );
			}
		else if (strlen($c) == 6 ){
			$r = hexdec( $c[0] . $c[2] );
			$g = hexdec( $c[2] . $c[2] );
			$b = hexdec( $c[4] . $c[2] );
			}
		else {
			$r = "ff";
			$g = "ff";
			$b = "ff";
			}
		return Array("red" => $r, "green" => $g, "blue" => $b);
		}
	$bg = afift_hex2rgbcolors($auto_image_bg_color);
	$text = afift_hex2rgbcolors($auto_image_text_color);
	$shadow = afift_hex2rgbcolors($auto_image_shadow_color);

	// Set up some variables
	$post_slug = str_replace(' ', '-', $post_title);
	$post_slug = preg_replace('/[^A-Za-z0-9\-]/', '', $post_slug);
	$pluginsdir = str_replace($_SERVER["SERVER_NAME"],'',plugins_url());
	$pluginsdir = str_replace('http://','',$pluginsdir);
	$backgroundimg = $_SERVER["DOCUMENT_ROOT"] . $pluginsdir . "/auto-featured-image-from-title/images/$auto_image_bg_image";
	$auto_image_shadow_y = $auto_image_height+1;
	$topoftext = $auto_image_height+2;
	$offset = 0;
	$i = 0;

	// Start generating the image
	$new_featured_img = imagecreatefromjpeg($backgroundimg);
	if($auto_image_bg_image!="blank.jpg"){

		$width = imagesx($new_featured_img);
		$height = imagesy($new_featured_img);

		$original_aspect = $width / $height;
		$thumb_aspect = $auto_image_width / $auto_image_height;

		if ( $original_aspect >= $thumb_aspect ){
			// If image is wider than thumbnail (in aspect ratio sense)
			$new_height = $auto_image_height;
			$new_width = $width / ($height / $auto_image_height);
			}
		else {
			// If the thumbnail is wider than the image
			$new_width = $auto_image_width;
			$new_height = $height / ($width / $auto_image_width);
			}

		$thumb = imagecreatetruecolor( $auto_image_width, $auto_image_height );

		// Resize and crop
		imagecopyresampled(
			$thumb,
			$new_featured_img,
			0 - ($new_width - $auto_image_width) / 2, // Center the image horizontally
			0 - ($new_height - $auto_image_height) / 2, // Center the image vertically
			0, 0,
			$new_width, $new_height,
			$width, $height);
		}

	$text_color = imagecolorallocate( $new_featured_img, $text["red"], $text["green"], $text["blue"]);
	$shadow_color = imagecolorallocate( $new_featured_img, $shadow["red"], $shadow["green"], $shadow["blue"]);
	$font = plugin_dir_path( __FILE__ ) . "fonts/" . $auto_image_fontface;
	$words = explode(" ", $post_title);

	while($topoftext > ($auto_image_height-$auto_image_shadow_y)){

		if(($topoftext > ($auto_image_height-$auto_image_shadow_y)) && ($auto_image_bg_image!="blank.jpg")){
//		if($topoftext > ($auto_image_height-$auto_image_shadow_y)){
			if($new_featured_image){imagedestroy($new_featured_img);};
			$new_featured_img = imagecreatefromjpeg($backgroundimg);

			$width = imagesx($new_featured_img);
			$height = imagesy($new_featured_img);

			$original_aspect = $width / $height;
			$thumb_aspect = $auto_image_width / $auto_image_height;

			if ( $original_aspect >= $thumb_aspect ){
				// If image is wider than thumbnail (in aspect ratio sense)
				$new_height = $auto_image_height;
				$new_width = $width / ($height / $auto_image_height);
				}
			else {
				// If the thumbnail is wider than the image
				$new_width = $auto_image_width;
				$new_height = $height / ($width / $auto_image_width);
				}

			$thumb = imagecreatetruecolor( $auto_image_width, $auto_image_height );

			// Resize and crop
			imagecopyresampled(
				$thumb,
				$new_featured_img,
				0 - ($new_width - $auto_image_width) / 2, // Center the image horizontally
				0 - ($new_height - $auto_image_height) / 2, // Center the image vertically
				0, 0,
				$new_width, $new_height,
				$width, $height);
			$new_featured_img = $thumb;
			imagealphablending($new_featured_img, true);
			imagesavealpha($new_featured_img, true);
			$trans_layer_overlay = imagecolorallocatealpha($new_featured_img, 220, 220, 220, 127);
			imagefill($new_featured_img, 0, 0, $trans_layer_overlay);
			}

		if($auto_image_bg_image=="blank.jpg"){
			$new_featured_img = imagecreatetruecolor($auto_image_width, $auto_image_height);
			$background_color = imagecolorallocate( $new_featured_img, $bg["red"], $bg["green"], $bg["blue"]);
			imagefill($new_featured_img, 0, 0, $background_color);
			}


		// Center the text
		$offset++;
		$auto_image_text_array = imagettfbbox($auto_image_fontsize, 0, $font, $post_title);
		$auto_image_text_x = ceil(($auto_image_width - $auto_image_text_array[2]) / 2);
		$auto_image_text_y = ceil(($auto_image_height/2)+($auto_image_fontsize/4)) - $offset;

		$string = "";
		$tmp_string = "";
		$lineheight = $auto_image_fontsize*1.5;

		for($i = 0; $i < count($words); $i++) {
			if($i==0){
				$topoftext = $auto_image_text_y;
				}

			$tmp_string .= $words[$i]." ";

			//check size of string
			$dim = imagettfbbox($auto_image_fontsize, 0, $font, $tmp_string);

			if($dim[4] < $auto_image_width) { 
				$string = $tmp_string;
			} else {
				$i--;
				$tmp_string = "";
				$auto_image_text_array = imagettfbbox($auto_image_fontsize, 0, $font, $string);
				$auto_image_text_x = ceil(($auto_image_width - $auto_image_text_array[2]) / 2);
				$auto_image_shadow_x = $auto_image_text_x + 2;
				$auto_image_shadow_y = $auto_image_text_y + 2;
				if($auto_image_shadow=='yes'){
					imagettftext($new_featured_img, $auto_image_fontsize, 0, $auto_image_shadow_x, $auto_image_shadow_y, $shadow_color, $font, $string);
					}
				imagettftext($new_featured_img, $auto_image_fontsize, 0, $auto_image_text_x, $auto_image_text_y, $text_color, $font, $string); 

				$string = "";
				$auto_image_text_y += $lineheight;
				}
			if($auto_image_shadow_y > $auto_image_height){
				$auto_image_fontsize--;
				}
			}

		$auto_image_text_array = imagettfbbox($auto_image_fontsize, 0, $font, $string);
		$auto_image_text_x = ceil(($auto_image_width - $auto_image_text_array[2]) / 2);
		$auto_image_shadow_x = $auto_image_text_x + 2;
		$auto_image_shadow_y = $auto_image_text_y + 2;
		}

	if($auto_image_shadow=='yes'){
		imagettftext($new_featured_img, $auto_image_fontsize, 0, $auto_image_shadow_x, $auto_image_shadow_y, $shadow_color, $font, $string);
		}
	imagettftext($new_featured_img, $auto_image_fontsize, 0, $auto_image_text_x, $auto_image_text_y, $text_color, $font, $string);

	// Save the image
	$upload_dir = wp_upload_dir();
	$newimg = $upload_dir['path'] . "/$post_slug.png";
	imagepng( $new_featured_img, $newimg );
	imagecolordeallocate( $new_featured_img, $text_color );
	imagecolordeallocate( $new_featured_img, $background_color );
	imagecolordeallocate( $new_featured_img, $shadow_color );

	// Process the image into the Media Library
	$newimg_url = $upload_dir['url'] . "/$post_slug.png";
	$attachment = array(
		'guid'           => $newimg_url, 
		'post_mime_type' => 'image/png',
		'post_title'     => $post_title,
		'post_content'   => '',
		'post_status'    => 'inherit'
		);
	$attach_id = wp_insert_attachment( $attachment, $newimg, $post->ID );
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	$attach_data = wp_generate_attachment_metadata( $attach_id, $newimg );
	wp_update_attachment_metadata( $attach_id, $attach_data );

	// Set the image as the featured image
	set_post_thumbnail( $post_id, $attach_id );
	}

add_action( 'save_post', 'auto_featured_image_from_title' );

// Use the color picker on the admin page
add_action( 'admin_enqueue_scripts', 'mw_enqueue_color_picker' );
function mw_enqueue_color_picker( $hook_suffix ) {
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'auto_featured_image', plugins_url('colorpicker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}

add_action('admin_menu', 'afift_settings');

// Set up the admin page
function afift_settings() {

	//create new top-level menu
	add_options_page('Auto Featured Image', 'Auto Featured Image', 'manage_options', 'auto-featured-image-from-title.php', 'afift_settings_page');
	add_filter( "plugin_action_links", "afift_settings_link", 10, 2 );
	//call register settings function
	add_action( 'admin_init', 'register_auto_featured_image' );
}

function afift_settings_link($links, $file) {
	static $this_plugin;
		if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
		if ($file == $this_plugin){
	$afift_settings_link = '<a href="options-general.php?page=auto-featured-image-from-title.php">'.__("Settings", "auto-featured-image-from-title").'</a>';
		array_unshift($links, $afift_settings_link);
		}
	return $links;
	}

function register_auto_featured_image() {
	//register our settings
	register_setting( 'auto_featured_image_group', 'auto_image_width' );
	register_setting( 'auto_featured_image_group', 'auto_image_height' );
	register_setting( 'auto_featured_image_group', 'auto_image_bg_color' );
	register_setting( 'auto_featured_image_group', 'auto_image_bg_image' );
	register_setting( 'auto_featured_image_group', 'auto_image_fontface' );
	register_setting( 'auto_featured_image_group', 'auto_image_fontsize' );
	register_setting( 'auto_featured_image_group', 'auto_image_text_color' );
	register_setting( 'auto_featured_image_group', 'auto_image_shadow' );
	register_setting( 'auto_featured_image_group', 'auto_image_shadow_color' );
}

function afift_css_head() { ?>
<style type="text/css">

#afift {margin-right:300px;}
#afift_settings, #afift_info {background-color:#fff;border:#ccc 1px solid; padding:15px;}
#afift_settings {float:left;width:100%;}
#afift_info {float:right;margin-right:-280px;width:200px;}
#afift_settings label {width:200px;display:table;}
#afift_settings .bg_group {text-align:center;padding:10px;width:240px;float:left;}
.showfonts {position:relative;color:#00f;}
.showfonts span {display:none;}
.showfonts:hover span {display:block;position:absolute;top:0px;left:60px;background-color:#fff;border:#aaa 1px solid;padding:5px;width:155px;}

</style>

<?php }
add_action('admin_head', 'afift_css_head');

function afift_settings_page() { ?>

<div id="afift">
<h2>Auto Featured Image From Title</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'auto_featured_image_group' ); ?>
        <div id="afift_settings">
        <p><label for="auto_image_width">Width:</label>
		<input name="auto_image_width" type="text" id="auto_image_width" value="<?php form_option('auto_image_width'); ?>" /></p>
        <p><label for="auto_image_height">Height:</label>
		<input name="auto_image_height" type="text" id="auto_image_height" value="<?php form_option('auto_image_height'); ?>" /></p>
        <p><label for="auto_image_fontface">Font: <small class="showfonts">Show fonts<span><img src="<?php echo plugins_url() ?>/auto-featured-image-from-title/images/ChunkFive.png"><img src="<?php echo plugins_url() ?>/auto-featured-image-from-title/images/CaviarDreams.png"><img src="<?php echo plugins_url() ?>/auto-featured-image-from-title/images/Windsong.png"></span></small></label>
		<select name="auto_image_fontface" id="auto_image_fontface">
			<option value='chunkfive.ttf'<?php if((get_option('auto_image_fontface'))=='chunkfive.ttf'){ echo " selected";} ?>>Chunk Five</option>
			<option class="Windsong" value='Windsong.ttf'<?php if((get_option('auto_image_fontface'))=='Windsong.ttf'){ echo " selected";} ?>>Windsong</option>
			<option class="CaviarDreams" value='CaviarDreams.ttf'<?php if((get_option('auto_image_fontface'))=='CaviarDreams.ttf'){ echo " selected";} ?>>Caviar Dreams</option>
		</select></p>
        <p><label for="auto_image_fontsize">Font Size (in pixels):</label>
		<input name="auto_image_fontsize" type="text" id="auto_image_fontsize" value="<?php form_option('auto_image_fontsize'); ?>" /></p>
	<p><label for="auto_image_text_color">Text Color:</label>
		<input name="auto_image_text_color" type="text" value="<?php form_option('auto_image_text_color'); ?>" class="my-color-field" /></p>
        <p><label for="auto_image_shadow">Text Shadow:</label>
		<select name="auto_image_shadow" id="auto_image_shadow">
			<option value='yes'<?php if((get_option('auto_image_shadow'))=='yes'){ echo " selected";} ?>>Yes</option>
			<option value='no'<?php if((get_option('auto_image_shadow'))=='no'){ echo " selected";} ?>>No</option>
		</select></p>
	<p><label for="auto_image_shadow_color">Shadow Color:</label>
		<input name="auto_image_shadow_color" type="text" value="<?php form_option('auto_image_shadow_color'); ?>" class="my-color-field" /></p>
	<p><label for="auto_image_bg_color">Background Color:</label>
		<input name="auto_image_bg_color" type="text" value="<?php form_option('auto_image_bg_color'); ?>" class="my-color-field" /></p>
	<p><label for="auto_image_bg_image">Background Image:</label>
		<span class="bg_group"><img src="<?php echo plugins_url() ?>/auto-featured-image-from-title/images/sunset-240x112.jpg" width="240" height="112" alt="Sunset" /><br /><input type="radio" id="auto_image_bg_image" name="auto_image_bg_image"<?php if(get_option('auto_image_bg_image')=="sunset.jpg"){echo ' checked="checked"';} ?> value="sunset.jpg" /></span>
		<span class="bg_group"><img src="<?php echo plugins_url() ?>/auto-featured-image-from-title/images/flower-240x112.jpg" width="240" height="112" alt="Flower" /><br /><input type="radio" id="auto_image_bg_image" name="auto_image_bg_image"<?php if(get_option('auto_image_bg_image')=="flower.jpg"){echo ' checked="checked"';} ?> value="flower.jpg" /></span>
		<span class="bg_group"><img src="<?php echo plugins_url() ?>/auto-featured-image-from-title/images/book-240x112.jpg" width="240" height="112" alt="Book" /><br /><input type="radio" id="auto_image_bg_image" name="auto_image_bg_image"<?php if(get_option('auto_image_bg_image')=="book.jpg"){echo ' checked="checked"';} ?> value="book.jpg" /></span>
		<span class="bg_group"><img src="<?php echo plugins_url() ?>/auto-featured-image-from-title/images/grunge-240x112.jpg" width="240" height="112" alt="Grunge" /><br /><input type="radio" id="auto_image_bg_image" name="auto_image_bg_image"<?php if(get_option('auto_image_bg_image')=="grunge.jpg"){echo ' checked="checked"';} ?> value="grunge.jpg" /></span>
		<span class="bg_group"><img src="<?php echo plugins_url() ?>/auto-featured-image-from-title/images/bokeh-240x112.jpg" width="240" height="112" alt="Bokeh" /><br /><input type="radio" id="auto_image_bg_image" name="auto_image_bg_image"<?php if(get_option('auto_image_bg_image')=="bokeh.jpg"){echo ' checked="checked"';} ?> value="bokeh.jpg" /></span>
		<span class="bg_group"><img src="<?php echo plugins_url() ?>/auto-featured-image-from-title/images/grass-hill-240x112.jpg" width="240" height="112" alt="Grass Hill" /><br /><input type="radio" id="auto_image_bg_image" name="auto_image_bg_image"<?php if(get_option('auto_image_bg_image')=="grass-hill.jpg"){echo ' checked="checked"';} ?> value="grass-hill.jpg" /></span>
		<span class="bg_group"><img src="<?php echo plugins_url() ?>/auto-featured-image-from-title/images/clouds-240x112.jpg" width="240" height="112" alt="Clouds" /><br /><input type="radio" id="auto_image_bg_image" name="auto_image_bg_image"<?php if(get_option('auto_image_bg_image')=="clouds.jpg"){echo ' checked="checked"';} ?> value="clouds.jpg" /></span>
		<span class="bg_group"><img src="<?php echo plugins_url() ?>/auto-featured-image-from-title/images/blank-240x112.jpg" width="240" height="112" alt="Blank" /><br /><input type="radio" id="auto_image_bg_image" name="auto_image_bg_image"<?php if(get_option('auto_image_bg_image')=="blank.jpg"){echo ' checked="checked"';} ?> value="blank.jpg" /></span>
	</p>
	<p><input type="submit" value="Save Changes" /></p>

</form>

</div>

<div id="afift_info">

	<strong>Support Development</strong><br /><br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="QHWFUH3H97TWJ">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

	<br /><br />

	<strong>Font Licenses:</strong><br />
	<small><a href="http://www.fontsquirrel.com/license/ChunkFive">ChunkFive</a> | <a href="http://www.fontsquirrel.com/license/Windsong">Windsong</a> | <a href="http://www.fontsquirrel.com/license/Caviar-Dreams">Caviar Dreams</a></small>

</div>

<?php }

?>