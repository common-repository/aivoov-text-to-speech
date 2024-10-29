<?php
/**
 * Plugin Name:       AiVOOV Text to Speech
 * Plugin URI:        https://wordpress.org/plugins/aivoov-text-to-speech
 * Description:       By using AiVOOV Wordpress plugin, increase audience growth and the commitment that allows bloggers and publishers to convert all your articles into a natural and human-sounding speech with just one click. The plugin allows you to automatically convert text to speech and integrate it into your articles for seamless reading aloud.
 * Version:           1.1.9
 * Author:            aivoov
 * Author URI:        https://aivoov.com/wordpress/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       aivoov_tts
 */

require_once plugin_dir_path( __FILE__ ) . 'admin/admin.php';
require_once plugin_dir_path( __FILE__ ) . 'public/public.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/elementor-addon.php';


register_activation_hook( __FILE__, 'aivoov_tts_activate_plugin' );

function aivoov_tts_activate_plugin() {

	// ENABLE OPTIONS BY DEFAULT
	update_option( 'aivoov_tts_auto', false );
	update_option( 'aivoov_tts_count', false );
	update_option( 'aivoov_tts_player_background_color', 'eae6e6' );
	update_option( 'aivoov_tts_player_button_color', '37538b' );
	update_option( 'aivoov_tts_player_text_color', '28292e' );
	update_option( 'aivoov_tts_default_voice_id', 'en-US-GuyNeural' );
	update_option( 'aivoov_tts_player_position', 'after-content' );

}

register_deactivation_hook( __FILE__, 'aivoov_tts_deactivate_plugin' );

function aivoov_tts_deactivate_plugin() {
}

register_uninstall_hook(
	__FILE__,
	'aivoov_tts_uninstall_plugin'
);

// UNINSTALL OR DELETE PLUGIN
function aivoov_tts_uninstall_plugin() {
	delete_option( 'aivoov_tts_key' );
	delete_option( 'aivoov_tts_auto' );
	delete_option( 'aivoov_tts_count' );
	delete_option( 'aivoov_tts_player_background_color');
	delete_option( 'aivoov_tts_player_button_color');
	delete_option( 'aivoov_tts_player_text_color');
	delete_option( 'aivoov_tts_default_voice_id');
	delete_option( 'aivoov_tts_favorite_voices');
}
//
// ADMIN STYLES & SCRIPTS
//
function aivoov_tts_admin_style() {
	wp_enqueue_style('aivoov_tts-admin-style', plugin_dir_url(__FILE__) . 'admin/css/admin-style.css');
}
add_action('admin_enqueue_scripts', 'aivoov_tts_admin_style');

function aivoov_tts_admin_scripts() {
	wp_enqueue_script( 'aivoov_tts-admin-scripts', plugin_dir_url(__FILE__) . 'admin/js/admin-js.js');
}
add_action('admin_enqueue_scripts', 'aivoov_tts_admin_scripts');


//
// FRONT-END STYLES & SCRIPTS
//
function aivoov_tts_style() {
	wp_enqueue_style('aivoov_tts-plyr', plugin_dir_url(__FILE__) . 'public/css/plyr.css' ); 
	wp_enqueue_style('aivoov_tts-style', plugin_dir_url(__FILE__) . 'public/css/player.css' );
}
add_action( 'wp_enqueue_scripts', 'aivoov_tts_style' );

function aivoov_tts_scripts() {
	wp_enqueue_script('aivoov_tts-plyr', plugin_dir_url(__FILE__) . 'public/js/plyr.js', array( 'jquery' ) );
	wp_enqueue_script('aivoov_tts-scripts', plugin_dir_url(__FILE__) . 'public/js/audio-player.js', array( 'jquery' ) );
}
add_action( 'wp_enqueue_scripts', 'aivoov_tts_scripts' );


//
// UPDATE POSTS DATA
// hourly & and on every loging in
//

//add_action('wp_login', 'aivoov_tts_update_posts_data');

//add_action( 'aivoov_tts_update_posts_data_cron_hook', 'aivoov_tts_update_posts_data' );

if ( ! wp_next_scheduled( 'aivoov_tts_update_posts_data_cron_hook' ) ) {
    wp_schedule_event( time(), 'hourly', 'aivoov_tts_update_posts_data_cron_hook' );
}

function aivoov_tts_update_posts_data() {
	$args = array(
		'fields'	 => 'ids',
		'nopaging'	 => true,
		'meta_query' => array(
			array(
				'key' => 'aivoov_tts_enabled',
				'value' => true,
				'compare' => '=',
			)
		)
	);
	$aivoov_ttsEnabledPosts = get_posts($args);

	if ( get_option('aivoov_tts_key') && count($aivoov_ttsEnabledPosts) > 0 ) {
		$posts = aivoov_api_get_posts_info($aivoov_ttsEnabledPosts);
		if($posts){
			foreach ($posts as $i => $post) {

				$post_id = $post['id'];
				$playCount = $post['playCount'];
				$playMinutes = $post['playMinutes'];
				update_post_meta($post_id, 'aivoov_tts_count', $playCount);
				update_post_meta($post_id, 'aivoov_tts_time', $playMinutes);

			}
		}
	}

}

function enqueue_scripts() {
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'aivoov-free-tts', plugin_dir_url( __FILE__ ) . 'public/css/style.css' );
	
	/* FREE TEXT TO SPEECH EXPERIMENTAL
	$handle    = 'aivoov-tts';
	$src       = plugin_dir_url( __FILE__ ) . 'public/js/spoken-word.js';
	$deps      = array();
	$in_footer = true;
	wp_enqueue_script( $handle, $src, $deps, '1.0.1', $in_footer );

 
	$dialog_polyfill_included = apply_filters( 'spoken_word_include_dialog_polyfill', true );

	if ( $dialog_polyfill_included ) {
		wp_add_inline_script(
			$handle,
			sprintf(
				'if ( ! ( "showModal" in document.createElement( "dialog" ) ) ) { document.write( %s ); }',
				wp_json_encode( sprintf(
					'<script src="%s"></script><link rel="stylesheet" href="%s">',  
					esc_url( plugin_dir_url( __FILE__ ) . 'public/js/dialog-polyfill.js' ),
					esc_url( plugin_dir_url( __FILE__ ) . 'public/js/dialog-polyfill.css' )
				) )
			)
		);
	}
 
	wp_add_inline_script(
		'spoken-word',
		'spokenWord.setLocaleData( ' . wp_json_encode( get_jed_locale_data( 'spoken-word' ) ) . ' );',
		'after'
	); 
	$exports = array(
		'contentSelector' => '.entry-content, .hentry .entry-content, .h-entry .e-content, [itemprop="articleBody"]',
		'useDashicons'    => true,
	);
	wp_add_inline_script( $handle, sprintf( 'spokenWord.initialize( %s );', wp_json_encode( $exports ) ), 'after' );
	*/
}
if ( get_option('aivoov_tts_key') == '') { 
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_scripts' );
}
function get_jed_locale_data( $domain ) {
	$translations = \get_translations_for_domain( $domain );

	$locale = array(
		'domain'      => $domain,
		'locale_data' => array(
			$domain => array(
				'' => array(
					'domain' => $domain,
					'lang'   => \is_admin() ? \get_user_locale() : \get_locale(),
				),
			),
		),
	);

	if ( ! empty( $translations->headers['Plural-Forms'] ) ) {
		$locale['locale_data'][ $domain ]['']['plural_forms'] = $translations->headers['Plural-Forms'];
	}

	foreach ( $translations->entries as $msgid => $entry ) {
		$locale['locale_data'][ $domain ][ $msgid ] = $entry->translations;
	}

	return $locale;
}

?>
