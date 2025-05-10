<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Редактировать тренинг (ID: <?php echo $course['course_id'];?>)</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
        </div>
    </div>
    <form action="" method="POST" enctype="multipart/form-data">

        <ul class="breadcrumb">
            <li>
                <a href="/admin">Дашбоард</a>
            </li>
            <li>
                <a href="/admin/courses/">Тренинги</a>
            </li>
            <li>Редактировать тренинг</li>
        </ul>
        <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
<div class="traning-top">
    <h3 class="traning-title"><?php echo $course['name'];?></h3>
    <ul class="nav_button">
        <li><input type="submit" name="savecourse" value="Сохранить" class="button save button-white font-bold"></li>
        <li class="nav_button__last"><a class="button red-link" href="/admin/courses/">Закрыть</a></li>
    </ul>
</div>
<div class="tabs">
    <ul class="overflow-container tabs-ul">
        <li>Основное</li>
        <li>Доступ</li>
        <li>Кнопки покупки</li>
        <li>Внешний вид</li>
        <li>Блоки уроков</li>
        <li>SEO</li>
    </ul>
    <div class="admin_form">
        <!-- 1 вкладка -->
        <div>
            <div class="row-line">
                <div class="col-1-2">
                    <p><label>Название: </label><input type="text" name="name" value="<?php echo $course['name'];?>" placeholder="Название курса" required="required"></p>


                    <p><label>Статус: </label>
                        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="status" type="radio" value="1"<?php if($course['status'] == 1) echo ' checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="status" type="radio" value="0"<?php if($course['status'] != 1) echo ' checked';?>><span>Откл</span></label>
                        </span>
                    </p>

                    <p><label>Показывать в общем списке курсов: </label>
                        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="show_in_main" type="radio" value="1"<?php if(isset($course['show_in_main']) && $course['show_in_main'] == 1) echo ' checked';?>><span>Показать</span></label>
                        <label class="custom-radio"><input name="show_in_main" type="radio" value="0"<?php if(isset($course['show_in_main']) && $course['show_in_main'] == 0) echo ' checked';?>><span>Скрыть</span></label>
                        </span>
                    </p>

                    <p><label>Бесплатный тренинг? </label>
                        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="is_free" type="radio" value="1"<?php if(isset($course['is_free']) && $course['is_free'] == 1) echo ' checked';?>><span>Да</span></label>
                        <label class="custom-radio"><input name="is_free" type="radio" value="0"<?php if(isset($course['is_free']) && $course['is_free'] == 0) echo ' checked';?>><span>Нет</span></label>
                        </span>
                    </p>

                    <div>
                        <label>Категория: </label>
                        <div class="select-wrap">
                        <select name="cat_id">
                            <option value="0">Без категории</option>
                            <?php $cat_list = Course::getCourseCatFromList();
                            if($cat_list):
                            foreach($cat_list as $cat):?>
                            <option value="<?php echo $cat['cat_id'];?>"<?php if($cat['cat_id'] == $course['cat_id']) echo ' selected="selected"';?>><?php echo $cat['name'];?></option>
                            <?php endforeach;
                            endif;?>
                        </select>
                    </div>
                    </div>

                    <!--p><label>Профессия: </label><select name="prof_id">
                        <option value="0">Без профессии</option>
                        <?php //$prof_list = Course::getCourseProfList();
                        //if($prof_list):
                        //foreach($prof_list as $prof):?>
                        <option value="<?php //echo $prof['prof_id'];?>"<?php //if($prof['prof_id'] == $course['prof_id']) echo ' selected="selected";'?>><?php //echo $prof['title'];?></option>
                        <?php //endforeach;
                        //endif;?>
                    </select></p-->

                    <input type="hidden" name="prof_id" value="0">

                    <p>
                        <label>Последовательный доступ: </label>
                        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input checked type="radio" name="autotrain" value="1"<?php if($course['auto_train'] == 1) echo ' checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input type="radio" name="autotrain" value="0"<?php if($course['auto_train'] == 0) echo ' checked';?>><span>Выкл</span></label>
                        </span>
                    </p>


                    <div class="width-100"><label title="Если у вас в курсе будут бесплатные первые уроки, то укажите их кол-во">Кол-во бесплатных уроков: </label>
                    <input type="text" size="2" value="<?php if(isset($course['free_lessons'])) echo $course['free_lessons'];?>" name="free_lessons"></div>

                    <div class="width-100">
                    <label>Автор</label>
                    <?php $authors = User::getAuthors();?>
                        <div class="select-wrap">
                        <select name="author_id">
                            <option value="0">Выберите</option>
                            <?php if($authors):
                                foreach($authors as $author):?>
                                    <option value="<?php echo $author['user_id'];?>"<?php if($author['user_id'] == $course['author_id']) echo ' selected="selected"';?>><?php echo $author['user_name'];?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                        </div>
                    </div>
                    
                    <div class="width-100"><label>Кураторы: </label>
                        <select class="multiple-select" name="curators[]" multiple="multiple">
                            <?php $curators = User::getCurators();
                            if($course['curators'] != null) $curators_arr = unserialize($course['curators']);
                                foreach($curators as $curator):?>
                                    <option value="<?php echo $curator['user_id']?>"<?php if($course['curators'] != null) {if(in_array($curator['user_id'], $curators_arr)) echo 'selected="selected"';}?>><?php echo $curator['user_name']?></option>
                                <?php endforeach;?>
                        </select>
                    </div>
                    

                    <p><label>Сортировка: </label><input type="text" size="3" value="<?php echo $course['sort'];?>" name="sort_course"></p>

                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>

                <div class="col-1-2">

                    <p><label>Обложка: </label><input type="file" name="cover"></p>
                    <?php if(!empty($course['cover'])) {?>
                    <div class="del_img_wrap">
                    <img src="/images/course/<?php echo $course['cover']?>" alt="" width="150">
                    <span class="del_img_link">
                            <button type="submit" form="del_img" value=" " title="Удалить изображение с сервера?" name="del_img"><span class="icon-remove"></span></button>
                        </span>
                    </div>
                    <?php }?>
                    <p><label>Alt: </label><input type="text" size="35" value="<?php echo $course['img_alt'];?>" name="img_alt" placeholder="Альтернативный текст">
                        <input type="hidden" name="current_img" value="<?php echo $course['cover'];?>"></p>

                    <p><label title="В любых CSS величинах: px, em">Отступы обложки: </label>
                    <input type="text" size="3" value="<?php if(isset($course['padding'])) echo $course['padding'];?>" name="padding" placeholder="Для примера: 20px 20px 20px 20px"></p>

                    <p class="width-100" title="Описание для списка курсов"><label>Краткое описание: </label>
                    <textarea name="short_desc"><?php if(isset($course['short_desc'])) echo $course['short_desc'];?></textarea></p>

                </div>

                <div class="col-1-1">
                    <label>Описание:</label>
                    <textarea class="editor" name="desc"><?php echo $course['course_desc'];?></textarea>
                </div>

            </div>
        </div>




        <!-- ДОСТУП -->
         <div>
            <div class="row-line">
                <div class="col-1-1 mb-0">
                    <h4>Настройка доступа</h4>
                </div>
                <div class="col-1-2">
                    <div class="width-100"><label>Тип доступа:</label>
                        <div class="select-wrap">
                            <select name="access_type">
                                <option value="">-- Не выбрано --</option>
                                <option data-show_on="access2group_box" value="1"<?php if($course['access_type'] == 1) echo ' selected="selected"';?>>По группе</option>
                                <?php $membership = System::CheckExtensension('membership', 1);
                                if($membership):?>
                                    <option data-show_on="access2subs_box" value="2"<?php if($course['access_type'] == 2) echo ' selected="selected"';?>>По подписке</option>
                                <?php endif;?>
                                <option value="9"<?php if($course['access_type'] == 9) echo ' selected="selected"';?>>Свободный доступ</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100 hidden" id="access2group_box"><label>Группа:</label>
                        <select class="multiple-select" name="groups[]" multiple="multiple">
                            <?php $group_list = User::getUserGroups();
                            $groups = unserialize($course['groups']);
                            foreach($group_list as $group):?>
                                <option value="<?php echo $group['group_id'];?>"<?php if($groups != null) {if(in_array($group['group_id'], $groups)) echo ' selected="selected"';}?>><?php echo $group['group_title'];?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if($membership):?>
                        <div class="width-100 hidden" id="access2subs_box"><label>Подписка:</label>
                        <select class="multiple-select" name="accesses[]" multiple="multiple">
                            <?php $planes = Member::getPlanes();
                            $access_arr = unserialize($course['access']);
							if($planes):
                            foreach($planes as $plane):?>
                                <option value="<?php echo $plane['id'];?>"<?php if($access_arr != null) {if(in_array($plane['id'], $access_arr)) echo ' selected="selected"';}?>><?php if(empty($plane['service_name'])) echo $plane['name']; else echo $plane['service_name'];?></option>
                            <?php endforeach; 
							endif;?>
                        </select>
                    </div>
                        <?php endif;?>
                </div>
            </div>
        </div>



        <!-- ПОКУПКА КУРСА  -->
        <div>
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Кнопка для покупки:</h4>
                    <div class="width-100"><label>Направлять на:</label>
                        <div class="select-wrap">
                        <select id="type_access_buy" name="type_access_buy">
                        <option value="0"<?php if($course['type_access_buy'] == 0) echo ' selected="selected"';?>>Не выбрано</option>
                        <option value="1"<?php if($course['type_access_buy'] == 1) echo ' selected="selected"';?>>Страницу заказа продукта</option>
                        <option value="2"<?php if($course['type_access_buy'] == 2) echo ' selected="selected"';?>>Внутреннее описание продукта</option>
                        <option value="3"<?php if($course['type_access_buy'] == 3) echo ' selected="selected"';?>>На свой URL</option>
                    </select>
                    </div>
                    </div>

                    <div id="product_access_buy">
                        <label>если продукт, то выберите:</label>
                        <div class="select-wrap">
                        <select name="product_access_buy">
                        <option value="0">Не выбран</option>
                        <?php $product_list = Product::getProductListOnlySelect();
                                foreach($product_list as $product):?>
                        <option value="<?php echo $product['product_id'];?>"<?php if($course['product_access'] == $product['product_id']) echo ' selected="selected"';?>><?php echo $product['product_name'];?></option>
                        <?php endforeach;?>
                    </select>
                    </div>
                    </div>
                    <p id="link_access_buy"><label>если свой URL, то укажите: </label><input type="text" name="link_access_buy" value="<?php echo $course['link_access'];?>" placeholder="http://"></p>

                    <p class="width-100"><label>Надпись на кнопке купить: </label>
                    <input type="text" value="<?php if(isset($course['button_text'])) echo $course['button_text'];?>" name="button_text"></p>
                    <!--
                    <script>
                      $(document).ready(function() {
                        $('#type_access_buy').change(function() {
                          if($(this).val() === "1" || $(this).val() === "2")
                          {
                            $("#product_access_buy").css("display", "block");
                            $("#link_access_buy").css("display", "none");
                          }
                          else if($(this).val() === "0")
                          {
                            $("#link_access_buy, #product_access_buy").css("display", "none");
                          }
                          else if($(this).val() === "3")
                          {
                            $("#link_access_buy").css("display", "block");
                            $("#product_access_buy").css("display", "none");
                          }
                        });

                        $('#type_access_buy').change(function() {
                          $.each($.viewMap, function() { this.hide(); });
                          $.viewMap[$(this).val()].show();
                        }).change(); //only change to your code!
                      });
                    </script>
                    -->
                </div>


                <div class="col-1-2">
                    <h4>Ссылка для просмотра (под кнопкой):</h4>
                    <div class="width-100"><label>Направлять на:</label>
                        <div class="select-wrap">
                        <select name="view_desc[type]">
                        <option value="0"<?php if(isset($view_desc['type']) && $view_desc['type'] == 0) echo ' selected="selected"';?>>Никуда, всё отключено</option>
                        <option value="1"<?php if(isset($view_desc['type']) && $view_desc['type'] == 1) echo ' selected="selected"';?>>Внутренний лендинг продукта</option>
                        <option value="2"<?php if(isset($view_desc['type']) && $view_desc['type'] == 2) echo ' selected="selected"';?>>Свой URL адрес</option>
                    </select>
                    </div>
                    </div>

                    <div id="product_access_buy">
                        <label>если на продукт, то выберите:</label>
                        <div class="select-wrap">
                        <select name="view_desc[product]">
                        <option value="0">Не выбран</option>
                        <?php foreach($product_list as $product):?>
                        <option value="<?php echo $product['product_id'];?>"<?php if($view_desc['product'] == $product['product_id']) echo ' selected="selected"';?>><?php echo $product['product_name'];?></option>
                        <?php endforeach;?>
                    </select>
                    </div>
                    </div>
                    <p><label>если свой URL, то укажите: </label><input type="text" name="view_desc[link]" value="<?php if(isset($view_desc)) echo $view_desc['link'];?>" placeholder="http://"></p>
                    <p><label>Текст ссылки: </label><input type="text" name="view_desc[link_anchor]" value="<?php if(isset($view_desc)) echo $view_desc['link_anchor'];?>"></p>

                </div>
            </div>
        </div>
        <div>
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Отображение</h4>

                    <p>
                        <label>Кол-во уроков: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input checked type="radio" name="lessons_count" value="1"<?php if($course['show_lessons_count'] == 1) echo ' checked';?>><span>Да</span></label>
                            <label class="custom-radio"><input type="radio" name="lessons_count" value="0"<?php if($course['show_lessons_count'] == 0) echo ' checked';?>><span>Нет</span></label>
                        </span>
                    </p>

                    <p>
                        <label>Описание курса: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input type="radio" checked name="show_desc" value="1"<?php if($course['show_desc'] == 1) echo ' checked';?>><span>Да</span></label>
                            <label class="custom-radio"><input type="radio" name="show_desc" value="0"<?php if($course['show_desc'] == 0) echo ' checked';?>><span>Нет</span></label>
                        </span>
                    </p>

                    <p>

                        <label>Показать прогресс: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input type="radio" checked name="show_progress" value="1"<?php if($course['show_progress'] == 1) echo ' checked';?>><span>Да</span></label>
                            <label class="custom-radio"><input type="radio" name="show_progress" value="0"<?php if($course['show_progress'] == 0) echo ' checked';?>><span>Нет</span></label>
                        </span>
                    </p>

                    <p>
                        <label>Показать комментарии: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input type="radio" checked name="show_comments" value="1"<?php if($course['show_comments'] == 1) echo ' checked';?>><span>Да</span></label>
                            <label class="custom-radio"><input type="radio" name="show_comments" value="0"<?php if($course['show_comments'] == 0) echo ' checked';?>><span>Нет</span></label>
                        </span>
                    </p>

                    <p>
                        <label>Показать просмотры: </label>
                        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input type="radio" checked name="show_hits" value="1"<?php if($course['show_hits'] == 1) echo ' checked';?>><span>Да</span></label>
                        <label class="custom-radio"><input type="radio" name="show_hits" value="0"<?php if($course['show_hits'] == 0) echo ' checked';?>><span>Нет</span></label>
                    </span>

                    </p>



                    <p>
                        <label>Показать дату начала: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input type="radio" checked name="show_begin" value="1"<?php if(isset($course['show_begin']) && $course['show_begin'] == 1) echo ' checked';?>><span>Да</span></label>
                            <label class="custom-radio"><input type="radio" name="show_begin" value="0"<?php if(isset($course['show_begin']) && $course['show_begin'] == 0) echo ' checked';?>><span>Нет</span></label>
                        </span>
                    </p>

                    <!--p><label>Показать учеников: </label><select name="show_pupil">
                    <option value="1"<?php //if($course['show_pupil'] == 1) echo ' selected="selected"';?>>Показать</option>
                    <option value="0"<?php //if($course['show_pupil'] == 0) echo ' selected="selected"';?>>Скрыть</option>
                    </select></p-->
                    <input type="hidden" name="show_pupil" value="0">

                    <div>
                        <label>Сортировка уроков: </label>
                        <div class="select-wrap">
                            <select name="sort_less">
                                <option value="0"<?php if($course['sort_less'] == 0) echo ' selected="selected"';?>>По убыванию</option>
                                <option value="1"<?php if($course['sort_less'] == 1) echo ' selected="selected"';?>>По возрастанию</option>
                            </select>
                        </div>
                    </div>
                    <!--p><label>Сертификат обучения: </label><select name="sertificate">
                    <option value="">Нет</option>
                    <?php $sert_list = Course::getCourseSertificateList();
                    if($sert_list):
                    foreach($sert_list as $sert):?>
                    <option value="<?php echo $sert['id'];?>"<?php if($sert['id'] == $course['sertificate_id']) echo ' selected="selected"';?>><?php echo $sert['name'];?></option>
                    <?php endforeach;
                    endif;?>
                    </select></p-->
                    <input type="hidden" name="sertificate" value="">
                </div>

                <div class="col-1-2">
                    <h4>Публикация</h4>
                    <p><label>Дата публикации</label><input type="text" autocomplete="off" value="<?php if($course['start_date'] != null) echo date("d.m.Y H:i", $course['start_date']);?>" class="datetimepicker" name="start"></p>
                    <p><label>Дата завершения</label><input type="text" autocomplete="off" value="<?php if($course['start_date'] != null) echo  date("d.m.Y H:i", $course['end_date']);?>" class="datetimepicker" name="end"></p>

                </div>
            </div>
        </div>

        <!-- 3 вкладка -->
        <div>
            <div class="row-line">
                <div class="col-1-1">
                    <div style="overflow:hidden; margin-bottom:1em">
                        <h4>Создать блок:</h4>
                        <form id="blocks" method="POST" action="">
                            <input type="text" style="width:58%; border:1px solid #b1b1b1; float:left; margin-right:2%" name="block_name" placeholder="Название блока">
                            <input type="text" style="width:18%; border:1px solid #b1b1b1; float:left; margin-right:2%" name="sort" value="<?php if(isset($lessons_blocks)){ if(!$lessons_blocks) echo 1; else echo $sort_num['sort']+1; }?>" placeholder="№">
                            <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                            <input type="submit" style="width:20%; float:right" name="add_block" class="button save button-green-rounding button-lesson" value="Создать">
                        </form>
                    </div>

                    <div class="mt-30" style="overflow:hidden">
                        <h4>Блоки уроков:</h4>
                        <?php if(isset($lessons_blocks) && $lessons_blocks == true):
                        foreach($lessons_blocks as $block):?>
                        <div style="overflow:hidden; padding: 0 0 1em 0;">
                            <form action="" method="POST" id="edit_block">
                                <input type="text" style="width:58%; float:left; margin-right:2%" name="block_name" value="<?php echo $block['block_name'];?>" placeholder="Название блока">
                                <input type="text" style="width:18%; float:left; margin-right:2%" name="sort" value="<?php echo $block['sort'];?>" placeholder="№">
                                <input type="hidden" name="block_id" value="<?php echo $block['block_id'];?>">
                                <button type="submit" style="width:9%; float:right; border-radius:3px" title="Удалить блок" name="del_block" class="button save button-red-rounding button-lesson"><span class="icon-remove"></span></button>

                                    <button type="submit" style="width:9%; float:right; border-radius:3px; margin-right: 1%;" title="Изменить блок" name="save_block" class="button save button-green-rounding button-lesson"><span class="icon-floppy-disk"></span></button>
                                <!--input type="image" src="/template/admin/images/reload.png" title="Обновить" name="reload">
                                <input type="image" onclick="return confirm('Вы уверены?')" src="/template/admin/images/del.png" title="Удалить" name="reload"-->
                            </form>
                        </div>
                        <?php endforeach;
                         endif; ?>

                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Настройки для SEO</h4>
                    <p><label>Alias (хвост ссылки) </label><input type="text" name="alias" value="<?php echo $course['alias'];?>" placeholder="Алиас курса"></p>
                    <p><label>Title </label><input type="text" name="title" value="<?php echo $course['title'];?>" placeholder="Title курса"></p>
                    <p><label>Meta Description</label><textarea name="meta_desc" rows="3" cols="40"><?php echo $course['meta_desc'];?></textarea></p>
                    <p><label>Meta Keys:</label><textarea name="meta_keys" rows="3" cols="40"><?php echo $course['meta_keys'];?></textarea></p>
                </div>
            </div>
        </div>
    </div>
    </form>
    <div class="buttons-under-form">
    <p class="button-delete">
        <a onclick="return confirm('Вы уверены?')" href="<?php echo $setting['script_url'];?>/admin/courses/delete/<?php echo $course['course_id'];?>?token=<?php echo $_SESSION['admin_token'];?>"><i class="icon-remove"></i>Удалить тренинг</a>
    </p>

        <form class="copy-but" action="<?php echo $setting['script_url'];?>/admin/courses" method="POST"><input type="hidden" name="course_id" value="<?php echo $course['course_id'];?>">
            <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
            <button class="button-copy-2" type="submit" name="copy"><i class="icon-copy"></i> Копировать тренинг</button>
        </form>
        <form class="copy-but" action="<?php echo $setting['script_url'];?>/admin/courses" method="POST"><input type="hidden" name="course_id" value="<?php echo $course['course_id'];?>">
            <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
            <button class="button-copy-2" type="submit" name="exportcsv"><i class="icon-copy"></i> Выгрузить тренинг в CSV</button>
        </form> 

    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

<form action="/admin/delimg/<?php echo $course['course_id'];?>" id="del_img" method="POST">
    <input type="hidden" name="path" value="images/course/<?php echo $course['cover'];?>">
    <input type="hidden" name="page" value="admin/courses/edit/<?php echo $course['course_id'];?>">
    <input type="hidden" name="table" value="course">
    <input type="hidden" name="name" value="cover">
    <input type="hidden" name="where" value="course_id">
    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
</form>

<link rel="stylesheet" type="text/css" href="/template/admin/css/jquery.datetimepicker.min.css">
<script src="/template/admin/js/jquery.datetimepicker.full.min.js"></script>
<script>
  jQuery('.datetimepicker').datetimepicker({
    format:'d.m.Y H:i',
    lang:'ru'
  });
</script>
<style>
    .mce-tinymce{
        width: 100% !important;
    }
</style>
</body>
</html>