    document.addEventListener("DOMContentLoaded",
        function() {
            var div, n,
                v = document.getElementsByClassName("yep-youtube");
            for (n = 0; n < v.length; n++) {
                div = document.createElement("div");
				if (typeof v[n].dataset.nocookie !== 'undefined' && v[n].dataset.nocookie === '1'){
					div.setAttribute("data-nocookie", v[n].dataset.nocookie);
				}
				if (typeof v[n].dataset.controls !== 'undefined' && v[n].dataset.controls === '0'){
					div.setAttribute("data-controls", v[n].dataset.controls);
				}
				if (typeof v[n].dataset.start !== 'undefined' && parseInt(v[n].dataset.start) > 0 ) {
					div.setAttribute("data-start", v[n].dataset.start);
				}
                div.setAttribute("data-id", v[n].dataset.id);
                div.innerHTML = yepThumb(v[n].dataset.id);
                div.onclick = yepIframe;
                v[n].appendChild(div);
            }
        });

	function yepThumb(id) {
		var thumbRes = (document.body.clientWidth > 640) ? 'maxresdefault.jpg' : 'hqdefault.jpg',
			thumbImg = '<img src="https://i.ytimg.com/vi/ID/'+thumbRes+'">',
			thumbBut = '<p class="yepPlayButton"><svg height="100%" version="1.1" viewBox="0 0 68 48" width="100%"><path class="ytp-large-play-button-bg" d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z" fill="#212121" fill-opacity="0.8"></path><path d="M 45,24 27,14 27,34" fill="#fff"></path></svg></p>';
		return thumbImg.replace("ID", id) + thumbBut;
	}


    function yepIframe() {
        var iframe = document.createElement("iframe");
		var ytdomain = (this.dataset.nocookie === '1') ? 'youtube-nocookie' : 'youtube' ;
		var controls = (this.dataset.controls === '0') ? '&controls=0' : '' ;
		var start = (this.dataset.start > 0) ? '&start='+this.dataset.start : '' ;
        iframe.setAttribute("src", "https://www."+ytdomain+".com/embed/" + this.dataset.id + "?autoplay=1"+controls+start );
        iframe.setAttribute("frameborder", "0");
        iframe.setAttribute("allowfullscreen", "1");
        iframe.setAttribute("mute", "1");
        iframe.setAttribute("allow", "accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture");
        this.parentNode.replaceChild(iframe, this);

    }
  
    var playButtons = document.getElementsByClassName("yepPlayButton");
	for (var i = 0; i < playButtons.length; i++) {
		playButtons[i].addEventListener("click", function () {
			video.muted = false;
		});
	}