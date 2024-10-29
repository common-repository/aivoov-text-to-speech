<?php

function aivoov_tts_notices(){
    global $pagenow;

	//
	// CTA WHEN NO POST IS ENABLED
	//
	$args = array(
		'fields'	 => 'ids',
		'meta_query' => array(
			array(
				'key' => 'aivoov_tts_enabled',
				'value' => true,
				'compare' => '=',
			)
		)
	);
	$posts = get_posts($args);
	$EnabledPosts = count($posts);
	if ( get_option('aivoov_tts_key') && $EnabledPosts == 0 ) {

		$args = array(
			'numberposts' => '1',
			'fields'	  => 'id'
		);
		$newest_post = wp_get_recent_posts( $args );
		if ( count($newest_post) > 0 ) {

			$id = $newest_post[0]['ID'];
			echo '<div class="notice notice-error is-dismissible aivoov_tts-notice aivoov_tts-notice--add-first">
					<p>' . __('Try adding audio to your first post. Click “Add audio” to convert your latest post into natural sounding speech and make it available for seamless playback. Or go to “Posts” section to add manualy.', 'aivoov_tts') . '</p>
					<a href="#" class="button button-primary" id="aivoov_tts_add_first_audio" data-id="' . esc_attr($id) . '">' . __('Add audio', 'aivoov_tts') . '</a>
				</div>';

		}

	}

	//
	// UPDATE AVAILABLE
	//
	if ( get_option('aivoov_tts_key') ) {
		$update_plugins = get_site_transient( 'update_plugins' );
		if( isset( $update_plugins->response[ 'aivoov/aivoov.php' ] ) ) {
			echo '<div class="notice notice-success is-dismissible">
					<p>' . __('Text to Speech by AiVOOV has a new version available. Click') . ' <a href="/wp-admin/plugins.php" >' . __('here', 'aivoov_tts') . '</a> ' . __('to update it.') . '</p>
				</div>';
		}
	}

	//
	// UNABLE TO CONVERT
	//
	if ( get_option('aivoov_tts_key') ) {
		$queries = array();
		wp_parse_str($_SERVER['QUERY_STRING'], $queries);
		if ( isset($queries['aivoov_tts_unable_to_convert']) && $queries['aivoov_tts_unable_to_convert'] === '1') {
			echo '<div class="notice notice-warning is-dismissible">
					<p>' . __('AiVOOV was unable to convert the post. Please visit the AiVOOV account to make sure you have credits available. If the problem persists please reach out in the', 'aivoov_tts') . ' <a href="https://aivoov.com/ticket" >' . __('help center', 'aivoov_tts') . '</a>.</p>
				</div>';
		}
	}

	//
	// CONVERTED ONE POST
	//
	if ( get_option('aivoov_tts_key') ) {
		$queries = array();
		wp_parse_str($_SERVER['QUERY_STRING'], $queries);
		if ( isset($queries['aivoov_tts_successfuly_converted']) && $queries['aivoov_tts_successfuly_converted'] === '1') {
			echo '<div class="notice notice-success is-dismissible">
					<p>' . __('AiVOOV has added audio for your post.') . '</p>
				</div>';
		}
	}

	//
	// POSTS LIST - BULK POSTS CONVERTED
	//
	if ( get_option('aivoov_tts_key') ) {
		$queries = array();
		wp_parse_str($_SERVER['QUERY_STRING'], $queries);
		if ( isset($queries['bulk_aivoov_tts_audio_added']) && $queries['bulk_aivoov_tts_audio_added'] === '1') {
			echo '<div class="notice notice-success is-dismissible">
					<p>' . __('Bulck Audio process has been started it will take some time to create your post audio.') . '</p>
				</div>';
		}
	}

    //
    // ADMIN NOTICES
    //
    if ( $pagenow == 'admin.php' ) {

		$queries = array();
		wp_parse_str($_SERVER['QUERY_STRING'], $queries);

		if ( isset($queries['page']) && isset($queries['status']) ) {
			if ( $queries['page'] === 'aivoov_tts' && $queries['status'] === 'success') {
				echo '<div class="notice notice-success is-dismissible">
						<p>' . __('Congratulations! Your aivoov Text to Speech plugin is enabled.', 'aivoov_tts') . '</p>
					</div>';
			} else if ($queries['page'] === 'aivoov_tts' && $queries['status'] === 'failure') {
				echo '<div class="notice notice-error is-dismissible">
						<p>' . __($queries['msg'], 'aivoov_tts').'</p>
					</div>';
			} else if ($queries['page'] === 'aivoov_tts' && $queries['status'] === 'settingschanged') {
				echo '<div class="notice notice-success is-dismissible">
						<p>' . __('Settings successfully changed.', 'aivoov_tts') . '</p>
					</div>';
			}else if ($queries['page'] === 'aivoov_tts' && $queries['status'] === 'sync') {
				echo '<div class="notice notice-success is-dismissible">
						<p>' . __('Voice synchronized successfully.', 'aivoov_tts') . '</p>
					</div>';
			}
		}

    } else {

    	//
		// CONNECT WITH APP
		//
    	if (!get_option('aivoov_tts_key')) {

    		echo '<div class="notice notice-error is-dismissible aivoov_tts-notice aivoov_tts-notice--connect">
					<p>' . __('Connect with your aivoov.com account to enable the plugin.', 'aivoov_tts') . '</p>
					<div class="aivoov_tts-notice--connect__button-wrapper">
						<a href="' . esc_url(get_bloginfo("url")) . '/wp-admin/admin.php?page=aivoov_tts" class="button button-primary">Connect your account</a>
						<a href="https://aivoov.com/signup" class="button button-secondary" target="_blank">Try Us Free</a>
						<p><a href="https://www.aivoov.com/wordpress" target="_blank">' . __('Learn more', 'aivoov_tts') . '</a> ' . __('about text to speech tool for audience growth and engagement.', 'aivoov_tts') . '</p>
					</div>
				</div>';

    	}

    }
}

add_action('admin_notices', 'aivoov_tts_notices');

 ?>
