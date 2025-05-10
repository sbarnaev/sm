document.addEventListener('DOMContentLoaded', function() {
  $('[data-edit_answer]').click(function (e) {
    let $answer_block = $('.block_edit_answer');
    $answer_block.toggleClass('active');

    if ($answer_block.hasClass('active')) {
      let answer_id = $(this).data('edit_answer');
      $.ajax({
        url: '/trainingajax/answer',
        type: "POST",
        dataType: "html",
        data: {answer_id: answer_id},
        success: function (html) {
          if (html !== '') {
            $answer_block.html(html);
            $answer_block.parents('div').children('form').hide(600);
            $('input[type="file"]').styler();
          }
        }
      });
    } else {
      $answer_block.parents('div').children('form').show();
    }
  });

  $('[data-edit_comment], [data-edit_curator_comment]').click(function (e) {
    let $comment_block = $('#block_edit_comment');
    $comment_block.toggleClass('active');

    if ($comment_block.hasClass('active')) {
      if (typeof($(this).data('scroll_to')) !== 'undefined') {
        let $block2scroll = $($(this).data('scroll_to'));
        if ($block2scroll.length > 0) {
          let pos = $block2scroll.offset().top - 100;
          $([document.documentElement, document.body]).animate({
            scrollTop: pos
          }, 500);
        }
      }

      let comment_id = typeof($(this).data('edit_comment')) !== 'undefined' ? $(this).data('edit_comment') : $(this).data('edit_curator_comment');
      let lesson_id = $(this).data('lesson_id');
      let user_id = typeof($(this).data('user_id')) !== 'undefined' ? $(this).data('user_id') : null;

      $.ajax({
        url: '/trainingajax/comment',
        type: "POST",
        dataType: "html",
        data: {comment_id: comment_id, lesson_id: lesson_id, user_id: user_id},
        success: function (html) {
          if (html !== '') {
            $comment_block.parents('div').children('form').hide(600);
            $comment_block.html(html);
            $('input[type="file"]').styler();
          }
        }
      });
    } else {
      $comment_block.parents('div').children('form').show();
    }
  });

  $('.like_btn').click(function() {
    var $like_btn = $(this);
    var answer_id = $like_btn.data('answer_id');
    var is_like = $like_btn.hasClass('like') ? 0 : 1;

    $.ajax({
      url: '/trainingajax/answerlike',
      type: "POST",
      dataType: "json",
      data: {is_like: is_like, answer_id: answer_id},
      success: function ($resp) {
        if ($resp['status']) {
          if ($resp['is_like'] == 1) {
            $like_btn.addClass('like');
          } else if($resp['is_like'] == 0) {
            $like_btn.removeClass('like');
          }
        } else {
          console.error($resp['error']);
        }
      }
    });
  });
  dependent_blocks();

  /*Открыть блок с уроками, которые доступны для прохождения*/
  $('.block_list .cut').each(function() {
    if ($(this).find('.lesson-title-status.open').length > 0) {
      $(this).addClass('active');
      $(this).find('.mini_cut').css('display', 'block');
      return false;
    }
  });
}, false);


var dependent_blocks = function() {
  if ($('[data-show_on]').length > 0) {
    $('[data-show_on]').each(function () {
      let $block = $('#' + $(this).data('show_on'));
      if ($block.length > 0 && ($(this).is(':selected') || $(this).is(':checked'))) {
        $block.show();
      }
    });
  }

  $(document).on('change', 'select', function (e) {
    let $el = $("option:selected", this);
    let block_id = '';
    if (typeof($el.data('show_on')) !== 'undefined') {
      block_id = '#' + $el.data('show_on');
      $(block_id).show(200);
    }

    let $select = $el.parent('select');
    $els = $select.find('option[data-show_on]');
    $els.each(function () {
      let $bloc__id = '#' + $(this).data('show_on');
      if (block_id != $bloc__id && !$(this).is(':selected') && $($bloc__id).is(':visible')) {
        $($bloc__id).hide(200);
      }
    });
  });

  $('input[data-show_on]').parents('.custom-radio-wrap').find('input[type="radio"]').click(function () {
    $(this).parents('.custom-radio-wrap').find('input[type="radio"]').each(function () {
      let $block = $('#' + $(this).data('show_on'));
      if ($block.length > 0 && $(this).is(':checked')) {
        $block.show(200);
      } else {
        $block.hide(200);
      }
    });
  });


  if ($('[data-show_off]').length > 0) {
    $('[data-show_off]').each(function () {
      let $el = $(this);
      let blocks = $(this).data('show_off').split(',');
      blocks.forEach(function (val) {
        let $block = $('#' + val);
        if ($block.length > 0 && ($el.is(':selected') || $el.is(':checked'))) {
          $block.hide();
        }
      });
    });
  }

  $(document).on('change', $('option[data-show_off]').parent('select'), function (e) {
    let block_hide = '';
    $els = $(this).find('option[data-show_off]');
    $els.each(function () {
      let $el = $(this);
      let blocks = $(this).data('show_off').split(',');
      blocks.forEach(function (val) {
        let $block = $('#' + val);
        if ($block.length > 0) {
          if ($el.is(':selected')) {
            $block.hide(200);
            block_hide = $el.data('show_off');
          } else if (block_hide != $el.data('show_off')) {
            $block.show(200);
          }
        }
      });
    });
  });

  $(document).on('click', 'input[data-show_off]', function () {
    let $el = $(this);
    let blocks = $(this).data('show_off').split(',');
    blocks.forEach(function (val) {
      let $block = $('#' + val);
      if ($block.length > 0 && $el.is(':checked')) {
        $block.hide(200);
      }
    });
  });
};

$('.nav-click').click(function () {
  $(this).closest('.nav_gorizontal__parent-wrap').toggleClass('active');
});

$(document).on('click', function(e) {
  if (!$(e.target).closest(".nav-click").length) {
    $('.nav_gorizontal__parent-wrap').removeClass('active');
  }
  e.stopPropagation();
});


$(document).on('click', '.modal-access a[href="#ModalAccess"]', function(e) {
  e.stopPropagation();
  e.preventDefault();

  let modal_id = $(this).attr('href');
  let lesson_id = $(this).data('lesson_id');
  let section_id = $(this).data('section_id');

  $.ajax({
    url: '/trainingajax/renderbybuttons',
    type: "POST",
    dataType: "html",
    data: {lesson_id: lesson_id, section_id: section_id},
    success: function (html) {
      if (html !== '') {
        $(modal_id + ' .userbox').html(html);
      }
    }
  });

  UIkit.modal(modal_id, {center: true}).show();
});

$(document).on('click', 'a[href="#ModalAccessTestAnswer"]', function(e) {
  e.stopPropagation();
  e.preventDefault();

  let modal_id = $(this).attr('href');
  let lesson_id = $(this).data('lesson_id');
  let user_id = $(this).data('user_id');
  
  $.ajax({
    url: '/trainingajax/GetTestAnswer',
    type: "POST",
    dataType: "html",
    data: {lesson_id: lesson_id, user_id: user_id},
    success: function (html) {
      if (html !== '') {
        $(modal_id + ' .userbox').html(html);
      }
    }
  });

  UIkit.modal(modal_id, {center: true}).show();
});
