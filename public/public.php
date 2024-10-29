<?php
if (get_option('aivoov_tts_key')) {
	function aivoov_get_post_types ( ) {
			$get_post_types = get_post_types();
			unset($get_post_types['attachment'],
				$get_post_types['revision'],
				$get_post_types['wp_template'],
				$get_post_types['custom_css'],
				$get_post_types['wp_global_styles'],
				$get_post_types['wp_navigation'],
				$get_post_types['nav_menu_item'],
				$get_post_types['customize_changeset'],
				$get_post_types['user_request'],
				$get_post_types['wp_template'],
				$get_post_types['oembed_cache'],
				$get_post_types['wp_block'],
				$get_post_types['wp_template_part']
			);  
			return $get_post_types;
	}
	function aivoov_tts_display_player ( $content ) {
		if (  in_the_loop() && ! ( is_singular( aivoov_get_post_types() ) ) ) { return $content; }

		$id = get_the_ID();
		$enabled = boolval( get_post_meta($id, 'aivoov_tts_enabled', true) );
		$audio_path = get_post_meta($id, 'aivoov_tts_audioFile', true);
		$permission = json_decode(get_option('aivoov_tts_permission')); 
		if ( $enabled &&  get_option('aivoov_tts_player_position')  === 'bottom-fixed') {
			
			$p = do_shortcode("[aivoov_player post_id=$id]");
			$custom_content = ' 
			<div class="aivoov_tts-audio-player">
				 '.$p.'
			</div>';
			$custom_content = $content . $custom_content;
			return $custom_content;
		} else {
			return $content;
		}

	}
	
	add_filter('the_content', 'aivoov_tts_display_player');
	
	function add_player_to_title( $title,$id ) {
		 
		if ( is_admin() ) { return  $title; } 
		if (in_the_loop() && !(is_singular( aivoov_get_post_types() ))) { return $title; }
		$id = get_the_ID();
		$enabled = boolval( get_post_meta($id, 'aivoov_tts_enabled', true) );
		$audio_path = get_post_meta($id, 'aivoov_tts_audioFile', true);
		$custom_content = do_shortcode("[aivoov_player post_id=$id]");
		if ( $enabled ) {
			if (get_option('aivoov_tts_player_position') === 'before-title' ) {
				return $custom_content = $title . $custom_content; 
			}	
			if (get_option('aivoov_tts_player_position') === 'after-title' ) {
				return $custom_content = $custom_content.$title;
			}  
			return $title;
		} else {
			  return $title;
		} 
	}
	add_filter('the_title', 'add_player_to_title', 10, 2 );
	function aivoov_tts_display_player_post ( $content ) {
		
		if ( is_admin() ) { return  $content; }
		if ( in_the_loop() && ! ( is_singular( aivoov_get_post_types() ) ) ) { return $content; }
		$id = get_the_ID();
		$enabled = boolval( get_post_meta($id, 'aivoov_tts_enabled', true) );
		$audio_path = get_post_meta($id, 'aivoov_tts_audioFile', true);
		$custom_content = do_shortcode("[aivoov_player post_id=$id]");
		
		if ( $enabled ) {
		if (get_option('aivoov_tts_player_position') === 'before-content' ) {
			return $custom_content = $content . $custom_content;
        }	
		else if ( get_option('aivoov_tts_player_position')  === 'after-content') {
			return $custom_content = $custom_content.$content;
		} else{
			return $content;
		}
		} else {
			return $content;
		}

	}
	add_filter('the_content', 'aivoov_tts_display_player_post');
	function aivoov_player( $_atts ) {
		
        if ( isset( $_GET['aivoov-template'] ) && 'aivoov' === $_GET['aivoov-template'] ) { return false; }
		$defaults = array(
			'post_id' => get_the_ID(),
			'size' => '',
		);
		$atts = shortcode_atts( $defaults, $_atts );
		// Confirm that $post_id is an integer.
		$atts['post_id'] = absint( $atts['post_id'] );
		$size = $atts['size'];
		 
		$id = $atts['post_id'];
		$enabled = boolval( get_post_meta($id, 'aivoov_tts_enabled', true) );
		$audio_path = get_post_meta($id, 'aivoov_tts_audioFile', true);
		
		$permission = json_decode(get_option('aivoov_tts_permission'));
		$html = "";
		ob_start();
		if ($enabled) {
		?>
		<style>
			.plyr__controls .plyr__controls__item:first-child{
				background:#<?php echo esc_attr(get_option('aivoov_tts_player_button_color')) ?> !important; 
			}
			:root{
			--plyr-color-main:#<?php echo esc_attr(get_option('aivoov_tts_player_button_color')) ?>;  
			--plyr-audio-controls-background:#<?php echo esc_attr(get_option('aivoov_tts_player_background_color')) ?>;
			--plyr-audio-control-color:#<?php echo esc_attr(get_option('aivoov_tts_player_text_color')) ?>;
			}
			[data-plyr="mute"] { color: #<?php echo esc_attr(get_option('aivoov_tts_player_button_color')) ?> !important; }
			.aivoov-text-color{color:#<?php echo esc_attr(get_option('aivoov_tts_player_text_color')) ?>}
		</style>  
		<div class="audio_player">
		<audio class="js-player" controls="">
			<source src="<?php echo $audio_path; ?>" type="audio/mp3">
			<source src="<?php echo $audio_path; ?>" type="audio/ogg" />
		</audio> 
		<?php if(!$permission->white_labelled) { ?>
		  <div class="powerd_by aivoov-text-color"><p><a rel="nofollow" target="_blank" href="http://aivoov.com/?um_source=plugin_powered_by" style="color: inherit;">Powered by AiVOOV</a></p></div>
		<?php } ?> 
		</div>
		<?php
		}
		$html = ob_get_clean();
		return $html;
	}
	add_shortcode("aivoov_player", "aivoov_player");
}

?>
