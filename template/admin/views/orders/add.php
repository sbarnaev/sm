<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('CREATE_ORDER');?></h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/orders/">Заказы</a>
        </li>
        <li>Создать заказ</li>
    </ul>

    <form action="" method="POST" enctype="multipart/form-data">

        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/zakaz.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0"><?php echo System::Lang('CREATE_ORDER');?></h3>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="add" value="<?php echo System::Lang('SAVE');?>" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/orders/"><?php echo System::Lang('CLOSE');?></a></li>
            </ul>
        </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Сохранено!</div>'; ?>
    <div class="admin_form">

        <div class="row-line">

        <div class="col-1-2">
            <h4><?php echo System::Lang('BASIC');?></h4>
            <p class="width-100"><label><?php echo System::Lang('CLIENT_NAME');?>: </label><input type="text" name="name"></p>
            <p class="width-100"><label>Email: </label><input type="text" name="email"></p>
            <p class="width-100"><label><?php echo System::Lang('CLIENT_PHONE');?>: </label><input type="text" name="phone"></p>
            <p class="width-100"><label><?php echo System::Lang('CITY');?>: </label><input type="text" name="city"></p>
            <p class="width-100"><label><?php echo System::Lang('POSTCODE');?>: </label><input type="text" name="index"></p>
            <p class="width-100"><label><?php echo System::Lang('ADDRESS');?>: </label><textarea cols="40" rows="2" name="address"></textarea></p>
            
            <div class="width-100"><label><?php echo System::Lang('STATUS');?>: </label>
                <div class="select-wrap">
                <select name="status" required>
                <option value="">-- Выберите --</option>
				<option value="1"><?php echo System::Lang('PAID');?></option>
                <option value="0"><?php echo System::Lang('NOT_PAID');?></option>
                <option value="2"><?php echo System::Lang('VERIFY');?></option>
                <option value="7"><?php echo System::Lang('CLIENT_CONFIRM');?></option>
                <option value="9"><?php echo System::Lang('REFUND');?></option>
            </select>
                </div>
            </div>
            
            <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
        </div>
        
        <div class="col-1-2">
            <div class="round-block">
            <p class="width-100"><strong><?php echo System::Lang('PARTNER');?>:</strong> нет</p>
            <p><strong>IP:</strong> ---</p>
            <p class="width-100"><label><?php echo System::Lang('ADMIN_COMMENT');?>: </label><textarea cols="55" rows="2" name="admin_comment"></textarea></p>
            </div>
        </div>
        
        <div class="col-1-1">
            <h4><?php echo System::Lang('ORDER_CONTENT');?></h4>
            
            
            <div class="width-100">

                <select class="multiple-select" name="order_items[]" multiple="multiple" size="10">
            <?php $products = Product::getProductListOnlySelect();
                foreach($products as $product):?>
                <option value="<?php echo $product['product_id']?>"><?php if(!empty($product['service_name'])) echo $product['service_name']; else echo $product['product_name'];?></option>
                <?php endforeach; ?>
            </select>

            </div>
            <div class="width-100"><label>Цена для продуктов в заказе: </label>
                <div class="select-wrap">
                    <select name="price">
                        <option value="1">Обычная цена</option>
                        <option value="0">Цена со скидкой</option>
                        <option value="3">1 <?php echo $setting['currency'];?></option>
                        <option value="2">Нулевая</option>
                        <option value="4">Разделить цену заказа на все продукты поровну</option>
                    </select>
                </div>
            </div>
            
            <div class="width-100"><label>Общая сумма заказа: </label>
                <input type="text" required="required" name="summ">
            </div>
        
        </div>
        </div>
    </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>