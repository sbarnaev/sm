<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php'); ?>
<body class="cart-page" id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    ?>
    <div id="order_form">
        <div class="container-cart">
            <ul class="container-crumbs">
                <li class="crumbs-no-active"><?=System::Lang('FIRST_YOUR_DATAS');?></li>
                <li class="two-active"><?=System::Lang('SECOND_CART');?></li>
                <li><?=System::Lang('THIRD_PAYMENT_VARIANT');?></li>
            </ul>
            <h2><?=System::Lang('CART');?></h2>

            <div class="order_data">

                <div class="offer main">

                <?php $total = 0 + $tax; 
                $full_price = 0;
                $i = 0;
                foreach($order_items as $item):
                $product = Product::getProductById($item['product_id']);?>

                    <div class="order_item">
                        <?php if($product['product_cover'] != null):?>
                        <div class="order_item-left">
                            <img src="/images/product/<?php echo $product['product_cover'];?>" alt="<?php echo $product['img_alt'];?>">
                        </div>
                        <?php endif;?>

                        <div class="order_item-desc">
                            <h4><?php echo $product['product_name'];?></h4>
                        </div>

                        <div class="order_item-price_box-right">
                            <?php if($product['price'] > $item['price']){?>
                            <span class="old_price"><?php echo $product['price'];?> <?php echo $setting['currency'];?></span>
                            <div class="font-bold"><?php echo $item['price'];?> <?php echo $setting['currency'];?></div>
                            <?php } else {?>
                            <div class="font-bold"><?php echo $item['price'];?> <?php echo $setting['currency'];?></div>
                            <?php }?>

                            <?php if($i > 0):?>
                            <form class="mt-5" action="" method="POST">
                                <input type="hidden" name="item_id" value="<?php echo $item['order_item_id'];?>">
                                <button class="btn-delete" type="submit" name="delete_item"><i class="icon-remove"></i></button>
                            </form>
                            <?php endif;?>
                        </div>
                    </div>
                    <?php $total = $total + $item['price'];
                        $full_price = $full_price + $product['price'];
                        $added_array[] = $item['product_id'];
                        $i++;
                         ?>
                    <hr />




                <?php endforeach;?>
                <div class="order_item-bottom">
                    <div class="order_item-bottom__left">
                        <p><?=System::Lang('ORDER_NUMBER');?> <?php echo $order_date;?></p>
                        <p><?php echo $order['client_name'];?> (<?php echo $order['client_email'];?>)</p>
                    </div>
                    <div class="payment-bottom__right">
                        <p><?=System::Lang('SUMM_ORDER');?> <?php echo $full_price; ?> <?php echo $setting['currency'];?></p>
                        <?php if($full_price > $total):?>
                        <p><?=System::Lang('YOUR_SAVINGS');?> <?php echo $full_price - $total;?> <?php echo $setting['currency'];?></p>
                        <?php endif;?>
                    </div>
                </div>
                <div class="payment-itogo"><?=System::Lang('RESULT');?> <?php echo $total; ?> <?php echo $setting['currency'];?></div>
                <p class="btn-next"><a class="btn-blue" href="/pay/<?php echo $order_date;?>"><?=System::Lang('OK_CONTINUE');?></a></p>

                </div>

                <?php
                foreach($related_products as $related){
                    $rel_array[] = $related['product_id'];
                    $res = array_diff($rel_array, $added_array);
                } 
                if(!empty($res)) echo '<h3>Добавить со скидкой в корзину</h3>';?>

                <?php foreach($related_products as $related):
                if(in_array($related['product_id'], $added_array)) continue;
                $product = Product::getProductById($related['product_id']);?>
                <div class="related-offer">
                    <div class="order_item">
                        <?php if($product['product_cover'] != null):?>
                        <div class="order_item-left">
                            <img src="/images/product/<?php echo $product['product_cover'];?>" alt="<?php echo $product['img_alt'];?>">
                        </div>
                        <?php endif;?>

                        <div class="order_item-desc">
                            <h4><?php echo $product['product_name'];?></h4>
                            <div><?php if($related['offer_desc'] != null) echo $related['offer_desc'];
                            else echo $product['product_desc'];?>
                            </div>
                            <?php if($product['external_landing'] == 1) $link = $product['external_url'];
                            else $link = '/catalog/'.$product['product_alias'];?>
                            <a href="<?php echo $link;?>" target="_blank" class="order_item-readmore"><?=System::Lang('MORE');?></a>
                            <div class="order_item-price_box">
                                <?php if($product['price'] > $related['price']){?>
                                <p><?=System::Lang('COAST');?> <span class="old_price"><?php echo $product['price'];?></span> <?php echo $setting['currency'];?></p>
                                <p><strong><?=System::Lang('SET_ORDER');?> <?php echo $related['price'];?> <?php echo $setting['currency'];?></strong></p>
                                <?php } else {?>
                                <p><?=System::Lang('SET_COAST_PAYMENT');?> <?php echo $related['price'];?> <?php echo $setting['currency'];?></p>
                                <?php }?>
                            </div>
							
                            <form class="add_offer-form" action="" method="POST">
                                <? /* Комплектация - HTML блок
                                <div class="complect">
                                    <h5 class="complect-name">Комплектация - HTML блок</h5>
                                    <div class="complect-row">
                                        <label class="custom-radio">
                                            <input name="complect" type="radio">
                                            <span>VIP (7900 р. <strong>5450 р.</strong>)</span>
                                        </label>
                                        <label class="custom-radio">
                                            <input name="complect" type="radio">
                                            <span>Стандарт (7900 р. <strong>5450 р.</strong>)</span>
                                        </label>
                                    </div>
                                </div>
                                */ ?>
                                <input type="hidden" name="offer_id" value="<?php echo $related['id'];?>">
                                <input class="btn-green" type="submit" name="add_offer" value="Добавить к заказу">
                            </form>
                        </div>
                    </div>
				</div>
				<?php endforeach; ?>
        </div>
    </div>
</div>
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
</body>
</html>