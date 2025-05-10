<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Добавить изображение</h1>
        <div class="logout">
            <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
        </div>
    </div>
    <form action="" method="POST" enctype="multipart/form-data">
        <ul class="breadcrumb">
            <li>
                <a href="/admin">Дашбоард</a>
            </li>
            <li>
                <a href="/admin/gallery">Галерея</a>
            </li>
            <li>Добавить изображение</li>
        </ul>
        <div class="traning-top">
            <h3 class="traning-title">Добавить изображение</h3>
            <ul class="nav_button">
                <li><input type="submit" name="addimg" value="Создать" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/gallery">Закрыть</a></li>
            </ul>
        </div>
    
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название</label><input type="text" name="title" placeholder="Название изображения"></p>

                    <div class="width-100"><label>Категория</label>
                        <div class="select-wrap">
                        <select name="cat_id">
                        <?php $cat_list = Gallery::getCatList();
                        if($cat_list):
                        foreach($cat_list as $cat):?>
                        <option value="<?php echo $cat['cat_id'];?>"><?php echo $cat['cat_name'];?></option>
                        <?php endforeach;
                        endif;?>
                    </select>
                    </div>
                    </div>
                    <p class="width-100"><label>Файл</label><input type="file" name="image[]" multiple="multiple" required="required"></p>

                    <div class="width-100"><label>Статус</label>
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
                    <h4>Дополнительно</h4>
                    <p class="width-100"><label>Alt</label><input type="text" name="img_alt"></p>
                    <p class="width-100"><label>Описание</label><textarea name="img_desc" rows="3" cols="40"></textarea></p>
                    <p class="width-100"><label>Ссылка</label><input type="url" name="link"></p>
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>