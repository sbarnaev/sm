<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo $lesson['name'];?> (#<?php echo $lesson['lesson_id'];?>)</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout/" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/courses/">Тренинги</a>
        </li>
		<li><a href="/admin/courses/edit/<?php echo $lesson['course_id'];?>"><?php echo Course::getCourseNameByID($lesson['course_id']);?></a></li>
        <li>
            <a href="/admin/lessons/">Список уроков</a>
        </li>
        <li><?php echo $lesson['name'];?> (#<?php echo $lesson['lesson_id'];?>)</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <div class="traning-top">
        <h3 class="traning-title">
            <a href="<?php echo "/courses/{$course['alias']}/{$lesson['alias']}";?>" target="_blank">
                <?php echo $lesson['name'];?>
            </a>
        </h3>
        <ul class="nav_button">
            <li><input type="submit" name="editless" value="Сохранить" class="button save button-white font-bold"></li>
            <li class="nav_button__last"><a class="button red-link" href="/admin/lessons/">Закрыть</a></li>
        </ul>
    </div>
    <div class="tabs">
        <ul>
            <li>Основное</li>
            <li>Содержание</li>
            <li>Доступ</li>
            <li>Задание</li>
            <li>Внешний вид</li>
            <li>SEO</li>
        </ul>

        <div class="admin_form">
            <!-- 1 вкладка -->
            <div>
                <h4>Основное</h4>
                <div class="row-line">
                <div class="col-1-2">
                    <p><label>Название: </label><input type="text" name="name" value="<?php echo $lesson['name'];?>" placeholder="Название урока" required="required"></p>
                    
                    <div class="width-100"><label>Статус: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1"<?php if($lesson['status'] == 1) echo ' checked';?>><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0"<?php if($lesson['status'] != 1) echo ' checked';?>><span>Откл</span></label>
                        </span>
                    </div>
                    
                    <div class="width-100"><label>Курс: </label>
                        <div class="select-wrap">
                        <select name="course_id">
                        <?php $course_list = Course::getCourseList(0, 0);
                        if($course_list):
                        foreach($course_list as $course): ?>
                        <option value="<?php echo $course['course_id'];?>"<?php if($lesson['course_id'] == $course['course_id']) echo ' selected="selected"';?>><?php echo $course['name'];?></option>
                        <?php endforeach;
                        endif;?>
                    </select>
                    </div>
                    </div>

                    <div class="width-100"><label>Блок: </label>
                        <div class="select-wrap">
                        <select name="block_id">
                    <option value="">-- Не выбран --</option>
                        <?php $block_list = Course::getBlocksFromCourse($lesson['course_id']);
                        if($block_list):
                        foreach($block_list as $block): ?>
                        <option value="<?php echo $block['block_id'];?>"<?php if($lesson['block_id'] == $block['block_id']) echo ' selected="selected"';?>><?php echo $block['block_name'];?></option>
                        <?php endforeach;
                        endif;?>
                    </select>
                    </div>
                    </div>

                    <p class="width-100"><label>Продолжительность, мин </label><input type="text" size="35" name="duration" value="<?php echo $lesson['duration'];?>" placeholder="Продолжительность"></p>

                    <p><label>Порядок: </label><input type="text" size="3" value="<?php echo $lesson['sort'];?>" name="sort"></p>


                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>

                <div class="col-1-2">

                    <p><label>Обложка: </label><input type="file" name="cover">
                    <input type="hidden" name="current_img" value="<?php echo $lesson['cover'];?>"></p>
                    <?php if(!empty($lesson['cover'])) {?>
                    <div class="del_img_wrap">
                    <img style="width:150px" src="/images/lessons/<?php echo $lesson['cover'];?>">

                        <span class="del_img_link">
                            <button type="submit" form="del_img" value=" " title="Удалить изображение с сервера?" name="del_img"><span class="icon-remove"></span></button>
                        </span>
                        </div>
                    <?php }?>
                    <p><label>Alt: </label><input type="text" size="35" value="<?php echo $lesson['img_alt'];?>" name="img_alt" placeholder="Альтернативный текст"></p>

                </div>

                <div class="col-1-1">
                    <h4>Краткое описание (отображается в списке уроков)</h4>
                    <textarea class="editor" name="desc"><?php echo $lesson['less_desc'];?></textarea>
                </div>
                </div>
            </div>



            <!-- 2 вкладка СОДЕРЖАНИЕ-->
            <div>
                <div class="row-line">
                    <div class="col-1-1">
                        <div class="width-100">
                            <h4 title="Ссылки на файлы уроков для защищённого плеера (через запятую)">URL адреса видео роликов (каждый URL с новой строки)*:</h4>
                            <textarea name="video_urls" rows="3"><?php echo $lesson['video_urls'];?></textarea>
                        </div>
						
						<div class="width-100">
                            <h4 title="Ссылки на файлы аудио роликов для защищённого плеера">URL адреса аудио (каждый URL с новой строки)*:</h4>
                            <textarea name="audio_urls" rows="3"><?php echo $lesson['audio_urls'];?></textarea>
                        </div>


                        <div class="width-100">
                            <h4>Суть урока</h4>
                            <textarea class="editor" name="content"><?php echo htmlspecialchars($lesson['content']);?></textarea>
                        </div>

                        <div class="width-100 mt-30">
                            <label>Вложения к урокам:</label>
                            <div class="lesson_attachs_wrap lesson_attachs_wrap__no-border">
                                <input id="lesson_attachs_files" type="file" multiple name="attachments[]">
                                <input type="hidden" name="current_attachments" value="<?php echo htmlspecialchars($lesson['attach']);?>">
                                <div>
                                <?php if(!empty($lesson['attach'])):?>
                                    <div class="lesson_attachs">
                                        <?php foreach (json_decode($lesson['attach'], true) as $attach_name):?>
                                        <div class="lesson_attach_wrap">
                                            <div class="lesson_attach">
                                                <img src="/template/admin/images/attachment.png" alt="">
                                                <a class="del_attach_link" href="javascript:void(0);" title="Удалить вложение с сервера?" name="del_attach" data-attach_name="<?php echo $attach_name;?>">
                                                    <img src="/template/admin/images/delx.png" alt="">
                                                </a>
                                            </div>
                                            <span class="small"><?php echo $attach_name;?></span>
                                        </div>
                                        <?php endforeach;?>
                                    </div>
                                <?php endif;?>
                                </div>
                            </div>
                        </div>

                        <p class="width-100 mt-30"><label>Произвольный html код (выводится перед содержанием урока):</label><textarea name="custom_code_up" rows="5" cols="40"><?php echo $lesson['custom_code_up'];?></textarea></p>
                        <p class="width-100"><label>Произвольный html код (выводится под содержанием урока):</label><textarea name="custom_code" rows="5" cols="40"><?php echo $lesson['custom_code'];?></textarea></p>
                    </div>
                </div>
            </div>



             <!-- 3 вкладка ДОСТУП -->
            <div>
                <div class="row-line">
                    <div class="col-1-2">
                        <h4>Настройка доступа:</h4>
                        <div class="width-100"><label>Тип доступа:</label>
                            <div class="select-wrap">
                                <select id="type_access" name="type_access">
                                    <option value="9"<?php if($lesson['access_type'] == 9) echo ' selected="selected"';?>>Из настроек курса</option>
                                    <option data-show_on="access2group_box" value="1"<?php if($lesson['access_type'] == 1) echo ' selected="selected"';?>>По группе</option>
                                    <?php $membership = System::CheckExtensension('membership', 1);
                                    if($membership):?>
                                        <option data-show_on="access2subs_box" value="2"<?php if($lesson['access_type'] == 2) echo ' selected="selected"';?>>По подписке</option>
                                    <?php endif;?>
                                    <option value="0"<?php if($lesson['access_type'] == 0) echo ' selected="selected"';?>>Свободный доступ</option>
                                </select>
                            </div>
                        </div>

                        <div class="width-100 hidden" id="access2group_box"><label>Группа:</label>
                            <select name="groups[]" class="multiple-select" multiple="multiple">
                                <?php $group_list = User::getUserGroups();
                                $groups = unserialize($lesson['groups']);
                                foreach($group_list as $group):?>
                                    <option value="<?php echo $group['group_id'];?>"<?php if($groups != null && in_array($group['group_id'], $groups)) echo ' selected="selected"';?>>
                                        <?=$group['group_title'];?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if($membership):?>
                            <div class="width-100 hidden" id="access2subs_box"><label>Подписка:</label>
                                <select name="accesses[]" class="multiple-select" multiple="multiple">
                                    <?php $planes = Member::getPlanes();
                                    $access_arr = unserialize($lesson['access']);
                                    if($planes):
									foreach($planes as $plane):?>
                                        <option value="<?=$plane['id'];?>"<?php if($access_arr != null && in_array($plane['id'], $access_arr)) echo ' selected="selected"';?>>
                                            <?=empty($plane['service_name']) ? $plane['name'] : $plane['service_name'];?>
                                        </option>
                                    <?php endforeach;
									endif;?>
                                </select>
                            </div>
                        <?php endif;?>
                    </div>

                    <div class="col-1-2">
                        <h4>При покупке направлять:</h4>
                        <div class="width-100">
                            <label>Тип:</label>
                            <div class="select-wrap">
                            <select id="type_access_buy" name="type_access_buy">
                            <option value="0"<?php if($lesson['type_access_buy'] == 0) echo ' selected="selected"';?>>Взять настройки из курса</option>
                            <option value="1"<?php if($lesson['type_access_buy'] == 1) echo ' selected="selected"';?>>Продукт - страница заказа</option>
                            <option value="2"<?php if($lesson['type_access_buy'] == 2) echo ' selected="selected"';?>>Продукт - лендинг</option>
                            <option value="3"<?php if($lesson['type_access_buy'] == 3) echo ' selected="selected"';?>>Своя ссылка</option>
                        </select>
                        </div>
                        </div>

                        <div id="product_access_buy" class="width-100"><label>если продукт, выберите:</label>
                            <div class="select-wrap">
                            <select name="product_access_buy">
                            <option value="0">Не выбран</option>
                            <?php $product_list = Product::getProductListOnlySelect();
                            foreach($product_list as $product):?>
                            <option value="<?php echo $product['product_id'];?>"<?php if($lesson['product_access'] == $product['product_id']) echo ' selected="selected"';?>><?php echo $product['product_name'];?></option>
                            <?php endforeach;?>
                        </select>
                        </div>
                        </div>
                        <p id="link_access_buy"><label>если ссылка, укажите: </label><input type="text" name="link_access_buy" value="<?php echo $lesson['link_access'];?>" placeholder="http://"></p>

                        <h4>Открытие по расписанию:</h4>
                        <p title="Открыть урок через указанный период после присвоения группы (т.е. покупки курса) / или после старта курса"><label>Открыть урок через</label><input type="text" name="timing" value="<?php echo $lesson['timing'];?>"></p>
                        <div class="width-100">
                            <label>Тип периода:</label>
                            <div class="select-wrap">
                            <select name="timing_period">
                            <option value="">-- Выбрать --</option>
                            <option value="hour"<?php if($lesson['timing_period'] == 'hour') echo ' selected="selected"';?>>Часов</option>
                            <option value="day"<?php if($lesson['timing_period'] == 'day') echo ' selected="selected"';?>>Дней</option>
                            <option value="week"<?php if($lesson['timing_period'] == 'week') echo ' selected="selected"';?>>Недель</option>
                            <option value="month"<?php if($lesson['timing_period'] == 'month') echo ' selected="selected"';?>>Месяц (30дн)</option>
                        </select>
                        </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- 4 вкладка ЗАДАНИЕ -->
            <div>
                <div class="row-line">
                    <div class="col-1-2">
                        <div class="width-100">
                            <label>Тип проверки: </label>
                            <div class="select-wrap">
                            <select name="task_type">
                            <option value="0"<?php if($lesson['task_type'] == 0) echo ' selected="selected"'?>>-- Нет задания и нет проверки --</option>
                            <option value="1"<?php if($lesson['task_type'] == 1) echo ' selected="selected"'?>>Без проверки</option>
                            <option value="2"<?php if($lesson['task_type'] == 2) echo ' selected="selected"'?>>Автопроверка</option>
                            <option value="3"<?php if($lesson['task_type'] == 3) echo ' selected="selected"'?>>Ручная проверка</option>
                        </select>
                        </div>
                        </div>
                        <p><label>Задержка автопроверки, мин</label><input type="text" value="<?php echo $lesson['task_time'];?>" name="task_time"></p>
                    </div>

                    <div class="col-1-1">
                        <p><label>Задание после урока: </label><textarea class="editor" name="task"><?php echo $lesson['task'];?></textarea></p>
                    </div>
                </div>
            </div>



            <!-- 5 вкладка  ВНЕШНИЙ ВИД-->
            <div>
               <div class="row-line">
                <div class="col-1-2">
                    <h4>Отображение</h4>
                    <!--p><label>Разрешить комментарии: </label><select name="allow_comments">
                    <option value="1"<?php if($lesson['allow_comments'] == 1) echo ' selected="selected"';?>>Разрешить</option>
                    <option value="0"<?php if($lesson['allow_comments'] == 0) echo ' selected="selected"';?>>Запретить</option>
                    </select></p-->
                    <input type="hidden" name="allow_comments" value="1">

                    <div class="width-100"><label>Показать комментарии: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="show_comments" type="radio" value="1"<?php if($lesson['show_comments'] == 1) echo ' checked';?>><span>Да</span></label>
                            <label class="custom-radio"><input name="show_comments" type="radio" value="0"<?php if($lesson['show_comments'] == 0) echo ' checked';?>><span>Нет</span></label>
                       </span>
                    </div>

                    <div class="width-100"><label>Показать просмотры: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="show_hits_count" type="radio" value="1"<?php if($lesson['show_hits_count'] == 1) echo ' checked';?>><span>Да</span></label>
                            <label class="custom-radio"><input name="show_hits_count" type="radio" value="0"<?php if($lesson['show_hits_count'] == 0) echo ' checked';?>><span>Нет</span></label>
                        </span>
                    </div>
                </div>

                <div class="col-1-2">
                    <h4>Публикация</h4>
                    <p><label>Дата публикации</label><input type="text" class="datetimepicker" value="<?php echo date("d.m.Y H:i", $lesson['public_date']);?>" autocomplete="off" name="start"></p>
                    <p><label>Дата завершения</label><input type="text" class="datetimepicker" value="<?php echo date("d.m.Y H:i", $lesson['end_date']);?>" autocomplete="off" name="end"></p>
                </div>
                </div>
            </div>




            <!-- 6 вкладка SEO  -->
            <div>
                <div class="row-line">
                    <div class="col-1-2">
                        <h4>Настройки SEO</h4>
                        <p><label>Алиас: </label><input type="text" name="alias" value="<?php echo $lesson['alias'];?>" placeholder="Алиас урока"></p>
                        <p><label>Title: </label><input type="text" name="title" value="<?php echo $lesson['title'];?>" placeholder="Title урока"></p>
                        <p>Meta Description:<br /><textarea name="meta_desc" rows="3" cols="40"><?php echo $lesson['meta_desc'];?></textarea></p>
                        <p>Meta Keys:<br /><textarea name="meta_keys" rows="3" cols="40"><?php echo $lesson['meta_keys'];?></textarea></p>
                    </div>
                </div>
            </div>


        </div>
    </div>
    </form>

    <div class="buttons-under-form">
        <p class="button-delete">
            <a onclick="return confirm('Вы уверены?')" href="<?php echo $setting['script_url'];?>/admin/lessons/del/<?php echo $lesson['lesson_id'];?>?token=<?php echo $_SESSION['admin_token'];?>"><i class="icon-remove"></i>Удалить урок</a>
        </p>
        <form class="copy-but" action="/admin/lessons/" method="POST">
            <input type="hidden" name="lesson_id" value="<?php echo $lesson['lesson_id'];?>">
            <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
            <button class="button-copy-2 orange" type="submit" name="copy"><i class="icon-copy"></i> Копировать урок</button>
        </form>
    </div>

    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
    <form action="/admin/delimg/<?php echo $lesson['lesson_id'];?>" id="del_img" method="POST">
        <input type="hidden" name="path" value="images/lessons/<?php echo $lesson['cover'];?>">
        <input type="hidden" name="page" value="admin/lessons/edit/<?php echo $lesson['lesson_id'];?>">
        <input type="hidden" name="table" value="course_lessons">
        <input type="hidden" name="name" value="cover">
        <input type="hidden" name="where" value="lesson_id">
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
    .lesson_attachs_wrap {
        border:1px solid #d6d6d6;
        border-radius:5px;
        padding:10px 15px;
    }
    .lesson_attachs {
        margin-bottom: -10px;
    }
    .lesson_attach {
        position: relative;
    }
    .lesson_attach_wrap {
        margin-right: 10px;
        margin-bottom: 10px;
    }
    .lesson_attach_wrap, .lesson_attach {
        display: inline-block;
    }
    .del_attach_link {
        content: "";
        position: absolute;
        display: block;
        top: -7px;
        right: -7px;
    }
    .lesson_attachs_wrap .jq-file__name {
        overflow: visible;
        white-space: normal;
    }
</style>
<script>
  $(document).ready(function() {
    $(".del_attach_link").click(function() {
      if (confirm('Вы точно хотите удалить файл?')) {
        let $attach = $(this);
        let attach_name = $attach.data('attach_name');
        let attachments = $('input[name="current_attachments"').val();

        $.ajax({
          url: '/admin/lessons/delattach',
          method: 'post',
          dataType: 'json',
          data: {lesson_id:"<?php echo $lesson['lesson_id'];?>", attach_delete:attach_name, attachments: attachments},
          success: function(data) {
            if (data.result) {
              $attach.parents('.lesson_attach_wrap').remove();
              $('input[name="current_attachments"').val(data.attachments);
            }
          }
        });
      }
    });

    let dropZone = $('.lesson_attachs_wrap');
    if (typeof(window.FileReader) != 'undefined') {
      dropZone[0].ondragover = function() {
        return false;
      };
      dropZone[0].ondragleave = function() {
        return false;
      };
      dropZone[0].ondrop = function(event) {
        event.preventDefault();
        let files = event.dataTransfer.files;
        document.getElementById("lesson_attachs_files").files = files;
        setTimeout(function() {
          $('#lesson_attachs_files').trigger('refresh');
          if (Object.keys(files).length > 0) {
            let files_info = '<b>Выбрано файлов: ' + (Object.keys(files).length) + ' </b>(';
            $.each(files, function(i) {
              files_info += (i > 0 ? ', ' : '') + this.name;
            });
            files_info += ')';
            $('.lesson_attachs_wrap .jq-file__name').html(files_info);
          }
        }, 1)
      };
    }
  });
</script>
</body>
</html>