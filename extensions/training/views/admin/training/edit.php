<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php');
$membership = System::CheckExtensension('membership', 1);
$group_list = User::getUserGroups();
$planes = Member::getPlanes();
$lesson_list = TrainingLesson::getLessons2Training($id);
$product_list = Product::getProductListOnlySelect();
//TODO Временно пока для экспериментов
$access_task_global = json_decode($training["access_task_global"]);?>

<div class="main">
    <div class="top-wrap">
        <h1>Редактировать тренинг (ID: <?=$training['training_id'];?>)</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a>
            <a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>

    <form action="" method="POST" enctype="multipart/form-data">
        <ul class="breadcrumb">
            <li><a href="/admin">Дашбоард</a></li>
            <li><a href="/admin/training/">Тренинги</a></li>
            <li>Редактировать тренинг</li>
        </ul>

        <?php if(isset($_GET['success'])):?>
            <div class="admin_message">Успешно!</div>
        <?php endif;?>

        <div class="traning-top">
            <h3 class="traning-title"><?=$training['name'];?></h3>
            <ul class="nav_button">
                <li><input type="submit" name="savetraining" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last">
                    <a class="button red-link" href="/admin/training/structure/<?=$training['training_id'];?>">Закрыть</a>
                </li>
            </ul>
        </div>

        <div class="tabs">
            <ul class="overflow-container tabs-ul">
                <li>Основное</li>
                <li>Доступ</li>
                <li>Кнопки</li>
                <li>Внешний вид</li>
                <? /* <li>Защита</li> */ ?>
                <li>SEO</li>
                <li>Уведомления</li>
                <li>Сертификат</li>
            </ul>

            <div class="admin_form">
                <!-- 1 вкладка -->
                <div>
                    <div class="row-line">

                        <div class="col-1-2">
                            <h4>Основное</h4>
                            <p class="width-100"><label>Название</label>
                                <input type="text" name="name" placeholder="Название тренинга" value="<?=$training['name'];?>" required="required">
                            </p>
                            <p><label>Статус</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="status" type="radio" value="1"<?php if($training['status']) echo ' checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="status" type="radio" value="0"<?php if(!$training['status']) echo ' checked';?>><span>Откл</span></label>
                                </span>
                            </p>

                            <div class="width-100"><label>Категория в списке тренингов</label>
                                <div class="select-wrap">
                                    <select name="cat_id">
                                        <option value="0">Без категории</option>
                                        <?php $cat_list = TrainingCategory::getCatList();
                                        if($cat_list):
                                            foreach($cat_list as $cat):?>
                                        <option value="<?=$cat['cat_id'];?>"<?php if($cat['cat_id'] == $training['cat_id']) echo ' selected="selected"';?>><?=$cat['name'];?></option>
                                        <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100"><label>Показывать в списке тренингов</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_in_main" type="radio" value="1"<?php if($training['show_in_main']) echo ' checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="show_in_main" type="radio" value="0"<?php if(!$training['show_in_main']) echo ' checked';?>><span>Выкл</span></label>
                                </span>
                            </div>
                            
                            <div class="width-100">
                                <label>Платный тренинг</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="is_free" type="radio" value="0"<?php if(!$training['is_free']) echo ' checked';?>><span>Да</span></label>
                                    <label class="custom-radio"><input name="is_free" type="radio" value="1"<?php if($training['is_free']) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>

                            <? /*
                            <div class="width-100"><label>Разрешить пользовательские заметки</label>
                                <span class="custom-radio-wrap">
                                                <label class="custom-radio"><input name="allow_user_notes" type="radio" value="1" <?php if($training['allow_user_notes']) echo ' checked';?>><span>Вкл</span></label>
                                                <label class="custom-radio"><input name="allow_user_notes" type="radio" value="0" <?php if(!$training['allow_user_notes']) echo ' checked';?>><span>Выкл</span></label>
                                            </span>
                            </div>
                        */ ?>

                        </div>

                        <div class="col-1-2">
                            <h4>Обложка</h4>
                            <div class="width-100"><label>Обложка</label>
                                <input type="file" name="cover">
                            </div>

                            <?php if(!empty($training['cover'])):?>
                                <div class="width-100 del_img_wrap">
                                    <img src="/images/training/<?=$training['cover']?>" alt="" width="210">
                                </div>
                            <?php endif;?>

                            <div class="width-100"><label>Alt обложки</label>
                                <input type="text" size="35" value="<?=$training['img_alt'];?>" name="img_alt" placeholder="Альтернативный текст">
                                <input type="hidden" name="current_img" value="<?=$training['cover'];?>">
                            </div>

                            <div class="width-100"><label title="В любых CSS величинах: px, em">Отступы обложки</label>
                                <input type="text" size="3" value="<?=$training['padding'];?>" name="padding" name="padding" placeholder="Для примера: 20px 20px 20px 20px">
                            </div>
                            <input type="hidden" value="0" name="count_free_lessons">
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Авторы</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Выберите авторов</label>
                                <?php $authors = User::getAuthors();?>
                                <select size="6" name="authors[]" multiple="multiple" class="multiple-select">
                                    <?php if($authors):
                                        $tr_authors = explode(',', $training['authors']);
                                        foreach($authors as $author):?>
                                    <option value="<?=$author['user_id'];?>"<?php if($tr_authors && in_array($author['user_id'], $tr_authors)) echo ' selected="selected"';?>><?=$author['user_name'];?></option>
                                    <?php endforeach;
                                    endif; ?>
                                </select>
                            </div>

                        </div>
                        <? /*
                        <div class="col-1-2">
                            <div class="width-100"><label>Авторы могут править тренинг</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="authors_can_edit" type="radio" value="1"<?php if($training['authors_can_edit']) echo ' checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="authors_can_edit" type="radio" value="0"<?php if(!$training['authors_can_edit']) echo ' checked';?>><span>Выкл</span></label>
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
                                <select size="6" class="multiple-select" name="mastercurators[]" multiple="multiple">
                                    <?php $curators = User::getCurators();
                                    if ($curators):
                                        foreach($curators as $curator):?>
                                    <option value="<?=$curator['user_id']?>"<?php if($tr_curators['datamaster'] && in_array($curator['user_id'], $tr_curators['datamaster'])) echo ' selected="selected"';?>><?=$curator['user_name'] .' '. $curator['surname']?></option>
                                    <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Автоматическое распределение учеников</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="curators_auto_assign" type="radio" value="1"<?php if($training['curators_auto_assign']) echo ' checked';?> data-show_on="curators-to-assign"><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="curators_auto_assign" type="radio" value="0"<?php if(!$training['curators_auto_assign']) echo ' checked';?>><span>Выкл</span></label>
                                </span>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Кураторы</label>
                                <select size="6" id="simplecurators" class="multiple-select" name="curators[]" multiple="multiple">
                                    <?php $curators = User::getCurators();
                                    if ($curators):
                                        foreach($curators as $curator):?>
                                    <option value="<?=$curator['user_id']?>"<?php if($tr_curators['datacurators'] && in_array($curator['user_id'], $tr_curators['datacurators'])) echo ' selected="selected"';?>><?=$curator['user_name'] .' '. $curator['surname']?></option>
                                    <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        </div>

                        <? /*
                        <div class="col-1-2">
                            <div class="width-100"><label>Кураторы могут править тренинг</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="curators_can_edit" type="radio" value="1"<?php if($training['curators_can_edit']) echo ' checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="curators_can_edit" type="radio" value="0"<?php if(!$training['curators_can_edit']) echo ' checked';?>><span>Выкл</span></label>
                                </span>
                            </div>
                        </div>
                        */ ?>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Краткое описание тренинга (в списке тренингов)</h4>
                        </div>

                        <div class="col-1-1">
                            <div class="width-100" title="Описание для списка тренингов"><label>Описание</label>
                                <textarea class="editorsmall" name="short_desc"><?=$training['short_desc'];?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Полное описание тренинга (внутри тренинга)</h4>
                        </div>

                        <div class="col-1-2">
                            <?php $full_cover_param = json_decode($training['full_cover_param'], true);?>
                            <div class="row-line">
                                <div class="col-55">
                                    <div class="overlay-wrap"><label>Оверлей</label>
                                        <div class="overlay-inner">
                                            <input type="color" name="full_cover_param[overlaycolor]" value="<?=isset($full_cover_param['overlaycolor']) ? $full_cover_param['overlaycolor'] : '#000000'?>">выбрать
                                        </div>
                                    </div>
                                </div>

                                <div class="col-45">
                                    <div class="width-100"><label>Прозрачность</label>
                                        <div class="select-wrap">
                                            <select name="full_cover_param[overlay]">
                                                <option value="1.0"<?php if(isset($full_cover_param['overlay']) && $full_cover_param['overlay'] == '1.0') echo ' selected="selected"';?>>1.0</option>
                                                <option value="0.9"<?php if(isset($full_cover_param['overlay']) && $full_cover_param['overlay'] == '0.9') echo ' selected="selected"';?>>0.9</option>
                                                <option value="0.8"<?php if(isset($full_cover_param['overlay']) && $full_cover_param['overlay'] == '0.8') echo ' selected="selected"';?>>0.8</option>
                                                <option value="0.7"<?php if(isset($full_cover_param['overlay']) && $full_cover_param['overlay'] == '0.7') echo ' selected="selected"';?>>0.7</option>
                                                <option value="0.6"<?php if(isset($full_cover_param['overlay']) && $full_cover_param['overlay'] == '0.6') echo ' selected="selected"';?>>0.6</option>
                                                <option value="0.5"<?php if(isset($full_cover_param['overlay']) && $full_cover_param['overlay'] == '0.5') echo ' selected="selected"';?>>0.5</option>
                                                <option value="0.4"<?php if(isset($full_cover_param['overlay']) && $full_cover_param['overlay'] == '0.4') echo ' selected="selected"';?>>0.4</option>
                                                <option value="0.3"<?php if(isset($full_cover_param['overlay']) && $full_cover_param['overlay'] == '0.3') echo ' selected="selected"';?>>0.3</option>
                                                <option value="0.2"<?php if(isset($full_cover_param['overlay']) && $full_cover_param['overlay'] == '0.2') echo ' selected="selected"';?>>0.2</option>
                                                <option value="0.1"<?php if(isset($full_cover_param['overlay']) && $full_cover_param['overlay'] == '0.1') echo ' selected="selected"';?>>0.1</option>
                                                <option value="0.0"<?php if(isset($full_cover_param['overlay']) && $full_cover_param['overlay'] == '0.0') echo ' selected="selected"';?>>0.0</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="width-100 mt-20"><label>Позиция фона</label>
                                <div class="select-wrap">
                                    <select name="full_cover_param[position]">
                                        <option value="center center"<?php if(isset($full_cover_param['position']) && $full_cover_param['position'] == 'center center') echo ' selected="selected"';?>>По центру</option>
                                        <option value="center top"<?php if(isset($full_cover_param['position']) && $full_cover_param['position'] == 'center top') echo ' selected="selected"';?>>Сверху и по центру </option>
                                    </select>
                                </div>
                            </div>

                            <p class="px-label-wrap"><label>Высота блока<span class="px-label">px</span></label>
                                <input type="text" name="full_cover_param[heroheigh]" value="<?=isset($full_cover_param['heroheigh']) ? $full_cover_param['heroheigh'] : '';?>">
                            </p>

                            <p class="px-label-wrap"><label>Высота блока на мобильных<span class="px-label">px</span></label>
                                <input type="text" name="full_cover_param[heromobileheigh]" value="<?=isset($full_cover_param['heromobileheigh']) ? $full_cover_param['heromobileheigh'] : '';?>">
                            </p>
                        </div>

                        <div class="col-1-2">
                            <?php if(!empty($training['full_cover'])):?>
                            <div class="width-100 del_img_wrap">
                                <input type="hidden" name="full_cover_current_img" value="<?=$training['full_cover']?>">
                                <img src="/images/training/<?=$training['full_cover']?>" alt="" width="210">
                                <span class="del_img_link">
                                    <button type="submit" form="del_full_img" value=" " title="Удалить изображение с сервера?" name="del_img">
                                        <span class="icon-remove"></span>
                                    </button>
                                </span>
                            </div>
                            <?php else:?>
                            <p><label>Обложка внутри тренинга</label><input type="file" name="full_cover"></p>
                            <?php endif;?>
                        </div>

                        <div class="col-1-1">
                            <div class="width-100" title="Описание для списка тренингов"><label>Описание</label>
                                <textarea class="editor"  name="full_desc"><?=$training['full_desc'];?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ДОСТУП -->
                <div>
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Настройка доступа к тренингу</h4>
                        </div>

                        <div class="col-1-2">
                            <div class=""><label>Тип доступа к тренингу</label>
                                <div class="select-wrap">
                                    <select name="access_type">
                                        <option value="<?=Training::ACCESS_TO_GROUP;?>"<?php if($training['access_type'] == Training::ACCESS_TO_GROUP) echo ' selected="selected"';?> data-show_on="access_group">Группа</option>
                                        <?php if($membership):?>
                                            <option value="<?=Training::ACCESS_TO_SUBS;?>"<?php if($training['access_type'] == Training::ACCESS_TO_SUBS) echo ' selected="selected"';?> data-show_on="access_member">Подписка</option>
                                        <?php endif;?>
                                        <option value="<?=Training::ACCESS_FREE;?>"<?php if($training['access_type'] == Training::ACCESS_FREE) echo ' selected="selected"';?>>Свободный</option>
                                    </select>
                                </div>
                            </div>

                            <div class="hidden mt-20" id="access_group"><label>Выберите группу</label>
                                <select size="6" class="multiple-select" name="access_groups[]" multiple="multiple">
                                    <?php $groups = json_decode($training['access_groups'], true);
                                    if($group_list):
                                        foreach($group_list as $group):?>
                                            <option value="<?=$group['group_id'];?>"<?php if($groups && in_array($group['group_id'], $groups)) echo ' selected="selected"';?>><?=$group['group_title'];?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>

                            <?php if($membership):?>
                                <div class="width-100 mt-20 hidden" id="access_member"><label>Подписка для доступа к тренингу</label>
                                    <select class="multiple-select" name="access_planes[]" multiple="multiple">
                                        <?php $accesses = json_decode($training['access_planes'], true);
                                        if($planes):
                                            foreach($planes as $plane):?>
                                                <option value="<?=$plane['id'];?>"<?php if($accesses && in_array($plane['id'], $accesses)) echo ' selected="selected"';?>><?=!empty($plane['service_name']) ? $plane['service_name'] : $plane['name'];?></option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            <?php endif;?>
                        </div>

                        <? /*
                        <div class="col-1-2">
                            <p><label>Показывать в кабинете клиента, если нет доступа</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_in_lk2not_access" type="radio" value="1"<?php if($training['show_in_lk2not_access'] == 1) echo ' checked';?>><span>Показывать</span></label>
                                    <label class="custom-radio"><input name="show_in_lk2not_access" type="radio" value="0"<?php if($training['show_in_lk2not_access'] == 0) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </p>
                        </div>
                        */ ?>
                    </div>

                    <div class="row-line mt-20">
                        <div class="col-1-2">
                            <div class="width-100"><label>Дата старта тренинга</label>
                                <div class="datetimepicker-wrap"><input type="text" placeholder="выберите дату" autocomplete="off" class="datetimepicker" name="start_date" value="<?php if($training['start_date']) echo date("d.m.Y H:i", $training['start_date']);?>"></div>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Дата окончания тренинга</label>
                                <div class="datetimepicker-wrap"><input type="text" placeholder="выберите дату" autocomplete="off" class="datetimepicker" name="end_date" value="<?php if($training['end_date']) echo date("d.m.Y H:i", $training['end_date']);?>"></div>
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
                                        <option value="<?=Training::ACCESS_TO_GROUP;?>"<?php if(isset($access_task_global->curator->groups)) echo ' selected="selected"';?> data-show_on="access_group2task_for_curator">Группа</option>
                                        <?php if($membership):?>
                                            <option value="<?=Training::ACCESS_TO_SUBS;?>"<?php if(isset($access_task_global->curator->planes)) echo ' selected="selected"';?> data-show_on="access_member2task_for_curator">Подписка</option>
                                        <?php endif;?>
                                        <option value="<?=Training::ACCESS_FREE;?>"<?php if(isset($access_task_global->curator->free)) echo ' selected="selected"';?> data-show_off="group_auto_self">Свободный</option>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100 hidden" id="access_group2task_for_curator"><label>Группа доступа к домашним заданиям</label>
                                <select class="multiple-select" name="access_task_groups_for_curator[]" multiple="multiple">
                                    <?php $cur_groups = isset($access_task_global->curator->groups) ? $access_task_global->curator->groups : false;
                                    if($group_list):
                                        foreach($group_list as $group):?>
                                            <option value="<?=$group['group_id'];?>"<?php if($cur_groups && in_array($group['group_id'], $access_task_global->curator->groups)) echo ' selected="selected"';?>><?=$group['group_title'];?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>

                            <?php if($membership):?>
                                <div class="width-100 hidden" id="access_member2task_for_curator"><label>Подписка для доступа к домашним заданиям</label>
                                    <select class="multiple-select" name="access_task_planes_for_curator[]" multiple="multiple">
                                        <?php $cur_planes = isset($access_task_global->curator->planes) ? $access_task_global->curator->planes : false;
                                        if($planes):
                                            foreach($planes as $plane):?>
                                                <option value="<?=$plane['id'];?>"<?php if($cur_planes && in_array($plane['id'], $access_task_global->curator->planes)) echo ' selected="selected"';?>><?=!empty($plane['service_name']) ? $plane['service_name'] : $plane['name'];?></option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            <?php endif;?>

                            <div class="" id="group_auto_self">
                                <div class="width-100"><label>Тип доступа при автоматической проверке</label>
                                    <div class="select-wrap">
                                        <select name="access_task_type_automat">
                                            <option value="<?=Training::ACCESS_TO_GROUP;?>"<?php if(isset($access_task_global->automat->groups)) echo ' selected="selected"';?> data-show_on="access_group2task_for_automat">Группа</option>
                                            <?php if($membership):?>
                                                <option value="<?=Training::ACCESS_TO_SUBS;?>"<?php if(isset($access_task_global->automat->planes)) echo ' selected="selected"';?> data-show_on="access_member2task_for_automat">Подписка</option>
                                            <?php endif;?>
                                            <option value="<?=Training::ACCESS_FREE;?>"<?php if(isset($access_task_global->automat->free)) echo ' selected="selected"';?> data-show_off="group_self">Свободный</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="width-100 hidden" id="access_group2task_for_automat"><label>Группа доступа к домашним заданиям</label>
                                    <select class="multiple-select" name="access_task_groups_for_automat[]" multiple="multiple">
                                        <?php $avt_groups = isset($access_task_global->automat->groups) ? $access_task_global->automat->groups : false;
                                        if($group_list):
                                            foreach($group_list as $group):?>
                                                <option value="<?=$group['group_id'];?>"<?php if($avt_groups && in_array($group['group_id'], $access_task_global->automat->groups)) echo ' selected="selected"';?>><?=$group['group_title'];?></option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>

                                <?php if($membership):?>
                                    <div class="width-100 hidden" id="access_member2task_for_automat"><label>Подписка для доступа к домашним заданиям</label>
                                        <select class="multiple-select" name="access_task_planes_for_automat[]" multiple="multiple">
                                            <?php $avt_planes = isset($access_task_global->automat->planes) ? $access_task_global->automat->planes : false;
                                            if($group_list):
                                                foreach($planes as $plane):?>
                                                    <option value="<?=$plane['id'];?>"<?php if($avt_planes && in_array($plane['id'], $access_task_global->automat->planes)) echo ' selected="selected"';?>><?=!empty($plane['service_name']) ? $plane['service_name'] : $plane['name'];?></option>
                                                <?php endforeach;
                                            endif;?>
                                        </select>
                                    </div>
                                <?php endif;?>


                                <div id="group_self" class="mb-20">
                                    <div class="width-100"><label>Тип доступа при самостоятельной проверке</label>
                                        <div class="select-wrap">
                                            <select name="access_task_type_bezproverki">
                                                <option value="<?=Training::ACCESS_TO_GROUP;?>"<?php if(isset($access_task_global->bezproverki->groups)) echo ' selected="selected"';?> data-show_on="access_group2task_for_bezproverki">Группа</option>
                                                <?php if($membership):?>
                                                    <option value="<?=Training::ACCESS_TO_SUBS;?>"<?php if(isset($access_task_global->bezproverki->planes)) echo ' selected="selected"';?> data-show_on="access_member2task_for_bezproverki">Подписка</option>
                                                <?php endif;?>
                                                <option value="<?=Training::ACCESS_FREE;?>"<?php if(isset($access_task_global->bezproverki->free)) echo ' selected="selected"';?>>Свободный</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="hidden" id="access_group2task_for_bezproverki"><label>Группа доступа к домашним заданиям</label>
                                        <select class="multiple-select" name="access_task_groups_for_bezproverki[]" multiple="multiple">
                                            <?php $bez_group = isset($access_task_global->bezproverki->groups) ? $access_task_global->bezproverki->groups : false;
                                            if($group_list):
                                                foreach($group_list as $group):?>
                                                    <option value="<?=$group['group_id'];?>"<?php if($bez_group && in_array($group['group_id'], $access_task_global->bezproverki->groups)) echo ' selected="selected"';?>><?=$group['group_title'];?></option>
                                                <?php endforeach;
                                            endif?>
                                        </select>
                                    </div>

                                    <?php if($membership):?>
                                        <div class="width-100 hidden" id="access_member2task_for_bezproverki"><label>Подписка для доступа к домашним заданиям</label>
                                            <select class="multiple-select" name="access_task_planes_for_bezproverki[]" multiple="multiple">
                                                <?php $bez_planes = isset($access_task_global->bezproverki->planes) ? $access_task_global->bezproverki->planes : false;
                                                if($planes):
                                                    foreach($planes as $plane):?>
                                                        <option value="<?=$plane['id'];?>"<?php if($bez_planes && in_array($plane['id'], $access_task_global->bezproverki->planes)) echo ' selected="selected"';?>><?=!empty($plane['service_name']) ? $plane['service_name'] : $plane['name'];?></option>
                                                    <?php endforeach;
                                                endif;?>
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
                                    <label class="custom-radio"><input name="homework_edit" type="radio" value="1"<?php if($training['homework_edit'] == 1) echo ' checked';?> checked><span>Да</span></label>
                                    <label class="custom-radio"><input name="homework_edit" type="radio" value="0"<?php if($training['homework_edit'] == 0) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>

                            <?/*ToDo убрать после раскомментирования ниже кода*/?>
                            <input type="hidden" value="0" name="on_public_homework">
                            <? /*
                            <div class="width-100"><label>Включить публичные ДЗ</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="on_public_homework" type="radio" value="1"<?php if($training['on_public_homework'] == 1) echo ' checked';?>><span>Да</span></label>
                                    <label class="custom-radio"><input name="on_public_homework" type="radio" value="0"<?php if($training['on_public_homework'] == 0) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>
                        */ ?>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Включить возможность комментирования</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="homework_comment_add" type="radio" value="1"<?php if($training['homework_comment_add'] == 1) echo ' checked';?> checked data-show_on="block_comments"><span>Да</span></label>
                                    <label class="custom-radio"><input name="homework_comment_add" type="radio" value="0"<?php if($training['homework_comment_add'] == 0) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>

                            <div id="block_comments" class="width-100 hidden"><label>Блокировка комментариев после принятия ДЗ</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="lock_comment" type="radio" value="1"<?php if($training['lock_comment'] == 1) echo ' checked';?>><span>Да</span></label>
                                    <label class="custom-radio"><input name="lock_comment" type="radio" value="0"<?php if($training['lock_comment'] == 0) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Начало тренинга</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Что считать началом тренинга</label>
                                <div class="select-wrap">
                                    <select name="start_type">
                                        <option value="<?=Training::START_DATE;?>"<?php if($training['start_type'] == Training::START_DATE) echo ' selected="selected"';?>>Дата старта</option>
                                        <option value="<?=Training::GROUP_DIST_DATE;?>"<?php if($training['start_type'] == Training::GROUP_DIST_DATE) echo ' selected="selected"';?>>Дата назначения группы</option>
                                        <option value="<?=Training::SUBS_DIST_DATE;?>"<?php if($training['start_type'] ==Training::SUBS_DIST_DATE) echo ' selected="selected"';?>>Дата назначения подписки</option>
                                        <option value="<?=Training::ENTER_IN_LESSON_DATE;?>"<?php if($training['start_type'] == Training::ENTER_IN_LESSON_DATE) echo ' selected="selected"';?> data-show_on="start_lessons">Вошёл в урок</option>
                                        <option value="<?=Training::ANSWER_IN_LESSON_DATE;?>"<?php if($training['start_type'] == Training::ANSWER_IN_LESSON_DATE) echo ' selected="selected"';?> data-show_on="start_lessons">Ответил в уроке</option>
                                        <option value="<?=Training::LESSON_COMPLETED_DATE;?>"<?php if($training['start_type'] == Training::LESSON_COMPLETED_DATE) echo ' selected="selected"';?> data-show_on="start_lessons">Выполнил урок</option>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100"><label>Виден ли тренинг в кабинете до даты начала?</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_before_start" type="radio" value="1"<?php if($training['show_before_start'] == 1) echo ' checked';?>><span>Да</span></label>
                                    <label class="custom-radio"><input name="show_before_start" type="radio" value="0"<?php if($training['show_before_start'] == 0) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>

                            <?/*<p><a href="/" target="_blank">Показать пользователей, начавших тренинг</a></p>*/?>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100 hidden" id="start_lessons"><label>Выбрать урок</label>
                                <select class="multiple-select" name="start_lessons[]" multiple="multiple">
                                    <?php if($lesson_list):
                                        $start_lessons = json_decode($training['start_lessons'], true);
                                        foreach($lesson_list as $lesson):?>
                                            <option value="<?=$lesson['lesson_id'];?>"<?php if($start_lessons && in_array($lesson['lesson_id'], $start_lessons)) echo ' selected="selected"';?>><?=$lesson['name'];?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
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
                                        <option value="<?=Training::END_DATE;?>"<?php if($training['finish_type'] == Training::END_DATE) echo ' selected="selected"';?>>Дата окончания</option>
                                        <option value="<?=Training::ENTER_IN_LESSON_DATE;?>"<?php if($training['finish_type'] == Training::ENTER_IN_LESSON_DATE) echo ' selected="selected"';?> data-show_on="finish_lessons">Вошёл в урок</option>
                                        <option value="<?=Training::ANSWER_IN_LESSON_DATE;?>"<?php if($training['finish_type'] == Training::ANSWER_IN_LESSON_DATE) echo ' selected="selected"';?> data-show_on="finish_lessons">Ответил в уроке</option>
                                        <option value="<?=Training::LESSON_COMPLETED_DATE;?>"<?php if($training['finish_type'] == Training::LESSON_COMPLETED_DATE) echo ' selected="selected"';?> data-show_on="finish_lessons">Выполнил урок</option>
                                    </select>
                                </div>
                            </div>

                            <? /* <p><a href="/" target="_blank">Показать пользователей, окончивших тренинг</a></p> */ ?>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100 hidden" id="finish_lessons"><label>Выбрать урок</label>
                                <select class="multiple-select" name="finish_lessons[]" multiple="multiple">
                                    <?php if($lesson_list):
                                        $finish_lesson = json_decode($training['finish_lessons'], true);
                                        foreach($lesson_list as $lesson):?>
                                            <option value="<?=$lesson['lesson_id'];?>"<?php if($finish_lesson && in_array($lesson['lesson_id'], $finish_lesson)) echo ' selected="selected"';?>><?=$lesson['name'];?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        </div>
                    </div>

                     <div class="row-line">
                         <div class="col-1-1 mb-0">
                             <h4>Что сделать по окончанию тренинга?</h4>
                         </div>

                        <div class="col-1-1">
                            <?php if(!$events_finish || count($events_finish) < 3):?>
                                <p class="width-100">
                                    <a href="#modal_add_event" class="link-add" data-uk-modal="{center:true}">Добавить событие</a>
                                </p>
                            <?php endif;
                            if($events_finish):
                                foreach ($events_finish as $event_finish):
                                    $params = json_decode($event_finish['params'], true);?>
                                    <p class="width-100 modal_edit-wrap">
                                        <a class="modal_edit" href="#modal_edit_<?=$event_finish['event_type'];?>" data-uk-modal="{center:true}">
                                            <span class="modal_edit-title"><?=$params['title'];?></span>
                                        </a>

                                        <a class="icon-remove ajax" href="javascript:void(0)" data-url="/admin/trainingajax/deltrainingeventfinish" data-id="<?=$event_finish['id'];?>"></a>
                                    </p>
                                <?php endforeach;
                            endif;?>
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
                                    <?php $big_button = json_decode($training['big_button'], true);?>
                                    <select id="big_button_type" name="big_button[type]">
                                        <option value="0"<?php if($big_button['type'] == 0) echo ' selected="selected"';?> data-show_off="big_button_text">Нет кнопки</option>
                                        <option value="1"<?php if($big_button['type'] == 1) echo ' selected="selected"';?> data-show_on="big_button_product_order">Заказ продукта</option>
                                        <option value="2"<?php if($big_button['type'] == 2) echo ' selected="selected"';?> data-show_on="big_button_rate">Выбор тарифа (несколько продуктов)</option>
                                        <option value="3"<?php if($big_button['type'] == 3) echo ' selected="selected"';?> data-show_on="big_button_product_desc">Описание продукта</option>
                                        <option value="7"<?php if($big_button['type'] == 7) echo ' selected="selected"';?> data-show_on="big_button_product_desc">Описание продукта (в модальном окне)</option>
                                        <option value="4"<?php if($big_button['type'] == 4) echo ' selected="selected"';?> data-show_on="big_button_url">Свой Url</option>
                                        <option value="6"<?php if($big_button['type'] == 6) echo ' selected="selected"';?> data-show_on="big_button_text">Войти в тренинг</option>
                                    </select>
                                </div>
                            </div>

                            <p class="width-100" id="big_button_text"><label>Текст кнопки</label>
                                <input type="text" name="big_button[text]" value="<?=isset($big_button['text']) ? $big_button['text'] : '';?>">
                            </p>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100 hidden" id="big_button_product_order">
                                <label>Выбор продукта</label>
                                <div class="select-wrap">
                                    <select name="big_button[product_order]">
                                        <option value="0">Не выбран</option>
                                        <?php foreach($product_list as $product):?>
                                            <option value="<?=$product['product_id'];?>"<?php if(isset($big_button['product_order']) && $big_button['product_order'] == $product['product_id']) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100 mb-0 hidden" id="big_button_rate"><label>Выбор тарифа</label>
                                <select name="big_button[rate][]" multiple="multiple" class="multiple-select">
                                    <option value="0">Не выбран</option>
                                    <?php foreach($product_list as $product):?>
                                        <option value="<?=$product['product_id'];?>"<?php if(isset($big_button['rate']) && in_array($product['product_id'], $big_button['rate'])) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>

                            <div class="width-100 mb-0 hidden" id="big_button_product_desc">
                                <label>Выбор продукта</label>
                                <div class="select-wrap">
                                    <select name="big_button[product_desc]">
                                        <option value="0">Не выбран</option>
                                        <?php foreach($product_list as $product):?>
                                            <option value="<?=$product['product_id'];?>"<?php if(isset($big_button['product_desc']) && $big_button['product_desc'] == $product['product_id']) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>

                            <div id="big_button_url" class="hidden mb-0">
                                <p><label>Url ссылки</label>
                                    <input type="text" name="big_button[your_url]" value="<?=$big_button['your_url']?>" placeholder="http://">
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Маленькая кнопка</h4>
                        </div>

                        <div class="col-1-2">
                            <?php $small_button = json_decode($training['small_button'], true);?>
                            <div class="width-100 mb-0"><label>Назначение</label>
                                <div class="select-wrap">
                                    <select id="small_button_type" name="small_button[type]">
                                        <option value="0"<?php if($small_button['type'] == 0) echo ' selected="selected"';?> data-show_off="small_button_text">Нет кнопки</option>
                                        <option value="1"<?php if($small_button['type'] == 1) echo ' selected="selected"';?> data-show_on="small_button_product_order">Заказ продукта</option>
                                        <option value="2"<?php if($small_button['type'] == 2) echo ' selected="selected"';?> data-show_on="small_button_rate">Выбор тарифа (несколько продуктов)</option>
                                        <option value="3"<?php if($small_button['type'] == 3) echo ' selected="selected"';?> data-show_on="small_button_product_desc">Описание продукта</option>
                                        <option value="7"<?php if($small_button['type'] == 7) echo ' selected="selected"';?> data-show_on="small_button_product_desc">Описание продукта (в модальном окне)</option>
                                        <option value="4"<?php if($small_button['type'] == 4) echo ' selected="selected"';?> data-show_on="small_button_url">Свой Url</option>
                                        <option value="6"<?php if($small_button['type'] == 6) echo ' selected="selected"';?> data-show_on="small_button_text">Войти в тренинг</option>
                                    </select>
                                </div>
                            </div>

                            <p class="width-100" id="small_button_text"><label>Текст кнопки</label>
                                <input type="text" name="small_button[text]" value="<?=isset($small_button['text']) ? $small_button['text'] : ''?>">
                            </p>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100 hidden mb-0" id="small_button_product_order"><label>Выбор продукта</label>
                                <div class="select-wrap">
                                    <select name="small_button[product_order]">
                                        <option value="0">Не выбран</option>
                                        <?php foreach($product_list as $product):?>
                                            <option value="<?=$product['product_id'];?>"<?php if(isset($small_button['product_order']) && $small_button['product_order'] == $product['product_id']) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100 hidden mb-0" id="small_button_rate"><label>Выбор тарифа</label>
                                <select name="small_button[rate][]" multiple="multiple" class="multiple-select">
                                    <option value="0">Не выбран</option>
                                    <?php foreach($product_list as $product):?>
                                        <option value="<?=$product['product_id'];?>"<?php if(isset($small_button['rate']) && in_array($product['product_id'], $small_button['rate'])) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>

                            <div class="width-100 hidden mb-0" id="small_button_product_desc">
                                <label>Выбор продукта</label>
                                <div class="select-wrap">
                                    <select name="small_button[product_desc]">
                                        <option value="0">Не выбран</option>
                                        <?php foreach($product_list as $product):?>
                                            <option value="<?=$product['product_id'];?>"<?php if(isset($small_button['product_desc']) && $product['product_id'] == $small_button['product_desc']) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>

                            <div id="small_button_url" class="hidden mb-0">
                                <p><label>Url ссылки</label>
                                    <input type="text" name="small_button[your_url]" value="<?=$small_button['your_url'];?>" placeholder="http://">
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Кнопки покупок по типам проверок домашних заданий</h4>
                        </div>
                    </div>
                    <div class="row-line mt-20">
                        <div class="col-1-2">
                            <div class="width-100"><label>Купить проверку куратором</label>
                                <div class="select-wrap">
                                    <select name="by_button_curator_hw[type]">
                                        <option value="<?=Training::BY_BUTTON_TYPE_NOT_BUTTON;?>"<?php if($by_button_curator_hw['type'] == Training::BY_BUTTON_TYPE_NOT_BUTTON) echo ' selected="selected"';?> data-show_off="by_button_curator_hw_text">Нет кнопки</option>
                                        <option value="<?=Training::BY_BUTTON_TYPE_PRODUCT_ORDER;?>" data-show_on="by_button_curator_hw_product_order"<?php if($by_button_curator_hw['type'] == Training::BY_BUTTON_TYPE_PRODUCT_ORDER) echo ' selected="selected"';?>>Заказ продукта</option>
                                        <option value="<?=Training::BY_BUTTON_TYPE_RATE;?>" data-show_on="by_button_curator_hw_rate"<?php if($by_button_curator_hw['type'] == Training::BY_BUTTON_TYPE_RATE) echo ' selected="selected"';?>>Выбор тарифа (несколько продуктов)</option>
                                        <option value="<?=Training::BY_BUTTON_TYPE_YOUR_URL;?>" data-show_on="by_button_curator_hw_url"<?php if($by_button_curator_hw['type'] == Training::BY_BUTTON_TYPE_YOUR_URL) echo ' selected="selected"';?>>Свой Url</option>
                                    </select>
                                </div>

                                <div class="width-100" id="by_button_curator_hw_text"><label>Текст кнопки</label>
                                    <input type="text" name="by_button_curator_hw[text]" value="<?=isset($by_button_curator_hw['text']) ? $by_button_curator_hw['text'] : '';?>">
                                </div>
                            </div>

                            <div class="width-100 hidden" id="by_button_curator_hw_product_order">
                                <label>Выбор продукта</label>
                                <div class="select-wrap">
                                    <select name="by_button_curator_hw[product_order]">
                                        <option value="0">Не выбран</option>
                                        <?php foreach($product_list as $product):?>
                                            <option value="<?=$product['product_id'];?>"<?php if(isset($by_button_curator_hw['product_order']) && $by_button_curator_hw['product_order'] == $product['product_id']) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100 hidden" id="by_button_curator_hw_rate"><label>Выбор тарифа</label>
                                <select name="by_button_curator_hw[rate][]" multiple="multiple" class="multiple-select">
                                    <option value="0">Не выбран</option>
                                    <?php foreach($product_list as $product):?>
                                        <option value="<?=$product['product_id'];?>"<?php if(isset($by_button_curator_hw['rate']) && in_array($product['product_id'], $by_button_curator_hw['rate'])) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>

                            <div id="by_button_curator_hw_url" class="mb-20 hidden">
                                <p><label>Url ссылки</label>
                                    <input type="text" name="by_button_curator_hw[your_url]" value="<?php if(isset($by_button_curator_hw['your_url'])) echo  $by_button_curator_hw['your_url']?>" placeholder="http://">
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row-line mt-0">

                        <div class="col-1-2">
                            <div class="width-100"><label>Купить автоматическую проверку</label>
                                <div class="select-wrap">
                                    <select name="by_button_autocheck_hw[type]">
                                        <option value="<?=Training::BY_BUTTON_TYPE_NOT_BUTTON;?>"<?php if($by_button_autocheck_hw['type'] == Training::BY_BUTTON_TYPE_NOT_BUTTON) echo ' selected="selected"';?> data-show_off="by_button_autocheck_hw_text">Нет кнопки</option>
                                        <option value="<?=Training::BY_BUTTON_TYPE_PRODUCT_ORDER;?>" data-show_on="by_button_autocheck_hw_product_order"<?php if($by_button_autocheck_hw['type'] == Training::BY_BUTTON_TYPE_PRODUCT_ORDER) echo ' selected="selected"';?>>Заказ продукта</option>
                                        <option value="<?=Training::BY_BUTTON_TYPE_RATE;?>" data-show_on="by_button_autocheck_hw_rate"<?php if($by_button_autocheck_hw['type'] == Training::BY_BUTTON_TYPE_RATE) echo ' selected="selected"';?>>Выбор тарифа (несколько продуктов)</option>
                                        <option value="<?=Training::BY_BUTTON_TYPE_YOUR_URL;?>" data-show_on="by_button_autocheck_hw_url"<?php if($by_button_autocheck_hw['type'] == Training::BY_BUTTON_TYPE_YOUR_URL) echo ' selected="selected"';?>>Свой Url</option>
                                    </select>
                                </div>

                                <div class="width-100 mt-20" id="by_button_autocheck_hw_text"><label>Текст кнопки</label>
                                    <input type="text" name="by_button_autocheck_hw[text]" value="<?=isset($by_button_autocheck_hw['text']) ? $by_button_autocheck_hw['text'] : '';?>">
                                </div>
                            </div>

                            <div class="width-100 hidden" id="by_button_autocheck_hw_product_order">
                                <label>Выбор продукта</label>
                                <div class="select-wrap">
                                    <select name="by_button_autocheck_hw[product_order]">
                                        <option value="0">Не выбран</option>
                                        <?php foreach($product_list as $product):?>
                                            <option value="<?=$product['product_id'];?>"<?php if(isset($by_button_autocheck_hw['product_order']) && $by_button_autocheck_hw['product_order'] == $product['product_id']) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100 hidden" id="by_button_autocheck_hw_rate"><label>Выбор тарифа</label>
                                <select name="by_button_autocheck_hw[rate][]" multiple="multiple" class="multiple-select">
                                    <option value="0">Не выбран</option>
                                    <?php foreach($product_list as $product):?>
                                        <option value="<?=$product['product_id'];?>"<?php if(isset($by_button_autocheck_hw['rate']) && in_array($product['product_id'], $by_button_autocheck_hw['rate'])) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>

                            <div id="by_button_autocheck_hw_url" class="hidden mb-20">
                                <p><label>Url ссылки</label>
                                    <input type="text" name="by_button_autocheck_hw[your_url]" value="<?php if(isset($by_button_autocheck_hw['your_url'])) echo  $by_button_autocheck_hw['your_url']?>" placeholder="http://">
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row-line mt-0">
                        <div class="col-1-2">
                            <div class="width-100"><label>Купить самостоятельную проверку</label>
                                <div class="select-wrap">
                                    <select name="by_button_self_hw[type]">
                                        <option value="<?=Training::BY_BUTTON_TYPE_NOT_BUTTON;?>"<?php if($by_button_self_hw['type'] == Training::BY_BUTTON_TYPE_NOT_BUTTON) echo ' selected="selected"';?> data-show_off="by_button_self_hw_text">Нет кнопки</option>
                                        <option value="<?=Training::BY_BUTTON_TYPE_PRODUCT_ORDER;?>" data-show_on="by_button_self_hw_product_order"<?php if($by_button_self_hw['type'] == Training::BY_BUTTON_TYPE_PRODUCT_ORDER) echo ' selected="selected"';?>>Заказ продукта</option>
                                        <option value="<?=Training::BY_BUTTON_TYPE_RATE;?>" data-show_on="by_button_self_hw_rate"<?php if($by_button_self_hw['type'] == Training::BY_BUTTON_TYPE_RATE) echo ' selected="selected"';?>>Выбор тарифа (несколько продукта)</option>
                                        <option value="<?=Training::BY_BUTTON_TYPE_YOUR_URL;?>" data-show_on="by_button_self_hw_url"<?php if($by_button_self_hw['type'] == Training::BY_BUTTON_TYPE_YOUR_URL) echo ' selected="selected"';?>>Свой Url</option>
                                    </select>
                                </div>

                                <div class="width-100 mt-20" id="by_button_self_hw_text"><label>Текст кнопки</label>
                                    <input type="text" name="by_button_self_hw[text]" value="<?=isset($by_button_self_hw['text']) ? $by_button_self_hw['text'] : '';?>">
                                </div>
                            </div>

                            <div class="width-100 hidden" id="by_button_self_hw_product_order">
                                <label>Выбор продукта</label>
                                <div class="select-wrap">
                                    <select name="by_button_self_hw[product_order]">
                                        <option value="0">Не выбран</option>
                                        <?php foreach($product_list as $product):?>
                                            <option value="<?=$product['product_id'];?>"<?php if(isset($by_button_self_hw['product_order']) && $by_button_self_hw['product_order'] == $product['product_id']) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100 hidden" id="by_button_self_hw_rate"><label>Выбор тарифа</label>
                                <select name="by_button_self_hw[rate][]" multiple="multiple" class="multiple-select">
                                    <option value="0">Не выбран</option>
                                    <?php foreach($product_list as $product):?>
                                        <option value="<?=$product['product_id'];?>"<?php if(isset($by_button_self_hw['rate']) && in_array($product['product_id'], $by_button_self_hw['rate'])) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>

                            <div id="by_button_self_hw_url" class="hidden">
                                <p><label>Url ссылки</label>
                                    <input type="text" name="by_button_self_hw[your_url]" value="<?php if(isset($by_button_self_hw['your_url'])) echo  $by_button_self_hw['your_url']?>" placeholder="http://">
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
                                    <label class="custom-radio"><input name="duration_type" type="radio" value="1"<?php if($training['duration_type'] == 1) echo ' checked';?>><span>Считать</span></label>
                                    <label class="custom-radio"><input name="duration_type" type="radio" value="2"<?php if($training['duration_type'] == 2) echo ' checked';?> data-show_on="duration"><span>Написать</span></label>
                                </span>
                            </div>

                            <p class="width-100 hidden" id="duration"><label>Продолжительность</label>
                                <input type="text" name="duration" value="<?=$training['duration'];?>">
                            </p>

                            <div class="width-100"><label>Сложность курса</label>
                                <div class="select-wrap">
                                    <select name="complexity">
                                        <option value="1"<?php if($training['complexity'] == 1) echo ' selected="selected"';?>>Лёгкий</option>
                                        <option value="2"<?php if($training['complexity'] == 2) echo ' selected="selected"';?>>Средний</option>
                                        <option value="3"<?php if($training['complexity'] == 3) echo ' selected="selected"';?>>Сложный</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Количество уроков</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="count_lessons_type" type="radio" value="1"<?php if($training['count_lessons_type'] == 1) echo ' checked';?>><span>Считать</span></label>
                                    <label class="custom-radio"><input name="count_lessons_type" type="radio" value="2"<?php if($training['count_lessons_type'] == 2) echo ' checked';?> data-show_on="count_lessons"><span>Написать</span></label>
                                </span>
                            </div>

                            <p class="width-100 hidden" id="count_lessons"><label>Уроков</label>
                                <input type="text" name="count_lessons" value="<?=$training['count_lessons'];?>">
                            </p>

                            <div class="width-100"><label>Сортировка уроков</label>
                                <div class="select-wrap">
                                    <select name="sort_lessons">
                                        <option value="1"<?php if($training['sort_lessons'] == 1) echo ' selected="selected"';?>>По возрастанию</option>
                                        <option value="2"<?php if($training['sort_lessons'] == 2) echo ' selected="selected"';?>>По убыванию</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Внешний вид в каталоге</h4>
                        </div>

                        <div class="col-1-2">

                            <div class="width-100"><label>Описание тренинга</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_desc" type="radio" value="1"<?php if($training['show_desc']) echo ' checked';?>><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_desc" type="radio" value="0"<?php if(!$training['show_desc']) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>

                            <div class="width-100"><label>Количество уроков</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_count_lessons" type="radio" value="1"<?php if($training['show_count_lessons']) echo ' checked';?>><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_count_lessons" type="radio" value="0"<?php if(!$training['show_count_lessons']) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>

                            <div class="width-100"><label>Прогресс прохождения</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_progress2list" type="radio" value="1"<?php if($training['show_progress2list']) echo ' checked';?>><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_progress2list" type="radio" value="0"<?php if(!$training['show_progress2list']) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>
                            <div class="width-100"><label>Сертификат</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="sertificate[show_sert]" type="radio" value="1"<?php if(isset($sertificate['show_sert']) && $sertificate['show_sert']==1) echo ' checked';?>><span>Показать</span></label>
                                    <label class="custom-radio"><input name="sertificate[show_sert]" type="radio" value="0"<?php if(!isset($sertificate['show_sert']) || $sertificate['show_sert']==0) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Дата начала</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_start_date" type="radio" value="1"<?php if($training['show_start_date']) echo ' checked';?>><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_start_date" type="radio" value="0"<?php if(!$training['show_start_date']) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>

                            <div class="width-100"><label>Сложность курса</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_complexity" type="radio" value="1"<?php if($training['show_complexity']) echo ' checked';?>><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_complexity" type="radio" value="0"<?php if(!$training['show_complexity']) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>

                            <div class="width-100"><label>Стоимость</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_price" type="radio" value="1"<?php if($training['show_price']) echo ' checked';?> data-show_on="show_price"><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_price" type="radio" value="0"<?php if(!$training['show_price']) echo ' checked';?>><span>Нет</span></label>
                                </span>

                                <p class="hidden" id="show_price">
                                    <input type="text" name="price" value="<?=$training['price'];?>">
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Внешний вид в списке уроков</h4>
                        </div>

                        <? /*
                        <div class="col-1-2">
                            <div class="width-100"><label>Виджет о курсе</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_widget_training" type="radio" value="1"<?php if($training['show_widget_training']) echo ' checked';?>><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_widget_training" type="radio" value="0"<?php if(!$training['show_widget_training']) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>
                        </div>
                    */ ?>

                        <div class="col-1-2">
                            <div class="width-100"><label>Виджет с прогрессом</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_widget_progress" type="radio" value="1"<?php if($training['show_widget_progress']) echo ' checked';?>><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_widget_progress" type="radio" value="0"<?php if(!$training['show_widget_progress']) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-1-2">
                            <div class="width-100"><label>Обложки уроков на мобильных</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_lesson_cover_2mobile" type="radio" value="1"<?php if($training['show_lesson_cover_2mobile']) echo ' checked';?>><span>Показать</span></label>
                                    <label class="custom-radio"><input name="show_lesson_cover_2mobile" type="radio" value="0"<?php if(!$training['show_lesson_cover_2mobile']) echo ' checked';?>><span>Скрыть</span></label>
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
                                        <option value="1"<?php if($training['lessons_tmpl'] == 1) echo ' selected="selected"';?>>Обычный</option>
                                        <option value="2"<?php if($training['lessons_tmpl'] == 2) echo ' selected="selected"';?>>Широкий</option>
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
                            <div class="width-100"><label>Показать</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_in_lk2not_buy" type="radio" value="1"<?php if($training['show_in_lk2not_buy']) echo ' checked';?> data-show_on="text_in_lk2not_buy"><span>Да</span></label>
                                    <label class="custom-radio"><input name="show_in_lk2not_buy" type="radio" value="0"<?php if(!$training['show_in_lk2not_buy']) echo ' checked';?>><span>Нет</span></label>
                                </span>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <p class="width-100 hidden" id="text_in_lk2not_buy"><label>Надпись, если не куплен</label>
                                <input type="text" name="text_in_cabinet2not_buy" value="<?=$training['text_in_lk2not_buy'];?>">
                            </p>
                        </div>
                    </div>*/?>
                </div>

                <?/*<!-- 5 ВКЛАДКА ЗАЩИТА-->
                <div>
                <div class="row-line" style=";background-color: #eeeeff;">
                    <div class="col-1-1">
                        <h4>Заполнение профиля для доступа</h4>
                    </div>

                    <div class="col-1-2">
                        <div class="width-100"><label>Требовать подтверждение телефона</label>
                            <span class="custom-radio-wrap">
                                <label class="custom-radio"><input name="confirm_phone" type="radio" value="1"<?php if($training['confirm_phone']) echo ' checked';?>><span>Да</span></label>
                                <label class="custom-radio"><input name="confirm_phone" type="radio" value="0"<?php if(!$training['confirm_phone']) echo ' checked';?>><span>Нет</span></label>
                            </span>
                        </div>
                    </div>

                    <div class="col-1-2">
                        <div class="width-100"><label>Запрашивать привязку Telegram</label>
                            <span class="custom-radio-wrap">
                                <label class="custom-radio"><input name="binding_tg" type="radio" value="1"<?php if($training['binding_tg']) echo ' checked';?>><span>Да</span></label>
                                <label class="custom-radio"><input name="binding_tg" type="radio" value="0"<?php if(!$training['binding_tg']) echo ' checked';?>><span>Нет</span></label>
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
                                <label class="custom-radio"><input name="show_watermark_phone" type="radio" value="1"<?php if($training['show_watermark_phone']) echo ' checked';?>><span>Да</span></label>
                                <label class="custom-radio"><input name="show_watermark_phone" type="radio" value="0"<?php if(!$training['show_watermark_phone']) echo ' checked';?>><span>Нет</span></label>
                            </span>
                        </div>
                    </div>

                    <div class="col-1-2">
                        <div class="width-100"><label title="Для вывода водного знака нужно сконфигурировать свой плеер. Подробнее в справке.">Показывать e-mail</label>
                            <span class="custom-radio-wrap">
                                <label class="custom-radio"><input name="show_watermark_email" type="radio" value="1"<?php if($training['show_watermark_email']) echo ' checked';?>><span>Да</span></label>
                                <label class="custom-radio"><input name="show_watermark_email" type="radio" value="0"<?php if(!$training['show_watermark_email']) echo ' checked';?>>><span>Нет</span></label>
                            </span>
                        </div>
                    </div>
                </div>
            </div>*/?>

                <!-- 6 ВКЛАДКА SEO -->
                <div>
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>SEO</h4>
                        </div>

                        <div class="col-1-2">
                            <p class="width-100"><label>Title</label>
                                <input type="text" name="title" placeholder="Title тренинга" value="<?=$training['title'];?>">
                            </p>

                            <p class="width-100"><label>Meta Description</label>
                                <textarea name="meta_desc" rows="3" cols="40"><?=$training['meta_desc'];?></textarea>
                            </p>
                        </div>

                        <div class="col-1-2">
                            <p class="width-100"><label>Alias</label>
                                <input type="text" name="alias" placeholder="Алиас тренинга" value="<?=$training['alias'];?>">
                            </p>

                            <p class="width-100"><label>Meta Keyword</label>
                                <textarea name="meta_keys" rows="3" cols="40"><?=$training['meta_keys'];?></textarea>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- 6 ВКЛАДКА УВЕДОМЛЕНИЯ -->
                <div>
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Куратору о выполнении ДЗ</h4>
                        </div>

                        <div class="col-1-1 mb-0">
                            <div class="width-100">
                                <label>Включить уведомление</label>
                                <span class="custom-radio-wrap">
                                <?php if(isset($training['send_email_to_curator'])):?>
                                    <label class="custom-radio"><input name="send_email_to_curator" type="radio" value="1"<?php if($training['send_email_to_curator']) echo ' checked';?> data-show_on="show-send-email-to-curator-letter"><span>Да</span></label>
                                    <label class="custom-radio"><input name="send_email_to_curator" type="radio" value="0"<?php if(!$training['send_email_to_curator']) echo ' checked';?>><span>Нет</span></label>
                                <?php else:?>
                                    <label class="custom-radio"><input name="send_email_to_curator" type="radio" value="1" checked data-show_on="show-send-email-to-curator-letter"><span>Да</span></label>
                                    <label class="custom-radio"><input name="send_email_to_curator" type="radio" value="0"><span>Нет</span></label>
                                    <?php endif;?>
                                </span>
                            </div>
                            <div class="hidden mb-20" id="show-send-email-to-curator-letter">
                                <div class="row">
                                    <div class="col-1-2">
                                        <div class="width-100"><label>Отправлять всем кураторам, если не назначен персональный</label>
                                            <span class="custom-radio-wrap">
                                    <?php if(isset($training['send_email_to_all_curators'])):?>
                                                <label class="custom-radio"><input name="send_email_to_all_curators" type="radio" value="1"<?php if($training['send_email_to_all_curators']) echo ' checked';?>><span>Да</span></label>
                                        <label class="custom-radio"><input name="send_email_to_all_curators" type="radio" value="0"<?php if(!$training['send_email_to_all_curators']) echo ' checked';?>><span>Нет</span></label>
                                                <?php else:?>
                                                <label class="custom-radio"><input name="send_email_to_all_curators" type="radio" value="1" checked><span>Да</span></label>
                                        <label class="custom-radio"><input name="send_email_to_all_curators" type="radio" value="0"><span>Нет</span></label>
                                                <?php endif;?>
                                    </span>
                                        </div>
                                    </div>
                                </div>
                                <p class="label"><label>Тема письма</label><input type="text" name="subject_letter_to_curator" value="<?php if(isset($training['subject_letter_to_curator']) && !empty($training['subject_letter_to_curator'])) echo $training['subject_letter_to_curator']; else echo '[Проверить ДЗ] Ученик выполнил домашнее задание. Требуется ваше действие.'?>"></p>
                                <p><label>Текст письма</label><textarea name="letter_to_curator" class="editor" rows="6" style="width:100%"><?php if(isset($training['letter_to_curator']) && !empty($training['letter_to_curator'])) echo $training['letter_to_curator']; else echo '<p>Здравствуйте, [CURATOR]!</p>
<p>Пользователь выполнил домашнюю работу. Требуется ваше действие.</p>
<p>Информация:</p>
<p>Пользователь: [NAME]</p>
<p>Тренинг: [TRAINING]</p>
<p>Урок: [LESSON]</p>
<p>&nbsp;</p>
<p>&gt;&gt; <a href="[LINK]">Проверить</a></p>
<p>---<br />С уважением,<br />ваш робот-помощник</p>'?></textarea></p>
                                <p>Переменные для подстановки:<br />
                                    [LINK] - ссылка <br />
                                    [TRAINING] - название тренинга<br />
                                    [LESSON] - название урока<br />
                                    [NAME] - имя клиента<br />
                                    [SURNAME] - фамилия клиента<br />
                                    [EMAIL] - Email клиента<br />
                                    [CURATOR] - Имя куратора
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Пользователю о проверке ДЗ</h4>
                        </div>

                        <div class="col-1-1 mb-10">
                            <div class="width-100">
                                <label>Включить уведомление</label>
                                <span class="custom-radio-wrap">
                                <?php if(isset($training['send_email_to_user'])):?>
                                    <label class="custom-radio"><input name="send_email_to_user" type="radio" value="1"<?php if($training['send_email_to_user']) echo ' checked';?> data-show_on="show-send-email-to-user-letter"><span>Да</span></label>
                                    <label class="custom-radio"><input name="send_email_to_user" type="radio" value="0"<?php if(!$training['send_email_to_user']) echo ' checked';?>><span>Нет</span></label>
                                <?php else:?>
                                    <label class="custom-radio"><input name="send_email_to_user" type="radio" value="1" checked data-show_on="show-send-email-to-user-letter"><span>Да</span></label>
                                    <label class="custom-radio"><input name="send_email_to_user" type="radio" value="0"><span>Нет</span></label>
                                    <?php endif;?>
                                </span>
                            </div>
                            <div class="hidden" id="show-send-email-to-user-letter">
                                <p class="label"><label>Тема письма</label><input type="text" name="subject_letter_to_user" value="<?php if(isset($training['subject_letter_to_user']) && !empty($training['subject_letter_to_user'])) echo $training['subject_letter_to_user']; else echo '[Проверено] Домашнее задание к уроку'?>"></p>
                                <p><label>Текст письма</label><textarea name="letter_to_user" class="editor" rows="6" style="width:100%"><?php if(isset($training['letter_to_user']) && !empty($training['letter_to_user'])) echo $training['letter_to_user']; else echo '<p>Здравствуйте, [NAME]!</p>
<p>Ваше домашнее задание проверено. Вероятно, требуется действие.</p>
<p>Статус: [STATUS]</p>
<p>Тренинг: [TRAINING]</p>
<p>Урок: [LESSON]</p>
<p>Комментарий куратора: <br />[MESSAGE]</p>
<p>&gt;&gt; <a href="[LINK]">Перейти к уроку</a></p>
<p><br />Хорошего дня!<br />--<br />С уважением,<br />команда сайта и ваш куратор [CURATOR]</p>'?></textarea></p>
                                <p>Переменные для подстановки:<br />
                                    [LINK] - ссылка <br />
                                    [TRAINING] - название тренинга<br />
                                    [LESSON] - название урока<br />
                                    [NAME] - имя клиента<br />
                                    [SURNAME] - фамилия клиента<br />
                                    [CURATOR] - Имя куратора<br />
                                    [MESSAGE] - Сообщение куратора, если есть в ответе<br />
                                    [STATUS] - Статус домашнего задания - выполнено, отклонено
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-1-1 mb-0">
                            <h4>Пользователю об открытии нового урока</h4>
                        </div>
                        
                        <div class="col-1-1 mb-10">
                            <p>Отправляется при наступлении даты открытия урока и после проверки куратором, если проверяемый урок был стоповым.</p>
                            <div class="width-100">
                                <label>Включить уведомление</label>
                                <span class="custom-radio-wrap">
                                <?php if(isset($training['send_email_to_user_for_open_lesson'])):?>
                                    <label class="custom-radio"><input name="send_email_to_user_for_open_lesson" type="radio" value="1"<?php if($training['send_email_to_user_for_open_lesson']) echo ' checked';?> data-show_on="show-send-email-to-user-letter-for-open-lesson"><span>Да</span></label>
                                    <label class="custom-radio"><input name="send_email_to_user_for_open_lesson" type="radio" value="0"<?php if(!$training['send_email_to_user_for_open_lesson']) echo ' checked';?>><span>Нет</span></label>
                                <?php else:?>
                                    <label class="custom-radio"><input name="send_email_to_user_for_open_lesson" type="radio" value="1" checked data-show_on="show-send-email-to-user-letter-for-open-lesson"><span>Да</span></label>
                                    <label class="custom-radio"><input name="send_email_to_user_for_open_lesson" type="radio" value="0"><span>Нет</span></label>
                                    <?php endif;?>
                                </span>
                            </div>

                            <div class="hidden" id="show-send-email-to-user-letter-for-open-lesson">
                                <p class="label"><label>Тема письма</label><input type="text" name="subject_letter_to_user_for_open_lesson" value="<?php if(isset($training['subject_letter_to_user_for_open_lesson']) && !empty($training['subject_letter_to_user_for_open_lesson'])) echo $training['subject_letter_to_user_for_open_lesson']; else echo '[Новый урок] Открыт доступ к новому уроку';?>"></p>
                                <p><label>Текст письма</label><textarea name="letter_to_user_for_open_lesson" class="editor" rows="6" style="width:100%"><?php if(isset($training['letter_to_user_for_open_lesson']) && !empty($training['letter_to_user_for_open_lesson'])) echo $training['letter_to_user_for_open_lesson']; else echo '<p>Здравствуйте, [NAME]!</p>
<p>Вам открыт доступ к следующему уроку.</p>
<p>Тренинг: [TRAINING]</p>
<p>Урок: [LESSON]</p>
<p>&nbsp;</p>
<p>&gt;&gt; <a href="[LINK]" target="_blank" rel="noopener">Перейти к просмотру</a></p>
<p>&nbsp;</p>
<p>Хорошего дня!</p>
<p>--</p>
<p>С уважением,<br />команда проекта</p>'?></textarea></p>
                                <p>Переменные для подстановки:<br />
                                    [LINK] - ссылка <br />
                                    [TRAINING] - название тренинга<br />
                                    [LESSON] - название урока<br />
                                    [NAME] - имя клиента<br />
                                    [SURNAME] - фамилия клиента<br />
                                </p>
                            </div>
                        </div>

                    </div>

                </div>
                
                <!-- СЕРТИФИКАТЫ   -->
                <div>
                    <div class="row-line">
                        <div class="col-1-1">
                        <h4>Бланк сертификата</h4>
                            <div class="row-line">
                                
                                <div class="col-1-2">
                                    
                                    
                                    <div class="width-100"><label>Загрузить бланк</label>
                                        <input type="file" name="sertificate_file">
                                    </div>

                                    <?php if(isset($sert_picture)):?>
                                        <div class="width-100 del_img_wrap">
                                            <img src="/images/training/sertificate/<?=$sertificate['template_file']?>" alt="" width="210">
                                        </div>
                                        ширина: <?=$size_sert[0];?>px высота:  <?=$size_sert[1];?>px
                                    <?php endif;?>

                                    <div class="width-100">
                                        <input type="hidden" name="current_sert" value="<?=$sertificate['template_file'];?>">
                                    </div>
                                </div>
                                <div class="col-1-2">
                                    <div class="width-100"><label title="Заголовок который выводится в виджете у пользователя, для примера можно написать Ваш диплом или Аттестат, Свидетельство и т.д.">Заголовок виджета</label>
                                        <input type="text" name="sertificate[header]" value="<?=isset($sertificate['header']) ? $sertificate['header']: '';?>" placeholder="Ваш диплом">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-1-1">
                            <h4>Координаты надписей</h4>
                            <div class="row-line">
                                <div class="col-1-3">
                                    <p class="width-100"><label>Имя фамилия (center/top), px</label>
                                        <input type="text" name="sertificate[fio_koord]" value="<?=isset($sertificate['fio_koord']) ? $sertificate['fio_koord']: '';?>" placeholder="пример 200/200">
                                    </p>
                                </div>
                                <div class="col-1-6">
                                    <p class="width-100"><label>размер шрифта, pt</label>
                                        <input type="text" name="sertificate[fio_koord_fs]" value="<?=isset($sertificate['fio_koord_fs']) ? $sertificate['fio_koord_fs'] : '';?>" placeholder="18">
                                    </p>
                                </div>
                                <div class="col-1-3">
                                    <label>Выбор шрифта</label>
                                    <div class="select-wrap">
                                        <select name="sertificate[fio_koord_font]">
                                            <?php if ($fonts_cert):
                                                foreach ($fonts_cert as $font):?>
                                                    <option value="<?=$font;?>"<?php if(isset($font) && $font==$sertificate['fio_koord_font']) echo ' selected="selected"';?>><?=$font;?></option>
                                                <?php endforeach;
                                            endif;?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row-line">
                                <div class="col-1-3">
                                    <p class="width-100"><label>Номер сертификата (left/top), px</label>
                                        <input type="text" name="sertificate[number_koord]" value="<?=isset($sertificate['number_koord']) ? $sertificate['number_koord'] : '';?>" placeholder="пример 100/100">
                                    </p>
                                </div>
                                <div class="col-1-6">
                                    <p class="width-100"><label>размер шрифта, pt</label>
                                        <input type="text" name="sertificate[number_koord_fs]" value="<?=isset($sertificate['number_koord_fs']) ? $sertificate['number_koord_fs'] : '';?>" placeholder="18">
                                    </p>
                                </div>
                                <div class="col-1-3">
                                    <label>Выбор шрифта</label>
                                    <div class="select-wrap">
                                        <select name="sertificate[number_koord_font]">
                                            <?php if ($fonts_cert):
                                                foreach ($fonts_cert as $font):?>
                                                    <option value="<?=$font;?>"<?php if(isset($font) && $font==$sertificate['number_koord_font']) echo ' selected="selected"';?>><?=$font;?></option>
                                                <?php endforeach;
                                            endif;?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row-line">
                                <div class="col-1-3">
                                    <p class="width-100"><label>Дата выдачи (left/top), px</label>
                                        <input type="text" name="sertificate[date_koord]" value="<?=isset($sertificate['date_koord']) ? $sertificate['date_koord'] : '';?>" placeholder="пример 100/100">
                                    </p>
                                </div>
                                <div class="col-1-6">
                                    <p class="width-100"><label>размер шрифта, pt</label>
                                        <input type="text" name="sertificate[date_koord_fs]" value="<?=isset($sertificate['date_koord_fs']) ? $sertificate['date_koord_fs'] : '';?>" placeholder="18">
                                    </p>
                                </div>
                                <div class="col-1-3">
                                    <label>Выбор шрифта</label>
                                    <div class="select-wrap">
                                        <select name="sertificate[date_koord_font]">
                                            <?php if ($fonts_cert):
                                                foreach ($fonts_cert as $font):?>
                                                    <option value="<?=$font;?>"<?php if(isset($font) && $font==$sertificate['date_koord_font']) echo ' selected="selected"';?>><?=$font;?></option>
                                                <?php endforeach;
                                            endif;?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row-line">
                                <div class="col-1-2">
                                    <div class="width-100">
                                        <a target="_blank" class="button red-link" href="/admin/training/previewcertificate/<?=$training['training_id'];?>">Предпросмотр</a>
                                    </div>
                                </div>
                                <div class="col-1-2">
                                    <div class="width-100">
                                        <a href="javascript:void(0)" onclick="javascript:window.open('/lib/file_man/filemanager/dialog.php?type=2&fldr=training/sertificate/fonts','okno', 'width=845, height=400, status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes')" class="btn iframe-btn" type="button">Загрузить свой шрифт</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="reference-link">
                        <a class="button-blue-rounding" target="_blank" href="https://lk.school-master.ru/rdr/45"><i class="icon-info"></i>Справка</a>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    </form>

    <div class="buttons-under-form">
        <p class="button-delete">
            <a onclick="return confirm('Вы уверены?')" href="/admin/training/del/<?=$training['training_id'];?>?token=<?=$_SESSION['admin_token'];?>"><i class="icon-remove"></i>Удалить тренинг</a>
        </p>

        <form class="copy-but" action="/admin/training/" method="POST">
            <input type="hidden" name="training_id" value="<?=$training['training_id'];?>">
            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
            <button class="button-copy-2" type="submit" name="copy"><i class="icon-copy"></i>Копировать тренинг</button>
        </form>

        <form class="copy-but" action="/admin/training/" method="POST"><input type="hidden" name="training_id" value="<?=$training['training_id'];?>">
            <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
<!--            <button class="button-copy-2" type="submit" name="exportcsv"><i class="icon-copy"></i> Выгрузить тренинг в CSV</button>-->
        </form> 
    </div>

    <?php // TODO longtime это тоже какое-то решение которое надо по моему на аякс переделать полностью и везде #удалениекартинки ?>
    <form action="/admin/delimg/<?=$training['training_id'];?>" id="del_full_img" method="POST">
        <input type="hidden" name="path" value="images/training/<?=$training['full_cover'];?>">
        <input type="hidden" name="page" value="admin/training/edit/<?=$training['training_id'];?>">
        <input type="hidden" name="table" value="training">
        <input type="hidden" name="name" value="full_cover">
        <input type="hidden" name="where" value="training_id">
        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    </form>

    <form action="/admin/delimg/<?=$training['training_id'];?>" id="del_img" method="POST">
        <input type="hidden" name="path" value="images/training/<?=$training['cover'];?>">
        <input type="hidden" name="page" value="admin/training/edit/<?=$training['training_id'];?>">
        <input type="hidden" name="table" value="training">
        <input type="hidden" name="name" value="cover">
        <input type="hidden" name="where" value="training_id">
        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    </form>

    <?php require_once (__DIR__ . '/events_finish/index.php');
    require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>

<style>
    .mce-tinymce {
        width: 100% !important;
    }
    #modal_edit_give_access .uk-modal-dialog{
        width: 854px;
    }
</style>
</body>
</html>