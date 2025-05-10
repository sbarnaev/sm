<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Редактировать платёжный модуль</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/paysettings/">Список платёжных модулей</a></li>
        <li>Редактировать платёжный модуль</li>
    </ul>
    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Редактировать платёжный модуль</h3>
            <ul class="nav_button">
                <li><input type="submit" name="savepayments" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/paysettings/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">
            <div class="row-line">
            <div class="col-1-2">
                <h4 class="h4-border"><?php echo $payment['title'];?></h4>
                <p class="width-100"><label>Название: </label><input type="text" name="title" value="<?php echo $payment['title'];?>"></p>
                <p class="width-100"><label>Название для пользователей: </label><input type="text" name="public_title" value="<?php echo $payment['public_title'];?>"></p>
                <p class="width-100"><label>Порядок вывода: </label><input type="text" size="3" value="<?php echo $payment['sort'];?>" name="sort"></p>
                <div class="width-100"><label>Статус: </label>
                    <span class="custom-radio-wrap">
                        <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($payment['status'] == 1) echo 'checked';?>><span>Вкл</span></label>
                        <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($payment['status'] == 0) echo 'checked';?>><span>Откл</span></label>
                    </span>
                </div>
            </div>
            
            <?php if($payment['name'] == 'cloudpayments'):?>
            <div class="col-1-2">
                <script>
                    $(document).ready(function(){
                        $('.button_req').click(function () {
                            $(this).next('.div_req').toggleClass('open');
                        });
                    });
                </script>
                <style>
                    .button_req {text-decoration:none; border-bottom: 1px dashed #555; overflow: hidden;}
                    .div_req {height:0; visibility:hidden; transition: 0.5s;}
                    .div_req.open {height:700px; visibility:visible; padding:0.5em 0 0 0; transition: 0.5s}
                    .cloud {margin-top:0}
                </style>
                <h4 class="h4-border cloud">Быстрое подключение</h4>
                <p>Через эту форму вы можете отправить заявку на подключение, никуда ходить не надо, менеджер CloudPyaments свяжется с вами через несколько минут.</p>
                
                <a class="button_req" href="javascript:void(0);">Заполнить форму</a>
                <div class="div_req">
                    <p class="width-100"><label>Наименование юрлица (ИП): </label><input form="cloud" type="text" required="required" name="orgname"></p>
                    <p class="width-100"><label>ОГРН: </label><input form="cloud" type="text" required="required" name="ogrn"></p>
                    <p class="width-100"><label>ИНН: </label><input form="cloud" type="text" required="required" name="inn"></p>
                    <p class="width-100"><label>Телефон: </label><input form="cloud" type="text" required="required" name="phone"></p>
                    <p class="width-100"><label>Email: </label><input form="cloud" type="text" required="required" name="email"></p>
                    <p><label>Юр.адрес:</label>
                    <textarea form="cloud" required="required" name="address"></textarea></p>
                    <p class="width-100"><label>Примерный оборот в рублях: </label><input form="cloud" type="text" name="value"></p>
                    <p><a href="https://static.cloudpayments.ru/docs/oferta_itv.pdf" target="_blank">Ознакомится с договором эквайринга</a></p>
                    <p><input type="submit" name="cloud" form="cloud" class="button save button-green-rounding add-prod-but" value="Отправить заявку"></p>
                </div>
            </div>
            <?php endif;?>

                <div class="col-1-1">
                    <div><label>Описание: </label>
                        <textarea name="payment_desc" class="editor"><?php echo $payment['payment_desc'];?></textarea></div>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
        </div>



                    <?php require (ROOT . '/payments/'. $payment['name'].'/params.php'); ?>


        </div>
    </form>
    <form id="cloud" method="POST" action="https://lk.school-master.ru/cloudpayments">
        <input type="hidden" name="url" value="<?=$setting['script_url'];?>">
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

</body>
</html>