<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');
$open_date = time();
$close_date = System::getNextDateInMonth($open_date);?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php');?>

<div class="main">
    <div class="top-wrap">
        <h1>Создать раздел</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/training/">Тренинги</a></li>
        <li><a href="/admin/training/structure/<?=$training_id;?>">Структура</a></li>
        <li>Создать раздел</li>
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
                    <h3 class="mb-0">Создать раздел для <?=$training['name'];?></h3>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="add_section" value="Сохранить" class="button save button-white font-bold"></li>
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
                        <input type="text" name="name" value="" placeholder="Название раздела" required="required">
                    </div>

                    <div class="width-100"><label>Статус</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1" checked><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0"><span>Выкл</span></label>
                        </span>
                    </div>

                    <div class="width-100"><label>Описание раздела</label>
                        <textarea name="section_desc" rows="3" cols="40"></textarea>
                    </div>
                </div>

                <div class="col-1-2">
                    <div class="width-100"><label>Изображение</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="image_type" type="radio" value="1" checked><span>Нумерация</span></label>
                            <label class="custom-radio"><input name="image_type" type="radio" value="2" data-show_on="cover"><span>Свое</span></label>
                        </span>
                    </div>

                    <div class="hidden" id="cover">
                        <div class="width-100" >
                            <input type="file" name="cover">
                        </div>

                        <div class="width-100"><label>Alt обложки</label>
                            <input type="text" size="35" name="img_alt" value="">
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
                            <option value="<?=$curator['user_id']?>"><?=$curator['user_name'] .' '. $curator['surname']?></option>
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
                                <option value="1" data-show_on="shedule">Относительное время</option>
                                <option value="2" data-show_on="open_date">Конкретная дата</option>
                            </select>
                        </div>
                    </div>

                    <div id="shedule" class="width-100 hidden">
                        <div class="width-100" id="shedule_how_fast_open"><label>Как быстро открыть доступ?</label>
                            <div class="select-wrap">
                                <select name="shedule_how_fast_open">
                                    <option value="1" data-show_on="open_wait_days" data-show_off="open_skip_weekdays">Через X дней</option>
                                    <option value="2" data-show_on="open_wait_weekday">Дождаться дня недели</option>
                                </select>
                            </div>
                        </div>

                        <div class="width-100 hidden" id="open_wait_weekday"><label>День недели</label>
                            <div class="select-wrap">
                                <select name="open_wait_weekday">
                                    <option value="1">Понедельник</option>
                                    <option value="2">Вторник</option>
                                    <option value="3">Среда</option>
                                    <option value="4">Четверг</option>
                                    <option value="5">Пятница</option>
                                    <option value="6">Суббота</option>
                                    <option value="7">Воскресенье</option>
                                </select>
                            </div>
                        </div>

                        <div class="width-100 px-label-wrap hidden" id="open_wait_days"><label>Сдвиг открытия<span class="px-label">дн.</span></label>
                            <input type="text" size="35" name="open_wait_days" value="0">
                        </div>

                        <div class="width-100 px-label-wrap" id="open_skip_weekdays"><label>Сдвиг открытия<span class="px-label">нед.</span></label>
                            <input type="text" name="open_skip_weekdays"  value="">
                        </div>
                    </div>

                    <div class="width-100 hidden" id="open_date"><label>Дата открытия раздела</label>
                        <div class="datetimepicker-wrap">
                            <input class="datetimepicker" autocomplete="off" type="text" size="35" name="open_date" value="<?=date('d.m.Y', $open_date);?>">
                        </div>
                    </div>

                    <div class="width-100"><label>Виден ли раздел до даты начала</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="is_show_before_open" type="radio" value="1" checked><span>Да</span></label>
                            <label class="custom-radio"><input name="is_show_before_open" type="radio" value="0"><span>Нет</span></label>
                        </span>
                    </div>
                </div>

                <div class="col-1-2">
                    <div class="width-100"><label>Тип закрытия</label>
                        <div class="select-wrap">
                            <select name="close_type">
                                <option value="1" data-show_on="close_wait_days">Относительное время</option>
                                <option value="2" data-show_on="close_date">Конкретная дата</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100 px-label-wrap hidden" id="close_wait_days"><label>Сдвиг закрытия<span class="px-label">дн.</span></label>
                        <input type="text" size="35" name="close_wait_days" value="0">
                    </div>

                    <div class="width-100 hidden" id="close_date"><label>Дата закрытия раздела</label>
                        <div class="datetimepicker-wrap"><input class="datetimepicker" autocomplete="off" type="text" size="35" name="close_date" value="<?=date('d.m.Y', $close_date);?>"></div>
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
                                <option value="1">Дата старта тренинга</option>
                                <option value="2" data-show_on="start_lessons">Вошёл в урок</option>
                                <option value="3" data-show_on="start_lessons">Прошел урок</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100 hidden mb-20" id="start_lessons"><label>Выбрать урок</label>
                        <select class="multiple-select" name="start_lessons[]" multiple="multiple">
                            <?php if($lesson_list):
                                   foreach($lesson_list as $lesson):?>
                            <option value="<?=$lesson['lesson_id'];?>"><?=$lesson['name'];?></option>
                            <?php endforeach;
                               endif;?>
                        </select>
                    </div>
                </div>

                <div class="col-1-2">
                    <div class="width-100"><label>Что считать окончанием прохождения</label>
                        <div class="select-wrap">
                            <select name="finish_type">
                                <option value="0">Не учитывать прохождение</option>
                                <option value="1">Дата окончания тренинга</option>
                                <option value="2" data-show_on="finish_lessons">Вошёл в урок</option>
                                <option value="3" data-show_on="finish_lessons">Прошел урок</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100 hidden mb-20" id="finish_lessons"><label>Выбрать урок</label>
                        <select class="multiple-select" name="finish_lessons[]" multiple="multiple">
                            <?php if($lesson_list):
                                   foreach($lesson_list as $lesson):?>
                            <option value="<?=$lesson['lesson_id'];?>"><?=$lesson['name'];?></option>
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
                    <div class="width-100"><label>Доступ к урокам раздела</label>
                        <div class="select-wrap">
                            <select name="access_type">
                                <option value="<?=Training::ACCESS_TO_INHERIT;?>" data-show_off="by_button">Наследовать</option>
                                <option value="<?=Training::ACCESS_FREE;?>" data-show_off="by_button">Свободный</option>
                                <option value="<?=Training::ACCESS_TO_SUBS;?>" data-show_on="access_member">По подписке</option>
                                <option value="<?=Training::ACCESS_TO_GROUP;?>" data-show_on="access_group">По группе</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100 hidden" id="access_group"><label>Группа</label>
                        <select class="multiple-select" name="access_groups[]" multiple="multiple">
                            <?php $group_list = User::getUserGroups();
                            $access_groups = json_decode($section['access_groups']);
                            foreach($group_list as $group):?>
                                <option value="<?=$group['group_id'];?>"><?=$group['group_title'];?></option>
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
                                        <option value="<?=$plane['id'];?>"><?=$plane['name'];?></option>
                                    <?php endforeach;
                                endif;?>
                            </select>
                        </div>
                    <?php endif;?>
                </div>
            </div>

            <div class="row-line mt-20" id="by_button">
                <div class="col-1-2">
                    <div class="width-100"><label>Кнопка купить</label>
                        <div class="select-wrap">
                            <select name="by_button[type]">
                                <option value="<?=Training::BY_BUTTON_TYPE_NOT_BUTTON;?>" data-show_off="by_button_text">Нет кнопки</option>
                                <option value="<?=Training::BY_BUTTON_TYPE_PRODUCT_ORDER;?>" data-show_on="by_button_product_order">Заказ продукта</option>
                                <option value="<?=Training::BY_BUTTON_TYPE_RATE;?>" data-show_on="by_button_rate">Выбор тарифа (несколько продуктов)</option>
                                <option value="<?=Training::BY_BUTTON_TYPE_YOUR_URL;?>" data-show_on="by_button_url">Свой Url</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100 mb-20" id="by_button_text"><label>Текст кнопки</label>
                        <input type="text" name="by_button[text]" value="">
                    </div>
                </div>

                <div class="col-1-2">
                    <div class="width-100 hidden" id="by_button_product_order">
                        <label>Выбор продукта</label>
                        <div class="select-wrap">
                            <select name="by_button[product_order]">
                                <option value="0">Не выбран</option>
                                <?php foreach($product_list as $product):?>
                                    <option value="<?=$product['product_id'];?>"><?=$product['product_name'];?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>

                    <div class="width-100 hidden" id="by_button_rate"><label>Выбор тарифа</label>
                        <select name="by_button[rate][]" multiple="multiple">
                            <option value="0">Не выбран</option>
                            <?php foreach($product_list as $product):?>
                                <option value="<?=$product['product_id'];?>"><?=$product['product_name'];?></option>
                            <?php endforeach;?>
                        </select>
                    </div>

                    <div id="by_button_url" class="hidden mb-20">
                        <p><label>Url ссылки</label>
                            <input type="text" name="by_button[your_url]" value="" placeholder="http://">
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
                        <input type="text" name="title" value="">
                    </div>


                    <div class="width-100"><label>Meta Description</label>
                        <textarea name="meta_desc" rows="3" cols="40"></textarea>
                    </div>
                </div>

                <div class="col-1-2">
                    <div class="width-100"><label>Алиас</label>
                        <input type="text" name="alias" value="">
                    </div>

                    <div class="width-100"><label>Meta Keyword</label>
                        <textarea name="meta_keys" rows="3" cols="40"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    </form>

    <?php require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>
</body>
</html>