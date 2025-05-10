<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php'); ?>
<body class="cart-page" id="page">
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/header.php');
    ?>
    <div id="order_form">
        <div class="container-cart">
            <ul class="container-crumbs container-crumbs-two-steps ">
                <li class="first-active"><span>1</span><?=System::Lang('YOUR_DATES');?></li>
                <li><span>2</span><?=System::Lang('PAYMENT_OPTION');?></li>
            </ul>
            <h2><?=System::Lang('VARIANT_OF_DELIVERY_AND_PAYMENT');?></h2>
            
            <div class="order_data">
                <?php if(isset($message)){?>
                <div class="success_message success_message-alert"><?php echo $message;?></div>
                <?php } ?>
                <h3><?=System::Lang('ORDER_DATES');?> <?php echo $order_date;?></h3>
                
                <div class="offer main">
                    <?php $total = 0; 
                    foreach($order_items as $item):?>
                    <div class="order_item">
                        <div class="order_item-desc">
                            <h4><?php echo $item['product_name'];?></h4>
                        </div>
                        <div class="order_item-price_box-right">
                            <span class="font-bold"><?php echo $item['price'];?> <?php echo $setting['currency'];?></span>
                        </div>
                    </div>
                    <div class="payment-itogo">
                    <?php $total = $total + $item['price']; endforeach; ?>
                    <?=System::Lang('RESULT');?> <?php echo $total; ?> <?php echo $setting['currency'];?>
                    </div>
                </div>
            </div>
            <form action="" method="POST">
                    
            <div class="payments_list">
            <h3><?=System::Lang('DELIVERY_VARIANTS');?></h3>
                <div class="offer main">
                <?php if($delivery_methods):
                foreach($delivery_methods as $method):?>
                <div class="payment_item">
                    <div class="delivery_radio">
                        <label class="custom-radio" for="payment_<?php echo $method['method_id'];?>">
                            <input type="radio" id="payment_<?php echo $method['method_id'];?>" name="method" value="<?php echo $method['method_id'];?>">
                            <span><?php echo $method['title'];?> </span>
                        </label>
                    </div>
                    <div class="payment_img font-bold">
                    <?php if($method['tax'] != 0):?>+ <?php echo $method['tax'];?> <?php echo $setting['currency'];?><?php endif;?>
                    </div>
                    <div class="payment_desc"><?php echo $method['ship_desc'];?></div>
                </div>
                <?php endforeach;
                endif;?>
                </div>
            </div>

                <div class="payments_list">
                    <h3><?=System::Lang('PAYMENT_OPTION');?></h3>
                    <div class="offer main">
            <p><label><?=System::Lang('CHOOSE_PAYMENT');?> </label></p>
               <div class="payments_list-row">
                        <div>
                   <div class="select-wrap">
                        <select name="pay">
                <option value=""><?=System::Lang('CHOOSE');?></option>
                <option value="1"><?=System::Lang('PAY_NOW');?></option>
                <option value="0"><?=System::Lang('PAY_WHEN_RESIVING');?></option>
            </select>
                    </div>
               </div>
                   <div>
            <input type="hidden" name="total" value="<?php echo $total;?>">
            <input class="btn-blue-small" type="submit" name="delivery_ok" value="Продолжить">
                   </div>
                </div>
                </div>
                </div>
            </form>
        </div>
    </div>
    
    <?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/footer.php');
    require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php')?>
    <script type="text/javascript">
    	setTimeout(function(){$('.success_message').fadeOut('fast')},4000); 
    </script>
</body>
</html>