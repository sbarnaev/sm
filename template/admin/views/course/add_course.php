<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Добавить новый курс</h1>
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
        <li>Добавить новый курс</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="traning-top">
            <h3 class="traning-title">Добавить новый курс</h3>
            <ul class="nav_button">
                <li><input type="submit" name="addcourse" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/courses/">Закрыть</a></li>
            </ul>
        </div>
    <div class="tabs">
        <ul>
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
                <div class="col-1-2 mb-0">
                    <p class="width-100"><label>Название: </label><input type="text" name="name" placeholder="Название курса" required="required"></p>

                    <div class="width-100">
                        <label>Статус: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1" checked><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0"><span>Откл</span></label>
                        </span>
                    </div>
                    
                    <div class="width-100">
                        <label>Показывать в общем списке курсов: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="show_in_main" type="radio" value="1" checked><span>Показать</span></label>
                            <label class="custom-radio"><input name="show_in_main" type="radio" value="0"><span>Скрыть</span></label>
                        </span>
                    </div>
                    
                    <div class="width-100">
                        <label>Бесплатный тренинг? </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="is_free" type="radio" value="1"><span>Да</span></label>
                            <label class="custom-radio"><input name="is_free" type="radio" value="0" checked><span>Нет</span></label>
                        </span>
                    </div>

                    <div class="width-100"><label>Категория: </label>
                        <div class="select-wrap">
                        <select name="cat_id">
                        <option value="0">Без категории</option>
                        <?php $cat_list = Course::getCourseCatFromList();
                        if($cat_list):
                        foreach($cat_list as $cat):?>
                        <option value="<?php echo $cat['cat_id'];?>"><?php echo $cat['name'];?></option>
                        <?php endforeach;
                        endif;?>
                    </select>
                        </div>
                    </div>
                    
                    <!--div class="width-100"><label>Профессия: </label>
                        <div class="select-wrap">
                        <select name="prof_id">
                        <option value="0">Без профессии</option>
                        <?php //$prof_list = Course::getCourseProfList();
                        //if($prof_list):
                        //foreach($prof_list as $prof):?>
                        <option value="<?php //echo $prof['prof_id'];?>"><?php // echo $prof['title'];?></option>
                        <?php //endforeach;
                       // endif;?>
                    </select>
                        </div>
                    </div-->
                    <input type="hidden" name="prof_id" value="0">
                    
                    <div class="width-100"><label>Последовательный доступ: </label>
                        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="autotrain" type="radio" value="0" checked><span>Нет</span></label>
                            <label class="custom-radio"><input name="autotrain" type="radio" value="1"><span>Да</span></label>
                        </span>
                    </div>
                    <p><label title="Если у вас в курсе будут бесплатные первые уроки, то укажите их кол-во">Кол-во бесплатных уроков: </label><input type="text" size="2" value="0" name="free_lessons"></p>
                    
                    <div class="width-100"><label>Кураторы: </label>
                        <select class="multiple-select" size="5" name="curators[]" multiple="multiple">
                        <?php $curators = User::getCurators();
                        foreach($curators as $curator):?>
                            <option value="<?php echo $curator['user_id']?>"><?php echo $curator['user_name']?></option>
                        <?php endforeach;?>
                        </select>
                    </div>
                    
                    <p class="width-100"><label>Сортировка: </label><input type="text" size="3" name="sort"></p>

                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                
                <div class="col-1-2 mb-0">
                    <p class="width-100"><label>Обложка: </label><input type="file" name="cover"></p>
                    <p class="width-100"><label>Alt: </label><input type="text" size="35" name="img_alt" placeholder="Альтернативный текст"></p>
                    
                    <p class="width-100" title="Описание для списка курсов"><label>Краткое описание: </label><textarea name="short_desc"></textarea></p>
                </div>
                
                <div class="col-1-1">
                    <label>Описание:</label>
                    <textarea class="editor" name="desc"></textarea>
                </div>
                </div>
            </div>
            
            
            
            <!-- ДОСТУП -->
             <div>
                <div class="row-line">
                    <div class="col-1-1 mb-0"><h4>Настройка доступа</h4></div>
                    <div class="col-1-2">
                            <div class="width-100"><label>Тип доступа: </label>
                                <div class="select-wrap">
                                    <select name="access_type">
                                    <option value="">-- Не выбрано --</option>
                                    <option value="1">По группе</option>
                                    <?php $membership = System::CheckExtensension('membership', 1);
                                    if($membership):?>
                                    <option value="2">По подписке</option>
                                    <?php endif;?>
                                    <option value="9">Свободный доступ</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="width-100"><label>Группа: </label>
        
                                <select class="multiple-select" name="groups[]" multiple="multiple">
                                <?php $group_list = User::getUserGroups();
                                $groups = unserialize($course['groups']);
                                foreach($group_list as $group):?>
                                    <option value="<?php echo $group['group_id'];?>"><?php echo $group['group_title'];?></option>
                                <?php endforeach; ?>
                                </select>
                            </div>
        
                            <?php if($membership):?>
                            <div class="width-100"><label>Подписка: </label>
        
                                <select class="multiple-select" name="accesses[]" multiple="multiple">
                                    <?php $planes = Member::getPlanes();
                                    $access_arr = unserialize($course['access']);
                                    foreach($planes as $plane):?>
                                    <option value="<?php echo $plane['id'];?>"><?php echo $plane['name'];?></option>
                                    <?php endforeach; ?>
                                </select>
        
                            </div>
                            <?php endif;?>
                    </div>
                </div>
            </div>
            
            
            
            <!-- ПОКУПКА -->
            
            <div>
                <div class="row-line">
                    <div class="col-1-2">
                        <h4>Кнопка для покупки:</h4>
                        <div class="width-100"><label>Направлять на:</label>
                            <div class="select-wrap">
                            <select name="type_access_buy">
                                <option value="0">Не выбран</option>
                                <option value="1">Страницу заказа продукта</option>
                                <option value="2">Внутреннее описание продукта</option>
                                <option value="3">Свой URL адрес</option>
                            </select>
                            </div>
                        </div>
    
                        <div class="width-100"><label>если продукт, то выберите:</label>
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
                        <p class="width-100"><label>если свой URL, то укажите: </label><input type="text" name="link_access_buy" placeholder="http://"></p>
                        
                        <p class="width-100"><label>Надпись на кнопке купить: </label><input type="text" name="button_text"></p>
                    </div>
                    
                    <div class="col-1-2">
                        <h4>Ссылка для просмотра:</h4>
                        <div class="width-100"><label>Направлять на:</label>
                            <div class="select-wrap">
                            <select name="view_desc[type]">
                                <option value="0">Не выбран</option>
                                <option value="1">Внутреннее описание продукта</option>
                                <option value="2">Свой URL адрес</option>
                            </select>
                            </div>
                        </div>
    
                        <div class="width-100"><label>если продукт, то выберите:</label>
                            <div class="select-wrap">
                            <select name="view_desc[product]">
                            <option value="0">Не выбран</option>
                            <?php foreach($product_list as $product):?>
                            <option value="<?php echo $product['product_id'];?>"><?php echo $product['product_name'];?></option>
                            <?php endforeach;?>
                        </select>
                        </div>
                        </div>
                        <p class="width-100"><label>если свой URL, то укажите: </label><input type="text" name="view_desc[link]" placeholder="http://"></p>
                        
                    </div>
                </div>
            </div>
            
            <!-- 3 вкладка -->
            <div>
                <div class="row-line">
                <div class="col-1-2">
                    <h4>Отображение</h4>
                    <div class="width-100"><label>Показать кол-во уроков: </label>
                        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="lessons_count" type="radio" value="1" checked><span>Да</span></label>
                    <label class="custom-radio"><input name="lessons_count" type="radio" value="0"><span>Нет</span></label>
                    </span>
                    </div>
                    
                    <div class="width-100"><label>Описание курса: </label>
                        <span class="custom-radio-wrap">
                    <label class="custom-radio"><input name="show_desc" type="radio" value="1" checked><span>Да</span></label>
                        <label class="custom-radio"><input name="show_desc" type="radio" value="0"><span>Нет</span></label>
                            </span>
                    </div>
                    
                    <div class="width-100"><label>Показать прогресс: </label>
                        <span class="custom-radio-wrap">
                    <label class="custom-radio"><input name="show_progress" type="radio" value="1" checked><span>Да</span></label>
                        <label class="custom-radio"><input name="show_progress" type="radio" value="0"><span>Нет</span></label>
                    </span>
                    </div>
                    
                    <div class="width-100"><label>Показать комментарии: </label>
                        <span class="custom-radio-wrap">
                    <label class="custom-radio"><input name="show_comments" type="radio" value="1" checked><span>Да</span></label>
                        <label class="custom-radio"><input name="show_comments" type="radio" value="0"><span>Нет</span></label>
                    </span>
                        </div>

                    <div class="width-100"><label>Показать просмотры: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="show_hits" type="radio" value="1" checked><span>Да</span></label>
                                <label class="custom-radio"><input name="show_hits" type="radio" value="0"><span>Нет</span></label>
                     </span>
                    </div>
                    
                    <div class="width-100"><label>Показать дату начала: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="show_begin" type="radio" value="1" checked><span>Да</span></label>
                            <label class="custom-radio"><input name="show_begin" type="radio" value="0"><span>Нет</span></label>
                        </span>
                    </div>

                    <!--div class="width-100"><label>Показать учеников: </label>
                        <div class="select-wrap">
                        <select name="show_pupil">
                    <option value="1">Показать</option>
                    <option value="0">Скрыть</option>
                    </select>
                        </div>
                    </div-->
                    <input type="hidden" name="show_pupil" value="0">
                    
                    <div class="width-100"><label>Сортировка уроков: </label>
                        <div class="select-wrap">
                        <select name="sort_less">
                    <option value="1">По возрастанию</option>
                    <option value="0">По убыванию</option>
                    </select>
                        </div>
                    </div>
                    <p>---------</p>
                    <!--p><label>Сертификат обучения: </label><select name="sertificate">
                    <option value="">Нет</option>
                    <?php $sert_list = Course::getCourseSertificateList();
                    if($sert_list):
                    foreach($sert_list as $sert):?>
                    <option value="<?php echo $sert['id'];?>"><?php echo $sert['name'];?></option>
                    <?php endforeach;
                    endif;?>
                    </select></p-->
                    <input type="hidden" name="sertificate" value="">
                </div>
                
                <div class="col-1-2">
                    <h4>Публикация</h4>
                    <p class="width-100"><label>Дата публикации</label><input type="text" autocomplete="off" class="datetimepicker" name="start"></p>
                    <p class="width-100"><label>Дата завершения</label><input type="text" autocomplete="off" class="datetimepicker" name="end"></p>
                    
                    
                </div>
                </div>
            </div>
            
            <!-- 4 вкладка -->
            <div>
                <div class="row-line">
                    <div class="col-1-2">
                    <p>Блоки будут доступны после сохранения курса</p>
                    </div>
                </div>
            </div>
            
            
            <!-- 5 вкладка -->
            <div>
                <div class="row-line">
                    <div class="col-1-2">
                        <h4>SEO</h4>
                        <p class="width-100"><label>Алиас: </label><input type="text" name="alias" placeholder="Алиас курса"></p>
                        <p class="width-100"><label>Title: </label><input type="text" name="title" placeholder="Title курса"></p>
                        <p class="width-100"><label>Meta Description:</label><textarea name="meta_desc" rows="3" cols="40"></textarea></p>
                        <p class="width-100"><label>Meta Keys:</label><textarea name="meta_keys" rows="3" cols="40"></textarea></p>
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