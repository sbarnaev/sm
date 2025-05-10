<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Создать метку отзывов</h1>
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
        <li>
            <a href="/admin/reviews/">Отзывы</a>
        </li>
        <li>Создать метку отзывов</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">

        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/tag1.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Новая метка</h3>
                    <p class="mt-0">для отзыва</p>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="addlabel" value="Создать" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/reviews/labels/">Закрыть</a></li>
            </ul>
        </div>

        <div class="admin_form">
        <div class="row-line">
            <div class="col-1-2">
                <h4>Основное</h4>
                <p class="width-100"><label>Название: </label><input type="text" name="name" placeholder="Название метки" required="required"></p>
                <div class="width-100">
                        <label>Статус: </label>
                        <div class="select-wrap">
                            <select name="status">
                                <option value="1">Включено</option>
                                <option value="0">Отключено</option>
                            </select>
                        </div>
                    </div>
                <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
            </div>
            <div class="col-1-2">
                <h4>SEO</h4>
                <p class="width-100"><label>Алиас: </label><input type="text" name="alias" placeholder="Алиас метки"></p>
            </div>
        </div>
    </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>