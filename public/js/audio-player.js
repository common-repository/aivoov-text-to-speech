jQuery(function($){
	Plyr.setup('.js-player', {  });
});
let aivoov_tts = {
        audio_file_played: 0,
        fqdn: "https://aivoov.com/api/v1/stats/",
        article_id: !1,
        post_id: !1,
        guest_id: !1,
        device_type: "",
        time_listened: "10"
    },
    requestInterval = !1;

function calculateTotalValue(e) {
    var t = Math.floor(e / 60),
        n = (e - 60 * t).toString().substr(0, 2);
    return 1 === n.indexOf(".") && (n = "0" + n.substr(0, 1)), t + ":" + n
}

function calculateCurrentValue(e) {
    parseInt(e / 3600);
    var t = parseInt(e / 60) % 60,
        n = (e % 60).toFixed();
    return (t < 10 ? "0" + t : t) + ":" + (n < 10 ? "0" + n : n)
}

function seekForward() {
    let e = document.getElementById("player"),
        t = e.duration,
        n = e.currentTime;
    e.currentTime = n + 15 < t ? n + 15 : t
}

function seekBackward() {
    let e = document.getElementById("player"),
        t = e.currentTime;
    e.currentTime = t - 15 > 0 ? t - 15 : 0
}

function toggleShareBlock() {
    let e = document.getElementsByClassName("share-container")[0];
    "visible" === e.style.visibility ? (e.style.visibility = "hidden", e.style.opacity = 0) : (e.style.visibility = "visible", e.style.opacity = 1)
}

function removeInitialScreen() {
    console.log();
    let e = document.getElementById("aivoov_tts-player-controls"),
        t = document.getElementById("aivoov_tts-control-buttons"),
        n = document.getElementById("aivoov_tts-initial-player-screen");
    e.classList.remove("hidden"), t.classList.remove("hidden"), n.classList.add("hidden"), document.getElementById("play-btn").click()
}

function initProgressBar() {
    var e = document.getElementById("player"),
        t = e.duration,
        n = e.currentTime,
        i = calculateTotalValue(t);
    console.log(n), document.getElementById("end-time").innerHTML = i;
    var l = calculateCurrentValue(n);
    document.getElementById("start-time").innerHTML = l;
    var a = document.getElementById("seek-obj");
    a.value = e.currentTime / e.duration, a.addEventListener("click", function(t) {
       // console.log("Seek - event"), console.log(t), console.log(this);
        var n = t.offsetX / this.offsetWidth;
        e.currentTime = n * e.duration, a.value = n / 100
    }), e.currentTime == e.duration && (document.getElementById("play-btn").className = "", clearInterval(requestInterval))
}

function initPlayers(e) {
    "" === aivoov_ttsGetCookie("aivoov_tts_guest_id") && aivoov_ttsSetCookie("aivoov_tts_guest_id", generateGuestID(32), 365), aivoov_tts.guest_id = aivoov_ttsGetCookie("aivoov_tts_guest_id"), aivoov_tts.device_type = getDeviceType(), document.getElementsByName("wp_post_id").length && (aivoov_tts.article_id = document.getElementsByName("wp_post_id")[0].value, aivoov_tts.post_id = aivoov_tts.article_id);
    for (var t = 0; t < e; t++) ! function() {
        document.getElementById("player-container");
        var e = document.getElementById("player"),
            t = document.getElementById("play-btn");
        null != t && t.addEventListener("click", function() {
            !1 === e.paused ? (e.pause(), !1, document.getElementById("play-btn").className = "", clearInterval(requestInterval)) : (e.play(), document.getElementById("play-btn").className = "pause", !0, aivoov_tts.audio_file_played /*  ||jQuery.post(`${aivoov_tts.fqdn}register/playback`, {
                article_id: aivoov_tts.article_id,
                post_id: aivoov_tts.post_id,
                guest_id: aivoov_tts.guest_id,
                device_type: aivoov_tts.device_type
            }, function(e) {
                "success" === e.status && (aivoov_tts.audio_file_played = 1)
            }), requestInterval = setInterval(() => {
                jQuery.post(`${aivoov_tts.fqdn}register/listentime`, {
                    article_id: aivoov_tts.article_id,
                    post_id: aivoov_tts.post_id,
                    guest_id: aivoov_tts.guest_id,
                    device_type: aivoov_tts.device_type,
                    time_listened: 10
                }, function(e) {
                    "success" !== e.status && console.error("Error while saving request...")
                }) 
            }, 1e4)*/)
        })
    }()
}

function aivoov_ttsSetCookie(e, t, n) {
    var i = new Date;
    i.setTime(i.getTime() + 24 * n * 60 * 60 * 1e3);
    var l = "expires=" + i.toUTCString();
    document.cookie = e + "=" + t + ";" + l + ";path=/"
}

function aivoov_ttsGetCookie(e) {
    for (var t = e + "=", n = document.cookie.split(";"), i = 0; i < n.length; i++) {
        for (var l = n[i];
            " " == l.charAt(0);) l = l.substring(1);
        if (0 == l.indexOf(t)) return l.substring(t.length, l.length)
    }
    return ""
}
const getDeviceType = () => {
    const e = navigator.userAgent;
    return /(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(e) ? "tablet" : /Mobile|iP(hone|od|ad)|Android|BlackBerry|IEMobile|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(e) ? "mobile" : "desktop"
};

function generateGuestID(e) {
    let t = "",
        n = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789",
        i = n.length;
    for (var l = 0; l < e; l++) t += n.charAt(Math.floor(Math.random() * i));
    return t
}
window.setTimeout(function() {
    if (!jQuery) return console.error("Critical error! jQuery wasn't found! Please, make sure you have included jQuery library in your page!"), -1;
    jQuery(document).ready(function() {
        initPlayers(jQuery("#player-container").length)
    })
}, 100);