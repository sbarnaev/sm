<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Список инфопродуктов</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a>
            <a href="/admin/logout" class="red">Выход</a>
        </div>
    </div>
    
    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li>Продукты</li>
    </ul>
    
    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap">
                <div class="nav_gorizontal__parent">
                    <a href="javascript:void(0);" class="nav-click button-red-rounding">Создать продукт</a>
                    <span class="nav-click icon-arrow-down"></span>
                </div>
                
                <ul class="drop_down">
                    <li><a href="/admin/products/add">Цифровой продукт</a></li>
                    <li><a href="/admin/products/add?type=2">Физический продукт</a></li>
                    <?php $member = System::CheckExtensension('membership', 1);
                    if($member):?>
                        <li><a href="/admin/products/add?type=3">Продукт мембершип</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            
            <li class="nav_gorizontal__parent-wrap">
                <div class="nav_gorizontal__parent nav_gorizontal__parent-yellow">
                    <a href="javascript:void(0);" class="nav-click button-yellow-rounding">Категории</a>
                    <span class="nav-click icon-arrow-down"></span>
                </div>
                
                <ul class="drop_down">
                    <li><a href="/admin/category/add/">Создать категорию</a></li>
                    <li><a href="/admin/category/">Список категорий</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="filter admin_form">
        <form action="" method="POST">
            <div class="filter-row">
                <div class="max-width-147">
                    <div class="select-wrap">
                        <select name="cat_id">
                            <option value="">Категория</option>
                            <?php $cat_list = Product::getAllCatList();
                            if($cat_list):
                                foreach($cat_list as $cat):?>
                                    <option value="<?=$cat['cat_id']?>"><?=$cat['cat_name']?></option>
                                <?php endforeach;
                            endif;?>
                        </select>
                    </div>
                </div>
                
                <div class="max-width-120">
                    <div class="select-wrap">
                        <select name="type">
                            <option value="">Тип</option>
                            <option value="1" <?php if(isset($type) && $type=="1") echo 'selected="selected"'; ?>>Цифровой</option>
                            <option value="2" <?php if(isset($type) && $type=="2") echo 'selected="selected"'; ?>>Физический</option>
                            <?php if($member):?>
                                <option value="3" <?php if(isset($type) && $type=="3") echo 'selected="selected"'; ?>>Мембершип</option>
                            <?php endif;?>
                        </select>
                    </div>
                </div>
                
                <div class="max-width-120 mr-auto">
                    <div class="select-wrap">
                        <select name="status">
                            <option value="" >Статус</option>
                            <option value="off" <?php if(isset($status) && $status == "off" ) echo 'selected="selected"'; ?> >Отключен</option>
                            <option value="on" <?php if(isset($status) && $status=="on") echo 'selected="selected"'; ?> >Включен</option>
                            <option value="arch" <?php if(isset($status) && $status=="arch") echo 'selected="selected"'; ?> >В архиве</option>
                        </select>
                    </div>
                </div>

                <div>
                    <div class="button-group">
                        <input class="button-blue-rounding" type="submit" name="filter" value="Найти">
                        <a class="red-link" href="/admin/products">Сбросить</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;?>


    <div class="course-list">
        <input type="hidden" name="sort_upd_url" value="/admin/products/updatesort">
        <?php if($list_products):
            foreach($list_products as $product):
                $set = $product['base_id'] != 0 ? ' class="complect"' : '';?>
                <div class="course-list-item d-flex  <?php if($product['status'] == 0) echo ' course-list-item__off';?>">
                    <input type="hidden" name="sort[]" value="<?=$product['product_id'];?>">
                    <div class="course-list-item__left button-drag">
                        <img src="/template/admin/images/icons/<?=$product['status'] == 1 ? 'prod-status-yes.svg' : 'prod-status-no.svg';?>" alt="">
                    </div>
        
                    <div class="course-list-item__center">
                        <h4 class="course-list-item__name">
                            <a href="/admin/products/edit/<?=$product['product_id'];?>?type=<?=$product['type_id'];?>">
                                <?=!empty($product['service_name']) ? $product['service_name'] : $product['product_name'];?>
                            </a>
                        </h4>
                        <?php if(!empty($product['service_name'])):?>
                            <div style="padding:0 0 10px 0; margin-top:-8px; color:#888">
                                <?=$product['product_name'];?>
                            </div>
                        <?php endif;?>
                        
                        <div class="course-list-item__data">
                            <div class="course-list-item__author">
                                <i class="icon-cifr"></i>
                                <?php $type_name = Product::getTypeName($product['type_id']); echo $type_name['type_title'];?>
                            </div>
                            
                            <div>
                                <?php if ($cat_data = Product::getCatData($product['cat_id'])):?>
                                    <i class="icon-list"></i><?=$cat_data['cat_name'];?>
                                <?php endif;?>
                            </div>
                        </div>
                        
                        <p class="course-list-item__descr">
                            <a target="_blank" title="Перейти на страницу заказа" href="/buy/<?=$product['product_id']?>">Страница оплаты</a>
                        </p>
                    </div>
                    
                    <div class="course-list-item__right">
                        <span class="course-list_price" title="Обычная цена">
                            <?="{$product['price']} {$setting['currency']}";?>
                        </span>
                        <?php if($product['red_price'] != 0):?>
                            <span title="Цена со скидкой" class="course-list-old_price">
                                <?="{$product['red_price']} {$setting['currency']}";?>
                            </span>
                        <?php endif;?>
                    </div>
                </div>
            <?php endforeach;
        endif;?>
    </div>

    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>