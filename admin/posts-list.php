<?php 

// 
// ADD COLUMNS
// 

// ENABLE
if ( get_option('aivoov_tts_key') ) {

	add_filter( 'manage_posts_columns', 'aivoov_tts_add_enable_column' );
	function aivoov_tts_add_enable_column( $columns ) {
		$columns_merged = array_merge( $columns, 
		array( 'aivoov_tts_enable' => __( 'TTS', 'aivoov_tts' ) ) );

		$columns_ordered = array();
		foreach($columns_merged as $key => $value) {
			if ($key == 'date') {
				$columns_ordered['aivoov_tts_enable'] = 'aivoov_tts_enable';
			}
				$columns_ordered[$key] = $value;
		}

		return $columns_ordered;
	}

	add_action( 'manage_posts_custom_column' , 'aivoov_tts_display_enable_column', 10, 2 );
	function aivoov_tts_display_enable_column( $column, $post_id ) {
	    if ($column == 'aivoov_tts_enable'){
	    	$checked = boolval( get_post_meta($post_id, 'aivoov_tts_enabled', true) );
	        echo '<label class="aivoov_tts-switch">
					<input type="checkbox"' . (esc_attr($checked) ? 'checked' : '') . ' onclick="aivoov_handle_enable_toggle(event, this, ' . esc_attr($post_id) . '); " class="listen_button" id="listen_button-97" name="listen_button">
					<span class="aivoov_tts-slider round"></span>
				</label>';
	    }
	}

}

// PLAY TIME
if ( get_option('aivoov_tts_key') && get_option('aivoov_tts_time') ) {

	add_filter( 'manage_posts_columns', 'aivoov_tts_add_time_column' );
	function aivoov_tts_add_time_column( $columns ) {
		$columns_merged = array_merge( $columns, 
		array( 'aivoov_tts_time' => __( 'Play minutes', 'aivoov_tts' ) ) );

		$columns_ordered = array();
		$flag = FALSE;
		foreach($columns_merged as $key => $value) {
			if ($flag == TRUE) {
				$columns_ordered['aivoov_tts_time'] = 'aivoov_tts_time';
				$flag = FALSE;
			}
			if ($key == 'aivoov_tts_enable') {
				$flag = TRUE;
			}
				$columns_ordered[$key] = $value;
		}

		return $columns_ordered;
	}

	add_action( 'manage_posts_custom_column' , 'aivoov_tts_display_time_column', 10, 2 );
	function aivoov_tts_display_time_column( $column, $post_id ) {
	    if ($column == 'aivoov_tts_time'){
	    	echo wp_kses(get_post_meta($post_id, 'aivoov_tts_time', true));
	    }
	}

}

// PLAY COUNT
if ( get_option('aivoov_tts_key') && get_option('aivoov_tts_count') ) {

	add_filter( 'manage_posts_columns', 'aivoov_tts_add_count_column' );
	function aivoov_tts_add_count_column( $columns ) {
		$columns_merged = array_merge( $columns, 
		array( 'aivoov_tts_count' => __( 'Play count', 'aivoov_tts' ) ) );

		$columns_ordered = array();
		$flag = FALSE;
		foreach($columns_merged as $key => $value) {
			if ($flag == TRUE) {
				$columns_ordered['aivoov_tts_count'] = 'aivoov_tts_count';
				$flag = FALSE;
			}
			if ($key == 'aivoov_tts_enable') {
				$flag = TRUE;
			}
				$columns_ordered[$key] = $value;
		}

		return $columns_ordered;
	}

	add_action( 'manage_posts_custom_column' , 'aivoov_tts_display_count_column', 10, 2 );
	function aivoov_tts_display_count_column( $column, $post_id ) {
	    if ($column == 'aivoov_tts_count'){
	    	echo wp_kses(get_post_meta($post_id, 'aivoov_tts_count', true));
	    }
	}

}

// 
// BULK ACTIONS
// 
if ( get_option('aivoov_tts_key') ) {
	// REGISTER BULK ACTIONS
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
	$get_post_types['oembed_cache']
	); 
	foreach($get_post_types as $bulk){
		add_filter( 'bulk_actions-edit-'.$bulk, 'aivoov_tts_register_bulk_actions' );
	}
	function aivoov_tts_register_bulk_actions($bulk_actions) {
		$bulk_actions['aivoov_tts_add_audio'] = __( 'Add Audio', 'aivoov_tts');
		$bulk_actions['aivoov_tts_remove_audio'] = __( 'Remove Audio', 'aivoov_tts');
		return $bulk_actions;
	}

	// HANDLE BULK ACTIONS
	add_filter( 'handle_bulk_actions-edit-post', 'aivoov_tts_bulk_actions_handler', 10, 3 );
	function aivoov_tts_bulk_actions_handler( $redirect_to, $doaction, $post_ids ) {

		if ( $doaction == 'aivoov_tts_add_audio' ) { 
			$args = array ( $post_ids );
			wp_schedule_single_event(time(), 'bulk_audio_action', $args);
			$redirect_to = preg_replace('/\?.*/', '', $redirect_to);
			$redirect_to = add_query_arg( 'bulk_aivoov_tts_audio_added', true, $redirect_to );
			return $redirect_to;
		}

		if ( $doaction == 'aivoov_tts_remove_audio' ) {
			$count = 0;

			foreach ( $post_ids as $post_id ) {
				$enabled = boolval( get_post_meta($post_id, 'aivoov_tts_enabled', true) );

				if ($enabled) {
					update_post_meta($post_id, 'aivoov_tts_enabled', false);
					$count++;
				}
			}

			$redirect_to = preg_replace('/\?.*/', '', $redirect_to);
			$redirect_to = add_query_arg( 'bulk_aivoov_tts_audio_removed', $count, $redirect_to );
			return $redirect_to;
		}

	}
	add_action('bulk_audio_action', 'bulk_audio');
	

	function bulk_audio($post_ids)
	{
		$count = 0;
		$myfile = fopen(wp_upload_dir()['basedir']."/aivoov_log-".date("Y-m-d").".txt", "a");
		foreach ($post_ids as $post_id ) {
			$enabled = boolval( get_post_meta($post_id, 'aivoov_tts_enabled', true) );
			if (!$enabled) {
				$res = aivoov_convert_post($post_id);
				if ($res) {
					$audioFile = esc_url_raw($res->audioFile);
					$playCount = intval($res->playCount);
					$playMinutes = intval($res->playMinutes);

					if($audioFile){
						update_post_meta($post_id, 'aivoov_tts_enabled', true);
						update_post_meta($post_id, 'aivoov_tts_audioFile', $audioFile);
					} 
					$count++;
					$text = date('Y-m-d H:m:i')." Audio Created for post id $post_id \n \n";
				} else {
					$text = date('Y-m-d H:m:i')." Audio Create failed for post id $post_id \n \n";
				}
			}
			fwrite($myfile, $text);
			sleep(7); 
		}
		fclose($myfile);
	}
}

?>