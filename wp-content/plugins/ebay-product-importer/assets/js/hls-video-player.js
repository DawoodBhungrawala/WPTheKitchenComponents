
/* Function to dynamically load the HLS.js script */
function loadHlsJsScript(callback) {
  if (typeof Hls !== 'undefined') {
    /* Hls.js is already loaded, execute the callback immediately */
    if (callback && typeof callback === 'function') {
      callback();
    }
    return;
  }

  var script = document.createElement('script');
  script.src = nxtal_importer_params.hls_url; /* 'https://cdn.jsdelivr.net/npm/hls.js@latest'; */
  script.async = true;

  script.onload = function() {
    console.log('HLS.js script loaded successfully.');
    if (callback && typeof callback === 'function') {
      callback();
    }
  };

  script.onerror = function() {
    console.error('Failed to load HLS.js script.');
  };

  document.head.appendChild(script);
}

document.addEventListener('DOMContentLoaded', function() {
  var videos = document.querySelectorAll('video');

  videos.forEach(function(video) {
    var videoUrl = video.src;

    if (videoUrl.includes(".m3u8")) {
      loadHlsJsScript(function() {
        if (Hls.isSupported()) {
          var hls = new Hls();
          hls.loadSource(videoUrl);
          hls.attachMedia(video);
          hls.on(Hls.Events.MANIFEST_PARSED, function() {
            /* video.play(); */
          });
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
          video.src = videoUrl;
          video.addEventListener('canplay', function() {
            /* video.play(); */
          });
        } else {
          console.error("HLS is not supported on this browser for video:", videoUrl);
        }
      });
    }
  });
});
