<?php defined('BILLINGMASTER') or die;
$allow_add_true_answer = $question['true_answer'] == 2 || !$options || count(array_column($options, 'valid')) == 0 ? 1 : 0;?>

<form action="/admin/training/test/answer/add/<?="$training_id/$lesson_id/{$quest_id}";?>" method="POST" enctype="multipart/form-data" class="ajax">
    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    <input type="hidden" name="allow_add_true_answer" value="<?=$allow_add_true_answer?>">
    <input type="hidden" name="show_form" value="#modal_edit_element">

    <div class="modal-admin_top">
        <h3 class="modal-traning-title">Вариант ответа</h3>
        <ul class="modal-nav_button">
            <li><input type="submit" name="add_answer" value="Добавить" class="button save button-green"></li>
            <li class="modal-nav_button__last">
                <a class="button uk-modal-close uk-close modal-nav_button__close" href="#close"><i class="icon-close"></i></a>
            </li>
        </ul>
    </div>

    <div class="admin_form">
        <div class="row-line">
            <div class="col-1-2">
                <div class="width-100"><label>Вариант ответа</label>
                    <input type="text" name="title" placeholder="Вариант ответа" required="required">
                </div>
            </div>

            <div class="col-1-2">
                <div class="width-100"><label>Изображение</label>
                    <input id="test_answer_img" type="text" name="cover">
                    <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=1&popup=1&field_id=test_answer_img&relative_url=0', 'okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Выбрать изображение</a>
                </div>
            </div>

            <div class="col-1-2">
                <div class="width-100"><label>Это правильный ответ?</label>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="valid" type="radio" value="1"<?php if ($allow_add_true_answer) echo ' checked="checked"';?>><span>да</span></label>
                        <label class="custom-radio"><input name="valid" type="radio" value="0"<?php if (!$allow_add_true_answer) echo ' checked="checked"';?>><span>нет</span></label>
                    </span>
                </div>

                <div class="width-100"><label>Начислим баллов</label>
                    <input type="text" name="points">
                </div>
            </div>
        </div>
    </div>
</form>

<?php if(!$allow_add_true_answer):?>
<script>
  $(function() {
    $('#modal_add_answer input[name="valid"]').click(function () {
      if ($(this).val() == 1 && $('#answers_for_question input.answer-valid:checked').length > 0) {
        alert('Больше одного ответа выбрать нельзя');
        return false;
      }
    });
  });
</script>
<?php endif;?>