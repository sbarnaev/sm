<?php defined('BILLINGMASTER') or die;

require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php'); ?>
<body class="cart-page" id="page">
    <?php require_once (ROOT . "/template/{$setting['template']}/layouts/header.php");?>
    <div id="order_form" class="order-info-container">
        <div class="container-cart">
            <div class="top">
                <h2 class="mb-30 mt-0 ml-15"><?=$h2;?></h2>
                <h3 class="mb-30 mt-0 <?=$h3_class;?>"><?=$h3;?></h3>
            </div>

            <div class="maincol-inner-white">
                <div class="order_data">
                    <div class="order_items">
                        <?php foreach($order_items as $order_item):
                            $product = Product::getProductData($order_item['product_id']);?>

                            <div class="order_item">
                                <div class="order_item-left">
                                    <?php if(!empty($product['product_cover'])):?>
                                        <img src="/images/product/<?=$product['product_cover'];?>" alt="<?=$product['img_alt'];?>">
                                    <?php endif;?>
                                </div>

                                <div class="order_item-desc">
                                    <h4><?=$product['product_name'];?></h4>
                                    <?php if($product['product_desc'] != null):?>
                                        <div class="product_desc"><?=nl2br($product['product_desc']);?></div>
                                    <?php endif;?>
                                </div>

                                <div class="order_item-price_box-right">
                                    <span class="font-bold"><?="{$order_item['price']} {$setting['currency']}";?></span>
                                </div>
                            </div>
                        <?php endforeach;?>
                    </div><hr>

                    <div class="bottom">
                        <div>
                            <span><?=System::Lang('ORDER_NUMBER');?><?=$order['order_date'];?></span><br>
                            <?php if($pc_order && $pc_order['profile_id']):?>
                                <span><?=System::Lang('REQUEST_NUMBER');?><?=$pc_order['profile_id'];?></span><br>
                            <?php endif;?>
                            <span><?="$client_name ({$order['client_email']})";?></span>
                        </div>
                        <h4><?=System::Lang('ORDER_SUMM_TAG');?> <?="{$total}{$setting['currency']}";?></h4>
                    </div>

                    <?php if($pos_credit_data['status']):?>
                        <hr>
                        <div class="to-do-text-info">
                            <div class="mb-15">
                                <span><?=System::Lang('WHAT_NEXT');?></span>
                            </div>

                            <div>
                                <ul>
                                    <?=System::Lang('WHAT_NEXT_SEQUENS');?>
                                </ul>
                            </div>
                        </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
    <?php require_once (ROOT . "/template/{$setting['template']}/layouts/footer.php");
    require_once (ROOT . "/template/{$setting['template']}/layouts/tech-footer.php")?>
</body>
</html>