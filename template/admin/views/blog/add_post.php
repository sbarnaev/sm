<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Добавить запись в блог</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/blog/">Список записей блога</a></li>
        <li>Добавить запись в блог</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">

        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Добавить запись в блог</h3>
            <ul class="nav_button">
                <li><input type="submit" name="addpost" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/blog/">Закрыть</a></li>
            </ul>
        </div>

    <div class="tabs">
        <ul>
            <li>Основное</li>
            <li>Дополнительно</li>
        </ul>
        <div class="admin_form">
            <!-- 1 вкладка -->
            <div>
                <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" placeholder="Название" required="required"></p>
                    
                    <div class="width-100"><label>Категория: </label>
                        <div class="select-wrap">
                        <select name="rub_id">
                    <?php if($rubric_list):
                    foreach($rubric_list as $rubric):?>
                    <option value="<?php echo $rubric['id'];?>"><?php echo $rubric['name'];?></option>
                    <?php endforeach;
                    endif; ?>
                    </select>
                    </div>
                    </div>
                    
                    <p class="width-100"><label>Обложка: </label><input type="file" name="cover"></p>
                    <p class="width-100"><label>Alt: </label><input type="text" size="35" name="img_alt" placeholder="Альтернативный текст"></p>
                    
                    <p class="width-100"><label>Краткое описание (интро): </label><textarea rows="5" cols="65" name="short_desc"></textarea></p>
                    
                    <div class="width-100"><label>Автор</label>
                        <div class="select-wrap">
                            <select name="author_id">
                                <option value="">-- Выберите --</option>
                                <?php $admins = User::getAdministrationUser();
                                if($admins){
                                    foreach($admins as $admin):?>
                                <option value="<?php echo $admin['user_id'];?>"><?php echo $admin['user_name'];?></option>
                                <?php endforeach;
                                }?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="width-100"><label>Статус: </label>
                        <div class="select-wrap">
                        <select name="status">
                    <option value="1">Включен</option>
                    <option value="0">Отключен</option>
                    </select>
                        </div>
                    </div>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                
                <div class="col-1-2">
                    <h4>SEO</h4>
                    <p class="width-100"><label>Алиас: </label><input type="text" name="alias" placeholder="Алиас записи"></p>
                    <p class="width-100"><label>Title: </label><input type="text" name="title" placeholder="Title записи"></p>
                    <p class="width-100"><label>Meta Description:</label><textarea name="meta_desc" rows="3" cols="40"></textarea></p>
                    <p class="width-100"><label>Meta Keys:</label><textarea name="meta_keys" rows="3" cols="40"></textarea></p>
                </div>
                
                <div class="col-1-1">
                    <h4>Описание:</h4>
                    <textarea class="editor" name="text"></textarea>
                </div>
                </div>
            </div>
            
            <!-- 2 вкладка -->
            <div>
                <div class="row-line">
                <div class="col-1-2">
                    <h4>Публикация</h4>
                    <p><label>Дата публикации</label><input type="text" class="datetimepicker" name="start" autocomplete="off"></p>
                    <p><label>Дата завершения</label><input type="text" class="datetimepicker" name="end" autocomplete="off"></p>
                </div>
                
                <div class="col-1-2">
                    <div class="width-100"><label>Показывать обложку: </label>
                        <div class="select-wrap">
                        <select name="show_cover">
                            <option value="1">Показать</option>
                            <option value="0">Скрыть</option>
                        </select>
                        </div>
                    </div>
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