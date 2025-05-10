<?php defined('BILLINGMASTER') or die;?>

<div id="modal_playlist" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-add-elem">
        <div class="userbox modal-userbox-3">
            <form  enctype="multipart/form-data" action="/admin/training/lessons/element/add" method="POST">
                <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                <input type="hidden" name="training_id" value="<?=$training_id;?>">
                <input type="hidden" name="lesson_id" value="<?=$lesson_id;?>">
                <input type="hidden" name="element_type" value="<?=TrainingLesson::ELEMENT_TYPE_PLAYLIST;?>">

                <div class="modal-admin_top">
                    <h3 class="modal-traning-title"><span class="modal-traning-title-icon"><img src="/extensions/training/web/admin/images/icons/pleylist-icon.svg" alt=""></span>Добавить плейлист</h3>
                    <ul class="modal-nav_button">
                        <li class="modal-nav_button__last">
                            <a class="button uk-modal-close uk-close modal-nav_button__close" href="#close"><i class="icon-close"></i></a>
                        </li>
                    </ul>
                </div>

                <div class="admin_form">
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Настройки плейлиста</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Заголовок</label>
                                <input type="text" name="title" placeholder="Заголовок плейлиста в уроке" required="required">
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Служебное название</label>
                                <input type="text" name="name" placeholder="Название плейлиста в списке" required="required">
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Добавить элемент</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Тип элемента:</label>
                                <div class="select-wrap">
                                    <select name="params[type]">
                                        <option value="2">Видео</option>
                                        <option value="3">Аудио</option>
                                        <option value="4" data-show_off="add_pl_item_cover_box">Ссылка на видеохостинг</option>
                                        <option value="5" data-show_off="add_pl_item_url,add_pl_item_time">Изображение</option>
                                        <option value="1">Infoprotector</option>
                                    </select>
                                </div>
                            </div>
                            <div class="width-100"><label>Заголовок</label>
                                <input type="text" name="params[title]" placeholder="" required="required">
                            </div>
                            <div class="width-100" id="add_pl_item_time"><label>Продолжительность</label>
                                <input type="text" name="params[time]" placeholder="">
                            </div>
                        </div>
                        <div class="col-1-2">
                            <div class="width-100" id="add_pl_item_url"><label>URL</label>
                                <input type="text" name="params[url]" placeholder="">
                            </div>
                            <div class="width-100" id="add_pl_item_cover_box"><label>Обложка</label>
                                <input id="add_pl_item_cover" type="text" name="params[cover]" placeholder="">
                                <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=1&popup=1&field_id=add_pl_item_cover&relative_url=0', 'okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Выбрать изображение</a>
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
                                <input type="submit" name="add_element" value="Добавить элемент" class="button save button-green">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>