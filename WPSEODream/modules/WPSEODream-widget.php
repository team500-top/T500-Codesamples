<?php
/**
 * Module for UI.
 */
if (!defined('ABSPATH')) return;

class WPSEODreamWidget {

	private $content;
	
	public function __construct() {
		$this->content = new WPSEODreamContent();
	}

	public function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'WPSEODream_widget',
			'WPSEODream',
			array($this, 'print_dashboard_widget')
        );
	}

	public function print_dashboard_widget() {
		$indexing_denied = get_option('blog_public') === '0';

		echo '<div class="WPSEODream-dashboard-widget">';
		echo '<table>';

		echo '<tr><td><strong>'.__('Overall SEO score', 'WPSEODream').'</strong></td><td><strong>'.$this->get_overall_seo_status_score_text().'</strong></td></tr>';

		echo '<tr><td>'.__('Frontpage SEO score', 'WPSEODream').'</td><td>'.$this->get_frontpage_seo_status_text().'</td></tr>';

		echo '<tr><td>'.__('Articles SEO score', 'WPSEODream').'</td><td>'.$this->get_posts_seo_status_text('post').'</td></tr>';

		echo '<tr><td>'.__('Pages SEO score', 'WPSEODream').'</td><td>'.$this->get_posts_seo_status_text('page').'</td></tr>';

		echo '<tr><td>'.__('Sitemap updated', 'WPSEODream').'</td><td>'.$this->get_sitemap_status().'</td></tr>';

		echo '</table>';

		if ($indexing_denied) {
			printf('<table><tr><td class="icon"><span class="dashicons dashicons-warning"></span></td><td>'.__('Search engines are told not to index this site. Change the setting in <a href="%s">Reading</a> > Search Engine Visibility.', 'WPSEODream').'</td></tr></table>', get_admin_url().'/options-reading.php');
		}

		echo '</div>';
	}

	private function get_overall_seo_status_score_text() {
		$score = $this->get_overall_seo_status_score();
		$score_text = __('Low', 'WPSEODream') . ' <span class="dashicons dashicons-thumbs-down small-icon"></span>';

		if ($score > 0.9) {
			$score_text = __('High', 'WPSEODream') . ' <span class="dashicons dashicons-thumbs-up small-icon"></span>';
		}
		else if ($score > 0.6) {
			$score_text = __('OK', 'WPSEODream');
		}

		$style = $score > 0.6 ? 'WPSEODream-ok' : 'WPSEODream-fail';

		return "<span class=\"{$style}\">{$score_text}</span>";
	}

	private function get_overall_seo_status_score() {
		$robots_score = $this->get_robots_seo_status_score();
		$frontpage_score = $this->get_frontpage_seo_status_score();
		$articles_score = $this->get_articles_seo_status_score();
		$pages_score = $this->get_pages_seo_status_score();
		$sitemap_score = $this->get_sitemap_seo_status_score();

		$overall_score = ($robots_score + $frontpage_score + $articles_score + $pages_score + $sitemap_score) / 5;
		return $overall_score;
	}

	private function get_robots_seo_status_score() {
		$indexing_denied = get_option('blog_public') === '0';

		if ($indexing_denied) {
			return 0;
		}

		return 1;
	}

	private function get_frontpage_seo_status_score() {
		$settings_ok = 0;

		$option_title = get_option('WPSEODream_frontpage_title');
		$option_description = get_option('WPSEODream_frontpage_description');

		if (!empty($option_title)) {
			$settings_ok++;
		}

		if (!empty($option_description)) {
			$settings_ok++;
		}

		return $settings_ok / 2;
	}

	private function get_articles_seo_status_score() {
		$seo_status = $this->get_posts_seo_status('post');

		if ($seo_status['post_seoed'] == 0) {
			return 0;
		}

		return $seo_status['post_seoed'] / $seo_status['post_total'];
	}

	private function get_pages_seo_status_score() {
		$seo_status = $this->get_posts_seo_status('page');

		if ($seo_status['post_seoed'] == 0) {
			return 0;
		}

		return $seo_status['post_seoed'] / $seo_status['post_total'];
	}

	private function get_sitemap_seo_status_score() {
		$sitemap_created_time = get_option('WPSEODream_sitemap_create_time', FALSE);

		if ($sitemap_created_time !== FALSE) {
			return 1;
		}

		return 0;
	}

	private function get_frontpage_seo_status_text() {
		$settings_count = 2;
		$settings_ok = 0;

		$option_title = get_option('WPSEODream_frontpage_title');
		$option_description = get_option('WPSEODream_frontpage_description');

		if (!empty($option_title)) {
			$settings_ok++;
		}

		if (!empty($option_description)) {
			$settings_ok++;
		}

		$seo_status_style = $this->get_seo_success_style($settings_count, $settings_ok);
		return "<span class=\"{$seo_status_style}\">{$settings_ok}/{$settings_count}</span>";
	}

	private function get_posts_seo_status_text($post_type) {
		$articles_seo_status = $this->get_posts_seo_status($post_type);
		$seo_status_style = $this->get_seo_success_style($articles_seo_status['post_total'], $articles_seo_status['post_seoed']);

		return "<span class=\"{$seo_status_style}\">".sprintf('%s / %s', $articles_seo_status['post_seoed'], $articles_seo_status['post_total']).'</span>';
	}

	private function get_posts_seo_status($post_type) {
		$result = array('post_total' => 0, 'post_seoed' => 0);

		$post_query_arguments = array(
			'post_type' => $post_type,
			'post_status' => 'publish',
			'posts_per_page' => -1
		);

		$post_query = new WP_Query($post_query_arguments);

		while ($post_query->have_posts()) {
			$post_query->the_post();

			if ($this->is_excluded_post_id(get_the_ID())) {
				continue;
			}

			$seo_title = get_post_meta(get_the_ID(), 'WPSEODream_title', TRUE);
			$seo_description = get_post_meta(get_the_ID(), 'WPSEODream_description', TRUE);
			$too_few_words = $this->content->is_word_count_too_low(get_post_field('post_content', get_the_ID()));

			if (!$too_few_words && !empty($seo_title) && !empty($seo_description)) {
				$result['post_seoed']++;
			}

			$result['post_total']++;
		}

		return $result;
	}

	private function is_excluded_post_id($id) {
		return in_array($id, explode(',', str_replace(' ', '', get_option('WPSEODream_exclude_posts'))));
	}

	private function get_seo_success_style($total_count, $ok_count) {
		if ($total_count === 0) {
			return 'WPSEODream-ok';
		}

		if ($total_count === $ok_count) {
			return 'WPSEODream-ok';
		}

		return 'WPSEODream-fail';
	}

	private function get_sitemap_status() {
		$settings_url = get_admin_url() . WPSEODream::ADMIN_SETTINGS_URL;

		$sitemap_enabled = get_option('WPSEODream_sitemap_enabled');
		$sitemap_updated = get_option('WPSEODream_sitemap_create_success', FALSE);
		$sitemap_created_time = get_option('WPSEODream_sitemap_create_time', FALSE);

		if ($sitemap_enabled !== WPSEODream::OPTION_ON) {
			return '<span class="WPSEODream-fail">'. sprintf(__('Not enabled. <a href="%s">Enable here</a>', 'WPSEODream'), $settings_url) . '</span>';
		}

		if ($sitemap_updated === WPSEODream::STATUS_ERROR) {
			return '<span class="WPSEODream-fail">' . __('Error', 'WPSEODream') . '</span>';
		}

		if (($sitemap_updated === WPSEODream::STATUS_OK || $sitemap_updated === FALSE) && $sitemap_created_time !== FALSE) {
			return '<span class="WPSEODream-ok">' . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $sitemap_created_time) . '</span>';
		}
	}

}
