<?php defined('BILLINGMASTER') or die;?>

<form enctype="multipart/form-data" action="/admin/training/lessons/element/edit/<?=$element['id'];?>" method="POST">
    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    <input type="hidden" name="training_id" value="<?=$lesson['training_id'];?>">

    <div class="modal-admin_top">
        <h3 class="modal-traning-title"><span class="modal-traning-title-icon"><img src="/extensions/training/web/admin/images/icons/text.svg" alt=""></span>Редактировать текст</h3>
        <ul class="modal-nav_button">
            <li><input type="submit" name="edit_element" value="Сохранить" class="button save button-green"></li>
            <li class="modal-nav_button__last">
                <a class="button uk-modal-close uk-close modal-nav_button__close" href="#close"><i class="icon-close"></i></a>
            </li>
        </ul>
    </div>

    <div class="admin_form">
        <div class="row-line">

            <div class="col-1-2">
                <div class="width-100"><label>Тип</label>
                    <div class="select-wrap">
                        <select name="params[type]">
                            <option value="<?=TrainingLesson::ELEMENT_TEXT_TYPE_TEXT;?>"<?php if($element['params']['type'] == TrainingLesson::ELEMENT_TEXT_TYPE_TEXT) echo ' selected="selected"';?>>Текст</option>
                            <option value="<?=TrainingLesson::ELEMENT_TEXT_TYPE_ACCORDEON;?>" data-show_on="edit_text_title"<?php if($element['params']['type'] == TrainingLesson::ELEMENT_TEXT_TYPE_ACCORDEON) echo ' selected="selected"';?>>Аккордеон</option>
                            <option value="<?=TrainingLesson::ELEMENT_TEXT_TYPE_POPAP;?>" data-show_on="edit_text_title"<?php if($element['params']['type'] == TrainingLesson::ELEMENT_TEXT_TYPE_POPAP) echo ' selected="selected"';?>>Всплывающее окно</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-1-2 hidden" id="edit_text_title">
                <div class="width-100"><label>Заголовок</label>
                    <input type="text" name="params[title]" value="<?=$element['params']['title'];?>">
                </div>
            </div>

            <div class="col-1-1">
                <div class="width-100"><label>Текстовый редактор</label>
                    <textarea class="editor" name="params[text]" rows="3" cols="40" required="required"><?=$element['params']['text'];?></textarea>
                </div>
            </div>

            <div class="col-1-2">
                <div class="width-100"><label>Служебное название</label>
                    <input placeholder="Название элемента в списке" type="text" name="params[name]" required="required" value="<?=$element['params']['name'];?>">
                </div>
            </div>
        </div>
    </div>
</form>