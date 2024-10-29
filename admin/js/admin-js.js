
function set_favorite(voice_id, language_id, ids, engine) {
	var data = {
		'action': 'aivoov_tts_add_to_favourite', 
		'voice_id': voice_id,
		'language_id': language_id,
		'engine': engine,
		'ids': ids
	};
	jQuery.post(ajaxurl, data, function(response) { 
		var json = JSON.parse(response)
		if (json.result) { 
		if (json.is_delete == 2) { 
			jQuery('#table #fav' + ids+' a').html('Remove Favourite');
		} else {
			jQuery('#table #fav' + ids+' a').html('Add to Favourite');
		} 
	  }
	  else { 
		alert(json.message);
		return;
	  }
	}); 
}


function sample_voice(url, id) {
		var sound = document.getElementById('plyPro');
		var old_id = sound.getAttribute("data-id");
		if(old_id != null){
			jQuery('#play' + old_id+' .icon').addClass('ni-play-circle');
			jQuery('#play' + old_id+' .icon').removeClass('ni-stop-circle text-danger'); 
		}
		if (sound.duration > 0 && !sound.paused && old_id == id) {
			sound.pause();
			sound.currentTime = 0;
			jQuery('#play' + id+' .icon').addClass('ni-play-circle');
			jQuery('#play' + id+' .icon').removeClass('ni-stop-circle text-danger'); 
		}else{
			jQuery('#play' + id+' .icon').removeClass('ni-play-circle');
			jQuery('#play' + id+' .icon').addClass('ni-stop-circle text-danger');
			jQuery("#plyPro").attr("data-id", id);
			jQuery("#plyPro").attr("src", url).trigger("play");
		}

} 

 jQuery("#plyPro").bind('ended', function () { 
	jQuery('.playAudio .icon').removeClass('ni-stop-circle');
	jQuery('.playAudio .icon').addClass('ni-play-circle');
	jQuery("#plyPro").attr("src","");
  });

function updateQueryStringParameter(uri, key, value) {
	var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
	var separator = uri.indexOf('?') !== -1 ? "&" : "?";
	if (uri.match(re)) {
		return uri.replace(re, '$1' + key + "=" + value + '$2');
	}
	else {
		return uri + separator + key + "=" + value;
	}
}

const aivoov_handle_enable_toggle = (e, el, id) => {

	e.preventDefault();

	const countColumn = el.parentNode.parentNode.parentNode.querySelector('.column-aivoov_tts_count');
	const timeColumn = el.parentNode.parentNode.parentNode.querySelector('.column-aivoov_tts_time');

	const switchEl = el.parentNode;
	switchEl.classList.add('loading');

	const enable = el.checked;

	jQuery(document).ready(function($) {
		var data = {
			'action': 'aivoov_tts_convert_enable_post_ajax',
			'enable': enable,
			'id': id
		};

		jQuery.post(ajaxurl, data, function(response) {
			if (response) {

				const { playCount: count, playMinutes: time } = JSON.parse(response);
				const notice = document.querySelector('.aivoov_tts-notice--add-first');

				if (enable) {

					const url = window.location.pathname;
					const newUrl = `${url}?aivoov_tts_successfuly_converted=1`;
					window.location.replace( newUrl );
					
				} else {

					const url = window.location.pathname;
					window.location.replace( url );

				}

			} else {

				const url = window.location.pathname;
				const newUrl = `${url}?aivoov_tts_unable_to_convert=1`;
				window.location.replace( newUrl );
			}
			switchEl.classList.remove('loading');
		});
	});

}

const aivoov_handle_enable_toggle_single_post = (e, el, id) => {

	e.preventDefault();

	const switchEl = el.parentNode;
	switchEl.classList.add('loading');

	const enable = el.checked;
	const box = el.parentNode.parentNode;

	jQuery(document).ready(function($) {
		var data = {
			'action': 'aivoov_tts_convert_enable_post_ajax',
			'enable': enable,
			'id': id
		};

		jQuery.post(ajaxurl, data, function(response) {
			console.log(response) 
			if (enable) {
				el.checked = true;
				box.classList.add('checked');
			} else {
				el.checked = false;
				box.classList.remove('checked');
			}
			switchEl.classList.remove('loading');
		});
	});

}
const aivoov_handle_enable_toggle_single_post_recreate = (e, el, id) => {

	e.preventDefault();

	const switchEl = el.parentNode;
	switchEl.classList.add('loading');
	var voice_id = document.getElementById("voice");
	var voice_id = voice_id.value; 
	const enable = true;
	const box = el.parentNode.parentNode;
	el.value="Please Wait..."; 
	el.disabled=true; 
	jQuery(document).ready(function($) { 
		var engine = jQuery('#voice option:selected').data('engine');
		var hash_key = jQuery('#voice option:selected').data('hash_key');
		var data = {
			'action': 'aivoov_tts_convert_post_ajax',
			'enable': enable,
			'hash_key': hash_key,
			'voice_id': voice_id,
			'engine': engine,
			'id': id
		};
		jQuery.post(ajaxurl, data, function(response) {
			console.log(response)
			var res = JSON.parse(response)
			if (res.status) {
				jQuery('#aivoov_status_success').text(res.message);
				jQuery('#aivoov_status').text("");
				location.reload();
			}else{
				jQuery('#aivoov_status').text(res.message);
			}
			el.value="Re-Create Audio"; 
			el.disabled=false; 
			switchEl.classList.remove('loading');
		});
	});

}
const aivoov_handle_default_vocie = () => {
 
	var voice_id = document.getElementById("voice");
	 
	jQuery(document).ready(function($) {
		
		var engine = jQuery('#voice option:selected').data('engine');
		var hash_key = jQuery('#voice option:selected').data('hash_key');
		var data = {
			'action': 'aivoov_handle_default_vocie_ajax',
			'voice_id': voice_id.value, 
			'hash_key': hash_key, 
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('#aivoov_status_success').text("Default voice change to "+ voice_id.options[voice_id.selectedIndex].text);
		});
	});

}
const aivoov_handle_sync_voice = () => {
	var e = document.getElementById("sync_audio");
	e.value = "Syncing...";
	jQuery(document).ready(function($) {
		var data = {
			'action': 'aivoov_handle_sync_voice_ajax'
		};
		jQuery.post(ajaxurl, data, function(response) { 
			response =JSON.parse(response);
			var voice = response.data;
			var append_voice = '';  
			var selected = '';
			if(voice.length > 0){
				for (let i = 0; i < voice.length; i++) {
					if("" == voice[i].voice_id)  selected = "selected"; else selected='';
					append_voice += "<option value="+voice[i].voice_id+"  data-hash_key="+voice[i].hash_key+"   data-engine="+voice[i].engine+"  "+selected+"><em>"+voice[i].name+"</em> - "+voice[i].gender+" - "+voice[i].language_name+" </option>";
				}  
				if (document.getElementById('voice')){
					document.getElementById("voice").innerHTML = append_voice;
				}else{
					window.location.replace( window.location.pathname+'?page=aivoov_tts&status=sync' );
				}
				
				jQuery('#aivoov_status_success').text("Voice synchronized successfully");
			}else{ 
				jQuery('#aivoov_status').text("Voice not found. Please add voice favorite in aivoov account before sync voice.");
			}
			
			e.value = "Sync Favorite voices";
		});
	});

}

const aivoov_handle_bulk_actions = () => {

	const bulkActionForm = document.querySelector('#posts-filter');
	if (bulkActionForm) {
		const applyButton = bulkActionForm.querySelector('#doaction');
		const select = bulkActionForm.querySelector('#bulk-action-selector-top');
		applyButton.addEventListener('click', () => {
			const value = select.value;

			const rowsMarked = [...bulkActionForm.querySelectorAll('#the-list tr')].filter( tr => tr.querySelector('.check-column input').checked );
			const inputs = rowsMarked.map( tr => tr.querySelector('.column-aivoov_tts_enable input') );

			if ( value === 'aivoov_tts_add_audio' ) {
				[...inputs].filter( input => !input.checked ).forEach( input => input.parentNode.classList.add('loading') );
			} else if ( value === 'aivoov_tts_remove_audio' ) {
				[...inputs].filter( input => input.checked ).forEach( input => input.parentNode.classList.add('loading') );
			}
		})
	}

}

const aivoov_tts_setup_add_first_post_button = () => {
	const aivoov_ttsAddFirstAudio = document.querySelector('#aivoov_tts_add_first_audio');
	if (aivoov_ttsAddFirstAudio) {
		aivoov_ttsAddFirstAudio.addEventListener('click', e => {
			e.preventDefault();
			
			if ( !aivoov_ttsAddFirstAudio.classList.contains('adding') ) {

				aivoov_ttsAddFirstAudio.innerText = 'Adding...';
				aivoov_ttsAddFirstAudio.classList.add('adding');

				const id = aivoov_ttsAddFirstAudio.dataset.id;
 
 
				const data = {
					'action': 'aivoov_tts_convert_post_ajax',
					'enable': true, 
					'id': id
				};

				jQuery.post(ajaxurl, data, function(response) {
					if (response) {
						const url = window.location.href;
						const newUrl = updateQueryStringParameter(url, 'aivoov_tts_successfuly_converted', 1);
						window.location.replace( newUrl );
					} else {
						const url = window.location.href;
						const newUrl = updateQueryStringParameter(url, 'bulk_aivoov_tts_audio_removed', 1);
						window.location.replace( newUrl );
					}
				});

			}

		})
	}
}

window.onload = () => {
  aivoov_handle_bulk_actions();
  aivoov_tts_setup_add_first_post_button();
};

