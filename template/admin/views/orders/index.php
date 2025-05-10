<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1><?=System::Lang('ORDER_LIST');?></h1>
        <div class="logout">
            <a href="<?=$setting['script_url'];?>" target="_blank"><?=System::Lang('GO_SITE');?></a><a href="<?=$setting['script_url'];?>/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>
    
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Заказы</li>
    </ul>
    
    <div class="nav_gorizontal">
        <ul class="flex-right">
            <li>
                <a class="button-red-rounding" href="<?=$setting['script_url'];?>/admin/orders/add"><?=System::Lang('CREATE_ORDER');?></a>
            </li>
        </ul>
    </div>
    
    <form action="" method="POST">
        <div class="admin_form">
            <div class="order-filter-row">
                <div class="order-filter-1-4">
                    <input type="text" name="email" value="<?=$filter && $filter['email'] ? $filter['email'] : '';?>" placeholder="E-mail">
                </div>
                
                <div class="order-filter-1-4">
                    <input type="text" name="number"  value="<?=$filter && $filter['number'] ? $filter['number'] : '';?>" placeholder="Номер">
                </div>
                
                <div class="order-filter-1-4">
                    <div class="select-wrap">
                        <select name="status">
                            <option value="">Статус</option>
                            <option value="ok"<?php if($filter && $filter['status'] == 'ok') echo ' selected="selected"';?>>Оплачен</option>
                            <option value="no"<?php if($filter && $filter['status'] == 'no') echo ' selected="selected"';?>>Не оплачен</option>
							<option value="inst"<?php if($filter && $filter['status'] == 'inst') echo ' selected="selected"';?>>Рассрочка</option>
                            <option value="check"<?php if($filter && $filter['status'] == 'check') echo ' selected="selected"';?>>Требует проверки</option>
                            <option value="confirm"<?php if($filter && $filter['status'] == 'confirm') echo ' selected="selected"';?>>Подтверждён клиентом</option>
                            <option value="refund"<?php if($filter && $filter['status'] == 'refund') echo ' selected="selected"';?>>Возврат</option>
                        </select>
                    </div>
                </div>

                <div class="order-filter-1-4">
                    <div class="select-wrap">
                        <select name="paid">
                            <option value="">Тип заказа</option>
                            <option value="1"<?php if($filter && $filter['paid'] == 1) echo ' selected="selected"';?>>Платные</option>
                            <option value="2"<?php if($filter && $filter['paid'] == 2) echo ' selected="selected"';?>>Бесплатные</option>
                        </select>
                    </div>
                </div>

                <div class="order-filter-1-4">
                    <div class="select-wrap">
                        <select name="product_id">
                            <option value="">Продукт</option>
                            <?php $products = Product::getProductListOnlySelect();
                            if ($products):
                                foreach ($products as $product):?>
                                    <option value="<?=$product['product_id'];?>"<?php if($filter && $product['product_id'] == $filter['product_id']) echo ' selected="selected"';?>><?=$product['product_name'];?></option>
                                    <?php if($product['service_name']):?>
                                        <option disabled="disabled" class="service-name">(<?=$product['service_name'];?>)</option>
                                    <?php endif;
                                endforeach;
                            endif;?>
                        </select>
                    </div>
                </div>

                <div class="order-filter-1-4">
                    <div class="datetimepicker-wrap">
                        <input type="text" class="datetimepicker" name="start"<?php if($filter && $filter['start']) echo ' value="'.date('d.m.Y H:i', $filter['start']).'"';?> placeholder="От" autocomplete="off">
                    </div>
                </div>
                
                <div class="order-filter-1-4">
                    <div class="datetimepicker-wrap">
                        <input type="text" class="datetimepicker" name="finish"<?php if($filter && $filter['finish']) echo ' value="'.date('d.m.Y H:i', $filter['finish']).'"';?> placeholder="До" autocomplete="off">
                    </div>
                </div>
                <!--
                <div class="order-filter-2-4">
                    Тут будет название продукта
                </div>
                -->
                <div class="order-filter-button">
                    <div class="order-filter-two-row">
                        <div>
                            <div class="order-filter-result">
                                <?php if($filter && $filter['is_filter']):?>
                                    <div><p>Отфильтровано: <?=$total_order;?> объекта</p></div>
                                <?php endif;?>
                                <input class="csv__link"  type="submit" name="load_csv" value="Выгрузить в csv">
                            </div>
                        </div>
                        
                        <div>
                            <div class="order-filter-submit">
                                <?php if($filter && $filter['is_filter']):?>
                                    <a class="order-filter-reset" href="/admin/orders?reset">Сбросить</a>
                                <?php endif;?>
                                <input class="button-blue-rounding" type="submit" name="filter" value="Найти">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;?>
    
    <div class="admin_form admin_form--margin-top">
        <div class="overflow-container">
            <table class="table table-sort">
                <thead>
                    <tr>
                        <th class="text-left">Номер</th>
                        <th class="text-left"><?=System::Lang('CLIENT_NAME');?></th>
                        <th class="text-left"><?=System::Lang('PRODUCT');?></th>
                        <th><?=System::Lang('SUMM');?></th>
                        <th><?=System::Lang('STATUS');?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(!empty($order_list)):
                        foreach($order_list as $order):?>
                            <tr class=" <?=OrderStatus($order['status']);?>">
                                <td class="text-left" style="width: 135px;">
                                    <a title="Просмотр заказа" href="/admin/orders/edit/<?=$order['order_id'];?>"><?=$order['order_date'];?></a><br />
                                    <?=date("d.m.Y H:i", $order['order_date']);?><br />
                                    <?php if(OrderTask::checkErrors2Order($order['order_id'])):?>
                                        <span title='Есть ошибки по крон-событиям' style="font-size:80%;color:#ff0000"><?=$order['order_id'];?></span>
                                    <?php else:?>
                                        <span style="font-size:80%;color:#888"><?=$order['order_id'];?></span>
                                    <?php endif;?>
                                </td>

                                <td class="text-left"><?php $link = User::getUserIDatEmail($order['client_email']);
                                    if($link):?>
                                        <a target="_blank" href="/admin/users/edit/<?=$link;?>"><?=urldecode($order['client_name']);?></a>
                                    <?php else:
                                        echo $order['client_name'];?>
                                    <?php endif;?>
                                    <br /><span class="small link-inherit"><?=$order['client_email'];?></span>
                                </td>

                                <td class="text-left"><?php $items = Order::getOrderItems($order['order_id']);
                                    $total = 0;
                                    if($items):
                                        foreach($items as $item):
                                            $product_data = Product::getProductName($item['product_id']);
                                            $total = $total + $item['price'];
                                            echo $product_data['product_name'].$product_data['mess'];
                                            if($item['type_id'] == 2):?>
                                                <div class="delivery_icon" title="<?=System::Lang('HAVE_DELIVERY');?>"></div>
                                            <?php endif;?><br />
                                        <?php endforeach;
                                    endif;

                                    if(!empty($order['admin_comment'])):?>
                                        <div class="admin_comment_in_order" title="<?=System::Lang('ADMIN_COMMENT');?>">
                                            <i class="fas fa-comment-dots"></i>
                                        </div>
                                    <?php endif;?>
                                </td>

                                <td class=""><?=$total; ?> <?=$setting['currency'];?></td>

                                <td>
                                    <?php if($order['installment_map_id'] != 0):?>
                                        <img src="/template/admin/images/icons/installment.png" title="Рассрочка">
                                    <?php endif;?>

                                    <?php if($order['status'] == 1) echo '<span class="checked-status"></span>';?>
                                    <?php if($order['status'] == 0) echo '<a style="text-decoration:none; color:#E04265" onclick="return confirm(\'Вы уверены что хотите удалить этот заказ?\')" href="/admin/orders/del/'.$order['order_id'].'?token='.$_SESSION['admin_token'].'"><span class="icon-stopwatch"></span></a>';?>
                                    <?php if($order['status'] == 2) echo '<a style="text-decoration:none" target="_blank" onclick="return confirm(\'Вы уверены что хотите подтвердить оплату этого заказа?\')" href="/confirmcustom?key='.md5($order['order_id'].$setting['secret_key']).'&date='.$order['order_date'].'"><span class="status-close"></span></a>';?>
                                    <?php if($order['status'] == 9) echo '<span class="status-return"></span>';?>
                                    <?php if($order['status'] == 7) echo '<span class="status-time"></span>';?>
                                </td>
                            </tr>
                        <?php endforeach;
                    endif;?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if($is_pagination == true) echo $pagination->get();?>
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

<?php function OrderStatus($status)
{
    switch ($status){
        case 2 :
        $class = ' conf" title="Ручной перевод - нажмите на иконку чтобы подтвердить оплату"';
        break;

        case 0 :
        $class = ' off" title="Не оплачен"';
        break;

        case 7 :
        $class = ' send" title="Подтверждён клиентом"';
        break;

        case 9 :
        $class = ' refund" title="Возврат"';
        break;

        default :
        $class = '"';
    }

    return $class;
}
?>