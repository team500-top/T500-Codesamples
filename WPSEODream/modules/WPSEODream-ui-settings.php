<?php

/**
 * Module for settings UI.
 */

if (!defined('ABSPATH')) return;

class WPSEODreamUiSettings {

	public function print_settings_page() {
		if (!current_user_can('manage_options')) {
			return;
		}
		?>

		<div class="wrap WPSEODream-settings-wrap" style="display: none">

		<h1><?= esc_html_e('Open WordPress SEO Settings', 'WPSEODream'); ?></h1>

		<h2 class="nav-tab-wrapper">
			<a href="options-general.php?page=WPSEODream&tab=main-settings" class="nav-tab WPSEODream-navtab nav-tab-active main-settings-tab-button"><span class="dashicons dashicons-star-filled"></span> <?php _e('Main Settings', 'WPSEODream'); ?></a>
			<a href="options-general.php?page=WPSEODream&tab=automatic-titles" class="nav-tab WPSEODream-navtab automatic-titles-tab-button"><span class="dashicons dashicons-admin-settings"></span> <?php _e('Automatic Titles', 'WPSEODream'); ?></a>
			<a href="options-general.php?page=WPSEODream&tab=sitemaps" class="nav-tab WPSEODream-navtab sitemaps-tab-button"><span class="dashicons dashicons-networking"></span> <?php _e('Sitemap', 'WPSEODream'); ?></a>
			<a href="options-general.php?page=WPSEODream&tab=advanced" class="nav-tab WPSEODream-navtab advanced-tab-button"><span class="dashicons dashicons-welcome-learn-more"></span> <?php _e('Advanced', 'WPSEODream'); ?></a>
		</h2>

		<div class="WPSEODream-settings">

			<?php $this->print_notifications(); ?>

			<div class="WPSEODream-settings-tab" id="main-settings" style="display: none">

				<h2><span class="dashicons dashicons-admin-users"></span> <?php _e('Actions', 'WPSEODream'); ?></h2>
				<form action="admin-ajax.php" method="post">
					<input type="hidden" name="create-sitemap" value="yes"/>
					<input type="hidden" name="action" value="WPSEODream_sitemap_create"/>
					<input type="submit" name="submit" value="<?php _e('Create sitemap now', 'WPSEODream'); ?>"/>
				</form>

				<form action="admin-ajax.php" method="post">
					<input type="hidden" name="create-sitemap" value="yes"/>
					<input type="hidden" name="action" value="WPSEODream_toggle_gzip"/>
					<?php
						$compression_enabled = get_option('WPSEODream_gzip_compression') !== WPSEODream::OPTION_ON;

						if ($compression_enabled) :
					?>
					<input type="submit" name="submit" value="<?php _e('Enable Gzip compression', 'WPSEODream') ?>"/>
					<?php else : ?>
					<input type="submit" name="submit" value="<?php _e('Disable Gzip compression', 'WPSEODream') ?>"/>
					<?php endif; ?>
				</form>

				<form action="options.php" method="post">
					<?php
						submit_button(__('Save settings', 'WPSEODream'));
						settings_fields('WPSEODream');
						do_settings_sections('WPSEODream');
						submit_button(__('Save settings', 'WPSEODream'));
					?>
				</form>

			</div>

			<div class="WPSEODream-settings-tab" id="automatic-titles" style="display: none">
				<form action="options.php" method="post">
					<?php
						submit_button(__('Save settings', 'WPSEODream'));
						settings_fields('WPSEODream-automatic-titles');
						echo '<table class="form-table">';
						$this->print_automatic_titles_options_header();
						$this->print_automatic_title_instructions();
						$this->print_option_use_automatic_titles();
						$this->print_automatic_title_setting_fields();
						echo '</table>';
						submit_button(__('Save settings', 'WPSEODream'));
					?>
				</form>
			</div>

			<div class="WPSEODream-settings-tab" id="sitemaps" style="display: none">
				<form action="options.php" method="post">
					<?php
						submit_button(__('Save settings', 'WPSEODream'));
						settings_fields('WPSEODream-sitemap');
						do_settings_sections('WPSEODream-sitemap');
						submit_button(__('Save settings', 'WPSEODream'));
					?>
				</form>
			</div>

			<div class="WPSEODream-settings-tab" id="advanced" style="display: none">
				<form action="options.php" method="post">
					<?php
						submit_button(__('Save settings', 'WPSEODream'));
						settings_fields('WPSEODream-advanced');
						do_settings_sections('WPSEODream-advanced');
						submit_button(__('Save settings', 'WPSEODream'));
					?>
				</form>
			</div>

			<?php
				delete_option('WPSEODream_sitemap_create_success');
				delete_option('WPSEODream_image_sitemap_create_success');
				delete_option('WPSEODream_htaccess_save');
				delete_option('WPSEODream_gzip_test_result');
			?>
		</div>
		</div> <!-- wrap -->
		<?php
	}

	private function print_notifications() {
		if (get_option('WPSEODream_htaccess_save', FALSE) === WPSEODream::STATUS_ERROR) : ?>
		<div class="notice error">
			<p><strong><?php _e('Enabling Gzip compression failed. Could not not update .htaccess file. Please check that the file is writable.', 'WPSEODream'); ?></strong></p>
		</div>
		<?php endif; ?>

		<?php if (get_option('WPSEODream_gzip_test_result', FALSE) === WPSEODream::STATUS_ERROR) : ?>
			<div class="notice error">
				<p><strong><?php _e('Gzip compression seems not to be working. Perhaps mod_deflate module is not active.', 'WPSEODream'); ?></strong></p>
			</div>
		<?php endif; ?>

		<?php
			$htaccess_saved = get_option('WPSEODream_htaccess_save', FALSE) === WPSEODream::STATUS_OK;
			$gzip_working = get_option('WPSEODream_gzip_test_result', FALSE) === WPSEODream::STATUS_OK;
			if ($htaccess_saved && $gzip_working) : ?>
			<div class="notice updated">
				<?php if (get_option('WPSEODream_gzip_compression') === WPSEODream::OPTION_ON) : ?>
				<p><strong><?php _e('Gzip compression is now enabled.', 'WPSEODream'); ?></strong></p>
				<?php else : ?>
				<p><strong><?php _e('Gzip compression is now disabled.', 'WPSEODream'); ?></strong></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php
			$sitemap_created_time = get_option('WPSEODream_sitemap_create_time', FALSE);
			$sitemap_updated = get_option('WPSEODream_sitemap_create_success', FALSE);
			$image_sitemap_created_time = get_option('WPSEODream_image_sitemap_create_time', FALSE);
			$image_sitemap_updated = get_option('WPSEODream_image_sitemap_create_success', FALSE);

			if ($sitemap_updated === 'not_enabled') {
				echo '<div class="notice error"><p><strong>'. __('Please check "Enable XML sitemap" option on Sitemap tab and save settings before using the Create sitemap now button.', 'WPSEODream').'</strong></p></div>';
			}

			if ($sitemap_updated === WPSEODream::STATUS_OK) {
				echo '<div class="notice updated"><p><strong>'. sprintf(__('<a  target="_blank" href="/sitemap.xml">Sitemap.xml</a> was succesfully updated %s.', 'WPSEODream'), date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $sitemap_created_time)).'</strong></p></div>';
			}

			if ($image_sitemap_updated === WPSEODream::STATUS_OK) {
				echo '<div class="notice updated"><p><strong>'. sprintf(__('<a href="/image-sitemap.xml" target="_blank">Image-sitemap.xml</a> was succesfully updated %s.', 'WPSEODream'), date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $image_sitemap_created_time)).'</strong></p></div>';
			}
		?>

		<?php if (get_option('WPSEODream_sitemap_create_success', FALSE) === WPSEODream::STATUS_ERROR) : ?>
		<div class="notice error">
			<p><strong><?php printf(__('Could not create sitemap. Please check that your WordPress directory or %s is writable.', 'WPSEODream'), WPSEODreamSitemap::SITEMAP_FILENAME); ?></strong></p>
		</div>
		<?php endif; ?>

		<?php if (get_option('WPSEODream_image_sitemap_create_success', FALSE) === WPSEODream::STATUS_ERROR) : ?>
		<div class="notice error">
			<p><strong><?php printf(__('Could not create image sitemap. Please check that your WordPress directory or %s is writable.', 'WPSEODream'), WPSEODreamImageSitemap::IMAGE_SITEMAP_FILENAME); ?></strong></p>
		</div>
		<?php endif; ?>

		<?php if (get_option('blog_public') === '0') : ?>
		<div class="notice error">
			<p><strong><?php printf(__('Search engines are told not to index this site. Change the setting in <a href="%s">Reading</a> > Search Engine Visibility.', 'WPSEODream'), get_admin_url().'/options-reading.php'); ?></strong></p>
		</div>
		<?php endif; ?>

		<?php
		if (strpos(get_option('permalink_structure'), '%postname%') === FALSE) {
			?>
			<div class="notice error">
				<p><strong><?php _e('The permalink structure does not include post name. It is recommended to set permalink structure to "Post name" on Permalinks settings page.', 'WPSEODream'); ?></strong></p>
			</div>
			<?php
		}

		// These may contain time or string "error".
		$ping_google_time = get_option('WPSEODream_ping_google_time', 0);
		$ping_bing_time = get_option('WPSEODream_ping_bing_time', 0);
		
		if ($ping_google_time === WPSEODream::STATUS_ERROR) {
		?>
			<div class="notice error">
				<p><strong><?php _e('Tried to notify Google but failed.', 'WPSEODream'); ?></strong></p>
			</div>					
		<?php
		}
		else if ($ping_google_time + 300 > time()) : ?>
			<div class="notice updated">
				<p><strong><?php _e('Google was recently notified about changes.', 'WPSEODream'); ?></strong></p>
			</div>
		<?php endif;

		if ($ping_bing_time === WPSEODream::STATUS_ERROR) {
		?>
			<div class="notice error">
				<p><strong><?php _e('Tried to notify Bing but failed.', 'WPSEODream'); ?></strong></p>
			</div>			
		<?php
		}
		else if ($ping_bing_time + 300 > time()) : ?>
			<div class="notice updated">
				<p><strong><?php _e('Bing was recently notified about changes.', 'WPSEODream'); ?></strong></p>
			</div>
		<?php endif;
	}

	public function print_option_frontpage_title() {
		$frontpage_title = get_option('WPSEODream_frontpage_title');
		echo '<input type="text" maxlength="60" name="WPSEODream_frontpage_title" id="WPSEODream_title" value="'.$frontpage_title.'"/>';
	}

	public function print_option_frontpage_description() {
		$frontpage_description = get_option('WPSEODream_frontpage_description');
		echo '<textarea maxlength="160" name="WPSEODream_frontpage_description" id="WPSEODream_description">'.$frontpage_description.'</textarea>';
		?>

		<div class="WPSEODream-serp-preview">
			<div class="WPSEODream-preview-title">
			<?php
				$title = get_option('WPSEODream_frontpage_title');
				if (empty($title)) {
					$title = get_bloginfo('name');
				}

				if (mb_strlen($title) > 60) {
					$title = mb_substr($title, 0, 60);
				}

				echo $title;
			?>
			</div>
			<div class="WPSEODream-preview-address">
			<?php echo get_bloginfo('url'); ?>
			</div>
			<div class="WPSEODream-preview-description">
			<?php
				$description = $frontpage_description;

				if (empty($description)) {
					$description = get_bloginfo('description');
				}

				if (empty($description)) {
					$description = __('No description set.', 'WPSEODream');
				}

				if (mb_strlen($description) > 160) {
					$description = mb_substr($description, 0, 160) . ' ...';
				}

				echo $description;
			?>
			</div>
		</div>
		<?php
	}

	public function print_option_frontpage_seo_credits() {
		$show_credits = get_option('WPSEODream_show_seo_credits');
		echo '<input type="checkbox" name="WPSEODream_show_seo_credits" ' . checked(WPSEODream::OPTION_ON, $show_credits, FALSE) . '/>';
		echo '<span class="dashicons dashicons-editor-help info"><span class="description">'. __('Displays a credits notification for this plugin in the footer of the site. If you find this plugin useful, please check this option.', 'WPSEODream') .'</span>';
	}

	public function print_option_noindex_for_categories() {
		$use_noindex = get_option('WPSEODream_noindex_for_categories');
		echo '<input type="checkbox" name="WPSEODream_noindex_for_categories" ' . checked(WPSEODream::OPTION_ON, $use_noindex, FALSE) . '/>';
		echo '<span class="dashicons dashicons-editor-help info"><span class="description">'. __('Guide search engines not to index category pages. They may contain duplicate content.', 'WPSEODream') .'</span>';
	}

	public function print_option_noindex_for_tags() {
		$use_noindex = get_option('WPSEODream_noindex_for_tags');
		echo '<input type="checkbox" name="WPSEODream_noindex_for_tags" ' . checked(WPSEODream::OPTION_ON, $use_noindex, FALSE) . '/>';
		echo '<span class="dashicons dashicons-editor-help info"><span class="description">'. __('Guide search engines not to index tag archive pages. They may contain duplicate content.', 'WPSEODream') .'</span>';
	}

	public function print_option_noindex_for_archives() {
		$use_noindex = get_option('WPSEODream_noindex_for_archives');
		echo '<input type="checkbox" name="WPSEODream_noindex_for_archives" ' . checked(WPSEODream::OPTION_ON, $use_noindex, FALSE) . '/>';
		echo '<span class="dashicons dashicons-editor-help info"><span class="description">'. __('Guide search engines not to index user or date archive pages. They will most likely contain duplicate content that you don\'t want to have. (Recommended)', 'WPSEODream') .'</span>';
	}

	public function print_option_noindex_for_paged() {
		$use_noindex = get_option('WPSEODream_noindex_for_paged');
		echo '<input type="checkbox" name="WPSEODream_noindex_for_paged" ' . checked(WPSEODream::OPTION_ON, $use_noindex, FALSE) . '/>';
		echo '<span class="dashicons dashicons-editor-help info"><span class="description">'. __('Guide search engines not to index other than the first page of each page or article. (Recommended)', 'WPSEODream') .'</span>';
	}

	public function print_option_sitemap_enable() {
		$sitemap_enabled = get_option('WPSEODream_sitemap_enabled');
		echo '<input type="checkbox" name="WPSEODream_sitemap_enabled" ' . checked(WPSEODream::OPTION_ON, $sitemap_enabled, FALSE) . '/>';
		echo '<span class="dashicons dashicons-editor-help info"><span class="description">'. __('<p>The plugin will create and automatically maintain an XML sitemap when you add content. (Recommended)</p><p>After saving the settings, use the "Create sitemap now" button on Main Settings to verify that sitemap creation is working.</p>', 'WPSEODream') .'</span>';
	}

	public function print_option_sitemap_include_lastmod() {
		$sitemap_include_lastmod = get_option('WPSEODream_sitemap_include_lastmod');
		echo '<input type="checkbox" name="WPSEODream_sitemap_include_lastmod" ' . checked(WPSEODream::OPTION_ON, $sitemap_include_lastmod, FALSE) . '/>';
	}

	public function print_option_sitemap_include_tags() {
		$sitemap_include_tags = get_option('WPSEODream_sitemap_include_tags');
		echo '<input type="checkbox" name="WPSEODream_sitemap_include_tags" ' . checked(WPSEODream::OPTION_ON, $sitemap_include_tags, FALSE) . ' />';
	}

	public function print_option_sitemap_include_categories() {
		$sitemap_include_categories = get_option('WPSEODream_sitemap_include_categories');
		echo '<input type="checkbox" name="WPSEODream_sitemap_include_categories" ' . checked(WPSEODream::OPTION_ON, $sitemap_include_categories, FALSE) . ' />';
	}

	public function print_option_redirect_attachment_to_post() {
		$redirect = get_option('WPSEODream_redirect_attachment_to_post');
		echo '<input type="checkbox" name="WPSEODream_redirect_attachment_to_post" ' . checked(WPSEODream::OPTION_ON, $redirect, FALSE) . ' />';
		echo '<span class="dashicons dashicons-editor-help info"><span class="description">'. __('Every image you attach to posts creates an attachment post. Redirect to original article when accessing these attachment posts. (Recommended)', 'WPSEODream') .'</span>';
	}

	public function print_option_disable_emojis() {
		$redirect = get_option('WPSEODream_disable_emojis');
		echo '<input type="checkbox" name="WPSEODream_disable_emojis" ' . checked(WPSEODream::OPTION_ON, $redirect, FALSE) . ' />';
		echo '<span class="dashicons dashicons-editor-help info"><span class="description">'. __('If you do not use emojis (little emotion icons) disable them to speed up the loading of website.', 'WPSEODream') .'</span>';
	}

	public function print_option_sitemap_prioritities() {
		$sitemap_priorities = get_option('WPSEODream_sitemap_prioritities');

		?>
		<table class="WPSEODream-sitemap-priorities">

		<tr>
			<th><?php _e('Item type', 'WPSEODream'); ?></th>
			<th><?php _e('Priority', 'WPSEODream'); ?></th>
		</tr>

		<?php
			foreach (get_post_types(NULL, 'names') as $post_type) {

				$post_type_details = get_post_type_object($post_type);
				$post_type_name = $post_type_details->labels->singular_name;
				if (empty($post_type_name)) {
					$post_type_name = $post_type;
				}
		?>
				<tr>
				<td><?php echo $post_type_name; ?></td>
				<td>
				<select name="WPSEODream_sitemap_prioritities[<?php echo $post_type; ?>]" autocomplete="off">
					<?php
						if (array_key_exists($post_type, $sitemap_priorities)) {
							$current_priority = $sitemap_priorities[$post_type];
						}
						else {
							$current_priority = WPSEODreamSitemap::PAGE_PRIORITY_MEDIUM;
						}

						$this->print_sitemap_priority_option(WPSEODreamSitemap::PAGE_PRIORITY_HIGH, $current_priority, $post_type, __('High', 'WPSEODream'));
						$this->print_sitemap_priority_option(WPSEODreamSitemap::PAGE_PRIORITY_MEDIUM, $current_priority, $post_type, __('Medium', 'WPSEODream'));
						$this->print_sitemap_priority_option(WPSEODreamSitemap::PAGE_PRIORITY_LOW, $current_priority, $post_type, __('Low', 'WPSEODream'));
					?>
				</select>
				<?php
					if ($post_type == 'page') {
						echo '<span class="dashicons dashicons-editor-help info"><span class="description">'. __('Medium or High value recommended.', 'WPSEODream') .'</span>';
					}
					else if ($post_type == 'post') {
						echo '<span class="dashicons dashicons-editor-help info"><span class="description">'. __('Medium or High value recommended.', 'WPSEODream') .'</span>';
					}
				?>
				</td>
				</tr>

		<?php } // end of for each ?>
		</table>

		<?php
	}

	public function print_option_exclude_posts() {
		$excluded_posts = get_option('WPSEODream_exclude_posts');
		echo '<textarea style="width: 85%" name="WPSEODream_exclude_posts" placeholder="'. __('Enter post IDs separated by commas...', 'WPSEODream') .'">'.$excluded_posts.'</textarea>';
		echo '<span class="dashicons dashicons-editor-help info"><span class="description">'. __('Enter the IDs you wish to exclude separated by commas.', 'WPSEODream') .'</span>';
	}

	public function print_option_add_code_to_footer() {
		$footer_code = get_option('WPSEODream_add_code_to_footer');
		echo '<textarea style="width: 85%" cols="5" rows="7" name="WPSEODream_add_code_to_footer" placeholder="'. __('Copy/paste your code here...', 'WPSEODream') .'">'.$footer_code.'</textarea>';
		echo '<span class="dashicons dashicons-editor-help info"><span class="description">'. __('Here you can enter HTML / JavaScript (e.g. statistics scripts) that will be inserted into the footer of each page.', 'WPSEODream') .'</span>';
	}

	public function print_option_facebook_app_id() {
		$facebook_app_id = get_option('WPSEODream_facebook_app_id');
		echo '<input type="text" class="narrow" name="WPSEODream_facebook_app_id" value="'.$facebook_app_id.'"/>';
		echo '<span class="dashicons dashicons-editor-help info"><span class="description">'. __('In order to use Facebook Insights you must add the app ID to your page. Insights lets you view analytics for traffic to your site from Facebook.', 'WPSEODream') .'</span>';
	}

	private function print_sitemap_priority_option($priority, $current_priority, $page_type, $text) {
		echo "<option value=\"{$priority}\" " . selected($current_priority, $priority, FALSE) . ">{$text}</option>";
	}

	public function print_option_google_analytics_code() {
		$tracking_code = get_option('WPSEODream_google_analytics_code');
		echo '<input type="text" class="half-width" name="WPSEODream_google_analytics_code" value="'.$tracking_code.'"/>';
		echo '<span class="dashicons dashicons-editor-help info"><span class="description">'. sprintf(__('The tracking code you get from Google Analytics (%s).', 'WPSEODream'), 'www.google.com/analytics') .'</span>';
	}

	private function print_automatic_titles_options_header() {
		echo '<h2>'.__('<span class="dashicons dashicons-admin-settings"></span> Automatic Titles', 'WPSEODream').'</h2>';
	}

	private function print_automatic_title_instructions() {
		echo '<div class="WPSEODream-instructions"><p><span class="dashicons dashicons-info"></span> ';
		_e('Use the following variables in the titles to print out post or site related information:', 'WPSEODream');
		echo '</p><ul>';
		echo '<li><strong>%article_name%</strong> - '.__('The name of the item', 'WPSEODream').'</li>';
		echo '<li><strong>%site_name%</strong> - '.__('The name of the site', 'WPSEODream').'</li>';
		echo '<li><strong>%category_name%</strong> - '.__('The name of the item\'s first category', 'WPSEODream').'</li>';
		echo '<li><strong>%author_name%</strong> - '.__('The name of the item\'s author', 'WPSEODream').'</li>';
		echo '<li><strong>%article_date%</strong> - '.__('The publish date of the item', 'WPSEODream').'</li>';
		echo '</ul>';
		echo '</div>';
	}

	public function print_option_use_automatic_titles() {
		echo '<tr><th scope="row">'.__('Use automatic titles when post specific title has not been set', 'WPSEODream').'</th><td>';

		$use_automatic_titles = get_option('WPSEODream_use_automatic_titles');
		echo '<input type="checkbox" name="WPSEODream_use_automatic_titles" ' . checked(WPSEODream::OPTION_ON, $use_automatic_titles, FALSE) . ' />';

		echo '</td></tr>';
	}

	public function print_automatic_title_setting_fields() {
		foreach (get_post_types(NULL, 'names') as $post_type) {
			$title = get_option('WPSEODream_automatic_title_' . $post_type);
			$post_type_details = get_post_type_object($post_type);
			$post_type_name = $post_type_details->labels->singular_name;
			if (empty($post_type_name)) {
				$post_type_name = $post_type;
			}

			echo '<tr><th scope="row">'.__('Title format for post type: ', 'WPSEODream').$post_type_name.'</th><td><input type="text" maxlength="200" name="WPSEODream_automatic_title_'.$post_type.'" value="'.$title.'"></td></tr>';
		}
	}

}
