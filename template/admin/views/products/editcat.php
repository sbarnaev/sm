<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Редактировать категорию</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/products/">Продукты</a>
        </li>
        <li>Редактировать категорию</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">

        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/new-categ.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Редактировать категорию</h3>
                    <p class="mt-0">для продукта</p>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="save" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/category/">Закрыть</a></li>
            </ul>
        </div>

    <div class="admin_form">
        <div class="box2">
            <h4>Основное</h4>
            <p><label>Название: </label><input type="text" name="cat_name" value="<?php echo $cat['cat_name'];?>" placeholder="Название категории" required="required"></p>
            <p>Описание категории:<br /><textarea rows="4" cols="45" name="cat_desc"><?php echo $cat['cat_desc'];?></textarea></p>
            <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
        </div>
        <div class="box2">
            <h4>SEO</h4>
            <p><label>Алиас: </label><input type="text" name="alias" value="<?php echo $cat['cat_alias'];?>" placeholder="Алиас категории"></p>
            <p><label>Title: </label><input type="text" name="title" value="<?php echo $cat['cat_title'];?>" placeholder="Title категории"></p>
            <p class="width-100"><label>Meta Desc: </label><textarea name="cat_meta_desc"><?php echo $cat['cat_meta_desc'];?></textarea></p>
            <p class="width-100"><label>Ключевые слова: </label><textarea name="cat_keys"><?php echo $cat['cat_keys'];?></textarea></p>
        </div>
    </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>