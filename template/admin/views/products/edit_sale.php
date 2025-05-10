<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Изменить акцию</h1>
        <div class="logout">
            <a href="<?=$setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?=$setting['script_url'];?>/admin/logout" class="red">Выход</a>
        </div>
    </div>
    
    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/products/">Продукты</a></li>
        <li><a href="/admin/sales/">Акции</a></li>
        <li>Изменить акцию</li>
    </ul>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/sale-1.svg" alt="">
                </div>
                
                <div>
                    <h3 class="traning-title mb-0">Изменить акцию</h3>
                </div>
            </div>
            
            <ul class="nav_button">
                <li><input type="submit" name="edit" value="Сохранить" class="button save button-green-rounding"></li>
                <li class="nav_button__last"><a class="button button-red-rounding" href="/admin/sales/">Закрыть</a></li>
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
                    <p><label>Название:</label>
                        <input type="text" name="name" placeholder="Название акции" value="<?=$sale['name'];?>" required="required">
                    </p>

                    <div class="width-100">
                        <label>Статус:</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio">
                                <input name="status" type="radio" value="1" <?php if($sale['status'] == 1) echo 'checked';?>><span>Вкл</span>
                            </label>
                            <label class="custom-radio">
                                <input name="status" type="radio" value="0" <?php if($sale['status'] == 0) echo 'checked';?>><span>Откл</span>
                            </label>
                        </span>
                    </div>
                    
                    <div class="width-100"><label>Тип:</label>
                        <div class="select-wrap">
                            <select name="type">
                                <option value="1"<?php if($sale['type'] == 1) echo ' selected="selected"';?>>Красная цена</option>
                                <option data-show_on="promo_calc_discount_box, type_discount" value="2"<?php if($sale['type'] == 2) echo ' selected="selected"';?>>Промо код</option>
                                <!--option value="3"<?php // if($sale['type'] == 3) echo ' selected="selected"';?>>Динамическая</option-->
                            </select>
                        </div>
                    </div>
                    
                    <p><label>Скидка:</label>
                        <input type="text" size="4" value="<?=$sale['discount'];?>" name="discount" placeholder="Размер скидки">
                    </p>
                    
                    <div class="width-100">
                        <label>Тип скидки:</label>
                        <div class="select-wrap">
                            <select name="discount_type">
                                <option value="summ"<?php if($sale['discount_type'] == 'summ') echo ' selected="selected"';?>>Сумма</option>
                                <option value="percent"<?php if($sale['discount_type'] == 'percent') echo ' selected="selected"';?>>Проценты</option>
                            </select>
                        </div>
                    </div>
    
                    <div class="width-100 hidden" id="promo_calc_discount_box">
                        <label>Считать скидку для промокода от:</label>
                        <div class="select-wrap">
                            <select name="promo_calc_discount">
                                <option value="1"<?php if($sale['promo_calc_discount'] == '1') echo ' selected="selected"';?>>Базовой цены</option>
                                <option value="2"<?php if($sale['promo_calc_discount'] == '2') echo ' selected="selected"';?>>Красной цены</option>
                            </select>
                        </div>
                    </div>
                    
                    <p><label>Промо код:</label>
                        <input type="text" name="promo" value="<?=$sale['promo_code'];?>" placeholder="Промо код">
                    </p>
                    <p><label>Описание:</label>
                        <textarea rows="4" cols="45" name="desc"><?=$sale['sale_desc'];?></textarea>
                    </p>
                    <?php $extension = System::CheckExtensension('partnership', 1);
                    if($extension):?>
                        <p><label>Привязка к партнёру:</label>
                            <input type="text" value="<?=$sale['partner_id'];?>" name="partner_id" placeholder="ID партнёра">
                        </p>
                    <?php endif;?>
                </div>
                
                <div class="col-1-2">
                    <p><label>Начало:</label>
                        <input type="text" class="datetimepicker" value="<?=date("d.m.Y H:i", $sale['start']);?>" name="start" autocomplete="off">
                    </p>
                    
                    <p><label>Завершение:</label>
                        <input type="text" class="datetimepicker" value="<?=date("d.m.Y H:i", $sale['finish']);?>" name="finish" autocomplete="off">
                    </p>

                    <div class="width-100"><label>Действует на:</label><br />
                        <select name="product[]" multiple="multiple" class="multiple-select" size="10">
                            <?php $product_list = Product::getProductListOnlySelect();
                            foreach ($product_list as $product):
                                $products_arr = unserialize($sale['products']);?>
                                <option value="<?=$product['product_id'];?>"<?php if($products_arr != null && in_array($product['product_id'], $products_arr)) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                <?php if ($product['service_name']):?>
                                    <option disabled="disabled" class="service-name">(<?=$product['service_name'];?>)</option>
                                <?php endif;
                            endforeach;?>
                        </select>
                        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                    </div>
                    
                    <?php /*<p><label>Время действия, часы (динамическая):</label><input type="text" value="<?php echo $sale['duration'];?>" name="duration" placeholder="Часы"></p>*/?>
                </div>
            </div>

            <?php $calc_sales = Product::getCountAndSumToOrdersSale($sale['id']);?>
            <div class="row-line mt-20">
                <div class="col-1-1">
                    <div class="paid_message">
                        <?php if($calc_sales):
                            if($sale['type'] == 2):?>
                                <p><?="Промо код использовался: {$calc_sales['count']} раз на сумму {$calc_sales['summ']} {$setting['currency']}";?></p>
                            <?php else:?>
                                <p><?="По этой акции оплачено заказов: {$calc_sales['count']}, на сумму {$calc_sales['summ']} {$setting['currency']}";?></p>
                            <?php endif;
                        else:?>
                            <p>Промо код еще не использовался</p>
                        <?php endif?>
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