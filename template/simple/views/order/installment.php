<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php'); ?>
<body class="cart-page" id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');?>
    <div id="order_form">
        <div class="container-cart">
            <ul class="container-crumbs <?php if($related_products && $setting['use_cart'] == 0) echo ''; else echo 'container-crumbs-two-steps'?> ">
                <li class="crumbs-no-active crumbs-order"><span>1</span><?=System::Lang('YOUR_DATES');?></li>
                <li class="three-active"><span><?php if($related_products && $setting['use_cart'] == 0) echo '3'; else echo '2'?></span><?=System::Lang('INSTALLMENT_APPLICATION');?></li>
            </ul>

            <h2><?=System::Lang('REQUEST');?></h2>

            <div class="order_data">
                <h3><?=System::Lang('ORDER_DATES');?> <?=$order_date;?></h3>

                <div class="offer main offer-mb-35">
                    <?php $full_price = 0;
                    foreach($order_items as $item):?>
                        <div class="order_item">
                            <?php $product = Product::getMinProductById($item['product_id']);
                            if ($product['installment'] > 0) {
                                $installment = $product['installment'];
                            }

                            if($product['product_cover']!= null):?>
                                <div class="order_item-left">
                                    <img src="/images/product/<?=$product['product_cover'];?>" alt="">
                                </div>
                            <?php endif;?>

                            <div class="order_item-desc">
                                <h4><?=$item['product_name'];?></h4>
                            </div>

                            <div class="order_item-price_box-right">
                                <?php if($product['price'] > $item['price']):?>
                                    <span class="old_price"><?=$product['price'];?> <?=$setting['currency'];?></span>
                                    <div class="font-bold"><?=$item['price'];?> <?=$setting['currency'];?></div>
                                <?php else:?>
                                    <div class="font-bold"><?=$item['price'];?> <?=$setting['currency'];?></div>
                                <?php endif;?>
                            </div>
                        </div>
                        <hr>
                        <?php
                        $full_price = $full_price + $product['price'];
                    endforeach; ?>

                    <div class="order_item-bottom">
                        <div class="order_item-bottom__left">
                            <p><?=System::Lang('ORDER_NUMBER');?> <?=$order_date;?></p>
                            <?php if(!isset($hide_cl_email) || !$hide_cl_email):?>
                                <p><?=$order['client_name'];?> (<?=$order['client_email'];?>)</p>
                            <?php endif;

                            if($tax != 0):?>
                                <p><?=System::Lang('DELIVERY');?> <?=$tax;?> <?=$setting['currency'];?></p>
                            <?php endif;?>
                        </div>

                        <div class="payment-bottom__right">
                            <p><?=System::Lang('ORDER_SUMM_TAG');?> <?=$total;?> <?=$setting['currency'];?></p>
                            <?php if($full_price > $total):?>
                                <p><?=System::Lang('YOUR_SAVINGS');?> <?=$full_price - $total;?> <?=$setting['currency'];?></p>
                            <?php endif;?>
                        </div>
                    </div>

                    <div class="payment-itogo"><?=System::Lang('RESULT');?> <?=$total;?> <?=$setting['currency'];?></div>
                </div>


                <div class="installment_pay">
                    <h3><?=System::Lang('INSTALLMENTS_PAYMENT');?></h3>
                    <?php if(!$installment_data) {
                        exit('Error Installment');
                    }

                    $first_pay = round(($total / 100) * $installment_data['first_pay']);
                    $other_pay = round(($total / 100) * $installment_data['other_pay']);

                    $p = 2;
                    $m = 1;?>

                    <div class="offer main">
                        <div class="install_item">
                            <h4 class="tabs-payment-subtitle"><?=$installment_data['title'];?></h4>
                            <?php $increase_pay = $installment_data['increase'] > 0 ? $installment_data['increase'] / $installment_data['max_periods'] : 0; ?>

                            <table class="install_item-table">
                                <tr>
                                    <th><?=System::Lang('PAYMENT_NUMBER');?></th>
                                    <th><?=System::Lang('PAYMENT_DATE');?></th>
                                    <th class="install_item-table__last"><?=System::Lang('SUMM');?></th>
                                </tr>

                                <tr>
                                    <td>1</td>
                                    <td><?=System::Lang('TODAY');?></td>
                                    <td><?=round($first_pay + $increase_pay);?> <?=$setting['currency'];?></td>
                                </tr>

                                <?php while($installment_data['max_periods'] >= $p):
                                    $pay_date = Installment::getNextPayDate($installment_data, $now, $installment_data['date_second_payment'], $m++);?>
                                    <tr>
                                        <td><?=$p++?></td>
                                        <td><?=date("d.m.Y", $pay_date);?></td>
                                        <td class="install_item-table__last"><?=round($other_pay + $increase_pay);?> <?=$setting['currency'];?></td>
                                    </tr>
                                    <?php endwhile;?>
                            </table>

                            <p class="install_item__last-block"><?=System::Lang('INSTALLMENT_COAST');?> <?=$installment_data['increase'];?> <?=$setting['currency'];?></p>
                            <p class="install_item__last-block"><strong><?=System::Lang('INSTALLMENT_COAST_SUMM');?> <?=$installment_total;?> <?=$setting['currency'];?></strong></p>

                            <div class="short_rules">
                                <?=$installment_data['installment_desc'];?>
                            </div>
                        </div>

                        <?php $fields = unserialize(base64_decode($installment_data['fields']));?>
                        <div class="user_data">
                            <form action="" method="POST" enctype="multipart/form-data">
                                <p><input type="text" name="name" placeholder="Ваше Имя" required="required"></p>
                                <p><input type="text" name="soname" placeholder="Ваша Фамилия" required="required"></p>
                                <p><input type="text" name="otname" placeholder="Ваше Отчество"></p>

                                <?php if($fields['passport'] > 0):?>
                                    <p><input type="text" name="passport" placeholder="Серия и номер паспорта" <?php if($fields['passport'] == 2) echo 'required="required"';?></p>
                                <?php endif;?>

                                <p><input type="email" name="email" disabled="disabled" placeholder="Email" value="<?=$order['client_email'];?>"></p>
                                <p><input type="text" name="phone" placeholder="Номер телефона" value="<?=$order['client_phone'];?>"></p>

                                <?php if($fields['address'] > 0):?>
                                    <p><input type="text" name="city" <?php if($fields['address'] == 2) echo 'required="required"';?> placeholder="Город"></p>
                                    <p><textarea name="address" <?php if($fields['address'] == 2) echo 'required="required"';?> placeholder="Адрес"></textarea></p>
                                <?php endif;?>

                                <?php if($fields['skan1'] > 0):?>
                                    <p><span class="scan-text"><?=System::Lang('PASSPORT_SCAN');?></span>
                                        <input type="file" name="skan" <?php if($fields['skan1'] == 2) echo 'required="required"';?>>
                                    </p>
                                <?php endif;?>

                                <?php if($fields['skan2'] > 0):?>
                                    <p><span class="scan-text"><?=System::Lang('PASSPORT_SCAN_REG');?></span>
                                        <input type="file" name="skan2" <?php if($fields['skan2'] == 2) echo 'required="required"';?>>
                                    </p>
                                <?php endif;?>

                                <input type="hidden" name="order_date" value="<?=$order['order_date'];?>">
                                <input type="hidden" name="install_id" value="<?=$installment_id;?>">
                                <input type="hidden" name="install_title" value="<?=$installment_data['title'];?>">

                                <div><label class="check_label">
                                        <input checked type="checkbox" required="">
                                        <span><?=System::Lang('LINK_AGREE');?></span>
                                    </label>
                                </div>

                                <div class="payment-submir-wrap">
                                    <button type="submit" name="go_installment" class="btn-green-small"><?=System::Lang('SEND_REQUEST');?></button>
                                </div>

                                <p><span class="small"><?=System::Lang('DEDLINE');?></span></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>