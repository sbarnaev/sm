<?php defined('BILLINGMASTER') or die;?>

<form  enctype="multipart/form-data" action="/admin/training/lessons/element/edit/<?=$element['id'];?>" method="POST">
    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    <input type="hidden" name="training_id" value="<?=$lesson['training_id'];?>">

    <div class="modal-admin_top">
        <h3 class="modal-traning-title"><span class="modal-traning-title-icon"><img src="/extensions/training/web/admin/images/icons/video.svg" alt=""></span>Редактировать видео/аудио</h3>
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
                <div class="width-100"><label>Тип элемента</label>
                    <div class="select-wrap">
                        <select name="params[element_type]element_type">
                            <option value="2"<?if($element['params']['element_type'] == 2) echo ' selected="selected"';?>>Видео</option>
                            <option value="3"<?if($element['params']['element_type'] == 3) echo ' selected="selected"';?>>Аудио</option>
                            <option value="4"<?if($element['params']['element_type'] == 4) echo ' selected="selected"';?> data-show_off="edit_playlist_cover_box">Ссылка на видеохостинг</option>
                            <option value="5"<?if($element['params']['element_type'] == 5) echo ' selected="selected"';?> data-show_on="add_playlist_item_file">Изображение</option>
							<option value="1"<?if($element['params']['element_type'] == 1) echo ' selected="selected"';?>>Infoprotector</option>
                        </select>
                    </div>
                </div>
                <div class="width-100"><label>Заголовок</label>
                    <input type="text" name="params[title]" placeholder="Заголовок" value="<?=$element['params']['title'];?>">
                </div>
                <div class="width-100"><label>Служебное название</label>
                    <input type="text" name="params[name]" placeholder="Название элемента в списке" required="required" value="<?=$element['params']['name'];?>">
                </div>
                <div class="width-100"><label title="Для вывода водного знака нужно сконфигурировать свой плеер. Подробнее в справке.">Выводить watermark:</label>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="params[show_watermark]" type="radio" value="1"<?php if($element['params']['show_watermark']) echo ' checked';?>><span>Да</span></label>
                        <label class="custom-radio"><input name="params[show_watermark]" type="radio" value="0"<?php if(!$element['params']['show_watermark']) echo ' checked';?>><span>Нет</span></label>
                    </span>
                </div>
            </div>
            <div class="col-1-2">
                <div class="width-100"><label>URL</label>
                    <input type="text" name="params[url]" placeholder="" required="required" value="<?=$element['params']['url'];?>">
                </div>
                <div class="width-100<?php if($element['params']['element_type'] == 4) echo ' hidden';?>" id="edit_playlist_cover_box"><label>Обложка</label>
                    <input id="edit_playlist_cover" type="text" name="params[cover]" value="<?=$element['params']['cover'];?>">
                    <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=1&popup=1&field_id=edit_playlist_cover&relative_url=0', 'okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Выбрать изображение</a>
                </div>
            </div>
        </div>
    </div>
</form>