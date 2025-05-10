<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Редактировать сопутствующий продукт для корзины</h1>
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
        <li><a href="<?php echo $setting['script_url'];?>/admin/products/edit/<?php echo $base_id;?>">Редактировать продукт</a></li>
        <li>Редактировать сопутствующий продукт</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Сохранено!</div>';?>
<div class="admin_top admin_top-flex">
    <h3 class="traning-title">Редактировать сопутствующий продукт</h3>
    <ul class="nav_button">
        <li><input type="submit" name="save" value="Сохранить" class="button save button-white font-bold"></li>
        <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/products/edit/<?php echo $base_id;?>">Назад</a></li>
    </ul>
</div>
    <div class="admin_form">
        <div class="row-line">
        <div class="col-1-2">
            <h4><?php $name = Product::getProductName($related['product_id']); echo $name['product_name'];?></h4>
            <p><label>Цена: </label><input type="text" name="price" value="<?php echo $related['price'];?>" placeholder="Цена"></p>
            <p><label>Сортировка: </label> <input type="text" title="Порядок" placeholder="Порядок" name="sort" value="<?php echo $related['sort'];?>"></p>
            <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
        </div>
        <div class="col-1-2">
            <h4>Параметры</h4>
            <input type="hidden" name="show_complects" value="0">
            
            <p>
                <label>Статус:</label>
                <span class="custom-radio-wrap">
                    <label class="custom-radio"><input name="status" value="1" type="radio" <?php if($related['status'] == 1) echo 'checked';?>><span>Вкл</span></label>
                    <label class="custom-radio"><input name="status" value="0" type="radio" <?php if($related['status'] == 0) echo 'checked';?>><span>Откл</span></label>
                </span>
            </p>
        </div>
        <div class="col-1-1">
            <p>Описание для корзины:<br /><textarea class="editor" name="related_desc"><?php echo $related['offer_desc'];?></textarea></p>
        </div>
        </div>
    </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>