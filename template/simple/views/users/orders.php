<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/main_menu.php');?>

<div id="content">
    <div class="layout" id="lk">
        <div class="content-wrap">
            <div <?php if($sidebar) echo 'class="_min content-with-sidebar"';?>>
                <div class="pay-page">
                    <?php  // Вывод промо кода
                    require_once (__DIR__ . '/../common/show_promo_code.php');?>

                    <?php  // Вывод уведомления CallPassword
                    if (CallPassword::isShowButton($user)):
                        require_once (ROOT . '/extensions/callpassword/views/show_notice.php');
                    endif;?>

                    <?php  // Вывод уведомления Telegram
                    if (Telegram::isShowButton($user['user_id'], $user['nick_telegram'])):
                        require_once (ROOT . '/extensions/telegram/views/show_notice.php');
                    endif;?>

                    <h1><?=System::Lang('MY_ORDERS');?></h1>

                    <?php if(isset($_GET['success'])):?>
                        <div class="success_message"><?=System::Lang('LINKS_SEND_TO_EMAIL');?></div>
                    <?php endif;?>

                    <?php if(isset($_GET['fail'])):?>
                        <div class="warning_message"><?=System::Lang('ERROR_PRODUCT');?></div>
                    <?php endif;?>

                    <?php if($orders):?>
                        <div class="pay_orders">
                            <div class="table-responsive">
                                <table class="pay-table">
                                    <tr>
                                        <th class="text-left"><?=System::Lang('COUNT_NUMBER');?></th>
                                        <th class="text-left"><?=System::Lang('ITEM');?></th>
                                        <th><?=System::Lang('PAYMENT_DATE');?></th>
                                        <th><?=System::Lang('STATUS');?></th>
                                    </tr>
                                    <?php foreach($orders as $order):
                                        $right = 0;?>
                                        <tr>
                                            <td class="text-left"><?=$order['order_date'];?></td>
                                            <td class="text-left"><?php $items = Order::getOrderItems($order['order_id']);
                                                if ($items):
                                                    foreach($items as $item):
                                                        $product_data = Product::getProductName($item['product_id']);?>
                                                        <h4 class="order-final-title">
                                                            <?php if ($product_data['group_id']) {
                                                                $prod_groups = explode(",", $product_data['group_id']);
                                                                foreach($prod_groups as $group){
                                                                    if (is_array($user_groups) && in_array($group, $user_groups)) {
                                                                        $right++;
                                                                    }
                                                                }
                                                            }
                                                            echo "{$product_data['product_name']}{$product_data['mess']}";?>
                                                        </h4>

                                                        <?php if(!empty($item['pincode'])):?>
                                                            <div class="payment-info"><?=System::Lang('KEY');?> <?=$item['pincode'];?></div>
                                                        <?php endif;?>
                                                    <?php endforeach;?>
                                                <?php endif;?>

                                                <!--div class="payment-info">Дополнительная информация</div-->
                                                <?php if($setting['dwl_in_lk'] == 1 && $right != 0 && !empty($product_data['link'])):?>
                                                    <form action="" method="POST">
                                                        <input type="hidden" name="order" value="<?=$order['order_date'];?>">
                                                        <input type="submit" class="btn getlink btn-red" name="getlink" value="Отправить письмо заказа ещё раз">
                                                    </form>
                                                <?php endif; ?>
                                            </td>

                                            <td><?=date("d.m.Y H:i:s", $order['order_date']);?></td>

                                            <td>
                                                <span class="status-act"><?=System::Lang('ACTIVE');?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    <?php else:?>
                        <?=System::Lang('NO_ORDERS');?>
                    <?php endif;?>

                    <div class="installments">
                        <?php $installment_list = Order::searchInstallmentByEmail($user['email']);
                        if($installment_list):?>
                            <h2><?=System::Lang('MY_INSTALLMENTS');?></h2>

                            <?php foreach($installment_list as $installment):
                                $pay_actions = !empty($installment['pay_actions']) ? unserialize(base64_decode($installment['pay_actions'])) : false;
                                $installment_data = Product::getInstallmentData($installment['installment_id']);?>
                                
                                <div class="table-responsive">
                                    <table class="pay-table">
                                        <tr>
                                            <th><?=System::Lang('ID');?></th>
                                            <th class="text-left"><?=System::Lang('DESCRIPTION');?></th>
                                            <th class="text-right"><?=System::Lang('PAIDED');?></th>
                                            <th class="text-right"><?=System::Lang('NEXT_PAYMENT');?></th>
                                        </tr>

                                        <tr>
                                            <td><?=$installment['id'];?></td>

                                            <td class="text-left">
                                                <p><?=System::Lang('SUMM');?> <?php echo $summ = $installment['summ']; echo " {$setting['currency']}, на {$installment['max_periods']} мес."; ?></p>
                        
                                                <?php if($installment['status'] == 9):
                                                    $summ = $summ + $installment_data['sanctions'];?>
                                                    <p class="max-w-280">
                                                        <span style="color:red"><?=System::Lang('PAYMENT_DOWN');?>
                                                            <?php if ($installment_data['sanctions']!= 0) {
                                                                echo " (штраф: {$installment_data['sanctions']} {$setting['currency']})";
                                                            }?>
                                                        </span>
                                                    </p>
                                                <?php endif;

                                                $pay_summ = 0;
                                                if ($pay_actions) {
                                                    foreach ($pay_actions as $action) {
                                                        $pay_summ = $pay_summ + $action['summ'];
                                                    }
                                                }?>

                                                <p><a class="btn-green" href="/installahead/<?=$installment['id'];?>">Погасить досрочно <?=$summ - $pay_summ.' '.$setting['currency'];?></a></p>
                                            </td>

                                            <td class="text-right">
                                                <?php $i = 0;
                                                if ($pay_actions):
                                                    foreach($pay_actions as $action):?>
                                                        <p class="sum-paid-item">
                                                            <span class="sum-paid"><?=$action['summ']?> <?=$setting['currency'];?></span>
                                                            <span class="span-paid"><?=date("d.m.Y H:i", $action['date'])?></span>
                                                        </p>

                                                        <?php $i++;
                                                    endforeach;
                                                else:
                                                    echo '---';
                                                endif?>
                                            </td>

                                            <td class="text-right">
                                                <div class="next-pay">
                                                    <div class="next-pay-price">
                                                        <?php $num_next_pays = $installment['max_periods'] - $i;
                                                        echo ($num_next_pays > 0 ? round(($summ - $pay_summ) / $num_next_pays) : 0) . $setting['currency'];?>
                                                    </div>
                                                    <?php if($installment['next_pay']):?>
                                                        <span class="font-12"><?=date("d.m.Y H:i", $installment['next_pay']);?></span>
                                                    <?php else:
                                                        echo '---';
                                                    endif;

                                                    if($installment['status'] == 9):?>
                                                        <span class="paid-expired"><?=System::Lang('EXPIRED');?></span>
                                                    <?php endif;?>
                                                </div>

                                                <?php if($installment['next_order'] != 0){?>
                                                    <div class="pay-link-blue">
                                                        <a class="link-blue" target="_blank" href="/pay/<?=$installment['next_order'];?>"><?=System::Lang('TO_PAY');?></a>
                                                    </div>
                                                <?php } else {?>
                                                    <div class="pay-link-blue">
                                                        <a href="/installament/ahead/<?php echo $installment['id'];?>"><?=System::Lang('PAY_EARLY');?></a>
                                                    </div>
                                                <?php }?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            <?php endforeach;?>
                        <?php endif;?>
                    </div>

                    <?php if($orders_nopay):?>
                        <div class="nopay_orders">
                            <h2><?=System::Lang('NOT_COMPLITE_ORDERS');?></h2>

                            <div class="table-responsive">
                                <table class="pay-table nopay">
                                    <tr>
                                        <th class="text-left"><?=System::Lang('ORDER_NUM_TAG');?></th>
                                        <th class="text-left"><?=System::Lang('ORDER_CONTENT');?></th>
                                        <th><?=System::Lang('ORDER_DATE');?></th>
                                        <th><?=System::Lang('SUMM');?></th>
                                        <th><?=System::Lang('STATUS');?></th>
                                    </tr>

                                    <?php foreach($orders_nopay as $ord_nopay):
									$total = 0;?>
                                        <tr>
                                            <td class="text-left"><?=$ord_nopay['order_date'];?></td>

                                            <td class="text-left">
                                                <?php $items = Order::getOrderItems($ord_nopay['order_id']);
                                                if ($items):
                                                    foreach($items as $item):?>
                                                        <h5 class="order-title"><?php $product_data = Product::getProductName($item['product_id']);
                                                            echo "{$product_data['product_name']}{$product_data['mess']}";?>
                                                        </h5>

                                                        <?php $total = $total + $item['price'];
                                                    endforeach;
                                                endif;?>

                                                <div class="pay-move">
                                                    <div class="pay-move__button">
                                                        <a class="btn-green" target="_blank" href="/pay/<?=$ord_nopay['order_date'];?>"><?=System::Lang('TO_PAY');?></a>
                                                        <?php if(isset($params['allow_user_to_delete_orders']) && $params['allow_user_to_delete_orders'] == 1):?>
                                                            <a class="link-red" onclick="return confirm('Вы уверены?')" href="/cancelpay/<?=$ord_nopay['order_date'];?>?key=<?=md5($user['email'].':'.$ord_nopay['order_date']);?>"><?=System::Lang('CANCEL');?></a>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                            </td>

                                            <td><?=date("d.m.Y", $ord_nopay['order_date']);?><br><?=date("H:i:s", $ord_nopay['order_date']);?></td>

                                            <td><?=$total;?> <?=$setting['currency'];?></td>

                                            <td><span class="status-noact"><?=System::Lang('NOT_PAID');?></span><br><br>
                                                <!--div class="status-remove">Отменён</div-->
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    <?php endif;?>
                </div>
            </div>

            <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/sidebar.php');?>
        </div>
    </div>
</div>

<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>

<script type="text/javascript">
  setTimeout(function(){$('.success_message').fadeOut('fast')},4000);
</script>
</body>
</html>