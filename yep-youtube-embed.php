<?php
/*
 * Plugin Name:			Youtube Embed Performance
 * Plugin URI:			https://www.helper-wp.com/plugins/youtube-embed-plugin-wordpress/
 * Description:			This plugin make your video in publications fast and responsive.
 * Version:				1.0.4
 * Requires at least:	4.8.3
 * Requires PHP:		5.6
 * Author:				Webamator
 * Author URI:			https://www.helper-wp.com/wordpress-freelancer/
 * License:				GPL v2 or later
 * License URI:			http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:			yep-youtube-embed
 * Domain Path:			/languages/
*/

/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	load_plugin_textdomain( 'yep-youtube-embed', false, basename( dirname( __FILE__ ) ) . '/languages/' );

	require_once ( plugin_dir_path ( __FILE__ ) . 'includes/webamator-check-requirement.php' );
	$waRequirements = new WebamatorCheckRequirement();

	$requirements = array (
		'file'	=> plugin_basename( __FILE__ ),
		'name'	=> 'Youtube Embed Performance',
		'slug'	=> 'yep-youtube-embed',
		'woo'	=> false
	);
	
	$waRequirements->set_requirements( $requirements );
	$waRequirements->check_requirement( $requirements );



	require_once( 'includes/webamator-check-plugins.php' );
	$waPlugins = new WebamatorCheckPlugins();

	$plugin_data = array (
		'text_domain'	=> 'yep-for-wordpress',
	);
	$waPlugins->set_plugin_data( $plugin_data );
	$waPlugins->add_wa_plugins_menu( $plugin_data );

	//let`s go :)

	register_activation_hook(__FILE__, 'yep_for_wordpress_set_options');
	register_deactivation_hook(__FILE__, 'yep_for_wordpress_unset_options');
	add_action('wp_head', 'yep_for_wordpress_style', 100);
	add_action('wp_enqueue_scripts', 'yep_for_wordpress_script');
	add_action('admin_enqueue_scripts', 'yep_for_wordpress_admin_style' );
	add_action('admin_menu', 'register_yep_for_wordpress_submenu_page');
	add_action('admin_menu', 'options_yep_for_wordpress_submenu_page');
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'yep_for_wordpress_action_links' );

	function yep_for_wordpress_set_options() {
	
		add_option('yep_version', '1.0.4');
		add_option('yep_date_install', time());
		add_option('yep_settings', array( 'controls' => 1 ) );

	}


	function yep_for_wordpress_unset_options() {

		delete_option('yep_version');
		delete_option('yep_date_install');
		delete_option('yep_settings');

	}



	function yep_for_wordpress_style() {

		echo '<style>';
		echo '.yep-youtube {position: relative;padding-bottom: 56.23%;height: 0;overflow: hidden;max-width: 100%;background: #000;}';
		echo '.yep-youtube iframe,.yep-youtube object,.yep-youtube embed {position: absolute;top: 0;left: 0;width: 100%;height: 100%;z-index: 100;background: transparent;}';
		echo '.yep-youtube img {bottom: 0;display: block;left: 0;margin: auto;max-width: 100%;width: 100%;position: absolute;right: 0;top: 0;border: none;height: auto;cursor: pointer;-webkit-transition: .4s all;-moz-transition: .4s all;transition: .4s all;}';
		echo '.yep-youtube .yepPlayButton {position: absolute;left: 50%;top: 50%;width: 68px;height: 48px;margin-left: -34px;margin-top: -24px;}';
		echo '.yep-youtube .yepPlayButton:hover{cursor: pointer;}';
		echo '.yep-youtube .yepPlayButton:hover .ytp-large-play-button-bg{fill: #f00;fill-opacity: 1;}';
		echo '.wp-block-embed-youtube .yep-youtube{position: initial;padding-bottom:0;}';
		echo '</style>';

	}

	function yep_for_wordpress_admin_style() {
	
		wp_enqueue_style( 'yep_admin_style', plugins_url('assets/css/admin.css', __FILE__));
	
	}

	function yep_for_wordpress_script() {
	
		wp_enqueue_script( 'yep-for-wordpress', plugins_url('/assets/js/yep.js', __FILE__), array(), '1.0', true);
	
	}
	
	function register_yep_for_wordpress_submenu_page() {

		add_submenu_page( 'wa-plugins', __( 'YouTube Embed', 'yep-youtube-embed' ), __( 'YouTube Embed', 'yep-youtube-embed' ), 'manage_options', 'yep_youtube_embed', 'yep_for_wordpress_options_page' ); 

	}	

	function options_yep_for_wordpress_submenu_page() {

		add_submenu_page( 'options-general.php', __( 'YouTube Embed', 'yep-youtube-embed' ), __( 'YouTube Embed', 'yep-youtube-embed' ), 'manage_options', 'yep_youtube_embed', 'yep_for_wordpress_options_page' ); 

	}	

	function yep_for_wordpress_action_links( $links ) {

		$links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=yep_youtube_embed') ) .'">'.__( 'Settings', 'yep-youtube-embed' ).'</a>';
		return $links;

	}	
	
	
	
	
	function yep_for_wordpress_mod_content($content){
	
		//youtube.com\^(?!href=)
		if (preg_match_all('#(?<!href\=\")https\:\/\/www.youtube.com\/watch\?([\\\&\;\=\w\d]+|)v\=[\w\d]{11}+([\\\&\;\=\w\d]+|)(?!\"\>)#', $content, $youtube_match))
		{

			foreach ($youtube_match[0] as $youtube_url) {

				parse_str( parse_url( wp_specialchars_decode( $youtube_url ), PHP_URL_QUERY ), $youtube_video );

				if (isset($youtube_video['v'])){
					$content = str_replace($youtube_url, '<div class="yep-wrapper"><div class="yep-youtube" data-id="'.$youtube_video['v'].'"'.yep_embeds_data_values().'></div></div>', $content);
				}

			}

		}
		
		//youtu.be
		if (preg_match_all('#(?<!href\=\")https\:\/\/youtu.be/([\\\&\;\=\w\d]+|)(?!\"\>)#', $content, $youtube_match))
		{
		
			foreach ($youtube_match[0] as $youtube_url) {
			
				$youtube_video = str_replace('https://youtu.be/', '', $youtube_url);
			
				if (isset($youtube_video)){
					$content = str_replace($youtube_url, '<div class="yep-wrapper"><div class="yep-youtube" data-id="'.$youtube_video.'"'.yep_embeds_data_values().'></div></div>', $content);
				}
			
			}
		
		}
	
		return $content;

	}
	add_filter('the_content', 'yep_for_wordpress_mod_content',1);	



//	[yep_youtube width="400" height="300" nocookie="0" controls="1" start="0"]11111111111[/yep_youtube]
	function wa_shortcode_yep_youtube($atts=false, $content) {
	
		if (!preg_match('/^[\w\d]{11}$/i', $content)) {
			return;
		}
		
		$wrapper_style	=
		$wrapper_width	= 
		$wrapper_height	=
		$data_nocookie	=
		$data_controls	=
		$data_start		= false;
		
		if (isset($atts['width']) && preg_match('/^[\d]+$/', $atts['width'])){
			$wrapper_width = 'width:'.$atts['width'].'px;';
		}
		if (isset($atts['height']) && preg_match('/^[\d]+$/', $atts['height'])){
			$wrapper_height = 'height:'.$atts['height'].'px;';
		}
		if (isset($atts['nocookie']) && preg_match('/^(0|1)$/', $atts['nocookie'])){
			$data_nocookie = ' data-nocookie="'.$atts['nocookie'].'"';
		}
		if (isset($atts['controls']) && preg_match('/^(0|1)$/', $atts['controls'])){
			$data_controls = ' data-controls="'.$atts['controls'].'"';
		}
		if (isset($atts['start']) && ( yep_startfrom_value( $atts['start'] ) > 0 ) ){
			$data_start = ' data-start='.yep_startfrom_value( $atts['start'] );
		}
		if ($wrapper_width || $wrapper_height){
			$wrapper_style = ' style="'.$wrapper_width.$wrapper_height.'"';
		}

		return '<div class="yep-wrapper"'.$wrapper_style.'><div class="yep-youtube" data-id="'.$content.'"'.$data_nocookie.$data_controls.$data_start.'></div></div>';


	}
	add_shortcode ('yep_youtube', 'wa_shortcode_yep_youtube');



	function yep_for_wordpress_options_page(){
	?>
		<div class="wrap">
			
			<h2 id="title"><?php _e( 'Settings for YEP', 'yep-youtube-embed' ) ?></h2>

			<form action="options.php" method="POST">
			<?php
				settings_fields( 'option_group' );
				do_settings_sections( 'yep_settings_page' ); 
				submit_button();
			?>
			</form>

			<h2><?php _e('Shortcodes:', 'yep-youtube-embed') ?></h2>
		
			<p><?php _e('Simple shortcode:', 'yep-youtube-embed') ?></p>
			
			<p><?php _e('[yep_youtube]XXXXXXXXXXX[/yep_youtube]', 'yep-youtube-embed') ?></p>
			
			<p><?php _e('Shortcode with attributes:', 'yep-youtube-embed') ?></p>
			
			<p><?php _e('[yep_youtube width="XXX" height="XXX" nocookie="0" controls="1" start="0"]XXXXXXXXXXX[/yep_youtube]', 'yep-youtube-embed') ?></p>

		</div>
	<?php
	}


	add_action('admin_init', 'yep_plugin_settings');

	function yep_plugin_settings(){
		
		//$option_group, $option_name, $sanitize_callback
		register_setting( 
			'option_group',
			'yep_settings',
			'yep_sanitize_callback'
			);

		//$id, $title, $callback, $page
		add_settings_section(
			'yep_settings_section', 
			__('Options:', 'yep-youtube-embed'), 
			'', 
			'yep_settings_page' 
			); 

		// $id, $title, $callback, $page, $section, $args
		add_settings_field(
			'startfrom_field', 
			__('Start at', 'yep-youtube-embed'),
			'get_startfrom_value', 
			'yep_settings_page', 
			'yep_settings_section' 
			);
		add_settings_field(
			'nocookie_field', 
			__('Privacy Enhanced', 'yep-youtube-embed'),
			'get_nocookie_value', 
			'yep_settings_page', 
			'yep_settings_section' 
			);
		add_settings_field(
			'controls_field', 
			__('Сontrols', 'yep-youtube-embed'), 
			'get_controls_value', 
			'yep_settings_page', 
			'yep_settings_section' 
			);


	}


	function get_startfrom_value(){
		$val = get_option('yep_settings');
		$val = isset( $val['startfrom'] ) ? $val['startfrom'] : null;
		?>
		<label><input type="time" name="yep_settings[startfrom]" value="<?php echo esc_attr( $val ) ?>" class="without_ampm" step="1" min="00:00:00" max="02:59:59" /> <?php _e('Empty or format XX:XX:XX (Hours:Minutes:Seconds).', 'yep-youtube-embed') ?></label>
		<?php
	}

	function get_nocookie_value(){
		$val = get_option('yep_settings');
		$val = isset( $val['nocookie'] ) ? $val['nocookie'] : null;
		?>
		<label><input type="checkbox" name="yep_settings[nocookie]" value="1" <?php checked( 1, $val ) ?> /> <?php _e('Enable privacy-enhanced mode.', 'yep-youtube-embed') ?></label>
		<?php
	}

	function get_controls_value(){
		$val = get_option('yep_settings');
		$val = isset( $val['controls'] ) ? $val['controls'] : null;
		?>
		<label><input type="checkbox" name="yep_settings[controls]" value="1" <?php checked( 1, $val ) ?> /> <?php _e('Show player controls.', 'yep-youtube-embed') ?></label>
		<?php
	}


	function yep_sanitize_callback( $options ){ 

		foreach( $options as $name => $val ){
			if( $name == 'startfrom' )
				$val = strip_tags( $val );

			if( $name == 'nocookie' )
				$val = intval( $val );

			if( $name == 'controls' )
				$val = intval( $val );
			}

		return $options;
	}
	
	
	function yep_startfrom_value( $str ){

		$time = explode(':', $str);

		if (empty($time)) {
			$result 	= false;
		}
		
		if (isset($time[0]) && !isset($time[1]) && !isset($time[2])) {
			if ( preg_match('/^[\d]+$/', $time[0] ) ) {
				$result 	= $time[0];
			} else {
				$result 	= false;
			}
		}
		
		if (isset($time[0]) && isset($time[1]) && !isset($time[2])) {
			if ( preg_match('/^(\d{1,2})$/', $time[0] ) && preg_match('/^(\d{1,2})$/', $time[1] ) && $time[0] < 60 && $time[1] < 60 ) {
				$seconds	= $time[1];
				$minutes	= $time[0]*60;
				$result		= $minutes+$seconds;
			} else {
				$result 	= false;
			}
		}

		if (isset($time[0]) && isset($time[1]) && isset($time[2])) {
			if ( preg_match('/^(\d{1,2})$/', $time[0] ) && preg_match('/^(\d{1,2})$/', $time[1] ) && preg_match('/^(\d{1,2})$/', $time[2] ) && $time[0] < 12 && $time[1] < 60 && $time[2] < 60  ) {
			$seconds	= $time[2];
			$minutes	= $time[1]*60;
			$hours		= $time[0]*3600;
			$result		= $hours+$minutes+$seconds;
			} else {
				$result 	= false;
			}

		}

	
		return $result;
	
	}


	function yep_embeds_data_values() {

		$data_nocookie	=
		$data_controls	=
		$data_start		= false;

		$yep_settings = get_option('yep_settings');

		if (isset($yep_settings['nocookie']) && preg_match('/^(0|1)$/', $yep_settings['nocookie'])){
			$data_nocookie = ' data-nocookie="'.$yep_settings['nocookie'].'"';
		}
		if (!isset($yep_settings['controls']) || $yep_settings['controls'] == 0){
			$data_controls = ' data-controls="0"';
		}
		if (isset($yep_settings['startfrom']) && ( yep_startfrom_value( $yep_settings['startfrom'] ) > 0 ) ){
			$data_start = ' data-start='.yep_startfrom_value($yep_settings['startfrom']);
		}
	
		return $data_nocookie.$data_controls.$data_start;
	
	}


?>