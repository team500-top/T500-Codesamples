<?php
/**
 * Uninstallation of WPSEODream plugin
 */
require_once(__DIR__.'/modules/WPSEODream-compression.php');

if (!defined('WP_UNINSTALL_PLUGIN')) die();

$posts = get_posts('numberposts=-1&post_type=post&post_status=any');

foreach ($posts as $post) {
	delete_post_meta($post->ID, 'WPSEODream_title');
	delete_post_meta($post->ID, 'WPSEODream_description');
}

delete_option('WPSEODream_frontpage_title');
delete_option('WPSEODream_frontpage_description');
delete_option('WPSEODream_sitemap_enabled');
delete_option('WPSEODream_noindex_for_tags');
delete_option('WPSEODream_noindex_for_categories');
delete_option('WPSEODream_sitemap_include_lastmod');
delete_option('WPSEODream_sitemap_prioritities');
delete_option('WPSEODream_sitemap_include_categories');
delete_option('WPSEODream_sitemap_include_tags');
delete_option('WPSEODream_google_analytics_code');
delete_option('WPSEODream_sitemap_create_time');
delete_option('WPSEODream_sitemap_create_success');
delete_option('WPSEODream_image_sitemap_create_time');
delete_option('WPSEODream_image_sitemap_create_success');

$compression = new WPSEODreamCompression();
$compression->remove_gzip_compression_from_htaccess();
