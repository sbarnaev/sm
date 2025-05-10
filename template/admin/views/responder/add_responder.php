<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Создать автосерию писем</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
        <ul class="breadcrumb">
            <li>
                <a href="/admin">Дашбоард</a>
            </li>
            <li><a href="/admin/responder/mass/">Массовые рассылки</a></li>
            <li>Создать автосерию писем</li>
        </ul>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Создать автосерию писем</h3>
            <ul class="nav_button">
                <li><input type="submit" name="add" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/responder/auto/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" placeholder="Название" required="required"></p>
                    <p class="width-100"><label>Описание: </label><textarea name="desc" cols="45" rows="4"></textarea></p>
                    <input type="hidden" name="type" value="2">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>

                <div class="col-1-1">
                    <h4>Требовать подтверждения:</h4>
                    <div class="width-100"><label>Подтверждение e-mail: </label>
                       <div class="select-wrap">
                        <select name="confirmation">
                    <option value="0">Нет</option>
                    <option value="1">Всегда</option>
                    <option value="2">При подписке через форму</option>
                    </select>
                    </div>
                    </div>
                    
                    <h4>Текст после подтверждения email:</h4>
                    <p class="width-100"><textarea name="after_confirm_text" class="editor"></textarea></p>
                </div>

                <div class="col-1-1">
                    <h4>Письмо подтверждения:</h4>
                    <p class="width-100"><label>Тема письма: </label><input type="text" name="confirm_subject"></p>
                    <p class="width-100"><label>Текст письма: </label><textarea name="confirm_body" class="editor"></textarea></p>
                    <hr />
                    <div class="width-100 tags_letter">
                    <p><strong>Теги для подстановки:</strong></p>
                    <p>[NAME] - имя подписчика<br>[DELIVERY] - имя рассылки<br>[EMAIL] - емейл подписчика<br>[CONFIRM_LINK] - ссылка для подтверждения</p>
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