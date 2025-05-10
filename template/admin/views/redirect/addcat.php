<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Создать категорию редиректов</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/redirect/">Редиректы</a></li>
        <li>Создать категорию редиректов</li>
    </ul>
    <form action="" method="POST">

        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Создать категорию редиректов</h3>
            <ul class="nav_button">
                <li><input type="submit" name="addcat" value="Создать" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/redirect/cats/">Закрыть</a></li>
            </ul>
        </div>
    
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" placeholder="Название категории" required="required"></p>

                    <p class="width-100"><label>Описание:</label><textarea name="cat_desc" rows="3" cols="40"></textarea></p>

                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>