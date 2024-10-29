<?php 

$aivoov_tts_BASE_URL = "https://aivoov.com/api/v2";

// 
// VERIFY TOKEN
// 
function aivoov_api_verify_token($token) {

	global $aivoov_tts_BASE_URL;

	$args = array(
		'timeout'     => 45,
		'sslverify'   => false,
		'headers'     => array(
			'X-API-KEY' => $token,
			'Content-Type'  => 'multipart/form-data',
		),
	); 
    $url = '?domain=' . parse_url( site_url(), PHP_URL_HOST ) . '&'; 
    $url .= 'admin_e=' . base64_encode( get_option( 'admin_email' ) );
	$request = wp_remote_get( $aivoov_tts_BASE_URL . '/activation'.$url, $args );
	$response = wp_remote_retrieve_body( $request );
	//print_r($response); exit;
	$response_decoded = json_decode($response);
 
	$res = false;

	if ($response_decoded) {
		if ($response_decoded->status == true) {
			$res = true;
		}
	}

	return $response_decoded;
}

function aivoov_api_worepress_log() {

	global $aivoov_tts_BASE_URL;

	$args = array(
		'timeout'     => 45,
		'sslverify'   => false,
		'headers' => array(
            'X-API-KEY' => get_option('aivoov_tts_key'),  
        ),
	); 
	$request = wp_remote_get( $aivoov_tts_BASE_URL . '/wordpresslog?debug',$args);
	$response = wp_remote_retrieve_body( $request );
	
	$response_decoded = json_decode($response); 
	$res = false;

	if ($response_decoded) {
		if ($response_decoded->status == true) {
			$res = true;
		}
	}

	return $response_decoded;
}
function aivoov_api_voice_resource_search($limit = 10) {

	global $aivoov_tts_BASE_URL;

	$args = array(
		'timeout'     => 45,
		'sslverify'   => false,
		'headers' => array(
            'X-API-KEY' => get_option('aivoov_tts_key'),  
        ),
	); 
	$filter_gender = isset($_GET['filter_gender'])?$_GET['filter_gender']:'';
	$filter_language = isset($_GET['transcribe_language'])?$_GET['transcribe_language']:'';
	$filter_voice = isset($_GET['filter_voice'])?$_GET['filter_voice']:'';
	$request = wp_remote_get( $aivoov_tts_BASE_URL . "/voiceResourceSearch?filter_gender=$filter_gender&filter_language=$filter_language&filter_voice=$filter_voice",$args);
	$response = wp_remote_retrieve_body( $request );
	
	$response_decoded = $request;  
	$res = false;
	$response_decoded = json_decode($response); 
	return $response_decoded;
}

// 
// CONVERT / UPDATE POST
// 
function get_frontend_url( $post_id, $template = null ) {

        /** Get full permalink for the current post. */
        $url = get_permalink( $post_id );

        /** Returns a string if the URL has parameters or NULL if not. */
        $query = parse_url( $url, PHP_URL_QUERY );
 
        /** Add template param to url. */
        if ( $template ) {

            $url .=  '?aivoov-template=' . $template;

        }
        return $url;

    }

function parse_post_content( $post_id, $template = null )
{ 
        /** Frontend url with post content to parse. */
		$url = get_frontend_url( $post_id, $template ); 

        /** Get page content */
        $response = wp_remote_get(
            $url,
            array(
                'sslverify' => false,
                'timeout'   => 30,
            )
        );

        /** Throw error message */
        if ( is_wp_error( $response ) ) {

            $return = [
                'success' => false,
                'message' => esc_html__( 'Error connecting to', 'aivoov' ) . ' ' . $url . ' ' . $response->get_error_message() . ' (' . $response->get_error_code() . ')',
            ];
            wp_send_json( $return );

        }

        /** Get post content ot throw an error */
        $html = wp_remote_retrieve_body( $response );
        if ( $html === '' ) {

            $response_code = wp_remote_retrieve_response_code( $response );
            $return = [
                'success' => false,
                'message' => esc_html__( 'Failed to get content due to an error:', 'aivoov' ) . 'HTTP: ' . $response_code . ' URL: ' . $url
            ];
            wp_send_json( $return );

        }

  

        return apply_filters( 'aivoov_parse_post_content', $html );

}
 function clean_content( $post_content ) {
        /** Remove <script>...</script>. */
        $post_content = preg_replace( '/<\s*script.+?<\s*\/\s*script.*?>/si', ' ', $post_content );

        /** Remove <style>...</style>. */
        $post_content = preg_replace( '/<\s*style.+?<\s*\/\s*style.*?>/si', ' ', $post_content );

        /** Trim, replace tabs and extra spaces with single space. */
        $post_content = preg_replace( '/[ ]{2,}|[\t]/', ' ', trim( $post_content ) );

        return $post_content;
}	
function get_string_between( $string, $start, $end ) {

	$string = ' ' . $string;
	$ini = strpos( $string, $start );
	if ( $ini === 0 ) { return ''; }

	$ini += strlen( $start );
	$len = strpos( $string, $end, $ini ) - $ini;

	return substr( $string, $ini, $len );

}
	 function repair_html( $html ) {

		/** Hide DOM parsing errors. */
		libxml_use_internal_errors( true );
		libxml_clear_errors();

		/** Load the possibly malformed HTML into a DOMDocument. */
		$dom          = new DOMDocument();
		$dom->recover = true;
		//$dom->loadHTML( '<?xml encoding="UTF-8"><body id="repair">' . $html . '</body>' ); // input UTF-8.
		$dom->loadHTML( '<?xml encoding="UTF-8"><!DOCTYPE html><html lang=""><head><title>R</title></head><body id="repair">' . $html . '</body></html>' );

		/** Copy the document content into a new document. */
		$doc = new DOMDocument();
		foreach ( $dom->getElementById( 'repair' )->childNodes as $child ) {
			$doc->appendChild( $doc->importNode( $child, true ) );
		}

		/** Output the new document as HTML. */
		$doc->encoding     = 'UTF-8'; // output UTF-8.
		$doc->formatOutput = false;

		return trim( $doc->saveHTML() );
	}
function great_divider( $html, $max = 3500 ) {

	/** Get voice wrapper for whole content */
	$voice_tag = (object)array();
	$is_voice_wrapper = false;
	 

	$parts = [];

	/** Divide HTML by closing tags '</' */
	$html_array = preg_split( '/(<\/)/', $html );
	$html_array = array_filter( $html_array );

	/** Fix broken tags, add '</' to all except first element. */
	$count = 0;
	foreach ( $html_array as $i => $el ) {
		$count ++;
		if ( $count === 1 ) {
			continue;
		} // Skip first element.

		$html_array[ $i ] = '</' . $el;
	}

	/** Fix broken html. */
	foreach ( $html_array as $i => $el ) {
		$html_array[ $i ] = repair_html( $el );
	}

	/** Remove empty elements. */
	$html_array = array_filter( $html_array );

	/** Divide into parts. */
	$current   = "";
	foreach ( $html_array as $i => $el ) {
		$previous = $current;
		$current   .= $el;
		if ( strlen( $current ) >= $max ) {
			$parts[]  = $previous;
			$current   = $el;
		}
	}
	$parts[] = html_entity_decode($current);

	/** Add voice wrapper for whole content, which was added for whole content */
	if ( $is_voice_wrapper ) {

		array_walk( $parts, [ $this, 'voice_tag_wrap' ], $voice_tag );

	}

	return $parts;

}
function aivoov_convert_post($id, $voice_id='', $engine='', $hash_key='') {
	if($voice_id == ''){
		$voice_id = get_option('aivoov_tts_default_voice_id');
	}
	if($hash_key == ''){
		$hash_key = get_option('aivoov_tts_default_voice_hash_key');
	}
	global $aivoov_tts_BASE_URL;
	$post_content = parse_post_content( $id, 'aivoov' );
	$content = get_string_between( html_entity_decode($post_content), '<div class="aivoov-content-start"></div>', '<div class="aivoov-content-end"></div>' );

	$content =clean_content( $content ); 
	$content = strip_tags( $content, '<break><say-as><sub><emphasis><prosody><voice>'); 
	$parts = preg_split("/.{0,700}\K(?:\s+|$)/", $content, 0, PREG_SPLIT_NO_EMPTY);
	/*if ( strlen( $content ) > 3500 ) {
		$parts = great_divider( $content, 3500 );
	}*/
	//$content = get_post_field('post_content', $id);
	$title = get_the_title($id);
	$url = get_permalink($id);
	$author_id = get_post_field('post_author', $id);
	$author = get_the_author_meta('display_name' , $author_id);
	$thumbnail_url = get_the_post_thumbnail_url($id);
 
	if(get_post_status($id) != "publish"){
		$data['result'] = false;
		$data['message'] = "Publish a post before you can generate an audio.";
		return json_decode(json_encode($data));
	}
	$body = array(
		'id' 			=> $id,
		'audioEnabled'	=> true,
		'transcribe_text' => $parts,
		'voice_id' 		=> $voice_id, 
		'engine' 		=> $engine, 
		'hash_key' 		=> $hash_key, 
		'title' 		=> $title,
		'author'		=> $author,
		'url' 			=> $url,
		'image'			=> $thumbnail_url ? $thumbnail_url : null,
	);

	 $args = array(
        'method'      => 'POST',
        'timeout'     => 60,
        'sslverify'   => false,
		'headers' => array(
            'X-API-KEY' => get_option('aivoov_tts_key'), 
        ),
        'body'        => $body,
    );
	$request = wp_remote_post( $aivoov_tts_BASE_URL .'/wordpress', $args );
	 if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
        error_log( print_r( $request, true ) );
    }
    $response = wp_remote_retrieve_body( $request );
	return json_decode($response);
}

// 
//  FAVOURITE
// 
function aivoov_api_add_to_favourite($post) {
	 
	global $aivoov_tts_BASE_URL;
   
	 $args = array(
        'method'      => 'POST',
        'timeout'     => 60,
        'sslverify'   => false,
		'headers' => array(
            'X-API-KEY' => get_option('aivoov_tts_key'), 
        ),
        'body'        => $post,
    );
	$request = wp_remote_post( $aivoov_tts_BASE_URL .'/favorite', $args );
	 if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
        error_log( print_r( $request, true ) );
    }
    $response = wp_remote_retrieve_body( $request );
	$response_decoded = json_decode($response);
	update_option( 'aivoov_tts_favorite_voices', wp_json_encode($response_decoded->data));
	if(get_option('aivoov_tts_default_voice_id') == ''){
		update_option( 'aivoov_tts_default_voice_id', sanitize_text_field($response_decoded->data[0]->voice_id));
	}
	return $response_decoded; 
}

// 
// DISABLE POST
// 
function aivoov_disable_post($id) {

	global $aivoov_tts_BASE_URL;

	$body = array(
		'id' 			=> $id,
		'audioEnabled'	=> false,
	);

	$args = array(
		'method'      => 'POST',
		'timeout'     => 45,
		'sslverify'   => false,
		'headers'     => array(
			'Authorization' => 'Bearer '. get_option('aivoov_tts_key'),
			'Content-Type'  => 'application/json',
		),
		'body'        => wp_json_encode($body)
	);
	$request = wp_remote_post( $aivoov_tts_BASE_URL . '/post' , $args );
	$response = wp_remote_retrieve_body( $request );
	$response_decoded = json_decode( $response, true );

	return $response_decoded['post'];
}

// 
// GET POST
// 
function aivoov_api_get_posts_info($ids) {
	return true;
	global $aivoov_tts_BASE_URL;

	$args = array(
		'timeout'     => 45,
		'sslverify'   => false,
		'headers'     => array(
			'Authorization' => 'Bearer '. get_option('aivoov_tts_key'),
			'Content-Type'  => 'application/json',
		),
	);

	$query = '?';

	foreach ( $ids as $i => $id ) {
		$query .= 'id%5B%5D=' . $id;
		if ( $i < count($ids) - 1 ) $query .= '&';
	}

	$request = wp_remote_get( $aivoov_tts_BASE_URL . '/post' . $query , $args );
	$response = wp_remote_retrieve_body( $request );

	$response_decoded = json_decode( $response, true );

	return $response_decoded['posts'];
}
// 
// GET VOICE LIST FROM  OPTIONS
// 
function aivoov_api_get_voice() {
	return json_decode(get_option('aivoov_tts_favorite_voices'));
}

// 
// SYNC VOICE LIST FROM AiVOOV
// 
function aivoov_api_sync_voice() {

	global $aivoov_tts_BASE_URL;

	$args = array(
		'timeout'     => 45,
		'sslverify'   => false,
		'headers'     => array(
			'X-API-KEY' =>  get_option('aivoov_tts_key'),
			'Content-Type'  => 'multipart/form-data',
		),
	);

	$request = wp_remote_get( $aivoov_tts_BASE_URL . '/vocies_list', $args );
	$response = wp_remote_retrieve_body( $request );
	
	$response_decoded = json_decode($response);
	update_option( 'aivoov_tts_favorite_voices', wp_json_encode($response_decoded->data));
	if(get_option('aivoov_tts_default_voice_id') == ''){
		update_option( 'aivoov_tts_default_voice_id', sanitize_text_field($response_decoded->data[0]->voice_id));
	}
	return $response_decoded;
}

?>