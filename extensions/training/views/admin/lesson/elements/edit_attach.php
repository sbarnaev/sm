<?php defined('BILLINGMASTER') or die;
$path_info = pathinfo($element['params']['attach']);
$attach_name = $path_info['basename'];?>

<form enctype="multipart/form-data" action="/admin/training/lessons/element/edit/<?=$element['id'];?>" method="POST">
    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    <input type="hidden" name="current_attach" value="<?=$element['params']['attach'];?>">
    <input type="hidden" name="training_id" value="<?=$lesson['training_id'];?>">
    <input type="hidden" name="params[attach]" value="<?=$element['params']['attach'];?>">

    <div class="modal-admin_top">
        <h3 class="modal-traning-title"><span class="modal-traning-title-icon"><img width="18" src="/extensions/training/web/admin/images/icons/attach.svg" alt=""></span>Редактировать вложение</h3>
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
                <div class="width-100"><label>Тип вложения</label>
                    <div class="select-wrap">
                        <select name="params[type]">
                            <option value="1" data-show_on="edit_lesson_attach"<?php if($element['params']['type'] == 1) echo ' selected="selected"';?>>Файл</option>
                            <option value="2" data-show_on="edit_lesson_link"<?php if($element['params']['type'] == 2) echo ' selected="selected"';?>>Ссылка</option>
                        </select>
                    </div>
                </div>
                <p class="width-100"><label>Заголовок</label>
                    <input type="text" name="params[title]" required="required" value="<?=$element['params']['title'];?>">
                </p>
                <div class="width-100"><label>Служебное название</label>
                    <input placeholder="Название элемента в списке" type="text" name="params[name]" required="required" value="<?=$element['params']['name'];?>">
                </div>
                <p class="width-100"><label>Выстроить вложения в ряд</label>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="params[line_up]" type="radio" value="1"<?php if($element['params']['line_up']) echo ' checked';?>><span>Да</span></label>
                        <label class="custom-radio"><input name="params[line_up]" type="radio" value="0"<?php if(!$element['params']['line_up']) echo ' checked';?>><span>Нет</span></label>
                    </span>
                </p>
            </div>
            <div class="col-1-2">

                <div class="<?=$element['params']['type'] != 1 ? ' hidden' : '';?>" id="edit_lesson_attach">
                    <p class="width-100"><label>Выберете файл</label>
                        <input type="file" name="attach">
                    </p>

                    <?php if($element['params']['attach']):?>
                    <div class="lesson_attach popap-lesson_attach">
                        <img src="<?=$element['params']['cover'] ? $element['params']['cover'] : '/template/admin/images/attachment.png'?>">
                        <span class="small"><?=$attach_name;?></span>
                    </div>
                    <?php endif;?>
                </div>

                <p class="mt-0 width-100<?=$element['params']['type'] != 2 ? ' hidden' : '';?>" id="edit_lesson_link"><label>Укажите ссылку</label>
                    <input type="text" name="params[link]"  value="<?=$element['params']['link'];?>">
                </p>
                <p class="width-100"><label>Обложка</label>
                    <input id="edit_attach_cover" type="text" name="params[cover]" value="<?=$element['params']['cover'];?>">
                    <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=1&popup=1&field_id=edit_attach_cover&relative_url=0', 'okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Выбрать изображение</a>
                </p>
            </div>
        </div>
    </div>
</form>