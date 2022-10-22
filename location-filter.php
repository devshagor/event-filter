<?php
/**
 * Plugin Name:       Location Filter
 * Description:       Location Filter assessment for shagor
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            devshagor
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       locatefilter
 */

use \Elementor\Plugin as Plugin;
if ( ! defined( 'ABSPATH' ) ) {
	wp_die(esc_html__("Direct Access Not Allow",'locationfilter'));
}


final class Elementorlocationfilter {

	const VERSION = "1.0.0";
	const MINIMUM_ELEMENTOR_VERSION = "2.6.8";
	const MINIMUM_PHP_VERSION = "7.0";

	private static $_instance = null;
   
    //instance 
    public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}


	public function __construct() {
        add_action( 'plugins_loaded', [ $this, 'init' ] );
    }
	public function init() {
		load_plugin_textdomain( 'locationfilter' );
	
        // Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
        }
        
		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
        }
        
		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

		add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
		
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_new_cat' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'pricing_editor_assets' ] );
	}

	function pricing_editor_assets(){
		wp_enqueue_script("event-scripts", plugins_url("/assets/js/event-script.js",__FILE__),array("jquery"),time(),true);
	}

	public function widget_styles(){
		wp_enqueue_style('event-style', plugins_url('/assets/css/event.css',__FILE__ ), '1.0.0');
	}
	function register_new_cat($manager){
		$manager->add_category('eventfilter',[
			'title'=>esc_html('Event Filter','locationfilter'),
			'icon' => 'fa fa-plug',
		]);
	}

    public function init_widgets(){
		// Include Widget files

		// price style1
        require_once( __DIR__ . '/widgets/location-filter.php' );
		Plugin::instance()->widgets_manager->register_widget_type( new \LocationFilterOne() );
	}
	
    public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'locationfilter' ),
			'<strong>' . esc_html__( 'LocationFilter', 'locationfilter' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'locationfilter' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

    }
    
    public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'locationfilter' ),
			'<strong>' . esc_html__( 'LocationFilter', 'locationfilter' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'locationfilter' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

    public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'locationfilter' ),
			'<strong>' . esc_html__( 'LocationFilter', 'locationfilter' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'locationfilter' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

    }
    
	public function includes() {}

}
Elementorlocationfilter::instance();

// 


 // Enqueue scripts
 function load_scripts(){

	// JS File
	wp_register_script( 'event_scripts', plugins_url('/assets/js/event-script.js',__FILE__), array( 'jquery' ), time(), true );
	wp_register_style( 'event_style', plugins_url('/assets/css/event.css',__FILE__ ), '0.1.0' );
	
	// Localization
	wp_localize_script( 'event_scripts', 'wpAjax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'load_scripts' );


function filter_posts() {
    // Validate with isset and not empty then sanitize with sanitize_text_text also use wp_unslash.
    $cat_slug = isset( $_POST['category'] ) && ! empty( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '';

    // Define query args in a variable, it will help us to add tax query conditionally.
    $query_args = array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
		'category_name' => $cat_slug,
        'tax_query'      => array( 'relation' => 'OR' ),
    );
    
	// $location = get_terms('location', array('hide_empty' => false));
						

	//  if ( ! empty( $cat_slug ) ) {
    //     $query_args['tax_query'][] = array(
    //         'taxonomy' => 'location',
    //         'field'    => 'slug',
    //         'operator' => '=',
	// 		'location_name' => $cat_slug,
    //         'terms'    => $location,
    //     );
    // }

    // Run the query.
    $ajaxposts = new WP_Query( $query_args );
    // We will use ob functions to collect the buffered output.
    ob_start();

    // check if has posts.
    if ( $ajaxposts->have_posts() ) {

        // Loop through the posts.
        while ( $ajaxposts->have_posts() ) :

            $ajaxposts->the_post();

            // Get content.
           ?>
           <div class="col-md-4">
                <div class="event-item">
                    <?php if(has_post_thumbnail()): ?>
                        <div class="thumb">
                            <a href="<?php echo esc_url(the_permalink()); ?>">
                                <?php 
                                    the_post_thumbnail();
                                ?>
                        </a>
                        </div>
                    <?php endif; ?>
                    <div class="content">
                        <h2 class="event-title">
                            <a href="<?php echo esc_url(the_permalink()); ?>">
                                <?php echo esc_html(the_title()); ?>
                            </a>
                        </h2>
                        <p>
                            <?php 
                                echo wp_trim_words( get_the_excerpt(), 10, '...' );
                            ?>
                        </p>
                    </div>
                </div>
            </div>
           <?php

        endwhile;
    }

    wp_reset_postdata();

    $response = ob_get_clean();

    echo $response;

    exit;
}
add_action( 'wp_ajax_filter_posts', 'filter_posts' );
add_action( 'wp_ajax_nopriv_filter_posts', 'filter_posts' );


/**
 * Add custom taxonomies
 *
 * Additional custom taxonomies can be defined here
 * https://codex.wordpress.org/Function_Reference/register_taxonomy
 */

function add_custom_taxonomies() {
	// Add new "Locations" taxonomy to Posts
	register_taxonomy('location', 'post', array(
	 
	  'hierarchical' => true,
	  // This array of options controls the labels displayed in the WordPress Admin UI
	  'labels' => array(
		'name' => _x( 'Locations', 'taxonomy general name' ),
		'singular_name' => _x( 'Location', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Locations' ),
		'all_items' => __( 'All Locations' ),
		'parent_item' => __( 'Parent Location' ),
		'parent_item_colon' => __( 'Parent Location:' ),
		'edit_item' => __( 'Edit Location' ),
		'update_item' => __( 'Update Location' ),
		'add_new_item' => __( 'Add New Location' ),
		'new_item_name' => __( 'New Location Name' ),
		'menu_name' => __( 'Locations' ),
	  ),
	  // Control the slugs used for this taxonomy
	  'rewrite' => array(
		'slug' => 'locations', // This controls the base slug that will display before each term
		'with_front' => false, // Don't display the category base before "/locations/"
		'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
	  ),
	));
}
add_action( 'init', 'add_custom_taxonomies', 0 );