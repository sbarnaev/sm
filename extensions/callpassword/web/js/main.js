$(document).ready(function () {
  $('#cp_confirm').click(function() {
    let $phone = $(this).parents('form').find('[name="phone"]');
    let phone = $phone.val();
    let phone_code = '';
    if ($('.selected-flag .selected-dial-code').length > 0) {
      phone_code = $(this).parents('form').find('.selected-flag .selected-dial-code').text();
    }

    if (phone) {
      alert('Сейчас вам позвонят, что бы подтвердить номер');
      $('.loader_box').show();
      $.ajax({
        url: '/callpassword/confirmphone',
        method: 'post',
        dataType: 'json',
        data: {phone: phone, phone_code: phone_code},
        success: function(resp) {
          $('.loader_box').hide();
          if (resp.status) {
            if (resp.confirm) {
              $phone.parents('.button_right_box').removeClass('button_right_box');
              $('#cp_confirm').hide();
              alert('Ваш номер подтвержден');
            } else {
              alert('Ваш номер не подтвержден');
            }
          } else {
            $('.loader_box').hide();
            alert('Произошла ошибка при обработке данных');
            console.log(resp.error);
          }
        },
        error: function(err) {
          $('.loader_box').hide();
          alert("Произошла ошибка при обработке данных");
          console.log(err);
        }
      });
    }
  });
});
