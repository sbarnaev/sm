<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php');
$membership = System::CheckExtensension('membership', 1);
$group_list = User::getUserGroups();
$planes = Member::getPlanes();
$product_list = Product::getProductListOnlySelect();?>

<div class="main">
    <div class="top-wrap">
        <h1>Добавить новый тренинг</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a>
            <a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/training/">Тренинги</a></li>
        <li>Добавить новый тренинг</li>
    </ul>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="traning-top">
            <h3 class="traning-title">Добавить новый тренинг</h3>
            <ul class="nav_button">
                <li><input type="submit" name="add_training" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/training/">Закрыть</a></li>
            </ul>
        </div>

        <div class="tabs">
            <ul>
                <li>Основное</li>
                <li>Доступ</li>
                <li>Кнопки</li>
                <li>Внешний вид</li>
                <? /* <li>Защита</li> */ ?>
                <li>SEO</li>
            </ul>

            <div class="admin_form">
                <!-- 1 ВКЛАДКА ОСНОВНОЕ-->
                <div>
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Основное</h4>
                        </div>
                        <div class="col-1-2">
                            <p class="width-100"><label>Название</label>
                                <input type="text" name="name" placeholder="Название тренинга" required="required">
                            </p>

                            <div class="width-100"><label>Статус</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="status" type="radio" value="1" checked><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="status" type="radio" value="0"><span>Выкл</span></label>
                                </span>
                            </div>

                            <div class="width-100"><label>Категория в списке тренингов</label>
                                <div class="select-wrap">
                                    <select name="cat_id">
                                        <option value="0">Без категории</option>
                                        <?php $cat_list = TrainingCategory::getCatList();
                                        if($cat_list):
                                            foreach($cat_list as $cat):?>
                                        <option value="<?=$cat['cat_id'];?>"><?=$cat['name'];?></option>
                                        <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100"><label>Показывать в списке тренингов</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_in_main" type="radio" value="1" checked><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="show_in_main" type="radio" value="0"><span>Выкл</span></label>
                                </span>
                            </div>

                            <? /*
                            <div class="width-100"><label>Разрешить пользовательские заметки</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="allow_user_notes" type="radio" value="1" checked><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="allow_user_notes" type="radio" value="0"><span>Выкл</span></label>
                                </span>
                            </div>
                        */ ?>

                        </div>
                        <div class="col-1-2">

                            <div class="width-100"><label>Платный тренинг</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="is_free" type="radio" value="0" checked><span>Да</span></label>
                                    <label class="custom-radio"><input name="is_free" type="radio" value="1"><span>Нет</span></label>
                                </span>
                            </div>

                            <input type="hidden" value="0" name="count_free_lessons">
                        </div>
                    </div>

                    <div class="row-line">

                        <div class="col-1-1 mb-0">
                            <h4>Авторы</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100" id="authors"><label>Выберите авторов</label>
                                <?php $authors = User::getAuthors();?>
                                <select size="6" name="authors[]" multiple="multiple" class="multiple-select">
                                    <?php if($authors):
                                        foreach($authors as $author):?>
                                            <option value="<?=$author['user_id'];?>"><?=$author['user_name'];?></option>
                                        <?php endforeach;
                                    endif; ?>
                                </select>
                            </div>
                        </div>
                        <? /*
                        <div class="col-1-2">
                            <div class="width-100"><label>Авторы могут править тренинг</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="authors_can_edit" type="radio" value="1"><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="authors_can_edit" type="radio" value="0" checked><span>Выкл</span></label>
                                </span>
                            </div>
                        </div>
                */ ?>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Кураторы</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Мастер-кураторы</label>
                                <select size="6" class="multiple-select" name="mastercurators[]" multiple="multiple" onChange="getSelectedOptions(this)">
                                    <?php $curators = User::getCurators();
                                    foreach($curators as $curator):?>
                                        <option value="<?=$curator['user_id']?>"><?=$curator['user_name'] .' '. $curator['surname']?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                        <div class="col-1-2">
                            <div class="width-100"><label>Включить автоматическое распределение учеников</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="curators_auto_assign" type="radio" value="1" data-show_on="curators-to-assign"><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="curators_auto_assign" type="radio" value="0" checked><span>Выкл</span></label>
                                </span>
                            </div>
                        </div>
                        <div class="col-1-2">
                            <div class="width-100"><label>Кураторы</label>
                                <select size="6" class="multiple-select" name="curators[]" multiple="multiple">
                                    <?php $curators = User::getCurators();
                                    foreach($curators as $curator):?>
                                        <option value="<?=$curator['user_id']?>"><?=$curator['user_name'] .' '. $curator['surname']?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                        <? /*
                        <div class="col-1-2">
                            <div class="width-100"><label>Кураторы могут править тренинг</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="curators_can_edit" type="radio" value="1"><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="curators_can_edit" type="radio" value="0" checked><span>Выкл</span></label>
                                </span>
                            </div>
                        </div>
                        */ ?>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Краткое описание тренинга (в списке тренингов)</h4>
                        </div>

                        <div class="col-1-2">
                            <p class="width-100"><label>Обложка</label>
                                <input type="file" name="cover">
                            </p>

                            <p class="width-100"><label>Alt обложки</label>
                                <input type="text" size="35" name="img_alt" placeholder="Альтернативный текст">
                            </p>

                            <p class="width-100"><label title="В любых CSS величинах: px, em">Отступы обложки</label>
                                <input type="text" size="35" name="padding" placeholder="Отступы обложки" name="padding" placeholder="Для примера: 20px 20px 20px 20px">
                            </p>
                        </div>

                        <div class="col-1-1">
                            <div class="width-100" title="Описание для списка тренингов"><label>Описание</label>
                                <textarea class="editorsmall" name="short_desc"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Полное описание тренинга (внутри тренинга)</h4>
                        </div>
                        <div class="col-1-2">
                            <div class="row-line">
                                <div class="col-55">
                                    <div class="overlay-wrap"><label>Оверлей</label><div class="overlay-inner"><input type="color" name="full_cover_param[overlaycolor]" value="<?php if(isset($full_cover_param['overlaycolor'])) echo $full_cover_param['overlaycolor']; else echo '#000000'?>">выбрать</div></div>
                                </div>
                                <div class="col-45">
                                    <div class="width-100"><label>Прозрачность</label>
                                        <div class="select-wrap">
                                            <select name="full_cover_param[overlay]">
                                                <option value="1.0">1.0</option>
                                                <option value="0.9">0.9</option>
                                                <option value="0.8">0.8</option>
                                                <option value="0.7">0.7</option>
                                                <option value="0.6">0.6</option>
                                                <option value="0.5">0.5</option>
                                                <option value="0.4">0.4</option>
                                                <option value="0.3">0.3</option>
                                                <option value="0.2">0.2</option>
                                                <option value="0.1">0.1</option>
                                                <option value="0.0" selected="selected">0.0</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="width-100 mt-20"><label>Позиция фона</label>
                                <div class="select-wrap">
                                    <select name="full_cover_param[position]">
                                        <option value="center center">По центру</option>
                                        <option value="center top">Сверху и по центру </option>
                                    </select>
                                </div>
                            </div>

                            <p class="px-label-wrap"><label>Высота блока<span class="px-label">px</span></label>
                                <input type="text" name="full_cover_param[heroheigh]" value="390">
                            </p>

                            <p class="px-label-wrap"><label>Высота блока на мобильных<span class="px-label">px</span></label>
                                <input type="text" name="full_cover_param[heromobileheigh]" value="">
                            </p>
                        </div>
                        <div class="col-1-2">
                            <p class="width-100"><label>Обложка внутри тренинга</label>
                                <input type="file" name="full_cover">
                            </p>
                        </div>
                        

                        <div class="col-1-1">
                            <div class="width-100" title="Описание для списка тренингов"><label>Описание</label>
                                <textarea class="editor"  name="full_desc"></textarea>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- 2 ВКЛАДКА ДОСТУП -->
                <div>
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Настройка доступа к тренингу</h4>
                        </div>

                        <div class="col-1-2">
                            <div class=""><label>Тип доступа к тренингу</label>
                                <div class="select-wrap">
                                    <select name="access_type">
                                        <option value="1" data-show_on="access_group">Группа</option>
                                        <?php if($membership):?>
                                            <option value="<?=Training::ACCESS_TO_SUBS;?>" data-show_on="access_member">Подписка</option>
                                        <?php endif;?>
                                        <option value="<?=Training::ACCESS_FREE;?>" selected="selected">Свободный</option>
                                    </select>
                                </div>
                            </div>

                            <div class=" mt-20 hidden" id="access_group"><label>Группа доступа к тренингу</label>
                                <select class="multiple-select" name="access_groups[]" multiple="multiple">
                                    <?php foreach($group_list as $group):?>
                                        <option value="<?=$group['group_id'];?>"><?=$group['group_title'];?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if($membership):?>
                                <div class="width-100 mt-20 hidden"  id="access_member"><label>Подписка для доступа к тренингу</label>
                                    <select class="multiple-select" name="access_planes[]" multiple="multiple">
                                        <?php foreach($planes as $plane):?>
                                            <option value="<?=$plane['id'];?>"><?=$plane['name'];?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif;?>
                        </div>

                        <? /*
                        <div class="col-1-2">
                            <p><label>Показывать в кабинете клиента, если нет доступа</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_in_lk2not_access" type="radio" value="1"><span>Показывать</span></label>
                                    <label class="custom-radio"><input name="show_in_lk2not_access" type="radio" value="0" checked><span>Нет</span></label>
                                </span>
                            </p>
                        </div>
                        */ ?>

                    </div>

                    <div class="row-line mt-20">
                        <div class="col-1-2">
                            <div class="width-100"><label>Дата старта тренинга</label>
                                <div class="datetimepicker-wrap"><input placeholder="выберите дату" type="text" autocomplete="off" class="datetimepicker" name="start_date"></div>
                            </div>
                        </div>
                        <div class="col-1-2">
                            <div class="width-100"><label>Дата окончания тренинга</label>
                                <div class="datetimepicker-wrap"><input type="text" placeholder="выберите дату" autocomplete="off" class="datetimepicker" name="end_date"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Настройка доступа к домашнему заданию</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Тип доступа при проверке куратором</label>
                                <div class="select-wrap">
                                    <select name="access_task_type_curator">
                                        <option value="1" data-show_on="access_group2task_for_curator">Группа</option>
                                        <?php if($membership):?>
                                            <option value="<?=Training::ACCESS_TO_SUBS;?>" data-show_on="access_member2task_for_curator">Подписка</option>
                                        <?php endif;?>
                                        <option value="<?=Training::ACCESS_FREE;?>" selected="selected" data-show_off="group_auto_self">Свободный</option>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100 hidden" id="access_group2task_for_curator"><label>Группа доступа к домашним заданиям</label>
                                <select class="multiple-select" name="access_task_groups_for_curator[]" multiple="multiple">
                                <?php foreach($group_list as $group):?>
                                        <option value="<?=$group['group_id'];?>"><?=$group['group_title'];?></option>
                                <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if($membership):?>
                                <div class="width-100 hidden" id="access_member2task_for_curator"><label>Подписка для доступа к домашним заданиям</label>
                                    <select class="multiple-select" name="access_task_planes_for_curator[]" multiple="multiple">
                                        <?php foreach($planes as $plane):?>
                                            <option value="<?=$plane['id'];?>"><?=!empty($plane['service_name']) ? $plane['service_name'] : $plane['name'];?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif;?>

                            <div class="width-100 hidden" id="group_auto_self">
                                <div class="width-100"><label>Тип доступа при автоматической проверке</label>
                                    <div class="select-wrap">
                                        <select name="access_task_type_automat">
                                            <option value="1" data-show_on="access_group2task_for_automat">Группа</option>
                                            <?php if($membership):?>
                                                <option value="<?=Training::ACCESS_TO_SUBS;?>" data-show_on="access_member2task_for_automat">Подписка</option>
                                            <?php endif;?>
                                            <option value="<?=Training::ACCESS_FREE;?>" selected="selected" data-show_off="group_self">Свободный</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="width-100 hidden" id="access_group2task_for_automat"><label>Группа доступа к домашним заданиям</label>
                                    <select class="multiple-select" name="access_task_groups_for_automat[]" multiple="multiple">
                                        <?php foreach($group_list as $group):?>
                                            <option value="<?=$group['group_id'];?>"><?=$group['group_title'];?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <?php if($membership):?>
                                    <div class="width-100 hidden" id="access_member2task_for_automat"><label>Подписка для доступа к домашним заданиям</label>
                                        <select class="multiple-select" name="access_task_planes_for_automat[]" multiple="multiple">
                                            <?php foreach($planes as $plane):?>
                                                <option value="<?=$plane['id'];?>"><?=!empty($plane['service_name']) ? $plane['service_name'] : $plane['name'];?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif;?>


                                <div id="group_self" class="mb-20">
                                    <div class="width-100"><label>Тип доступа при самостоятельной проверке</label>
                                        <div class="select-wrap">
                                            <select name="access_task_type_bezproverki">
                                                <option value="1" data-show_on="access_group2task_for_bezproverki">Группа</option>
                                                <?php if($membership):?>
                                                    <option value="<?=Training::ACCESS_TO_SUBS;?>" data-show_on="access_member2task_for_bezproverki">Подписка</option>
                                                <?php endif;?>
                                                <option value="<?=Training::ACCESS_FREE;?>" selected="selected">Свободный</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="width-100 hidden" id="access_group2task_for_bezproverki"><label>Группа доступа к домашним заданиям</label>
                                        <select class="multiple-select" name="access_task_groups_for_bezproverki[]" multiple="multiple">
                                            <?php foreach($group_list as $group):?>
                                                <option value="<?=$group['group_id'];?>"><?=$group['group_title'];?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <?php if($membership):?>
                                        <div class="width-100 hidden" id="access_member2task_for_bezproverki"><label>Подписка для доступа к домашним заданиям</label>
                                            <select class="multiple-select" name="access_task_planes_for_bezproverki[]" multiple="multiple">
                                                <?php foreach($planes as $plane):?>
                                                    <option value="<?=$plane['id'];?>"><?=!empty($plane['service_name']) ? $plane['service_name'] : $plane['name'];?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row-line mt-0">
                        <div class="col-1-2">
                            <div class="width-100"><label>Может редактировать свою работу до проверки</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="homework_edit" type="radio" value="1" checked><span>Да</span></label>
                                    <label class="custom-radio"><input name="homework_edit" type="radio" value="0"><span>Нет</span></label>
                                </span>
                            </div>

                            <input type="hidden" name="on_public_homework" value="0"><?/*ToDo убрать после раскоментирования ниже кода*/?>
                            <?/*<div class="width-100"><label>Включить публичные ДЗ</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="on_public_homework" type="radio" value="1"><span>Да</span></label>
                                    <label class="custom-radio"><input name="on_public_homework" type="radio" value="0" checked><span>Нет</span></label>
                                </span>
                            </div>*/?>
                        </div>
                        <div class="col-1-2">
                            <div class="width-100"><label>Включить возможность комментирования</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="homework_comment_add" type="radio" value="1" checked data-show_on="block_comments"><span>Да</span></label>
                                    <label class="custom-radio"><input name="homework_comment_add" type="radio" value="0"><span>Нет</span></label>
                                </span>
                            </div>

                            <div id="block_comments" class="width-100 hidden"><label>Блокировка комментариев после принятия ДЗ</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="lock_comment" type="radio" value="1"><span>Да</span></label>
                                    <label class="custom-radio"><input name="lock_comment" type="radio" value="0" checked><span>Нет</span></label>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Начало тренинга</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Что считать началом прохождения</label>
                                <div class="select-wrap">
                                    <select name="start_type">
                                        <option value="<?=Training::START_DATE;?>">Дата старта</option>
                                        <option value="<?=Training::GROUP_DIST_DATE;?>">Дата назначения группы</option>
                                        <option value="<?=Training::SUBS_DIST_DATE;?>">Дата назначения группы</option>
                                        <option value="<?=Training::ENTER_IN_LESSON_DATE;?>">Вошёл в урок</option>
                                        <option value="<?=Training::ANSWER_IN_LESSON_DATE;?>">Ответил в уроке</option>
                                        <option value="<?=Training::LESSON_COMPLETED_DATE;?>">Выполнил урок</option>
                                    </select>
                                </div>
                            </div>

                            <p><label>Виден ли тренинг в кабинете до даты начала?</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_before_start" type="radio" value="1" checked><span>Да</span></label>
                                    <label class="custom-radio"><input name="show_before_start" type="radio" value="0"><span>Нет</span></label>
                                </span>
                            </p>

                            <?/*<p><a href="/" target="_blank">Показать пользователей, начавших тренинг</a></p>*/?>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Что будет окончанием тренинга?</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Что считать окончанием тренинга</label>
                                <div class="select-wrap">
                                    <select name="finish_type">
                                        <option value="<?=Training::END_DATE;?>">Дата окончания</option>
                                        <option value="<?=Training::ENTER_IN_LESSON_DATE;?>">Вошёл в урок</option>
                                        <option value="<?=Training::ANSWER_IN_LESSON_DATE;?>">Ответил в уроке</option>
                                        <option value="<?=Training::LESSON_COMPLETED_DATE;?>">Выполнил урок</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- 3 ВКЛАДКА КНОПКИ -->
                <div>
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Большая кнопка</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100 mb-0"><label>Назначение</label>
                                <div class="select-wrap">
                                    <select id="big_button_type" name="big_button[type]">
                                        <option value="0" data-show_off="big_button_text">Нет кнопки</option>
                                        <option value="1" data-show_on="big_button_product_order">Заказ продукта</option>
                                        <option value="2" data-show_on="big_button_rate">Выбор тарифа (несколько продуктов)</option>
                                        <option value="3" data-show_on="big_button_product_desc">Описание продукта</option>
                                        <option value="7" data-show_on="big_button_product_desc">Описание продукта (в модальном окне)</option>
                                        <option value="4" data-show_on="big_button_url">Свой Url</option>
                                        <option value="6" data-show_on="big_button_text">Войти в тренинг</option>
                                    </select>
                                </div>
                            </div>

                            <p class="width-100" id="big_button_text"><label>Текст кнопки</label>
                                <input type="text" name="big_button[text]" value="<?=isset($small_button['text']) ? $small_button['text'] : ''?>">
                            </p>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100 hidden mb-0" id="big_button_product_order">
                                <label>Выбор товара</label>
                                <div class="select-wrap">
                                    <select name="big_button[product_order]">
                                        <option value="0">Не выбран</option>
                                        <?php foreach($product_list as $product):?>
                                            <option value="<?=$product['product_id'];?>"><?=$product['product_name'];?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100 hidden mb-0" id="big_button_rate"><label>Выбор тарифа</label>
                                <select name="big_button[rate][]" multiple="multiple" class="multiple-select">
                                    <option value="0">Не выбран</option>
                                    <?php foreach($product_list as $product):?>
                                        <option value="<?=$product['product_id'];?>"><?=$product['product_name'];?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>

                            <div class="width-100 hidden mb-0" id="big_button_product_desc">
                                <label>Выбор товара</label>
                                <div class="select-wrap">
                                    <select name="big_button[product_desc]">
                                        <option value="0">Не выбран</option>
                                        <?php foreach($product_list as $product):?>
                                            <option value="<?=$product['product_id'];?>"><?=$product['product_name'];?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>

                            <div id="big_button_url" class="hidden mb-0">
                                <p><label>Url ссылки</label>
                                    <input type="text" name="big_button[your_url]" placeholder="http://">
                                </p>

                                <p class="width-100"><label>Текст для ссылки</label>
                                    <input type="text" name="big_button[text]">
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Маленькая кнопка</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100 mb-0"><label>Назначение</label>
                                <div class="select-wrap">
                                    <select id="small_button_type" name="small_button[type]">
                                        <option value="0" data-show_off="small_button_text">Нет кнопки</option>
                                        <option value="1" data-show_on="small_button_product_order">Заказ продукта</option>
                                        <option value="2" data-show_on="small_button_rate">Выбор тарифа (несколько продуктов)</option>
                                        <option value="3" data-show_on="small_button_product_desc">Описание продукта</option>
                                        <option value="7" data-show_on="small_button_product_desc">Описание продукта (в модальном окне)</option>
                                        <option value="4" data-show_on="small_button_url">Свой Url</option>
                                        <option value="6">Войти в тренинг</option>
                                    </select>
                                </div>
                            </div>
                            
                            <p class="width-100" id="small_button_text"><label>Текст кнопки</label>
                                <input type="text" name="small_button[text]" value="<?=isset($small_button['text']) ? $small_button['text'] : ''?>">
                            </p>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100 hidden mb-0" id="small_button_product_order"><label>Выбор товара</label>
                                <div class="select-wrap">
                                    <select name="small_button[product_order]">
                                        <option value="0">Не выбран</option>
                                        <?php foreach($product_list as $product):?>
                                            <option value="<?=$product['product_id'];?>"><?=$product['product_name'];?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100 hidden mb-0" id="small_button_rate"><label>Выбор тарифа</label>
                                <select name="small_button[rate][]" multiple="multiple" class="multiple-select">
                                    <option value="0">Не выбран</option>
                                    <?php foreach($product_list as $product):?>
                                        <option value="<?=$product['product_id'];?>"><?=$product['product_name'];?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>

                            <div class="width-100 hidden mb-0" id="small_button_product_desc">
                                <label>Выбор товара</label>
                                <div class="select-wrap">
                                    <select name="small_button[product_desc]">
                                        <option value="0">Не выбран</option>
                                        <?php foreach($product_list as $product):?>
                                            <option value="<?=$product['product_id'];?>"><?=$product['product_name'];?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>

                            <div id="small_button_url" class="hidden mb-0">
                                <p><label>Url ссылки</label>
                                    <input type="text" name="small_button[your_url]" placeholder="http://">
                                </p>

                                <p class="width-100"><label>Текст для ссылки</label>
                                    <input type="text" name="small_button[text]">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- 4 ВКЛАДКА ВНЕШНИЙ ВИД-->
                <div>
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Опции</h4>
                        </div>

                        <div class="col-1-2">

                            <div class="width-100"><label>Продолжительность тренинга</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="duration_type" type="radio" value="1" checked><span>Считать</span></label>
                                    <label class="custom-radio"><input name="duration_type" type="radio" value="2" data-show_on="duration"><span>Написать</span></label>
                                </span>
                            </div>

                            <p class="width-100 hidden" id="duration"><label>Продолжительность</label>
                                <input type="text" name="duration">
                            </p>

                            <div class="width-100"><label>Сложность курса</label>
                                <div class="select-wrap">
                                    <select name="complexity">
                                        <option value="1">Лёгкий</option>
                                        <option value="2">Средний</option>
                                        <option value="3">Сложный</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="col-1-2">

                            <div class="width-100"><label>Количество уроков</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="count_lessons_type" type="radio" value="1" checked><span>Считать</span></label>
                                    <label class="custom-radio"><input name="count_lessons_type" type="radio" value="2" data-show_on="count_lessons"><span>Написать</span></label>
                                </span>
                            </div>

                            <p class="width-100 hidden" id="count_lessons"><label>Уроков</label>
                                <input type="text" name="count_lessons">
                            </p>

                            <div class="width-100"><label>Сортировка уроков</label>
                                <div class="select-wrap">
                                    <select name="sort_lessons">
                                        <option value="1">По возрастанию</option>
                                        <option value="2">По убыванию</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Внешний вид в списке</h4>
                        </div>

                        <div class="col-1-2">

                            <div class="width-100"><label>Описание тренинга</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_desc" type="radio" value="1" checked><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_desc" type="radio" value="0"><span>Нет</span></label>
                                </span>
                            </div>
                            <div class="width-100"><label>Количество уроков</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_count_lessons" type="radio" value="1" checked><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_count_lessons" type="radio" value="0"><span>Нет</span></label>
                                </span>
                            </div>

                            <div class="width-100"><label>Прогресс прохождения</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_progress2list" type="radio" value="1" checked><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_progress2list" type="radio" value="0"><span>Нет</span></label>
                                </span>
                            </div>
                        </div>

                        <div class="col-1-2">

                            <div class="width-100"><label>Дата начала</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_start_date" type="radio" value="1"><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_start_date" type="radio" value="0" checked><span>Нет</span></label>
                                </span>
                            </div>

                            <div class="width-100"><label>Сложность курса</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_complexity" type="radio" value="1"><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_complexity" type="radio" value="0" checked><span>Нет</span></label>
                                </span>
                            </div>

                            <div class="width-100"><label>Стоимость</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_price" type="radio" value="1" data-show_on="show_price"><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_price" type="radio" value="0" checked><span>Нет</span></label>
                                </span>
                                <p class="hidden" id="show_price">
                                    <input type="text" name="price" value="">
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Внешний вид в списке</h4>
                        </div>

                        <? /*
                        <div class="col-1-2">
                            <div class="width-100"><label>Виджет о курсе</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_widget_training" type="radio" value="1" checked><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_widget_training" type="radio" value="0"><span>Нет</span></label>
                                </span>
                            </div>
                        </div>
                    */ ?>

                        <div class="col-1-2">
                            <div class="width-100"><label>Виджет с прогрессом</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_widget_progress" type="radio" value="1" checked><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_widget_progress" type="radio" value="0"><span>Нет</span></label>
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-1-2">
                            <div class="width-100"><label>Обложки уроков на мобильных</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_lesson_cover_2mobile" type="radio" value="1"><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_lesson_cover_2mobile" type="radio" value="0" checked><span>Скрыть</span></label>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Внешний вид внутри тренинга</h4>
                        </div>
                        <div class="col-1-2">
                            <div class="width-100"><label>Макет уроков</label>
                                <div class="select-wrap">
                                    <select name="lessons_tmpl">
                                        <option value="1">Обычный</option>
                                        <option value="2">Широкий</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?/*<div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Выводить в личном кабинете, если не куплен</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Выводить в кабинете пользователя, если не куплен</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_in_lk2not_buy" type="radio" value="1" checked data-show_on="text_in_lk2not_buy"><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_in_lk2not_buy" type="radio" value="0"><span>Нет</span></label>
                                </span>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <p class="width-100 hidden" id="text_in_lk2not_buy"><label>Надпись, если не куплен</label>
                                <input type="text" autocomplete="off" class="" name="text_in_lk2not_buy">
                            </p>
                        </div>
                    </div>*/?>
                </div>


                <? /*
                <!-- 5 ВКЛАДКА ЗАЩИТА-->
                <div>
                    <div class="row-line" style=";background-color: #eeeeff;">
                        <div class="col-1-1">
                            <h4>Заполнение профиля для доступа</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Требовать подтверждение телефона</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="confirm_phone" type="radio" value="1"><span>Да</span></label>
                                    <label class="custom-radio"><input name="confirm_phone" type="radio" value="0" checked><span>Нет</span></label>
                                </span>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Запрашивать привязку Telegram</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="binding_tg" type="radio" value="1"><span>Да</span></label>
                                    <label class="custom-radio"><input name="binding_tg" type="radio" value="0" checked><span>Нет</span></label>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row-line" style=";background-color: #eeeeff;">
                        <div class="col-1-1">
                            <h4>Watermark</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label title="Для вывода водного знака нужно сконфигурировать свой плеер. Подробнее в справке.">Показывать телефон</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_watermark_phone" type="radio" value="1"><span>Да</span></label>
                                    <label class="custom-radio"><input name="show_watermark_phone" type="radio" value="0" checked><span>Нет</span></label>
                                </span>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label title="Для вывода водного знака нужно сконфигурировать свой плеер. Подробнее в справке.">Показывать e-mail</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_watermark_email" type="radio" value="1"><span>Да</span></label>
                                    <label class="custom-radio"><input name="show_watermark_email" type="radio" value="0" checked><span>Нет</span></label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                */ ?>

                <!-- 6 ВКЛАДКА SEO -->
                <div>
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>SEO</h4>
                        </div>

                        <div class="col-1-2">
                            <p class="width-100"><label>Title</label>
                                <input type="text" name="title" placeholder="Title тренинга">
                            </p>

                            <p class="width-100"><label>Meta Description</label>
                                <textarea name="meta_desc" rows="3" cols="40"></textarea>
                            </p>
                        </div>

                        <div class="col-1-2">
                            <p class="width-100"><label>Alias</label>
                                <input type="text" name="alias" placeholder="Алиас тренинга">
                            </p>

                            <p class="width-100"><label>Meta Keyword</label>
                                <textarea name="meta_keys" rows="3" cols="40"></textarea>
                            </p>
                        </div>
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