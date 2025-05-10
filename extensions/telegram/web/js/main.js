$(document).ready(function () {
  var show_result = function(resp, data) {
    if (resp.bind) {
      $('#tg_bind_account').hide();
      alert('Ваш аккаунт успешно привязан');
    }
  };

  var get_time = function() {
    return parseInt(new Date().getTime()/1000);
  };

  var binding_user = function(resp, data) {
    alert('Для привязки вам нужно будет перейти в приложение Telegram и нажать кнопку "Запустить (Start)"');

    var open = window.open(data.tg_link, '_blank');
    if (typeof(open) == 'undefined' || open == null) {
      window.location = data.tg_link;
    }

    var counter = 0;
    window.onfocus = function () {
      if (counter < 3 && $('#tg_bind_account').is(":visible")) {
        counter += 1;
        ajax('/telegram/checkbindinguser', data, show_result);
      }
    };
  };

  function ajax(url, data, func) {
    $.ajax({
      url: url,
      method: 'post',
      dataType: 'json',
      data: data,
      success: function(resp) {
        if (resp.status) {
          if (!resp.error_msg) {
            func(resp, data);
          } else {
            alert(resp.error_msg);
          }
        } else {
          alert('Произошла ошибка при обработке данных');
          console.log(resp.error);
        }
      },
      error: function(err) {
        alert("Произошла ошибка при обработке данных");
        console.log(err);
      }
    });
  }

  $('#tg_bind_account').click(function() {
    var tg_username = $(this).parents('form').find('[name="nick_telegram"]').val();
    var tg_link = $(this).data('link');
    if (tg_username && tg_link) {
      var data = {'tg_link': tg_link, 'tg_username': tg_username};
      ajax('/telegram/savedata', data, binding_user);
    }
  });
});
