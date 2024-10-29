<?php 
// 
// CONVERT POST TO VOICE
// 
add_action( 'wp_ajax_aivoov_tts_convert_post_ajax', 'aivoov_tts_convert_post_ajax' );

function aivoov_tts_convert_post_ajax() {

	$id = intval(sanitize_text_field($_POST['id']));
	$enable = sanitize_text_field($_POST['enable']) == 'true' ? true : false;

	if ($enable) {
		$res = aivoov_convert_post($id,sanitize_text_field($_POST['voice_id']),sanitize_text_field($_POST['engine']),sanitize_text_field($_POST['hash_key'])); 
		if ($res) {
			$audioFile = esc_url_raw($res->transcribe_uri);
			$playCount = $res->playCount;
			$playMinutes = $res->playMinutes;

			if($audioFile){
				update_post_meta($id, 'aivoov_tts_audioFile', $audioFile);
				update_post_meta($id, 'aivoov_tts_enabled', $enable);
				update_post_meta($id, 'aivoov_tts_count', sanitize_text_field($playCount));
				update_post_meta($id, 'aivoov_tts_time', sanitize_text_field($playMinutes));
			}

			echo wp_json_encode(array(
				'playCount' => $playCount,
				'playMinutes' => $playMinutes,
				'status' => $res->status, 
				'message' => isset($res->error)?$res->error:$res->message, 
			));

			wp_die();
		}

	} else {
		update_post_meta($id, 'aivoov_tts_enabled', false);

		echo true;
		/*$res = aivoov_disable_post($id);

		if ($res) {

			

			wp_die();	
		}*/

	}

	echo false;

	wp_die();
	
}
// 
// ENABLE POST
// 
add_action( 'wp_ajax_aivoov_tts_convert_enable_post_ajax', 'aivoov_tts_convert_enable_post_ajax' );
function aivoov_tts_convert_enable_post_ajax() {

	$id = intval(sanitize_text_field($_POST['id']));
	$enable = sanitize_text_field($_POST['enable']) == 'true' ? true : false;
	update_post_meta($id, 'aivoov_tts_enabled', $enable);
	if ($enable) {
		echo true;
		wp_die();
	}else{
		echo false;
	}
	wp_die();
	
}
// 
// ADD VOICE TO DEFAULT
// 
add_action( 'wp_ajax_aivoov_handle_default_vocie_ajax', 'aivoov_handle_default_vocie_ajax' );
function aivoov_handle_default_vocie_ajax() { 
	update_option( 'aivoov_tts_default_voice_id', sanitize_text_field($_POST['voice_id']));
	update_option( 'aivoov_tts_default_voice_hash_key', sanitize_text_field($_POST['hash_key']));
	wp_die();
}
//
// ADD VOICE TO FAVOURITE
//
add_action( 'wp_ajax_aivoov_tts_add_to_favourite', 'aivoov_tts_add_to_favourite' );
function aivoov_tts_add_to_favourite() { 
	$data = aivoov_api_add_to_favourite($_POST);
	echo  wp_json_encode($data);
	wp_die();
}
add_action( 'wp_ajax_aivoov_handle_sync_voice_ajax', 'aivoov_handle_sync_voice_ajax' );
function aivoov_handle_sync_voice_ajax() { 
	$data = aivoov_api_sync_voice();
	echo  wp_json_encode($data); wp_die();
}

?>