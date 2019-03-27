<?php
function total_views_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

function total_views_add_meta_box() {
	add_meta_box(
		'total_views-total-views',
		__( 'Total Views', 'total_views' ),
		'total_views_html',
		'youtubecounter',
		'normal',
		'low'
	);


	add_meta_box(
		'video_thumbnail-video-thumbnail',
		__( 'Video Thumbnail', 'video_thumbnail' ),
		'video_thumbnail_html',
		'youtubecounter',
		'normal',
		'default'
	);

}
add_action( 'add_meta_boxes', 'total_views_add_meta_box' );

function total_views_html( $post) {
	wp_nonce_field( '_total_views_nonce', 'total_views_nonce' ); ?>
	<p>
		<label for="total_views_total_views"><?php _e( 'Total Views', 'total_views' ); ?></label><br>
		<input type="text" name="total_views_total_views" id="total_views_total_views" value="<?php echo total_views_get_meta( 'total_views_total_views' ); ?>">
	</p>
	<p>
		<input type="button" value="Fetch Thumbnail and Views From Youtube" id="fetch">
	</p>

	<?php
}

function total_views_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['total_views_nonce'] ) || ! wp_verify_nonce( $_POST['total_views_nonce'], '_total_views_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['total_views_total_views'] ) )
		update_post_meta( $post_id, 'total_views_total_views', esc_attr( $_POST['total_views_total_views'] ) );
}
add_action( 'save_post', 'total_views_save' );



function video_thumbnail_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}


function video_thumbnail_html( $post) {
	wp_nonce_field( '_video_thumbnail_nonce', 'video_thumbnail_nonce' ); ?>
	<p>
		<label for="video_thumbnail_video_thumbnail"><?php _e( 'Video Thumbnail', 'video_thumbnail' ); ?></label><br>

		<?php
		$link = video_thumbnail_get_meta( 'video_thumbnail_video_thumbnail' );
		if ( $link != false ) {
			$src = $link;
		} else {
			$src = "https://sspride.org/wp-content/uploads/2017/03/image-placeholder-500x500-300x300.jpg";
		}
		?>
<img src="<?php echo $src; ?>" width="100" height="100" id="video_thumbnail" />

<input type="text" name="video_thumbnail_video_thumbnail" id="video_thumbnail_video_thumbnail" value="<?php echo video_thumbnail_get_meta( 'video_thumbnail_video_thumbnail' ); ?>" style="display: none;">

</p>
	<?php
}

function video_thumbnail_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['video_thumbnail_nonce'] ) || ! wp_verify_nonce( $_POST['video_thumbnail_nonce'], '_video_thumbnail_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['video_thumbnail_video_thumbnail'] ) )
		update_post_meta( $post_id, 'video_thumbnail_video_thumbnail', esc_attr( $_POST['video_thumbnail_video_thumbnail'] ) );
}
add_action( 'save_post', 'video_thumbnail_save' );












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
                jQuery("#video_thumbnail_video_thumbnail").attr("value", data.items[0].snippet.thumbnails.default.url); // add video thumbnail src
                jQuery("input#total_views_total_views").val(data.items[0].statistics.viewCount); // add total views
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








// Add Shortcode
function custom_shortcode() {

$terms = get_terms([
    'taxonomy' => 'youtubeactors',
    'hide_empty' => false,
]);


// var_dump($terms);


// $the_query = new WP_Query( array(
//     'post_type' => 'Adverts',
//     'tax_query' => array(
//         array (
//             'taxonomy' => 'youtubeactors',
//             'field' => 'slug',
//             'terms' => 'politics',
//         )
//     ),
// ) );

// while ( $the_query->have_posts() ) :
//     $the_query->the_post();
//     // Show Posts ...


// endwhile;


	$str = "<div class=table-responsive-xl>
<table class='table table-condensed'>
		    <thead>
		      <tr>
		        <th>Artists</th>
		        <th>&nbsp;</th>
		      </tr>
		    </thead>
		    <tbody>";

 foreach ($terms as $value) {

  $str .= "
  		<tr>
			<td>". $value->slug ."</td>
			<td>v</td>
			<td>v</td>
		</tr>
  ";

 }

 	 $str .= "</tbody> </table></div>";

	return $str;

}
add_shortcode( 'youtubecounter', 'custom_shortcode' );



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
