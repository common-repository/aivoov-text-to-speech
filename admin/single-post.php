<?php 
// 
// ADD METABOX TO ADD POST ADMIN
// 
if ( get_option('aivoov_tts_key') ) {

	function aivoov_tts_add_metabox()
	{
	     $screens = get_post_types();
	    foreach ($screens as $screen) {
	        add_meta_box(
	            'aivoov_tts_metabox_id',
	             __( 'Text to speech', 'aivoov_tts' ),
	            'aivoov_tts_metabox_html',
	            $screen,
	            'side',
	            'high'
	        );
	    }
	}
	add_action('add_meta_boxes', 'aivoov_tts_add_metabox');

	function aivoov_tts_metabox_html($post)
	{	 
		$post_id = $post->ID;
		$checked = boolval(get_post_meta($post_id, 'aivoov_tts_enabled', true));
		$selected =  get_post_meta($post_id, 'aivoov_tts_selected_voice', true);
		$voice = aivoov_api_get_voice(); 
		if( $post->post_status == 'publish' && $post->post_type === 'post') {
			$text = 'Re-Create Audio';
		}else{
			$text = 'Create Audio';
		}
	    ?>
	    <div>
			<strong>Select Your Voice</strong> <br>
			<small>Add voice in to Favorites on AiVOOV to view it here.</small>
			<br>
			<br>
			<?php if($voice){ ?>
			<select name="voice" id="voice">
			<?php foreach($voice as $v) { ?>
			
				<option value="<?php echo esc_attr($v->voice_id); ?>" data-engine="<?php echo esc_attr($v->engine); ?>" data-hash_key="<?php echo esc_attr($v->hash_key); ?>" <?php if(get_option('aivoov_tts_default_voice_id') == $v->voice_id) echo "selected";?>><?php echo esc_html($v->name); ?>-<?php echo esc_html($v->gender); ?>-<?php echo esc_html($v->language_name); ?></option>
			<?php } ?>
			</select>
			<br>
			<a href="javascript:;" onclick="aivoov_handle_default_vocie()">Set as default voice</a>
			<?php } else { ?>
				No favorite audio found
			<?php } ?>
			
		</div>
	    <div class="aivoov_tts-metabox <?php echo esc_attr($checked) ? 'checked' : '';  ?>">
		    <label class="aivoov_tts-switch">
				<input type="checkbox" <?php echo esc_attr($checked) ? 'checked' : '';  ?> onclick="aivoov_handle_enable_toggle_single_post( event, this, <?php echo esc_attr($post_id); ?> )" class="listen_button" id="listen_button-97" name="listen_button">
				<span class="aivoov_tts-slider round"></span>
			</label>
			
			<p class="aivoov_tts-metabox__enabled-text"><?php _e('This post has audio enabled', 'aivoov_tts'); ?></p>
			<p class="aivoov_tts-metabox__disabled-text"><?php _e('This post does not have audio', 'aivoov_tts'); ?></p>
			<input type="button" name="create_audio" onclick="aivoov_handle_enable_toggle_single_post_recreate( event, this, <?php echo esc_attr($post_id); ?> )" class="button button-primary button-large" value="<?php echo esc_html($text) ?>" style="margin-top:10px">
			<small style="color:red">*Update your post before Re-Create audio.</small>
		</div>
		<div id="aivoov_status" style="color:red"></div>
		<div id="aivoov_status_success" style="color:green"></div>
	    <?php
	}

}

?>