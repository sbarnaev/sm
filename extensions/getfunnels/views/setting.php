<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Настройки GetFunnels</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
        </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li>Настройки GetFunnels</li>
    </ul>
    
    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Сохранено!</div>
    <?php endif?>
    
    <form action="" method="POST">
        <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/nastr-tren.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Настройки GetFunnels</h3>
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
                    <div class="width-100"><label>Статус:</label>
                        <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($enable == 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($enable == 0) echo 'checked';?>><span>Откл</span></label>
                    </span>
                    </div>

                    <div class="width-100"><label>Имя аккаунта</label>
                        <input type="text" name="getfunnels[params][account_name]" value="<?php echo $params['params']['account_name'];?>">
                    </div>
                    
                    <div class="width-100"><label>Секретный ключ</label>
                        <input type="text" name="getfunnels[params][secret_key]" value="<?php echo $params['params']['secret_key'];?>">
                    </div>

                    <div class="width-100"><label>Название поля для передачи ID партнера в заказе</label>
                        <input type="text" name="getfunnels[params][partner_id_fname]" value="<?=isset($params['params']['partner_id_fname']) ? $params['params']['partner_id_fname'] : '';?>">
                    </div>

                    <div class="width-100"><label>Название поля для передачи ID партнера в профиле пользователя</label>
                        <input type="text" name="getfunnels[params][partner_id_fname_to_user]" value="<?=isset($params['params']['partner_id_fname_to_user']) ? $params['params']['partner_id_fname_to_user'] : '';?>">
                    </div>

                    <div class="width-100"><label>Название поля для передачи служебного названия продукта</label>
                        <input type="text" name="getfunnels[params][prod_srv_names_fname]" value="<?=isset($params['params']['prod_srv_names_fname']) ? $params['params']['prod_srv_names_fname'] : '';?>">
                    </div>
                </div>
            </div>
        </div>
        <!-- TODO ссылка на getfunnels
        <div class="reference-link">
            <a class="button-blue-rounding" target="_blank" href=""><i class="icon-info"></i>Справка по расширению</a>
        </div> -->
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>