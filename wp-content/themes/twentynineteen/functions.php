<?php
 /* Fire our meta box setup function on the post editor screen. */
add_action('load-post.php', 'videocounter_post_meta_boxes_setup');
add_action('load-post-new.php', 'videocounter_post_meta_boxes_setup');
/* Meta box setup function. */
function videocounter_post_meta_boxes_setup()
{
	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action('add_meta_boxes', 'yc_add_post_meta_boxes');
}
/* Create one or more meta boxes to be displayed on the post editor screen. */
function yc_add_post_meta_boxes()
{
	add_meta_box(
		'total-views',      // Unique ID
		esc_html__('Vide Details', 'example'),    // Title
		'yc_show_post_class_meta_box',   // Callback function
		'youtubecounter',         // Admin page (or post type)
		'normal',         // Context
		'default'        // Priority
	);

	// video thumbnil.
	add_meta_box(
		'_video_thumbnail_',      // Unique ID
		esc_html__('Vide Details'),    // Title
		'yc_show_post_class_meta_box2',   // Callback function
		'youtubecounter',         // Admin page (or post type)
		'normal',         // Context
		'default'        // Priority
	);
}
function yc_show_post_class_meta_box2($post)
{ ?>
<?php wp_nonce_field(basename(__FILE__), 'yc_post_class_nonce'); ?>
<p>
    <label>Video Thumbnail</label>
    <br />
    <img src="https://sspride.org/wp-content/uploads/2017/03/image-placeholder-500x500-300x300.jpg" width="100" height="100" id="video_thumbnail" />
    <input class="widefat" type="text" name="_video_thumbnail_" id="_video_thumbnail_" value="<?php echo esc_attr(get_post_meta($post->ID, '_video_thumbnail_', true)); ?>" size="30" style="display:none;" />
</p>
<?php

}
/* Display the post meta box. */
function yc_show_post_class_meta_box($post)
{ ?>
<?php wp_nonce_field(basename(__FILE__), 'yc_post_class_nonce'); ?>
<p>
    <label>Total Views</label>
    <br />
    <input class="widefat" type="text" name="total-views" id="total-views" value="<?php echo esc_attr(get_post_meta($post->ID, '_total_views_', true)); ?>" size="30" />
</p>
<p>
    <input type="button" value="Fetch Thumbnail and Views From Youtube" id="fetch">
</p>
<?php

}
/* Save post meta on the 'save_post' hook. */
add_action('save_post', 'yc_save_post_class_meta', 10, 2);
/* Save the meta box's post metadata. */
function yc_save_post_class_meta($post_id, $post)
{
	/* Verify the nonce before proceeding. */
	if (!isset($_POST['yc_post_class_nonce']) || !wp_verify_nonce($_POST['yc_post_class_nonce'], basename(__FILE__)))
		return $post_id;
	/* Get the post type object. */
	$post_type = get_post_type_object($post->post_type);
	/* Check if the current user has permission to edit the post. */
	if (!current_user_can($post_type->cap->edit_post, $post_id))
		return $post_id;
	/* Get the posted data and sanitize it for use as an HTML class. */
	$new_meta_value = (isset($_POST['total-views']) ? sanitize_html_class($_POST['total-views']) : ’);
	$new_meta_value2 = (isset($_POST['_video_thumbnail_']) ? sanitize_html_class($_POST['_video_thumbnail_']) : ’); // get video thumbnail.
	/* Get the meta key. */
	$meta_key = '_total_views_';
	$meta_key_video_thumbnail_ = '_video_thumbnail_';

	/* Get the meta value of the custom field key. */
	$meta_value = get_post_meta($post_id, $meta_key, true);
	$meta_key_video_thumbnail_ = get_post_meta($post_id, $meta_key_video_thumbnail_, true); // thumbnail.


	/* If a new meta value was added and there was no previous value, add it. */
	if ($new_meta_value && '’' == $meta_value)
		add_post_meta($post_id, $meta_key, $new_meta_value, true);
	/* If the new meta value does not match the old value, update it. */
	elseif ($new_meta_value && $new_meta_value != $meta_value)
		update_post_meta($post_id, $meta_key, $new_meta_value);
	/* If there is no new meta value but an old value exists, delete it. */
	elseif ('’' == $new_meta_value && $meta_value)
		delete_post_meta($post_id, $meta_key, $meta_value);

	// --------------------------------------------------------------------------------------------------------------------------------------------
	/* If a new meta value was added and there was no previous value, add it. */
	if ($new_meta_value2 && '’' == $meta_key_video_thumbnail_)
		add_post_meta($post_id, $meta_key_video_thumbnail_, $new_meta_value2, true);


	/* If the new meta value does not match the old value, update it. */
	// elseif ($new_meta_value_video_thumbnail_ && $new_meta_value_video_thumbnail_ != $meta_key_video_thumbnail_)
	// 	update_post_meta($post_id, $meta_key_video_thumbnail_, $new_meta_value_video_thumbnail_);
	// /* If there is no new meta value but an old value exists, delete it. */
	// elseif ('’' == $new_meta_value_video_thumbnail_ && $meta_key_video_thumbnail_)
	// 	delete_post_meta($post_id, $meta_key_video_thumbnail_, $meta_key_video_thumbnail_);
}
function admin_js()
{ ?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('#fetch').on('click', function() {
            // console.log(jQuery('#title').val());
            // get only video id from string.
            var video_link = jQuery('#title').val(); // get video link.
            var id = video_link.substr(32, 11);
            var url = "https://content.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails%2Cstatistics&id=" + id + "&key=AIzaSyAblvIArQ_G37jRqlR8xORi-_w21v8fCn8";
            // ajax request
            jQuery.get(url, function(data, status) {
                // alert("Data: " + data + "\nStatus: " + status);
                jQuery("#video_thumbnail").attr("src", data.items[0].snippet.thumbnails.default.url); // add video thumbnail src
                jQuery("#_video_thumbnail_").attr("value", data.items[0].snippet.thumbnails.default.url); // add video thumbnail src
                jQuery("input#total-views").val(data.items[0].statistics.viewCount); // add total views
                // alert(data.items[0].snippet.thumbnails.default.url);
                // console.log(data.items[0]);
                // console.log(data);
            });
        });
        // convert checkboxes to radio
        jQuery('form#post').find('.categorychecklist input').each(function() {
            var new_input = jQuery('<input type="radio" />'),
                attrLen = this.attributes.length;
            for (i = 0; i < attrLen; i++) {
                if (this.attributes[i].name != 'type') {
                    new_input.attr(this.attributes[i].name.toLowerCase(), this.attributes[i].value);
                }
            }
            jQuery(this).replaceWith(new_input);
        });
    });
</script>
<?php

}
add_action('admin_head', 'admin_js');








/**
 * Twenty Nineteen functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */
/**
 * Twenty Nineteen only works in WordPress 4.7 or later.
 */
if (version_compare($GLOBALS['wp_version'], '4.7', '<')) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}
if (!function_exists('twentynineteen_setup')) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function twentynineteen_setup()
	{
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Twenty Nineteen, use a find and replace
		 * to change 'twentynineteen' to the name of your theme in all the template files.
		 */
		load_theme_textdomain('twentynineteen', get_template_directory() . '/languages');
		// Add default posts and comments RSS feed links to head.
		add_theme_support('automatic-feed-links');
		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support('title-tag');
		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support('post-thumbnails');
		set_post_thumbnail_size(1568, 9999);
		// This theme uses wp_nav_menu() in two locations.
		register_nav_menus(
			array(
				'menu-1' => __('Primary', 'twentynineteen'),
				'footer' => __('Footer Menu', 'twentynineteen'),
				'social' => __('Social Links Menu', 'twentynineteen'),
			)
		);
		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);
		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 190,
				'width'       => 190,
				'flex-width'  => false,
				'flex-height' => false,
			)
		);
		// Add theme support for selective refresh for widgets.
		add_theme_support('customize-selective-refresh-widgets');
		// Add support for Block Styles.
		add_theme_support('wp-block-styles');
		// Add support for full and wide align images.
		add_theme_support('align-wide');
		// Add support for editor styles.
		add_theme_support('editor-styles');
		// Enqueue editor styles.
		add_editor_style('style-editor.css');
		// Add custom editor font sizes.
		add_theme_support(
			'editor-font-sizes',
			array(
				array(
					'name'      => __('Small', 'twentynineteen'),
					'shortName' => __('S', 'twentynineteen'),
					'size'      => 19.5,
					'slug'      => 'small',
				),
				array(
					'name'      => __('Normal', 'twentynineteen'),
					'shortName' => __('M', 'twentynineteen'),
					'size'      => 22,
					'slug'      => 'normal',
				),
				array(
					'name'      => __('Large', 'twentynineteen'),
					'shortName' => __('L', 'twentynineteen'),
					'size'      => 36.5,
					'slug'      => 'large',
				),
				array(
					'name'      => __('Huge', 'twentynineteen'),
					'shortName' => __('XL', 'twentynineteen'),
					'size'      => 49.5,
					'slug'      => 'huge',
				),
			)
		);
		// Editor color palette.
		add_theme_support(
			'editor-color-palette',
			array(
				array(
					'name'  => __('Primary', 'twentynineteen'),
					'slug'  => 'primary',
					'color' => twentynineteen_hsl_hex('default' === get_theme_mod('primary_color') ? 199 : get_theme_mod('primary_color_hue', 199), 100, 33),
				),
				array(
					'name'  => __('Secondary', 'twentynineteen'),
					'slug'  => 'secondary',
					'color' => twentynineteen_hsl_hex('default' === get_theme_mod('primary_color') ? 199 : get_theme_mod('primary_color_hue', 199), 100, 23),
				),
				array(
					'name'  => __('Dark Gray', 'twentynineteen'),
					'slug'  => 'dark-gray',
					'color' => '#111',
				),
				array(
					'name'  => __('Light Gray', 'twentynineteen'),
					'slug'  => 'light-gray',
					'color' => '#767676',
				),
				array(
					'name'  => __('White', 'twentynineteen'),
					'slug'  => 'white',
					'color' => '#FFF',
				),
			)
		);
		// Add support for responsive embedded content.
		add_theme_support('responsive-embeds');
	}
endif;
add_action('after_setup_theme', 'twentynineteen_setup');
/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function twentynineteen_widgets_init()
{
	register_sidebar(
		array(
			'name'          => __('Footer', 'twentynineteen'),
			'id'            => 'sidebar-1',
			'description'   => __('Add widgets here to appear in your footer.', 'twentynineteen'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action('widgets_init', 'twentynineteen_widgets_init');
/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width Content width.
 */
function twentynineteen_content_width()
{
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters('twentynineteen_content_width', 640);
}
add_action('after_setup_theme', 'twentynineteen_content_width', 0);
/**
 * Enqueue scripts and styles.
 */
function twentynineteen_scripts()
{
	wp_enqueue_style('twentynineteen-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));
	wp_style_add_data('twentynineteen-style', 'rtl', 'replace');
	if (has_nav_menu('menu-1')) {
		wp_enqueue_script('twentynineteen-priority-menu', get_theme_file_uri('/js/priority-menu.js'), array(), '1.1', true);
		wp_enqueue_script('twentynineteen-touch-navigation', get_theme_file_uri('/js/touch-keyboard-navigation.js'), array(), '1.1', true);
	}
	wp_enqueue_style('twentynineteen-print-style', get_template_directory_uri() . '/print.css', array(), wp_get_theme()->get('Version'), 'print');
	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'twentynineteen_scripts');
/**
 * Fix skip link focus in IE11.
 *
 * This does not enqueue the script because it is tiny and because it is only for IE11,
 * thus it does not warrant having an entire dedicated blocking script being loaded.
 *
 * @link https://git.io/vWdr2
 */
function twentynineteen_skip_link_focus_fix()
{
	// The following is minified via `terser --compress --mangle -- js/skip-link-focus-fix.js`.
	?>
<script>
    /(trident|msie)/i.test(navigator.userAgent) && document.getElementById && window.addEventListener && window.addEventListener("hashchange", function() {
        var t, e = location.hash.substring(1);
        /^[A-z0-9_-]+$/.test(e) && (t = document.getElementById(e)) && (/^(?:a|select|input|button|textarea)$/i.test(t.tagName) || (t.tabIndex = -1), t.focus())
    }, !1);
</script>
<?php

}
add_action('wp_print_footer_scripts', 'twentynineteen_skip_link_focus_fix');
/**
 * Enqueue supplemental block editor styles.
 */
function twentynineteen_editor_customizer_styles()
{
	wp_enqueue_style('twentynineteen-editor-customizer-styles', get_theme_file_uri('/style-editor-customizer.css'), false, '1.1', 'all');
	if ('custom' === get_theme_mod('primary_color')) {
		// Include color patterns.
		require_once get_parent_theme_file_path('/inc/color-patterns.php');
		wp_add_inline_style('twentynineteen-editor-customizer-styles', twentynineteen_custom_colors_css());
	}
}
add_action('enqueue_block_editor_assets', 'twentynineteen_editor_customizer_styles');
/**
 * Display custom color CSS in customizer and on frontend.
 */
function twentynineteen_colors_css_wrap()
{
	// Only include custom colors in customizer or frontend.
	if ((!is_customize_preview() && 'default' === get_theme_mod('primary_color', 'default')) || is_admin()) {
		return;
	}
	require_once get_parent_theme_file_path('/inc/color-patterns.php');
	$primary_color = 199;
	if ('default' !== get_theme_mod('primary_color', 'default')) {
		$primary_color = get_theme_mod('primary_color_hue', 199);
	}
	?>
<style type="text/css" id="custom-theme-colors" <?php echo is_customize_preview() ? 'data-hue="' . absint($primary_color) . '"' : ''; ?>>
    <?php echo twentynineteen_custom_colors_css();
		?>
</style>
<?php

}
add_action('wp_head', 'twentynineteen_colors_css_wrap');
/**
 * SVG Icons class.
 */
require get_template_directory() . '/classes/class-twentynineteen-svg-icons.php';
/**
 * Custom Comment Walker template.
 */
require get_template_directory() . '/classes/class-twentynineteen-walker-comment.php';
/**
 * Enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';
/**
 * SVG Icons related functions.
 */
require get_template_directory() . '/inc/icon-functions.php';
/**
 * Custom template tags for the theme.
 */
require get_template_directory() . '/inc/template-tags.php';
/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';
