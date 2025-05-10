<?php defined('BILLINGMASTER') or die;?>
<!-- 4 Партнёрские заказы -->
<div>
    <div class="table-responsive">
        <p><?=System::Lang('SHOW');?> <a href="/lk/aff"<?php if(!isset($_GET['all'])) echo ' style="font-weight:bold"'?>><?=System::Lang('ONLY_PAID');?></a> | <a href="/lk/aff?all"<?php if(isset($_GET['all'])) echo ' style="font-weight:bold"'?>><?=System::Lang('SHOW_ALL');?></a></p>
        <table class="usertable fz-14">
            <tr>
                <th><?=System::Lang('NUMBER');?></th>
                <th><?=System::Lang('PRODUCT');?></th>
                <th><?=System::Lang('CREATION');?></th>
                <th><?=System::Lang('CLIENT_NAME');?></th>
                <th><?=System::Lang('SUMM_INVOICE');?></th>
                <th><?=System::Lang('PROMO');?></th>
                <th><?=System::Lang('PAID_OUT');?></th>
                <th><?=System::Lang('EARNED');?></th>
            </tr>

            <?php 
            $status = isset($_GET['all']) ? 'all' : 'pay';
            $orders = Aff::getPartnersOrders($userId, $status, $paid);
            if($orders):
                foreach($orders as $nopay):?>
                    <tr>
                        <td class="nowrap"><?=$nopay['order_date'];?><br /><span class="small"><?=System::Lang('ID');?>: <?=$nopay['order_id'];?></span></td>
                        <td style="white-space: break-spaces"><?php
                        $product_name = Product::getProductName($nopay['product_id']);
                        echo $product_name['product_name'];?>
                        </td>
                        <td><?php if($nopay['order_date'] != null) echo date("d.m.Y H:i:s", $nopay['order_date']);?></td>
                        <td><?=$params['params']['hidden_email'] == 1 ? System::hideEmail($nopay['client_email']) : $nopay['client_email'];?>
                        <?php if($nopay['partner_id'] != $userId) echo '<br> 2 или 3 уровень';?>
                        </td>
                        <td><?=$nopay['summ']>0 ? $nopay['summ'].' '.$setting['currency'] : 'Бесплатно';?></td>                                                     
                        <td><?php if(!empty($nopay['sale_id'])) {
                                $sale = Product::getSaleData($nopay['sale_id']);
                                if ($sale['type'] == 2) {
                                    echo $sale['promo_code'];
                                }
                            }?>
                        </td>
                        <td class="nowrap"><?php if($nopay['status'] == 1 && $nopay['summ']>0):?> 
                            <div class="partner-pay" title="Оплачено">
                                <i class="icon-dollar-green"></i>
                                <span><?=date("d.m.Y", $nopay['payment_date']);?><br><?=date("H:i:s", $nopay['payment_date']);?></span>
                            </div>
                        <?php elseif($nopay['status'] == 1 && $nopay['summ'] == 0):
                            echo '<span style="color:green">Получен</span>'; 
                        elseif($nopay['status'] == 9):?>
                            <div class="partner-pay" title="Возврат">
                                <i class="icon-dollar-red"></i>
                                <span><?=date("d.m.Y", $nopay['payment_date']);?><br><?=date("H:i:s", $nopay['payment_date']);?></span>
                            </div>
                        <?php else: echo ' <span style="color:orange">Не оплачен</span>';?></td>
                        <?php endif;?>
                            <td class="text-right"><?php if($nopay['trans_summ'] > 0) echo $nopay['trans_summ'].' '.$setting['currency']; else echo '---';?></td>
                            </tr>
                    <?php endforeach;
            endif;?>

            <?php $total_summ_orders = 0;
            if($orderss):
                foreach($orderss as $transact):?>
                    <?php $total_summ_orders = $total_summ_orders + $transact['summ'];
                endforeach;
            endif;?>
        </table>
        <p class="text-right"><?='Итого: '.$total_summ_orders;?> <?=$setting['currency'];?></p>
    </div>
</div>