<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('STAT_PRODUCTS');?></h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/stat/">Статистика</a></li>
        <li><?php echo System::Lang('STAT_PRODUCTS');?></li>
    </ul>

    <div class="nav_gorizontal">
        <ul>
            <li><a class="button-blue-border-rounding" href="<?php echo $setting['script_url'];?>/admin/stat/"><?php echo System::Lang('BACK');?></a></li>
        </ul>
            <div class="filter admin_form">
            <form action="" method="POST">
                <div class="order-filter-row">

                    <div class="order-filter-1-4">
                        <div class="datetimepicker-wrap">
                            <input type="text" class="datetimepicker" name="start"<?php if($start) echo ' value="'.date('d.m.Y H:i', $start).'"';?> placeholder="От" autocomplete="off">
                        </div>
                    </div>

                    <div class="order-filter-1-4">
                        <div class="datetimepicker-wrap">
                            <input type="text" class="datetimepicker" name="finish"<?php if($finish) echo ' value="'.date('d.m.Y H:i', $finish).'"';?> placeholder="До" autocomplete="off">
                        </div>
                    </div>

                    <div class="order-filter-button">
                        <div class="order-filter-two-row">
                            <div>
                                <div class="order-filter-submit">
                                    <a class="red-link" href="/admin/stat/product?reset">Сброс</a>
                                    <input class="button-blue-rounding" type="submit" name="filter" value="Фильтр">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="admin_form admin_form--margin-top">
<div class="overflow-container">
    <table class="table text-left">
        <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th><a href="<?php echo $setting['script_url'];?>/admin/stat/product?order=count">Заказов</a></th>
            <th><a href="<?php echo $setting['script_url'];?>/admin/stat/product?order=summ">Сумма</a></th>
        </tr>
        </thead>
        <tbody>
        <?php if($products):
        foreach($products as $product):?>
        <tr>
            <td><?php echo $product['product_id'];?></td>
            <td><a target="_blank" href="<?php echo $setting['script_url'];?>/admin/products/edit/<?php echo $product['product_id'];?>?type=<?php echo $product['type_id'];?>" title="<?php echo $product['product_name'];?>"><?php $p_name = Product::getProductName($product['product_id']); echo $p_name['product_name'];?></a></td>
            <td><?php echo $product['count'];?></td>
            <td><?php echo $product['summ'];?></td>
        </tr>
        <?php endforeach; endif;?>
        </tbody>
    </table>
</div>
    </div>
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