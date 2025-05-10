<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Создать акцию</h1>
        <div class="logout">
            <a href="<?=$setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?=$setting['script_url'];?>/admin/logout" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/products/">Продукты</a></li>
        <li><a href="/admin/sales/">Акции</a></li>
        <li>Создать акцию</li>
    </ul>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div><img src="/template/admin/images/icons/sale-1.svg" alt=""></div>
                <div><h3 class="traning-title mb-0">Создать акцию</h3></div>
            </div>

            <ul class="nav_button">
                <li><input type="submit" name="addsale" value="Создать" class="button save button-green-rounding"></li>
                <li class="nav_button__last"><a class="button button-red-rounding" href="<?=$setting['script_url'];?>/admin/sales/">Закрыть</a></li>
            </ul>
        </div>

        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-1 mb-0">
                    <h4>Основное</h4>
                </div>
            </div>

            <div class="row-line">
                <div class="col-1-2">
                    <p class="width-100"><label>Название: </label><input type="text" name="name" placeholder="Название акции" required="required"></p>

                    <div class="width-100">
                        <label>Статус: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1" checked=""><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0"><span>Откл</span></label>
                        </span>
                    </div>

                    <div class="width-100">
                        <label>Тип: </label>
                        <div class="select-wrap">
                            <select name="type">
                                <option value="1">Красная цена</option>
                                <option data-show_on="promo_calc_discount_box" value="2">Промо код</option>
                                <!--option value="3">Динамическая</option-->
                            </select>
                        </div>
                    </div>

                    <p class="width-100"><label>Скидка: </label><input type="text" size="4" name="discount" placeholder="Размер скидки"></p>
                    <div class="width-100">
                        <label>Тип скидки: </label>
                        <div class="select-wrap">
                            <select name="discount_type">
                                <option value="summ">Сумма</option>
                                <option value="percent">Проценты</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100 hidden" id="promo_calc_discount_box">
                        <label>Считать скидку для промокода от:</label>
                        <div class="select-wrap">
                            <select name="promo_calc_discount">
                                <option value="1">Базовой цены</option>
                                <option value="2">Красной цены</option>
                            </select>
                        </div>
                    </div>

                    <p class="width-100">
                        <label>Промо код: </label>
                        <input type="text" name="promo" placeholder="Промо код">
                    </p>

                    <div class="width-100">
                        <label>Описание:</label>
                        <textarea rows="4" cols="45" name="desc"></textarea>
                    </div>
                    <?php $extension = System::CheckExtensension('partnership', 1);
                    if($extension):?>
                        <p class="width-100"><label>Привязка к партнёру: </label><input type="text" name="partner_id" placeholder="ID партнёра"></p>
                    <?php endif;?>
                </div>

                <div class="col-1-2">
                    <div class="width-100"><label>Начало</label><input type="text" class="datetimepicker" name="start" autocomplete="off"></div>
                    <p class="width-100"><label>Окончание</label><input type="text" class="datetimepicker" name="finish" autocomplete="off"></p>

                    <div class="width-100">
                        <label>Действует на:</label>
                        <select class="multiple-select" name="product[]" multiple="multiple" required="required" size="10">
                            <?php $product_list = Product::getProductListOnlySelect();
                            foreach ($product_list as $product):?>
                                <option value="<?=$product['product_id'];?>"><?=$product['product_name'];?></option>
                                <?php if($product['service_name']):?>
                                    <option disabled="disabled" class="service-name">(<?=$product['service_name'];?>)</option>
                                <?php endif;
                            endforeach?>
                        </select>
                        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                    </div>
                    <!--p><label>Время действия, часы (динамическая): </label><input type="text" name="duration" placeholder="Часы"></p-->
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