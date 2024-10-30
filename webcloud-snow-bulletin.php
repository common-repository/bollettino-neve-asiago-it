<?php

namespace Webcloud\SnowBulletin;

/*
Plugin Name: Bollettino Neve Asiago.it
Description: Il bollettino neve di Asiago.it direttamente nel tuo WordPress.
Version: 1.0.8
Author: Webcloud
Author URI: https://www.webcloud.it/
*/

function get_snowbulletin($api_key, $args) {
	require_once plugin_dir_path(__FILE__) . 'views/snow-bulletin.php';
}

//[asiagosnowbulletin]
function asiagosnowbulletin($atts) {
	ob_start();
	get_snowbulletin(get_option('webcloud_snow_bulletin_options')['api_key'], $atts);
	return ob_get_clean();
}
add_shortcode('asiagosnowbulletin', 'Webcloud\SnowBulletin\asiagosnowbulletin');

class Widget extends \WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'webcloud_snow_bulletin_widget', // Base ID
			esc_html__('Bollettino Neve Asiago.it', 'text_domain'), // Name
			['description' => esc_html__('Il bollettino neve di Asiago.it', 'text_domain')]// Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args	 Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget($args, $instance) {
		echo $args['before_widget'];
		if (!empty($instance['title'])) {
			echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
		}
		get_snowbulletin(get_option('webcloud_snow_bulletin_options')['api_key'], []);
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance) {
		$title = !empty($instance['title']) ? $instance['title'] : '';
		?>
			<p>
				<label for="<?=esc_attr($this->get_field_id('title'));?>"><?php esc_attr_e('Titolo:', 'text_domain');?></label>
				<input class="widefat" id="<?=esc_attr($this->get_field_id('title'));?>" name="<?=esc_attr($this->get_field_name('title'));?>" type="text" value="<?=esc_attr($title);?>">
			</p>
			<?php
}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update($new_instance, $old_instance) {
		$instance = [];
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

		return $instance;
	}
}

class Settings {
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		add_action('admin_menu', array($this, 'add_plugin_page'));
		add_action('admin_init', array($this, 'page_init'));
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		// This page will be under "Settings"
		add_options_page(
			'Bollettino Neve Asiago.it',
			'Bollettino Neve Asiago.it',
			'manage_options',
			'webcloud-snow-bulletin',
			array($this, 'create_admin_page')
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		// Set class property
		$this->options = get_option('webcloud_snow_bulletin_options');
		?>
			<div class="wrap">
				<h1>Impostazioni Bollettino Neve Asiago.it</h1>
				<form method="post" action="options.php">
				<?php
					// This prints out all hidden setting fields
					settings_fields('webcloud_snow_bulletin_settings');
					do_settings_sections('webcloud-snow-bulletin');
					submit_button();
				?>
				</form>
			</div>
			<?php
}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting(
			'webcloud_snow_bulletin_settings', // Option group
			'webcloud_snow_bulletin_options', // Option name
			array($this, 'sanitize') // Sanitize
		);

		add_settings_section(
			'webcloud_snow_bulletin_settings_general', // ID
			null, // Title
			null, // Callback
			'webcloud-snow-bulletin' // Page
		);

		add_settings_field(
			'api_key', // ID
			'Chiave API', // Title
			array($this, 'api_key_callback'), // Callback
			'webcloud-snow-bulletin', // Page
			'webcloud_snow_bulletin_settings_general' // Section
		);

		add_settings_field(
			'mostratutto_default', // ID
			'Mostra solo piste aperte come opzione predefinita', // Title
			array($this, 'mostratutto_default_callback'), // Callback
			'webcloud-snow-bulletin', // Page
			'webcloud_snow_bulletin_settings_general' // Section
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize($input) {
		$new_input = [];

		if (isset($input['api_key'])) {
			$new_input['api_key'] = sanitize_text_field($input['api_key']);
		}

		if (isset($input['mostratutto_default'])) {
			$new_input['mostratutto_default'] = sanitize_text_field($input['mostratutto_default']);
		}

		return $new_input;
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function api_key_callback() {
		printf(
			'<input type="text" id="api-key" name="webcloud_snow_bulletin_options[api_key]" value="%s" />',
			isset($this->options['api_key']) ? esc_attr($this->options['api_key']) : ''
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function mostratutto_default_callback() {
		printf(
			'<input type="checkbox" id="toggle-default" name="webcloud_snow_bulletin_options[mostratutto_default]" %s />',
			(isset($this->options['mostratutto_default']) && $this->options['mostratutto_default'] == 'on') ? 'checked' : ''
		);
	}
}

if (is_admin()) {
	new Settings();
}

add_action('widgets_init', function () {
	register_widget('Webcloud\SnowBulletin\Widget');
});
