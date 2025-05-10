<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php');?>

<div class="main">
    <div class="top-wrap">
        <h1>Настройка раздела</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/training/">Тренинги</a></li>
        <li><a href="/admin/training/structure/<?=$training_id;?>">Структура</a></li>
        <li>Редактировать раздел</li>
    </ul>

    <form action="" method="POST" enctype="multipart/form-data">
        <?php if(isset($_GET['success'])):?>
            <div class="admin_message">Успешно!</div>
        <?php endif;?>

        <div class="admin_top admin_top-flex align-center">
            <div class="admin_top-inner">
                <div>
                    <img src="/extensions/training/web/admin/images/icons/folder.svg" alt="">
                </div>
                <div>
                    <h3 class="mb-0">Редактировать раздел <?=$section['name'];?></h3>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="edit_section" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last">
                    <a class="button red-link" href="/admin/training/structure/<?=$training_id;?>">Закрыть</a>
                </li>
            </ul>
        </div>
        
        <div class="admin_form">
            <!-- 1 вкладка -->

            <div class="row-line">
                <div class="col-1-1 mb-0">
                    <h4>Общие настройки</h4>
                </div>

                <div class="col-1-2">
                    <div class="width-100"><label>Название</label>
                        <input type="text" name="name" value="<?=$section['name'];?>" placeholder="Название раздела" required="required">
                    </div>

                    <div class="width-100"><label>Статус</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1"<?php if($section['status']) echo ' checked';?>><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0"<?php if(!$section['status']) echo ' checked';?>><span>Выкл</span></label>
                        </span>
                    </div>

                    <div class="width-100"><label>Описание</label>
                        <textarea name="section_desc" rows="3" cols="40"><?=$section['section_desc'];?></textarea>
                    </div>
                </div>

                <div class="col-1-2">
                    <div class="width-100"><label>Изображение</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="image_type" type="radio" value="1"<?php if($section['image_type'] == 1) echo ' checked';?>><span>Нумерация</span></label>
                            <label class="custom-radio"><input name="image_type" type="radio" value="2"<?php if($section['image_type'] == 2) echo ' checked';?> data-show_on="cover"><span>Свое</span></label>
                        </span>
                    </div>

                    <div class="hidden" id="cover">
                        <div class="width-100" >
                            <input type="file" name="cover">
                            <input type="hidden" value="<?=$section['cover'];?>" name="current_img"/>
                        </div>

                        <?php if($section['cover']):?>
                            <div class="width-100 del_img_wrap">
                                <img src="/images/training/sections/<?=$section['cover']?>" alt="" width="210">
                                <span class="del_img_link">
                                    <button type="submit" form="del_img" value="" title="Удалить изображение с сервера?" name="del_img">
                                        <span class="icon-remove"></span>
                                    </button>
                                </span>
                            </div>
                        <?php endif;?>

                        <div class="width-100"><label>Alt обложки</label>
                            <input type="text" size="35" name="img_alt" value="<?=$section['img_alt'];?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row-line">
                <div class="col-1-1 mb-0">
                    <h4>Настройки раздела</h4>
                </div>

                <div class="col-1-2">
                    <div class="width-100"><label>Кураторы</label>
                        <select size="6" class="multiple-select" name="curators[]" multiple="multiple">
                            <?php if($curators):
                                   foreach($curators as $curator):?>
                            <option value="<?=$curator['user_id']?>"<?php if($section_curators['datacurators'] && in_array($curator['user_id'], $section_curators['datacurators'])) echo ' selected="selected"';?>><?=$curator['user_name'] .' '. $curator['surname']?></option>
                            <?php endforeach;
                               endif;?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row-line">
                <div class="col-1-2">
                    <div class="width-100"><label>Тип открытия</label>
                        <div class="select-wrap">
                            <select name="open_type">
                                <option value="1"<?php if($section['open_type'] == 1) echo ' selected="selected"';?> data-show_on="shedule">Относительное время</option>
                                <option value="2"<?php if($section['open_type'] == 2) echo ' selected="selected"';?> data-show_on="open_date">Конкретная дата</option>
                            </select>
                        </div>
                    </div>

                    <div id="shedule" class="width-100 hidden">
                        <div class="width-100" id="shedule_how_fast_open"><label>Как быстро открыть доступ?</label>
                            <div class="select-wrap">
                                <select name="shedule_how_fast_open">
                                    <option value="1"<?php if($section['shedule_how_fast_open'] == 1) echo ' selected="selected"';?> data-show_on="open_wait_days" data-show_off="open_skip_weekdays">Через X дней</option>
                                    <option value="2"<?php if($section['shedule_how_fast_open'] == 2) echo ' selected="selected"';?> data-show_on="open_wait_weekday">Дождаться дня недели</option>
                                </select>
                            </div>
                        </div>

                        <div class="width-100 hidden" id="open_wait_weekday"><label>День недели</label>
                            <div class="select-wrap">
                                <select name="open_wait_weekday">
                                    <option value="1"<?php if($section['open_wait_weekday'] == 1) echo ' selected="selected"';?>>Понедельник</option>
                                    <option value="2"<?php if($section['open_wait_weekday'] == 2) echo ' selected="selected"';?>>Вторник</option>
                                    <option value="3"<?php if($section['open_wait_weekday'] == 3) echo ' selected="selected"';?>>Среда</option>
                                    <option value="4"<?php if($section['open_wait_weekday'] == 4) echo ' selected="selected"';?>>Четверг</option>
                                    <option value="5"<?php if($section['open_wait_weekday'] == 5) echo ' selected="selected"';?>>Пятница</option>
                                    <option value="6"<?php if($section['open_wait_weekday'] == 6) echo ' selected="selected"';?>>Суббота</option>
                                    <option value="7"<?php if($section['open_wait_weekday'] == 7) echo ' selected="selected"';?>>Воскресенье</option>
                                </select>
                            </div>
                        </div>

                        <div class="width-100 px-label-wrap hidden" id="open_wait_days"><label>Сдвиг открытия<span class="px-label">дн.</span></label>
                            <input type="text" size="35" name="open_wait_days" value="<?=$section['open_wait_days'];?>">
                        </div>

                        <div class="width-100 px-label-wrap" id="open_skip_weekdays"><label>Сдвиг открытия<span class="px-label">нед.</span></label>
                            <input type="text" name="open_skip_weekdays"  value="<?=$section['open_skip_weekdays'];?>">
                        </div>
                    </div>

                    <div class="width-100 hidden" id="open_date"><label>Дата открытия раздела</label>
                        <div class="datetimepicker-wrap">
                            <input class="datetimepicker" autocomplete="off" type="text" size="35" name="open_date" value="<?=$section['open_date'] ? date('d.m.Y', $section['open_date']) : '';?>">
                        </div>
                    </div>

                    <div class="width-100"><label>Виден ли раздел до даты открытия</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="is_show_before_open" type="radio" value="1"<?php if($section['is_show_before_open']) echo ' checked';?>><span>Да</span></label>
                            <label class="custom-radio"><input name="is_show_before_open" type="radio" value="0"<?php if(!$section['is_show_before_open']) echo ' checked';?>><span>Нет</span></label>
                        </span>
                    </div>
                </div>

                <div class="col-1-2">
                    <div class="width-100"><label>Тип закрытия</label>
                        <div class="select-wrap">
                            <select name="close_type">
                                <option value="1"<?php if($section['close_type'] == 1) echo ' selected="selected"';?> data-show_on="close_wait_days">Относительное время</option>
                                <option value="2"<?php if($section['close_type'] == 2) echo ' selected="selected"';?> data-show_on="close_date">Конкретная дата</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100 px-label-wrap hidden" id="close_wait_days"><label>Сдвиг закрытия<span class="px-label">дн.</span></label>
                        <input type="text" size="35" name="close_wait_days" value="<?=$section['close_wait_days'];?>">
                    </div>

                    <div class="width-100 hidden" id="close_date"><label>Дата закрытия раздела</label>
                        <div class="datetimepicker-wrap"><input class="datetimepicker" autocomplete="off" type="text" size="35" name="close_date" value="<?=$section['close_date'] ? date('d.m.Y', $section['close_date']) : '';?>"></div>
                    </div>
                </div>
            </div>

            <div class="row-line">
                <div class="col-1-1">
                    <h4 class="mb-0">Для статистики</h4>
                </div>
                <div class="col-1-2">
                    <div class="width-100"><label>Что считать началом прохождения</label>
                        <div class="select-wrap">
                            <select name="start_type">
                                <option value="1"<?php if($section['start_type'] == 1) echo ' selected="selected"';?>>Дата старта тренинга</option>
                                <option value="2"<?php if($section['start_type'] == 2) echo ' selected="selected"';?> data-show_on="start_lessons">Вошёл в урок</option>
                                <option value="3"<?php if($section['start_type'] == 3) echo ' selected="selected"';?> data-show_on="start_lessons">Прошел урок</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100 mb-20 hidden" id="start_lessons"><label>Выбрать урок</label>
                        <select class="multiple-select" name="start_lessons[]" multiple="multiple">
                            <?php if($lesson_list):
                                $start_lessons = json_decode($section['start_lessons'], true);
                                foreach($lesson_list as $lesson):?>
                                    <option value="<?=$lesson['lesson_id'];?>"<?php if($start_lessons && in_array($lesson['lesson_id'], $start_lessons)) echo ' selected="selected"';?>><?=$lesson['name'];?></option>
                                <?php endforeach;
                            endif;?>
                        </select>
                    </div>
                </div>

                <div class="col-1-2">
                    <div class="width-100"><label>Что считать окончанием прохождения</label>
                        <div class="select-wrap">
                            <select name="finish_type">
                                <option value="0"<?php if($section['finish_type'] == 0) echo ' selected="selected"';?>>Не учитывать прохождение</option>
                                <option value="1"<?php if($section['finish_type'] == 1) echo ' selected="selected"';?>>Дата окончания тренинга</option>
                                <option value="2"<?php if($section['finish_type'] == 2) echo ' selected="selected"';?> data-show_on="finish_lessons">Вошёл в урок</option>
                                <option value="3"<?php if($section['finish_type'] == 3) echo ' selected="selected"';?> data-show_on="finish_lessons">Прошел урок</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100 mb-20 hidden" id="finish_lessons"><label>Выбрать урок</label>
                        <select class="multiple-select" name="finish_lessons[]" multiple="multiple">
                            <?php if($lesson_list):
                                $finish_lessons = json_decode($section['finish_lessons'], true);
                                foreach($lesson_list as $lesson):?>
                                    <option value="<?=$lesson['lesson_id'];?>"<?php if($finish_lessons && in_array($lesson['lesson_id'], $finish_lessons)) echo ' selected="selected"';?>><?=$lesson['name'];?></option>
                                <?php endforeach;
                            endif;?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row-line mt-10">
                <div class="col-1-1 mb-0">
                    <h4>Настройки доступа</h4>
                </div>

                <div class="col-1-2">
                    <div class="width-100 mb-20"><label>Доступ к урокам раздела</label>
                        <div class="select-wrap">
                            <select name="access_type">
                                <option value="<?=Training::ACCESS_TO_INHERIT;?>"<?php if($section['access_type'] == Training::ACCESS_TO_INHERIT) echo ' selected="selected"';?> data-show_off="by_button">Наследовать</option>
                                <option value="<?=Training::ACCESS_FREE;?>"<?php if($section['access_type'] == Training::ACCESS_FREE) echo ' selected="selected"';?> data-show_off="by_button">Свободный</option>
                                <option value="<?=Training::ACCESS_TO_SUBS;?>"<?php if($section['access_type'] == Training::ACCESS_TO_SUBS) echo ' selected="selected"';?> data-show_on="access_member">По подписке</option>
                                <option value="<?=Training::ACCESS_TO_GROUP;?>"<?php if($section['access_type'] == Training::ACCESS_TO_GROUP) echo ' selected="selected"';?> data-show_on="access_group">По группе</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-1-2">
                    <div class="width-100 hidden" id="access_group"><label>Группа</label>
                        <select class="multiple-select" name="access_groups[]" multiple="multiple">
                            <?php $group_list = User::getUserGroups();
                            $access_groups = json_decode($section['access_groups']);
                            foreach($group_list as $group):?>
                                <option value="<?=$group['group_id'];?>"<?php if($access_groups && in_array($group['group_id'], $access_groups)) echo ' selected="selected"';?>><?=$group['group_title'];?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if($membership):?>
                        <div class="width-100 hidden" id="access_member"><label>Подписка</label>
                            <select class="multiple-select" name="access_planes[]" multiple="multiple">
                                <?php $planes = Member::getPlanes();
                                if ($planes):
                                    $access_planes = json_decode($section['access_planes']);
                                    foreach($planes as $plane):?>
                                        <option value="<?=$plane['id'];?>"<?php if($access_planes && in_array($plane['id'], $access_planes)) echo ' selected="selected"';?>>
                                            <?=$plane['name'];?>
                                        </option>
                                    <?php endforeach;
                                endif;?>
                            </select>
                        </div>
                    <?php endif;?>
                </div>
            </div>

            <div class="row-line" id="by_button">
                <div class="col-1-2">
                    <div class="width-100"><label>Кнопка купить</label>
                        <div class="select-wrap">
                            <select name="by_button[type]">
                                <option value="<?=Training::BY_BUTTON_TYPE_NOT_BUTTON;?>"<?php if($by_button['type'] == Training::BY_BUTTON_TYPE_NOT_BUTTON) echo ' selected="selected"';?> data-show_off="by_button_text">Нет кнопки</option>
                                <option value="<?=Training::BY_BUTTON_TYPE_PRODUCT_ORDER;?>" data-show_on="by_button_product_order"<?php if($by_button['type'] == Training::BY_BUTTON_TYPE_PRODUCT_ORDER) echo ' selected="selected"';?>>Заказ продукта</option>
                                <option value="<?=Training::BY_BUTTON_TYPE_RATE;?>" data-show_on="by_button_rate"<?php if($by_button['type'] == Training::BY_BUTTON_TYPE_RATE) echo ' selected="selected"';?>>Выбор тарифа (несколько продуктов)</option>
                                <option value="<?=Training::BY_BUTTON_TYPE_YOUR_URL;?>" data-show_on="by_button_url"<?php if($by_button['type'] == Training::BY_BUTTON_TYPE_YOUR_URL) echo ' selected="selected"';?>>Свой Url</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100 mb-20" id="by_button_text"><label>Текст кнопки</label>
                        <input type="text" name="by_button[text]" value="<?=isset($by_button['text']) ? $by_button['text'] : '';?>">
                    </div>
                </div>

                <div class="col-1-2">
                    <div class="width-100 hidden" id="by_button_product_order">
                        <label>Выбор продукта</label>
                        <div class="select-wrap">
                            <select name="by_button[product_order]">
                                <option value="0">Не выбран</option>
                                <?php foreach($product_list as $product):?>
                                    <option value="<?=$product['product_id'];?>"<?php if(isset($by_button['product_order']) && $by_button['product_order'] == $product['product_id']) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>

                    <div class="width-100 hidden" id="by_button_rate"><label>Выбор тарифа</label>
                        <select name="by_button[rate][]" multiple="multiple" class="multiple-select">
                            <option value="0">Не выбран</option>
                            <?php foreach($product_list as $product):?>
                                <option value="<?=$product['product_id'];?>"<?php if(isset($by_button['rate']) && in_array($product['product_id'], $by_button['rate'])) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                            <?php endforeach;?>
                        </select>
                    </div>

                    <div id="by_button_url" class="hidden mb-20">
                        <p><label>Url ссылки</label>
                            <input type="text" name="by_button[your_url]" value="<?php if(isset($by_button['your_url'])) echo  $by_button['your_url']?>" placeholder="http://">
                        </p>
                    </div>
                </div>
            </div>

            <div class="row-line mt-10">
                <div class="col-1-1 mb-0">
                    <h4>Настройки для SEO</h4>
                </div>

                <div class="col-1-2">
                    <div class="width-100"><label>Title</label>
                        <input type="text" name="title" value="<?=$section['title'];?>">
                    </div>


                    <div class="width-100"><label>Meta Description</label>
                        <textarea name="meta_desc" rows="3" cols="40"><?=$section['meta_desc'];?></textarea>
                    </div>
                </div>

                <div class="col-1-2">
                    <div class="width-100"><label>Алиас</label>
                        <input type="text" name="alias" value="<?=$section['alias'];?>">
                    </div>

                    <div class="width-100"><label>Meta Keyword</label>
                        <textarea name="meta_keys" rows="3" cols="40"><?=$section['meta_keys'];?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    </form>

    <form action="/admin/delimg/<?=$section['section_id'];?>" id="del_img" method="POST">
        <input type="hidden" name="path" value="images/training/sections/<?=$section['cover'];?>">
        <input type="hidden" name="page" value="admin/training/editsection/<?="{$section['training_id']}/{$section['section_id']}";?>">
        <input type="hidden" name="table" value="training_sections">
        <input type="hidden" name="name" value="cover">
        <input type="hidden" name="where" value="section_id">
        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    </form>

    <div class="buttons-under-form">
        <p class="button-delete">
            <a onclick="return confirm('Вы уверены?')" href="/admin/training/delsection/<?=$section['training_id'];?>/<?=$section['section_id'];?>?token=<?=$_SESSION['admin_token'];?>"><i class="icon-remove"></i>Удалить раздел</a>
        </p>
    </div>

    <?php require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>
</body>
</html>