<?php defined('BILLINGMASTER') or die;?>

<form enctype="multipart/form-data" action="/admin/training/lessons/playlistitem/edit/<?=$playlist_item['id'];?>" method="POST">
    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    <input type="hidden" name="training_id" value="<?=$lesson['training_id'];?>">

    <div class="modal-admin_top">
        <h3 class="modal-traning-title"><span class="modal-traning-title-icon"><img src="/extensions/training/web/admin/images/icons/pleylist-icon.svg" alt=""></span>Редактировать плейлист</h3>
        <ul class="modal-nav_button">
            <li><input type="submit" name="edit_playlist_item" value="Сохранить" class="button save button-green"></li>
            <li class="modal-nav_button__last">
                <a class="button uk-modal-close uk-close modal-nav_button__close" href="#close"><i class="icon-close"></i></a>
            </li>
        </ul>
    </div>

    <div class="admin_form">
        <div class="row-line">
            <div class="col-1-1 mb-0">
                <h4>Редактировать элемент плейлиста <?=$playlist_item['params']['title'];?></h4>
            </div>

            <div class="col-1-2">
                <div class="width-100"><label>Тип элемента</label>
                    <div class="select-wrap">
                        <select name="params[type]">
                            <option value="1"<?if($playlist_item['params']['type'] == 1) echo ' selected="selected"';?>>Infoprotector</option>
                            <option value="2"<?if($playlist_item['params']['type'] == 2) echo ' selected="selected"';?>>Видео</option>
                            <option value="3"<?if($playlist_item['params']['type'] == 3) echo ' selected="selected"';?>>Аудио</option>
                            <option value="4"<?if($playlist_item['params']['type'] == 4) echo ' selected="selected"';?> data-show_off="adit_pl_item_cover_box">Ссылка на видеохостинг</option>
                            <option value="5"<?if($playlist_item['params']['type'] == 5) echo ' selected="selected"';?> data-show_off="edit_pl_item_url,edit_pl_item_time">Изображение</option>
                        </select>
                    </div>
                </div>
                <div class="width-100"><label>Заголовок</label>
                    <input type="text" name="params[title]" required="required" value="<?=$playlist_item['params']['title'];?>">
                </div>
                <div class="width-100" id="edit_pl_item_time"><label>Продолжительность</label>
                    <input type="text" name="params[time]" value="<?=$playlist_item['params']['time'];?>">
                </div>
                <div class="width-100"><label title="Для вывода водного знака нужно сконфигурировать свой плеер. Подробнее в справке.">Выводить watermark</label>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="params[show_watermark]" type="radio" value="1"<?php if($playlist_item['params']['show_watermark']) echo ' checked';?>><span>Да</span></label>
                        <label class="custom-radio"><input name="params[show_watermark]" type="radio" value="0"<?php if(!$playlist_item['params']['show_watermark']) echo ' checked';?>><span>Нет</span></label>
                    </span>
                </div>
            </div>

            <div class="col-1-2">
                <div class="width-100" id="edit_pl_item_url"><label>URL</label>
                    <input type="text" name="params[url]" value="<?=$playlist_item['params']['url'];?>">
                </div>
                <div class="width-100" id="adit_pl_item_cover_box"><label>Обложка</label>
                    <input id="edit_pl_item_cover" type="text" name="params[cover]" value="<?=$playlist_item['params']['cover'];?>">
                    <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=1&popup=1&field_id=edit_pl_item_cover&relative_url=0', 'okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Выбрать изображение</a>
                </div>
            </div>
        </div>
    </div>
</form>