<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Добавить новый урок</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/courses/">Тренинги</a>
        </li>
        <li>Добавить новый урок</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">

        <div class="traning-top">
            <h3 class="traning-title">Добавить новый урок</h3>
            <ul class="nav_button">
                <li><input type="submit" name="addless" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/lessons?course=<?php echo intval($_GET['filter_course']);?>">Закрыть</a></li>
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
                    <p class="width-100"><label>Название: </label><input type="text" name="name" placeholder="Название урока" required="required"></p>

                    <div class="width-100"><label>Статус: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1" checked><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0"><span>Откл</span></label>
                        </span>
                    </div>

                    <div class="width-100"><label>Курс: </label>
                        <div class="select-wrap">
                            <select name="course_id">
                            <?php $course_list = Course::getCourseList(0, 0);
                            if($course_list):
                            foreach($course_list as $course): ?>
                            <option value="<?php echo $course['course_id'];?>"<?php if(isset($_GET['filter_course']) && $_GET['filter_course'] == $course['course_id']) echo ' selected="selected"';?>><?php echo $course['name'];?></option>
                            <?php endforeach;
                            endif;?>
                            </select>
                        </div>
                    </div>
                    
                    <?php if(isset($_GET['filter_course'])):?>
                    <p><label>Блок: </label>
                        <select name="block_id">
                            <option value="">-- Не выбран --</option>
                            <?php $block_list = Course::getBlocksFromCourse(intval($_GET['filter_course']));
                            if($block_list):
                            foreach($block_list as $block): ?>
                            <option value="<?php echo $block['block_id'];?>"><?php echo $block['block_name'];?></option>
                            <?php endforeach;
                            endif;?>
                        </select>
                    </p>
                    <?php endif;?>
                    
                    <p class="width-100"><label>Продолжительность, мин </label><input type="text" size="35" name="duration" placeholder="Продолжительность"></p>
                    
                    <p class="width-100"><label>Порядок: </label><input type="text" size="3" name="sort"></p>
                    

                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                
                <div class="col-1-2">
                
                    <p class="width-100"><label>Обложка: </label><input type="file" name="cover"></p>
                    <p class="width-100"><label>Alt: </label><input type="text" size="35" name="img_alt" placeholder="Альтернативный текст"></p>
                </div>
                
                <div class="col-1-1">
                    <h4>Краткое описание (отображается в списке уроков)</h4>
                    <textarea class="editor" name="desc"></textarea>
                </div>
                
                </div>
            </div>
            
            <!-- 2 вкладка СОДЕРЖАНИЕ-->
            <div>
                <div class="row-line">
                    <div class="col-1-1">
                        <div class="width-100">
                            <h4 title="Ссылки на файлы уроков для защищённого плеера (через запятую)">URL адреса видео роликов (каждый URL с новой строки)*</h4>
                            <textarea name="video_urls" rows="3"></textarea>
                        </div>
						
						<div class="width-100">
                            <h4 title="Ссылки на файлы аудио роликов для защищённого плеера">URL адреса аудио роликов (каждый URL с новой строки)*</h4>
                            <textarea name="audio_urls" rows="3"></textarea>
                        </div>
						
                        <div class="width-100">
                        <h4>Суть урока</h4>
                        <textarea class="editor" name="content"></textarea>
                        </div>
                        
                        <p class="width-100">Произвольный html код (выводится под содержанием урока):<br /><textarea name="custom_code" rows="5" cols="40"></textarea></p>
                    </div>
                    
                    
                </div>
            </div>
            
            
            <!-- 3 вкладка ДОСТУП -->
            <div>
                <div class="row-line">
                    
                    <div class="col-1-2">
                        <h4>Настройка доступа</h4>
                        <div class="width-100"><label>Тип доступа: </label>
                            <div class="select-wrap">
                            <select name="type_access">
                        <option value="9">Из настроек курса</option>
                        <option value="1">По группе</option>
                        <?php $membership = System::CheckExtensension('membership', 1);
                        if($membership):?>
                        <option value="2">По подписке</option>
                        <option value="0">Свободный доступ</option>
                        <?php endif;?>
                        </select>
                            </div>
                        </div>
                        
                        <div class="width-100"><label>Группа: </label>
    
                            <select class="multiple-select" name="groups[]" multiple="multiple">
                        <?php $group_list = User::getUserGroups();
                        foreach($group_list as $group):?>
                            <option value="<?php echo $group['group_id'];?>"><?php echo $group['group_title'];?></option>
                        <?php endforeach; ?>
                        </select>
                        </div>
                        
                        <?php if($membership):?>
                        <div class="width-100"><label>Подписка: </label>
    
                            <select class="multiple-select" name="accesses[]" multiple="multiple">
                            <?php $planes = Member::getPlanes();
                            foreach($planes as $plane):?>
                            <option value="<?php echo $plane['id'];?>"><?php echo $plane['name'];?></option>
                            <?php endforeach; ?>
                        </select>
    
                        </div>
                        <?php endif;?>
                    </div>
                    
                    <div class="col-1-2">
                        <h4>При покупке направлять:</h4>
                        
                        <div class="width-100"><label>Вариант:</label>
                            <div class="select-wrap">
                            <select name="type_access_buy">
                                <option value="0">Не выбран</option>
                                <option value="1">Продукт - страница заказа</option>
                                <option value="2">Продукт - лендинг</option>
                                <option value="3">Своя ссылка</option>
                            </select>
                            </div>
                        </div>
                        
                        <div class="width-100"><label>если продукт, выберите:</label>
                            <div class="select-wrap">
                            <select name="product_access_buy">
                            <option value="0">Не выбран</option>
                            <?php $product_list = Product::getProductListOnlySelect();
                            foreach($product_list as $product):?>
                            <option value="<?php echo $product['product_id'];?>"><?php echo $product['product_name'];?></option>
                            <?php endforeach;?>
                        </select>
                            </div>
                        </div>
                        <p class="width-100"><label>если ссылка, то укажите: </label><input type="text" name="link_access_buy" placeholder="http://"></p>
                    </div>
                </div>
            </div>
            
            <!-- 4 вкладка ЗАДАНИЕ -->
            <div>
                <div class="row-line">
                    <div class="col-1-2">
                        <div class="width-100"><label>Тип проверки: </label>
                            <div class="select-wrap">
                                <select name="task_type">
                                    <option value="0">-- Нет задания и нет проверки --</option>
                                    <option value="1">Без проверки</option>
                                    <option value="2">Автопроверка</option>
                                    <option value="3">Ручная проверка</option>
                                </select>
                            </div>
                        </div>
                        <p class="width-100"><label>Задержка автопроверки, мин</label><input type="text" name="task_time"></p>
                    </div>
                    
                    <div class="col-1-1">
                        <h4>Текст задания</h4>
                        <p class="width-100"><label>Задание: </label><textarea class="editor" name="task"></textarea></p>
                    </div>
                </div>
            </div>
            
            
            
            <!-- 5 вкладка ВНЕШНИЙ ВИД -->
            <div>
                <div class="row-line">
                <div class="col-1-2">
                    <h4>Отображение</h4>
                    <!--p><label>Разрешить комментарии: </label><select name="allow_comments">
                    <option value="1">Показать</option>
                    <option value="0">Скрыть</option>
                    </select></p-->
                    <input type="hidden" name="allow_comments" value="1">
                    
                    <div class="width-100"><label>Показать комментарии: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="show_comments" type="radio"  value="1"><span>Да</span></label>
                            <label class="custom-radio"><input name="show_comments" type="radio" value="0" checked><span>Нет</span></label>
                        </span>
                    </div>
                    
                    <div class="width-100"><label>Показать просмотры: </label>
                        <span class="custom-radio-wrap">
                    <label class="custom-radio"><input name="show_hits_count" type="radio" value="1" checked><span>Да</span></label>
                    <label class="custom-radio"><input name="show_hits_count" type="radio" value="0"><span>Нет</span></label>
                    </span>
                    </div>
                
                </div>
                
                <div class="col-1-2">
                    <h4>Публикация</h4>
                    <p class="width-100"><label>Дата публикации</label><input type="text" class="datetimepicker" autocomplete="off" name="start"></p>
                    <p class="width-100"><label>Дата завершения</label><input type="text" class="datetimepicker" autocomplete="off" name="end"></p>
                </div>
                </div>
            </div>
            
            
            
            <!-- 6 вкладка SEO  -->
            <div>
                <div class="row-line">
                    <div class="col-1-2">
                        <h4>Настройки SEO, если урок публичный</h4>
                        <p class="width-100"><label>Алиас (хвост ссылки): </label><input type="text" name="alias" placeholder="Алиас урока"></p>
                        <p class="width-100"><label>Title: </label><input type="text" name="title" placeholder="Title урока"></p>
                        <p class="width-100">Meta Description:<br /><textarea name="meta_desc" rows="3" cols="40"></textarea></p>
                        <p class="width-100">Meta Keys:<br /><textarea name="meta_keys" rows="3" cols="40"></textarea></p>
                    </div>
                </div>
            </div>
            
            
        </div>
    </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>

<link rel="stylesheet" type="text/css" href="/template/admin/css/jquery.datetimepicker.min.css">
<script src="/template/admin/js/jquery.datetimepicker.full.min.js"></script>
<script>
jQuery('.datetimepicker').datetimepicker({
format:'d.m.Y H:i',
lang:'ru'
});
</script>
</body>
</html>