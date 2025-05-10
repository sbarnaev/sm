<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1>Дашбоард</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>

    <div class="row-line admin_form--margin-top">
        <div class="col-1-3 admin_form resultant">
            <div class="resultant_icon"><img src="/template/admin/images/icons/icon-goods.svg" alt=""></div>
            <div class="resultant_text"><span>159</span>Продуктов</div>
        </div>
        <div class="col-1-3 admin_form resultant">
            <div class="resultant_icon"><img src="/template/admin/images/icons/icon-clients.svg" alt=""></div>
            <div class="resultant_text"><span>2661</span>Клиентов</div>
        </div>
        <div class="col-1-3 admin_form resultant">
            <div class="resultant_icon"><img src="/template/admin/images/icons/icon-ruble.png" alt=""></div>
            <div class="resultant_text"><span>1092000</span>Заработано всего</div>
        </div>
    </div>

    <div class="admin_form admin_form--margin-top">
        <div class="row-line">
            <div class="col-1-2">
                <?php
                $ch = curl_init('https://billing-master.ru/news.html');
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                $page = curl_exec($ch);
                curl_close($ch);
                //echo $page;
                ?>
            </div>
            <div class="col-1-2">
                <p class="width-100"><a class="button-green-border-rounding button-100" href="<?php echo $setting['script_url'];?>/admin/products/add">Создать цифровой продукт</a></p>
                <p class="width-100"><a class="button-yellow-border-rounding button-100" href="<?php echo $setting['script_url'];?>/admin/orders">Посмотреть список заказов</a></p>
                <p class="width-100"><a class="button-green-border-rounding button-100" href="<?php echo $setting['script_url'];?>/admin/sales/add">Создать акцию</a></p>
            </div>
        </div>
    </div>


    <div class="admin_form admin_form--margin-top">

        <h2 class="main-h2">Заказы за сегодня</h2>

        <div class="vertical-overflow-container">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th><?php echo System::Lang('CLIENT_NAME');?></th>
                    <th><?php echo System::Lang('PRODUCT');?></th>
                    <th><?php echo System::Lang('DATE_NUMBER_ORDER');?></th>
                    <th><?php echo System::Lang('SUMM');?></th>
                    <th><?php echo System::Lang('COMMENT');?></th>
                    <th><?php echo System::Lang('PARTNER');?></th>
                    <th class="td-last">Action</th>
                </tr>
                </thead>
                <tbody>

                <?php if($order_list):
                foreach($order_list as $order):?>
                <tr<?php echo OrderStatus($order['status']);?>>

                <td><?php echo $order['order_id'];?></td>

                <td><?php $link = User::getUserIDatEmail($order['client_email']);
                    if($link){?>
                    <a target="_blank" href="<?php echo $setting['script_url'];?>/admin/users/edit/<?php echo $link;?>"><?php echo $order['client_name'];?></a>
                    <?php } else echo $order['client_name'];?>
                    <br /><a class="small" target="_blank" href="mailto: <?php echo $order['client_email'];?>"><?php echo $order['client_email'];?></a>
                    <?php echo $order['client_phone'];?></td>

                <td><?php $items = Order::getOrderItems($order['order_id']);
                    $total = 0;
                    foreach($items as $item){
                        $product_data = Product::getProductName($item['product_id']);
                        echo $product_data['product_name'].$product_data['mess'];
                        if($item['type_id'] == 2) echo '<div class="delivery_icon" title="'.System::Lang('HAVE_DELIVERY').'"></div>';
        $total = $total + $item['price'];
        echo '<br />';
        }
        if(!empty($order['admin_comment'])) echo '<div class="admin_comment_in_order" title="'.System::Lang('ADMIN_COMMENT').'"><i class="fas fa-comment-dots"></i></div>';
        ?></td>

        <td><?php echo date("d m Y H:i:s", $order['order_date']);?><br /><a title="Просмотр заказа" href="<?php echo $setting['script_url'];?>/admin/orders/edit/<?php echo $order['order_id'];?>"><?php echo $order['order_date'];?></a></td>

        <td><?php echo $total; ?> <?php echo $setting['currency'];?></td>
        <td><?php echo $order['client_comment'];?></td>
        <td><?php echo $order['partner_id'];?></td>
        <td class="td-last"><?php if($order['status'] == 2):?>
            <a target="_blank" onclick="location.reload()" href="/confirmcustom?key=<?php echo md5($order['order_id'].$setting['secret_key']);?>&date=<?php echo $order['order_date'];?>" title="<?php echo System::Lang('CONFIRM_ORDER');?>"><img src="/template/admin/images/confirm.png" alt="Confirm"></a>
            <?php endif; ?>
            <a class="link-delete" onclick="return confirm('<?php echo System::Lang('YOU_SHURE');?>?')" href="<?php echo $setting['script_url'];?>/admin/orders/del/<?php echo $order['order_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="<?php echo System::Lang('DELETE');?>"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach;
                endif;?>
        </tbody>
        </table>
    </div>
</div>

	<div class="admin_form admin_form--margin-top">
    
    <div class="box3 dash">
        <?php $orders_count = Order::countOrders(1);?>
        <p>Оплачено заказов: <?php echo $orders_count = Order::countOrders(1);?></p>
        <p>Оборот: <?php $orders = Order::getAllOrders(); 
        $summ = 0;
        if($orders){
            foreach($orders as $order){
                $summ += Order::getOrderTotalSum($order['order_id']);
            }
        }
        echo number_format($summ);?> <?php echo $setting['currency'];?></p>
        <p>Средний чек: <?php if($orders_count != 0) echo number_format(round($summ / $orders_count)); else echo '---'?> <?php echo $setting['currency'];?></p>
    </div>
    
    <div class="box3 dash">
        <p>Всего клиентов: <?php $clients = User::getUserListForAdmin('is_client'); 
        if($clients) echo $client_count = count($clients);?></p>
        <p>Доход с 1 клиента: <?php if($clients){
        echo number_format(round($summ / $client_count));?> <?php echo $setting['currency'];
        } else echo 0; ?></p>
        <p>Покупок на 1 клиента: <?php if($clients) echo round($orders_count / $client_count);
        else echo 0;?></p>
    </div>
    
    <div class="box3">
        <p></p>
        <p><a href="<?php echo $setting['script_url'];?>/admin/products/add">Создать цифровой продукт</a></p>
        <p><a href="<?php echo $setting['script_url'];?>/admin/products">Перейти к списку продуктов</a></p>
        <p><a href="<?php echo $setting['script_url'];?>/admin/orders">Посмотреть список заказов</a></p>
        <p><a href="<?php echo $setting['script_url'];?>/admin/sales/add">Создать акцию</a></p>
        <p><a href="<?php echo $setting['script_url'];?>/admin/settings">Перейти в настройки</a></p>
    </div>
    </div>
    
    <div class="admin_form admin_form--margin-top">
    
    <?php $partnership = System::CheckExtensension('partnership', 1);
    $statbox = '';
    if($partnership) $statbox = ' min';?>
    <div class="boxstat<?php echo $statbox;?>">
        
        <h2 class="main-h2">Статистика за сегодня:</h2>
        <?php $today = Stat::CountOrders($start_time);?>
        <div class="stat_row">
            <div class="stat_item">
                <strong><?php echo number_format($today['summ'], 0, '.','.');?> <?php echo $setting['currency'];?></strong><br />
                <span>оплачено</span>
            </div>
            
            <div class="stat_item">
                <strong><?php if($order_list) echo count($order_list);
                else echo 0;?></strong><br />
                <span>заказы</span>
            </div>
            
            <div class="stat_item">
                <strong class="red"><?php echo number_format($today['nosumm'], 0, '.','.');?> <?php echo $setting['currency'];?></strong><br />
                <span>не оплачено</span>
            </div>
        </div>
        
    <?php if($partnership):?>
        <div class="box_aff">
        
        </div>
    <?php endif; ?>
    </div>
    
	</div>

    <div class="row-line">
        <div class="col-1-2">
            <div class="admin_form">
                <h2 class="main-h2">Notice Board (верстка)</h2>
                <div class="vertical-overflow-container">
                    <div class="news_item">
                        <div class="news_date">24 June, 2018</div>
                        <div class="news_data">
                            <div class="news_author">Mar Willy</div>
                            <div class="news_last">5 min ago</div>
                        </div>
                        <div class="news_text">
                            <p>Nimply dummy text of the printing and typesetting indu as been the industry's standard.</p>
                        </div>
                    </div>
                    <div class="news_item">
                        <div class="news_date">26 June, 2018</div>
                        <div class="news_data">
                            <div class="news_author">Steven</div>
                            <div class="news_last">5 min ago</div>
                        </div>
                        <div class="news_text">
                            <p>Nimply dummy text of the printing and typesetting indu as been the industry's standard.</p>
                        </div>
                    </div>
                    <div class="news_item">
                        <div class="news_date">28 June, 2018</div>
                        <div class="news_data">
                            <div class="news_author">Rezab Bian</div>
                            <div class="news_last">5 min ago</div>
                        </div>
                        <div class="news_text">
                            <p>Nimply dummy text of the printing and typesetting indu as been the industry's standard.</p>
                        </div>
                    </div>
                    <div class="news_item">
                        <div class="news_date">30 June, 2018</div>
                        <div class="news_data">
                            <div class="news_author">Steven</div>
                            <div class="news_last">5 min ago</div>
                        </div>
                        <div class="news_text">
                            <p>Nimply dummy text of the printing and typesetting indu as been the industry's standard.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-1-2">
            <div class="admin_form">
                <h2 class="main-h2">Кнопки (верстка)</h2>
                <div class="button-group">
                    <a href="#" class="button-violet">Home</a>
                    <a href="#" class="button-yellow">Button</a>
                    <a href="#" class="button-blue">Details</a>
                    <a href="#" class="button-red">Close</a>
                    <br>
                    <a href="#" class="button-green">Home</a>
                    <a href="#" class="button-yellow-border">Button</a>
                    <a href="#" class="button-blue-border">Details</a>
                    <a href="#" class="button-red-border">Close</a>
                    <br>
                    <a href="#" class="button-green-rounding">Home</a>
                    <a href="#" class="button-yellow-rounding">Button</a>
                    <a href="#" class="button-blue-rounding">Details</a>
                    <a href="#" class="button-red-rounding">Close</a>
                    <br>
                    <a href="#" class="button-green-border-rounding">Home</a>
                    <a href="#" class="button-yellow-border-rounding">Button</a>
                    <a href="#" class="button-blue-border-rounding">Details</a>
                    <a href="#" class="button-red-border-rounding">Close</a>
                </div>
            </div>
        </div>
    </div>

    <div class="admin_form admin_form--margin-top">
        <h2 class="main-h2">Заказы за сегодня - просто верстка</h2>
        <div class="vertical-overflow-container">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th><?php echo System::Lang('PRODUCT');?></th>
                    <th><?php echo System::Lang('CLIENT_NAME');?></th>
                    <th><?php echo System::Lang('DATE_NUMBER_ORDER');?></th>
                    <th><?php echo System::Lang('SUMM');?></th>
                    <th><?php echo System::Lang('STATUS');?></th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>

                <tr>
                    <td>73</td>
                    <td>E-mail рассылка для Биллинг мастер</td>
                    <td>
                        Александр Куртеев<br>alex.kurteev@gmail.com
                    </td>
                    <td>13.04.2019</td>
                    <td><span title="Обычная цена">490 руб</span><br>
                        <span title="Цена со скидкой" class="red_price">390 руб</span></td>
                    <td>
                        <span class="checked-status"></span>
                    </td>
                    <td>
                        <a class="link-edit" href="#"><i class="fas fa-edit"></i></a>
                        <a class="link-delete" onclick="return confirm('Вы уверены?')" href="#" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a>
                    </td>
                </tr>

                <tr>
                    <td>73</td>
                    <td>E-mail рассылка для Биллинг мастер</td>
                    <td>
                        Александр Куртеев<br>alex.kurteev@gmail.com
                    </td>
                    <td>13.04.2019</td>
                    <td><span title="Обычная цена">490 руб</span><br>
                        <span title="Цена со скидкой" class="red_price">390 руб</span></td>
                    <td>
                        <div class="icon-return"></div>
                    </td>
                    <td>
                        <a class="link-edit" href="#"><i class="fas fa-edit"></i></a>
                        <a class="link-delete" onclick="return confirm('Вы уверены?')" href="#" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a>
                    </td>
                </tr>

                <tr>
                    <td>73</td>
                    <td>E-mail рассылка для Биллинг мастер</td>
                    <td>
                        Александр Куртеев<br>alex.kurteev@gmail.com
                    </td>
                    <td>13.04.2019</td>
                    <td><span title="Обычная цена">490 руб</span><br>
                        <span title="Цена со скидкой" class="red_price">390 руб</span></td>
                    <td>
                        <span class="item_status off"></span>
                    </td>
                    <td>
                        <a class="link-edit" href="#"><i class="fas fa-edit"></i></a>
                        <a class="link-delete" onclick="return confirm('Вы уверены?')" href="#" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a>
                    </td>
                </tr>

                <tr>
                    <td>73</td>
                    <td>E-mail рассылка для Биллинг мастер</td>
                    <td>
                        Александр Куртеев<br>alex.kurteev@gmail.com
                    </td>
                    <td>13.04.2019</td>
                    <td><span title="Обычная цена">490 руб</span><br>
                        <span title="Цена со скидкой" class="red_price">390 руб</span></td>
                    <td>
                        <span class="status-close"></span>
                    </td>
                    <td>
                        <a class="link-edit" href="#"><i class="fas fa-edit"></i></a>
                        <a class="link-delete" onclick="return confirm('Вы уверены?')" href="#" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a>
                    </td>
                </tr>

                <tr>
                    <td>73</td>
                    <td>E-mail рассылка для Биллинг мастер</td>
                    <td>
                        Александр Куртеев<br>alex.kurteev@gmail.com
                    </td>
                    <td>13.04.2019</td>
                    <td><span title="Обычная цена">490 руб</span><br>
                        <span title="Цена со скидкой" class="red_price">390 руб</span></td>
                    <td>
                        <span class="status-yes"></span>
                    </td>
                    <td>
                        <a class="link-edit" href="#"><i class="fas fa-edit"></i></a>
                        <a class="link-delete" onclick="return confirm('Вы уверены?')" href="#" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a>
                    </td>
                </tr>

                <tr>
                    <td>73</td>
                    <td>E-mail рассылка для Биллинг мастер</td>
                    <td>
                        Александр Куртеев<br>alex.kurteev@gmail.com
                    </td>
                    <td>13.04.2019</td>
                    <td><span title="Обычная цена">490 руб</span><br>
                        <span title="Цена со скидкой" class="red_price">390 руб</span></td>
                    <td>
                        <span class="checked-status"></span>
                    </td>
                    <td>
                        <a class="link-edit" href="#"><i class="fas fa-edit"></i></a>
                        <a class="link-delete" onclick="return confirm('Вы уверены?')" href="#" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a>
                    </td>
                </tr>
            </tbody>
            </table>
        </div>
    </div>

    <div class="row-line">
        <div class="col-1-2 admin_form">
            <h2 class="main-h2">Формы (вёрстка)</h2>
            <form action="" method="post">
                <div class="row-line">
                    <div class="col-1-1">
                        <div class="label">Input</div>
                        <input type="text">
                    </div>
                    <div class="col-1-1">
                        <div class="label">Textarea</div>
                        <textarea></textarea>
                    </div>
                    <div class="col-1-2">
                        <div class="label">Select</div>
                        <div class="select-wrap">
                            <select>
                                <option>Select 1</option>
                                <option>Select 2</option>
                                <option>Select 3</option>
                                <option>Select 4</option>
                                <option>Select 5</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-1-2">
                        <div class="label">Placeholder</div>
                        <input type="text" placeholder="Это плейсхолдер">
                    </div>
                    <div class="col-1-1">
                        <div class="label">Кнопка загрузки</div>
                        <input type="file">
                    </div>
                    <div class="col-1-1">
                        <label class="custom-chekbox-wrap">
                            <input type="checkbox" name="checkbox" class="agree">
                            <span class="custom-chekbox"></span> Чекбокс
                        </label>
                    </div>
                    <div class="col-1-1">
                        <button type="submit" class="button-violet big-button">Большая кнопка</button>
                    </div>
                </div>
            </form>
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
        $class = ' class="conf" title="Ручной перевод"';
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