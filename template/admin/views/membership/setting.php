<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Настройки мембершипа</h1>
    <div class="logout">
        <a href="/" target="_blank">На сайт >></a>  <a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li>Настройки мембершипа</li>
    </ul>
    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/nastr-tren.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Настройки мембершипа</h3>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="savemember" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/extensions/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4 class="h4-border">Основное</h4>
                    <p><label>Мембершип: </label>
                        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($enable == 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($enable == 0) echo 'checked';?>><span>Откл</span></label>
                    </span>
                    </p>
                    <p><label title="Рекомендуется 3-5 дней">Через сколько дней выключать просроченную подписку:</label><input type="text" size="40" name="member[params][expires]" value="<?php if(isset($params['params']['expires'])) echo $params['params']['expires']?>"></p>

                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                <div class="col-1-2">
                    <h4 class="h4-border">Задание CRON:</h4>
                        <div><textarea cols="65" rows="3">php <?php echo ROOT ?>/task/member_cron.php</textarea></div>
                </div>
            </div>

            
        </div>
        <div class="reference-link">
            <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/category/51275"><i class="icon-info"></i>Справка по расширению</a>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

</body>
</html>