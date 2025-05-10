document.addEventListener('DOMContentLoaded', function() {
  $('.training_filter.select2').select2();

  $('li.access_filter_item').click(function(e) {
    e.preventDefault();
    var access = $(this).data('access');
    var $el = $(this);
    history.pushState(null, null, '?acc=' + access);
    upd_traings('?acc=' + access, $el);
  });

  $('.training_filter').change(function(e) {
    var url = '';
    var data = new Map();

    $('.training_filter option:selected').each(function(i, s) {
      var filter_name = $(this).data('filter');
      var value = $(this).val();
      url += (i == 0 ? '?' : '&') + filter_name + '=' + value;
    });

    history.pushState(null, null, url);
    upd_traings(url, '');
  });
}, false);

function upd_traings(url, $el) {
  $.ajax({
    method: 'get',
    url: url,
    dataType: 'html',
    success: function(html) {
      if (html) {
        $('.training_list').replaceWith($(html).find('.training_list'));

        if ($el) {
          $('li.access_filter_item.active').removeClass('active');
          $el.addClass('active');
        }
      }
    },
    error: function(e) {
      console.error(e.message);
    }
  });
}