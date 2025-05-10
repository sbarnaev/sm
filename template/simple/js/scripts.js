$(document).ready(function() {
  var url=document.location.href;
  $.each($(".client-menu__info a, .client-menu__bottom-line a"),function(){
    if(this.href==url){
      $(this).addClass('current');
    }
  });

  var block_heading_click = $('.block-heading__click');
  block_heading_click.click(function (e) {
    $(this).siblings('.mini_cut').slideToggle(300);
    $(this).parent('.cut').toggleClass('active');
  });
  $('.cut:first-child:not(.training-block):not(.un-login-cut)').addClass('active');

  $('.block-login__click').click(function () {
    $(this).parent('.block-login').toggleClass('active');
  });

  $(document).on('click', function(e) {
    if (!$(e.target).closest(".block-login__click").length) {
      $('.block-login').removeClass('active');
    }
    e.stopPropagation();
  });

  $('.promo-link').click(function (event) {
    $('.promo-block').slideToggle();
    event.preventDefault();
  });

  $(document).on('click', '.decryption-spoiler-title',function () {
    $('.decryption-spoiler-content').slideToggle();
    $(this).toggleClass('show');
  });

  $('input[type="file"]').styler();

  $('.review_desc a').each(function() {
    var a = new RegExp('/' + window.location.host + '/');
    if(!a.test(this.href)) {
      $(this).click(function(event) {
        event.preventDefault();
        event.stopPropagation();
        window.open(this.href, '_blank');
      });
    }
  });

  if ($('.datetimepicker').length > 0) {
    $.datetimepicker.setLocale('ru');
    $('.datetimepicker').datetimepicker({
      dayOfWeekStart : 1
    });
  }
});