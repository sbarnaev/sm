<?php defined('BILLINGMASTER') or die;?>

<div id="modal_add_attach" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-add-elem">
        <div class="userbox modal-userbox-3">
            <form  enctype="multipart/form-data" action="/admin/training/lessons/element/add/" method="POST">
                <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                <input type="hidden" name="training_id" value="<?=$training_id;?>">
                <input type="hidden" name="lesson_id" value="<?=$lesson_id;?>">
                <input type="hidden" name="element_type" value="<?=TrainingLesson::ELEMENT_TYPE_ATTACH?>">
                <input type="hidden" name="params[attach]" value="">

                <div class="modal-admin_top">
                    <h3 class="modal-traning-title"><span class="modal-traning-title-icon"><img width="18" src="/extensions/training/web/admin/images/icons/attach.svg" alt=""></span>Добавить вложение</h3>
                    <ul class="modal-nav_button">
                        <li><input type="submit" name="add_element" value="Добавить" class="button save button-green"></li>
                        <li class="modal-nav_button__last">
                            <a class="button uk-modal-close uk-close modal-nav_button__close" href="#close"><i class="icon-close"></i></a>
                        </li>
                    </ul>
                </div>

                <div class="admin_form">
                    <div class="row-line">

                        <div class="col-1-2">
                            <div class="width-100"><label>Тип вложения</label>
                                <div class="select-wrap">
                                    <select name="params[type]">
                                        <option value="1" data-show_on="add_lesson_attach">Файл</option>
                                        <option value="2" data-show_on="add_lesson_link">Ссылка</option>
                                    </select>
                                </div>
                            </div>
                            <div class="width-100"><label>Заголовок</label>
                                <input type="text" name="params[title]" placeholder="" required="required">
                            </div>
                            <div class="width-100"><label>Служебное название</label>
                                <input type="text" name="params[name]" placeholder="Название элемента в списке" required="required">
                            </div>
                            <p class="width-100"><label>Выстроить вложения в ряд</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="params[line_up]" type="radio" value="1" checked><span>Да</span></label>
                                    <label class="custom-radio"><input name="params[line_up]" type="radio" value="0"><span>Нет</span></label>
                                </span>
                            </p>

                        </div>
                        <div class="col-1-2">
                            <p class="width-100 hidden" id="add_lesson_attach"><label>Выберете файл</label>
                                <input type="file" name="attach">
                            </p>

                            <p class="width-100 hidden mt-0" id="add_lesson_link"><label>Укажите ссылку</label>
                                <input type="text" name="params[link]">
                            </p>

                            <div class="width-100"><label><?=System::Lang('ICON');?></label>
                                <input id="add_attach_cover" type="text" name="params[cover]" placeholder="">
                                <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=1&popup=1&field_id=add_attach_cover&relative_url=0', 'okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Выбрать изображение</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>