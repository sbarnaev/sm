$(function() {
  $('#question_save [name="quest[true_answer]"]').click(function() {
    if ($(this).prop("checked") && $('#answers_for_question input.answer-valid:checked').length > 1) {
      alert('Нельзя выбрать эту опцию с несколькими правильными ответами');
      return false;
    }
  });

  $('#answers_for_question input.answer-valid').on("click", function() {
    let true_answer = $('#question_save [name="quest[true_answer]"]:checked').val();

    if ($(this).prop("checked") && true_answer == 1 && $('#answers_for_question input.answer-valid:checked').length > 1) {
      alert('Больше одного ответа выбрать нельзя');
      return false;
    }
  });
});