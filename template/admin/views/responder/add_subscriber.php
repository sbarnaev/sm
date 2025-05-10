<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Создать подписчика</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/responder/mass/">Массовые рассылки</a></li>
        <li>Создать подписчика</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title mb-0">Создать подписчика</h3>
            <ul class="nav_button">
                <li><input type="submit" name="add" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/subscribers/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Данные подписчика</h4>
                    <p class="width-100"><label>Имя:</label><input type="text" name="name"></p>
                    <p class="width-100"><label>E-mail:</label><input type="email" name="email" pattern="^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,})+$" required="required"></p>
                    <p class="width-100"><label>Телефон:</label><input type="text" name="phone"></p>
                     <div class="width-100"><label>Требовать подтверждения:</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="confirm" type="radio" value="1"><span>Да</span></label>
                            <label class="custom-radio"><input name="confirm" type="radio" value="0" checked=""><span>Нет</span></label>
                        </span>
                     </div>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>

                <div class="col-1-2">
                    <h4>Подписать на рассылку</h4>
                    <div class="width-100"><label>Рассылка</label>
                       <div class="select-wrap">
                        <select name="delivery">
                    <option value="0">Выберите</option>
                    <?php $delivery_list = Responder::getDeliveryList(2);
                    foreach($delivery_list as $delivery):?>
                    <option value="<?php echo $delivery['delivery_id'];?>"><?php echo $delivery['name'];?></option>
                    <?php endforeach;?>
                    </select>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>