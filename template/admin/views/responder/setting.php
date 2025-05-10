<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Настройки E-mail рассылки</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li>Настройки E-mail рассылки</li>
    </ul>
    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/nastr-tren.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Настройки E-mail рассылки</h3>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="save" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/extensions/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">

            <div class="row-line">

            <div class="col-1-2">
                <h4 class="h4-border">Основное</h4>
                <div class="width-100"><label>Функция e-mail рассылки: </label>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($enable == 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($enable == 0) echo 'checked';?>><span>Откл</span></label>
                    </span>
                </div>
                <p class="width-100"><label>Имя подписчика (если не указано):</label><input type="text" name="responder[params][name]" value="<?php echo $params['params']['name'];?>" placeholder="Имя подписчика"></p>
                <div class="width-100"><label>Отсылать пароли новым подписчикам:</label>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="responder[params][send_pass]" type="radio" value="1" <?php if($params['params']['send_pass']== 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="responder[params][send_pass]" type="radio" value="0" <?php if($params['params']['send_pass']== 0) echo 'checked';?>><span>Откл</span></label>
                    </span>
                </div>
                <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
            </div>

            <div class="col-1-2">
                <h4 class="h4-border">Отправка</h4>
                <p class="width-100"><label>Кол-во писем в минуту:</label><input type="text" name="responder[params][count]" value="<?php echo $params['params']['count'];?>" size="3"></p>
                <p class="width-100"><label>Задание планировщика:</label><textarea cols="65" rows="3">php <?php echo ROOT . '/task/email_cron.php'?></textarea></p>
            </div>

            </div>
            
        </div>
        <div class="reference-link">
            <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/category/51415"><i class="icon-info"></i>Справка по расширению</a>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

</body>
</html>