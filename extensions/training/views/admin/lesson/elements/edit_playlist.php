<?php defined('BILLINGMASTER') or die;
$playlist_items = TrainingLesson::getPlaylistItems($element['id']);?>

<form enctype="multipart/form-data" action="/admin/training/lessons/playlistitem/add" method="POST">
    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    <input type="hidden" name="training_id" value="<?=$lesson['training_id'];?>">
    <input type="hidden" name="lesson_id" value="<?=$lesson['lesson_id'];?>">
    <input type="hidden" name="element_type" value="<?=TrainingLesson::ELEMENT_TYPE_PLAYLIST;?>">
    <input type="hidden" name="playlist_id" value="<?=$element['id'];?>">

    <div class="modal-admin_top">
        <h3 class="modal-traning-title">
            <span class="modal-traning-title-icon">
                <img src="/extensions/training/web/admin/images/icons/pleylist-icon.svg" alt="">
            </span>Редактировать плейлист
        </h3>

        <ul class="modal-nav_button">
            <li><input type="submit" name="edit_element" form="playlist" value="Сохранить" class="button save button-green"></li>
            <li class="modal-nav_button__last">
                <a class="button uk-modal-close uk-close modal-nav_button__close" href="#close"><i class="icon-close"></i></a>
            </li>
        </ul>
    </div>

    <div class="admin_form">
        <div class="row-line">
            <div class="col-1-1 mb-0">
                <h4>Добавить элемент</h4>
            </div>

            <div class="col-1-2">
                <div class="width-100"><label>Тип элемента</label>
                    <div class="select-wrap">
                        <select name="params[type]">
                            <option value="2">Видео</option>
                            <option value="3">Аудио</option>
                            <option value="4" data-show_off="edit_pl_cover_box">Ссылка на видеохостинг</option>
                            <option value="5" data-show_off="edit_pl_url,edit_pl_time">Изображение</option>
							<option value="1">Infoprotector</option>
                        </select>
                    </div>
                </div>

                <div class="width-100"><label>Заголовок</label>
                    <input type="text" name="params[title]" required="required">
                </div>

                <div class="width-100" id="edit_pl_time"><label>Продолжительность</label>
                    <input type="text" name="params[time]">
                </div>
            </div>

            <div class="col-1-2">
                <div class="width-100" id="edit_pl_url"><label>URL</label>
                    <input type="text" name="params[url]">
                    <a id="edit_playlist_item_file" class="hidden" href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=1&popup=1&field_id=add_playlist_item_url&relative_url=0', 'okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Выбрать файл на сервере</a>
                </div>

                <div class="width-100" id="edit_pl_cover_box"><label>Обложка</label>
                    <input id="edit_pl_cover" type="text" name="params[cover]">
                    <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=1&popup=1&field_id=edit_pl_cover&relative_url=0', 'okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Выбрать изображение</a>
                </div>
            </div>
        </div>

        <div class="row-line mt-20 add-playlist-line">
            <div class="col-1-2">
                <div class="width-100"><label title="Для вывода водного знака нужно сконфигурировать свой плеер. Подробнее в справке.">Выводить watermark</label>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="params[show_watermark]" type="radio" value="1"><span>Да</span></label>
                        <label class="custom-radio"><input name="params[show_watermark]" type="radio" value="0" checked><span>Нет</span></label>
                    </span>
                </div>
            </div>

            <div class="col-1-2">
                <div class="width-100 text-right">
                    <input type="submit" name="add_playlist_item" value="Добавить элемент" class="button save button-green">
                </div>
            </div>
        </div>

        <div class="row-line">
            <div class="col-1-1 mb-0">
                <h4>Настройки плейлиста</h4>
            </div>

            <div class="col-1-2">
                <div class="width-100"><label>Заголовок</label>
                    <input placeholder="Заголовок плейлиста в уроке" type="text" name="params[title]" form="playlist" value="<?=$element['params']['title'];?>" required="required">
                </div>
            </div>

            <div class="col-1-2">
                <div class="width-100"><label>Служебное название</label>
                    <input placeholder="Название плейлиста в списке" type="text" name="params[name]" form="playlist" value="<?=$element['params']['name'];?>" required="required">
                </div>
            </div>
        </div>

        <div class="row-line table-playlist-elem">
            <div class="col-1-1">
                <div class="width-100">
                    <?php $playlist_items = TrainingLesson::getPlaylistItems($element['id']);
                    if($playlist_items):?>
                        <table class="table">
                            <tbody class="sortable sortable_box">
                                <input type="hidden" name="sort_upd_url" value="/admin/trainingajax/updsortplaylist">

                                <?php foreach($playlist_items as $playlist_item):?>
                                    <tr>
                                        <td class="text-center">
                                            <i class="button-drag button-drag-play-icon">
                                                <img src="/extensions/training/web/admin/images/icons/video.svg" alt="">
                                            </i>
                                            <input type="hidden" name="sort_items[]" value="<?=$playlist_item['id'];?>" data-type="pl">
                                        </td>

                                        <td class="text-left">
                                            <?=$playlist_item['id'];?>
                                        </td>

                                        <td class="text-left">
                                            <a href="javascript:void(0)" class="pl-item-edit playlist_tem" data-url="/admin/trainingajax/playlistitemform?id=<?="{$playlist_item['id']};&token={$_SESSION['admin_token']}";?>"><?=$playlist_item['params']['title'];?></a>
                                        </td>

                                        <td class="text-left"><?=$playlist_item['params']['time'];?></td>

                                        <td class="td-last">
                                            <a onclick="return confirm('Вы уверены?')" href="/admin/training/lessons/playlistitem/del/<?="{$playlist_item['id']}?token={$_SESSION['admin_token']}";?>" title="Удалить"><span class="icon-remove play-remove"></span></a>
                                        </td>
                                    </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    <?php else:?>
                        <p>Нет добавленных элементов.</p><p><br /></p>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</form>

<form enctype="multipart/form-data" id="playlist" action="/admin/training/lessons/element/edit/<?=$element['id'];?>" method="POST">
    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    <input type="hidden" name="training_id" value="<?=$lesson['training_id'];?>">
</form>