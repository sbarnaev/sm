<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Создать письмо автосерии</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/responder/mass/">Массовые рассылки</a></li>
        <li>Создать письмо автосерии</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Создать письмо автосерии</h3>
            <ul class="nav_button">
                <li><input type="submit" name="addauto" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/responder/autoletters/<?php echo $id;?>">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Отправка</h4>
                    <p class="width-100"><label>Отправить через (часов):</label><input type="text" size="3" name="sending"></p>
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
                    <h4>Отправка</h4>
                    <p class="width-100"><label>Задача, цель письма:</label><textarea name="target" cols="55" rows="4"></textarea></p>
                </div>

                <div class="col-1-1">
                    <h4>Письмо:</h4>
                    <p class="width-100"><label>Тема письма: </label><input type="text" name="subject" placeholder="Тема письма" required="required"></p>
                    <p class="width-100"><textarea class="editor" name="letter"></textarea></p>
                    <hr />
                    <p class="width-100"><strong>Теги для подстановки:</strong></p>
                    <p class="width-100">[NAME] - имя подписчика
                        <br>[UNSUBSCRIBE] - ссылка на отписку<br>[PROMO] - выводит промокод, если он существует<br>[PROMO_DESC] - выводит промокод с описанием, если он существует</p>
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