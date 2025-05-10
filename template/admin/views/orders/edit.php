<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1><?=System::Lang('EDIT_ORDER');?> <?=$order['order_date'];?> | ID: <?=$order['order_id'];?></h1>
        <div class="logout">
            <a href="<?=$setting['script_url'];?>" target="_blank"><?=System::Lang('GO_SITE');?></a><a href="<?=$setting['script_url'];?>/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>
    
    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/orders/">Заказы</a></li>
        <li>Редактировать заказ</li>
    </ul>
    
    <?php if(isset($_GET['success'])):?><div class="admin_message">Сохранено!</div><?php endif;?>
    
    <form action="" method="POST" id="order" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/zakaz.svg" alt="">
                </div>
                
                <div>
                    <h3 class="traning-title mb-0">Заказ
                        <a title="Ссылка на заказ" target="_blank" href="/pay/<?=$order['order_date'];?>">
                            <?=$order['order_date'];?>
                        </a>
                    </h3>
                    <p class="mt-0">ID: <?=$order['order_id'];?></p>
                </div>
            </div>
            
            <ul class="nav_button">
                <li><input type="submit" name="save" value="<?=System::Lang('SAVE');?>" class="button save button-white font-bold"></li>
                <li class="nav_button__last">
                    <a class="button red-link" href="/admin/orders/">Закрыть</a>
                </li>
            </ul>
        </div>
        
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <p class="width-100"><label><?=System::Lang('ORDER_DATE');?>:</label>
                        <input class="datetimepicker" required type="text" name="order_date" value="<?=date("d.m.Y H:i:s", $order['order_date']);?>">
                    </p>
                    <?php if($order['payment_date'] != null):?>
                        <p class="width-100"><label>Дата оплаты:</label>
                            <input class="datetimepicker" required type="text" name="payment_date" value="<?=date("d.m.Y H:i:s", $order['payment_date']);?>">
                        </p>
                    <?php endif;?>
                    
                    <?php $link = User::getUserIDatEmail($order['client_email']);?>
                    <p class="width-100"><label>
                        <a target="_blank" href="/admin/users/edit/<?=$link;?>">
                            <?=System::Lang('CLIENT_NAME');?>:
                        </a></label>
                        <input type="text" name="name" value="<?=$order['client_name'];?>">
                    </p>
                    
                    <?php $order_info = unserialize(base64_decode($order['order_info']));
                    if(!empty($order_info['surname'])):?>
                        <p class="width-100"><label>Фамилия:</label>
                            <input type="text" name="surname" value="<?=$order_info['surname'];?>">
                        </p>
                    <?php endif;?>
                    
                    <p class="width-100"><label>Email: </label>
                        <input type="text" name="client_email" value="<?=$order['client_email'];?>">
                    </p>
                    <p class="width-100"><label><?=System::Lang('CLIENT_PHONE');?>:</label>
                        <input type="text" name="phone" value="<?=$order['client_phone'];?>">
                    </p>
                    <p class="width-100"><label><?=System::Lang('CITY');?>:</label>
                        <input type="text" name="city" value="<?=$order['client_city'];?>">
                    </p>
                    <p class="width-100"><label><?=System::Lang('POSTCODE');?>:</label>
                        <input type="text" name="index" value="<?=$order['client_index'];?>">
                    </p>
                    <p class="width-100"><label><?=System::Lang('ADDRESS');?>:</label>
                        <textarea cols="40" rows="2" name="address"><?=$order['client_address'];?></textarea>
                    </p>
    
                    <div><label><?=System::Lang('COMMENT');?>:</label>
                        <textarea cols="40" rows="2" name="client_comment"><?=$order['client_comment'];?></textarea>
                    </div>
                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                </div>
    
                <div class="col-1-2">
                    <div class="round-block mb-20">
                        <p class="width-100"><strong>Система оплаты: </strong>
                            <?php if($order['payment_id'] != null) {
                                $payment_data = Order::getPaymentDataForAdmin($order['payment_id']);
                                echo $payment_data['title'];
                            }?>
                        </p>
                        
                        <?php if($order['ship_method_id'] != null):
                            $ship_method = System::getShipMethod($order['ship_method_id']);?>
                            <p class="width-100"><strong>Способ доставки: <?=$ship_method['title'];?></strong></p>
                        <?php endif;

                        if($order['partner_id'] != null):?>
                            <p id="part_id" class="width-100">
                                <?=System::Lang('PARTNER');?>: <a target="_blank" href="/admin/users/edit/<?=$order['partner_id'];?>"><?=$order['partner_id'];?></a>
                                <div id="del_partner">
                                    <a onclick="deletePartner();">Удалить партнера из заказа<i class="icon-remove"></i></a>
                                </div>
                            </p>
                        <?php endif;

                        if($order['sale_id'] != null):?>
                            <p class="width-100"><strong>Акция: </strong>
                                <a target="_blank" href="/admin/sale/edit/<?= $order['sale_id'];?>"><?= $order['sale_id'];?></a>
                            </p>
                        <?php endif;?>

                        <p class="width-100">IP: <?=$order['ip'];?></p>
                        <?php if($order['channel_id'] != 0):?>
                            <p>Канал: <?php $channel = Stat::getChannelData($order['channel_id']);?> <a target="_blank" href="/admin/channels/edit/<?php echo $order['channel_id'];?>"><?php echo $channel['name'];?></a></p>
                        <?php endif;?>

                        <p class="width-100"><label><?=System::Lang('ADMIN_COMMENT');?>:</label>
                            <textarea cols="55" rows="2" name="admin_comment"><?=$order['admin_comment'];?></textarea>
                        </p>
						
                        <?php if($order['order_info'] != null):
                            $order_info = unserialize(base64_decode($order['order_info']));?>
                            <p><strong>Доп. инфо:</strong></p>
                            <p><?php if(isset($order_info['surname'])) echo 'Фамилия: '.$order_info['surname'];?><br />
                                <?php if(isset($order_info['nick_telegram'])) echo 'Ник в телеграм: '.$order_info['nick_telegram'];?><br />
                                <?php if(isset($order_info['nick_instagram'])) echo 'Ник в инстаграм: '. $order_info['nick_instagram'];?><br />
                                <?php if(isset($order_info['org'])) echo 'Организация: '.$order_info['org'];?><br />
                                <?php if(isset($order_info['inn'])) echo 'ИНН: '.$order_info['inn'];?><br />
                                <?php if(isset($order_info['bik'])) echo 'БИК: '.$order_info['bik'];?><br />
                                <?php if(isset($order_info['rs'])) echo 'Счёт: '.$order_info['rs'];?><br />
                                <?php if(isset($order_info['address'])) echo 'Адрес: '.$order_info['address'];?><br />
                                <?php if(isset($order_info['aff2'])) echo 'Партнёр №2: '.$order_info['aff2'];?><br />
                                <?php if(isset($order_info['aff3'])) echo 'Партнёр 32: '.$order_info['aff3'];?><br />
                                <?php if(isset($order_info['aff_summ'])) echo 'Сумма партнёрских: '.$order_info['aff_summ'];?>
                            </p>
                        <?php endif;?>

                        <?php // модуль оплаты post-credit
                        $pos_credit_settings = Order::getPaymentSetting('poscredit');
                        if ($pos_credit_settings['status']):
                            $posCredit = new PosCredit();
                            $pc_orders = $posCredit->getOrders($order['order_id']);
                            if ($pc_orders):?>
                                <p>
                                    <?php foreach ($pc_orders as $key => $pc_order):
                                        $client_status = PosCredit::getClientStatusText($pc_order['client_status']);?>
                                        <?php if($key) echo '<hr>';?>
                                        <p>
                                            <p><strong>ID заявки: </strong><?=$pc_order['profile_id'];?></p>
                                            <p><strong>Статус: </strong><?=$client_status;?></p>
                                            <?php if($pc_order['bank']):?>
                                                <p><strong>Выбранный банк: </strong><?=$pc_order['bank'];?></p>
                                            <?php endif;?>
                                            <p><a target="_blank" href="/order-info/<?=$order['order_date'];?>?profile_id=<?=$pc_order['profile_id'];?>&client_email=<?=$order['client_email'];?>">Информация по рассрочке</a></p>
                                        </p>
                                    <?php endforeach;?>
                                </p>
                            <?php endif;
                        endif;?>
                    </div>

                    <?php $order_info = $order['order_info'] ? unserialize(base64_decode($order['order_info'])) : null;
                    $utm_keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'utm_referrer'];
                    $order_utm = $order['utm'] ? System::getUtmData($order['utm']) : null;?>

                    <div class="nav_gorizontal statistics-tags">
                        <div class="nav_gorizontal__parent-wrap">
                            <div class="nav_gorizontal__parent">
                                <a href="javascript:void(0);" class="nav-click">Метки систем статистики</a>
                                <span class="nav-click icon-arrow-down"></span>
                            </div>

                            <ul class="drop_down">
                                <?php foreach ($utm_keys as $utm_key):?>
                                    <li class="flex flex-nowrap">
                                        <div class="statistics-tags-item__key"><?=$utm_key;?></div>
                                        <div class="statistics-tags-item__val"><?=$order_utm && isset($order_utm[$utm_key]) ? $order_utm[$utm_key] : '...';?></div>
                                    </li>
                                <?php endforeach;?>

                                <li class="flex flex-nowrap">
                                    <div class="statistics-tags-item__key">clientID YM</div>
                                    <div class="statistics-tags-item__val"><?=isset($order_info['userId_YM']) ? $order_info['userId_YM'] : '...';?></div>
                                </li>

                                <li class="flex flex-nowrap">
                                    <div class="statistics-tags-item__key">clientID GA</div>
                                    <div class="statistics-tags-item__val"><?=isset($order_info['userId_GA']) ? $order_info['userId_GA'] : '...';?></div>
                                </li>

                                <li class="flex flex-nowrap">
                                    <div class="statistics-tags-item__key">roistat_visitor</div>
                                    <div class="statistics-tags-item__val"><?=isset($order_info['roistat_visitor']) ? $order_info['roistat_visitor'] : '...';?></div>
                                </li>
                            </ul>
                        </div>
                    </div>


                    <div class="width-100"><label><?=System::Lang('STATUS');?>:</label>
                        <div class="select-wrap">
                            <select name="status">
                                <option value="<?=$order['status'];?>"><?=Order::getStatusText($order['status']);?></option>
                            </select>
                        </div>
                    </div>
                    
                    <?php if($order['installment_map_id'] != 0):?>
                        <div class="width-100">
                            <a href="/admin/installment/map/<?=$order['installment_map_id'];?>" target="_blank">Рассрочка ID <?=$order['installment_map_id'];?></a>
                        </div>
                    <?php endif;?>
    
                    <?php if (isset($acl['change_orders']) && $order['status'] != 1):?>
                        <p class="width-100">
                            <a class="button-red-rounding" target="_blank" onclick="return confirm('Вы действительно хотите подтвердить заказ?'); location.reload()" href="/confirmcustom?key=<?=md5($order['order_id'].$setting['secret_key']);?>&date=<?=$order['order_date'];?>&status=33">Подтвердить заказ</a>
                        </p>
                    <?php endif;?>
                </div>
            </form>
    
            <div class="col-1-1">
                <h4 class="h4-border"><?=System::Lang('ORDER_CONTENT');?></h4>
                <div class="overflow-container">
                    <table class="table-no-border table-tightly">
                        <?php $items = Order::getOrderItems($order['order_id']);
                        $total = 0;
                        if($items):
                            foreach($items as $item):?>
                                <tr<?php if($item['status'] == 0) echo ' class="off"'; elseif($item['status'] == 9) echo ' class="refund"';?>>
                                    <td>
                                        <form class="price-input-form" action="" id="reload__<?=$item['order_item_id'];?>" method="POST">
                                            <input required style="width:60px;margin-right:10px;" placeholder="ID" type="text" name="prod_id" value="<?=$item['product_id'];?>">
                                            <input type="hidden" name="reload_order_item" value="<?=$item['order_item_id'];?>">
                                            <input type="image" src="/template/admin/images/reload.png" title="Обновить" name="reload">
                                        </form>
                                    </td>
                                    
                                    <td><?php $product_data = Product::getProductName($item['product_id']);
                                        echo !empty($product_data['service_name']) ? "{$product_data['service_name']}{$product_data['mess']}" : "{$product_data['product_name']}{$product_data['mess']}";?>
                                    </td>
                                    
                                    <td>
                                        <form class="price-input-form" action="" id="reload_<?=$item['order_item_id'];?>" method="POST">
                                            <div class="price-input-wrap">
                                                <input class="price-input" type="text" name="price" value="<?=$item['price'];?>">
                                                <div class="price-input-cur"><?=$setting['currency'];?></div>
                                            </div>
                                            
                                            <input type="hidden" name="reload_order_item" value="<?=$item['order_item_id'];?>">
                                            <input type="image" src="/template/admin/images/reload.png" title="Обновить" name="reload">
                                        </form>
                                    </td>
                                    
                                    <td><input type="text" style="width:110px" value="<?=$item['pincode'];?>" placeholder="ключ"></td>
                                    <td style="width: 42px;">
                                        <?php if($item['status'] == 1):?>
                                            <form action="" id="order_<?=$item['order_item_id'];?>" method="POST">
                                                <input type="hidden" name="id" value="<?=$item['product_id'];?>">
                                                <input type="hidden" name="pin" value="<?=$item['pincode'];?>">
                                                <input type="hidden" name="order_item" value="<?=$item['order_item_id'];?>">
                                                <input type="hidden" name="email" value="<?=$order['client_email'];?>">
                                                <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                                                <button class="button-return button-black-rounding button-lesson" type="submit" name="refund" value="" title="Сделать возврат"><i class="icon-stat-2"></i></button>
                                            </form>
                                        <?php endif;?>
                                    </td>
                                
                                    <td style="width: 42px;">
                                        <form action="" method="POST" id="del_<?=$item['order_item_id'];?>">
                                            <input type="hidden" name="order_item_delete" value="<?=$item['order_item_id'];?>">
                                            <button class="button-red-rounding button-lesson" type="submit" title="Удалить" name="reload"><span class="icon-remove"></span></button>
                                            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                                        </form>
                                    </td>
                                </tr>
                                <?php $total = $total + $item['price'];
                            endforeach;
                        endif;?>
                    </table>
                </div>
                
                <p class="margin-top-20">Сумма: <?="$total {$setting['currency']}";?></p>

                <?php if($order['status'] != 1):?>
                    <p class="mt-30">
                        <a href="#modal_add_product" data-uk-modal="{center:true}">Добавить товар к заказу</a>
                    </p>
                <?php endif;?>
            </div>
        </div>
    </div>

    <p class="button-delete">
        <a onclick="return confirm('<?=System::Lang('YOU_SHURE');?>?')" href="<?=$setting['script_url'];?>/admin/orders/del/<?=$order['order_id'];?>?token=<?=$_SESSION['admin_token'];?>" title="<?=System::Lang('DELETE');?>">
            <i class="icon-remove"></i>Удалить заказ
        </a>
    </p>

    <?php require_once(__DIR__ . '/add_product.php');
    require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
<link rel="stylesheet" type="text/css" href="/template/admin/css/jquery.datetimepicker.min.css">
<script src="/template/admin/js/jquery.datetimepicker.full.min.js"></script>
<script>
  jQuery('.datetimepicker').datetimepicker({
    format:'d.m.Y H:i:s',
    lang:'ru'
  });
  
   function deletePartner() {
      if (confirm('Вы точно хотите удалить партнера из заказа и начисления ?')) {

         $.ajax({
           url: '/admin/orders/delpartner',
           method: 'post',
           dataType: 'json',
           data: {order_id:"<?php echo $order['order_id'];?>", partner_id:"<?php echo $order['partner_id'];?>", delpartner: 'true'},
           success: function(data) {
             if (data.success) {
                $('#part_id').remove();
                $('#del_partner').empty();
                $('#del_partner').html('<p>Партнер удален...</p>');                 
             }
            }
         });
      }
    };

   $('.statistics-tags .drop_down').click(function() {
     return false;
   });
</script>
</body>
</html>