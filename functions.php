<?php

use Timber\Timber;
use Timber\Twig_Filter;

include('admin-ui/admin-ui.php');

include('typerocket/init.php');

include('app/init.php');

session_start();
//unset($_SESSION['panier']);

$timber = new Timber();

// Check for Timber
if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php') ) . '</a></p></div>';
	});
	
	add_filter('template_include', function($template) {
		return get_stylesheet_directory() . '/static/no-timber.html';
	});
	
	return;
}

// Define paths to Twig templates

Timber::$dirname = array(
	'views',
	'templates'
);

// Define TimeberSite Child Class
class BootsmoothSite extends TimberSite {

	function __construct() {

		// Enable theme support for core WP functionality
//		add_theme_support( 'post-formats' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
        /* suppression de la barre d'administration sur le template */
        add_filter('show_admin_bar', '__return_false');

        // action a faire pour activer une page d'option de theme
        add_filter('tr_theme_options_page', array($this, 'theme_option_page'));
        add_filter('tr_theme_options_name', array($this, 'theme_option_name'));

        // Limiter l'access a l'espace admin pour les utilisateurs abonnes
        add_action('admin_init', array($this, 'no_admin_access'), 100);

        // Faire des modifications dans l'espace d'admin
        add_action('admin_menu', array($this, 'remove_menus_to_admin'));

		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
        add_action( 'init', array($this, 'allow_origin') );
//		add_action( 'init', array( $this, 'register_post_types' ) );
//		add_action( 'init', array( $this, 'register_taxonomies' ) );
//		add_action('widgets_init', array($this, 'register_widget_TR'));
//		add_action( 'init', array( $this, 'register_widget_areas' ) );
		add_action( 'init', array( $this, 'register_navigation_menus') );

		// Faire les elements assets
        add_action('wp_enqueue_scripts', array($this, 'wp_asset_frontend'));
        add_action('admin_enqueue_scripts', array($this, 'wp_asset_backend'));

        add_action('admin_head', array($this, 'wp_asset_head_backend'));
        add_action('admin_footer', array($this, 'wp_asset_head_backend'));

		parent::__construct();
	}

	function theme_option_page(){
        return get_template_directory() . '/app/options/init.php';
    }

    function theme_option_name(){
	    return 'options';
    }

    function no_admin_access()
    {
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : home_url('/');
        if (!defined('DOING_AJAX') && current_user_can('subscriber'))
            exit(wp_redirect($redirect));
    }

    function remove_menus_to_admin(){
        //	remove_menu_page( 'index.php' );                  //Dashboard
        //	remove_menu_page( 'jetpack' );                    //Jetpack*
        	remove_menu_page( 'edit.php' );                   //Posts
        //	remove_menu_page('upload.php');                 //Media
        //	remove_menu_page( 'edit.php?post_type=page' );    //Pages
        remove_menu_page('edit-comments.php');          //Comments
        //	remove_menu_page( 'themes.php' );                 //Appearance
        //	remove_menu_page( 'plugins.php' );                //Plugins
        //	remove_menu_page( 'users.php' );                  //Users
        //	remove_menu_page( 'tools.php' );                  //Tools
        //	remove_menu_page( 'options-general.php' );        //Settings
    }

    function allow_origin(){
        header("Access-Control-Allow-Origin: *");
    }

	function register_post_types() {
		//this is where you can register custom post types
	}

	function register_taxonomies() {
		//this is where you can register custom taxonomies
	}

	function register_navigation_menus() {
		// Register navigation menus
		register_nav_menus(
			array(
				'hearder-left' => 'menu header gauche',
				'hearder-right' => 'menu header droite',
				'footer-quick' => 'Menu Footer lien rapide',
                'menu-mobile' => 'Menu Mobile'
			)
		);
	}

	// register custom context variables
	function add_to_context( $context ) {
		$context['menu_left'] = new TimberMenu('hearder-left');
		$context['menu_right'] = new TimberMenu('hearder-right');
		$context['footer_quick'] = new TimberMenu('footer-quick');
		$context['menu_mobile'] = new TimberMenu('menu-mobile');
		$context['site'] = $this;
		$context['options'] = tr_options_field('options');
		$context['admin_url'] = admin_url('admin-ajax.php');
		$context['get'] = $_GET;
		$context['post'] = $_POST;

		$args = array(
		    'taxonomy' => 'catalogue',
            'hide_empty' => true,
            'parent' => 0,
            'orderby' => 'name',
            'order' => 'ASC'
        );
		$context['all_cat'] = Timber::get_terms($args);

        $args = array(
            'taxonomy' => 'fabriquant',
        );
        $context['partenaires'] = Timber::get_terms($args);

//		$context['sidebar_widgets'] = Timber::get_widgets( 'Left Sidebar' );

		return $context;
	}

	function add_to_twig( $twig ) {
		/* this is where you can add your own functions to twig */
		$twig->addExtension( new Twig_Extension_StringLoader() );
		$twig->addFilter(new Twig_Filter('is_array', function($data){
		    return is_array($data);
        }));
		return $twig;
	}

    function register_widget_areas() {
        // Register widget areas
        if ( function_exists('register_sidebar') ) {
            register_sidebar(array(
                    'name' => 'Left Sidebar',
                    'before_widget' => '<div class="sidebar-widget">',
                    'after_widget' => '</div>',
                    'before_title' => '<div class="uk-text-large">',
                    'after_title' => '</div>',
                )
            );

            register_sidebar(array(
                    'name' => 'Banner',
                    'before_widget' => '<div class="banner-widget">',
                    'after_widget' => '</div>',
                    'before_title' => '<div>',
                    'after_title' => '</div>',
                )
            );

            register_sidebar(array(
                    'name' => 'Footer Area 1',
                    'before_title' => '<div class="uk-text-large">',
                    'after_title' => '</div>'
                )
            );

            register_sidebar(array(
                    'name' => 'Footer Area 2',
                    'before_title' => '<div class="uk-text-large">',
                    'after_title' => '</div>'
                )
            );

            register_sidebar(array(
                    'name' => 'Footer Area 3',
                    'before_title' => '<div class="uk-text-large">',
                    'after_title' => '</div>'
                )
            );

            register_sidebar(array(
                    'name' => 'Footer Area 4',
                    'before_title' => '<div class="uk-text-large">',
                    'after_title' => '</div>'
                )
            );
        }
    }

    function register_widget_TR(){
        register_widget('Test_Widget');
    }

    function wp_asset_frontend(){

	    // Ajout des elements en JavaScript
        wp_register_script('app', get_stylesheet_directory_uri() . '/js/app.js', '', '1', true);
        wp_register_script('select2', get_stylesheet_directory_uri() . '/js/select2.min.js', '', '1.0', true);
        wp_register_script('datepicker', get_stylesheet_directory_uri() . '/js/datepicker.js', '', '1.0', true);
        wp_register_script('datepicker.fr', get_stylesheet_directory_uri() . '/js/datepicker.fr-FR.js', '', '1.0', true);
        wp_register_script('repeater', get_stylesheet_directory_uri() . '/js/jquery.repeater.js', array('jquery'), '1.0', true);
        wp_register_script('dataTable', get_stylesheet_directory_uri() . '/js/datatables.js', '', '1', true);
        wp_register_script('dataTableUikit', get_stylesheet_directory_uri() . '/js/dataTables.uikit.min.js', '', '1', true);
        wp_register_script('footer-frontend', get_stylesheet_directory_uri() . '/js/footer_frontend.js', '', '1', true);
        wp_register_script('caroussel', get_stylesheet_directory_uri() . '/js/owl.carousel.js', '', '1', true);

        wp_enqueue_script('datepicker');
        wp_enqueue_script('datepicker.fr');
        wp_enqueue_script('repeater');
        wp_enqueue_script('select2');
        wp_enqueue_script('dataTable');
        wp_enqueue_script('dataTableUikit');
        wp_enqueue_script('footer-frontend');
        wp_enqueue_script('caroussel');
        wp_enqueue_script('app');



        // Ajout des elements en CSS
        wp_register_style('datepickercss', get_stylesheet_directory_uri() . '/css/datepicker.css', false, '1.0', 'all');
        wp_register_style('app', get_stylesheet_directory_uri() . '/css/app.css', '', '', 'all');
        wp_register_style('select2css', get_stylesheet_directory_uri() . '/css/select2.css', false, '1.0', 'all');
        wp_register_style('dataTableUikit', get_stylesheet_directory_uri() . '/css/dataTables.uikit.css', '', '', 'all');
        wp_register_style('caroussel', get_stylesheet_directory_uri() . '/css/owl.carousel.css', '', '', 'all');
        wp_register_style('caroussel-default', get_stylesheet_directory_uri() . '/css/owl.theme.default.css', '', '', 'all');

        wp_enqueue_style('datepickercss');
        wp_enqueue_style('select2css');
        wp_enqueue_style('dataTableUikit');
        wp_enqueue_style('caroussel');
        wp_enqueue_style('caroussel-default');
        wp_enqueue_style('app');

    }

    function wp_asset_backend(){

	    // Ajout des elements css
        wp_register_style('datepickercss', get_stylesheet_directory_uri() . '/css/datepicker.css', false, '1.0', 'all');
        wp_register_style('select2css', get_stylesheet_directory_uri() . '/css/select2.css', false, '1.0', 'all');
        wp_register_style('uikit', get_stylesheet_directory_uri() . '/css/uikit.css', '', '', 'all');
        wp_register_style('admin', get_stylesheet_directory_uri() . '/css/admin.css', '', '', 'all');

        wp_enqueue_style('uikit');
        wp_enqueue_style('select2css');
        wp_enqueue_style('datepickercss');
        wp_enqueue_style('admin');

        // AJout des elements js
        wp_register_script('select2', get_stylesheet_directory_uri() . '/js/select2.min.js', '', '1.0', true);
        wp_register_script('uikit', get_stylesheet_directory_uri() . '/js/uikit.js', '', '1', true);
        wp_register_script('uikit-icon', get_stylesheet_directory_uri() . '/js/uikit-icons.js', '', '1', true);
        wp_register_script('datepicker', get_stylesheet_directory_uri() . '/js/datepicker.js', '', '1.0', true);
        wp_register_script('datepicker.fr', get_stylesheet_directory_uri() . '/js/datepicker.fr-FR.js', '', '1.0', true);
        wp_register_script('repeater', get_stylesheet_directory_uri() . '/js/jquery.repeater.js', array('jquery'), '1.0', true);


        wp_enqueue_script('uikit');
        wp_enqueue_script('uikit-icon');
        wp_enqueue_script('select2');
        wp_enqueue_script('datepicker');
        wp_enqueue_script('datepicker.fr');
        wp_enqueue_script('repeater');

    }

    function wp_asset_head_backend(){
        wp_register_script('head-backend', get_stylesheet_directory_uri() . '/js/head_backend.js', '', '1', true);
        wp_enqueue_script('head-backend');
    }

    function wp_asset_footer_backend(){
        wp_register_script('footer-backend', get_stylesheet_directory_uri() . '/js/footer_backend.js', '', '1', true);
        wp_enqueue_script('footer-backend');
    }



}

new BootsmoothSite();