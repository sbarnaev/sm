<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Изменить рассылку</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/responder/mass/">Массовые рассылки</a></li>
        <li>Изменить рассылку</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Изменить рассылку</h3>
            <ul class="nav_button">
                <li><input type="submit" name="edit" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/responder/mass/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">
            <!-- 1 вкладка -->
            <div>
                <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" value="<?php echo $delivery['name'];?>" placeholder="Название" required="required"></p>
                    <p class="width-100"><label>Описание: </label><textarea name="desc" cols="45" rows="4"><?php echo $delivery['delivery_desc'];?></textarea></p>
                    <input type="hidden" name="type" value="1">
                    <input type="hidden" name="letter_id" value="<?php echo $letter['letter_id'];?>">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                
                <div class="col-1-2">
                <h4>Отправка: </h4>
                <p class="width-100">Будет отправлено: <?php echo date("d-m-Y H:i:s", $delivery['send_time']);?></p>
                <p class="width-100"><label>Задача, цель письма:</label><br /><textarea name="target" cols="55" rows="4"><?php echo $letter['target'];?></textarea></p>
                <p class="width-100"><a class="link-remove" onclick="return confirm('Вы уверены?')" href="/admin/responder/del/<?php echo $delivery['delivery_id'];?>?token=<?php echo $_SESSION['admin_token'];?>&type=<?php echo $delivery['type'];?>"><i class="icon-remove"></i> Отменить отправку и удалить рассылку</a></p>
                </div>
                
                <div class="col-1-1">
                    <h4>Письмо:</h4>
                    <p class="width-100"><label>Тема письма: </label><input type="text" name="subject" value="<?php echo $letter['subject'];?>" placeholder="Тема письма" required="required"></p>
                    <p class="width-100"><textarea class="editor" name="letter"><?php echo $letter['body'];?></textarea></p>

                    <div class="width-100 tags_letter">
                    <p><strong>Теги для подстановки:</strong></p>
                    <p>[NAME] - имя подписчика<br>[UNSUBSCRIBE] - ссылка на отписку</p>
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