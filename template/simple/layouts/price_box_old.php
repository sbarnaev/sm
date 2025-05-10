<?php defined('BILLINGMASTER') or die; 

if($product['vip_id'] != 0) $style = 3;
elseif($product['premium_id'] != 0) $style = 2; 
else $style = '';

$metriks = null;
if(!empty($setting['yacounter'])) $ya_goal = "yaCounter".$setting['yacounter'].".reachGoal('ADD_TO_BUY');";
else $ya_goal = null;
if($setting['ga_target'] == 1) $ga_goal = "ga ('send', 'event', 'add_to_buy', 'click');";
else $ga_goal = null;
if(!empty($setting['yacounter']) || $setting['ga_target'] == 1) $metriks = ' onclick="'.$ya_goal.$ga_goal.' return true;"';
?>


<div class="product_price_box<?php echo $style;?>">
    <div class="standart_box">
    <h3><?=System::Lang('STANDART');?></h3>
    <ul><?php echo $product['base_list']; ?></ul>
    <?php $standart_price = Price::getFinalPrice($product['product_id']);?>
    
    <p class="price_str"><?php if($standart_price['real_price'] < $standart_price['price']):?>
    <span class="old_price"><?php echo $standart_price['price']?></span>
    <?php endif;?>
    <?php echo $standart_price['real_price'];?> <?php echo $setting['currency'];?></p>
    
    <?php if($product['product_amt'] != 0):
    if($setting['use_cart'] == 1){?>
    <p><button data-id="<?php echo $product['product_id'];?>" class="add_to_cart"<?php echo $metriks;?>><?=System::Lang('IN_CART');?></button></p>
    <?php } else {?>
    <p><a class="order_link" href="<?php echo $setting['script_url'];?>/buy/<?php echo $product['product_id']; ?>"<?php echo $metriks;?>><?=System::Lang('TO_ORDER');?></a></p>
    <?php } endif;?>
    <?php if($product['show_amt'] == 1):?>
        <?php echo prodCount($product['product_amt']);?>
    <?php endif; ?>
    </div>
    
    <?php if($product['premium_id'] != 0):?>
    <div class="premium_box">
    <h3><?=System::Lang('PREMIUM');?></h3>
    <?php $premium = Product::getMinProductById($product['premium_id']);
    $premium_price = Price::getFinalPrice($product['premium_id']);?>
    <ul><?php echo $product['premium_list']; ?></ul>
    <p class="price_str">
    <?php if($premium_price['real_price'] < $premium_price['price']):?>
    <span class="old_price"><?php echo $premium_price['price']?></span>
    <?php endif;?>
    <?php echo $premium_price['real_price'];?> <?php echo $setting['currency'];?></p>
    
    <?php if($premium['product_amt'] != 0): 
    if($setting['use_cart'] == 1){?>
    <p><button data-id="<?php echo $product['premium_id'];?>" class="add_to_cart"<?php echo $metriks;?>><?=System::Lang('IN_CART');?></button></p>
    <?php } else {?>
    <p><a class="order_link" href="<?php echo $setting['script_url'];?>/buy/<?php echo $product['premium_id']; ?>"<?php echo $metriks;?>><?=System::Lang('TO_ORDER');?></a></p>
    <?php } endif; ?>
    
    
    <?php if($premium['show_amt'] == 1):?>
        <?php echo prodCount($premium['product_amt']);?>
    <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php if($product['vip_id'] != 0):?>
    <div class="vip_box">
    <h3><?=System::Lang('VIP');?></h3>
    <?php $vip = Product::getMinProductById($product['vip_id']);
    $vip_price = Price::getFinalPrice($product['vip_id']);?>
    <ul><?php echo $product['vip_list']; ?></ul>
    <p class="price_str">
    <?php if($vip_price['real_price'] < $vip_price['price']):?>
    <span class="old_price"><?php echo $vip_price['price']?></span>
    <?php endif;?>
    <?php echo $vip_price['real_price'];?> <?php echo $setting['currency'];?></p>
    
    <?php if($vip['product_amt'] != 0):
    if($setting['use_cart'] == 1){?>
    <p><button data-id="<?php echo $product['premium_id'];?>" class="add_to_cart"<?php echo $metriks;?>><?=System::Lang('IN_CART');?></button></p>
    <?php } else {?>
    <p><a class="order_link" href="<?php echo $setting['script_url'];?>/buy/<?php echo $product['vip_id']; ?>"<?php echo $metriks;?>><?=System::Lang('TO_ORDER');?></a></p>
    <?php } endif; ?>
    
    <?php if($vip['show_amt'] == 1):?>
        <?php echo prodCount($vip['product_amt']);?>
    <?php endif; ?>
    </div>
    <?php endif; ?>
</div>


<?php function prodCount($count){
    if($count == 0) $result = 'Товар закончился';
    elseif($count == -1) $result = '';
    else $result = "Всего осталось: $count";
    return $result;
}
?>