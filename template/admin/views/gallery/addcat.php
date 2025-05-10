<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php');?>

<div class="main">
    <div class="top-wrap">
        <h1>Создать категорию</h1>
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
            <li>Создать категорию</li>
        </ul>
        <div class="traning-top">
            <h3 class="traning-title">Создать категорию</h3>
            <ul class="nav_button">
                <li><input type="submit" name="addcat" value="Создать" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/gallery/cats">Закрыть</a></li>
            </ul>
        </div>
    
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название</label><input type="text" name="name" placeholder="Название категррии" required="required"></p>
                    
                    <div class="width-100"><label>Родительская категория</label>
                        <div class="select-wrap">
                        <select name="parent_id">
                        <option value="0">Выбрерите</option>
                        <?php $cat_list = Gallery::getCatList();
                        if($cat_list):
                        foreach($cat_list as $cat):?>
                        <option value="<?php echo $cat['cat_id']?>"><?php echo $cat['cat_name'];?></option>
                        <?php endforeach;
                        endif;?>
                    </select>
                    </div>
                    </div>
                    
                    <p class="width-100"><label>Обложка</label><input type="file" name="cover"></p>
                    <p class="width-100"><label>Описание</label><textarea name="cat_desc" rows="3" cols="40"></textarea></p>
                    
                    <p class="width-100"><label>Порядок</label><input type="text" name="sort" style="width:100px"></p>
                    

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
                    <p class="width-100"><label>Алиас</label><input type="text" name="alias" placeholder="Алиас категории"></p>
                    <p class="width-100"><label>Title</label><input type="text" name="title" placeholder="Title категории"></p>
                    <p class="width-100"><label>Meta Description</label><textarea name="meta_desc" rows="3" cols="40"></textarea></p>
                    <p class="width-100"><label>Meta Keys</label><textarea name="meta_keys" rows="3" cols="40"></textarea></p>
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>