<?php
/*
Plugin Name: WPSEODream
*/

/*
For test purposes only
*/

/**
 * WPSEODream
 */

if (!defined('ABSPATH')) return;

require_once(__DIR__.'/modules/WPSEODream-ui.php');
require_once(__DIR__.'/modules/WPSEODream-ui-settings.php');
require_once(__DIR__.'/modules/WPSEODream-meta.php');
require_once(__DIR__.'/modules/WPSEODream-sitemap.php');
require_once(__DIR__.'/modules/WPSEODream-imagesitemap.php');
require_once(__DIR__.'/modules/WPSEODream-widget.php');
require_once(__DIR__.'/modules/WPSEODream-compression.php');
require_once(__DIR__.'/modules/WPSEODream-ping.php');
require_once(__DIR__.'/modules/WPSEODream-content.php');

add_action('init', array(WPSEODream::get_instance(), 'initialize'));
add_action('admin_notices', array(WPSEODream::get_instance(), 'plugin_activation_notice'));
add_action('plugins_loaded', array(WPSEODream::get_instance(), 'load_textdomain'));
register_activation_hook(__FILE__, array(WPSEODream::get_instance(), 'setup_plugin_on_activation')); 

/**
 * Main class of the plugin.
 */
class WPSEODream {
	
	const PLUGIN_NAME = "WPSEODream";
	const ADMIN_SETTINGS_URL = 'options-general.php?page=WPSEODream';
	const VERSION = '1.0.1';
	const OPTION_ON = 'on';
	const OPTION_OFF = 'off';
	const STATUS_OK = 'ok';
	const STATUS_ERROR = 'error';
	const TEXT_DOMAIN = 'WPSEODream';
	
	private static $instance;
	private static $ui;
	private static $ui_settings;
	private static $meta;
	private static $sitemap;
	private static $widget;
	private static $compression;
	private static $content;
	
	private function __construct() {}
		
	public static function get_instance() {
		if (!isset(self::$instance)) {
			self::$instance = new self();
			self::$ui = new WPSEODreamUi();
			self::$ui_settings = new WPSEODreamUiSettings();
			self::$meta = new WPSEODreamMeta();
			self::$sitemap = new WPSEODreamSitemap();
			self::$widget = new WPSEODreamWidget();
			self::$compression = new WPSEODreamCompression();
			self::$content = new WPSEODreamContent();
		}
		return self::$instance;
	}
	
	public function initialize() {
		load_plugin_textdomain(self::TEXT_DOMAIN, FALSE, basename(dirname( __FILE__ )) . '/languages');
		
		add_action('admin_enqueue_scripts', array($this, 'add_admin_style'));
		add_action('admin_enqueue_scripts', array($this, 'add_admin_javascript'));
		add_action('admin_init', array($this, 'initialize_settings'));
		add_action('admin_menu', array($this, 'create_options_menu'));
		add_action('admin_menu', array($this, 'post_page_init'));
		add_action('save_post', array($this, 'save_post_meta_fields'));
		add_action('edit_attachment', array($this, 'save_post_meta_fields'));
		add_action('save_post', array(self::$sitemap, 'create_sitemap_from_save'));
		add_action('wp_ajax_WPSEODream_sitemap_create', array(self::$sitemap, 'create_sitemap_from_control_panel'));
		add_action('wp_ajax_WPSEODream_toggle_gzip', array(self::$compression, 'toggle_gzip_compression'));
		
		add_filter('pre_get_document_title', array(self::$meta, 'get_meta_title'), 777);
		add_filter('wp_title', array(self::$meta, 'get_meta_title'), 776);
		add_action('wp_head', array($this, 'redirect_attachment_to_post'));
		add_action('wp_head', array(self::$meta, 'print_script_version'));
		add_action('wp_head', array(self::$meta, 'print_google_analytics_script'));
		add_action('wp_head', array(self::$meta, 'print_meta_description'));
		add_action('wp_head', array(self::$meta, 'print_meta_keywords'));		
		add_action('wp_head', array(self::$meta, 'print_meta_noindex'));
		add_action('wp_head', array(self::$meta, 'print_meta_canonical'));
		add_action('wp_head', array(self::$meta, 'print_meta_opengraph'));
		add_action('wp_footer', array($this, 'print_seo_credit_link'), 100);
		add_action('wp_footer', array($this, 'print_added_footer_code'), 999);
		
		add_filter('style_loader_tag', array($this, 'remove_type_attribute'));
		add_filter('script_loader_tag', array($this, 'remove_type_attribute'));
		
		add_filter('manage_posts_columns', array(self::$ui, 'seo_column_head'));
		add_action('manage_posts_custom_column', array(self::$ui, 'seo_column_content'), 10, 2);
		add_filter('manage_pages_columns', array(self::$ui, 'seo_column_head'));
		add_action('manage_pages_custom_column', array(self::$ui, 'seo_column_content'), 10, 2);
		
		add_action('wp_dashboard_setup', array(self::$widget, 'add_dashboard_widget'));
		
		add_filter('the_content', array(self::$content, 'fill_missing_img_alt_tags_with_filename'));
		
		if (get_option('WPSEODream_disable_emojis') === self::OPTION_ON) {
			remove_action('wp_head', 'print_emoji_detection_script', 7);
			remove_action('admin_print_scripts', 'print_emoji_detection_script');
			remove_action('wp_print_styles', 'print_emoji_styles');
			remove_action('admin_print_styles', 'print_emoji_styles'); 
			remove_filter('the_content_feed', 'wp_staticize_emoji');
			remove_filter('comment_text_rss', 'wp_staticize_emoji'); 
			remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
			add_filter('tiny_mce_plugins', array($this, 'disable_emojis_tinymce'));
			add_filter('wp_resource_hints', array($this, 'disable_emojis_remove_dns_prefetch'), 10, 2);
		}
	}
	
	public function post_page_init(){
		add_action('add_meta_boxes', array(self::$ui,'add_post_metaboxes'));
	}

	public function save_post_meta_fields($post_id){
		if (isset($_POST['WPSEODream_title'])){
			update_post_meta($post_id, 'WPSEODream_title', sanitize_text_field($_POST['WPSEODream_title']));
		}
		
		if (isset($_POST['WPSEODream_description'])){
			update_post_meta($post_id, 'WPSEODream_description', sanitize_text_field($_POST['WPSEODream_description']));
		}
	}

	public function create_options_menu() {
		add_submenu_page(
			'options-general.php',
			self::PLUGIN_NAME,
			self::PLUGIN_NAME,
			'manage_options',
			'WPSEODream',
			array(self::$ui_settings, 'print_settings_page')
		);
	}

	public function initialize_settings() {
		register_setting('WPSEODream', 'WPSEODream_frontpage_title');
		register_setting('WPSEODream', 'WPSEODream_frontpage_description');
		register_setting('WPSEODream', 'WPSEODream_noindex_for_tags');
		register_setting('WPSEODream', 'WPSEODream_noindex_for_categories');
		register_setting('WPSEODream', 'WPSEODream_noindex_for_archives');
		register_setting('WPSEODream', 'WPSEODream_noindex_for_paged');
		register_setting('WPSEODream-sitemap', 'WPSEODream_sitemap_enabled');
		register_setting('WPSEODream-sitemap', 'WPSEODream_sitemap_include_lastmod');
		register_setting('WPSEODream-sitemap', 'WPSEODream_sitemap_prioritities');
		register_setting('WPSEODream-sitemap', 'WPSEODream_sitemap_include_categories');		
		register_setting('WPSEODream-sitemap', 'WPSEODream_sitemap_include_tags');
		register_setting('WPSEODream-advanced', 'WPSEODream_exclude_posts');
		register_setting('WPSEODream-advanced', 'WPSEODream_redirect_attachment_to_post');
		register_setting('WPSEODream-advanced', 'WPSEODream_add_code_to_footer');
		register_setting('WPSEODream-advanced', 'WPSEODream_facebook_app_id');
		register_setting('WPSEODream-advanced', 'WPSEODream_disable_emojis');
		register_setting('WPSEODream', 'WPSEODream_show_seo_credits');
		register_setting('WPSEODream', 'WPSEODream_google_analytics_code');
		register_setting('WPSEODream-automatic-titles', 'WPSEODream_use_automatic_titles');
		$this->register_automatic_title_settings();
		
		add_settings_section( 
			'WPSEODream-frontpage', 
			__('<span class="dashicons dashicons-admin-home"></span> Frontpage', self::TEXT_DOMAIN), 
			null, 
			'WPSEODream'
		);
		
		add_settings_section( 
			'WPSEODream-robots', 
			__('<span class="dashicons dashicons-admin-generic"></span> Robots', self::TEXT_DOMAIN), 
			null, 
			'WPSEODream'
		);		
		
		add_settings_section( 
			'WPSEODream-sitemap', 
			__('<span class="dashicons dashicons-networking"></span> Sitemap', self::TEXT_DOMAIN), 
			null, 
			'WPSEODream-sitemap'
		);	
		
		add_settings_section( 
			'WPSEODream-tracking', 
			__('<span class="dashicons dashicons-visibility"></span> Visitor statistics', self::TEXT_DOMAIN), 
			null, 
			'WPSEODream'
		);	
		
		add_settings_section( 
			'WPSEODream-advanced', 
			__('<span class="dashicons dashicons-welcome-learn-more"></span> Advanced', self::TEXT_DOMAIN), 
			null, 
			'WPSEODream-advanced'
		);	
		
		add_settings_section( 
			'WPSEODream-automatic-titles', 
			'', 
			null, 
			'WPSEODream-automatic-titles'
		);		
		
		add_settings_field(
			'WPSEODream_frontpage_title',
			__('Frontpage title', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_frontpage_title'),
			'WPSEODream',
			'WPSEODream-frontpage'
		);	
		
		add_settings_field(
			'WPSEODream_frontpage_description',
			__('Frontpage description', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_frontpage_description'),
			'WPSEODream',
			'WPSEODream-frontpage'
		);
		
		add_settings_field(
			'WPSEODream_show_seo_credits',
			__('Show plugin credits', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_frontpage_seo_credits'),
			'WPSEODream',
			'WPSEODream-frontpage'
		);
		
		add_settings_field(
			'WPSEODream_noindex_for_categories',
			__('Use noindex for categories', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_noindex_for_categories'),
			'WPSEODream',
			'WPSEODream-robots'
		);
		
		add_settings_field(
			'WPSEODream_noindex_for_tags',
			__('Use noindex for tag archives', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_noindex_for_tags'),
			'WPSEODream',
			'WPSEODream-robots'
		);
		
		add_settings_field(
			'WPSEODream_noindex_for_archives',
			__('Use noindex for author and date archives', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_noindex_for_archives'),
			'WPSEODream',
			'WPSEODream-robots'
		);		
		
		add_settings_field(
			'WPSEODream_noindex_for_paged',
			__('Use noindex for other than the first page of each page or article', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_noindex_for_paged'),
			'WPSEODream',
			'WPSEODream-robots'
		);		
		
		add_settings_field(
			'WPSEODream_sitemap_enabled',
			__('Enable XML sitemap', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_sitemap_enable'),
			'WPSEODream-sitemap',
			'WPSEODream-sitemap'
		);	
		
		add_settings_field(
			'WPSEODream_sitemap_include_lastmod',
			__('Include last modification time', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_sitemap_include_lastmod'),
			'WPSEODream-sitemap',
			'WPSEODream-sitemap'
		);	

		add_settings_field(
			'WPSEODream_sitemap_include_categories',
			__('Include categories', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_sitemap_include_categories'),
			'WPSEODream-sitemap',
			'WPSEODream-sitemap'
		);
		
		add_settings_field(
			'WPSEODream_sitemap_include_tags',
			__('Include tag archives', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_sitemap_include_tags'),
			'WPSEODream-sitemap',
			'WPSEODream-sitemap'
		);		
		
		add_settings_field(
			'WPSEODream_sitemap_prioritities',
			__('Page priorities', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_sitemap_prioritities'),
			'WPSEODream-sitemap',
			'WPSEODream-sitemap'
		);

		add_settings_field(
			'WPSEODream_exclude_posts',
			__('Exclude items', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_exclude_posts'),
			'WPSEODream-advanced',
			'WPSEODream-advanced'
		);
		
		add_settings_field(
			'WPSEODream_add_code_to_footer',
			__('Add code to footer', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_add_code_to_footer'),
			'WPSEODream-advanced',
			'WPSEODream-advanced'
		);
		
		add_settings_field(
			'WPSEODream_redirect_attachment_to_post',
			__('Redirect attachment pages to containing page or article', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_redirect_attachment_to_post'),
			'WPSEODream-advanced',
			'WPSEODream-advanced'
		);
		
		add_settings_field(
			'WPSEODream_disable_emojis',
			__('Disable loading of emojis', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_disable_emojis'),
			'WPSEODream-advanced',
			'WPSEODream-advanced'
		);		
		
		add_settings_field(
			'WPSEODream_facebook_app_id',
			__('Facebook app ID', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_facebook_app_id'),
			'WPSEODream-advanced',
			'WPSEODream-advanced'
		);
		
		add_settings_field(
			'WPSEODream_google_analytics_code',
			__('Google Analytics tracking code', self::TEXT_DOMAIN),
			array(self::$ui_settings, 'print_option_google_analytics_code'),
			'WPSEODream',
			'WPSEODream-tracking'
		);

		add_settings_field(
			'WPSEODream_use_automatic_titles',
			'',
			array(self::$ui_settings, 'print_option_use_automatic_titles'),
			'WPSEODream-automatic-titles',
			'WPSEODream-automatic-titles'
		);		
		
		add_settings_field(
			'WPSEODream_automatic_title_post',
			'',
			array(self::$ui_settings, 'print_automatic_title_setting_fields'),
			'WPSEODream-automatic-titles',
			'WPSEODream-automatic-titles'
		);	
	}

	private function register_automatic_title_settings() {
		foreach (get_post_types(NULL, 'names') as $post_type) {
			register_setting('WPSEODream-automatic-titles', 'WPSEODream_automatic_title_'.$post_type);
		}
	}
	
	public function add_admin_style() {
		wp_register_style('WPSEODream_admin_style', plugin_dir_url(__FILE__) . 'css/admin.css');
		wp_enqueue_style('WPSEODream_admin_style');
	}
	
	public function add_admin_javascript() {
		wp_enqueue_script('WPSEODream_admin_js', plugin_dir_url(__FILE__) . 'js/admin.js');		
	}	
	
	public function redirect_attachment_to_post() {
		if (get_option('WPSEODream_redirect_attachment_to_post') !== self::OPTION_ON) {
			return;
		}
		
		global $post;
		if (!is_attachment() || empty($post->post_parent)) {
			return;
		}
		
		$post_url = get_permalink($post->post_parent);
		header('Location: ' . $post_url, TRUE, 301);
		exit();
	}
	
	public function setup_plugin_on_activation() {		
		set_transient('WPSEODream_activation_notice', TRUE, 5);
		add_action('admin_notices', array($this, 'plugin_activation_notice'));
		
		$default_value_options = array(
			'WPSEODream_noindex_for_tags',
			'WPSEODream_noindex_for_categories',
			'WPSEODream_noindex_for_archives',
			'WPSEODream_noindex_for_paged',
			'WPSEODream_sitemap_include_lastmod',
			'WPSEODream_redirect_attachment_to_post'
		);
		
		foreach ($default_value_options as $option) {
			if (get_option($option, FALSE) === FALSE) {
				update_option($option, self::OPTION_ON);
			}
		}
		
		if (get_option('WPSEODream_sitemap_prioritities', FALSE) === FALSE) {
			update_option('WPSEODream_sitemap_prioritities', array(
				'page' => WPSEODreamSitemap::PAGE_PRIORITY_HIGH,
				'post' => WPSEODreamSitemap::PAGE_PRIORITY_MEDIUM,
				'other' => WPSEODreamSitemap::PAGE_PRIORITY_LOW
			));			
		}
		
		foreach (get_post_types(NULL, 'names') as $post_type) {
			$option_name = 'WPSEODream_automatic_title_' . $post_type;
			$option_value = get_option($option_name);
			if (empty($option_value)) {
				update_option($option_name, '%article_name% - %site_name%');
			}
		}
	}
	
	public function plugin_activation_notice() {
		if (get_transient('WPSEODream_activation_notice')) {
			$settings_url = $settings_url = get_admin_url() . WPSEODream::ADMIN_SETTINGS_URL;
			echo '<div class="notice updated"><p><strong>'.sprintf(__('Open WordPress SEO activated. Please configure it at <a href="%s">settings page</a>.', self::TEXT_DOMAIN), $settings_url).'</strong></p></div>';	
		}		
	}
	
	public function print_seo_credit_link() {
		if (get_option('WPSEODream_show_seo_credits', FALSE) === self::OPTION_ON && is_front_page()) {
			echo '<div style="text-align: center; font-size: 80%">WordPress SEO Powered by <a href="https://github.com/tzri/open-wordpress-seo">Open WordPress SEO</a></div>';
		}		
	}
	
	public function print_added_footer_code() {
		$code = get_option('WPSEODream_add_code_to_footer');
		if (!empty($code)) {
			echo $code;
		}
	}
	
	public function load_textdomain() {
		load_plugin_textdomain(self::TEXT_DOMAIN, FALSE, dirname(plugin_basename(__FILE__)) . '/lang/');
	}
	
	public function remove_type_attribute($tag) {
		return preg_replace("/type=['\"]text\/(javascript|css)['\"]/", '', $tag);
	}
	
	public function disable_emojis_tinymce($plugins) {
		if (is_array($plugins)) {
			return array_diff($plugins, array('wpemoji'));
		}
		
		return array();
	}
	
	public function disable_emojis_remove_dns_prefetch($urls, $relation_type) {
		if ('dns-prefetch' == $relation_type) {
			$emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');
			$urls = array_diff($urls, array($emoji_svg_url));
		}

		return $urls;
	}
}