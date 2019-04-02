<?php
/**
 * Plugin Name:       Youtube Counter
 * Plugin URI:        www.youtubecounter.com
 * Description:       Task Task plugin given by explorelogic
 * Version:           1.0.0
 * Author:            Hamza
*/
/**
 * Flush our rewrite rules on deactivation.
 */
function deactivation_youtubecounter()
{
    flush_rewrite_rules(); // flush urls.

    unregister_post_type('youtubecounter'); // remove/unregister youtubecounter post type.
    unregister_taxonomy("youtubeactors");
}
register_deactivation_hook(__FILE__, 'plugin_deactivation_');
/**
 * Register post type and taxonomy.
 */
add_action('init', 'register_taxonomy_youtube_actors');
add_action('init', 'register_post_type_youtubecounter');
/**
 * Register Taxonomy Youtube Actors.
 */
function register_taxonomy_youtube_actors()
{
    $labels = array(
        "name" => __("Youtube Actors", "twentynineteen"),
        "singular_name" => __("Youtube Actor", "twentynineteen"),
    );

    $args = array(
        "label" => __("Youtube Actors", "twentynineteen"),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => array('slug' => 'youtubeactors', 'with_front' => true, ),
        "show_admin_column" => true,
        "show_in_rest" => true,
        "rest_base" => "youtubeactors",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "show_in_quick_edit" => false,
    );
    register_taxonomy("youtubeactors", array("youtubecounter"), $args);
}
/**
  Register post type youtubecounter
*/
function register_post_type_youtubecounter()
{
    $labels = array(
        "name" => __("Youtube Counter", "twentynineteen"),
        "singular_name" => __("All Youtube Counter", "twentynineteen"),
        "menu_name" => __("All Youtube Counter", "twentynineteen"),
        "all_items" => __("All Youtube Links", "twentynineteen"),
        "add_new" => __("Add New Link", "twentynineteen"),
        "add_new_item" => __("Add New Youtube Link", "twentynineteen"),
        "edit_item" => __("Edit New Youtube Link", "twentynineteen"),
    );

    $args = array(
        "label" => __("Youtube Counter", "twentynineteen"),
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "delete_with_user" => false,
        "show_in_rest" => true,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive" => false,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "exclude_from_search" => false,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => array("slug" => "youtubecounter", "with_front" => true),
        "query_var" => true,
        "supports" => array("title"),
    );
    register_post_type("youtubecounter", $args);
}
/**
 * Shortcode
 */
// Add Shortcode to display table
function custom_shortcode_youtubeactors()
{
    $terms = get_terms([
        'taxonomy' => 'youtubeactors',
        'hide_empty' => false,
    ]);
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
        $str .= "<tr><td>" . $value->slug . "</td>";
        $the_query = new WP_Query(array(
            'post_type' => 'youtubecounter',
            'tax_query' => array(
                array(
                    'taxonomy' => 'youtubeactors',
                    'field' => 'slug',
                    'terms' => $value->slug,
                )
            ),
        ));

        while ($the_query->have_posts()) :   $the_query->the_post();
            $str .= "<td> <img height='50' width='50' src='" . get_post_meta(get_the_ID(), 'video_thumbnail_video_thumbnail', true) . "'> " . number_format((float)get_post_meta(get_the_ID(), 'total_views_total_views', true))  . "</td>";
        endwhile;
        $str .= "	</tr>  ";
    }
    $str .= "</tbody> </table></div>";
    return $str;
}
add_shortcode('youtubecounter', 'custom_shortcode_youtubeactors');
/**
 *  Enqueue Admin Script.
 */
function admin_custom_enqueue_script()
{
    wp_enqueue_script('my_custom_script', plugin_dir_url(__FILE__) . 'js/ajax.js', '', '1.0.0', false);
}
add_action('admin_enqueue_scripts', 'admin_custom_enqueue_script');

