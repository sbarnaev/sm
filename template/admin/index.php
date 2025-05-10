<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Дашбоард</h1>
        
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a>
            <a href="/admin/logout" class="red">Выход</a>
        </div>
    </div>
    
    <?php if(isset($acl['show_perms'])):
        $orders_count = Order::countOrders(1);
        $client_count = User::countRegUsers(0, 1);?>
        <h2 class="main-h2">Основные показатели за все время</h2>
    
        <div class="resultant-row">
            <!-- Всего клиентов -->
            <div class="resultant" >
                <div class="resultant_icon" title="На 1 клиента приходится <?=$client_count ? round($orders_count / $client_count) : 0;?> заказов ">
                    <img src="/template/admin/images/icons/main1.png" alt="">
                </div>
                
                <div class="resultant_text">
                    <span><?=$client_count ? $client_count : 0;?></span>Клиентов
                </div>
            </div>

            <!-- Заработано -->
            <div class="resultant" title="Всего заработано">
                <div class="resultant_icon">
                    <img src="/template/admin/images/icons/main2.png" alt="">
                </div>
                
                <div class="resultant_text">
                    <span>
                        <span style="display: none;"><?php echo $orders_count;?></span>
                        <?php $summ = Order::getOrdersTotalSum();
                        echo ($summ ? $summ : 0).' '.$setting['currency'];?>
                    </span>Заработано
                </div>
            </div>


            <!-- Оплачено заказов -->
            <div class="resultant" title="Доход с 1 клиента <?=$client_count ? round($summ / $client_count).' '.$setting['currency'] : 0;?>">
                <div class="resultant_icon">
                    <img src="/template/admin/images/icons/main3.png" alt="">
                </div>
                
                <div class="resultant_text">
                    <span><?=($orders_count != 0 ? round($summ / $orders_count) : '---').' '.$setting['currency'];?></span>Средний чек
                </div>
            </div>
        </div>
    <?php endif;?>


    <?php if(isset($acl['show_stat'])):?>
        <h2 class="main-h2">Статистика за сегодня</h2>
            <?php $new_users = User::countRegUsers($start_time); // кол-во подписчиков за сегодня
            $new_clients = User::countRegUsers($start_time, 1);
            $today = Stat::CountOrders($start_time);?>
            <div class="resultant-row">
                <div class="resultant resultant-1-4">
                    <div class="resultant_text-2">
                        <span><?=$new_users ? $new_users : 0;?></span>
                        Подписчиков
                    </div>
                </div>

                <div class="resultant resultant-1-4">
                    <div class="resultant_text-2">
                        <span><?php echo $today['pay'];?></span>
                        Заказов
                    </div>
                </div>

                <div class="resultant resultant-1-4">
                    <div class="resultant_text-2">
                        <span><?=(!empty($today['summ']) ? $today['summ']: 0).' '.$setting['currency'];?></span>
                        Заработано
                    </div>
                </div>

                <div class="resultant resultant-1-4">
                    <div class="resultant_text-2">
                        <span><?=($today['summ'] > 0 ? round($today['summ'] / $today['pay']) : 0).' '.$setting['currency'];?></span>
                        Средний чек
                    </div>
                </div>
            </div>

            <?php if($partnership):?>
                <div class="box_aff"></div>
            <?php endif; ?>
    <?php endif; ?>

    <?php if(isset($acl['show_orders'])):?>
        <?php if($order_list):?>
            <h2 class="main-h2">Заказы за сегодня</h2>
            
            <div class="admin_form">
                <div class="vertical-overflow-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-left"><?php echo System::Lang('Номер');?></th>
                                <th class="text-left"><?php echo System::Lang('CLIENT_NAME');?></th>
                                <th class="text-left"><?php echo System::Lang('PRODUCT');?></th>
                                <th><?php echo System::Lang('SUMM');?></th>
                                <th><?php echo System::Lang('STATUS');?></th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <?php foreach($order_list as $order):?>
                                <tr<?=OrderStatus($order['status']);?>>
                                    <td class="text-left"><a title="Просмотр заказа" href="/admin/orders/edit/<?php echo $order['order_id'];?>"><?php echo $order['order_date'];?></a></td>
                                    <td class="text-left">
                                        <?php $link = User::getUserIDatEmail($order['client_email']);
                                        if($link):?>
                                            <a target="_blank" href="/admin/users/edit/<?=$link;?>">
                                                <?=$order['client_name'];?>
                                            </a>
                                        <?php else:
                                            echo $order['client_name'];
                                        endif?>
                                        <br />
                                        <span class="small link-inherit"><?=$order['client_email'];?></span>
                                        <?=$order['client_phone'];?>
                                    </td>
            
                                    <td class="text-left">
                                        <?php $items = Order::getOrderItems($order['order_id']);
                                        $total = 0;
                                        if($items):
                                            foreach($items as $item):
                                                $product_data = Product::getProductName($item['product_id']);
                                                echo $product_data['product_name'].$product_data['mess'];
                                                if($item['type_id'] == 2):?>
                                                    <div class="delivery_icon" title="<?=System::Lang('HAVE_DELIVERY')?>"></div>
                                                <?php endif;
                                                $total  += $item['price'];?>
                                                <br />
                                            <?php endforeach;
                                        endif;
                                        
                                        if(!empty($order['admin_comment'])):?>
                                            <div class="admin_comment_in_order" title="<?=System::Lang('ADMIN_COMMENT');?>">
                                                <i class="fas fa-comment-dots"></i>
                                            </div>
                                        <?php endif;?>
                                    </td>
            
                                    <td class="font-16"><?=$total.' '.$setting['currency'];?></td>
                                    <td>
                                        <?php if($order['status'] == 1):?>
                                            <span class="checked-status"></span>
                                        <?php endif;?>
                
                                        <?php if($order['status'] == 0):?>
                                            <span class="icon-stopwatch"></span>
                                        <?php endif;?>
                
                                        <?php if($order['status'] == 2):?>
                                            <a style="text-decoration:none" target="_blank" onclick="return confirm(\'Вы уверены что хотите подтвердить оплату этого заказа?\')" href="/confirmcustom?key=<?=md5($order['order_id'].$setting['secret_key'])?>&date=<?=$order['order_date'];?>">
                                                <span class="status-close"></span>
                                            </a>
                                        <?php endif;?>
                
                                        <?php if($order['status'] == 9):?>
                                            <span class="status-return"></span>
                                        <?php endif;?>
                
                                        <?php if($order['status'] == 7):?>
                                            <span class="status-time"></span>
                                        <?php endif;?>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else:?>
            <h2 class="main-h2">Сегодня заказов ещё не было</h2>
        <?php endif;?>
    <?php endif;?>
    
    <h2 class="main-h2">Новости</h2>
    <div class="admin_form">
        <div class="row-line">
            <div class="col-1-1">
                <?php $site = 'https://lk.school-master.ru/news.php?my='.$setting['script_url'];
                    $ch = curl_init($site);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                    $page = curl_exec($ch);
                    curl_close($ch);
                ?>
            </div>
        </div>
    </div>

    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>
<?php function OrderStatus($status)
{
    switch ($status){
        case 2 : 
        $class = ' class="conf" title="Ручной перевод - нажмите на иконку чтобы подтвердить оплату"';
        break;
        
        case 0 : 
        $class = ' class="off" title="Не оплачен"';
        break;
        
        case 7 : 
        $class = ' class="send" title="Подтверждён клиентом"';
        break;
        
        case 9 : 
        $class = ' class="refund" title="Возврат"';
        break;
        
        default : 
        $class = '';
    }
    
    return $class;
}
?>