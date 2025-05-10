<?php defined('BILLINGMASTER') or die;?>
<!-- Добавить вопрос теста -->
<div id="modal_add_test" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-add-elem">
        <div class="userbox modal-userbox-3">
            <form action="/admin/training/test/question/add/<?="{$lesson['training_id']}/{$lesson['lesson_id']}";?>" method="POST">
                <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                <div class="modal-admin_top">
                    <h3 class="modal-traning-title"><span class="modal-traning-title-icon">
                            <img src="/extensions/training/web/admin/images/icons/variant.svg" alt="">
                        </span>Выбор. Вопрос - ответ
                    </h3>

                    <ul class="modal-nav_button">
                        <li><input type="submit" name="add_quest" value="Добавить" class="button save button-green"></li>
                        <li class="modal-nav_button__last">
                            <a class="button uk-modal-close uk-close modal-nav_button__close" href="#close"><i class="icon-close"></i></a>
                        </li>
                    </ul>
                </div>

                <div class="admin_form">
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Суть вопроса</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Вопрос</label>
                                <input type="text" name="quest[name]" required="required">
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Изображение</label>
                                <input id="test_question_img" type="text" name="quest[cover]">
                                <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=1&popup=1&field_id=test_question_img&relative_url=0', 'okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Выбрать изображение</a>
                            </div>
                        </div>

                        <div class="col-1-1">
                            <div class="width-100"><label>Пояснение для расшифровки</label>
                                <textarea name="quest[help]" placeholder="Выводится после окончания теста"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-2">
                            <div class="width-100"><label>Правильный ответ</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="quest[true_answer]" type="radio" value="1" checked="checked"><span>один</span></label>
                                    <label class="custom-radio"><input name="quest[true_answer]" type="radio" value="2"><span>несколько</span></label>
                                </span>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Обязательно выбрать все<br>правильные</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="quest[require_all_true]" type="radio" value="1" checked="checked"><span>да</span></label>
                                    <label class="custom-radio"><input name="quest[require_all_true]" type="radio" value="0"><span>нет</span></label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Добавить ответ -->
<div id="modal_add_answer" class="uk-modal" data-show_modal="modal_edit_element">
    <div class="uk-modal-dialog uk-modal-add-elem">
        <div class="userbox modal-userbox-3"></div>
    </div>
</div>