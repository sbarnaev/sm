<?php defined('BILLINGMASTER') or die;?>

<form action="/admin/training/test/question/edit/<?="$training_id/$lesson_id/{$quest_id}";?>" class="ajax" id="question_save" method="POST" enctype="multipart/form-data">
    <div class="modal-admin_top">
        <h3 class="modal-traning-title">Выбор. Вопрос - ответ</h3>
        <ul class="modal-nav_button">
            <li><input type="submit" name="save_quest" value="Сохранить" class="button save button-green"></li>
            <li class="modal-nav_button__last">
                <a class="button uk-modal-close uk-close modal-nav_button__close" href="#close"><i class="icon-close"></i></a>
            </li>
        </ul>
    </div>

    <div class="admin_form">
        <div class="admin_message hidden">Успешно!</div>

        <div class="row-line">
            <div class="col-1-1 mb-0">
                <h4>Суть вопроса</h4>
            </div>

            <div class="col-1-2">
                <div class="width-100"><label>Вопрос</label>
                    <input type="text" name="quest[name]" value="<?=$question['question'];?>" required="required">
                </div>
            </div>


            <div class="col-1-2">
                <div class="width-100"><label>Изображение</label>
                    <input id="test_question_img-<?=$question['quest_id'];?>" type="text" name="quest[cover]" value="<?=$question['image'];?>">
                    <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=1&popup=1&field_id=test_question_img-<?=$question['quest_id'];?>&relative_url=0', 'okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Выбрать изображение</a>
                </div>

                <?php if($question['image']):?>
                    <div class="width-100">
                        <img src="<?=$question['image']?>" alt="" width="140">
                    </div>
                <?php endif;?>
            </div>

            <div class="col-1-1">
                <div class="width-100"><label>Пояснение для расшифровки</label>
                    <textarea name="quest[help]" placeholder="Выводится после окончания теста"><?=$question['help'];?></textarea>
                </div>
            </div>
        </div>

        <div class="row-line">
            <div class="col-1-2">
                <div class="width-100"><label>Правильный ответ</label>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="quest[true_answer]" type="radio" value="1"<?php if($question['true_answer'] == 1) echo ' checked="checked"';?>><span>один</span></label>
                        <label class="custom-radio"><input name="quest[true_answer]" type="radio" value="2"<?php if($question['true_answer'] == 2) echo ' checked="checked"';?>><span class="not-red">несколько</span></label>
                    </span>
                </div>
            </div>

            <div class="col-1-2">
                <div class="width-100"><label>Обязательно выбрать все правильные</label>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="quest[require_all_true]" type="radio" value="1"<?php if($question['require_all_true'] == 1) echo ' checked="checked"';?>><span>да</span></label>
                        <label class="custom-radio"><input name="quest[require_all_true]" type="radio" value="0"<?php if($question['require_all_true'] == 0) echo ' checked="checked"';?>><span>нет</span></label>
                    </span>
                </div>
            </div>

            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
        </div>

        <div class="row-line">
            <div class="col-1-1">
                <h4>Варианты ответа</h4>
                <div class="width-100">
                    <a href="javascript:void(0)" class="button button-green test-edit" data-url="/admin/trainingajax/testanswerform?quest_id=<?="{$question['quest_id']}&lesson_id=$lesson_id&training_id=$training_id";?>" data-modal_id="modal_add_answer"><i class="el-icon"></i>Добавить ответ</a>
                </div>
            </div>

            <?php require_once(__DIR__ . '/list_answers.php');?>
        </div>
    </div>
</form>

<script src="/extensions/training/web/admin/js/test_question.js"></script>