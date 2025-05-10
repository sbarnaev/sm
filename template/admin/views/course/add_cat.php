<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Создать категорию</h1>
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
        <li>Создать категорию</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">

        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Создать категорию</h3>
            <ul class="nav_button">
                <li><input type="submit" name="addcat" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/courses/cats/">Закрыть</a></li>
            </ul>
        </div>
        
        <div class="admin_form">
            <!-- 1 вкладка -->

            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" placeholder="Имя категории" required="required"></p>
                    
                    <p class="width-100"><label>Обложка: </label><input type="file" name="cover"></p>
                    <p class="width-100"><label>Alt: </label><input type="text" size="35" name="img_alt" placeholder="Альтернативный текст"></p>
                    
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
                    <p class="width-100"><label>Алиас: </label><input type="text" name="alias" placeholder="Алиас категории"></p>
                    <p class="width-100"><label>Title: </label><input type="text" name="title" placeholder="Title категории"></p>
                    <p class="width-100"><label>Meta Description:</label><textarea name="meta_desc" rows="3" cols="40"></textarea></p>
                    <p class="width-100"><label>Meta Keys:</label><textarea name="meta_keys" rows="3" cols="40"></textarea></p>
                </div>
                
                <div class="col-1-1">
                    <label>Описание:</label>
                    <textarea class="editor" name="cat_desc"></textarea>
                </div>
            </div>

        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>