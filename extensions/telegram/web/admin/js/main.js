$(document).ready(function () {
  function ajax(url, token, argv) {
    $.ajax({
      url: url  + (argv ? '?' + argv : ''),
      type: "POST",
      dataType: "json",
      data: {token: token},
      success: function (resp) {
        if (!resp.msg_error) {
          $('.progressbar-wrap').show();
          $(".progressbar-loader").css('width', resp.progress + '%');
          $(".progressbar-counter").html('Пользователей обработано: ' + resp.processed + ' (' + resp.progress + '%)');

          if (!resp.is_finish) {
            ajax(url, token, '');
          } else {
            setTimeout(function() {
              $('.progressbar-wrap').hide();
              alert('Удалено ' + resp.del_users + ' пользователей из чатов');
            }, 1000);
          }
        } else {
          $('.progressbar-wrap').hide();
          alert(resp.msg_error);
        }
      }
    });
  };

  $('a[name="del_stowaways"]').click(function() {
    var token = $(this).parents('form').find('[name="token"]').val();
    $(".progressbar-loader").css('width', '0%');
    $(".progressbar-counter").html('Пользователей обработано: 0 (0%)');

    ajax('/admin/telegramsetting/delstowaways', token, 'start=1');
  });
});
