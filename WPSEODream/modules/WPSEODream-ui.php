<?php
/**
 * Module for UI.
 */
if (!defined('ABSPATH')) return;

class WPSEODreamUi {
	
	private $content;
	
	public function __construct() {
		$this->content = new WPSEODreamContent();
		$this->meta = new WPSEODreamMeta();
	}
	
	public function add_post_metaboxes() {
		foreach (get_post_types(NULL, 'names') as $post_type) {
			add_meta_box('WPSEODream', '<span class="dashicons dashicons-admin-generic WPSEODream-animated"></span> WPSEODream', array($this, 'create_metabox_main'), $post_type, 'normal', 'high');
			add_meta_box('WPSEODream_serp_preview', '<span class="dashicons dashicons-visibility WPSEODream-animated"></span> '. __('SERP Preview', WPSEODream::TEXT_DOMAIN), array($this, 'create_metabox_serp_preview'), $post_type, 'normal', 'high');
		}
	}

	public function create_metabox_main($post){
		?>
		<div class="WPSEODream-settings">
		
		<p><strong><?php _e('Title', WPSEODream::TEXT_DOMAIN); ?></strong></p>
		<input type="text" maxlength="60" name="WPSEODream_title" id="WPSEODream_title" value="<?php echo get_post_meta($post->ID, 'WPSEODream_title', TRUE); ?>" /><br />
		
		<p><strong><?php _e('Meta Description', WPSEODream::TEXT_DOMAIN); ?></strong></p>
		<textarea maxlength="160" id="WPSEODream_description" name="WPSEODream_description"><?php echo get_post_meta($post->ID, 'WPSEODream_description', TRUE); ?></textarea>
		
		<?php if ($this->content->is_word_count_too_low($post->post_content)) : ?>				
		<p><?php echo sprintf(__('<span class="dashicons dashicons-warning WPSEODream-fail"></span> The article word count is too low. A minimum of %s words is recommended.', WPSEODream::TEXT_DOMAIN), WPSEODreamContent::RECOMMENDED_MINIMUM_POST_WORD_COUNT); ?></p>
		<?php endif; ?>
		
		<?php if (!$this->content->does_all_title_words_appear_in_content($this->meta->get_meta_title($post->post_title), $post->post_content)) : ?>				
		<p><?php _e('<span class="dashicons dashicons-warning WPSEODream-fail"></span> Not all the words in the title seem to appear in content. Consider adding them.', WPSEODream::TEXT_DOMAIN); ?></p>
		<?php endif; ?>	

		<?php if (!$this->content->content_contains_hyperlinks($post->post_content)) : ?>
		<p><?php _e('<span class="dashicons dashicons-warning WPSEODream-fail"></span> The content does not contain any hyperlinks. Consider adding them. ', WPSEODream::TEXT_DOMAIN); ?></p>

		<?php endif; ?>			
		
		</div>
		<?php 
	}
	
	public function create_metabox_serp_preview($post){
		?>
		<div class="WPSEODream-settings">

		<div class="WPSEODream-serp-preview">
			<div class="WPSEODream-preview-title">
			<?php 
				$title = get_post_meta($post->ID, 'WPSEODream_title', TRUE); 
						
				if (empty($title)) {
					$title = $this->meta->get_type_specific_title($post);
				}
				
				if (empty($title)) {
					$title = get_the_title($post->ID);
				}
				
				if (mb_strlen($title) > 60) {
					$title = mb_substr($title, 0, 60);
				}
				
				echo $title;
			?>
			</div>
			<div class="WPSEODream-preview-address">
			<?php echo get_permalink($post->ID); ?>
			</div>
			<div class="WPSEODream-preview-description">
			<?php 
				$description = get_post_meta($post->ID, 'WPSEODream_description', TRUE); 
				if (empty($description)) {
					$description = __('No description set.', WPSEODream::TEXT_DOMAIN);
				}
				
				if (mb_strlen($description) > 160) {
					$description = mb_substr($description, 0, 160) . ' ...';
				}
				
				echo $description;
			?>
			</div>
		</div>
		
		</div>
		<?php 
	}	
	
	public function seo_column_head($columns) {
		$columns['seo_status'] = __('SEO', WPSEODream::TEXT_DOMAIN);
		return $columns;
	}
	
	public function seo_column_content($column_name, $post_id) {
		if ($column_name === 'seo_status') {
			$seo_title = get_post_meta($post_id, 'WPSEODream_title', TRUE);
			$seo_description = get_post_meta($post_id, 'WPSEODream_description', TRUE);
			$post = get_post($post_id);
			
			$too_few_words = $this->content->is_word_count_too_low($post->post_content);
			$hyperlinks_exist = $this->content->content_contains_hyperlinks($post->post_content);
			
			if (!$too_few_words && $hyperlinks_exist && !empty($seo_title) && !empty($seo_description)) {
				echo '<span title="'.__('SEO for this item is in good condition.', WPSEODream::TEXT_DOMAIN).'" class="WPSEODream-table-icon WPSEODream-ok">&#10003;</span>';
			}
			else {
				echo '<span title="'.__('SEO for this item needs some work.', WPSEODream::TEXT_DOMAIN).'" class="WPSEODream-table-icon WPSEODream-fail">&#10007;</span>';
			}
		}
	}	
}