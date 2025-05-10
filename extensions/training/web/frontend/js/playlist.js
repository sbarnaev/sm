document.addEventListener("DOMContentLoaded", () => {
  var players = {};

  if ($('.player-content').find('.playlist_item').length > 0) {
    $('.player-content').find('.playlist_item').each(function(i, el) {
      players[i]  = new Playerjs({id: $(el).attr('id'), file:window.atob($(el).data('url')), design: 1, poster: $(el).data('cover')});
    });
  }

  $('.playlist-link').click(function() {
    var $el = $(this);
    $('.playlist-link').removeClass('active');
    $el.addClass('active');

    var index = $(this).index('.playlist-link');
    $('.player-content a').hide();
    $('.player-content a').eq(index).show();
    var plTitle = $el.parents('.player-content').data('title');
    $('.playlist-top .playlist-title').text(plTitle);

    $('.player-content a').find('iframe').replaceWith('<div class="iframe"></div>');

    if ($el.data('type') == 4) {
      let frame_html = '';
      if ($el.attr('href').indexOf('//player.vimeo.com') !== -1) {
        frame_html = '<iframe src="' + $el.attr('href') + '" style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
      } else {
        frame_html = '<iframe src="' + $el.attr('href') + '" frameborder="0" modestbranding="1" showinfo="0" rel="0" enablejsapi="1" allowfullscreen></iframe>';
      }
      $('.player-content a').eq(index).find('.iframe').replaceWith(frame_html);
    }

    for(key in players){
      players[key].api("stop");
    };

    return false;
  });

  $(".open-playlist").click(function () {
    $(".playlist-wrap").toggleClass("show");
  });

});
