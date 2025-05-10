<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Создать форму подписки</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/responder/mass/">Массовые рассылки</a></li>
        <li>Создать форму подписки</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title mb-0">Создать форму подписки</h3>
            <ul class="nav_button">
                <li><input type="submit" name="made" value="Создать" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/responder/auto/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <div class="width-100"><label>Выберите рассылку: </label>
                      <div class="select-wrap">
                        <select name="delivery">
                    <option value="0">Выберите</option>
                    <?php $delivery_list = Responder::getDeliveryList(2);
                    foreach($delivery_list as $delivery):?>
                    <option value="<?php echo $delivery['delivery_id'];?>"<?php if($id == $delivery['delivery_id']) echo ' selected="selected"';?>><?php echo $delivery['name'];?></option>
                    <?php endforeach;?>
                    </select>
                    </div>
                    </div>

                    <p class="width-100"><label>Поле имя: </label>
                      <div class="select-wrap">
                        <select name="name">
                    <option value="0">Нет</option>
                    <option value="1">Да, обязательное</option>
                    <option value="2">Да, не обязательно</option>
                    </select>
                    </div>
                    </p>

                    <div class="width-100"><label>Поле телефон: </label>
                       <div class="select-wrap">
                        <select name="phone">
                    <option value="0">Нет</option>
                    <option value="1">Да, обязательное</option>
                    <option value="2">Да, не обязательно</option>
                    </select>
                    </div>
                    </div>

                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>

                <div class="col-1-2">
                    <h4>Внешний вид:</h4>
                    <p class="width-100"><label>Заголовок формы: </label><input type="text" name="title" placeholder="Заголовок"></p>
                    <div class="width-100"><label>Стиль формы: </label>
                       <div class="select-wrap">
                        <select name="style">
                    <option value="">Чистая форма</option>
                    <option value="black">Тёмная</option>
                    <option value="light">Светлая</option>
                    </select>
                    </div>
                    </div>

                    <div class="width-100"><label>Редирект: </label>
                        <div class="select-wrap">
                        <select name="target">
                    <option value="0">В этом же окне</option>
                    <option value="1">В новом окне</option>
                    </select>
                    </div>
                    </div>
                    <p class="width-100"><label>Надпись над формой:</label><textarea name="header" rows="3" cols="40"></textarea></p>
                    <p class="width-100"><label>Надпись под формой:</label><textarea name="footer" rows="3" cols="40"></textarea></p>
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