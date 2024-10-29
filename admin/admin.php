<?php
require_once plugin_dir_path( __FILE__ ) . 'api.php';
require_once plugin_dir_path( __FILE__ ) . 'ajax.php';
require_once plugin_dir_path( __FILE__ ) . 'notices.php';
require_once plugin_dir_path( __FILE__ ) . 'posts-list.php';
require_once plugin_dir_path( __FILE__ ) . 'single-post.php';

//
// SETUP ADMIN MENU
//
add_action( 'admin_menu', 'aivoov_tts_add_menu_page' );

function aivoov_tts_add_menu_page() {
	add_menu_page(
		'AiVOOV',
		'AiVOOV',
		'manage_options',
		'aivoov_tts',
		'aivoov_tts_options_page_html',
		plugin_dir_url(__FILE__) . 'images/logo.png',
		100
	);
}

function aivoov_tts_options_page_html() {
	$default_tab = null;
  $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;

?> 
	  <div class="wrap">
		<h1 class="aivoov_tts__header"><?php _e( 'AiVOOV Text to Speech Settings', 'aivoov_tts' ); ?></h1>
	 
		<!-- Here are our tabs -->
		<nav class="nav-tab-wrapper">
		  <a href="?page=aivoov_tts" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>">Settings</a>
		  <a href="?page=aivoov_tts&tab=voices&transcribe_language=en-US" class="nav-tab <?php if($tab==='voices'):?>nav-tab-active<?php endif; ?>">Voices</a>
		  <a href="?page=aivoov_tts&tab=statistics" class="nav-tab <?php if($tab==='statistics'):?>nav-tab-active<?php endif; ?>">Characters Statistics</a>
		  <a href="?page=aivoov_tts&tab=tools" class="nav-tab <?php if($tab==='tools'):?>nav-tab-active<?php endif; ?>">Bulk Audio Logs</a>
		</nav>

		<div class="tab-content">
		<?php switch($tab) :
		  case 'statistics':
			?>
			
			<div class="aivoov_tts__options">
				<p class="options-title"><?php _e( 'Characters used in post', 'aivoov_tts' ); ?></p>
				<?php if ( get_option('aivoov_tts_key') ) { ?>
				<small>Last 50 audio log</small>
				<?php $logs = aivoov_api_worepress_log(); $logs = $logs->data;  ?>
				<table class="widefat fixed">
				 <thead>
				  <tr>
					<th class="manage-column column-columnname" >Post</th>
					<th class="manage-column column-columnname" >Characters Used</th>
					<th class="manage-column column-columnname" >Create Date</th>
				  </tr>
				   </thead>
				  <?php if($logs){foreach($logs as $log) { ?>
				  <tr>
					<td><?php echo get_the_title($log->post_id); ?></td>
					<td><?php echo $log->total_count; ?></td>
					<td><?php echo $log->created_time ; ?></td>
				  </tr>
				  <?php } } ?>
				</table>
				<?php } else { ?>
				<?php _e( 'Plugin not activated', 'aivoov_tts' ); ?>
				<?php } ?>
			</div>
			<?php
			break;
		  case 'voices':
			?>
			
			<div class="aivoov_tts__options">
			<audio id="plyPro" src=""></audio>
				<p class="options-title"><?php _e( 'Voices', 'aivoov_tts' ); ?></p>
				<?php if ( get_option('aivoov_tts_key') ) { ?>
				<small><?php _e( 'Explore all the premium and standard voices. Use the below filters to find the perfect voice.', 'aivoov_tts' ); ?></small>
				<?php $voice_list = aivoov_api_voice_resource_search(); $voices = $voice_list->rows;  ?>
				<div class="filters">
				<form method="GET">
				<input type="hidden" name="page" value="aivoov_tts">
				<input type="hidden" name="tab" value="voices">
				<select name="filter_voice" id="filter_voice" class="form-select">
					<option value="0">Voice Type Filter</option>
					<option value="standard" <?php if(isset($_GET['filter_voice']) && $_GET['filter_voice'] == 'standard') echo "selected"; ?>>Standard</option>
					<option value="neural" <?php if(isset($_GET['filter_voice']) && $_GET['filter_voice'] == 'neural') echo "selected"; ?>>Premium</option>
					<option value="neural2" <?php if(isset($_GET['filter_voice']) && $_GET['filter_voice'] == 'neural2') echo "selected"; ?>>Ultra</option> 
				</select>
				<select name="filter_gender" id="filter_gender" class="form-select">
					<option value="0">Gender Filter</option>
					<option value="male" <?php if(isset($_GET['filter_gender']) && $_GET['filter_gender'] == 'male') echo "selected"; ?>>Male</option>
					<option value="female" <?php if(isset($_GET['filter_gender']) && $_GET['filter_gender'] == 'female') echo "selected"; ?> >Female</option>
				</select>
				<select name="transcribe_language" id="filter_language">
					<option value="0" data-select2-id="6">Language</option>
					<option value="af-ZA" data-select2-id="26">Afrikaans (South Africa)</option>
					<option value="sq-AL" data-select2-id="27">Albanian (Albania)</option>
					<option value="ar-DZ" data-select2-id="28">Algeria</option>
					<option value="am-ET" data-select2-id="29">Amharic</option>
					<option value="ar-XA" data-select2-id="30">Arabic (Asia)</option>
					<option value="ar-EG" data-select2-id="31">Arabic (Egypt)</option>
					<option value="ar-AE" data-select2-id="32">Arabic (Gulf)</option>
					<option value="ar-IQ" data-select2-id="33">Arabic (Iraq)</option>
					<option value="ar-JO" data-select2-id="34">Arabic (Jordan)</option>
					<option value="ar-KW" data-select2-id="35">Arabic (Kuwait)</option>
					<option value="ar-LB" data-select2-id="36">Arabic (Lebanon)</option>
					<option value="ar-LY" data-select2-id="37">Arabic (Libya)</option>
					<option value="ar-MA" data-select2-id="38">Arabic (Morocco)</option>
					<option value="ar-OM" data-select2-id="39">Arabic (Oman)</option>
					<option value="ar-QA" data-select2-id="40">Arabic (Qatar)</option>
					<option value="ar-SA" data-select2-id="41">Arabic (Saudi Arabia)</option>
					<option value="ar-SY" data-select2-id="42">Arabic (Syria)</option>
					<option value="ar-TN" data-select2-id="43">Arabic (Tunisia)</option>
					<option value="ar-YE" data-select2-id="44">Arabic (Yemen)</option>
					<option value="hy-AM" data-select2-id="45">Armenian (Armenia)</option>
					<option value="az-AZ" data-select2-id="46">Azerbaijani (Azerbaijan)</option>
					<option value="ar-BH" data-select2-id="47">Bahrain</option>
					<option value="eu-ES" data-select2-id="48">Basque</option>
					<option value="bn-BD" data-select2-id="49">Bengali</option>
					<option value="bn-IN" data-select2-id="50">Bengali (India)</option>
					<option value="bs-BA" data-select2-id="51">Bosnian (Bosnia and Herzegovina)</option>
					<option value="bg-BG" data-select2-id="52">Bulgarian (Bulgaria)</option>
					<option value="my-MM" data-select2-id="53">Burmese (Myanmar)</option>
					<option value="ca-ES" data-select2-id="54">Catalan (Spain)</option>
					<option value="yue-CN" data-select2-id="55">Chinese (Cantonese, Simplified)</option>
					<option value="yue-HK" data-select2-id="56">Chinese (Hong Kong)</option>
					<option value="zh-CN-shandong" data-select2-id="57">Chinese (Jilu Mandarin, Shandong)</option>
					<option value="cmn-CN" data-select2-id="58">Chinese (Mainland China)</option>
					<option value="zh-CN-liaoning" data-select2-id="59">Chinese (Mandarin, Simplified)</option>
					<option value="zh-CN-sichuan" data-select2-id="60">Chinese (Mandarin, Simplified)</option>
					<option value="cmn-TW" data-select2-id="61">Chinese (Taiwan)</option>
					<option value="wuu-CN" data-select2-id="62">Chinese (Wu, Simplified)</option>
					<option value="zh-CN-henan" data-select2-id="63">Chinese (Zhongyuan Mandarin Henan)</option>
					<option value="zh-CN-shaanxi" data-select2-id="64">Chinese (Zhongyuan Mandarin Shaanxi)</option>
					<option value="hr-HR" data-select2-id="65">Croatian (Croatia)</option>
					<option value="cs-CZ" data-select2-id="66">Czech (Czech Republic)</option>
					<option value="da-DK" data-select2-id="67">Danish (Denmark)</option>
					<option value="nl-BE" data-select2-id="68">Dutch (Belgium)</option>
					<option value="nl-NL" data-select2-id="69">Dutch (Netherlands)</option>
					<option value="en-AU" data-select2-id="70">English (Australia)</option>
					<option value="en-CA" data-select2-id="71">English (Canada)</option>
					<option value="en-HK" data-select2-id="72">English (Hong Kong)</option>
					<option value="en-IN" data-select2-id="73">English (India)</option>
					<option value="en-IE" data-select2-id="74">English (Ireland)</option>
					<option value="en-KE" data-select2-id="75">English (Kenya)</option>
					<option value="en-NZ" data-select2-id="76">English (New Zealand)</option>
					<option value="en-NG" data-select2-id="77">English (Nigeria)</option>
					<option value="en-PH" data-select2-id="78">English (Philippines)</option>
					<option value="en-SG" data-select2-id="79">English (Singapore)</option>
					<option value="en-ZA" data-select2-id="80">English (South Africa)</option>
					<option value="en-TZ" data-select2-id="81">English (Tanzania)</option>
					<option value="en-GB" data-select2-id="82">English (UK)</option>
					<option value="en-US" data-select2-id="83">English (US)</option>
					<option value="en-GB-WLS" data-select2-id="84">English (Welsh)</option>
					<option value="et-EE" data-select2-id="85">Estonian (Estonia)</option>
					<option value="fil-PH" data-select2-id="86">Filipino (Philippines)</option>
					<option value="fi-FI" data-select2-id="87">Finnish (Finland)</option>
					<option value="fr-BE" data-select2-id="88">French (Belgium)</option>
					<option value="fr-CA" data-select2-id="89">French (Canada)</option>
					<option value="fr-FR" data-select2-id="90">French (France)</option>
					<option value="fr-CH" data-select2-id="91">French (Switzerland)</option>
					<option value="gl-ES" data-select2-id="92">Galician (Spain)</option>
					<option value="ka-GE" data-select2-id="93">Georgian (Georgia)</option>
					<option value="de-AT" data-select2-id="94">German (Austria)</option>
					<option value="de-DE" data-select2-id="95">German (Germany)</option>
					<option value="de-CH" data-select2-id="96">German (Switzerland)</option>
					<option value="el-GR" data-select2-id="97">Greek (Greece)</option>
					<option value="gu-IN" data-select2-id="98">Gujarati (India)</option>
					<option value="he-IL" data-select2-id="99">Hebrew (Israel)</option>
					<option value="hi-IN" data-select2-id="100">Hindi (India)</option>
					<option value="hu-HU" data-select2-id="101">Hungarian (Hungary)</option>
					<option value="is-IS" data-select2-id="102">Icelandic</option>
					<option value="id-ID" data-select2-id="103">Indonesian (Indonesia)</option>
					<option value="ga-IE" data-select2-id="104">Irish Gaelic</option>
					<option value="it-IT" data-select2-id="105">Italian (Italy)</option>
					<option value="ja-JP" data-select2-id="106">Japanese (Japan)</option>
					<option value="jv-ID" data-select2-id="107">Javanese (Indonesia)</option>
					<option value="kn-IN" data-select2-id="108">Kannada (India)</option>
					<option value="kk-KZ" data-select2-id="109">Kazakhstan</option>
					<option value="km-KH" data-select2-id="110">Khmer (Cambodia)</option>
					<option value="ko-KR" data-select2-id="111">Korean (South Korea)</option>
					<option value="lo-LA" data-select2-id="112">Lao (Laos)</option>
					<option value="lv-LV" data-select2-id="113">Latvian (Latvia)</option>
					<option value="lt-LT" data-select2-id="114">Lithuanian (Lithuania)</option>
					<option value="mk-MK" data-select2-id="115">Macedonian (Republic of North Macedonia)</option>
					<option value="ms-MY" data-select2-id="116">Malay (Malaysia)</option>
					<option value="ml-IN" data-select2-id="117">Malayalam (India)</option>
					<option value="mt-MT" data-select2-id="118">Maltese (Malta)</option>
					<option value="mr-IN" data-select2-id="119">Marathi (India)</option>
					<option value="mn-MN" data-select2-id="120">Mongolian (Mongolia)</option>
					<option value="ne-NP" data-select2-id="121">Nepali (Nepal)</option>
					<option value="nb-NO" data-select2-id="122">Norwegian (Norway)</option>
					<option value="ps-AF" data-select2-id="123">Pashto (Afghanistan)</option>
					<option value="fa-IR" data-select2-id="124">Persian (Iran)</option>
					<option value="pl-PL" data-select2-id="125">Polish (Poland)</option>
					<option value="pt-BR" data-select2-id="126">Portuguese (Brazil)</option>
					<option value="pt-PT" data-select2-id="127">Portuguese (Portugal)</option>
					<option value="pa-IN" data-select2-id="128">Punjabi (India)</option>
					<option value="ro-RO" data-select2-id="129">Romanian</option>
					<option value="ru-RU" data-select2-id="130">Russian (Russia)</option>
					<option value="sr-RS" data-select2-id="131">Serbian (Serbia)</option>
					<option value="si-LK" data-select2-id="132">Sinhala (Sri Lanka)</option>
					<option value="sk-SK" data-select2-id="133">Slovak (Slovakia)</option>
					<option value="sl-SI" data-select2-id="134">Slovenian (Slovenia)</option>
					<option value="so-SO" data-select2-id="135">Somali (Somalia)</option>
					<option value="es-AR" data-select2-id="136">Spanish (Argentina)</option>
					<option value="es-BO" data-select2-id="137">Spanish (Bolivia)</option>
					<option value="es-CL" data-select2-id="138">Spanish (Chile)</option>
					<option value="es-CO" data-select2-id="139">Spanish (Colombia)</option>
					<option value="es-CR" data-select2-id="140">Spanish (Costa Rica)</option>
					<option value="es-CU" data-select2-id="141">Spanish (Cuba)</option>
					<option value="es-DO" data-select2-id="142">Spanish (Dominican Republic)</option>
					<option value="es-EC" data-select2-id="143">Spanish (Ecuador)</option>
					<option value="es-SV" data-select2-id="144">Spanish (El Salvador)</option>
					<option value="es-GQ" data-select2-id="145">Spanish (Equatorial Guinea)</option>
					<option value="es-GT" data-select2-id="146">Spanish (Guatemala)</option>
					<option value="es-HN" data-select2-id="147">Spanish (Honduras)</option>
					<option value="es-MX" data-select2-id="148">Spanish (Mexico)</option>
					<option value="es-NI" data-select2-id="149">Spanish (Nicaragua)</option>
					<option value="es-PA" data-select2-id="150">Spanish (Panama)</option>
					<option value="es-PY" data-select2-id="151">Spanish (Paraguay)</option>
					<option value="es-PE" data-select2-id="152">Spanish (Peru)</option>
					<option value="es-PR" data-select2-id="153">Spanish (Puerto Rico)</option>
					<option value="es-ES" data-select2-id="154">Spanish (Spain)</option>
					<option value="es-UY" data-select2-id="155">Spanish (Uruguay)</option>
					<option value="es-US" data-select2-id="156">Spanish (US)</option>
					<option value="es-VE" data-select2-id="157">Spanish (Venezuela)</option>
					<option value="su-ID" data-select2-id="158">Sundanese (Indonesia)</option>
					<option value="sw-KE" data-select2-id="159">Swahili (Kenya)</option>
					<option value="sw-TZ" data-select2-id="160">Swahili (Tanzania)</option>
					<option value="sv-SE" data-select2-id="161">Swedish (Sweden)</option>
					<option value="ta-IN" data-select2-id="162">Tamil (India)</option>
					<option value="ta-MY" data-select2-id="163">Tamil (Malaysia)</option>
					<option value="ta-SG" data-select2-id="164">Tamil (Singapore)</option>
					<option value="ta-LK" data-select2-id="165">Tamil (Sri Lanka)</option>
					<option value="te-IN" data-select2-id="166">Telugu (India)</option>
					<option value="th-TH" data-select2-id="167">Thai (Thailand)</option>
					<option value="tr-TR" data-select2-id="168">Turkish (Turkey)</option>
					<option value="uk-UA" data-select2-id="169">Ukrainian (Ukraine)</option>
					<option value="ur-IN" data-select2-id="170">Urdu (India)</option>
					<option value="ur-PK" data-select2-id="171">Urdu (Pakistan)</option>
					<option value="uz-UZ" data-select2-id="172">Uzbek (Uzbekistan)</option>
					<option value="vi-VN" data-select2-id="173">Vietnamese (Vietnam)</option>
					<option value="cy-GB" data-select2-id="174">Welsh</option>
					<option value="zu-ZA" data-select2-id="175">Zulu (South Africa)</option>
				</select>
				<input type="submit" value="Search" class="button">
				</form>
				</div>
				<table class="widefat fixed" id="table">
				 <thead>
				  <tr>
					<th class="manage-column column-columnname">Voice Name</th>
					<th class="manage-column column-columnname">Language Name</th>
					<th class="manage-column column-columnname">Voice Quality</th>
					<th class="manage-column column-columnname">Gender</th>
					<th class="manage-column column-columnname">Action</th>
				  </tr>
				   </thead>
				  <?php if($voices){foreach($voices as $voice) { ?>
				  <tr>
					<td><?php echo $voice->name; ?></td>
					<td><?php echo $voice->language_name; ?></td>
					<td><?php echo $voice->engine ; ?></td>
					<td><?php echo $voice->gender ; ?></td>
					<td><?php echo $voice->edit ; ?></td> 
				  </tr>
				  <?php } } ?>
				</table>
				<?php } else { ?>
				<?php _e( 'Plugin not activated', 'aivoov_tts' ); ?>
				<?php } ?>
			</div>
			<?php
			break;
			case 'tools':  
			$date = isset($_POST['date']) ? $_POST['date'] :  date("Y-m-d");
			?>
			<div class="aivoov_tts__options">
				<p class="options-title"><?php _e( 'Characters used in post', 'aivoov_tts' ); ?></p>
				<?php if ( get_option('aivoov_tts_key') ) { ?>
				<form method="post">
					Filter log by date: <input type="date" name="date" value="<?php echo $date ?>">
					<input type="submit" value="Submit" class="button">
				</form>
				<br>
				<textarea style="width:100%" rows="20" readonly><?php
				$name = wp_upload_dir()['basedir']."/aivoov_log-".$date.".txt";
				
				if(file_exists($name)){	
					$myfile = fopen($name, "r") or die("Unable to open file!");				
					echo fread($myfile,filesize($name));
					fclose($myfile); 
				}else{
					echo "No log found.";
				} ?>
				</textarea>
				
				<?php } else { ?>
				<?php _e( 'Plugin not activated', 'aivoov_tts' ); ?>
				<?php } ?>
			</div>
			<?php
			break;
		  default: 
		?>
		<p class="aivoov_tts__enable-title">
			<?php _e( 'Enable the plugin', 'aivoov_tts' ); ?>
			<?php if (get_option('aivoov_tts_key')): ?>
				<span class="green"> - <?php _e( 'Enabled', 'aivoov_tts' ); ?></span>
			<?php endif; ?>
		</p>
		<p class="aivoov_tts__enable-text">
			<?php _e( 'Connect your AiVOOV account to start converting your posts into natural sounding audio articles. ', 'aivoov_tts' ); ?>
			<a href="https://aivoov.com/signup" target="_blank"><?php _e( 'Sign up for free', 'aivoov_tts' ); ?></a>
			<?php _e( 'In case you don’t have an account yet.', 'AiVOOV' ); ?>
		</p>
		<form action="<?php echo admin_url( 'admin-post.php'); ?>" method="post" class="aivoov_tts__form" >

			<?php if (get_option('aivoov_tts_key')): ?>
				<input type="hidden" name="action" value="disable_aivoov_tts_plugin" />
			<?php else: ?>
				<input type="hidden" name="action" value="enable_aivoov_tts_plugin" />
			<?php endif; ?>
			<?php if(get_option('aivoov_tts_key')=='') { ?>
			<div class="aivoov_tts__input-holder">
			<label><?php _e('API key', 'aivoov_tts'); ?></label>
				<input type="text" name="aivoov_tts_key" value="<?php echo esc_attr(get_option('aivoov_tts_key')); ?>" placeholder="Enter your API key here" required />
				<p><?php _e( 'The API key can be found under the Settings section of your AiVOOV account.', 'aivoov_tts' ); ?></p>
			</div>
			<?php } ?>
			<?php if (get_option('aivoov_tts_key')): ?>
				<?php submit_button( __( 'Disconnect account', 'aivoov_tts' ), 'secondary' ); ?>
			<?php else: ?>
				<?php submit_button( __( 'Connect account', 'aivoov_tts' ) ); ?>
			<?php endif; ?>
			
			<p class="options-cta"><?php _e( 'Need help?', 'aivoov_tts' ); ?> <?php _e( 'Click', 'aivoov_tts' ); ?> <a href="https://aivoov.com/ticket" target="_blank"><?php _e( 'here', 'aivoov_tts' ); ?></a> <?php _e( 'for support.', 'aivoov_tts' ); ?></p>

		</form>
		<?php if (!get_option('aivoov_tts_key')): ?>
			<p class="aivoov_tts__cta-text"><?php _e( 'Don’t have an account?', 'aivoov_tts' ); ?> <a href="https://aivoov.com" target="_blank"><?php _e( 'Sign up for free', 'aivoov_tts' ); ?></a>.</p>
		<?php endif; ?>

		<?php if (get_option('aivoov_tts_key')): $voice = aivoov_api_get_voice();  ?>
			
			<div class="aivoov_tts__options">
				<p class="options-title"><?php _e( 'Voice settings', 'aivoov_tts' ); ?></p>
					<div class="options-input-holder">
					 <div>
						<strong>Select Your Voice</strong> <br>
						<small>Add voice in to Favorites on AiVOOV to view it here.</small>
						<br> 
						<br> 
						<?php if($voice){ ?>
						<select name="voice" id="voice">
						<?php foreach($voice as $v) { ?>
							<option value="<?php echo $v->voice_id; ?>" data-engine="<?php echo esc_attr($v->engine); ?>" data-hash_key="<?php echo esc_attr($v->hash_key); ?>"  <?php if(get_option('aivoov_tts_default_voice_id') == $v->voice_id) echo "selected";?>><?php echo esc_html($v->name); ?>-<?php echo esc_html($v->gender); ?>-<?php echo esc_html($v->language_name); ?></option>
						<?php } ?>
						</select>
						<br> 
						<a href="javascript:;" onclick="aivoov_handle_default_vocie()">Set as default voice</a>
						<?php } else { ?>
							No favorite audio found
						<?php } ?>
						<br>
						<br>
						<input type="button" id="sync_audio" onclick="aivoov_handle_sync_voice()" class="button button-primary button-large" value="Sync Favorite voices">
						<br> 				
						<br> 				
						<div id="aivoov_status" style="color:red"></div>
						<div id="aivoov_status_success" style="color:green"></div>
					</div>
					</div>
					<br> 
			</div>
			<div class="aivoov_tts__options">
				<p class="options-title"><?php _e( 'Other settings', 'aivoov_tts' ); ?></p>
				<form action="<?php echo admin_url( 'admin-post.php'); ?>" method="post" class="options-form" >
					<input type="hidden" name="action" value="change_aivoov_tts_settings" />
					<div class="options-input-holder">
						<input type="checkbox" id="auto" name="auto" value="auto" <?php echo esc_attr(get_option('aivoov_tts_auto')) ? 'checked' : '' ?> ><label for="auto"><?php _e( 'Add audio automatically as new posts are created', 'aivoov_tts' ); ?></label>
					</div>
					<div class="options-input-holder">
						<input type="checkbox" id="aivoov_read_title" name="aivoov_read_title" value="on" <?php echo esc_attr(get_option('aivoov_read_title')) ? 'checked' : '' ?> ><label for="aivoov_read_title"><?php _e( 'Play title in audio', 'aivoov_tts' ); ?></label>
					</div>
					<!--
					<div class="options-input-holder">
						<input type="checkbox" id="count" name="count" value="count" <?php echo esc_attr(get_option('aivoov_tts_count')) ? 'checked' : '' ?> ><label for="count"><?php _e( 'Display “Play count” statistics in Posts section', 'aivoov_tts' ); ?></label>
					</div>
					<div class="options-input-holder">
						<input type="checkbox" id="time" name="time" value="time" <?php echo esc_attr(get_option('aivoov_tts_time')) ? 'checked' : '' ?> ><label for="time"><?php _e( 'Display “Play time” statistics in Posts section', 'aivoov_tts' ); ?></label>
					</div> -->
					<br>
					<p class="options-title"><?php _e( 'Player Position (<small>Select the Player position for display in post</small>)', 'aivoov_tts' ); ?></p>
						
					<div class="options-input-holder">
						<?php
							$options = [
								"before-content" => esc_html__( 'Before Content', 'aivoov_tts' ),
								"after-content" => esc_html__( 'After Content', 'aivoov_tts' ), 
								"bottom-fixed" => esc_html__( 'Bottom Fixed', 'aivoov_tts' ),
								//"before-title" => esc_html__( 'Before Title', 'aivoov_tts' ),
								//"after-title" => esc_html__( 'After Title', 'aivoov_tts' ),
								"shortcode" => esc_html__( 'None Use Shortcode', 'aivoov_tts' ),
								"" => esc_html__( 'Disable', 'aivoov_tts' )
							];
						?>
						<select name="aivoov_tts_player_position" id="aivoov_tts_player_position">
						<?php foreach($options as $key=>$o) { ?>
							<option value="<?php echo $key; ?>" <?php if(get_option('aivoov_tts_player_position') == $key) echo "selected";?>><?php echo esc_html($o); ?></option>
						<?php } ?>
						</select>
					</div>
					<br>
					<p class="options-title"><?php _e( 'Shortcode', 'aivoov_tts' ); ?></p>
					<div class=" ">
					[aivoov_player]<br><br>
					<strong>Attributes</strong> <br>
					post_id = Enter specific post id you want to play the audio<br> 
					[aivoov_player post_id="1"]
					</div>
					<br>
					<p class="options-title"><?php _e( 'Color settings', 'aivoov_tts' ); ?></p>
					<div class="options-input-holder">
					
					<input type="color" id="aivoov_tts_player_background_color" name="aivoov_tts_player_background_color" value="#<?php echo esc_attr( get_option('aivoov_tts_player_background_color')); ?>" ><label for="aivoov_tts_player_background_color"><?php _e( 'Player Background Color', 'aivoov_tts' ); ?></label>
					</div>
					<div class="options-input-holder">
					<input type="color" id="aivoov_tts_player_button_color" name="aivoov_tts_player_button_color" value="#<?php echo esc_attr(get_option('aivoov_tts_player_button_color')); ?>" ><label for="aivoov_tts_player_button_color"><?php _e( 'Player Button Color', 'aivoov_tts' ); ?></label>
					</div>
					<div class="options-input-holder">
					<input type="color" id="aivoov_tts_player_text_color" name="aivoov_tts_player_text_color" value="#<?php echo esc_attr(get_option('aivoov_tts_player_text_color')); ?>" ><label for="aivoov_tts_player_text_color"><?php _e( 'Player text Color', 'aivoov_tts' ); ?></label>
					</div>
					<div class="options-input-holder">
					 
					</div>
					<br>
					<?php submit_button( __( 'Save Changes', 'aivoov_tts' ) ); ?>
					
				</form>
			</div>
			
		<?php endif;   
		
		break;
		endswitch; ?>
		
		</div> 
	<?php
}

//
// HANDLE API KEY FORM SUBMIT
//
add_action( 'admin_post_enable_aivoov_tts_plugin', 'aivoov_tts_enable_plugin' );

function aivoov_tts_enable_plugin() {
	$aivoov_tts_key = sanitize_text_field($_POST["aivoov_tts_key"]);
	$key = ( !empty($aivoov_tts_key) ) ? $aivoov_tts_key : NULL;
	
	$token_valid = aivoov_api_verify_token($key);

	if ($token_valid->status == true) {

		update_option( 'aivoov_tts_key', sanitize_text_field($key)); 
		update_option( 'aivoov_tts_permission', json_encode($token_valid->data));
		aivoov_api_sync_voice();
		$redirect_url = get_bloginfo("url") . "/wp-admin/admin.php?page=aivoov_tts&status=success";
		header("Location: ".$redirect_url);
		exit;

	} else {
		$redirect_url = get_bloginfo("url") . "/wp-admin/admin.php?page=aivoov_tts&status=failure&msg=".$token_valid->error.$token_valid->message;
		header("Location: ".$redirect_url);
		exit;
	}

}

add_filter( 'template_include', 'aivoov_page_template', PHP_INT_MAX );
function aivoov_page_template( $template ) {

	/** Change template for correct parsing content. */
	if ( isset( $_GET['aivoov-template'] ) && 'aivoov' === $_GET['aivoov-template'] ) {

		/** Disable admin bar. */
		show_admin_bar( false );

		$template = __DIR__ . '/aivoov-template.php';
	}

	return $template;

}
add_action( 'admin_post_disable_aivoov_tts_plugin', 'aivoov_tts_disable_plugin' );

function aivoov_tts_disable_plugin() {
	delete_option( 'aivoov_tts_key' );

	$redirect_url = get_bloginfo("url") . "/wp-admin/admin.php?page=aivoov_tts";
	header("Location: ".$redirect_url);

	exit;

}


//
// HANDLE CHANGE SETTINGS FORM SUBMIT
//
add_action( 'admin_post_change_aivoov_tts_settings', 'aivoov_tts_change_settings' );

function aivoov_tts_change_settings() {
	$aivoov_tts_auto = sanitize_key($_POST["auto"]);
	$aivoov_read_title = sanitize_key($_POST["aivoov_read_title"]);
	$aivoov_tts_count = sanitize_key($_POST["count"]);
	$aivoov_tts_time = sanitize_key($_POST["time"]);
	$aivoov_tts_player_text_color = sanitize_key($_POST["aivoov_tts_player_text_color"]);
	$aivoov_tts_player_button_color = sanitize_key($_POST["aivoov_tts_player_button_color"]);
	$aivoov_tts_player_background_color = sanitize_key($_POST["aivoov_tts_player_background_color"]);
	$aivoov_tts_player_position = sanitize_key($_POST["aivoov_tts_player_position"]);

	$auto = (!empty($aivoov_tts_auto)) ? $aivoov_tts_auto : NULL;
	$aivoov_read_title = (!empty($aivoov_read_title)) ? $aivoov_read_title : NULL;
	$count = (!empty($aivoov_tts_count)) ? $aivoov_tts_count : NULL;
	$time = (!empty($aivoov_tts_time)) ? $aivoov_tts_time : NULL;

	update_option( 'aivoov_tts_auto', $auto === NULL ? false : true );
	update_option( 'aivoov_read_title', $aivoov_read_title === NULL ? false : true );
	update_option( 'aivoov_tts_count', $count === NULL ? false : true );
	update_option( 'aivoov_tts_time', $time === NULL ? false : true );
	update_option( 'aivoov_tts_player_background_color', $aivoov_tts_player_background_color );
	update_option( 'aivoov_tts_player_button_color', $aivoov_tts_player_button_color );
	update_option( 'aivoov_tts_player_text_color', $aivoov_tts_player_text_color );
	update_option( 'aivoov_tts_player_position', $aivoov_tts_player_position );

	$redirect_url = get_bloginfo("url") . "/wp-admin/admin.php?page=aivoov_tts&status=settingschanged";
	header("Location: ".$redirect_url);
	exit;

}

//
// HANDLE CONTENT CHANGE & ADDING NEW POST
//
add_action('transition_post_status', 'aivoov_tts_post_transition', 10, 3);

function aivoov_tts_post_transition($new_status, $old_status, $post) {
	if('publish' === $new_status && 'publish' !== $old_status && $post->post_type === 'post') {

		if ( get_option('aivoov_tts_auto') ) {

			$post_id = $post->ID;

			$res = aivoov_convert_post($post_id);

			if ($res) {
				
				$audioFile = esc_url_raw($res->audioFile);
				$playCount = intval($res->playCount);
				$playMinutes = intval($res->playMinutes);


				if($audioFile){
					update_post_meta($post_id, 'aivoov_tts_audioFile', $audioFile);
					update_post_meta($post_id, 'aivoov_tts_enabled', true);
					update_post_meta($post_id, 'aivoov_tts_count', $playCount);
					update_post_meta($post_id, 'aivoov_tts_time', $playMinutes);
				}
			}

		}

		return;

	}
}

?>
