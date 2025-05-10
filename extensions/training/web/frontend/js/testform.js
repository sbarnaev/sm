document.addEventListener('DOMContentLoaded', function() {
  let lesson_id = $('.test input[name="lesson_id"]').val();
  let $time_left = timestamp = intervalId = null;

  let time_left = function() {
    $time_left = $('.progress-row .time-left');
    timestamp = $time_left.data('time_left');

    if (!intervalId) {
      intervalId = setInterval(() => {
        time_left_change($time_left, timestamp -= 1);
      }, 1000);
    }
  };

  let send_answers = function(url, $form) {
    let data = {lesson_id: lesson_id};

    if ($form.hasClass('test-form')) {
      let answers = get_answers($form);
      data.answers = answers;
      data.question_id = $form.find('input[name="question_id"]').val();
    }

    ajax(url, data);
  };

  let time_left_change = function($time_left, timestamp) {
    if (timestamp < 0) {
      clearTimeout(intervalId);
      intervalId = null;
      let $form = $('.test-wrap .test-form');
      send_answers('/training/lesson/test/complete', $form);

      return true;
    }

    let days = Math.floor(timestamp / 86400);
    let hours = Math.floor(timestamp / 3600) - (days * 24);
    let minutes = Math.floor(timestamp / 60) - (days * 1440 + hours * 60);
    let seconds = timestamp % 60;

    let time_left_text = (days ? days + ' д.' : '') + (hours ? ' ' + hours + ' ч.' : '') + (minutes ? ' ' + minutes + ' мин.' : '') + ' ' + seconds + ' сек.';
    $time_left.text(time_left_text);
  };

  let get_answers = function($form) {
    let answers = [];
    $form.find('input[name^="option"]:checked').each(function() {
      answers.push($(this).data('id'));
    });

    return answers;
  };

  let ajax = function(url, data) {
    $.ajax({
      url: url,
      type: "POST",
      data: data,
      success: function (data) {
        if (data && typeof(data) !== 'object') {
          $('.test-wrap').html(data);
          if (url === '/training/lesson/test/complete') {
            $('.answer_form_wrap form.form-complete input[name="is_allow_submit_homework"]').val(1);
          }
        }
        if ($('.progress-row .time-left').length > 0) {
          time_left();
        }
      }
    });
  };

  $(document).on('submit', 'form.start-test-form, form.test-form', function(e) {
    e.preventDefault();
    let $form = $(this);
    let url = $form.attr('action');
    send_answers(url, $form);
  });

  $(document).on('click','#nextBtn, #prevBtn', function() {
    let action = $(this).attr('id') === 'nextBtn' ? 'next' : 'prev';
    let question_id = $(this).parents('form').find('input[name="question_id"]').val();
    let answers = get_answers($(this).parents('form'));
    let data = {lesson_id: lesson_id, answers: answers, question_id: question_id};
    let url = $(this).attr('id') === 'nextBtn' ? '/training/lesson/test/question/next' : '/training/lesson/test/question/prev';
    ajax(url, data);
  });

  ajax('/training/lesson/test/test', {'lesson_id': lesson_id});
}, false);
